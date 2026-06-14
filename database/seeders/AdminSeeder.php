<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin; // Panggil model Admin
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Sang Penguasa',
            'email' => 'cxnbhnqa@gmail.com',
            'password' => Hash::make('0705Course001@'),
        ]);
        
        $this->command->info('Akun Admin berhasil disuntik ke tabel rahasia!');
    }
}
