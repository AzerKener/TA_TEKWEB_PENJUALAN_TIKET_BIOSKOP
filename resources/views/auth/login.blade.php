@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="animate-slide-up">
    <h1 class="font-display text-3xl font-bold text-charcoal mb-2">Selamat Datang!</h1>
    <p class="text-charcoal-muted mb-8">Masuk untuk memesan tiket favoritmu.</p>

    <form method="POST" action="{{ route('login.post') }}" x-data="{ loading: false }" @submit="loading = true" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="form-input @error('email') border-red-400 ring-2 ring-red-200 @enderror"
                   placeholder="contoh@email.com" required autofocus>
            @error('email')
                <p class="form-error"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" name="password" id="password"
                       class="form-input pr-10 @error('password') border-red-400 ring-2 ring-red-200 @enderror"
                       placeholder="Masukkan password" required>
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-charcoal-muted hover:text-charcoal transition-colors">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-primary border-primary/30 rounded focus:ring-primary/30">
            <label for="remember" class="ml-2 text-sm text-charcoal-muted">Ingat saya</label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-primary w-full py-3 text-base" :disabled="loading">
            <span x-show="!loading">Masuk</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Memproses...
            </span>
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-charcoal-muted">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <!-- Demo credentials -->
    <div class="mt-8 p-4 bg-primary/5 rounded-2xl border border-primary/10">
        <p class="text-xs font-semibold text-primary mb-2">Demo Credentials:</p>
        <div class="space-y-1 text-xs text-charcoal-muted font-mono">
            <p><span class="font-semibold text-charcoal">Admin:</span> admin@cinexpress.id / admin123</p>
            <p><span class="font-semibold text-charcoal">Customer:</span> (lihat seeder) / password</p>
        </div>
    </div>
</div>
@endsection
