<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan {{ $order->order_number }} - LaundryPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-decoration-none">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <span class="text-lg font-extrabold tracking-tight text-slate-900">Laundry<span class="text-blue-600">Pro</span></span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition-colors">
                    &larr; Beranda
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-10 px-4 sm:px-6">
        <div class="max-w-2xl mx-auto">
            <!-- Order Header Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60 mb-2">
                            Tracking Real-Time
                        </span>
                        <h1 class="text-2xl font-bold text-slate-900">{{ $order->order_number }}</h1>
                        <p class="text-xs text-slate-500 mt-1">Didaftarkan pada: {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outlet Laundry</p>
                        <p class="text-base font-bold text-slate-900">{{ $order->tenant?->name ?? 'LaundryPro Partner' }}</p>
                        @if($order->tenant?->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->tenant->phone) }}?text=Halo%20{{ urlencode($order->tenant->name) }},%20saya%20ingin%20tanya%20order%20{{ $order->order_number }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 hover:text-emerald-700 mt-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.861.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                            Hubungi Outlet
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Nama Pelanggan:</p>
                        <p class="font-semibold text-slate-800">{{ $order->customer?->name ?? 'Pelanggan Umum' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-medium">Status Pembayaran:</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $order->payment_status === 'Lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status Timeline Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 mb-6">
                <h2 class="text-lg font-bold text-slate-900 mb-6">Status Pengerjaan Cucian</h2>

                @php
                    $steps = [
                        'Diterima' => ['label' => 'Diterima', 'desc' => 'Pakaian diterima di outlet dan ditimbang.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        'Dicuci' => ['label' => 'Sedang Dicuci', 'desc' => 'Pakaian dalam proses pencucian higienis.', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                        'Dikeringkan' => ['label' => 'Dikeringkan', 'desc' => 'Proses pengeringan mesin agar bebas lembap.', 'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'],
                        'Disetrika' => ['label' => 'Disetrika & Packing', 'desc' => 'Pakaian disetrika rapi, wangi, dan dipacking.', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                        'Selesai' => ['label' => 'Siap Diambil', 'desc' => 'Pakaian telah selesai dan siap diambil pelanggan.', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'Diambil' => ['label' => 'Sudah Diambil', 'desc' => 'Pakaian telah diserahkan kepada pelanggan.', 'icon' => 'M5 13l4 4L19 7']
                    ];

                    $statusKeys = array_keys($steps);
                    $currentIndex = array_search($order->status, $statusKeys);
                    if ($currentIndex === false) $currentIndex = 0;
                @endphp

                <div class="relative pl-6 sm:pl-8 border-l-2 border-slate-200 space-y-8 my-2">
                    @foreach($steps as $key => $info)
                        @php
                            $stepIndex = array_search($key, $statusKeys);
                            $isPassed = $stepIndex < $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                        @endphp
                        <div class="relative group">
                            <!-- Bullet Icon -->
                            <div class="absolute -left-[35px] sm:-left-[43px] top-0 w-8 h-8 rounded-full flex items-center justify-center border-2 {{ $isCurrent ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-500/30 ring-4 ring-blue-100' : ($isPassed ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-300 text-slate-400') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $info['icon'] }}"></path></svg>
                            </div>

                            <div class="pl-2">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold {{ $isCurrent ? 'text-blue-600 text-base' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                                        {{ $info['label'] }}
                                    </h3>
                                    @if($isCurrent)
                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-blue-100 text-blue-700 animate-pulse">
                                        Proses Saat Ini
                                    </span>
                                    @elseif($isPassed)
                                    <span class="text-emerald-600 text-xs font-semibold">✓ Selesai</span>
                                    @endif
                                </div>
                                <p class="text-xs {{ $isCurrent ? 'text-slate-600 font-medium mt-0.5' : 'text-slate-400 mt-0.5' }}">
                                    {{ $info['desc'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 mb-8">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Rincian Layanan Cucian</h2>
                <div class="divide-y divide-slate-100">
                    @forelse($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $item->service?->name ?? 'Layanan' }}</p>
                            <p class="text-xs text-slate-500">{{ $item->qty }} {{ $item->service?->unit ?? 'kg' }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Rincian item tidak tersedia.</p>
                    @endforelse
                </div>

                <div class="pt-4 mt-4 border-t border-slate-200 space-y-2">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($order->subtotal ?? $order->total, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-xs text-rose-600">
                        <span>Diskon / Potongan:</span>
                        <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-100">
                        <span>Total Bayar:</span>
                        <span class="text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Callout -->
            <div class="text-center">
                <p class="text-xs text-slate-500">
                    Sistem Pelacakan Ditenagai oleh <a href="{{ route('home') }}" class="font-semibold text-blue-600 hover:underline">LaundryPro SaaS</a>
                </p>
            </div>
        </div>
    </main>
</body>
</html>
