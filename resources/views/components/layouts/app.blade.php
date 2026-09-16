<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'LaundryPro SaaS' }}</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex h-screen overflow-hidden" x-data="{ showUpgradeModal: false }">
    @auth
    @php
        $tenant = auth()->user()->tenant;
        $isPro = $tenant?->isPro() ?? false;
        $monthlyUsed = $tenant?->getMonthlyOrdersCount() ?? 0;
        $orderLimit = $tenant?->getOrderLimit() ?? 30;
        $percentUsed = min(100, round(($monthlyUsed / max(1, $orderLimit)) * 100));
    @endphp

    <aside class="w-64 bg-white border-r border-gray-200 flex-shrink-0 hidden md:flex flex-col justify-between">
        <div>
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-blue-600">LaundryPro</span>
                </div>
                @if($isPro)
                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-900 uppercase tracking-wider shadow-xs flex items-center gap-1">
                        👑 PRO
                    </span>
                @else
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                        GRATIS
                    </span>
                @endif
            </div>

            <nav class="py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/customers" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('customers*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Customer
                        </a>
                    </li>
                    <li>
                        <a href="/services" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('services*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Layanan
                        </a>
                    </li>
                    <li>
                        <a href="/orders" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('orders*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Order
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Plan Status Widget in Sidebar -->
        <div class="p-3">
            @if(!$isPro)
                <!-- Free Plan Card with Usage Progress -->
                <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Paket Starter
                        </span>
                        <span class="text-[11px] text-slate-500">{{ $monthlyUsed }}/{{ $orderLimit }} Nota</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden mb-2">
                        <div class="h-full rounded-full transition-all duration-300 {{ $percentUsed >= 90 ? 'bg-rose-500' : ($percentUsed >= 70 ? 'bg-amber-500' : 'bg-blue-600') }}" style="width: {{ $percentUsed }}%"></div>
                    </div>

                    <p class="text-[11px] text-slate-500 mb-2.5 leading-tight">
                        @if($monthlyUsed >= $orderLimit)
                            <span class="text-rose-600 font-bold">Kuota order bulan ini telah habis!</span>
                        @else
                            Sisa {{ max(0, $orderLimit - $monthlyUsed) }} order bulan ini.
                        @endif
                    </p>

                    <button @click="showUpgradeModal = true" type="button" class="w-full py-1.5 px-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-[11px] text-center shadow-xs flex items-center justify-center gap-1 transition-all">
                        <span>Upgrade ke Pro ⚡</span>
                    </button>
                </div>
            @else
                <!-- Pro Plan Card -->
                <div class="bg-gradient-to-br from-amber-500/10 via-amber-400/5 to-yellow-500/10 rounded-xl p-3.5 border border-amber-300/80 text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-extrabold text-amber-900 flex items-center gap-1">
                            👑 Outlet PRO
                        </span>
                        <span class="text-[10px] bg-amber-200/80 text-amber-900 font-bold px-1.5 py-0.5 rounded">Aktif</span>
                    </div>
                    <p class="text-[11px] text-amber-800 leading-tight mb-2">
                        Semua fitur premium aktif: Unlimited Order, WhatsApp & Struk Bersih.
                    </p>
                    <button @click="showUpgradeModal = true" type="button" class="w-full py-1 px-2 rounded-lg bg-white/90 hover:bg-white text-slate-700 border border-amber-200 font-semibold text-[10px] text-center transition-colors">
                        Kelola Status Paket
                    </button>
                </div>
            @endif

            <!-- User Info & Logout -->
            <div class="pt-3 mt-3 border-t border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="truncate mr-2">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $tenant?->name ?? 'Laundry' }}</p>
                    </div>
                    <span class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded font-medium shrink-0">{{ auth()->user()->role?->display_name ?? 'Admin' }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </aside>
    @endauth

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        @auth
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 md:hidden">
            <div class="flex items-center gap-2">
                <span class="text-xl font-bold text-blue-600">LaundryPro</span>
                @if($isPro ?? false)
                    <span class="px-1.5 py-0.5 text-[9px] font-black rounded-full bg-amber-400 text-slate-900">👑 PRO</span>
                @else
                    <button @click="showUpgradeModal = true" class="px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-blue-100 text-blue-700">⚡ Upgrade</button>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs text-gray-600 font-medium">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Keluar</button>
                </form>
            </div>
        </header>
        @endauth
        
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8">
            <!-- Global Flash Message -->
            @if(session('success'))
                <div class="mb-5 max-w-7xl mx-auto flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm font-medium shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 max-w-7xl mx-auto flex items-center justify-between p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-sm font-medium shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Modal Perbandingan & Switch / Upgrade Paket -->
    @auth
    <div x-show="showUpgradeModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Backdrop -->
            <div x-show="showUpgradeModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="showUpgradeModal = false" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showUpgradeModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Perbandingan Paket Langganan</span>
                            <h3 class="text-xl font-extrabold text-slate-900">Tingkatkan Bisnis Laundry Anda</h3>
                        </div>
                        <button @click="showUpgradeModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Comparison Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
                        <!-- Free Plan Summary -->
                        <div class="rounded-2xl p-5 border {{ !$isPro ? 'border-blue-500 bg-blue-50/40 ring-2 ring-blue-500/20' : 'border-slate-200 bg-slate-50/50' }} flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-extrabold text-slate-900">Paket Starter</h4>
                                    @if(!$isPro)
                                        <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Paket Anda Saat Ini</span>
                                    @endif
                                </div>
                                <p class="text-2xl font-black text-slate-900 mb-4">Gratis <span class="text-xs font-normal text-slate-500">/ Rp 0</span></p>
                                <ul class="space-y-2 text-xs text-slate-600">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span>Maksimal <b>30 Order/bulan</b></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span>POS Kasir & Status Cucian</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-slate-400">
                                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        <span>Ada watermark di struk kasir</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-slate-400">
                                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        <span>Tanpa Ekspor Laporan Excel/CSV</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-slate-400">
                                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        <span>Tanpa Nota WhatsApp Otomatis</span>
                                    </li>
                                </ul>
                            </div>

                            @if($isPro)
                                <form method="POST" action="{{ route('tenant.switch-plan') }}" class="mt-5">
                                    @csrf
                                    <input type="hidden" name="plan" value="free">
                                    <button type="submit" class="w-full py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-colors">
                                        Uji Coba / Turun ke Paket Gratis
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Pro Plan Summary -->
                        <div class="rounded-2xl p-5 border-2 {{ $isPro ? 'border-amber-500 bg-amber-50/40 ring-2 ring-amber-400/30' : 'border-blue-600 bg-blue-50/30' }} flex flex-col justify-between relative shadow-sm">
                            <div class="absolute -top-3 right-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-extrabold text-[10px] uppercase px-3 py-0.5 rounded-full shadow-xs">
                                Sangat Direkomendasikan ⭐
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-extrabold text-slate-900 flex items-center gap-1">
                                        <span>Paket Pro Member</span>
                                        <span class="text-amber-500 text-sm">👑</span>
                                    </h4>
                                    @if($isPro)
                                        <span class="text-[10px] font-bold bg-amber-200 text-amber-900 px-2 py-0.5 rounded-full">Paket Anda Saat Ini</span>
                                    @endif
                                </div>
                                <p class="text-2xl font-black text-slate-900 mb-4">Rp 99.000 <span class="text-xs font-normal text-slate-500">/ bulan</span></p>
                                <ul class="space-y-2 text-xs text-slate-700">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span><b>Unlimited Order Masuk</b> (Tanpa Batas)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span><b>Nota WhatsApp Otomatis</b> langsung kirim</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span><b>Ekspor Laporan Transaksi</b> format CSV/Excel</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span><b>Struk Kasir Bersih</b> tanpa watermark sponsor</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span>Dukungan Prioritas Respons Cepat</span>
                                    </li>
                                </ul>
                            </div>

                            @if(!$isPro)
                                <form method="POST" action="{{ route('tenant.switch-plan') }}" class="mt-5">
                                    @csrf
                                    <input type="hidden" name="plan" value="pro">
                                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/25 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                        <span>Aktifkan Paket Pro Sekarang ⚡</span>
                                    </button>
                                </form>
                            @else
                                <div class="mt-5 py-2 px-3 bg-amber-100/70 rounded-xl text-center text-amber-900 font-bold text-xs">
                                    ✓ Akun Anda sudah Pro Member
                                </div>
                            @endif
                        </div>
                    </div>

                    <p class="text-[11px] text-center text-slate-400">
                        Peralihan paket dapat dicoba secara instan untuk melihat langsung perbedaannya pada sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endauth
</body>
</html>
