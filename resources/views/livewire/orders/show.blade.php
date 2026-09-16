<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

new #[Layout('components.layouts.app')] class extends Component
{
    public $orderId;

    public function mount($id)
    {
        $this->orderId = $id;
    }

    public function with()
    {
        return [
            'order' => Order::with(['customer', 'user', 'items.service', 'payments', 'tenant'])->findOrFail($this->orderId),
        ];
    }

    public function updateStatus($newStatus)
    {
        $validStatuses = ['Diterima', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Selesai', 'Diambil'];
        if (!in_array($newStatus, $validStatuses)) {
            return;
        }

        $order = Order::findOrFail($this->orderId);
        $oldStatus = $order->status;
        $order->update(['status' => $newStatus]);
        session()->flash('success', "Status cucian berhasil diubah dari \"{$oldStatus}\" menjadi \"{$newStatus}\".");
    }

    public function advanceStatus()
    {
        $stages = ['Diterima', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Selesai', 'Diambil'];
        $order = Order::findOrFail($this->orderId);
        $currentIndex = array_search($order->status, $stages);

        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            $nextStatus = $stages[$currentIndex + 1];
            $order->update(['status' => $nextStatus]);
            session()->flash('success', "Status cucian berhasil dinaikkan ke tahap: {$nextStatus}!");
        }
    }

    public function markAsPaid()
    {
        $order = Order::findOrFail($this->orderId);
        $paidAmount = $order->payments->sum('amount');
        $remaining = $order->total - $paidAmount;

        if ($remaining > 0) {
            $order->payments()->create([
                'amount' => $remaining,
                'method' => 'Cash',
            ]);
        }

        $order->update(['payment_status' => 'Lunas']);
        session()->flash('success', 'Pembayaran order ini berhasil dicatat LUNAS!');
    }

    public function deleteOrder()
    {
        DB::transaction(function () {
            $order = Order::findOrFail($this->orderId);
            $order->items()->delete();
            $order->payments()->delete();
            $order->delete();
        });

        session()->flash('success', 'Pesanan berhasil dihapus.');
        return $this->redirect('/orders', navigate: true);
    }
};
?>

<div class="space-y-6 pb-12">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="/orders" wire:navigate.hover class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $order->order_number }}</h1>
                    @if($order->payment_status == 'Lunas')
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60">Lunas</span>
                    @elseif($order->payment_status == 'DP')
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60">DP</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200/60">Belum Bayar</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Dibuat pada {{ $order->created_at->translatedFormat('l, d F Y - H:i') }} WIB</p>
            </div>
        </div>

        @php
            $tenant = auth()->user()->tenant;
            $isPro = $tenant?->isPro() ?? false;
            $custPhone = $order->customer?->phone ?? '';
            $cleanPhone = preg_replace('/[^0-9]/', '', $custPhone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $waText = "Halo Kak {$order->customer?->name},\n\nTerima kasih telah mencuci di *{$order->tenant->name}*!\nBerikut rincian nota cucian Anda:\n\n📄 *No. Nota:* {$order->order_number}\n📊 *Status Cucian:* {$order->status}\n💰 *Total:* Rp " . number_format($order->total, 0, ',', '.') . " (" . strtoupper($order->payment_status) . ")\n\n🔍 *Lacak Status Cucian Online:* " . url('/track/'.$order->order_number) . "\n\nTerima kasih!";
            $waUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($waText);
        @endphp

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if($isPro)
                @if(!empty($cleanPhone))
                    <a href="{{ $waUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-2xs">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Kirim Nota WA</span>
                    </a>
                @endif
            @else
                <button @click="showUpgradeModal = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-300 transition-colors shadow-2xs" title="Kirim WhatsApp otomatis khusus pengguna Pro">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Kirim Nota WA</span>
                    <span class="text-[9px] bg-amber-200 text-amber-900 font-black px-1.5 py-0.2 rounded">PRO</span>
                </button>
            @endif

            <a href="/track/{{ $order->order_number }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Lacak Pelanggan
            </a>

            <a href="/orders/{{ $order->id }}/print" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition-colors shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Nota
            </a>

            <button wire:click="deleteOrder" wire:confirm="Apakah Anda yakin ingin menghapus pesanan ini secara permanen?" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors shadow-2xs ml-auto sm:ml-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus
            </button>
        </div>
    </div>

    <!-- Flash Alert -->
    @if(session()->has('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-semibold">&times;</button>
    </div>
    @endif

    <!-- Production Pipeline & Order Tracking Stepper Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wide mb-1">
                    Control Center Produksi
                </span>
                <h2 class="text-lg font-bold text-slate-900">Alur Pengerjaan Cucian & Pelacakan</h2>
                <p class="text-xs text-slate-500">Klik tombol status untuk memajukan tahapan cucian secara berurutan</p>
            </div>

            <!-- Quick Status Change Controls -->
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $stages = ['Diterima', 'Dicuci', 'Dikeringkan', 'Disetrika', 'Selesai', 'Diambil'];
                    $currentIndex = array_search($order->status, $stages);
                    if ($currentIndex === false) $currentIndex = 0;
                    $hasNext = $currentIndex < count($stages) - 1;
                    $nextStatus = $hasNext ? $stages[$currentIndex + 1] : null;
                @endphp

                @if($hasNext)
                <button wire:click="advanceStatus" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:opacity-60 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm hover:shadow transition-all">
                    <svg wire:loading wire:target="advanceStatus" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="advanceStatus">Majukan ke {{ $nextStatus }}</span>
                    <span wire:loading wire:target="advanceStatus">Mengubah Status...</span>
                    <svg wire:loading.remove wire:target="advanceStatus" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-teal-50 text-teal-700 text-xs font-bold border border-teal-200">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Pesanan Telah Tuntas Diambil
                </span>
                @endif

                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <span class="text-[11px] font-semibold text-slate-500 pl-2">Pilih:</span>
                    <select wire:change="updateStatus($event.target.value)" wire:loading.attr="disabled" class="text-xs font-semibold rounded-lg border-0 py-1 px-2.5 bg-white text-slate-800 focus:ring-2 focus:ring-blue-500 shadow-2xs cursor-pointer disabled:opacity-50">
                        @foreach($stages as $stage)
                            <option value="{{ $stage }}" {{ $order->status === $stage ? 'selected' : '' }}>{{ $stage }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 6-Stage Visual Stepper Timeline -->
        <div class="p-6 sm:p-8">
            @php
                $stepDefinitions = [
                    'Diterima' => [
                        'label' => 'Diterima',
                        'sub' => 'Penimbangan & input nota',
                        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
                    ],
                    'Dicuci' => [
                        'label' => 'Dicuci',
                        'sub' => 'Pencucian higienis mesin',
                        'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'
                    ],
                    'Dikeringkan' => [
                        'label' => 'Dikeringkan',
                        'sub' => 'Pengeringan mesin dryer',
                        'icon' => 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'
                    ],
                    'Disetrika' => [
                        'label' => 'Disetrika',
                        'sub' => 'Setrika uap & packing rapi',
                        'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'
                    ],
                    'Selesai' => [
                        'label' => 'Selesai',
                        'sub' => 'Siap diambil pelanggan',
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                    ],
                    'Diambil' => [
                        'label' => 'Diambil',
                        'sub' => 'Serah terima tuntas',
                        'icon' => 'M5 13l4 4L19 7'
                    ],
                ];
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 relative">
                @foreach($stepDefinitions as $stepKey => $step)
                    @php
                        $stepIdx = array_search($stepKey, $stages);
                        $isCurrent = $stepIdx === $currentIndex;
                        $isCompleted = $stepIdx < $currentIndex;
                    @endphp

                    <div wire:click="updateStatus('{{ $stepKey }}')" class="cursor-pointer group flex flex-col items-center text-center p-3 rounded-xl transition-all {{ $isCurrent ? 'bg-blue-50/70 ring-2 ring-blue-500 shadow-xs' : ($isCompleted ? 'bg-slate-50/60 hover:bg-slate-100/60' : 'hover:bg-slate-50') }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all {{ $isCurrent ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30 scale-110' : ($isCompleted ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200') }}">
                            @if($isCompleted)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"></path></svg>
                            @endif
                        </div>

                        <div class="mt-2.5">
                            <span class="text-xs font-bold block {{ $isCurrent ? 'text-blue-700' : ($isCompleted ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $step['label'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">
                                {{ $step['sub'] }}
                            </span>
                        </div>

                        @if($isCurrent)
                        <span class="mt-2 px-1.5 py-0.5 text-[9px] font-bold rounded-full bg-blue-100 text-blue-700">
                            Aktif
                        </span>
                        @elseif($isCompleted)
                        <span class="mt-2 text-[10px] font-semibold text-emerald-600">
                            ✓ Lewat
                        </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- WhatsApp & Share Tracking Link Bar -->
        @php
            $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer->phone ?? '');
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $tenantName = $order->tenant?->name ?? 'Laundry Kami';
            $trackUrl = url('/track/' . $order->order_number);
            $waMessage = "Halo kak {$order->customer->name}, cucian Anda dengan nomor nota {$order->order_number} di {$tenantName} saat ini berstatus *{$order->status}*. Total tagihan: Rp " . number_format($order->total, 0, ',', '.') . " (" . $order->payment_status . "). Lacak progres pakaian Anda di sini: {$trackUrl}. Terima kasih!";
            $waLink = "https://wa.me/{$cleanPhone}?text=" . urlencode($waMessage);
        @endphp

        <div class="p-4 sm:p-5 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.861.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800">Bagikan Link Pelacakan ke Pelanggan</p>
                    <p class="text-[11px] text-slate-500">Kirim update progres langsung ke WhatsApp pelanggan dengan 1 klik</p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative flex-1 md:w-72">
                    <input type="text" readonly value="{{ $trackUrl }}" class="w-full text-xs font-mono py-1.5 pl-3 pr-8 rounded-lg border border-slate-300 bg-white text-slate-600 focus:outline-none" onclick="this.select(); navigator.clipboard.writeText(this.value);">
                </div>
                @if($cleanPhone)
                <a href="{{ $waLink }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-2xs whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.861.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                    Kirim WA
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Grid (Left: Customer & Services | Right: Finance & Payment) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Customer info & Items -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Pelanggan & Petugas
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Nama Pelanggan</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $order->customer->name ?? 'Pelanggan Umum' }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Nomor WhatsApp / HP</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $order->customer->phone ?? '-' }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">Kasir / Operator</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $order->user->name ?? 'Kasir Outlet' }}</p>
                    </div>
                </div>

                @if($order->notes)
                <div class="mt-4 p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/80">
                    <p class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Catatan Khusus Cucian:
                    </p>
                    <p class="text-xs text-amber-900 mt-1">{{ $order->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Items Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Item & Layanan Cucian
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Layanan</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Kuantitas</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($order->items as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-3.5 text-sm font-semibold text-slate-900">
                                    {{ $item->service->name ?? 'Layanan Laundry' }}
                                    <span class="block text-[11px] font-normal text-slate-400 capitalize">{{ $item->service->type ?? 'kiloan' }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-sm text-slate-700 text-right font-medium">
                                    {{ $item->qty }} {{ $item->service->unit ?? 'kg' }}
                                </td>
                                <td class="px-6 py-3.5 text-sm text-slate-600 text-right">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 text-sm font-bold text-slate-900 text-right">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Financial Summary & Payment Action -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ringkasan Keuangan
                </h3>

                <div class="space-y-3 text-sm pb-4 border-b border-slate-100">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-medium text-slate-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($order->discount > 0)
                    <div class="flex justify-between text-rose-600">
                        <span>Diskon</span>
                        <span class="font-medium">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-base font-extrabold text-slate-900 pt-2 border-t border-slate-100">
                        <span>Total Tagihan</span>
                        <span class="text-blue-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                @php
                    $paidTotal = $order->payments->sum('amount');
                    $remainingDebt = max(0, $order->total - $paidTotal);
                @endphp

                <div class="py-4 border-b border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Jumlah Dibayar:</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($paidTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Sisa Tagihan:</span>
                        <span class="font-bold {{ $remainingDebt > 0 ? 'text-rose-600' : 'text-slate-700' }}">
                            Rp {{ number_format($remainingDebt, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Payment Action Button -->
                <div class="pt-4">
                    @if($order->payment_status !== 'Lunas')
                        <button wire:click="markAsPaid" wire:confirm="Tandai pesanan ini sebagai Lunas (Pembayaran Tunai)?" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Tandai Lunas Sekarang (Cash)
                        </button>
                    @else
                        <div class="py-2 px-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-center text-xs font-bold flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Lunas Sepenuhnya
                        </div>
                    @endif
                </div>

                <!-- Payment Logs / History -->
                @if($order->payments->count() > 0)
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Riwayat Pembayaran</p>
                    <div class="space-y-2">
                        @foreach($order->payments as $payment)
                        <div class="flex justify-between items-center text-xs p-2 rounded-lg bg-slate-50">
                            <div>
                                <span class="font-bold text-slate-800">{{ $payment->method ?? 'Cash' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="font-bold text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>