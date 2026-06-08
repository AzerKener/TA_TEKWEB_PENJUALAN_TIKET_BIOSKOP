@extends('layouts.admin')
@section('title', 'Tambah Item F&B')

@section('content')
<div class="max-w-xl">
    <form action="{{ route('admin.fnb.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div class="bg-white rounded-2xl p-6 shadow-card">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">Detail Item</h3>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Nama Item *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Kategori *</label>
                    <select name="category" class="form-input" required>
                        @foreach(['food' => '🍔 Makanan', 'drink' => '🥤 Minuman', 'snack' => '🍟 Snack', 'combo' => '🎁 Paket Combo'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="form-input" min="0" step="500" required>
                </div>
                <div>
                    <label class="form-label">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', 100) }}" class="form-input" min="0" required>
                </div>
                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="2" class="form-input">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Gambar</label>
                    <input type="file" name="image" accept="image/*" class="form-input py-2">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_available" value="1" id="is_available" class="w-4 h-4 text-primary" checked>
                    <label for="is_available" class="text-sm font-medium">Tersedia untuk Dijual</label>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">💾 Simpan Item</button>
            <a href="{{ route('admin.fnb.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
