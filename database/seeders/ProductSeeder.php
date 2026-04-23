<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Inisialisasi Kategori
        $catMakanan = Category::firstOrCreate(['name' => 'Meal']);
        $catJus = Category::firstOrCreate(['name' => 'Juice']);
        $catSoftDrink = Category::firstOrCreate(['name' => 'Softdrinks']);
        $catAirMineral = Category::firstOrCreate(['name' => 'Mineral Water']);
        $catMinumanLain = Category::firstOrCreate(['name' => 'Other Drinks']);
        $catNasiBox = Category::firstOrCreate(['name' => 'Rice Box']);

        // 2. Data Makanan Utama (20 Item)
        $makanan = [
            ['item' => 'Nasi Beef Bulgogi', 'harga' => 23000, 'tags' => 'savory, beef, meal, warm', 'ing' => 'daging sapi, nasi, kecap asin, bawang bombay'],
            ['item' => 'Nasi Chicken Teriyaki', 'harga' => 20000, 'tags' => 'sweet, chicken, meal, warm', 'ing' => 'ayam, nasi, saos teriyaki, jahe, wijen'],
            ['item' => 'Nasi Beef Teriyaki', 'harga' => 23000, 'tags' => 'savory, beef, meal, warm', 'ing' => 'daging sapi, nasi, saos teriyaki, bawang bombay'],
            ['item' => 'Nasi Chicken Katsu', 'harga' => 20000, 'tags' => 'crunchy, chicken, meal, warm', 'ing' => 'ayam filet, tepung roti, nasi, telur'],
            ['item' => 'Nasi Goreng Telor', 'harga' => 18000, 'tags' => 'savory, egg, meal, warm', 'ing' => 'nasi, telur, kecap manis, bawang merah'],
            ['item' => 'Karaage Saos Gochujang', 'harga' => 14000, 'tags' => 'spicy, chicken, snack, warm', 'ing' => 'ayam goreng tepung, pasta gochujang, madu'],
            ['item' => 'Sosis Panggang Saos Gochujang', 'harga' => 12000, 'tags' => 'spicy, sausage, snack, warm', 'ing' => 'sosis sapi, pasta gochujang, saos sambal'],
            ['item' => 'Mukbang Celup GE', 'harga' => 23000, 'tags' => 'savory, seafood, sharing, warm', 'ing' => 'bakso ikan, crabstick, kuah kaldu, bumbu rempah'],
            ['item' => 'Sate Korea', 'harga' => 18000, 'tags' => 'spicy, beef, snack, warm', 'ing' => 'daging sapi slice, bumbu gochugaru, kecap'],
            ['item' => 'Spagethi Umami', 'harga' => 16000, 'tags' => 'savory, pasta, meal, warm', 'ing' => 'spageti, saus krim, jamur, keju'],
            ['item' => 'Kentang Goreng', 'harga' => 15000, 'tags' => 'salty, potato, snack, warm', 'ing' => 'kentang, garam, minyak nabati'],
            ['item' => 'Nasi Udang Mayo / Saos Lemon', 'harga' => 20000, 'tags' => 'sour, shrimp, meal, warm', 'ing' => 'udang, nasi, mayones, sari lemon'],
            ['item' => 'Nasi Rames GE', 'harga' => 17000, 'tags' => 'savory, traditional, meal, warm', 'ing' => 'nasi, sayur lodeh, orek tempe, sambal'],
            ['item' => 'Nasi Ayam Saos Lemon', 'harga' => 18500, 'tags' => 'sour, chicken, meal, warm', 'ing' => 'ayam, nasi, sari lemon, gula, maizena'],
            ['item' => 'Nasi Gudeg GE', 'harga' => 20000, 'tags' => 'sweet, traditional, meal, warm', 'ing' => 'nasi, nangka muda, krecek, telur pindang'],
            ['item' => 'Nasi Telor Sambal', 'harga' => 15000, 'tags' => 'spicy, egg, meal, warm', 'ing' => 'nasi, telur mata sapi, sambal ulek'],
            ['item' => 'Nasi Telor Ayam Sambal', 'harga' => 20000, 'tags' => 'spicy, chicken, meal, warm', 'ing' => 'nasi, ayam goreng, telur, sambal'],
            ['item' => 'Telor Dadar Krispi', 'harga' => 7000, 'tags' => 'salty, egg, snack, warm', 'ing' => 'telur, tepung bumbu, daun bawang'],
            ['item' => 'Telor Balado', 'harga' => 7000, 'tags' => 'spicy, egg, snack, warm', 'ing' => 'telur rebus, sambal balado, daun jeruk'],
            ['item' => 'Onigiri', 'harga' => 11000, 'tags' => 'savory, rice, snack, cold', 'ing' => 'nasi jepang, nori, isian tuna/ayam'],
        ];

        foreach ($makanan as $m) {
            Product::updateOrCreate(
                ['name' => $m['item']],
                [
                    'category_id' => $catMakanan->id,
                    'price' => $m['harga'],
                    'tags' => $m['tags'],
                    'ingredients' => $m['ing'],
                    'is_available' => true
                ]
            );
        }

        // 3. Data Aneka Jus
        $jus = [
            ['name' => 'Jus Alpukat', 'ing' => 'alpukat, susu coklat, air, gula'],
            ['name' => 'Jus Mangga', 'ing' => 'mangga arumanis, air, gula'],
            ['name' => 'Jus Jambu Biji', 'ing' => 'jambu biji merah, air, gula'],
            ['name' => 'Jus Jeruk Peras', 'ing' => 'jeruk peras segar, air, gula'],
            ['name' => 'Jus Buah Naga', 'ing' => 'buah naga merah, air, gula'],
            ['name' => 'Jus Apel', 'ing' => 'apel malang, air, gula'],
        ];
        foreach ($jus as $j) {
            Product::updateOrCreate(
                ['name' => $j['name']],
                [
                    'category_id' => $catJus->id,
                    'price' => 15000,
                    'tags' => 'fruit, fresh, beverage, cold',
                    'ingredients' => $j['ing'],
                    'is_available' => true
                ]
            );
        }

        // 4. Soft Drinks
        $soft = ["Coca-Cola", "Fanta", "Sprite", "Teh Botol Sosro", "Pulpy Orange"];
        foreach ($soft as $s) {
            Product::updateOrCreate(
                ['name' => $s],
                [
                    'category_id' => $catSoftDrink->id,
                    'price' => 8000,
                    'tags' => 'soda, fresh, beverage, cold',
                    'ingredients' => 'minuman kemasan pabrik',
                    'is_available' => true
                ]
            );
        }

        // 5. Air Mineral
        $air = ["Aqua 600ml", "Le Minerale"];
        foreach ($air as $a) {
            Product::updateOrCreate(
                ['name' => $a],
                [
                    'category_id' => $catAirMineral->id,
                    'price' => 5000,
                    'tags' => 'water, neutral, beverage, cold',
                    'ingredients' => 'air mineral pegunungan',
                    'is_available' => true
                ]
            );
        }

        // 6. Minuman Segar Lainnya
        $lain = [
            ['name' => 'Es Teh Manis', 'harga' => 5000, 'tags' => 'tea, sweet, beverage, cold', 'ing' => 'teh seduh, gula, es batu'],
            ['name' => 'Es Jeruk', 'harga' => 7000, 'tags' => 'fruit, fresh, beverage, cold', 'ing' => 'jeruk peras, gula, air'],
            ['name' => 'Lemon Tea', 'harga' => 8000, 'tags' => 'tea, sour, beverage, cold', 'ing' => 'teh, sari lemon, gula'],
            ['name' => 'Es Kelapa Muda', 'harga' => 12000, 'tags' => 'coconut, fresh, beverage, cold', 'ing' => 'air kelapa, daging kelapa muda'],
        ];
        foreach ($lain as $l) {
            Product::updateOrCreate(
                ['name' => $l['name']],
                [
                    'category_id' => $catMinumanLain->id,
                    'price' => $l['harga'],
                    'tags' => $l['tags'],
                    'ingredients' => $l['ing'],
                    'is_available' => true
                ]
            );
        }


        $box = [
            ['name' => 'Paket Penyetan Sambel Kencur', 'ing' => 'nasi, ayam/lele, sambal kencur, lalapan'],
            ['name' => 'Penyetan Komplit', 'ing' => 'nasi, ayam, telur, tahu tempe, sambal'],
            ['name' => 'Baso Iga Penyet', 'ing' => 'nasi, baso iga goreng, sambal penyet'],
        ];
        foreach ($box as $b) {
            Product::updateOrCreate(
                ['name' => $b['name']],
                [
                    'category_id' => $catNasiBox->id,
                    'price' => 40000,
                    'tags' => 'package, savory, heavy, warm',
                    'ingredients' => $b['ing'],
                    'is_available' => true
                ]
            );
        }
    }
}