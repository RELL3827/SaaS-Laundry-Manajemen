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

Route::match(['get', 'post'], '/track/{order_number?}', function (\Illuminate\Http\Request $request, $order_number = null) {
    $searchNumber = $order_number ?? $request->input('order_number') ?? $request->query('q');

    $order = null;
    $searched = !empty($searchNumber);

    if ($searchNumber) {
        $searchClean = trim($searchNumber);
        $order = App\Models\Order::withoutGlobalScopes()
            ->with(['tenant', 'customer', 'items.service'])
            ->where('order_number', $searchClean)
            ->orWhereHas('customer', function($q) use ($searchClean) {
                $q->where('phone', $searchClean);
            })
            ->latest()
            ->first();
    }

    if ($request->isMethod('post') && $order) {
        return redirect()->route('track', ['order_number' => $order->order_number]);
    }

    return view('track', [
        'order' => $order,
        'searchNumber' => $searchNumber,
        'searched' => $searched,
    ]);
})->name('track');

Route::get('/api/db-check', function () {
    $results = [
        'timestamp' => now()->toIso8601String(),
        'default_connection' => config('database.default'),
        'env_detected' => [
            'has_DATABASE_URL' => !empty(env('DATABASE_URL')),
            'has_DB_URL' => !empty(env('DB_URL')),
            'has_POSTGRES_URL' => !empty(env('POSTGRES_URL')),
            'has_POSTGRES_URL_NON_POOLING' => !empty(env('POSTGRES_URL_NON_POOLING')),
            'has_DB_PASSWORD' => !empty(env('DB_PASSWORD')),
            'has_POSTGRES_PASSWORD' => !empty(env('POSTGRES_PASSWORD')),
            'DB_HOST_raw' => env('DB_HOST', '(not set)'),
            'DB_USERNAME_raw' => env('DB_USERNAME', '(not set)'),
            'DB_DATABASE_raw' => env('DB_DATABASE', '(not set)'),
        ],
        'resolved_pgsql_config' => (function() {
            $base = config('database.connections.pgsql');
            if (!empty($base['url'])) {
                $parsed = (new \Illuminate\Support\ConfigurationUrlParser())->parseConfiguration($base);
                return [
                    'source' => 'url',
                    'host' => $parsed['host'] ?? null,
                    'database' => $parsed['database'] ?? null,
                    'username' => $parsed['username'] ?? null,
                    'password_length' => strlen($parsed['password'] ?? ''),
                    'password_starts_with_endpoint' => str_starts_with($parsed['password'] ?? '', 'endpoint='),
                    'sslmode' => $parsed['sslmode'] ?? $base['sslmode'] ?? 'require',
                ];
            }
            return [
                'source' => 'direct_vars',
                'host' => $base['host'] ?? null,
                'database' => $base['database'] ?? null,
                'username' => $base['username'] ?? null,
                'password_length' => strlen($base['password'] ?? ''),
                'password_starts_with_endpoint' => str_starts_with($base['password'] ?? '', 'endpoint='),
                'sslmode' => $base['sslmode'] ?? 'require',
            ];
        })(),
    ];

    try {
        $pdo = \Illuminate\Support\Facades\DB::connection('pgsql')->getPdo();
        $results['status'] = 'CONNECTED';
        $results['message'] = 'Koneksi ke Neon PostgreSQL BERHASIL!';
        $results['users_count'] = \Illuminate\Support\Facades\DB::connection('pgsql')->table('users')->count();
    } catch (\Throwable $e) {
        $results['status'] = 'FAILED';
        $results['error_message'] = $e->getMessage();
        $results['error_code'] = $e->getCode();
    }

    return response()->json($results, $results['status'] === 'CONNECTED' ? 200 : 500, [], JSON_PRETTY_PRINT);
});

