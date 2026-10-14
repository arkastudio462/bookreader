<?php

namespace Database\Seeders;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoBookSeeder extends Seeder
{
    public function run(): void
    {
        $publisher = User::where('role', 'publisher')->first();
        $author = User::where('role', 'author')->first();
        $users = User::where('role', 'user')->take(5)->get();

        $books = [
            [
                'title' => 'Hujan di Atas Kota Lama',
                'author_name' => 'Arya Wirawan',
                'isbn' => '978-602-03-4567-1',
                'price' => 70000,
                'discount_price' => 49000,
                'category' => 'Fiksi',
                'genre' => 'Historical',
                'pages' => 320,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAk4w8qzjfIEn4mBhQyZc7SON7yvsekEiNKvc7OxVdEQNxoG_TSP8Pcr0ULAUBeZkitzZ2YcwbB9hO1dQAUa2PmVjlE_fOtXepxADuogBFVoY7qIPPu7KUi1g4LDVLnVZNrWobl6xtZs7IcPXZ2numYblkix7IbzhyloyJ6tgr3FvoTw15yu1w6cVg7XfGc6zmkKXTl3O2_bOWnGVBma4X0CnE41-zGJMXR56fhWmNC9UyW7JZ144eQSQ',
                'featured' => true,
            ],
            [
                'title' => 'Kopi, Kota, dan Kamu',
                'author_name' => 'Dian Sastrowardoyo',
                'isbn' => '978-602-03-4567-2',
                'price' => 65000,
                'category' => 'Fiksi',
                'genre' => 'Romansa',
                'pages' => 280,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBPCSnDMIoNFI6IYPrWmpmWcpR9GHDa6gQJBJmuV4FipV0399_lx38t-rhP0B_1tCjUclVj2tYQFioE0DQSh3G6j6hUY4TADYcgD1fOuyod9VsRvveXPsqqljwiuQPlbUouPrd7OtW6w0XYCslWTkyG9ayjWnwRvlJGaD4lzX1Rb6PHLnpdb0BsF1z3YgCDqO2XS8Cs1DKsttAp43FOTXG77VoeHNM8fGedS3faIhGt9TtxTVrHCC9wWw',
                'featured' => true,
            ],
            [
                'title' => 'Peta Nusantara Lama',
                'author_name' => 'Bambang Haryo',
                'isbn' => '978-602-03-4567-3',
                'price' => 85000,
                'category' => 'Sejarah & Budaya',
                'genre' => 'Sejarah',
                'pages' => 410,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAf1Ffdow9zU3eHKynhd8sxvFydhox32sBArwdc4AGPmQc0P1xYtMY1Brjsv01VrShqjrzz7BCnQlROnkQUe6vo20N0SVOQabuuf1jn2f1zMp-nRWKJxuNWhfhGI9lJOh4do_jhBiCRpfEMHFR6IsWjb76koyJsUL5_4bLO9g_GP_2IEVXpO2ZBemJxdaPztjZ2PGvbLrkZlqANLD1HJYFpa8U7k19g1mwUykVwBDftRao8UkEB3ByS1A',
                'featured' => true,
            ],
            [
                'title' => 'Malam di Malioboro',
                'author_name' => 'Ratih Kumala',
                'isbn' => '978-602-03-4567-4',
                'price' => 75000,
                'discount_price' => 52500,
                'category' => 'Fiksi',
                'genre' => 'Sastra',
                'pages' => 290,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDe1APk6Vcy2ENic34n4kDPXljWaLgVT6QowCA0Uean2SEwW-wyc7RVAFymRr8uhHMlz5j607-6Zo8C-Noofj3Ox4ZsWBGH3RMJ4ZM01Qkl9pAiA6iBtKQhK0GftHCIDA0zGK1fdrTu1VwcZXodCA5CdJ4V0H1mSHvCGGUuNcwfV7OV5KHOIQDUh8OPr7p_9urooLn4n3x4DmmDu_vguRsb8S-lLT7aepXDBm3AsudZt9XI4wRKoauRwg',
                'featured' => true,
            ],
            [
                'title' => 'Arsitektur Batavia',
                'author_name' => 'Prof. Handoko',
                'isbn' => '978-602-03-4567-5',
                'price' => 120000,
                'category' => 'Sejarah & Budaya',
                'genre' => 'Sejarah',
                'pages' => 380,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAORoMQojqo2ssxkFJsbkPTBN-T55tNU4U7m5BKwr9m8F9YvFCS6CUgy6kzOwduNm8LSLRQ93xKHxM80XHP8wq2i6VTjpLAfpw85H9_5IdQTc307QFr2RO92dvSY2kjBjd0F8PgZIcd8HzIeyuDVywqu8bcRWr--YjBy4M0B44dkZpYLOUsXwUJuAaMyC9qSLt9PVsQcgn6xOAOEL9icKFknTQQ2rdv-Vawunu86UThM-PF3FgqorTHgA',
                'featured' => false,
            ],
            [
                'title' => 'Laut yang Memanggil',
                'author_name' => 'Gibran Mahesa',
                'isbn' => '978-602-03-4567-6',
                'price' => 68000,
                'category' => 'Fiksi',
                'genre' => 'Romansa',
                'pages' => 250,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCH7ZRS5yuVIJkHFPmwrl0uA3Sd31FqWUViFd6br3IHoAjnSY4VYNbkggam_COqfaq-Ot3EZ0KUg4HoFRxjeIBRyPYbhuB2sA3XQYSKtMPCIMDCM0zBdYn2K8prtAA5h7wbVLmTnEc_xojc98gIygiHMZxRH_eNfIh-KWzTArnIHCbIa6TXLowd99SPxi8yheeYjWL2WykyztBFp5tARjXc2cGPqSWXpIocnHPrqWPMDghlSCj1LHj9FQ',
                'featured' => false,
            ],
            [
                'title' => 'Garis Batas Khatulistiwa',
                'author_name' => 'Sinta Nuriyah',
                'isbn' => '978-602-03-4567-7',
                'price' => 92000,
                'category' => 'Pengembangan Diri',
                'genre' => 'Psikologi',
                'pages' => 340,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB6Hm1H-OPtlW0RuYyhY--CUpuzHNaiGdfPl6kcx2L0jbRDrkikfwmrV2WOxquGh0k0-hG6vF3RdG40taMyUKjGwovp5Mjgp9dtBpKYuxcp_lBy1W_IqoDDWDhKizEnnVHdwFNi9kRU842B5lPIxMs5w-X8XcO9mBo-A4TFTdjqXo3_PtMt4fbJpKMwG7cI1OkAUCK9o3RWl2Fxz1yy8GD9H8kb-s0ijzcqJlbgOPiB4_xeNU_jtzQoeA',
                'featured' => false,
            ],
            [
                'title' => 'Renjana Senja',
                'author_name' => 'Damar Sasongko',
                'isbn' => '978-602-03-4567-8',
                'price' => 55000,
                'category' => 'Fiksi',
                'genre' => 'Sastra',
                'pages' => 210,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBZ1Gkg72gZ54PBJ1GvdhWD2yZAEX5eXrmocRckO6xIoCCfkBzlgbI8XL3SYkuJiqvMripKsHTAocej0mxctRnZGA51R6vMeuGhHsQghprQMYAat0J9E12X0I9gg4U7FC_vczIekc4gW2VDzCXK5-q8GDzSNMPux6fhex7LJSJsYeuhy7-6pesHLGtH-1jGQShnARVSW3RMcYSjGoe2g02q-1PC2jcWOst4qCi9wdjNoi0j_YlMScBJA',
                'featured' => false,
            ],
            [
                'title' => 'Filosofi Teras Kota',
                'author_name' => 'Henry Manampiring',
                'isbn' => '978-602-03-4567-9',
                'price' => 75000,
                'discount_price' => 56250,
                'category' => 'Pengembangan Diri',
                'genre' => 'Filosofi',
                'pages' => 310,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBMiDtYEg1btOfPjcyQ2gOJ4WOUx6O0nbgLhwRxMee2vHWfti0aki-tz_01X6rTnU7og3BrIUM0U8sKc6t3YkBB6sezVE7r4OUYh8J9Ev1pueE06EvH6s_VeiVWLiwyuwM7ne557HhWtk_A7o6wvFcYprw9Fs6aIo1tWASmJnSbnE8w_oJoh7QjDoWuMOvIPCTnKS7bJibF2xtTQOreGsQ59MbQRYs-09154ehEOTbcZoETb6UhCtnFGA',
                'featured' => true,
            ],
            [
                'title' => 'Seni Menulis Cepat',
                'author_name' => 'Dewi Lestari',
                'isbn' => '978-602-03-4568-0',
                'price' => 70000,
                'discount_price' => 42000,
                'category' => 'Pengembangan Diri',
                'genre' => 'Sains',
                'pages' => 180,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB-48xjprCZwwNMqHoW53m_te60HPL9KfxckrvYpzRfv7Ezp-NEkixl9RLTy39H2nAJbiH35Di8HufguhG11pF2NiWiFDA3O_dlaDtHykNcn4_TR1GJHccnpk4QMffEPfBXFEe5vrpHrTe_pCMJsfbT_TA9DdtuxUVa2eKT8u7Nr8jo5DXhRWynMyDOa4EZA2I6x7AicZpno8gURlrDM55D8muqcDKmE5ZqCsnOuSMbGOOKBEH8j_XT3Q',
                'featured' => true,
            ],
            [
                'title' => 'Jejak Rempah Nusantara',
                'author_name' => 'Bondan Winarno',
                'isbn' => '978-602-03-4568-1',
                'price' => 90000,
                'discount_price' => 58500,
                'category' => 'Sejarah & Budaya',
                'genre' => 'Sejarah',
                'pages' => 420,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC6qtDG8uXnxrM77lMqSUrH6FvNyCjS8uudy3q4SCABDsDZP9xKLcU1j3nRH6D4X8p_cIvOF9-6scpVIAINzfphsyoxSwujpDfyuMBJN3Vquzh6Mwhg0U76O0BKbEdtiZT610Z3sxGdz5dCctmOtWqdWs7krFJl_J_UfE7gj1tl2KCyaF7ooWZpSQdn-63AiTBUkL-yamD2zChb8jr1J018onkI9MHm7YVtKcgYwgkERdg7C9WX5SQ_RA',
                'featured' => true,
            ],
            [
                'title' => 'Algoritma Jiwa',
                'author_name' => 'Dr. Ilham Habibie',
                'isbn' => '978-602-03-4568-2',
                'price' => 80000,
                'discount_price' => 64000,
                'category' => 'Teknologi & Sains',
                'genre' => 'Teknologi',
                'pages' => 360,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCAKqlF3uUK3hn-fItlqPkc8xe5Jf1ljpYVyQw7ooHSMcZ8ffvB6U0_QG0MoJRkJtrUNV-0KV9Wgo7afk85VKEgocUW9K5yuy-V58Gvub7I97XcNlYoAr2aIJ_meoidnPd65p1DCUeQ_E56l4qW_HHkc7Ar38_8lO2vP9_W25Gf3pvGhdsWt_2UKRqqmwXoqAQoOy7thL4lUjK7ELTXyq3yr2DxGqkNN0UEfRM30eLzS2q9occiIbpT3Q',
                'featured' => true,
            ],
            [
                'title' => 'Senja di Pelabuhan Ratu',
                'author_name' => 'Ahmad Tohari',
                'isbn' => '978-602-03-4568-3',
                'price' => 65000,
                'category' => 'Fiksi',
                'genre' => 'Romansa',
                'pages' => 270,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuD79KJCnayDQxPCCjiAPuUGKTlMPsWmuoasl79lwOTunt8iPHdNc-UXmNyE4c3X1_xYPiSRk703hoV9eZqrR5dfbipI1kP9RKPs1Vn5YFf2qFlqEXA1v2cU2vTk7o64EEvKDGvOgwexPRNd023TL7sIR-pbAM6gr3EID6bHxsRZcu_vMEotQg85ufHW2Vy4Au4eYUGxyD0Hjams3CcWzigNRqOC6_oQbePdPu2F_kkcMD30bTuuO3sobQ',
                'featured' => false,
                'is_free' => true,
            ],
            [
                'title' => 'Memulai Usaha Mandiri',
                'author_name' => 'Rhenald Kasali',
                'isbn' => '978-602-03-4568-4',
                'price' => 85000,
                'category' => 'Bisnis & Karier',
                'genre' => 'Bisnis',
                'pages' => 290,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuATr57CJthfhwQxN_uCk4Whaic-RmkISzO1KoraO3X5Xt-76rffYbDOq3QUGow3ROpJlGFjGgJbOZr6PlFjx0rSKMOyihv5dqWfJA9tWko-nx3-0VC_2H7IvUwuZYuRVmhV1ol1aD2YebfEeoLKb1ASGh0dOgrCY4vFNldQaFkZ63tmWE5mESc-IOTRiANi0gQaSsMoA11ceurzjRfy0hHSdPsSBOkKvIyC06bn_FslgoFT5yH49N_T8Q',
                'featured' => false,
            ],
            [
                'title' => 'Bumi Para Pendekar',
                'author_name' => 'Pramoedya A. T.',
                'isbn' => '978-602-03-4568-5',
                'price' => 92000,
                'category' => 'Fiksi',
                'genre' => 'Historical',
                'pages' => 450,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBrp1aNhrLg99xxJ5vybsMEfjaGYVpP2lFG_6yIDiPYBCTlGyEgoNVZEV5r5n9S_QmFSr9eBHYBJtBMsRTwTYG0Ihf9FFgD9tqi9qPmljx9v4xOHsKiEW_X2QHOreQdRMzVgZCM3M-u3OL8r4HNNV7DaHXDn1UzBhv2oDlUsnrYMad76V92CVKBkhsv3yVO7eVjFtnMahMZSyurrnFAziAYeZ_qhOTVbgef0WAlE_zjYbpWPmSGJdaVw',
                'featured' => false,
            ],
            [
                'title' => 'Belajar Python Praktis',
                'author_name' => 'Sandhika Galih',
                'isbn' => '978-602-03-4568-6',
                'price' => 110000,
                'category' => 'Teknologi & Sains',
                'genre' => 'Teknologi',
                'pages' => 520,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDxy782JnQuUjFFYPrDtP2VPgsFpIrIDRwWfi_1Klj-oYouzklxShc9Z1uDZogNtT4b09KB-dzPiB9h15re_l718hkYAOD-YdVMG-aKkBbZ-1zTqVfcWV-KNc8fHlWOYYkRapth8FoPegvgQ1pTsBAMD6fGPeH4YLi_tyCjZAnR4pXVVAg14bCDNAfnBEmqh9MtsSvJN1SzrwqbTfH4dROsPJW74uyrBOZUgXJMUlCtyxe0Ykt5GZ2j5g',
                'featured' => false,
            ],
            [
                'title' => 'Merawat Akal Sehat',
                'author_name' => 'Rocky Gerung',
                'isbn' => '978-602-03-4568-7',
                'price' => 78000,
                'category' => 'Pengembangan Diri',
                'genre' => 'Filosofi',
                'pages' => 240,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBX8tDqrST18blNRKb-jRuYhgC-TuNwsamRvxXPmRKNvFgDQQvUC27teaEGpoFmlEiI9EbgtlXHtcshhc56AZ0LeeSF1NmU5OCjWFMLLJoFKWWFu0zNDPox44kY4BvhXHfb3hL0r42sh_aOKH0xeD-6hHhY44n3e1j5v6XpzMxNh2eyxVIkhezSRmT73QIFkSOLgKoK7noxusngOfIBZuRdD_Bb8CXldXgfG42hQ15uDHPRxgO_OmAJ5Q',
                'featured' => false,
            ],
            [
                'title' => 'Surat untuk Masa Depan',
                'author_name' => 'Ayu Utami',
                'isbn' => '978-602-03-4568-8',
                'price' => 68000,
                'category' => 'Fiksi',
                'genre' => 'Sastra',
                'pages' => 310,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDPTpbtHdPK86hoKhUUUN0O0LpMbXSZSMA0sB9RZrw6XGoTp0Mm_gqDoZw6ggNdBUza1oPKW2XqfGnkN0SM_dHRsq-ASsLHR2VcasP_jsgmMmtyct4NVLHVc5vBzAoPEgZ8aJHz-rCRjTpy9f-1RfZ3vYeWIeNir61516DwNuCTUu_sORufE7durLfXdylCycKg762XRSpUHfo5sqibiZGvRQXNzhll6K4Iw6RWLZa1jodm-Ky4dImqw',
                'featured' => false,
            ],
            [
                'title' => 'Kisah dari Pelosok',
                'author_name' => 'Agustinus Wibowo',
                'isbn' => '978-602-03-4568-9',
                'price' => 89000,
                'category' => 'Sejarah & Budaya',
                'genre' => 'Biografi',
                'pages' => 380,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuALHc_Rgw8cRlN3F3GtAo1o2a1zXAGV1MFHMeer4RixzCmDy97ajaEK2IyAqTGCFwh7cUkuoaMkFFV4EjNUqp88ivHwuSNOzyWXYeW1DoLtTjdjaov_FhcVaFhwmhZcS1bNmk7nM2Fnko19XhQoiAdgNR5XQI4P3LvpFbzU1dOVLEOL_DTpKQ252UW81sW63U83jm3jYkra7BHtI4buVmnywgM4ggMGQIBv3UEEsQgAeHc8nhVRU3zvWng',
                'featured' => false,
            ],
            [
                'title' => 'Dongeng Kancil Cerdas',
                'author_name' => 'Murti Bunanta',
                'isbn' => '978-602-03-4569-0',
                'price' => 55000,
                'category' => 'Anak',
                'genre' => 'Sastra',
                'pages' => 120,
                'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAWGj250yXriVRIAhD6gv9389ZtOdGs6Y161WaAjREjkLm5FXpD26hGtv0t-3Deo_VqRezUTWx8J2OL4Mfdmioc19QF0eQZPyGxFIPZOKjUx2E66muH9ZALk9tM3j8mmN8YL1wTrphPgy5toR1rZkeZ41fMeivi7l2chUFJDy2crxwRtzVYf6uUzCYuQ7yySgTsdKNommZIBN6baAT0mYHYkkwz4xbn8R_9d4oULTVixo_m-VxDr87Xww',
                'featured' => false,
            ],
        ];

        foreach ($books as $bookData) {
            $category = Category::where('name', $bookData['category'])->first();
            $genre = Genre::where('name', $bookData['genre'])->first();

            $book = Book::create([
                'publisher_id' => $publisher->id,
                'title' => $bookData['title'],
                'slug' => Str::slug($bookData['title']),
                'description' => $bookData['description'] ?? $bookData['title'] . ' — sebuah karya dari ' . $bookData['author_name'] . '.',
                'author_name' => $bookData['author_name'],
                'isbn' => $bookData['isbn'],
                'price' => $bookData['is_free'] ?? false ? 0 : $bookData['price'],
                'discount_price' => $bookData['discount_price'] ?? null,
                'cover_path' => $bookData['cover'] ?? null,
                'pages' => $bookData['pages'] ?? 200,
                'language' => 'id',
                'category_id' => $category?->id,
                'genre_id' => $genre?->id,
                'status' => BookStatus::PUBLISHED,
                'is_featured' => $bookData['featured'] ?? false,
                'published_at' => now()->subDays(rand(1, 90)),
            ]);

            foreach ($users as $user) {
                Rating::create([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'stars' => rand(4, 5),
                ]);
            }
        }
    }
}
