@extends('layouts.admin')
@section('title', 'Tambah Film Baru')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-2xl p-6 shadow-card mb-5">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">
                Informasi Dasar
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label">Judul Film *</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-input @error('title') border-red-400 @enderror"
                           placeholder="Masukkan judul film" required>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Sutradara *</label>
                    <input type="text" name="director" value="{{ old('director') }}"
                           class="form-input" placeholder="Nama sutradara" required>
                    @error('director') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Durasi (menit) *</label>
                    <input type="number" name="duration" value="{{ old('duration', 120) }}"
                           class="form-input" min="1" max="600" required>
                </div>

                <div>
                    <label class="form-label">Rating *</label>
                    <select name="rating" class="form-input" required>
                        @foreach(['SU' => 'SU — Semua Umur', 'G' => 'G — General', 'PG' => 'PG — Parental Guidance', 'PG-13' => 'PG-13 — 13+', 'R' => 'R — Restricted', 'D17' => 'D17 — Dewasa 17+'] as $val => $label)
                            <option value="{{ $val }}" {{ old('rating') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Bahasa *</label>
                    <select name="language" class="form-input" required>
                        @foreach(['Indonesia', 'Inggris', 'Korea', 'Jepang', 'Mandarin'] as $lang)
                            <option value="{{ $lang }}" {{ old('language') === $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-input" required>
                        <option value="now_playing">Sedang Tayang</option>
                        <option value="coming_soon">Segera Tayang</option>
                        <option value="ended">Selesai</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Tanggal Rilis *</label>
                    <input type="date" name="release_date" value="{{ old('release_date') }}"
                           class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Tanggal Berakhir</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Rating IMDb</label>
                    <input type="number" name="imdb_rating" step="0.1" min="0" max="10"
                           value="{{ old('imdb_rating') }}" class="form-input" placeholder="0.0 – 10.0">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Genre <span class="text-charcoal-muted font-normal">(pisahkan koma)</span></label>
                    <input type="text" name="genre_input" value="{{ old('genre_input') }}"
                           class="form-input" placeholder="Aksi, Drama, Sci-Fi">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Sinopsis *</label>
                    <textarea name="synopsis" rows="4" class="form-input"
                              placeholder="Deskripsi singkat film..." required>{{ old('synopsis') }}</textarea>
                    @error('synopsis') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">Pemeran</label>
                    <textarea name="cast" rows="2" class="form-input"
                              placeholder="Nama pemeran, dipisah koma">{{ old('cast') }}</textarea>
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
                    <div class="border-2 border-dashed border-primary/20 rounded-xl p-4 text-center hover:border-primary/40 transition-colors">
                        <input type="file" name="poster_image" accept="image/*" class="w-full text-sm" id="poster-input"
                               onchange="previewPoster(this)">
                        <p class="text-xs text-charcoal-muted mt-2">Format: JPG, PNG, WebP. Maks 2MB.</p>
                        <img id="poster-preview" class="mt-3 max-h-32 mx-auto rounded-xl hidden">
                    </div>
                    @error('poster_image') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">URL Trailer (YouTube)</label>
                    <input type="url" name="trailer_url" value="{{ old('trailer_url') }}"
                           class="form-input" placeholder="https://youtube.com/watch?v=...">
                </div>

                <div>
                    <label class="form-label">Perusahaan Produksi</label>
                    <input type="text" name="production_company" value="{{ old('production_company') }}"
                           class="form-input">
                </div>

                <div>
                    <label class="form-label">Distributor</label>
                    <input type="text" name="distributor" value="{{ old('distributor') }}" class="form-input">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="has_subtitle" value="1" id="has_subtitle"
                           class="w-4 h-4 text-primary border-primary/30 rounded focus:ring-primary/30"
                           {{ old('has_subtitle', '1') ? 'checked' : '' }}>
                    <label for="has_subtitle" class="text-sm font-medium text-charcoal">Tersedia Subtitle</label>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">
                💾 Simpan Film
            </button>
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
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
