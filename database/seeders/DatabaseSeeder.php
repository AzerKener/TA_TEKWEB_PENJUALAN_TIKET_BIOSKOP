<?php

namespace Database\Seeders;

use App\Models\FnbItem;
use App\Models\Movie;
use App\Models\Review;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\Studio;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TransactionFnbItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎬 Seeding CineXpress Database...');
        $this->command->newLine();

        // ─── 1. USERS ────────────────────────────────────────────────────
        $this->command->info('👤 Seeding users...');

        // Admin
        User::create([
            'name'              => 'Administrator CineXpress',
            'email'             => 'admin@cinexpress.id',
            'password'          => Hash::make('admin123'),
            'role'              => 'admin',
            'phone'             => '081234567890',
            'email_verified_at' => now(),
        ]);

        // 150 Customers
        User::factory()->count(150)->create();
        $this->command->info('  ✓ 151 users (1 admin + 150 customers)');

        // ─── 2. MOVIES ───────────────────────────────────────────────────
        $this->command->info('🎥 Seeding movies...');

        $movies = [
            ['title' => 'Gundala: Bangkitnya Sang Putra Petir', 'genre' => ['Aksi', 'Superhero'], 'director' => 'Joko Anwar', 'duration' => 123, 'status' => 'now_playing', 'rating' => 'PG-13', 'language' => 'Indonesia', 'imdb' => 7.8, 'company' => 'Screenplay Bumilangit', 'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'],
            ['title' => 'Pengabdi Setan 3', 'genre' => ['Horor', 'Misteri'], 'director' => 'Joko Anwar', 'duration' => 112, 'status' => 'now_playing', 'rating' => 'D17', 'language' => 'Indonesia', 'imdb' => 7.5, 'company' => 'MD Pictures', 'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA'],
            ['title' => 'Laskar Pelangi: Generasi Baru', 'genre' => ['Drama', 'Keluarga', 'Inspiratif'], 'director' => 'Riri Riza', 'duration' => 118, 'status' => 'now_playing', 'rating' => 'SU', 'language' => 'Indonesia', 'imdb' => 8.1, 'company' => 'Miles Films', 'trailer' => 'https://www.youtube.com/watch?v=t6H09PN0DFQ'],
            ['title' => 'Dilan 1995', 'genre' => ['Drama', 'Romansa'], 'director' => 'Fajar Bustomi', 'duration' => 108, 'status' => 'now_playing', 'rating' => 'G', 'language' => 'Indonesia', 'imdb' => 6.9, 'company' => 'Falcon Pictures', 'trailer' => 'https://www.youtube.com/watch?v=pQXzn8pE1KY'],
            ['title' => 'KKN di Desa Penari 2', 'genre' => ['Horor', 'Thriller'], 'director' => 'Awi Suryadi', 'duration' => 115, 'status' => 'now_playing', 'rating' => 'D17', 'language' => 'Indonesia', 'imdb' => 6.7, 'company' => 'MD Pictures', 'trailer' => 'https://www.youtube.com/watch?v=eOrNdBpGMv8'],
            ['title' => 'Avengers: Secret Wars', 'genre' => ['Aksi', 'Sci-Fi', 'Superhero'], 'director' => 'Anthony & Joe Russo', 'duration' => 165, 'status' => 'now_playing', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => 8.9, 'company' => 'Marvel Studios', 'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'],
            ['title' => 'Oppenheimer 2', 'genre' => ['Drama', 'Sejarah', 'Biografi'], 'director' => 'Christopher Nolan', 'duration' => 178, 'status' => 'now_playing', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => 8.7, 'company' => 'Universal Pictures', 'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA'],
            ['title' => 'Mission Impossible: Final Reckoning', 'genre' => ['Aksi', 'Thriller', 'Petualangan'], 'director' => 'Christopher McQuarrie', 'duration' => 148, 'status' => 'now_playing', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => 8.2, 'company' => 'Paramount Pictures', 'trailer' => 'https://www.youtube.com/watch?v=t6H09PN0DFQ'],
            ['title' => 'Moana 3', 'genre' => ['Animasi', 'Petualangan', 'Keluarga'], 'director' => 'David G. Derrick Jr.', 'duration' => 102, 'status' => 'now_playing', 'rating' => 'SU', 'language' => 'Inggris', 'imdb' => 7.6, 'company' => 'Walt Disney Animation', 'trailer' => 'https://www.youtube.com/watch?v=pQXzn8pE1KY'],
            ['title' => 'Sri Asih 2', 'genre' => ['Aksi', 'Superhero', 'Drama'], 'director' => 'Upi Avianto', 'duration' => 116, 'status' => 'now_playing', 'rating' => 'PG-13', 'language' => 'Indonesia', 'imdb' => 7.3, 'company' => 'Screenplay Bumilangit', 'trailer' => 'https://www.youtube.com/watch?v=eOrNdBpGMv8'],
            // Coming Soon
            ['title' => 'Avatar 3: Fire and Ash', 'genre' => ['Sci-Fi', 'Petualangan', 'Fantasi'], 'director' => 'James Cameron', 'duration' => 190, 'status' => 'coming_soon', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => null, 'company' => '20th Century Studios', 'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'],
            ['title' => 'Jurassic World: Rebirth', 'genre' => ['Sci-Fi', 'Petualangan', 'Thriller'], 'director' => 'Gareth Edwards', 'duration' => 132, 'status' => 'coming_soon', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => null, 'company' => 'Universal Pictures', 'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA'],
            ['title' => 'Deadpool & Wolverine 2', 'genre' => ['Aksi', 'Komedi', 'Superhero'], 'director' => 'Shawn Levy', 'duration' => 127, 'status' => 'coming_soon', 'rating' => 'D17', 'language' => 'Inggris', 'imdb' => null, 'company' => 'Marvel Studios', 'trailer' => 'https://www.youtube.com/watch?v=t6H09PN0DFQ'],
            ['title' => 'Wicked: For Good', 'genre' => ['Musikal', 'Fantasi', 'Drama'], 'director' => 'Jon M. Chu', 'duration' => 142, 'status' => 'coming_soon', 'rating' => 'PG', 'language' => 'Inggris', 'imdb' => null, 'company' => 'Universal Pictures', 'trailer' => 'https://www.youtube.com/watch?v=pQXzn8pE1KY'],
            ['title' => 'Interstellar 2', 'genre' => ['Sci-Fi', 'Drama', 'Petualangan'], 'director' => 'Christopher Nolan', 'duration' => 172, 'status' => 'coming_soon', 'rating' => 'PG', 'language' => 'Inggris', 'imdb' => null, 'company' => 'Warner Bros.', 'trailer' => 'https://www.youtube.com/watch?v=eOrNdBpGMv8'],
            ['title' => 'Sang Pemimpi: Renjana', 'genre' => ['Drama', 'Inspiratif', 'Keluarga'], 'director' => 'Riri Riza', 'duration' => 110, 'status' => 'coming_soon', 'rating' => 'SU', 'language' => 'Indonesia', 'imdb' => null, 'company' => 'Miles Films', 'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'],
            ['title' => 'Godzilla x Kong: New Empire 2', 'genre' => ['Aksi', 'Sci-Fi', 'Petualangan'], 'director' => 'Adam Wingard', 'duration' => 135, 'status' => 'coming_soon', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => null, 'company' => 'Legendary Pictures', 'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA'],
            ['title' => 'The Conjuring: Annabelle Origins', 'genre' => ['Horor', 'Supernatural'], 'director' => 'James Wan', 'duration' => 112, 'status' => 'coming_soon', 'rating' => 'D17', 'language' => 'Inggris', 'imdb' => null, 'company' => 'Warner Bros.', 'trailer' => 'https://www.youtube.com/watch?v=t6H09PN0DFQ'],
            // Ended
            ['title' => 'Titanic: 4K Remastered', 'genre' => ['Drama', 'Romansa', 'Sejarah'], 'director' => 'James Cameron', 'duration' => 195, 'status' => 'ended', 'rating' => 'PG-13', 'language' => 'Inggris', 'imdb' => 7.8, 'company' => 'Paramount Pictures', 'trailer' => 'https://www.youtube.com/watch?v=pQXzn8pE1KY'],
            ['title' => 'Si Doel The Movie 4', 'genre' => ['Drama', 'Komedi', 'Keluarga'], 'director' => 'Rano Karno', 'duration' => 105, 'status' => 'ended', 'rating' => 'SU', 'language' => 'Indonesia', 'imdb' => 6.5, 'company' => 'SinemArt', 'trailer' => 'https://www.youtube.com/watch?v=eOrNdBpGMv8'],
        ];

        $createdMovies = [];
        $synopses = [
            "Sebuah kisah epik tentang pahlawan yang bangkit dari keterpurukan untuk melindungi kotanya dari ancaman gelap. Dengan kekuatan luar biasa dan tekad membara, ia menghadapi musuh terkuatnya sepanjang masa.",
            "Perjalanan emosional yang menyentuh hati tentang persahabatan, cinta, dan pengorbanan. Film yang akan membuat Anda tertawa, menangis, dan merenungi makna kehidupan.",
            "Misteri kelam menyelimuti sebuah tempat terpencil. Ketakutan, ketegangan, dan rahasia masa lalu terungkap satu per satu dalam kisah yang tidak terlupakan.",
            "Aksi menegangkan di tengah konflik global yang mengancam keselamatan umat manusia. Para pahlawan bersatu menghadapi ancaman terbesar yang pernah ada di alam semesta.",
            "Kisah cinta yang melintasi waktu dan ruang, membuktikan bahwa takdir tidak bisa dihindari. Dua jiwa yang dipertemukan semesta harus berjuang melawan segala rintangan.",
        ];

        $castData = [
            'Reza Rahadian, Pevita Pearce, Nicholas Saputra, Chelsea Islan',
            'Chicco Jerikho, Adinia Wirasti, Oka Antara, Tara Basro',
            'Tom Holland, Zendaya, Benedict Cumberbatch, Robert Downey Jr.',
            'Mawar Eva de Jongh, Putri Marino, Giulio Parengkuan, Zara JKT48',
            'Tom Cruise, Hayley Atwell, Simon Pegg, Ving Rhames',
        ];

        foreach ($movies as $i => $data) {
            $releaseDate = match($data['status']) {
                'now_playing' => Carbon::now()->subDays(rand(7, 50))->format('Y-m-d'),
                'coming_soon' => Carbon::now()->addDays(rand(10, 60))->format('Y-m-d'),
                'ended'       => Carbon::now()->subDays(rand(90, 180))->format('Y-m-d'),
            };
            $endDate = $data['status'] === 'ended' ? Carbon::now()->subDays(rand(30, 89))->format('Y-m-d') : null;

            $slug = Str::slug($data['title']);
            // Ensure unique slug
            $existingCount = Movie::where('slug', 'like', $slug . '%')->count();
            if ($existingCount > 0) {
                $slug .= '-' . ($existingCount + 1);
            }

            $movie = Movie::create([
                'title'              => $data['title'],
                'slug'               => $slug,
                'synopsis'           => $synopses[$i % count($synopses)],
                'genre'              => $data['genre'],
                'duration'           => $data['duration'],
                'rating'             => $data['rating'],
                'director'           => $data['director'],
                'cast'               => $castData[$i % count($castData)],
                'poster_image'       => null,
                'trailer_url'        => $data['trailer'],
                'language'           => $data['language'],
                'has_subtitle'       => true,
                'status'             => $data['status'],
                'release_date'       => $releaseDate,
                'end_date'           => $endDate,
                'imdb_rating'        => $data['imdb'],
                'production_company' => $data['company'],
                'distributor'        => 'Cinema XXI Distribution',
            ]);
            $createdMovies[] = $movie;
        }

        $this->command->info('  ✓ ' . count($createdMovies) . ' movies seeded');

        // ─── 3. STUDIOS ──────────────────────────────────────────────────
        $this->command->info('🏛️  Seeding studios & seats...');

        $studiosData = [
            ['name' => 'Studio 1 Regular', 'type' => 'regular', 'rows' => 10, 'per_row' => 12, 'vip' => [], 'couple' => []],
            ['name' => 'Studio 2 Regular', 'type' => 'regular', 'rows' => 8, 'per_row' => 10, 'vip' => [], 'couple' => []],
            ['name' => 'Studio 3 IMAX', 'type' => 'imax', 'rows' => 12, 'per_row' => 14, 'vip' => ['A', 'B'], 'couple' => ['L']],
            ['name' => 'Studio 4 VIP', 'type' => 'vip', 'rows' => 6, 'per_row' => 8, 'vip' => ['A', 'B', 'C', 'D', 'E', 'F'], 'couple' => []],
            ['name' => 'Studio 5 4DX', 'type' => '4dx', 'rows' => 8, 'per_row' => 10, 'vip' => ['A'], 'couple' => ['H']],
        ];

        $createdStudios = [];
        $totalSeatCount = 0;

        foreach ($studiosData as $studioData) {
            $studio = Studio::create([
                'name'           => $studioData['name'],
                'type'           => $studioData['type'],
                'total_rows'     => $studioData['rows'],
                'columns_layout' => "1-{$studioData['per_row']}",
                'description'    => "Studio {$studioData['type']} dengan kapasitas {$studioData['rows']} baris x {$studioData['per_row']} kursi.",
                'is_active'      => true,
            ]);

            // Generate kursi
            $rowLabels = array_map(fn($i) => chr(65 + $i), range(0, $studioData['rows'] - 1));
            $seatCount = 0;

            foreach ($rowLabels as $row) {
                $seatType = 'regular';
                if (in_array($row, $studioData['vip'])) {
                    $seatType = 'vip';
                } elseif (in_array($row, $studioData['couple'])) {
                    $seatType = 'couple';
                }

                for ($n = 1; $n <= $studioData['per_row']; $n++) {
                    Seat::create([
                        'studio_id'   => $studio->id,
                        'row_label'   => $row,
                        'seat_number' => $n,
                        'seat_code'   => $row . $n,
                        'type'        => $seatType,
                        'is_active'   => true,
                    ]);
                    $seatCount++;
                }
            }

            $totalSeatCount += $seatCount;
            $createdStudios[] = $studio;
        }

        $this->command->info("  ✓ 5 studios, {$totalSeatCount} seats seeded");

        // ─── 4. FNB ITEMS ────────────────────────────────────────────────
        $this->command->info('🍿 Seeding F&B items...');

        $fnbData = [
            // Food
            ['name' => 'Popcorn Regular Salted', 'category' => 'food', 'price' => 35000, 'desc' => 'Popcorn asin ukuran regular, renyah dan gurih'],
            ['name' => 'Popcorn Regular Caramel', 'category' => 'food', 'price' => 38000, 'desc' => 'Popcorn karamel manis ukuran regular'],
            ['name' => 'Popcorn Large Salted', 'category' => 'food', 'price' => 52000, 'desc' => 'Popcorn asin ukuran besar untuk 2 orang'],
            ['name' => 'Popcorn Large Caramel', 'category' => 'food', 'price' => 55000, 'desc' => 'Popcorn karamel manis ukuran besar'],
            ['name' => 'Hot Dog Original', 'category' => 'food', 'price' => 42000, 'desc' => 'Hot dog dengan saus mustard dan ketchup'],
            ['name' => 'Hot Dog Spicy', 'category' => 'food', 'price' => 45000, 'desc' => 'Hot dog pedas dengan jalapeño dan sriracha'],
            ['name' => 'Chicken Nugget 6 pcs', 'category' => 'food', 'price' => 38000, 'desc' => 'Nugget ayam crispy 6 pieces dengan saus BBQ'],
            ['name' => 'Nachos with Cheese Dip', 'category' => 'food', 'price' => 48000, 'desc' => 'Nachos tortilla chips dengan cheese dip'],
            ['name' => 'French Fries', 'category' => 'food', 'price' => 35000, 'desc' => 'Kentang goreng crispy dengan bumbu seasoning'],
            ['name' => 'Pizza Slice Pepperoni', 'category' => 'food', 'price' => 55000, 'desc' => 'Sepotong pizza pepperoni dengan mozzarella lumer'],
            // Drinks
            ['name' => 'Coca Cola Regular', 'category' => 'drink', 'price' => 28000, 'desc' => 'Minuman bersoda Coca Cola ukuran regular'],
            ['name' => 'Coca Cola Large', 'category' => 'drink', 'price' => 38000, 'desc' => 'Minuman bersoda Coca Cola ukuran large'],
            ['name' => 'Sprite Regular', 'category' => 'drink', 'price' => 28000, 'desc' => 'Minuman bersoda Sprite ukuran regular'],
            ['name' => 'Fanta Strawberry', 'category' => 'drink', 'price' => 28000, 'desc' => 'Fanta rasa strawberry segar'],
            ['name' => 'Ice Coffee', 'category' => 'drink', 'price' => 35000, 'desc' => 'Es kopi hitam dengan gula aren premium'],
            ['name' => 'Mineral Water Aqua', 'category' => 'drink', 'price' => 18000, 'desc' => 'Air mineral Aqua 600ml'],
            ['name' => 'Chocolate Milkshake', 'category' => 'drink', 'price' => 45000, 'desc' => 'Milkshake coklat creamy dengan whipped cream'],
            ['name' => 'Green Tea Latte', 'category' => 'drink', 'price' => 42000, 'desc' => 'Matcha latte dengan susu segar'],
            // Snacks
            ['name' => 'M&M Chocolate', 'category' => 'snack', 'price' => 32000, 'desc' => 'Permen coklat M&M sharing bag'],
            ['name' => 'KitKat Bar', 'category' => 'snack', 'price' => 28000, 'desc' => 'KitKat coklat crispy 4 batang'],
            ['name' => 'Gummy Bears', 'category' => 'snack', 'price' => 25000, 'desc' => 'Permen gummy beruang aneka rasa'],
            ['name' => 'Doritos Nacho Cheese', 'category' => 'snack', 'price' => 30000, 'desc' => 'Keripik Doritos rasa keju'],
            ['name' => 'Pringles Original', 'category' => 'snack', 'price' => 35000, 'desc' => 'Keripik kentang Pringles original'],
            // Combos
            ['name' => 'Combo Hemat 1 (Regular)', 'category' => 'combo', 'price' => 75000, 'desc' => 'Popcorn Regular + Minuman Regular - Hemat 20%'],
            ['name' => 'Combo Hemat 2 (Large)', 'category' => 'combo', 'price' => 105000, 'desc' => 'Popcorn Large + 2 Minuman Regular - Hemat 25%'],
            ['name' => 'Combo Couple', 'category' => 'combo', 'price' => 145000, 'desc' => 'Popcorn Large + 2 Minuman Large + 2 Snack - Hemat 30%'],
            ['name' => 'Combo Family Pack', 'category' => 'combo', 'price' => 210000, 'desc' => '2 Popcorn Large + 4 Minuman + 2 Snack - Hemat 35%'],
            ['name' => 'Snack Pack Premium', 'category' => 'combo', 'price' => 85000, 'desc' => 'Nachos + Nugget + Minuman Besar'],
            ['name' => 'Kids Combo', 'category' => 'combo', 'price' => 65000, 'desc' => 'Popcorn Kecil + Minuman + Gummy Bears'],
            ['name' => 'Date Night Special', 'category' => 'combo', 'price' => 160000, 'desc' => 'Popcorn Caramel Large + 2 Milkshake + M&M'],
        ];

        $fnbItems = [];
        foreach ($fnbData as $item) {
            $fnb = FnbItem::create([
                'name'         => $item['name'],
                'description'  => $item['desc'],
                'category'     => $item['category'],
                'price'        => $item['price'],
                'image'        => null,
                'stock'        => rand(50, 200),
                'is_available' => true,
            ]);
            $fnbItems[] = $fnb;
        }

        $this->command->info('  ✓ ' . count($fnbItems) . ' F&B items seeded');

        // ─── 5. SCHEDULES ────────────────────────────────────────────────
        $this->command->info('📅 Seeding schedules (200+ records)...');

        $nowPlayingMovies = array_filter($createdMovies, fn($m) => $m->status === 'now_playing');
        $showTimes = ['10:00', '13:00', '16:00', '19:00', '21:30'];

        $createdSchedules = [];

        // Jadwal 45 hari ke belakang (historis) + 15 hari ke depan
        for ($dayOffset = -45; $dayOffset <= 15; $dayOffset++) {
            $date = Carbon::today()->addDays($dayOffset)->format('Y-m-d');

            foreach ($createdStudios as $studio) {
                // 3-4 jadwal per studio per hari
                $dailyMovies = array_slice($nowPlayingMovies, 0, min(4, count($nowPlayingMovies)));
                shuffle($dailyMovies);
                $selectedMovies = array_slice($dailyMovies, 0, rand(2, 4));

                $usedTimes = [];

                foreach ($selectedMovies as $movie) {
                    $startTime = null;
                    foreach ($showTimes as $t) {
                        if (!in_array($t, $usedTimes)) {
                            $startTime = $t;
                            break;
                        }
                    }

                    if (!$startTime) continue;

                    $start  = Carbon::parse("{$date} {$startTime}");
                    $end    = $start->copy()->addMinutes($movie->duration + 30);
                    $langType = ['dubbed', 'subtitled', 'subtitled'][array_rand(['dubbed', 'subtitled', 'subtitled'])];

                    $priceMultiplier = match($studio->type) {
                        'imax'      => 1.8,
                        '4dx'       => 1.6,
                        'vip'       => 2.0,
                        'premiere'  => 2.5,
                        default     => 1.0,
                    };

                    $schedule = Schedule::create([
                        'movie_id'      => $movie->id,
                        'studio_id'     => $studio->id,
                        'show_date'     => $date,
                        'start_time'    => $startTime . ':00',
                        'end_time'      => $end->format('H:i:s'),
                        'price_regular' => round(50000 * $priceMultiplier / 5000) * 5000,
                        'price_vip'     => round(100000 * $priceMultiplier / 5000) * 5000,
                        'price_couple'  => round(150000 * $priceMultiplier / 5000) * 5000,
                        'language_type' => $langType,
                        'is_active'     => true,
                    ]);

                    $createdSchedules[] = $schedule;
                    $usedTimes[] = $startTime;
                }
            }
        }

        $this->command->info('  ✓ ' . count($createdSchedules) . ' schedules seeded');

        // ─── 6. TRANSACTIONS & TICKETS ───────────────────────────────────
        $this->command->info('💳 Seeding transactions & tickets (200 records)...');

        $customers     = User::customer()->get();
        $paymentMethods = ['transfer_bank', 'gopay', 'ovo', 'dana', 'shopee_pay', 'credit_card', 'qris'];
        $pastSchedules = array_filter($createdSchedules, fn($s) => $s->show_date < today()->format('Y-m-d'));
        $pastSchedules = array_values($pastSchedules);

        $transactionCount = 0;
        $ticketCount = 0;

        foreach (array_slice($pastSchedules, 0, 200) as $schedule) {
            if (rand(0, 100) < 40) continue; // 40% jadwal tidak ada yang beli

            $schedule->load('studio.seats');
            $seats = $schedule->studio->seats->shuffle()->take(rand(1, 5));

            if ($seats->isEmpty()) continue;

            $customer = $customers->random();
            $subtotalTicket = 0;
            $ticketData = [];

            foreach ($seats as $seat) {
                $price = match($seat->type) {
                    'vip'    => $schedule->price_vip,
                    'couple' => $schedule->price_couple,
                    default  => $schedule->price_regular,
                };
                $subtotalTicket += $price;
                $ticketData[] = ['seat' => $seat, 'price' => $price];
            }

            // Random F&B
            $subtotalFnb = 0;
            $fnbOrderData = [];

            if (rand(0, 1)) {
                $randomFnbs = collect($fnbItems)->random(rand(1, 3));
                foreach ($randomFnbs as $fnb) {
                    $qty = rand(1, 2);
                    $sub = $fnb->price * $qty;
                    $subtotalFnb += $sub;
                    $fnbOrderData[] = ['fnb' => $fnb, 'qty' => $qty, 'sub' => $sub];
                }
            }

            $taxAmount   = ($subtotalTicket + $subtotalFnb) * 0.10;
            $totalAmount = $subtotalTicket + $subtotalFnb + $taxAmount;
            $method      = $paymentMethods[array_rand($paymentMethods)];
            $paidAt      = Carbon::parse($schedule->show_date->toDateString() . ' ' . substr($schedule->start_time, 0, 5))->subHours(rand(1, 24));

            $transaction = Transaction::create([
                'user_id'        => $customer->id,
                'schedule_id'    => $schedule->id,
                'subtotal_ticket'=> $subtotalTicket,
                'subtotal_fnb'   => $subtotalFnb,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => $method,
                'payment_status' => 'paid',
                'paid_at'        => $paidAt,
                'expires_at'     => $paidAt->copy()->addMinutes(15),
            ]);

            $transactionCount++;

            foreach ($ticketData as $td) {
                // Cek duplikat kursi
                $alreadyBooked = Ticket::where('seat_id', $td['seat']->id)
                    ->where('schedule_id', $schedule->id)
                    ->exists();

                if (!$alreadyBooked) {
                    Ticket::create([
                        'transaction_id' => $transaction->id,
                        'seat_id'        => $td['seat']->id,
                        'schedule_id'    => $schedule->id,
                        'seat_type'      => $td['seat']->type,
                        'price'          => $td['price'],
                        'status'         => 'used',
                        'used_at'        => Carbon::parse($schedule->show_date->toDateString() . ' ' . substr($schedule->start_time, 0, 5))->addMinutes(5),
                    ]);
                    $ticketCount++;
                }
            }

            foreach ($fnbOrderData as $fd) {
                TransactionFnbItem::create([
                    'transaction_id' => $transaction->id,
                    'fnb_item_id'    => $fd['fnb']->id,
                    'quantity'       => $fd['qty'],
                    'unit_price'     => $fd['fnb']->price,
                    'subtotal'       => $fd['sub'],
                ]);
            }
        }

        // Tambah beberapa transaksi pending
        for ($i = 0; $i < 15; $i++) {
            $upcomingSchedules = array_filter($createdSchedules, fn($s) => $s->show_date >= today()->format('Y-m-d'));
            $schedule = collect($upcomingSchedules)->random();
            $customer = $customers->random();

            $schedule->load('studio.seats');
            $seat  = $schedule->studio->seats->random();
            $price = $schedule->price_regular;

            $transaction = Transaction::create([
                'user_id'        => $customer->id,
                'schedule_id'    => $schedule->id,
                'subtotal_ticket'=> $price,
                'subtotal_fnb'   => 0,
                'tax_amount'     => $price * 0.10,
                'total_amount'   => $price * 1.10,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => 'pending',
                'expires_at'     => now()->addMinutes(15),
            ]);
        }

        $this->command->info("  ✓ {$transactionCount} paid transactions, {$ticketCount} tickets seeded");

        // ─── 7. REVIEWS ──────────────────────────────────────────────────
        $this->command->info('⭐ Seeding reviews (180+ records)...');

        $reviewComments = [
            'Film yang sangat mengagumkan! Ceritanya menarik dan efek visualnya luar biasa.',
            'Akting para pemain sangat memukau. Recommended banget!',
            'Plotnya agak lemah di beberapa bagian, tapi secara keseluruhan oke.',
            'Sangat seru dan menegangkan dari awal sampai akhir!',
            'Sedikit mengecewakan, ekspektasi saya lebih tinggi dari ini.',
            'Film terbaik yang pernah saya tonton tahun ini. Harus ditonton!',
            'Cerita yang menyentuh hati. Saya sampai menangis di bioskop.',
            'Efek visualnya spektakuler! Sayang ceritanya biasa saja.',
            'Akting sangat natural, cerita original. Bangga dengan sinema Indonesia!',
            'Lumayan, cocok untuk nonton bareng keluarga di akhir pekan.',
        ];

        $reviewCount = 0;
        $usedPairs = []; // user_id-movie_id pairs

        foreach ($customers->shuffle()->take(120) as $customer) {
            $movieSample = collect($createdMovies)->where('status', '!=', 'coming_soon')->shuffle()->take(rand(1, 3));
            foreach ($movieSample as $movie) {
                $key = "{$customer->id}-{$movie->id}";
                if (in_array($key, $usedPairs)) continue;

                Review::create([
                    'user_id'    => $customer->id,
                    'movie_id'   => $movie->id,
                    'rating'     => rand(3, 5),
                    'comment'    => $reviewComments[array_rand($reviewComments)],
                    'is_approved'=> true,
                ]);

                $usedPairs[] = $key;
                $reviewCount++;
            }
        }

        $this->command->info("  ✓ {$reviewCount} reviews seeded");

        $this->command->newLine();
        $this->command->info('✅ CineXpress database seeded successfully!');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users',        User::count()],
                ['Movies',       Movie::count()],
                ['Studios',      Studio::count()],
                ['Seats',        Seat::count()],
                ['Schedules',    Schedule::count()],
                ['F&B Items',    FnbItem::count()],
                ['Transactions', Transaction::count()],
                ['Tickets',      Ticket::count()],
                ['Reviews',      Review::count()],
            ]
        );
    }
}
