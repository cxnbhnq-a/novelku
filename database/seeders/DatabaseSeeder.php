<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Novel;
use App\Models\Chapter;
use App\Models\Genre;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Panggil Seeder Admin Rahasia
        $this->call([AdminSeeder::class]);

        // 2. Bikin Daftar Genre
        $genres = collect(['Romance', 'Fantasy', 'Misteri', 'Sci-Fi', 'Horror', 'Action'])->map(function ($name) {
            return Genre::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        });

        // 3. Bikin Akun Testing Tetap
        $kreatorUtama = User::create([
            'name' => 'Alfan Fauzan',
            'email' => 'kreator@novelku.com',
            'password' => Hash::make('alfan123'),
            'role' => 'creator'
        ]);

        User::create([
            'name' => 'Rina Amalia',
            'email' => 'reader@novelku.com',
            'password' => Hash::make('rina123'),
            'role' => 'reader'
        ]);

        // 4. Produksi Massal: Bikin 5 Akun Kreator Random
        User::factory(5)->create(['role' => 'creator'])->each(function ($creator) use ($genres) {
            
            // Tiap Kreator bikin 3-5 Novel
            Novel::factory(rand(3, 5))->create([
                'creator_id' => $creator->id, // $creator->id sekarang otomatis ngirim UUID yang benar!
                'status' => 'ongoing'
            ])->each(function ($novel) use ($genres) {
                
                // Tempelkan 1-2 Genre secara acak
                $novel->genres()->attach($genres->random(rand(1, 2))->pluck('id'));

                // Tiap Novel diisi 5 Bab urut
                for ($i = 1; $i <= 5; $i++) {
                    Chapter::factory()->create([
                        'novel_id' => $novel->id, // Nyambungin UUID novel ke UUID chapter
                        'chapter_number' => $i,
                        'title' => 'Bab ' . $i . ': ' . fake()->sentence(3)
                    ]);
                }
            });
        });

        // 5. Kasih Kreator Utama (Alfan) beberapa novel buat lu testing langsung
        Novel::factory(4)->create([
            'creator_id' => $kreatorUtama->id, 
            'status' => 'ongoing'
        ])->each(function ($novel) use ($genres) {
            
            $novel->genres()->attach($genres->random(rand(1, 2))->pluck('id'));

            for ($i = 1; $i <= 3; $i++) {
                Chapter::factory()->create([
                    'novel_id' => $novel->id, 
                    'chapter_number' => $i,
                    'title' => 'Bab ' . $i . ': ' . fake()->sentence(2)
                ]);
            }
        });

        $this->command->info('Database berhasil dipenuhi dengan Novel, Chapter, dan Genre kelas Dewa (UUID)!');
    }
}