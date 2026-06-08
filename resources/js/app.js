import './bootstrap';
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import Chart from 'chart.js/auto';

// ── Alpine.js Setup ──
Alpine.plugin(persist);
window.Alpine = Alpine;
window.Chart = Chart;

// ── Global Alpine Data ──
document.addEventListener('alpine:init', () => {

    // Toast Notification System
    Alpine.data('toastSystem', () => ({
        toasts: [],
        show(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 5000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }));

    // Seat Map Component
    Alpine.data('seatMap', (scheduleId, lockUrl, unlockUrl, statusUrl, csrfToken) => ({
        seats: [],
        selectedSeats: [],
        schedule: {},
        loading: true,
        lockTimer: null,
        countdown: 600,

        async init() {
            await this.loadStatus();
            this.startCountdown();
        },

        async loadStatus() {
            this.loading = true;
            try {
                const res = await fetch(statusUrl);
                const data = await res.json();
                this.seats = data.seats;
                this.schedule = data.schedule;

                // Restore previously locked seats (mine)
                this.selectedSeats = this.seats
                    .filter(s => s.status === 'mine')
                    .map(s => s.id);
            } catch (e) {
                console.error('Error loading seats:', e);
            } finally {
                this.loading = false;
            }
        },

        async toggleSeat(seat) {
            if (seat.status === 'booked' || seat.status === 'locked') return;

            if (this.selectedSeats.includes(seat.id)) {
                await this.unlockSeat(seat.id);
                this.selectedSeats = this.selectedSeats.filter(id => id !== seat.id);
                this.updateSeatStatus(seat.id, 'available');
            } else {
                if (this.selectedSeats.length >= 8) {
                    alert('Maksimal 8 kursi per pemesanan.');
                    return;
                }
                const success = await this.lockSeat(seat.id);
                if (success) {
                    this.selectedSeats.push(seat.id);
                    this.updateSeatStatus(seat.id, 'mine');
                }
            }
        },

        async lockSeat(seatId) {
            try {
                const res = await fetch(lockUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ schedule_id: scheduleId, seat_id: seatId })
                });
                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'Gagal mengunci kursi.');
                    await this.loadStatus();
                    return false;
                }
                return true;
            } catch (e) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                return false;
            }
        },

        async unlockSeat(seatId) {
            try {
                await fetch(unlockUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ schedule_id: scheduleId, seat_id: seatId })
                });
            } catch (e) {
                console.error('Unlock failed:', e);
            }
        },

        updateSeatStatus(seatId, status) {
            const idx = this.seats.findIndex(s => s.id === seatId);
            if (idx !== -1) this.seats[idx].status = status;
        },

        startCountdown() {
            this.lockTimer = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    clearInterval(this.lockTimer);
                    alert('Waktu pemilihan kursi habis. Halaman akan dimuat ulang.');
                    window.location.reload();
                }
            }, 1000);
        },

        get countdownFormatted() {
            const m = Math.floor(this.countdown / 60).toString().padStart(2, '0');
            const s = (this.countdown % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        get totalPrice() {
            return this.seats
                .filter(s => this.selectedSeats.includes(s.id))
                .reduce((total, s) => {
                    const price = s.type === 'vip' ? this.schedule.price_vip :
                                  s.type === 'couple' ? this.schedule.price_couple :
                                  this.schedule.price_regular;
                    return total + (price || 0);
                }, 0);
        },

        get totalPriceFormatted() {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(this.totalPrice);
        },

        getSeatRows() {
            const grouped = {};
            this.seats.forEach(seat => {
                if (!grouped[seat.row]) grouped[seat.row] = [];
                grouped[seat.row].push(seat);
            });
            Object.keys(grouped).forEach(row => {
                grouped[row].sort((a, b) => a.number - b.number);
            });
            return grouped;
        },

        getSeatClass(seat) {
            if (seat.status === 'booked') return 'seat-booked';
            if (seat.status === 'locked') return 'seat-locked';
            if (seat.status === 'mine' || this.selectedSeats.includes(seat.id)) return 'seat-selected';
            if (seat.type === 'vip') return 'seat-vip';
            if (seat.type === 'couple') return 'seat-couple';
            return 'seat-available';
        },

        destroy() {
            if (this.lockTimer) clearInterval(this.lockTimer);
        }
    }));

    // FNB Cart Component
    Alpine.data('fnbCart', (scheduleId) => ({
        cart: Alpine.$persist({}).as(`fnb_cart_${scheduleId}`),

        addItem(id, price) {
            if (!this.cart[id]) this.cart[id] = { qty: 0, price };
            this.cart[id].qty++;
        },

        removeItem(id) {
            if (this.cart[id] && this.cart[id].qty > 0) {
                this.cart[id].qty--;
                if (this.cart[id].qty === 0) delete this.cart[id];
            }
        },

        getQty(id) { return this.cart[id]?.qty || 0; },

        get totalItems() { return Object.values(this.cart).reduce((t, v) => t + v.qty, 0); },

        get totalPrice() {
            return Object.values(this.cart).reduce((t, v) => t + (v.qty * v.price), 0);
        },

        get totalPriceFormatted() {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(this.totalPrice);
        },

        getFormData() {
            const result = {};
            Object.entries(this.cart).forEach(([id, data]) => {
                result[`fnb[${id}]`] = data.qty;
            });
            return result;
        }
    }));

});

Alpine.start();

// ── Utils ──
window.formatRupiah = (amount) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};

// Auto-dismiss flash messages
document.addEventListener('DOMContentLoaded', () => {
    const flashes = document.querySelectorAll('[data-flash]');
    flashes.forEach(el => {
        setTimeout(() => {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(() => el.remove(), 500);
        }, 5000);
    });
});
