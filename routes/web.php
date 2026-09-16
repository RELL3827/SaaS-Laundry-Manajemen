<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Volt::route('/login', 'auth.login')->name('login')->middleware('guest');
Volt::route('/register', 'auth.register-tenant')->name('register')->middleware('guest');

Route::middleware(['auth'])->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/customers', 'customers')->name('customers');
    Volt::route('/services', 'services')->name('services');
    Volt::route('/orders', 'orders.index')->name('orders.index');
    Volt::route('/orders/create', 'orders.create')->name('orders.create');
    Volt::route('/orders/{id}', 'orders.show')->name('orders.show');
    
    Route::get('/orders/{id}/print', function ($id) {
        $order = App\Models\Order::with(['tenant', 'customer', 'items.service'])->findOrFail($id);
        // Security check
        if ($order->tenant_id != auth()->user()->tenant_id) abort(403);
        
        return view('print.receipt', compact('order'));
    })->name('orders.print');

    Route::get('/orders-export', function () {
        $tenant = auth()->user()->tenant;
        if (!$tenant || !$tenant->canExportReport()) {
            abort(403, 'Fitur Ekspor Laporan hanya tersedia untuk Paket Pro.');
        }

        $orders = App\Models\Order::with(['customer', 'payments'])
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->get();

        $filename = 'laporan-pesanan-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['No. Nota', 'Tanggal', 'Pelanggan', 'No. HP', 'Status', 'Metode Bayar', 'Status Bayar', 'Subtotal', 'Diskon', 'Total']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer?->name ?? 'Pelanggan Umum',
                    $order->customer?->phone ?? '-',
                    $order->status,
                    $order->payments->first()?->method ?? 'Cash',
                    $order->payment_status,
                    $order->subtotal,
                    $order->discount,
                    $order->total,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    })->name('orders.export');

    Route::post('/tenant/switch-plan', function (\Illuminate\Http\Request $request) {
        $plan = $request->input('plan');
        if (in_array($plan, ['free', 'pro'])) {
            $tenant = auth()->user()->tenant;
            if ($tenant) {
                $tenant->update(['plan' => $plan]);
            }
        }
        return back()->with('success', 'Paket berhasil diperbarui menjadi: ' . ($plan === 'pro' ? 'PRO 👑' : 'GRATIS'));
    })->name('tenant.switch-plan');
    
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

Route::get('/track/{order_number}', function ($order_number) {
    $order = App\Models\Order::withoutGlobalScopes()->with(['tenant', 'customer', 'items.service'])
            ->where('order_number', $order_number)->firstOrFail();
    
    return view('track', compact('order'));
})->name('track');

Route::get('/run-migration', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();
        
        return response("
        <html>
        <head><title>Database Migration & Seed</title></head>
        <body style='background:#0f172a; color:#f8fafc; font-family:sans-serif; padding:40px;'>
            <div style='max-width:700px; margin:0 auto; background:#1e293b; padding:30px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.5);'>
                <h2 style='color:#38bdf8; margin-top:0;'>✅ Database Neon Berhasil Dimigrasi!</h2>
                <p style='color:#94a3b8;'>Tabel dan data bawaan (seeder) telah sukses dibuat di PostgreSQL.</p>
                
                <h4 style='color:#e2e8f0; margin-bottom:5px;'>Hasil Migrate:</h4>
                <pre style='background:#090d16; padding:15px; border-radius:8px; overflow-x:auto; color:#a7f3d0;'>{$migrateOutput}</pre>
                
                <h4 style='color:#e2e8f0; margin-bottom:5px;'>Hasil Seeder:</h4>
                <pre style='background:#090d16; padding:15px; border-radius:8px; overflow-x:auto; color:#a7f3d0;'>{$seedOutput}</pre>
                
                <div style='margin-top:25px; display:flex; gap:12px;'>
                    <a href='/' style='background:#2563eb; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:bold;'>Buka Halaman Utama &rarr;</a>
                    <a href='/track/LDR-20260915-0001' style='background:#334155; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:bold;'>Cek Lacak Nota Demo</a>
                </div>
            </div>
        </body>
        </html>
        ");
    } catch (\Throwable $e) {
        return response("
        <html>
        <body style='background:#0f172a; color:#f8fafc; font-family:sans-serif; padding:40px;'>
            <div style='max-width:700px; margin:0 auto; background:#450a0a; border:1px solid #dc2626; padding:30px; border-radius:12px;'>
                <h2 style='color:#f87171; margin-top:0;'>❌ Migrasi Gagal</h2>
                <pre style='background:#1c0303; padding:15px; border-radius:8px; overflow-x:auto; color:#fca5a5;'>{$e->getMessage()}</pre>
            </div>
        </body>
        </html>
        ", 500);
    }
});
