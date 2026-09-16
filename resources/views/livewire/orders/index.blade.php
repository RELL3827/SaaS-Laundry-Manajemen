<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    public function with()
    {
        $query = Order::with(['customer', 'user'])
                      ->where(function($q) {
                          $q->where('order_number', 'like', '%' . $this->search . '%')
                            ->orWhereHas('customer', function($c) {
                                $c->where('name', 'like', '%' . $this->search . '%');
                            });
                      });

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10)
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $newStatus]);
        session()->flash('success', "Status pesanan {$order->order_number} diperbarui menjadi: {$newStatus}");
    }

    public function deleteOrder($orderId)
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);
            $orderNumber = $order->order_number;
            $order->items()->delete();
            $order->payments()->delete();
            $order->delete();
            session()->flash('success', "Pesanan {$orderNumber} berhasil dihapus!");
        });
    }
};
?>

<div>
    @php
        $tenant = auth()->user()->tenant;
        $isPro = $tenant?->isPro() ?? false;
    @endphp

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Order & Produksi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola penerimaan cucian, perbarui status proses, dan cetak nota</p>
        </div>
        <div class="flex items-center gap-3">
            @if($isPro)
                <a href="{{ route('orders.export') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm shadow-xs transition-all">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Ekspor CSV</span>
                </a>
            @else
                <button @click="showUpgradeModal = true" type="button" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-dashed border-amber-300 bg-amber-50/70 hover:bg-amber-100 text-amber-800 font-semibold text-sm transition-all" title="Fitur ini khusus Paket Pro">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Ekspor CSV</span>
                    <span class="text-[10px] bg-amber-200 text-amber-900 font-black px-1.5 py-0.2 rounded">PRO</span>
                </button>
            @endif

            <a href="/orders/create" class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2.5 rounded-xl shadow-sm hover:shadow font-medium text-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Order / Terima Cucian
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if(session()->has('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-semibold">&times;</button>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:w-80">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nomor nota atau nama customer..." class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm bg-white">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Filter Status:</span>
                <select wire:model.live="status" class="w-full sm:w-48 rounded-lg border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs p-2 bg-white font-medium">
                    <option value="">Semua Status Cucian</option>
                    <option value="Diterima">Diterima</option>
                    <option value="Dicuci">Dicuci</option>
                    <option value="Dikeringkan">Dikeringkan</option>
                    <option value="Disetrika">Disetrika</option>
                    <option value="Selesai">Selesai (Siap Diambil)</option>
                    <option value="Diambil">Diambil (Tuntas)</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">No. Order & Tanggal</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status & Ubah Cepat</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/75 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-blue-600 font-mono">{{ $order->order_number }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-slate-900">{{ $order->customer->name ?? 'Pelanggan Umum' }}</div>
                            <div class="text-xs text-slate-500">{{ $order->customer->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusBadges = [
                                    'Diterima' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Dicuci' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Dikeringkan' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Disetrika' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Diambil' => 'bg-teal-50 text-teal-700 border-teal-200',
                                ];
                                $badgeClass = $statusBadges[$order->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <div class="flex items-center gap-2">
                                <select wire:change="updateStatus({{ $order->id }}, $event.target.value)" class="text-xs font-semibold rounded-lg border border-slate-200 py-1 px-2 bg-white hover:border-blue-400 focus:ring-1 focus:ring-blue-500 cursor-pointer shadow-2xs">
                                    <option value="Diterima" {{ $order->status === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                    <option value="Dicuci" {{ $order->status === 'Dicuci' ? 'selected' : '' }}>Dicuci</option>
                                    <option value="Dikeringkan" {{ $order->status === 'Dikeringkan' ? 'selected' : '' }}>Dikeringkan</option>
                                    <option value="Disetrika" {{ $order->status === 'Disetrika' ? 'selected' : '' }}>Disetrika</option>
                                    <option value="Selesai" {{ $order->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Diambil" {{ $order->status === 'Diambil' ? 'selected' : '' }}>Diambil</option>
                                </select>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->payment_status == 'Lunas')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60">Lunas</span>
                            @elseif($order->payment_status == 'DP')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60">DP</span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200/60">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="/track/{{ $order->order_number }}" target="_blank" title="Lacak Tampilan Pelanggan" class="inline-flex items-center text-xs font-semibold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200 transition-colors">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Lacak
                            </a>
                            <a href="/orders/{{ $order->id }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded-md border border-blue-200 transition-colors">
                                Detail
                            </a>
                            <button wire:click="deleteOrder({{ $order->id }})" wire:confirm="Apakah Anda yakin ingin menghapus order ini secara permanen?" class="inline-flex items-center text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 px-2 py-1 rounded-md border border-rose-200 transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="font-medium text-slate-600">Belum ada pesanan yang masuk</p>
                            <p class="text-xs text-slate-400 mt-1">Klik tombol "Buat Order / Terima Cucian" untuk input nota baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
