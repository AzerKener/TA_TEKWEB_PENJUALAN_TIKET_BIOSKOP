@extends('layouts.admin')
@section('title', 'Tambah Studio')

@section('content')
<div class="max-w-xl">
    <form action="{{ route('admin.studios.store') }}" method="POST" class="space-y-5">
        @csrf
        <div class="bg-white rounded-2xl p-6 shadow-card">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">Detail Studio</h3>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Nama Studio *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" placeholder="contoh: Studio 1 — IMAX" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Tipe Studio *</label>
                    <select name="type" class="form-input" required>
                        @foreach(['regular' => 'Regular', 'imax' => 'IMAX', '4dx' => '4DX', 'vip' => 'VIP', 'premiere' => 'Premiere'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jumlah Baris</label>
                    <input type="number" name="total_rows" value="{{ old('total_rows', 8) }}" class="form-input" min="1" max="30">
                    <p class="text-xs text-charcoal-muted mt-1">Baris akan diberi label A, B, C, ... secara otomatis saat seeder dijalankan.</p>
                </div>
                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-input" placeholder="Fasilitas khusus, dll.">{{ old('description') }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="w-4 h-4 text-primary" checked>
                    <label for="is_active" class="text-sm font-medium">Studio Aktif</label>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">💾 Simpan Studio</button>
            <a href="{{ route('admin.studios.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
