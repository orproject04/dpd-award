<?php

namespace Modules\Pendaftar\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pendaftar>
 */
class PendaftarFactory extends Factory
{
    /** @var class-string<Pendaftar> */
    protected $model = Pendaftar::class;

    public function definition(): array
    {
        return [
            'nomor_registrasi' => $this->faker->words(3, true),
            'kategori' => $this->faker->words(3, true),
            'nama' => $this->faker->words(3, true),
            'tempat_lahir' => $this->faker->words(3, true),
            'tanggal_lahir' => $this->faker->date(),
            'jenis_kelamin' => $this->faker->words(3, true),
            'pendidikan' => $this->faker->words(3, true),
            'alamat' => $this->faker->text(),
            'nomor_wa' => $this->faker->words(3, true),
            'email' => $this->faker->unique()->safeEmail(),
            'ktp' => $this->faker->words(3, true),
            'foto' => $this->faker->words(3, true),
        ];
    }
}
