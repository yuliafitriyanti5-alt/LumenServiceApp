<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;



class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan ID 1 ini ada di tabel users kamu.
        // Jika user kamu punya ID lain, ganti angka 1 di bawah ini.
        $userId = 1; 

        Book::create([
            'user_id' => $userId,
            'title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
            'published_year' => 1980,
        ]);

        Book::create([
            'user_id' => $userId,
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'published_year' => 2005,
        ]);

        Book::create([
            'user_id' => $userId,
            'title' => 'Filosofi Teras',
            'author' => 'Henry Manampiring',
            'published_year' => 2018,
        ]);
        
        Book::create([
            'user_id' => $userId,
            'title' => 'Sapiens: A Brief History of Humankind',
            'author' => 'Yuval Noah Harari',
            'published_year' => 2011,
        ]);
    }
}