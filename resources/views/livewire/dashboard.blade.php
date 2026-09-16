<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\Customer;

new #[Layout('components.layouts.app')] class extends Component
{
    public $totalOrders = 0;
    public $totalCustomers = 0;
    public $todayRevenue = 0;
    public $statusCounts = [];

    public function mount()
    {
        $tenantId = auth()->user()->tenant_id;
        
        $this->totalOrders = Order::where('tenant_id', $tenantId)->count();
        $this->totalCustomers = Customer::where('tenant_id', $tenantId)->count();
        $this->todayRevenue = Order::where('tenant_id', $tenantId)
                                ->whereDate('created_at', today())
                                ->sum('total');

        $stages = ['Diterima', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Selesai', 'Diambil'];
        foreach ($stages as $st) {
            $this->statusCounts[$st] = Order::where('tenant_id', $tenantId)->where('status', $st)->count();
        }
    }

    public function with()
    {
        $tenantId = auth()->user()->tenant_id;
        return [
            'recentOrders' => Order::with('customer')
                                ->where('tenant_id', $tenantId)
                                ->latest()
                                ->limit(5)
                                ->get()
        ];
    }
};
?>

<div class="p-4 md:p-6 space-y-8 max-w-7xl mx-auto">
    <!-- Header with Quick Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Outlet</h1>
            <p class="text-sm text-slate-500 mt-1">Selamat datang kembali, <span class="font-semibold text-slate-800">{{ auth()->user()->name }}</span> ({{ auth()->user()->tenant?->name }})</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/orders/create" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Terima Cucian Baru
            </a>
        </div>
    </div>

    <!-- Plan Status Banner -->
    @php
        $tenant = auth()->user()->tenant;
        $isPro = $tenant?->isPro() ?? false;
        $monthlyUsed = $tenant?->getMonthlyOrdersCount() ?? 0;
        $orderLimit = $tenant?->getOrderLimit() ?? 30;
        $remaining = max(0, $orderLimit - $monthlyUsed);
    @endphp

    @if(!$isPro)
        <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-3xl p-5 sm:p-6 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-5 border border-indigo-700/50">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-blue-500/30 text-blue-200 border border-blue-400/30 uppercase tracking-wider">
                        Paket Starter (Gratis)
                    </span>
                    <span class="text-xs text-blue-200/80">
                        • Sisa Kuota: <b class="text-white">{{ $remaining }}</b> dari {{ $orderLimit }} order bulan ini
                    </span>
                </div>
                <h3 class="text-lg sm:text-xl font-black text-white tracking-tight">
                    Tingkatkan ke Pro untuk Kuota Unlimited & Fitur WhatsApp
                </h3>
                <p class="text-xs text-blue-200/80 mt-1 leading-relaxed">
                    Pengguna Pro menikmati bebas kuota transaksi, kirim nota WA otomatis 1-klik, struk thermal bersih tanpa watermark, dan ekspor laporan Excel.
                </p>
            </div>
            <div class="relative z-10 flex items-center gap-3 shrink-0 w-full md:w-auto">
                <button @click="showUpgradeModal = true" type="button" class="w-full md:w-auto px-5 py-3 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 active:scale-95 transition-all text-center flex items-center justify-center gap-2">
                    <span>Upgrade ke Paket Pro ⚡</span>
                </button>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-amber-500/10 via-amber-400/5 to-yellow-500/10 rounded-2xl p-4 border border-amber-300/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white text-lg shadow-sm">
                    👑
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-extrabold text-slate-900">Status: Outlet Pro Aktif</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Unlimited</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">Semua fitur terbuka penuh: transaksi tanpa batas, nota WhatsApp resmi, ekspor CSV, dan cetak struk bersih.</p>
                </div>
            </div>
            <button @click="showUpgradeModal = true" type="button" class="text-xs font-bold text-amber-900 hover:text-amber-950 px-3 py-1.5 rounded-lg bg-amber-200/60 hover:bg-amber-200 transition-colors shrink-0">
                Lihat Keuntungan Pro &rarr;
            </button>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 flex items-center space-x-4">
            <div class="p-3.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Omzet Hari Ini</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 flex items-center space-x-4">
            <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Order Masuk</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalOrders }} <span class="text-xs font-medium text-slate-400">Nota</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 flex items-center space-x-4">
            <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan Terdaftar</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $totalCustomers }} <span class="text-xs font-medium text-slate-400">Orang</span></p>
            </div>
        </div>
    </div>

    <!-- Production Stage Monitor -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Monitoring Alur Produksi Cucian</h2>
                <p class="text-xs text-slate-500">Jumlah cucian yang sedang berada pada masing-masing tahapan</p>
            </div>
            <a href="/orders" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat Semua Order &rarr;</a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $stageConfigs = [
                    'Diterima' => ['color' => 'bg-slate-50 text-slate-700 border-slate-200', 'badge' => 'bg-slate-200'],
                    'Dicuci' => ['color' => 'bg-blue-50 text-blue-700 border-blue-200', 'badge' => 'bg-blue-200 text-blue-800'],
                    'Dikeringkan' => ['color' => 'bg-amber-50 text-amber-700 border-amber-200', 'badge' => 'bg-amber-200 text-amber-800'],
                    'Disetrika' => ['color' => 'bg-purple-50 text-purple-700 border-purple-200', 'badge' => 'bg-purple-200 text-purple-800'],
                    'Selesai' => ['color' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'badge' => 'bg-emerald-200 text-emerald-800'],
                    'Diambil' => ['color' => 'bg-teal-50 text-teal-700 border-teal-200', 'badge' => 'bg-teal-200 text-teal-800'],
                ];
            @endphp

            @foreach($stageConfigs as $stageName => $cfg)
                <a href="/orders" class="p-3.5 rounded-xl border {{ $cfg['color'] }} flex flex-col justify-between hover:shadow-sm transition-all group">
                    <span class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition-colors">{{ $stageName }}</span>
                    <span class="text-2xl font-black mt-2 text-slate-900">{{ $statusCounts[$stageName] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pesanan Masuk Terbaru</h2>
                <p class="text-xs text-slate-500">5 transaksi penerimaan cucian terakhir</p>
            </div>
            <a href="/orders" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Buka Manajemen Order &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No. Nota</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Pelanggan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status Cucian</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Total</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($recentOrders as $ro)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="font-mono text-xs font-bold text-blue-600">{{ $ro->order_number }}</span>
                            <span class="block text-[11px] text-slate-400">{{ $ro->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="text-xs font-bold text-slate-900">{{ $ro->customer->name ?? 'Pelanggan Umum' }}</span>
                            <span class="block text-[11px] text-slate-500">{{ $ro->customer->phone ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $ro->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs font-bold text-slate-900">
                            Rp {{ number_format($ro->total, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-right space-x-2">
                            <a href="/track/{{ $ro->order_number }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200">
                                Lacak
                            </a>
                            <a href="/orders/{{ $ro->id }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded-md border border-blue-200">
                                Detail & Ubah Status
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">
                            Belum ada pesanan terbaru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
