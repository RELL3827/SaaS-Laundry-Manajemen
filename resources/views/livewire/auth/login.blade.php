<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

new #[Layout('components.layouts.guest')] class extends Component
{
    public $email = '';
    public $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            $this->redirectIntended(default: '/dashboard', navigate: true);
            return;
        }

        $this->addError('email', 'Email atau kata sandi tidak cocok dengan data kami.');
    }
};
?>

<div class="min-h-[85vh] flex flex-col items-center justify-center p-4">
    <!-- Back to Home Link -->
    <div class="max-w-md w-full mb-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Beranda Utama</span>
        </a>
    </div>

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-200/80 p-8 sm:p-10">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">Laundry<span class="text-blue-600">Pro</span></span>
            </a>
            <h1 class="text-xl font-bold text-slate-900">Masuk ke Portal Kasir & Owner</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola operasional laundry Anda dengan mudah</p>
        </div>

        <form wire:submit="login" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Akun</label>
                <input wire:model="email" type="email" placeholder="nama@laundrypro.id" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-3 bg-white" required>
                @error('email') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi</label>
                <input wire:model="password" type="password" placeholder="••••••••" class="w-full rounded-xl border border-slate-300 shadow-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm p-3 bg-white" required>
                @error('password') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full py-3 px-4 rounded-xl shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 disabled:opacity-60 transition-all flex items-center justify-center gap-2">
                <svg wire:loading wire:target="login" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
                <span wire:loading wire:target="login">Memverifikasi Akun...</span>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Belum memiliki akun bisnis?</span>
            <a href="/register" wire:navigate.hover class="text-blue-600 font-bold hover:underline ml-1">Daftar Gratis 14 Hari</a>
        </div>
    </div>
</div>