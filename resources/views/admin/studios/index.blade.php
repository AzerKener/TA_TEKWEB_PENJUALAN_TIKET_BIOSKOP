@extends('layouts.admin')
@section('title', 'Kelola Studio')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-gray-500">{{ $studios->count() }} studio terdaftar</p>
    <a href="{{ route('admin.studios.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Studio
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($studios as $studio)
        <div class="bg-white rounded-2xl p-5 shadow-card border border-transparent hover:border-primary/20 transition-all">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-display font-bold text-xl text-charcoal">{{ $studio->name }}</h3>
                    <span class="badge badge-primary mt-1">{{ $studio->type_label }}</span>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-2xl">🏛️</div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                <div class="bg-cream rounded-lg p-2.5 text-center">
                    <p class="text-xs text-charcoal-muted">Total Kursi</p>
                    <p class="font-bold text-charcoal text-lg">{{ $studio->capacity }}</p>
                </div>
                <div class="bg-cream rounded-lg p-2.5 text-center">
                    <p class="text-xs text-charcoal-muted">Status</p>
                    <p class="font-bold {{ $studio->is_active ? 'text-green-600' : 'text-red-500' }}">
                        {{ $studio->is_active ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </div>
            </div>

            @if($studio->description)
                <p class="text-sm text-charcoal-muted mb-4 line-clamp-2">{{ $studio->description }}</p>
            @endif

            <div class="flex gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.studios.seats', $studio) }}" class="btn btn-sm bg-blue-50 text-blue-600 hover:bg-blue-100 flex-1 justify-center">
                    💺 Lihat Kursi
                </a>
                <a href="{{ route('admin.studios.edit', $studio) }}" class="btn-secondary btn-sm">Edit</a>
                <form action="{{ route('admin.studios.destroy', $studio) }}" method="POST"
                      onsubmit="return confirm('Hapus studio ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm bg-red-50 text-red-600 hover:bg-red-600 hover:text-white">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-3 text-center py-16">
            <div class="text-5xl mb-3">🏛️</div>
            <p class="text-charcoal-muted">Belum ada studio. <a href="{{ route('admin.studios.create') }}" class="text-primary font-semibold">Tambah sekarang</a></p>
        </div>
    @endforelse
</div>
@endsection
