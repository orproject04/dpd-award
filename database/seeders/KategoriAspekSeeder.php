<?php

namespace Database\Seeders;

use App\Models\KategoriAspek;
use Illuminate\Database\Seeder;

class KategoriAspekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (KategoriAspek::count() > 0) {
            return;
        }

        $data = [
            // 1. Bidang Seni dan Budaya
            [
                'kategori' => 'Bidang Seni dan Budaya',
                'aspek' => 'KEASLIAN DAN NILAI ESTETIKA',
                'dimensi' => "1. Menjaga keautentikan akar tradisi, pakem, filosofi dan kearifan lokal khas daerah;\n2. Memiliki standar mutu artistik/estetika yang tinggi dan penguasaan teknik berkarya yang matang;\n3. Keteguhan dalam menjaga nilai-nilai etika, keluhuran, budi pekerti dan kekayaan warisan budaya daerah.",
                'bobot' => 30,
            ],
            [
                'kategori' => 'Bidang Seni dan Budaya',
                'aspek' => 'DAMPAK DAN PEMBERDAYAAN SOSIAL',
                'dimensi' => "1. Memberikan Kontribusi nyata dalam edukasi nilai moral, kohesi sosial dan penguatan karakter/identitas daerah;\n2. Menggerakan ekosistem lokal (Pemberdayaan seniman lokal, sanggar, UMKM Berbasis daerah/Ekraf Daerah;\n3. Menjadi figur/program teladan yang menginspirasi harmoni dan kebanggaan masyarakat terhadap budaya daerah.",
                'bobot' => 25,
            ],
            [
                'kategori' => 'Bidang Seni dan Budaya',
                'aspek' => 'PARTISIPASI PUBLIK DAN REGENERASI',
                'dimensi' => "1. Keaktifan dalam membina, mendidik, dan mentransfer keahlian/pengetahuan seni budaya kepada generasi muda;\n2. Mampu membangun ruang partisipasi publik, komunitas, sanggar, atau jejaring penggiat budaya secara inklusif;\n3. Memiliki program atau mekanisme keberlanjutan untuk menjamin kelangsungan seni-budaya daerah di masa depan.",
                'bobot' => 20,
            ],
            [
                'kategori' => 'Bidang Seni dan Budaya',
                'aspek' => 'INOVASI',
                'dimensi' => "1. Menghadirkan ide/terobosan baru dalam penciptaan, pengemasan, dan medium penyajian karya seni-budaya khas daerah;\n2. Mampu megadaptasi unsur tradisional dengan tren/teknologi modern/digital tanpa menghilangkan esensi dan identitas aslinya;\n3. Menunjukkan daya saing dan relevansi karya terhadap perkembangan zaman dan minat publik lintas generasi.",
                'bobot' => 15,
            ],
            [
                'kategori' => 'Bidang Seni dan Budaya',
                'aspek' => 'REKAM JEJAK/PRESTASI',
                'dimensi' => "1. Rekam jejak konsistensi dedikasi dan pengabdian dalam pemajuan kebudayaan daerah (portofolio);\n2. Penghargaan, sertifikasi, atau apresiasi resmi yang diraih pada tingkat lokal/daerah, nasional hingga internasional;\n3. Pengakuan dari masyarakat, asosiasi profesi/budaya, akademisi maupun pemerintah atas kontribusi representasi daerah.",
                'bobot' => 10,
            ],

            // 2. Bidang Ketahanan Pangan
            [
                'kategori' => 'Bidang Ketahanan Pangan',
                'aspek' => 'DAMPAK KETAHANAN PANGAN',
                'dimensi' => 'Peran Individu dalam peningkatan ketersediaan pangan dan memberikan dampak pada masyarakat sekitar',
                'bobot' => 30,
            ],
            [
                'kategori' => 'Bidang Ketahanan Pangan',
                'aspek' => 'KEBERLANJUTAN DAN KONSISTENSI',
                'dimensi' => 'Kemampuan Individu secara konsisten dan berkelanjutan atas program kemandirian pangan',
                'bobot' => 25,
            ],
            [
                'kategori' => 'Bidang Ketahanan Pangan',
                'aspek' => 'KEPEMIMPINAN PENGGERAK DESA PANGAN',
                'dimensi' => 'Kemampuan individu dalam menginisiasi atau menggerakkan dan menjadi teladan bagi masyarakat dalam mewujudkan desa mandiri pangan',
                'bobot' => 20,
            ],
            [
                'kategori' => 'Bidang Ketahanan Pangan',
                'aspek' => 'INOVASI',
                'dimensi' => 'Kemampuan Individu dalam menciptakan Inovasi atau Kreativitas Program Ketahanan Pangan Mandiri',
                'bobot' => 10,
            ],
            [
                'kategori' => 'Bidang Ketahanan Pangan',
                'aspek' => 'PARTISIPASI PUBLIK',
                'dimensi' => 'Peran dan dukungan baik dari masyarakat maupun pemerintah terhadap inovasi atau Kreativitas yang telah dilakukan sebagai salah satu daerah mandiri pangan',
                'bobot' => 10,
            ],
            [
                'kategori' => 'Bidang Ketahanan Pangan',
                'aspek' => 'PRESTASI/REKAM JEJAK',
                'dimensi' => 'Kemampuan Individu dalam mendapatkan penghargaan, pengakuan, dan kontribusi nyata yang diraih dalam bidang ketahanan pangan',
                'bobot' => 5,
            ],

            // 3. Bidang Kesehatan
            [
                'kategori' => 'Bidang Kesehatan',
                'aspek' => 'INOVASI',
                'dimensi' => "Tingkat keterbaruan, terobosan atau kreatifitas atau modifikasi kreatif baik berupa alat kesehatan maupun pelayanan kesehatan\n1. Kemampuan dari inovasi tersebut dapat menyelesaikan permasalahan dibidang kesehatan, menurunkan angka penyakit atau memperbaiki kesehatan secara nyata\n2. Kemampuan membuat proses pelayanan lebih cepat, hemat biaya dan tidak membuang sumber daya\n3. Meningkatkan kualitas penanganan bidang kesehatan secara aman",
                'bobot' => 30,
            ],
            [
                'kategori' => 'Bidang Kesehatan',
                'aspek' => 'TINGKAT KEMANFAATAN, KEAMANAN DAN KEPATUHAN',
                'dimensi' => "Dampak positif dan manfaat yang dirasakan langsung oleh pengguna alat/pelayanan kesehatan\n1. Peningkatan terhadap layanan kesehatan dan minimalis resiko serta efek samping\n2. Ketepatan sasaran dan efisiensi waktu\n3. Kepatuhan terhadap SOP kesehatan",
                'bobot' => 25,
            ],
            [
                'kategori' => 'Bidang Kesehatan',
                'aspek' => 'AKSESSIBILITAS DAN KETERJANGKAUAN',
                'dimensi' => "Kemudahan dalam aksesibilitas dan keterjangkauan\n1. Inovasi yang dihasilkan mampu meningkatkan kinerja, kecepatan dan kualitas kesehatan\n2. Kemudahan Penggunaan/ Kemudahan Pemahaman/ Kemudahan Operasional\n3. Kesesuaian dengan Kondisi Lokal",
                'bobot' => 20,
            ],
            [
                'kategori' => 'Bidang Kesehatan',
                'aspek' => 'KEBERLANJUTAN/KONSISTENSI',
                'dimensi' => "Program dapat terus dilanjutkan dan dikembangkan secara berkesinambungan, mendapatkan dukungan dan partisipasi publik\n1. Hasil dari Inovasi dalam bidang kesehatan dapat terus berjalan, berkembang dan diimplementasikan\n2. Mampu berkolaborasi dengan instansi Pemerintah/Swasta",
                'bobot' => 15,
            ],
            [
                'kategori' => 'Bidang Kesehatan',
                'aspek' => 'REKAM JEJAK/PRESTASI',
                'dimensi' => "1. Penghargaan/pengakuan atas hasil karya yang pernah diperoleh\n2. Memiliki hak paten atau lisensi lainnya yang relevan dengan kesehatan",
                'bobot' => 10,
            ],

            // 4. Bidang Pendidikan
            [
                'kategori' => 'Bidang Pendidikan',
                'aspek' => 'INOVASI',
                'dimensi' => 'Terobosan, kreativitas, dan adaptabilitas dalam menciptakan atau menjalankan program pendidikan non-formal yang memberikan solusi baru bagi permasalahan di lingkungan sekitar.',
                'bobot' => 25,
            ],
            [
                'kategori' => 'Bidang Pendidikan',
                'aspek' => 'RELEVANSI & KETEPATAN SASARAN',
                'dimensi' => 'Mengukur analisis kebutuhan belajar masyarakat, kesesuaian tujuan pembelajaran dan ketepatan target sasaran',
                'bobot' => 20,
            ],
            [
                'kategori' => 'Bidang Pendidikan',
                'aspek' => 'SARANA PRASARANA DAN KETERSEDIAAN TENAGA PENDIDIK',
                'dimensi' => 'Ketersediaan sarana, prasarana dan tenaga pendidik yang dimiliki untuk mendukung terselenggaranya Program Pendidikan Luar Sekolah',
                'bobot' => 20,
            ],
            [
                'kategori' => 'Bidang Pendidikan',
                'aspek' => 'PROGRAM DAN PENGEMBANGAN KURIKULUM PENDIDIKAN',
                'dimensi' => 'Memiliki Program dan Kurikulum pendidikan dan Latihan untuk mendukung terelenggaranya pendidikan sesuai dengan visi misi yang dibangun',
                'bobot' => 15,
            ],
            [
                'kategori' => 'Bidang Pendidikan',
                'aspek' => 'KOLABORASI & PARTISIPASI PUBLIK',
                'dimensi' => 'Peran dan dukungan masyarakat sekitar terhadap keberadaan kelompok belajar tersebut, termasuk didalamnya antusias peserta didik',
                'bobot' => 10,
            ],
            [
                'kategori' => 'Bidang Pendidikan',
                'aspek' => 'REKAM JEJAK & DAMPAK (IMPACT)',
                'dimensi' => 'Konsistensi penyelenggaraan program pendidikan non formal sejak didirikan hingga saat ini',
                'bobot' => 10,
            ],
        ];

        foreach ($data as $item) {
            KategoriAspek::create($item);
        }
    }
}
