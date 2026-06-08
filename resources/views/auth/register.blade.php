@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<div class="animate-slide-up">
    <h1 class="font-display text-3xl font-bold text-charcoal mb-2">Buat Akun Baru</h1>
    <p class="text-charcoal-muted mb-8">Bergabunglah dan nikmati kemudahan booking tiket.</p>

    <form method="POST" action="{{ route('register.post') }}" x-data="{ loading: false, password: '', strength: 0 }" @submit="loading = true" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="form-input @error('name') border-red-400 @enderror"
                   placeholder="Nama lengkap Anda" required autofocus>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="form-input @error('email') border-red-400 @enderror"
                   placeholder="contoh@email.com" required>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="form-label">Nomor HP <span class="text-charcoal-muted font-normal">(opsional)</span></label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                   class="form-input" placeholder="08xxxxxxxxxx">
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" name="password" id="password"
                       class="form-input pr-10 @error('password') border-red-400 @enderror"
                       placeholder="Minimal 8 karakter" required
                       @input="password = $event.target.value; strength = password.length < 8 ? 1 : password.match(/[A-Z]/) && password.match(/[0-9]/) ? 3 : 2">
                <button type="button" @click="show = !show"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-charcoal-muted hover:text-charcoal">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>

            <!-- Password Strength Indicator -->
            <div class="mt-2" x-show="password.length > 0">
                <div class="flex gap-1">
                    <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 1 ? 'bg-red-400' : 'bg-gray-200'"></div>
                    <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 2 ? 'bg-yellow-400' : 'bg-gray-200'"></div>
                    <div class="h-1 flex-1 rounded-full transition-all" :class="strength >= 3 ? 'bg-green-400' : 'bg-gray-200'"></div>
                </div>
                <p class="text-xs mt-1 transition-colors"
                   :class="strength === 1 ? 'text-red-500' : strength === 2 ? 'text-yellow-600' : 'text-green-600'">
                    <span x-text="strength === 1 ? 'Lemah' : strength === 2 ? 'Sedang' : 'Kuat'"></span>
                </p>
            </div>

            @error('password') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-input" placeholder="Ulangi password" required>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-primary w-full py-3 text-base" :disabled="loading">
            <span x-show="!loading">Buat Akun</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Memproses...
            </span>
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-charcoal-muted">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
