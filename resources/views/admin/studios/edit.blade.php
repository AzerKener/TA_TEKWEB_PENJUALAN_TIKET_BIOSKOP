@extends('layouts.admin')
@section('title', 'Edit Studio — ' . $studio->name)

@section('content')
<div class="max-w-xl">
    <form action="{{ route('admin.studios.update', $studio) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl p-6 shadow-card">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">
                Edit: {{ $studio->name }}
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="form-label">Nama Studio *</label>
                    <input type="text" name="name" value="{{ old('name', $studio->name) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Tipe Studio *</label>
                    <select name="type" class="form-input" required>
                        @foreach(['regular' => 'Regular', 'imax' => 'IMAX', '4dx' => '4DX', 'vip' => 'VIP', 'premiere' => 'Premiere'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $studio->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-input">{{ old('description', $studio->description) }}</textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="w-4 h-4 text-primary"
                           {{ old('is_active', $studio->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium">Studio Aktif</label>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">💾 Update Studio</button>
            <a href="{{ route('admin.studios.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
