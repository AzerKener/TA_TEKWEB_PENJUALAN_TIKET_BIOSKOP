@extends('layouts.admin')
@section('title', 'Kelola F&B')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-gray-500">{{ $fnbItems->total() }} item tersedia</p>
    <a href="{{ route('admin.fnb.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Item F&B
    </a>
</div>

<div class="bg-white rounded-2xl shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fnbItems as $item)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-cream overflow-hidden flex-shrink-0">
                                    @if($item->image)
                                        <img src="{{ $item->image_url }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xl">{{ $item->category === 'drink' ? '🥤' : '🍟' }}</div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-charcoal">{{ $item->name }}</p>
                                    @if($item->description)
                                        <p class="text-xs text-charcoal-muted line-clamp-1">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-blue text-xs">{{ $item->category_label }}</span></td>
                        <td class="font-bold text-charcoal">{{ $item->price_formatted }}</td>
                        <td>
                            <span class="{{ $item->stock < 10 ? 'text-red-600 font-bold' : 'text-charcoal' }}">
                                {{ $item->stock }}
                            </span>
                        </td>
                        <td>
                            @if($item->is_available && $item->stock > 0)
                                <span class="badge badge-green">Tersedia</span>
                            @else
                                <span class="badge badge-gray">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <a href="{{ route('admin.fnb.edit', $item) }}" class="btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('admin.fnb.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Hapus item ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm bg-red-50 text-red-600 hover:bg-red-600 hover:text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            <div class="text-4xl mb-2">🍿</div>
                            <p>Belum ada item F&B. <a href="{{ route('admin.fnb.create') }}" class="text-primary font-semibold">Tambah sekarang</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $fnbItems->links() }}</div>
</div>
@endsection
