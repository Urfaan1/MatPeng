@extends('layouts.guest')

@section('title', 'Masuk ke Akun Anda')
@section('heading', 'Masuk ke MatPeng')
@section('subheading', 'Akses catatan keuangan dan pantau arus kas Anda.')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPass: false }">
    @csrf

    <!-- Email -->
    <div>
        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
            Alamat Email
        </label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="mail" class="w-4 h-4"></i>
            </span>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   placeholder="nama@email.com"
                   class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border @error('email') border-red-400 bg-red-50/20 @else border-slate-200 @enderror focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34] transition-all">
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Kata Sandi -->
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                Kata Sandi
            </label>
        </div>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i data-lucide="lock" class="w-4 h-4"></i>
            </span>
            <input id="password" 
                   :type="showPass ? 'text' : 'password'" 
                   name="password" 
                   required 
                   placeholder="••••••••"
                   class="w-full pl-10 pr-10 py-3 text-sm rounded-xl border @error('password') border-red-400 bg-red-50/20 @else border-slate-200 @enderror focus:outline-none focus:border-[#123E34] focus:ring-1 focus:ring-[#123E34] transition-all">
            <button type="button" 
                    @click="showPass = !showPass" 
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                <i data-lucide="eye" x-show="!showPass" class="w-4 h-4"></i>
                <i data-lucide="eye-off" x-show="showPass" class="w-4 h-4" style="display: none;"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <!-- Ingat Saya -->
    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#123E34] focus:ring-[#123E34] border-slate-300">
            <span class="text-xs text-slate-600 select-none">Ingat saya di perangkat ini</span>
        </label>
    </div>

    <!-- Tombol Submit -->
    <button type="submit" 
            class="w-full py-3 px-4 rounded-xl bg-[#123E34] hover:bg-[#1B594B] text-white font-bold text-sm transition-all shadow-md active:scale-[0.99] flex items-center justify-center gap-2">
        <i data-lucide="log-in" class="w-4 h-4"></i>
        Masuk ke Akun
    </button>

    <!-- Navigasi ke Registrasi -->
    <div class="pt-3 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-500">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-bold text-[#123E34] hover:text-[#1B594B] underline ml-1">
                Daftar sekarang
            </a>
        </p>
    </div>
</form>
@endsection
