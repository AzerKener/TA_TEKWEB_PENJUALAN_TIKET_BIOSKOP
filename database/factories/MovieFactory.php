<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    private array $movieData = [
        // Now Playing
        ['title' => 'Gundala: Bangkitnya Sang Putra Petir', 'genre' => ['Aksi', 'Superhero'], 'director' => 'Joko Anwar', 'duration' => 123, 'status' => 'now_playing', 'rating' => 'PG-13'],
        ['title' => 'Laskar Pelangi: Generasi Baru', 'genre' => ['Drama', 'Keluarga'], 'director' => 'Riri Riza', 'duration' => 118, 'status' => 'now_playing', 'rating' => 'SU'],
        ['title' => 'KKN di Desa Penari 2', 'genre' => ['Horor', 'Misteri'], 'director' => 'Awi Suryadi', 'duration' => 112, 'status' => 'now_playing', 'rating' => 'D17'],
        ['title' => 'Dilan 1995', 'genre' => ['Drama', 'Romansa'], 'director' => 'Fajar Bustomi', 'duration' => 108, 'status' => 'now_playing', 'rating' => 'G'],
        ['title' => 'Avengers: Secret Wars', 'genre' => ['Aksi', 'Sci-Fi', 'Superhero'], 'director' => 'Russo Brothers', 'duration' => 165, 'status' => 'now_playing', 'rating' => 'PG-13'],
        ['title' => 'The Dark Knight Returns', 'genre' => ['Aksi', 'Thriller'], 'director' => 'Christopher Nolan', 'duration' => 155, 'status' => 'now_playing', 'rating' => 'PG-13'],
        ['title' => 'Oppenheimer 2', 'genre' => ['Drama', 'Sejarah', 'Biografi'], 'director' => 'Christopher Nolan', 'duration' => 178, 'status' => 'now_playing', 'rating' => 'PG-13'],
        ['title' => 'Pengabdi Setan 3', 'genre' => ['Horor', 'Thriller'], 'director' => 'Joko Anwar', 'duration' => 114, 'status' => 'now_playing', 'rating' => 'D17'],
        ['title' => 'Moana 3', 'genre' => ['Animasi', 'Petualangan', 'Keluarga'], 'director' => 'Ron Clements', 'duration' => 102, 'status' => 'now_playing', 'rating' => 'SU'],
        ['title' => 'Mission Impossible: Final Reckoning', 'genre' => ['Aksi', 'Thriller', 'Petualangan'], 'director' => 'Christopher McQuarrie', 'duration' => 148, 'status' => 'now_playing', 'rating' => 'PG-13'],
        // Coming Soon
        ['title' => 'Avatar 3: Fire and Ash', 'genre' => ['Sci-Fi', 'Petualangan', 'Fantasi'], 'director' => 'James Cameron', 'duration' => 190, 'status' => 'coming_soon', 'rating' => 'PG-13'],
        ['title' => 'Jurassic World: Rebirth', 'genre' => ['Sci-Fi', 'Petualangan', 'Thriller'], 'director' => 'Gareth Edwards', 'duration' => 132, 'status' => 'coming_soon', 'rating' => 'PG-13'],
        ['title' => 'Deadpool & Wolverine 2', 'genre' => ['Aksi', 'Komedi', 'Superhero'], 'director' => 'Shawn Levy', 'duration' => 127, 'status' => 'coming_soon', 'rating' => 'D17'],
        ['title' => 'Wicked: Part Two', 'genre' => ['Musikal', 'Fantasi', 'Drama'], 'director' => 'Jon M. Chu', 'duration' => 142, 'status' => 'coming_soon', 'rating' => 'PG'],
        ['title' => 'Inception 2', 'genre' => ['Sci-Fi', 'Thriller', 'Aksi'], 'director' => 'Christopher Nolan', 'duration' => 160, 'status' => 'coming_soon', 'rating' => 'PG-13'],
        ['title' => 'Sri Asih 2', 'genre' => ['Aksi', 'Superhero', 'Drama'], 'director' => 'Upi Avianto', 'duration' => 116, 'status' => 'coming_soon', 'rating' => 'PG-13'],
        ['title' => 'Sang Pemimpi: Renjana', 'genre' => ['Drama', 'Inspiratif'], 'director' => 'Riri Riza', 'duration' => 110, 'status' => 'coming_soon', 'rating' => 'SU'],
        ['title' => 'Interstellar 2', 'genre' => ['Sci-Fi', 'Drama', 'Petualangan'], 'director' => 'Christopher Nolan', 'duration' => 172, 'status' => 'coming_soon', 'rating' => 'PG'],
        // Ended
        ['title' => 'Titanic: Remastered', 'genre' => ['Drama', 'Romansa', 'Sejarah'], 'director' => 'James Cameron', 'duration' => 195, 'status' => 'ended', 'rating' => 'PG-13'],
        ['title' => 'Si Doel The Movie 4', 'genre' => ['Drama', 'Komedi', 'Keluarga'], 'director' => 'Rano Karno', 'duration' => 105, 'status' => 'ended', 'rating' => 'SU'],
    ];

    private array $synopses = [
        'Sebuah kisah epik tentang seorang pahlawan yang bangkit dari keterpurukan untuk melindungi kotanya dari ancaman gelap yang semakin membesar. Dengan kekuatan luar biasa dan tekad yang membara, ia harus menghadapi musuh terkuatnya.',
        'Perjalanan emosional yang menyentuh hati tentang persahabatan, cinta, dan pengorbanan. Sebuah film yang akan membuat Anda tertawa, menangis, dan merenungi makna kehidupan yang sesungguhnya.',
        'Misteri kelam menyelimuti sebuah desa terpencil ketika sekelompok orang tiba dan menemukan hal-hal yang tak terduga. Ketakutan, ketegangan, dan rahasia masa lalu terungkap satu per satu.',
        'Kisah cinta yang melintasi waktu dan ruang, membuktikan bahwa takdir tidak bisa dihindari. Dua jiwa yang dipertemukan oleh semesta harus berjuang melawan segala rintangan.',
        'Aksi menegangkan di tengah konflik global yang mengancam keselamatan umat manusia. Para pahlawan bersatu untuk menghadapi ancaman terbesar yang pernah ada.',
    ];

    private array $casts = [
        'Reza Rahadian, Pevita Pearce, Nicholas Saputra, Chelsea Islan',
        'Chicco Jerikho, Adinia Wirasti, Oka Antara, Tara Basro',
        'Mawar Eva de Jongh, Putri Marino, Giulio Parengkuan, Zara JKT48',
        'Rio Dewanto, Raisa Andriana, Dion Wiyoko, Caitlin Halderman',
        'Tom Holland, Zendaya, Benedict Cumberbatch, Robert Downey Jr.',
    ];

    private array $productionCompanies = [
        'Screenplay Bumilangit', 'Miles Films', 'MD Pictures',
        'Soraya Intercine Films', 'Marvel Studios', 'Warner Bros.',
        'Universal Pictures', 'Falcon Pictures', 'Base Entertainment',
    ];

    private array $distributors = [
        'Disney Indonesia', 'United International Pictures', 'Warner Bros. Indonesia',
        'Falcon Pictures', 'MD Entertainment', 'Soraya Intercine',
    ];

    private array $trailerUrls = [
        'https://www.youtube.com/watch?v=TcMBFSGVi1c',
        'https://www.youtube.com/watch?v=JfVOs4VSpmA',
        'https://www.youtube.com/watch?v=t6H09PN0DFQ',
        'https://www.youtube.com/watch?v=pQXzn8pE1KY',
        'https://www.youtube.com/watch?v=eOrNdBpGMv8',
    ];

    public function definition(): array
    {
        $movie = $this->faker->randomElement($this->movieData);

        return [
            'title'              => $movie['title'],
            'slug'               => Str::slug($movie['title']) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'synopsis'           => $this->faker->randomElement($this->synopses),
            'genre'              => $movie['genre'],
            'duration'           => $movie['duration'],
            'rating'             => $movie['rating'],
            'director'           => $movie['director'],
            'cast'               => $this->faker->randomElement($this->casts),
            'poster_image'       => null,
            'trailer_url'        => $this->faker->randomElement($this->trailerUrls),
            'language'           => $this->faker->randomElement(['Indonesia', 'Inggris', 'Korea']),
            'has_subtitle'       => true,
            'status'             => $movie['status'],
            'release_date'       => match($movie['status']) {
                'now_playing'  => $this->faker->dateTimeBetween('-60 days', '-1 day')->format('Y-m-d'),
                'coming_soon'  => $this->faker->dateTimeBetween('+7 days', '+60 days')->format('Y-m-d'),
                'ended'        => $this->faker->dateTimeBetween('-180 days', '-61 days')->format('Y-m-d'),
            },
            'end_date'           => $movie['status'] === 'ended'
                ? $this->faker->dateTimeBetween('-60 days', '-30 days')->format('Y-m-d')
                : null,
            'imdb_rating'        => $this->faker->randomFloat(1, 5.5, 9.2),
            'production_company' => $this->faker->randomElement($this->productionCompanies),
            'distributor'        => $this->faker->randomElement($this->distributors),
        ];
    }

    public function nowPlaying(): static
    {
        return $this->state(fn($a) => ['status' => 'now_playing', 'release_date' => now()->subDays(rand(1, 30))->format('Y-m-d')]);
    }

    public function comingSoon(): static
    {
        return $this->state(fn($a) => ['status' => 'coming_soon', 'release_date' => now()->addDays(rand(7, 60))->format('Y-m-d')]);
    }
}
