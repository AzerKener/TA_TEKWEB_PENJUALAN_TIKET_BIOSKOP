<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    private static string $password = '';

    // Nama Indonesia yang realistis
    private array $indonesianFirstNames = [
        'Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitra', 'Gina', 'Hendra',
        'Indah', 'Joko', 'Kartini', 'Lukman', 'Maya', 'Nanda', 'Oktavia',
        'Putra', 'Ratna', 'Sari', 'Toni', 'Ulfa', 'Vera', 'Wahyu', 'Xenia',
        'Yudi', 'Zahra', 'Agus', 'Bagas', 'Candra', 'Dian', 'Erna',
        'Fajar', 'Galih', 'Hesti', 'Ivan', 'Jeni', 'Kiki', 'Lina',
        'Mira', 'Niko', 'Ophi', 'Panji', 'Qila', 'Rizki', 'Sinta',
        'Tama', 'Udin', 'Vina', 'Widi', 'Yayan', 'Zena',
    ];

    private array $indonesianLastNames = [
        'Santoso', 'Wijaya', 'Kusuma', 'Pratama', 'Setiawan', 'Hidayat',
        'Nugroho', 'Purnama', 'Rahayu', 'Sutrisno', 'Anwar', 'Basuki',
        'Cahyono', 'Darmawan', 'Effendi', 'Firmansyah', 'Gunawan', 'Hartono',
        'Irawan', 'Jaya', 'Kurniawan', 'Lestari', 'Mahendra', 'Nurdiana',
        'Oktavian', 'Permana', 'Qodri', 'Rosyadi', 'Sukmana', 'Tambunan',
        'Usman', 'Valentino', 'Wibowo', 'Yuliana', 'Zaenuri', 'Adiputra',
    ];

    public function definition(): array
    {
        $first = $this->faker->randomElement($this->indonesianFirstNames);
        $last  = $this->faker->randomElement($this->indonesianLastNames);
        $name  = "{$first} {$last}";

        return [
            'name'              => $name,
            'email'             => Str::lower("{$first}.{$last}" . $this->faker->numberBetween(1, 999)) . '@' . $this->faker->randomElement(['gmail.com', 'yahoo.com', 'outlook.com']),
            'phone'             => '08' . $this->faker->numerify('#########'),
            'role'              => 'customer',
            'date_of_birth'     => $this->faker->dateTimeBetween('-50 years', '-17 years')->format('Y-m-d'),
            'email_verified_at' => now(),
            'password'          => static::$password ?: static::$password = Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
