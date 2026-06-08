@extends('layouts.admin')
@section('title', 'Edit Jadwal')

@section('content')
<div class="max-w-2xl" x-data="{ conflict: false, conflictMsg: '' }">

    <div x-show="conflict" x-transition
         class="mb-5 p-4 bg-red-50 rounded-2xl border border-red-200 flex items-start gap-3">
        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-red-700">⚠️ Konflik Jadwal!</p>
            <p class="text-red-600 text-sm" x-text="conflictMsg"></p>
        </div>
    </div>

    <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl p-6 shadow-card">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">
                Edit Jadwal
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="form-label">Film *</label>
                    <select name="movie_id" class="form-input" required>
                        @foreach($movies as $movie)
                            <option value="{{ $movie->id }}" {{ old('movie_id', $schedule->movie_id) == $movie->id ? 'selected' : '' }}>
                                {{ $movie->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Studio *</label>
                    <select name="studio_id" id="studio_id" class="form-input" required @change="checkConflict({{ $schedule->id }})">
                        @foreach($studios as $studio)
                            <option value="{{ $studio->id }}" {{ old('studio_id', $schedule->studio_id) == $studio->id ? 'selected' : '' }}>
                                {{ $studio->name }} ({{ $studio->type_label }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tanggal Tayang *</label>
                    <input type="date" name="show_date" id="show_date"
                           value="{{ old('show_date', $schedule->show_date->format('Y-m-d')) }}"
                           class="form-input" required @change="checkConflict({{ $schedule->id }})">
                </div>
                <div>
                    <label class="form-label">Tipe Bahasa *</label>
                    <select name="language_type" class="form-input" required>
                        @foreach(['subtitled' => 'Subtitle', 'dubbed' => 'Sulih Suara', 'original' => 'Original'] as $val => $label)
                            <option value="{{ $val }}" {{ old('language_type', $schedule->language_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jam Mulai *</label>
                    <input type="time" name="start_time" id="start_time"
                           value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}"
                           class="form-input" required @change="checkConflict({{ $schedule->id }})">
                </div>
                <div>
                    <label class="form-label">Jam Selesai *</label>
                    <input type="time" name="end_time" id="end_time"
                           value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}"
                           class="form-input" required @change="checkConflict({{ $schedule->id }})">
                </div>

                <div class="flex items-center gap-2 md:col-span-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           class="w-4 h-4 text-primary"
                           {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium">Jadwal Aktif</label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-card">
            <h3 class="font-display font-bold text-lg text-charcoal mb-5 pb-3 border-b border-gray-100">Harga Tiket</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label">Harga Regular (Rp)</label>
                    <input type="number" name="price_regular" value="{{ old('price_regular', $schedule->price_regular) }}" class="form-input" min="0" step="1000" required>
                </div>
                <div>
                    <label class="form-label">Harga VIP (Rp)</label>
                    <input type="number" name="price_vip" value="{{ old('price_vip', $schedule->price_vip) }}" class="form-input" min="0" step="1000" required>
                </div>
                <div>
                    <label class="form-label">Harga Couple (Rp)</label>
                    <input type="number" name="price_couple" value="{{ old('price_couple', $schedule->price_couple) }}" class="form-input" min="0" step="1000" required>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">💾 Update Jadwal</button>
            <a href="{{ route('admin.schedules.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
async function checkConflict(excludeId = 0) {
    const studioId  = document.getElementById('studio_id').value;
    const showDate  = document.getElementById('show_date').value;
    const startTime = document.getElementById('start_time').value;
    const endTime   = document.getElementById('end_time').value;
    if (!studioId || !showDate || !startTime || !endTime) return;
    try {
        const res  = await fetch(`{{ route('admin.schedules.conflict') }}?studio_id=${studioId}&show_date=${showDate}&start_time=${startTime}&end_time=${endTime}&exclude_id=${excludeId}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        const comp = document.querySelector('[x-data]').__x.$data;
        comp.conflict = data.conflict; comp.conflictMsg = data.message || '';
    } catch (e) {}
}
</script>
@endpush
