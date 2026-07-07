<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quest;

class QuestSeeder extends Seeder
{
    public function run(): void
    {
        $quests = [
            // ==========================================
            // KATEGORI 1: LEVEL ABJAD (Reward: 50 Stars)
            // ==========================================
            [
                'title' => 'Pemanasan Isyarat A',
                'description' => 'Tunjukkan gestur huruf A dengan akurasi tinggi dan stabil di depan kamera.',
                'target_gesture' => 'A',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Tantangan Konsistensi B',
                'description' => 'Tahan gestur huruf B secara statis selama beberapa detik tanpa ragu.',
                'target_gesture' => 'B',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Fokus Bentuk Jari C',
                'description' => 'Bentuk jari menyerupai huruf C dengan lengkungan yang sempurna.',
                'target_gesture' => 'C',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Akurasi Telunjuk D',
                'description' => 'Posisikan telunjuk lurus ke atas untuk membentuk gestur huruf D yang presisi.',
                'target_gesture' => 'D',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Latihan Kelenturan E',
                'description' => 'Rapatkan seluruh ujung jari ke telapak tangan untuk membentuk huruf E.',
                'target_gesture' => 'E',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Gestur Terbuka F',
                'description' => 'Pertemukan ujung telunjuk dan jempol, biarkan tiga jari lainnya terbuka (Huruf F).',
                'target_gesture' => 'F',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Posisi Horizontal G',
                'description' => 'Tunjukkan gestur huruf G dengan posisi telunjuk dan jempol sejajar horizontal.',
                'target_gesture' => 'G',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Gestur Dua Jari H',
                'description' => 'Luruskan telunjuk dan jari tengah secara horizontal untuk huruf H.',
                'target_gesture' => 'H',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Isyarat Kelingking I',
                'description' => 'Tegakkan hanya jari kelingking Anda untuk membentuk huruf I yang jelas.',
                'target_gesture' => 'I',
                'reward_stars' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Gerakan Melengkung J',
                'description' => 'Mulai dari huruf I, lalu buat gerakan melengkung seperti kail pancing untuk huruf J.',
                'target_gesture' => 'J',
                'reward_stars' => 75, // Sedikit lebih tinggi karena butuh gerakan
                'is_active' => true,
            ],

            // ===============================================
            // KATEGORI 2: LEVEL KOSA KATA (Reward: 100 Stars)
            // ===============================================
            [
                'title' => 'Sapaan Ramah',
                'description' => 'Misi spesial: Bentuk isyarat sapaan "Halo" secara cepat dan natural.',
                'target_gesture' => 'Halo',
                'reward_stars' => 100,
                'is_active' => true,
            ],
            [
                'title' => 'Ungkapan Syukur',
                'description' => 'Peragakan isyarat "Terima Kasih" dengan gerakan yang halus dan stabil.',
                'target_gesture' => 'Terima Kasih',
                'reward_stars' => 100,
                'is_active' => true,
            ],
            [
                'title' => 'Permintaan Maaf Dasar',
                'description' => 'Tunjukkan rasa empati dengan mempraktikkan gestur "Maaf".',
                'target_gesture' => 'Maaf',
                'reward_stars' => 100,
                'is_active' => true,
            ],
            [
                'title' => 'Identitas Diri',
                'description' => 'Tunjuk diri sendiri dengan tepat menggunakan gestur "Saya".',
                'target_gesture' => 'Saya',
                'reward_stars' => 100,
                'is_active' => true,
            ],
            [
                'title' => 'Menyapa Lawan Bicara',
                'description' => 'Gunakan gestur "Kamu" untuk menunjuk lawan bicara dengan sopan.',
                'target_gesture' => 'Kamu',
                'reward_stars' => 100,
                'is_active' => true,
            ],
            [
                'title' => 'Kata Tanya Dasar',
                'description' => 'Latih ekspresi dan gestur tangan untuk bertanya "Apa".',
                'target_gesture' => 'Apa',
                'reward_stars' => 125,
                'is_active' => true,
            ],
            [
                'title' => 'Keterangan Tempat',
                'description' => 'Tunjukkan lokasi keberadaan dengan isyarat "Di Sini".',
                'target_gesture' => 'Di Sini',
                'reward_stars' => 125,
                'is_active' => true,
            ],
            [
                'title' => 'Afirmasi & Persetujuan',
                'description' => 'Beri persetujuan tegas menggunakan isyarat "Ya".',
                'target_gesture' => 'Ya',
                'reward_stars' => 100,
                'is_active' => true,
            ],

            // ==================================================
            // KATEGORI 3: LEVEL KALIMAT (Reward: 200-250 Stars)
            // ==================================================
            [
                'title' => 'Fokus Transisi: Perkenalan',
                'description' => 'Rangkai gestur tanpa ragu untuk membentuk kalimat "Halo Saya Bagus".',
                'target_gesture' => 'Halo Saya Bagus',
                'reward_stars' => 250,
                'is_active' => true,
            ],
            [
                'title' => 'Menanyakan Kabar',
                'description' => 'Gunakan kombinasi gestur dan ekspresi wajah untuk kalimat "Apa Kabar".',
                'target_gesture' => 'Apa Kabar',
                'reward_stars' => 200,
                'is_active' => true,
            ],
            [
                'title' => 'Ucapan Selamat Pagi',
                'description' => 'Awali hari dengan merangkai isyarat "Selamat" dan "Pagi" secara berurutan.',
                'target_gesture' => 'Selamat Pagi',
                'reward_stars' => 200,
                'is_active' => true,
            ],
            [
                'title' => 'Permintaan Tolong',
                'description' => 'Latih rangkaian kalimat empati "Tolong Bantu Saya" dengan transisi yang tepat.',
                'target_gesture' => 'Tolong Bantu Saya',
                'reward_stars' => 250,
                'is_active' => true,
            ],
            [
                'title' => 'Ungkapan Senang',
                'description' => 'Selesaikan tantangan tingkat tinggi: "Saya Senang Bertemu Kamu".',
                'target_gesture' => 'Saya Senang Bertemu Kamu',
                'reward_stars' => 300, // Reward tertinggi untuk kalimat panjang
                'is_active' => true,
            ],
        ];

        foreach ($quests as $quest) {
            Quest::create($quest);
        }
    }
}