@extends('layouts.admin')
@section('title', 'Edit Film — ' . $movie->title)

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.movies.update', $movie) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-6 shadow-card mb-5">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">
                Edit: {{ $movie->title }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label">Judul Film *</label>
                    <input type="text" name="title" value="{{ old('title', $movie->title) }}"
                           class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Sutradara *</label>
                    <input type="text" name="director" value="{{ old('director', $movie->director) }}" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Durasi (menit) *</label>
                    <input type="number" name="duration" value="{{ old('duration', $movie->duration) }}" class="form-input" min="1" required>
                </div>

                <div>
                    <label class="form-label">Rating *</label>
                    <select name="rating" class="form-input" required>
                        @foreach(['SU', 'G', 'PG', 'PG-13', 'R', 'D17'] as $r)
                            <option value="{{ $r }}" {{ old('rating', $movie->rating) === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Bahasa *</label>
                    <select name="language" class="form-input" required>
                        @foreach(['Indonesia', 'Inggris', 'Korea', 'Jepang', 'Mandarin'] as $lang)
                            <option value="{{ $lang }}" {{ old('language', $movie->language) === $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-input" required>
                        @foreach(['now_playing' => 'Sedang Tayang', 'coming_soon' => 'Segera Tayang', 'ended' => 'Selesai'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $movie->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Tanggal Rilis *</label>
                    <input type="date" name="release_date" value="{{ old('release_date', $movie->release_date->format('Y-m-d')) }}" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $movie->end_date?->format('Y-m-d')) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Rating IMDb</label>
                    <input type="number" name="imdb_rating" step="0.1" min="0" max="10"
                           value="{{ old('imdb_rating', $movie->imdb_rating) }}" class="form-input">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Genre <span class="text-charcoal-muted font-normal">(pisahkan koma)</span></label>
                    <input type="text" name="genre_input" class="form-input"
                           value="{{ old('genre_input', implode(', ', $movie->genre ?? [])) }}"
                           placeholder="Aksi, Drama, Sci-Fi">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Sinopsis *</label>
                    <textarea name="synopsis" rows="4" class="form-input" required>{{ old('synopsis', $movie->synopsis) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Pemeran</label>
                    <textarea name="cast" rows="2" class="form-input">{{ old('cast', $movie->cast) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-card mb-5">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">
                Media & Distribusi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label">Poster Film</label>
                    @if($movie->poster_image)
                        <div class="flex items-start gap-4 mb-3">
                            <img src="{{ $movie->poster_url }}" class="w-20 h-28 object-cover rounded-xl" alt="">
                            <div class="text-sm text-charcoal-muted">Poster saat ini.<br>Upload baru untuk menggantinya.</div>
                        </div>
                    @endif
                    <input type="file" name="poster_image" accept="image/*" class="form-input py-2"
                           onchange="previewPoster(this)">
                    <img id="poster-preview" class="mt-3 max-h-32 rounded-xl hidden">
                    @error('poster_image') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">URL Trailer (YouTube)</label>
                    <input type="url" name="trailer_url" value="{{ old('trailer_url', $movie->trailer_url) }}"
                           class="form-input" placeholder="https://youtube.com/watch?v=...">
                </div>

                <div>
                    <label class="form-label">Perusahaan Produksi</label>
                    <input type="text" name="production_company" value="{{ old('production_company', $movie->production_company) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Distributor</label>
                    <input type="text" name="distributor" value="{{ old('distributor', $movie->distributor) }}" class="form-input">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="has_subtitle" value="1" id="has_subtitle"
                           class="w-4 h-4 text-primary"
                           {{ old('has_subtitle', $movie->has_subtitle) ? 'checked' : '' }}>
                    <label for="has_subtitle" class="text-sm font-medium">Tersedia Subtitle</label>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">💾 Update Film</button>
            <a href="{{ route('admin.movies.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewPoster(input) {
    const preview = document.getElementById('poster-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => { preview.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
