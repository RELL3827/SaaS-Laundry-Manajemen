<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaundryPro - Sistem Manajemen & Kasir POS Laundry Modern</title>
    <meta name="description" content="Aplikasi kasir POS laundry modern, nota digital WhatsApp otomatis, pelacakan cucian real-time, dan laporan keuangan multi-outlet.">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white">

    <!-- Background Glow Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-gradient-to-tr from-blue-400/20 via-indigo-300/20 to-sky-300/10 rounded-full blur-3xl"></div>
        <div class="absolute top-[45%] -right-40 w-[600px] h-[600px] bg-indigo-400/10 rounded-full blur-3xl"></div>
        <div class="absolute top-[75%] -left-40 w-[600px] h-[600px] bg-blue-400/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900">Laundry<span class="text-blue-600">Pro</span></span>
                        <span class="block text-[10px] uppercase font-bold tracking-widest text-slate-400 -mt-1">SaaS Management</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Fitur Unggulan</a>
                    <a href="#kalkulator" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Simulasi Omzet</a>
                    <a href="#alur" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Cara Kerja</a>
                    <a href="#harga" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Paket Harga</a>
                    <a href="#lacak" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Cek Status Nota</a>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-md shadow-blue-500/20 hover:bg-blue-700 active:scale-95 transition-all">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-slate-700 hover:text-blue-600 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-md shadow-blue-500/20 hover:bg-blue-700 active:scale-95 transition-all">
                            Coba Gratis Sekarang
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="pt-16 pb-20 lg:pt-24 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <!-- Pill Tag -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold tracking-wide shadow-xs mb-6 animate-bounce duration-1000">
                    <span class="flex h-2 w-2 rounded-full bg-blue-600"></span>
                    <span>Platform Manajemen Laundry #1 Berbasis Cloud</span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15] mb-6">
                    Kelola Bisnis Laundry Lebih <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Cepat, Rapi, & Omzet Terpantau</span>.
                </h1>

                <!-- Subheadline -->
                <p class="text-lg sm:text-xl text-slate-600 leading-relaxed mb-10">
                    Tinggalkan buku nota manual. Nikmati kasir POS instan 30 detik, nota digital WhatsApp otomatis, pelacakan cucian real-time oleh pelanggan, dan rekapan omzet harian yang akurat.
                </p>

                <!-- CTA Button Group -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-blue-600 text-white font-bold text-base shadow-xl shadow-blue-600/30 hover:bg-blue-700 hover:shadow-blue-600/40 active:scale-95 transition-all">
                        <span>Mulai Uji Coba Gratis 14 Hari</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="#lacak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl bg-white border border-slate-300 text-slate-700 font-semibold text-base shadow-xs hover:bg-slate-50 active:scale-95 transition-all">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Coba Lacak Pesanan</span>
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="flex flex-wrap items-center justify-center gap-6 text-xs font-semibold text-slate-500">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Tanpa Kartu Kredit</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Setup Instan 2 Menit</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Bisa di HP, Tablet, & Laptop</span>
                </div>
            </div>

            <!-- Hero Interactive Dashboard Visual Showcase -->
            <div class="mt-14 relative max-w-5xl mx-auto">
                <div class="rounded-3xl p-2 sm:p-4 bg-gradient-to-b from-slate-200 to-slate-300 shadow-2xl border border-white/60">
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-inner">
                        <!-- Browser Bar Simulation -->
                        <div class="bg-slate-100/90 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            </div>
                            <div class="flex items-center gap-2 bg-white px-3 py-1 rounded-lg border border-slate-200 text-xs text-slate-500 font-mono w-64 justify-center">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>app.laundrypro.id/dashboard</span>
                            </div>
                            <div class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Live Demo</div>
                        </div>

                        <!-- Mock Dashboard Body -->
                        <div class="p-6 sm:p-8 bg-slate-50/60">
                            <!-- Metrics Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Omzet Hari Ini</p>
                                    <p class="text-2xl font-black text-slate-900 mt-1">Rp 1.840.000</p>
                                    <span class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-1 mt-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                        +24% dari kemarin
                                    </span>
                                </div>
                                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Order Masuk</p>
                                    <p class="text-2xl font-black text-blue-600 mt-1">38 Nota</p>
                                    <span class="text-xs font-semibold text-slate-500 mt-1 block">12 cucian siap diambil</span>
                                </div>
                                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-xs">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggan Aktif</p>
                                    <p class="text-2xl font-black text-indigo-600 mt-1">214 Orang</p>
                                    <span class="text-xs font-semibold text-emerald-600 mt-1 block">94% Repeat Order</span>
                                </div>
                            </div>

                            <!-- Mock Orders Table Preview -->
                            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs">
                                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pesanan Terbaru Hari Ini</h3>
                                    <span class="text-xs font-semibold text-blue-600">Update Real-Time</span>
                                </div>
                                <div class="divide-y divide-slate-100 text-xs">
                                    <div class="px-5 py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 font-bold flex items-center justify-center">#01</div>
                                            <div>
                                                <p class="font-bold text-slate-900">Ibu Shinta Bella</p>
                                                <p class="text-slate-400">Cuci + Setrika Kilat (4.5 Kg)</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-[11px]">Dicuci</span>
                                            <span class="font-bold text-slate-900">Rp 45.000</span>
                                        </div>
                                    </div>
                                    <div class="px-5 py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center">#02</div>
                                            <div>
                                                <p class="font-bold text-slate-900">Mas Rian Hidayat</p>
                                                <p class="text-slate-400">Bed Cover King & Sepatu Sneaker</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[11px]">Selesai</span>
                                            <span class="font-bold text-slate-900">Rp 55.000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: LIVE ORDER TRACKER WIDGET -->
    <section id="lacak" class="py-16 bg-white border-y border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="bg-gradient-to-tr from-slate-900 to-indigo-950 rounded-3xl p-8 sm:p-12 text-white shadow-2xl relative overflow-hidden">
                <!-- Background ambient circle -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 text-center max-w-xl mx-auto">
                    <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-blue-300 text-xs font-bold uppercase tracking-wider mb-3 border border-white/10">
                        Fitur Ramah Pelanggan
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">Lacak Cucian Pelanggan Tanpa Login</h2>
                    <p class="text-slate-300 text-sm mb-8">
                        Beri kenyamanan ekstra bagi pelanggan Anda! Cukup ketik nomor nota di bawah ini untuk melihat status cucian terkini.
                    </p>

                    <form onsubmit="event.preventDefault(); trackOrder();" class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
                        <input id="orderNumberInput" type="text" placeholder="Contoh: LDR-20260915-0001" class="flex-1 px-4 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white/20 transition-all font-mono" required>
                        <button type="submit" class="px-6 py-3.5 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-sm shadow-md transition-all active:scale-95 shrink-0 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span>Lacak Sekarang</span>
                        </button>
                    </form>

                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-slate-400">
                        <span>Coba nomor demo:</span>
                        <button type="button" onclick="setDemoOrder('LDR-20260915-0001')" class="text-blue-300 hover:underline font-mono font-medium">LDR-20260915-0001</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: INTERACTIVE REVENUE & ROI CALCULATOR -->
    <section id="kalkulator" class="py-20 lg:py-28 bg-slate-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-2">Simulasi Keuntungan</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Hitung Potensi Omzet Laundry Anda
                </h2>
                <p class="text-slate-600 text-base mt-3">
                    Geser slider di bawah untuk melihat estimasi pemasukan dan waktu operasional yang dihemat dengan sistem otomatis LaundryPro.
                </p>
            </div>

            <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    <!-- Left: Sliders -->
                    <div class="lg:col-span-7 space-y-8">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-bold text-slate-700">Rata-rata Cucian per Hari (Kg):</label>
                                <span id="weightDisplay" class="text-lg font-extrabold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">60 Kg</span>
                            </div>
                            <input id="weightSlider" type="range" min="10" max="300" step="5" value="60" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                            <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                                <span>10 Kg (Pemula)</span>
                                <span>150 Kg (Ramai)</span>
                                <span>300 Kg (Besar)</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-bold text-slate-700">Tarif Rata-rata per Kg (Rp):</label>
                                <span id="priceDisplay" class="text-lg font-extrabold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">Rp 7.000</span>
                            </div>
                            <input id="priceSlider" type="range" min="4000" max="15000" step="500" value="7000" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                            <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                                <span>Rp 4.000 / kg</span>
                                <span>Rp 8.000 / kg</span>
                                <span>Rp 15.000 / kg</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60 text-xs text-slate-600 flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold">i</div>
                            <p>Perhitungan di samping adalah estimasi pendapatan kotor bulanan dengan asumsi 30 hari operasional penuh. LaundryPro membantu mencegah kebocoran nota dan nota hilang hingga 100%.</p>
                        </div>
                    </div>

                    <!-- Right: Results Card -->
                    <div class="lg:col-span-5 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-6 sm:p-8 shadow-xl flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-blue-200 uppercase tracking-widest block">Proyeksi Omzet Bulanan</span>
                            <div class="mt-2 mb-6">
                                <span id="monthlyRevenue" class="text-3xl sm:text-4xl font-black tracking-tight">Rp 12.600.000</span>
                                <span class="text-xs text-blue-200 block mt-1">/ bulan (30 hari operasional)</span>
                            </div>

                            <div class="space-y-4 pt-6 border-t border-white/20 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-100">Waktu Catat Nota Dihemat:</span>
                                    <span id="timeSaved" class="font-extrabold text-white text-sm">30 Jam / bln</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-100">Efisiensi Biaya Kertas Nota:</span>
                                    <span class="font-extrabold text-emerald-300 text-sm">Hemat 80% (WhatsApp)</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-100">Pemberitahuan Cucian Selesai:</span>
                                    <span class="font-extrabold text-white text-sm">Otomatis 100%</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register') }}" class="mt-8 w-full py-3 px-4 bg-white text-blue-600 hover:bg-blue-50 font-bold text-sm rounded-xl text-center shadow-md active:scale-95 transition-all">
                            Capai Omzet Ini Sekarang &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: 6 CORE FEATURES -->
    <section id="fitur" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-2">Fitur Dirancang Khusus Laundry</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Semua yang Anda Butuhkan untuk Mengembangkan Bisnis Laundry
                </h2>
                <p class="text-slate-600 text-base mt-3">
                    Dirancang berdasarkan kendala nyata pemilik laundry: nota tercecer, kasir lama mengantre, dan status cucian yang membingungkan pelanggan.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Kasir POS Cepat (30 Detik)</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Input pesanan secepat kilat dengan wizard 4 langkah. Pilih pelanggan, klik layanan kiloan/satuan, pilih metode pembayaran, dan nota selesai dibuat.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Nota WhatsApp & Print Struk</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Kirim link nota digital via WhatsApp langsung ke ponsel pelanggan tanpa boros kertas thermal, atau cetak struk rapi dengan printer kasir bluetooth.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-bold mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Pelacakan Status Real-Time</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Pelanggan dapat memantau status pengerjaan cucian secara mandiri dari tautan nota (Diterima &rarr; Dicuci &rarr; Dikeringkan &rarr; Selesai).
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Layanan Kiloan & Satuan</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Dukung berbagai model bisnis: Cuci Komplit kiloan, Cuci Kering, Setrika Saja, Bed Cover, Jas, Karpet, hingga Cuci Sepatu premium.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center font-bold mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Sistem Poin Loyalitas</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Tingkatkan retensi pelanggan dengan otomatisasi poin loyalitas setiap transaksi. Pelanggan akan selalu kembali mencuci di outlet Anda.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-slate-50 hover:bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Laporan Omzet Otomatis</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Lihat omzet harian, mingguan, dan bulanan kapan saja dari smartphone Anda. Cegah kecurangan kasir dengan pencatatan audit digital yang rapi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: 4 STEPS HOW IT WORKS -->
    <section id="alur" class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-2">Alur Kerja Praktis</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    4 Langkah Kerja Lebih Efisien Bersama LaundryPro
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
                    <span class="text-4xl font-black text-blue-100 absolute top-4 right-4">01</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold mb-4">1</div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Terima & Timbang</h3>
                    <p class="text-xs text-slate-500">Pakaian kotor diterima di kasir, ditimbang, dan didata nama pelanggan.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
                    <span class="text-4xl font-black text-blue-100 absolute top-4 right-4">02</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold mb-4">2</div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Input Nota 30 Detik</h3>
                    <p class="text-xs text-slate-500">Pilih paket layanan dan otomatis kirim nota digital ke WhatsApp pelanggan.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
                    <span class="text-4xl font-black text-blue-100 absolute top-4 right-4">03</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold mb-4">3</div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Proses & Update Status</h3>
                    <p class="text-xs text-slate-500">Tim operasional mengupdate status dari dicuci hingga siap diambil.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs relative">
                    <span class="text-4xl font-black text-blue-100 absolute top-4 right-4">04</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold mb-4">4</div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Ambil & Bayar</h3>
                    <p class="text-xs text-slate-500">Pelanggan mengambil cucian wangi & rapi. Omzet langsung tercatat di laporan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: PRICING -->
    <section id="harga" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-2">Paket Langganan Terjangkau</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Investasi Terbaik untuk Menumbuhkan Outlet Anda
                </h2>
                <p class="text-slate-600 text-base mt-3">
                    Pilih paket sesuai skala bisnis laundry Anda. Tanpa komitmen jangka panjang, batalkan kapan saja.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch max-w-5xl mx-auto">
                <!-- Plan 1: Starter -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Starter Laundry</h3>
                        <p class="text-xs text-slate-500 mb-6">Cocok untuk outlet laundry rumahan baru.</p>
                        <div class="mb-6">
                            <span class="text-4xl font-black text-slate-900">Gratis</span>
                            <span class="text-xs text-slate-500 block mt-1">Uji Coba 14 Hari Pertama</span>
                        </div>
                        <ul class="space-y-3 text-xs text-slate-600 mb-8">
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> 1 Outlet Laundry</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> POS Kasir Unlimited Order</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Pelacakan Status Cucian Online</li>
                            <li class="flex items-center gap-2 text-slate-400"><svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> Multi-kasir & Staf</li>
                        </ul>
                    </div>
                    <a href="{{ route('register', ['plan' => 'free']) }}" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center transition-colors">
                        Daftar Gratis
                    </a>
                </div>

                <!-- Plan 2: Pro (Featured) -->
                <div class="bg-gradient-to-b from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-2xl relative flex flex-col justify-between border-2 border-blue-400">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-400 text-slate-900 font-extrabold text-[11px] uppercase tracking-wider px-4 py-1 rounded-full shadow-md">
                        Paling Populer
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white mb-1">Pro Laundry</h3>
                        <p class="text-xs text-blue-100 mb-6">Paling banyak dipilih pemilik laundry berkembang.</p>
                        <div class="mb-6">
                            <span class="text-4xl font-black text-white">Rp 99.000</span>
                            <span class="text-xs text-blue-200 block mt-1">/ bulan per outlet</span>
                        </div>
                        <ul class="space-y-3 text-xs text-blue-100 mb-8">
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Semua Fitur Starter</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Multi Kasir & Role Staf Produksi</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Nota Digital WhatsApp Otomatis</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Sistem Poin Loyalitas Pelanggan</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Laporan Omzet & Keuangan Harian</li>
                        </ul>
                    </div>
                    <a href="{{ route('register', ['plan' => 'pro']) }}" class="w-full py-3.5 rounded-xl bg-white hover:bg-blue-50 text-blue-600 font-extrabold text-xs text-center shadow-md active:scale-95 transition-all">
                        Pilih Paket Pro Sekarang
                    </a>
                </div>

                <!-- Plan 3: Multi-Outlet -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Franchise & Cabang</h3>
                        <p class="text-xs text-slate-500 mb-6">Solusi laundry multi-cabang terintegrasi.</p>
                        <div class="mb-6">
                            <span class="text-4xl font-black text-slate-900">Rp 249.000</span>
                            <span class="text-xs text-slate-500 block mt-1">/ bulan (Hingga 5 Cabang)</span>
                        </div>
                        <ul class="space-y-3 text-xs text-slate-600 mb-8">
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Semua Fitur Pro</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Dashboard Monitoring Multi-Outlet</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Prioritas Support WhatsApp 24/7</li>
                        </ul>
                    </div>
                    <a href="{{ route('register', ['plan' => 'pro']) }}" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center transition-colors">
                        Hubungi Sales
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: TESTIMONIALS -->
    <section class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-2">Cerita Sukses Mitra</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Dipercaya oleh Ratusan Pemilik Laundry di Seluruh Indonesia
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex text-amber-400 mb-4">★★★★★</div>
                        <p class="text-slate-700 text-sm italic leading-relaxed mb-6">
                            "Dulu kasir sering selisih omzet karena nota kertas hilang. Sejak pakai LaundryPro, semua tercatat otomatis dan pelanggan senang sekali bisa lacak status cucian dari WhatsApp."
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">H</div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">Hendra Gunawan</p>
                            <p class="text-xs text-slate-400">Owner Berkah Laundry Express (Bandung)</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex text-amber-400 mb-4">★★★★★</div>
                        <p class="text-slate-700 text-sm italic leading-relaxed mb-6">
                            "Fitur poin loyalitasnya beneran manjur! Pelanggan jadi langganan tetap dan semangat ngumpulin poin. Omzet laundry kami naik 30% dalam 2 bulan pertama."
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm">S</div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">Siti Rahmawati</p>
                            <p class="text-xs text-slate-400">Owner Melati Wash & Dry (Jakarta Selatan)</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex text-amber-400 mb-4">★★★★★</div>
                        <p class="text-slate-700 text-sm italic leading-relaxed mb-6">
                            "Aplikasi ini sangat ringan, dibuka di HP android kasir pun lancar jaya. Mau cetak struk bluetooth tinggal klik satu kali. Benar-benar ngebantu operasional harian!"
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm">A</div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm">Aditya Pratama</p>
                            <p class="text-xs text-slate-400">Owner CleanHub Laundry (Surabaya)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: FAQ ACCORDION -->
    <section class="py-20 lg:py-28 bg-white border-t border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-16">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-2">FAQ</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
            </div>

            <div class="space-y-4">
                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50">
                    <button type="button" onclick="toggleFaq(1)" class="w-full flex justify-between items-center text-left font-bold text-slate-900 text-base">
                        <span>Apakah saya harus menginstall aplikasi khusus di komputer kasir?</span>
                        <span id="faq-icon-1" class="text-slate-400 text-xl font-bold">+</span>
                    </button>
                    <div id="faq-ans-1" class="hidden text-sm text-slate-600 mt-3 pt-3 border-t border-slate-200/60 leading-relaxed">
                        Tidak perlu! LaundryPro adalah aplikasi berbasis cloud (web-based) yang bisa diakses langsung lewat browser Chrome di smartphone Android, iPhone, iPad, tablet kasir, ataupun laptop PC tanpa repot install file besar.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50">
                    <button type="button" onclick="toggleFaq(2)" class="w-full flex justify-between items-center text-left font-bold text-slate-900 text-base">
                        <span>Bagaimana cara mencetak struk untuk pelanggan?</span>
                        <span id="faq-icon-2" class="text-slate-400 text-xl font-bold">+</span>
                    </button>
                    <div id="faq-ans-2" class="hidden text-sm text-slate-600 mt-3 pt-3 border-t border-slate-200/60 leading-relaxed">
                        Anda bisa mencetak struk thermal 58mm atau 80mm menggunakan printer thermal bluetooth/USB standar. Selain itu, Anda juga bisa membagikan nota digital gratis via tautan WhatsApp langsung ke nomor pelanggan!
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50">
                    <button type="button" onclick="toggleFaq(3)" class="w-full flex justify-between items-center text-left font-bold text-slate-900 text-base">
                        <span>Apakah data pelanggan dan omzet saya aman?</span>
                        <span id="faq-icon-3" class="text-slate-400 text-xl font-bold">+</span>
                    </button>
                    <div id="faq-ans-3" class="hidden text-sm text-slate-600 mt-3 pt-3 border-t border-slate-200/60 leading-relaxed">
                        Sangat aman. Setiap outlet laundry memiliki tenant id tersendiri (*multi-tenant data isolation*) yang dienkripsi ketat. Data laundry Anda tidak akan pernah bisa dilihat oleh outlet lain.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl p-6 bg-slate-50/50">
                    <button type="button" onclick="toggleFaq(4)" class="w-full flex justify-between items-center text-left font-bold text-slate-900 text-base">
                        <span>Apakah saya bisa menambahkan layanan satuan selain kiloan?</span>
                        <span id="faq-icon-4" class="text-slate-400 text-xl font-bold">+</span>
                    </button>
                    <div id="faq-ans-4" class="hidden text-sm text-slate-600 mt-3 pt-3 border-t border-slate-200/60 leading-relaxed">
                        Tentu saja! Anda bebas menambahkan jenis layanan apapun: kiloan (cuci kering/komplit), satuan (bed cover, jas, gaun), per meter (karpet/gorden), hingga per pasang (sepatu) dengan harga yang dapat disesuaikan sesuka Anda.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: BOTTOM HERO CTA -->
    <section class="py-20 bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-700 text-white relative overflow-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-6">
                Siap Bikin Bisnis Laundry Anda Lebih Modern & Cepat Untung?
            </h2>
            <p class="text-blue-100 text-base sm:text-lg mb-10 max-w-2xl mx-auto">
                Bergabunglah dengan ratusan pengusaha laundry cerdas yang telah menghemat waktu dan meningkatkan omzet dengan LaundryPro.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-2xl bg-white text-blue-600 font-extrabold text-base shadow-xl hover:bg-blue-50 active:scale-95 transition-all">
                    Daftar Outlet Sekarang (Gratis 14 Hari)
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 rounded-2xl bg-blue-800/60 hover:bg-blue-800 text-white font-semibold text-base border border-white/20 active:scale-95 transition-all">
                    Sudah Punya Akun? Masuk
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-950 text-slate-400 py-12 border-t border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-base font-bold text-white tracking-tight">Laundry<span class="text-blue-500">Pro</span></span>
                </div>
                <div class="flex flex-wrap gap-6 text-slate-400">
                    <a href="#fitur" class="hover:text-white transition-colors">Fitur</a>
                    <a href="#kalkulator" class="hover:text-white transition-colors">Simulasi</a>
                    <a href="#harga" class="hover:text-white transition-colors">Harga</a>
                    <a href="#lacak" class="hover:text-white transition-colors">Lacak Pesanan</a>
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors">Portal Kasir</a>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-slate-300 font-medium">Sistem Beroperasi Normal</span>
                </div>
            </div>
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-slate-500">
                <p>&copy; {{ date('Y') }} LaundryPro SaaS. Hak Cipta Dilindungi Undang-Undang.</p>
                <p>Platform Manajemen Usaha Laundry Masa Depan.</p>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts for Landing Page -->
    <script>
        // Interactive Revenue Calculator
        const weightSlider = document.getElementById('weightSlider');
        const priceSlider = document.getElementById('priceSlider');
        const weightDisplay = document.getElementById('weightDisplay');
        const priceDisplay = document.getElementById('priceDisplay');
        const monthlyRevenue = document.getElementById('monthlyRevenue');
        const timeSaved = document.getElementById('timeSaved');

        function updateCalculator() {
            const weight = parseInt(weightSlider.value);
            const price = parseInt(priceSlider.value);
            const monthlyTotal = weight * price * 30;
            const hours = Math.round((weight * 30 * 0.75) / 60);

            weightDisplay.innerText = weight + ' Kg';
            priceDisplay.innerText = 'Rp ' + price.toLocaleString('id-ID');
            monthlyRevenue.innerText = 'Rp ' + monthlyTotal.toLocaleString('id-ID');
            timeSaved.innerText = Math.max(hours, 20) + ' Jam / bln';
        }

        if (weightSlider && priceSlider) {
            weightSlider.addEventListener('input', updateCalculator);
            priceSlider.addEventListener('input', updateCalculator);
            updateCalculator();
        }

        // Quick Order Tracking Redirection
        function trackOrder() {
            const input = document.getElementById('orderNumberInput');
            if (input && input.value.trim()) {
                window.location.href = '/track/' + encodeURIComponent(input.value.trim());
            }
        }

        function setDemoOrder(num) {
            const input = document.getElementById('orderNumberInput');
            if (input) {
                input.value = num;
                input.focus();
            }
        }

        // Accordion Toggle
        function toggleFaq(id) {
            const ans = document.getElementById('faq-ans-' + id);
            const icon = document.getElementById('faq-icon-' + id);
            if (ans.classList.contains('hidden')) {
                ans.classList.remove('hidden');
                icon.innerText = '−';
            } else {
                ans.classList.add('hidden');
                icon.innerText = '+';
            }
        }
    </script>
</body>
</html>
