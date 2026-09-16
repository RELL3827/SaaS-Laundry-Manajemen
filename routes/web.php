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
