<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Jalankan database seeder untuk data produk Koperasi GIAT dengan deskripsi dan gambar.
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
            [
                'item' => 'Nasi Beef Bulgogi', 
                'harga' => 23000, 
                'tags' => 'savory, beef, meal, warm', 
                'ing' => 'daging sapi, nasi, kecap asin, bawang bombay',
                'desc' => 'Irisan daging sapi tipis yang dimarinasi dengan saus Bulgogi manis gurih khas Korea, disajikan di atas nasi putih hangat.',
                'img' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Chicken Teriyaki', 
                'harga' => 20000, 
                'tags' => 'sweet, chicken, meal, warm', 
                'ing' => 'ayam, nasi, saos teriyaki, jahe, wijen',
                'desc' => 'Potongan ayam empuk dengan balutan saus teriyaki otentik yang manis dan kental, ditaburi wijen sangrai.',
                'img' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Beef Teriyaki', 
                'harga' => 23000, 
                'tags' => 'savory, beef, meal, warm', 
                'ing' => 'daging sapi, nasi, saos teriyaki, bawang bombay',
                'desc' => 'Daging sapi tumis dengan bawang bombay segar dan saus teriyaki yang kaya rasa, sangat cocok untuk makan siang.',
                'img' => 'https://images.unsplash.com/photo-1626074353765-517a681e40be?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Chicken Katsu', 
                'harga' => 20000, 
                'tags' => 'crunchy, chicken, meal, warm', 
                'ing' => 'ayam filet, tepung roti, nasi, telur',
                'desc' => 'Fillet dada ayam goreng tepung roti yang renyah di luar dan juicy di dalam, lengkap dengan nasi putih.',
                'img' => 'https://images.unsplash.com/photo-1594911771146-519391060931?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Goreng Telor', 
                'harga' => 18000, 
                'tags' => 'savory, egg, meal, warm', 
                'ing' => 'nasi, telur, kecap manis, bawang merah',
                'desc' => 'Nasi goreng bumbu rumahan yang harum, disajikan dengan telur dadar/ceplok dan kerupuk renyah.',
                'img' => 'https://images.unsplash.com/photo-1512058560366-cd242959b4fe?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Karaage Saos Gochujang', 
                'harga' => 14000, 
                'tags' => 'spicy, chicken, snack, warm', 
                'ing' => 'ayam goreng tepung, pasta gochujang, madu',
                'desc' => 'Ayam goreng ala Jepang yang dibalut saus Gochujang pedas manis khas Korea. Camilan yang bikin nagih!',
                'img' => 'https://images.unsplash.com/photo-1623294862310-14665518b771?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Sosis Panggang Saos Gochujang', 
                'harga' => 12000, 
                'tags' => 'spicy, sausage, snack, warm', 
                'ing' => 'sosis sapi, pasta gochujang, saos sambal',
                'desc' => 'Sosis sapi berkualitas yang dipanggang sempurna dan disiram saus pedas Korea yang menggugah selera.',
                'img' => 'https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Mukbang Celup GE', 
                'harga' => 23000, 
                'tags' => 'savory, seafood, sharing, warm', 
                'ing' => 'bakso ikan, crabstick, kuah kaldu, bumbu rempah',
                'desc' => 'Berbagai macam bakso ikan dan seafood olahan dalam kuah hangat yang gurih, cocok dinikmati bersama.',
                'img' => 'https://images.unsplash.com/photo-1551815615-69b2300bf5c7?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Sate Korea', 
                'harga' => 18000, 
                'tags' => 'spicy, beef, snack, warm', 
                'ing' => 'daging sapi slice, bumbu gochugaru, kecap',
                'desc' => 'Sate daging sapi dengan bumbu rempah Korea yang pedas dan meresap hingga ke dalam.',
                'img' => 'https://images.unsplash.com/photo-1514516322520-299289fa2ca7?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Spagethi Umami', 
                'harga' => 16000, 
                'tags' => 'savory, pasta, meal, warm', 
                'ing' => 'spageti, saus krim, jamur, keju',
                'desc' => 'Pasta spageti dengan saus krim jamur yang kaya rasa dan sentuhan keju parut yang melimpah.',
                'img' => 'https://images.unsplash.com/photo-1546549032-9571cd6b27df?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Kentang Goreng', 
                'harga' => 15000, 
                'tags' => 'salty, potato, snack, warm', 
                'ing' => 'kentang, garam, minyak nabati',
                'desc' => 'Kentang goreng renyah dengan taburan garam gurih, camilan favorit sepanjang masa.',
                'img' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Udang Mayo / Saos Lemon', 
                'harga' => 20000, 
                'tags' => 'sour, shrimp, meal, warm', 
                'ing' => 'udang, nasi, mayones, sari lemon',
                'desc' => 'Udang krispi dengan pilihan saus mayones creamy atau saus lemon segar yang unik.',
                'img' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523b?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Rames GE', 
                'harga' => 17000, 
                'tags' => 'savory, traditional, meal, warm', 
                'ing' => 'nasi, sayur lodeh, orek tempe, sambal',
                'desc' => 'Nasi rames khas nusantara dengan aneka lauk tradisional yang lengkap dan mengenyangkan.',
                'img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Ayam Saos Lemon', 
                'harga' => 18500, 
                'tags' => 'sour, chicken, meal, warm', 
                'ing' => 'ayam, nasi, sari lemon, gula, maizena',
                'desc' => 'Ayam goreng tepung yang disiram saus lemon asam manis yang segar, disajikan dengan nasi putih.',
                'img' => 'https://images.unsplash.com/photo-1562607374-06013e11768c?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Gudeg GE', 
                'harga' => 20000, 
                'tags' => 'sweet, traditional, meal, warm', 
                'ing' => 'nasi, nangka muda, krecek, telur pindang',
                'desc' => 'Gudeg nangka manis khas Jogja yang disajikan dengan krecek pedas dan telur pindang.',
                'img' => 'https://images.unsplash.com/photo-1610192244261-3f33de3f55e4?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Telor Sambal', 
                'harga' => 15000, 
                'tags' => 'spicy, egg, meal, warm', 
                'ing' => 'nasi, telur mata sapi, sambal ulek',
                'desc' => 'Menu sederhana namun juara, nasi dengan telur mata sapi dan sambal ulek pedas mantap.',
                'img' => 'https://images.unsplash.com/photo-1536392139158-b4b14f40f06a?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Nasi Telor Ayam Sambal', 
                'harga' => 20000, 
                'tags' => 'spicy, chicken, meal, warm', 
                'ing' => 'nasi, ayam goreng, telur, sambal',
                'desc' => 'Kombinasi nasi, ayam goreng, dan telur yang disiram sambal pedas menggoda.',
                'img' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Telor Dadar Krispi', 
                'harga' => 7000, 
                'tags' => 'salty, egg, snack, warm', 
                'ing' => 'telur, tepung bumbu, daun bawang',
                'desc' => 'Telor dadar yang digoreng hingga garing dan krispi, pas untuk lauk tambahan atau camilan.',
                'img' => 'https://images.unsplash.com/photo-1518492104633-130d0cc84637?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Telor Balado', 
                'harga' => 7000, 
                'tags' => 'spicy, egg, snack, warm', 
                'ing' => 'telur rebus, sambal balado, daun jeruk',
                'desc' => 'Telur rebus goreng yang diselimuti bumbu balado merah yang pedas dan harum daun jeruk.',
                'img' => 'https://images.unsplash.com/photo-1542345812-d9626fc9802c?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'item' => 'Onigiri', 
                'harga' => 11000, 
                'tags' => 'savory, rice, snack, cold', 
                'ing' => 'nasi jepang, nori, isian tuna/ayam',
                'desc' => 'Nasi kepal khas Jepang dengan isian gurih dan bungkus rumput laut yang renyah.',
                'img' => 'https://images.unsplash.com/photo-1534422298391-e4f8c170db06?q=80&w=400&auto=format&fit=crop'
            ],
        ];

        foreach ($makanan as $m) {
            Product::updateOrCreate(
                ['name' => $m['item']],
                [
                    'category_id' => $catMakanan->id,
                    'price' => $m['harga'],
                    'tags' => $m['tags'],
                    'ingredients' => $m['ing'],
                    'description' => $m['desc'],
                    'image' => $m['img'],
                    'is_available' => true
                ]
            );
        }

        // 3. Data Aneka Jus
        $jus = [
            ['name' => 'Jus Alpukat', 'ing' => 'alpukat, susu coklat, air, gula', 'desc' => 'Jus alpukat mentega yang kental dengan siraman kental manis coklat.', 'img' => 'https://images.unsplash.com/photo-1589733901241-5d5297e4474f?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Jus Mangga', 'ing' => 'mangga arumanis, air, gula', 'desc' => 'Jus mangga segar dari buah pilihan, murni tanpa pengawet.', 'img' => 'https://images.unsplash.com/photo-1591073113125-e46713c829ed?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Jus Jambu Biji', 'ing' => 'jambu biji merah, air, gula', 'desc' => 'Kaya akan vitamin C, jus jambu biji merah ini sangat baik untuk imunitas.', 'img' => 'https://images.unsplash.com/photo-1622350811905-f376ba816f1c?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Jus Jeruk Peras', 'ing' => 'jeruk peras segar, air, gula', 'desc' => 'Jeruk peras murni yang memberikan kesegaran instan di siang hari.', 'img' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Jus Buah Naga', 'ing' => 'buah naga merah, air, gula', 'desc' => 'Jus buah naga merah yang segar dan kaya akan serat.', 'img' => 'https://images.unsplash.com/photo-1523456760083-f667e7c4f42b?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Jus Apel', 'ing' => 'apel malang, air, gula', 'desc' => 'Kesegaran jus apel malang yang manis alami dan menyehatkan.', 'img' => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?q=80&w=400&auto=format&fit=crop'],
        ];
        foreach ($jus as $j) {
            Product::updateOrCreate(
                ['name' => $j['name']],
                [
                    'category_id' => $catJus->id,
                    'price' => 15000,
                    'tags' => 'fruit, fresh, beverage, cold',
                    'ingredients' => $j['ing'],
                    'description' => $j['desc'],
                    'image' => $j['img'],
                    'is_available' => true
                ]
            );
        }

        // 4. Soft Drinks
        $soft = [
            ['name' => 'Coca-Cola', 'img' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Fanta', 'img' => 'https://images.unsplash.com/photo-1624517452488-04869289c4ca?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Sprite', 'img' => 'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Teh Botol Sosro', 'img' => 'https://images.unsplash.com/photo-1621252069677-71701389e6e8?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Pulpy Orange', 'img' => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?q=80&w=400&auto=format&fit=crop'],
        ];
        foreach ($soft as $s) {
            Product::updateOrCreate(
                ['name' => $s['name']],
                [
                    'category_id' => $catSoftDrink->id,
                    'price' => 8000,
                    'tags' => 'soda, fresh, beverage, cold',
                    'ingredients' => 'minuman kemasan pabrik',
                    'description' => 'Minuman berkarbonasi atau teh kemasan yang menyegarkan saat dingin.',
                    'image' => $s['img'],
                    'is_available' => true
                ]
            );
        }

        // 5. Air Mineral
        $air = [
            ['name' => 'Aqua 600ml', 'img' => 'https://images.unsplash.com/photo-1616031037011-087000171abe?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Le Minerale', 'img' => 'https://images.unsplash.com/photo-1560023907-5f339617ea30?q=80&w=400&auto=format&fit=crop'],
        ];
        foreach ($air as $a) {
            Product::updateOrCreate(
                ['name' => $a['name']],
                [
                    'category_id' => $catAirMineral->id,
                    'price' => 5000,
                    'tags' => 'water, neutral, beverage, cold',
                    'ingredients' => 'air mineral pegunungan',
                    'description' => 'Air mineral murni berkualitas untuk hidrasi tubuh yang sehat.',
                    'image' => $a['img'],
                    'is_available' => true
                ]
            );
        }

        // 6. Minuman Segar Lainnya
        $lain = [
            ['name' => 'Es Teh Manis', 'harga' => 5000, 'tags' => 'tea, sweet, beverage, cold', 'ing' => 'teh seduh, gula, es batu', 'desc' => 'Teh seduh tradisional dengan gula asli dan es batu yang menyegarkan.', 'img' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Es Jeruk', 'harga' => 7000, 'tags' => 'fruit, fresh, beverage, cold', 'ing' => 'jeruk peras, gula, air', 'desc' => 'Sari jeruk asli yang diperas langsung, manis dan kaya vitamin.', 'img' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Lemon Tea', 'harga' => 8000, 'tags' => 'tea, sour, beverage, cold', 'ing' => 'teh, sari lemon, gula', 'desc' => 'Perpaduan teh pilihan dan ekstrak lemon yang memberikan sensasi segar.', 'img' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Es Kelapa Muda', 'harga' => 12000, 'tags' => 'coconut, fresh, beverage, cold', 'ing' => 'air kelapa, daging kelapa muda', 'desc' => 'Air kelapa muda segar langsung dari buahnya, alami dan menghidrasi.', 'img' => 'https://images.unsplash.com/photo-1525904097878-94fb15835963?q=80&w=400&auto=format&fit=crop'],
        ];
        foreach ($lain as $l) {
            Product::updateOrCreate(
                ['name' => $l['name']],
                [
                    'category_id' => $catMinumanLain->id,
                    'price' => $l['harga'],
                    'tags' => $l['tags'],
                    'ingredients' => $l['ing'],
                    'description' => $l['desc'],
                    'image' => $l['img'],
                    'is_available' => true
                ]
            );
        }

        // 7. Paket Nasi Box
        $box = [
            ['name' => 'Paket Penyetan Sambel Kencur', 'ing' => 'nasi, ayam/lele, sambal kencur, lalapan', 'desc' => 'Paket nasi lengkap dengan ayam atau lele goreng dan sambal kencur yang khas aroma rempahnya.', 'img' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Penyetan Komplit', 'ing' => 'nasi, ayam, telur, tahu tempe, sambal', 'desc' => 'Kenikmatan paket nasi penyetan dengan lauk lengkap ayam, telur, dan tahu tempe.', 'img' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?q=80&w=400&auto=format&fit=crop'],
            ['name' => 'Baso Iga Penyet', 'ing' => 'nasi, baso iga goreng, sambal penyet', 'desc' => 'Baso iga goreng yang empuk dipadukan dengan pedasnya sambal penyet yang nendang.', 'img' => 'https://images.unsplash.com/photo-1621252069677-71701389e6e8?q=80&w=400&auto=format&fit=crop'],
        ];
        foreach ($box as $b) {
            Product::updateOrCreate(
                ['name' => $b['name']],
                [
                    'category_id' => $catNasiBox->id,
                    'price' => 40000,
                    'tags' => 'package, savory, heavy, warm',
                    'ingredients' => $b['ing'],
                    'description' => $b['desc'],
                    'image' => $b['img'],
                    'is_available' => true
                ]
            );
        }
    }
}