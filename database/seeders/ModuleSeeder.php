<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DATA SEEDER: ABJAD
        $alphabet = range('A', 'Z');
        
        foreach ($alphabet as $char) {
            Module::create([
                'title' => 'Abjad ' . $char,
                'level_type' => 'abjad',
                'target_gesture' => $char,
                'description' => 'Panduan pembelajaran interaktif untuk mendeteksi dan melatih akurasi bentuk isyarat abjad ' . $char . '.',
                'reference_image' => 'module-references/' . strtolower($char) . '.png',
                'is_active' => true,
            ]);
        }

        // 2. DATA SEEDER: KOSA KATA
        $vocabularyData = [
            [
                'title' => 'Kosa Kata: Halo',
                'level_type' => 'kata',
                'target_gesture' => 'HALO',
                'description' => 'Isyarat sapaan standar dengan cara melambaikan telapak tangan terbuka ke arah depan.',
                'reference_image' => 'module-references/kata_halo.png',
            ],
            [
                'title' => 'Kosa Kata: Saya',
                'level_type' => 'kata',
                'target_gesture' => 'SAYA',
                'description' => 'Isyarat menunjuk ke arah dada sendiri menggunakan jari telunjuk atau telapak tangan terbuka.',
                'reference_image' => 'module-references/kata_saya.png',
            ],
            [
                'title' => 'Kosa Kata: Kamu',
                'level_type' => 'kata',
                'target_gesture' => 'KAMU',
                'description' => 'Isyarat mengarahkan jari telunjuk secara langsung ke arah lawan bicara.',
                'reference_image' => 'module-references/kata_kamu.png',
            ],
            [
                'title' => 'Kosa Kata: Makan',
                'level_type' => 'kata',
                'target_gesture' => 'MAKAN',
                'description' => 'Menguncupkan jari-jari tangan dan menggerakkannya mendekati mulut seperti menyuap makanan.',
                'reference_image' => 'module-references/kata_makan.png',
            ],
            [
                'title' => 'Kosa Kata: Minum',
                'level_type' => 'kata',
                'target_gesture' => 'MINUM',
                'description' => 'Memperagakan gestur memegang gelas menggunakan ibu jari dan jemari lalu mengarahkannya ke mulut.',
                'reference_image' => 'module-references/kata_minum.png',
            ],
            [
                'title' => 'Kosa Kata: Belajar',
                'level_type' => 'kata',
                'target_gesture' => 'BELAJAR',
                'description' => 'Membuka kedua telapak tangan berdampingan di depan dada seperti membaca buku terbuka.',
                'reference_image' => 'module-references/kata_belajar.png',
            ],
            [
                'title' => 'Kosa Kata: Guru',
                'level_type' => 'kata',
                'target_gesture' => 'GURU',
                'description' => 'Isyarat meletakkan jari telunjuk dan jari tengah di samping kening, melambangkan profesi pendidik.',
                'reference_image' => 'module-references/kata_guru.png',
            ],
            [
                'title' => 'Kosa Kata: Maaf',
                'level_type' => 'kata',
                'target_gesture' => 'MAAF',
                'description' => 'Mengepalkan tangan kanan dan menggerakkannya secara memutar di depan dada dengan ekspresi menyesal.',
                'reference_image' => 'module-references/kata_maaf.png',
            ],
            [
                'title' => 'Kosa Kata: Tolong',
                'level_type' => 'kata',
                'target_gesture' => 'TOLONG',
                'description' => 'Isyarat menempelkan kedua telapak tangan secara rapat di depan dada menyerupai gestur memohon.',
                'reference_image' => 'module-references/kata_tolong.png',
            ],
            [
                'title' => 'Kosa Kata: Terima Kasih',
                'level_type' => 'kata',
                'target_gesture' => 'TERIMA_KASIH',
                'description' => 'Menggerakkan ujung jari tangan kanan dari area dagu ke arah depan luar menghadap lawan bicara.',
                'reference_image' => 'module-references/kata_terima_kasih.png',
            ],
            [
                'title' => 'Kosa Kata: Orang Tua',
                'level_type' => 'kata',
                'target_gesture' => 'ORANG_TUA',
                'description' => 'Isyarat gabungan yang memperagakan gestur melambangkan ayah dan ibu secara berurutan.',
                'reference_image' => 'module-references/kata_orang_tua.png',
            ],
            [
                'title' => 'Kosa Kata: Sama-sama',
                'level_type' => 'kata',
                'target_gesture' => 'SAMA_SAMA',
                'description' => 'Isyarat balasan terima kasih dengan menggerakkan kedua telapak tangan sejajar secara horizontal.',
                'reference_image' => 'module-references/kata_sama_sama.png',
            ],
        ];

        foreach ($vocabularyData as $kata) {
            Module::create([
                'title' => $kata['title'],
                'level_type' => $kata['level_type'],
                'target_gesture' => $kata['target_gesture'],
                'description' => $kata['description'],
                'reference_image' => $kata['reference_image'],
                'is_active' => true,
            ]);
        }

        // 3. DATA SEEDER: KALIMAT
        $sentenceData = [
            [
                'title' => 'Kalimat: Saya mau belajar',
                'level_type' => 'kalimat',
                'target_gesture' => 'SAYA_MAU_BELAJAR',
                'description' => 'Rangkaian gestur kontinu yang diawali penunjukan diri sendiri, keinginan, dan membaca buku.',
                'reference_image' => 'module-references/kalimat_saya_mau_belajar.png',
            ],
            [
                'title' => 'Kalimat: Terima kasih guru',
                'level_type' => 'kalimat',
                'target_gesture' => 'TERIMA_KASIH_GURU',
                'description' => 'Kombinasi isyarat ungkapan terima kasih dari dagu dilanjutkan dengan gestur profesi guru.',
                'reference_image' => 'module-references/kalimat_terima_kasih_guru.png',
            ],
            [
                'title' => 'Kalimat: Saya sayang orang tua',
                'level_type' => 'kalimat',
                'target_gesture' => 'SAYA_SAYANG_ORANG_TUA',
                'description' => 'Transisi gerakan dari menunjuk diri, menyilangkan tangan di dada, dan gestur orang tua.',
                'reference_image' => 'module-references/kalimat_saya_sayang_orang_tua.png',
            ],
            [
                'title' => 'Kalimat: Apa kabar kamu',
                'level_type' => 'kalimat',
                'target_gesture' => 'APA_KABAR_KAMU',
                'description' => 'Isyarat interaktif berupa pertanyaan pembuka kabar diikuti dengan penunjukan lawan bicara.',
                'reference_image' => 'module-references/kalimat_apa_kabar_kamu.png',
            ],
            [
                'title' => 'Kalimat: Saya mau makan',
                'level_type' => 'kalimat',
                'target_gesture' => 'SAYA_MAU_MAKAN',
                'description' => 'Gerakan menyambung antara identitas diri, gestur hendak sesuatu, dan isyarat menyuap ke mulut.',
                'reference_image' => 'module-references/kalimat_saya_mau_makan.png',
            ],
            [
                'title' => 'Kalimat: Saya mohon maaf',
                'level_type' => 'kalimat',
                'target_gesture' => 'SAYA_MOHON_MAAF',
                'description' => 'Gestur formal gabungan dari penunjukan diri, permohonan tolong, dan memutar telapak tangan di dada.',
                'reference_image' => 'module-references/kalimat_saya_mohon_maaf.png',
            ],
            [
                'title' => 'Kalimat: Tolong bantu saya',
                'level_type' => 'kalimat',
                'target_gesture' => 'TOLONG_BANTU_SAYA',
                'description' => 'Isyarat meminta pertolongan yang diakhiri dengan penunjukan kembali ke arah diri sendiri.',
                'reference_image' => 'module-references/kalimat_tolong_bantu_saya.png',
            ],
            [
                'title' => 'Kalimat: Kamu mau minum',
                'level_type' => 'kalimat',
                'target_gesture' => 'KAMU_MAU_MINUM',
                'description' => 'Kalimat tanya untuk menawarkan minuman, memperagakan penunjukan lawan bicara dan memegang gelas.',
                'reference_image' => 'module-references/kalimat_kamu_mau_minum.png',
            ],
            [
                'title' => 'Kalimat: Guru membantu saya',
                'level_type' => 'kalimat',
                'target_gesture' => 'GURU_MEMBANTU_SAYA',
                'description' => 'Struktur kalimat lengkap yang memperagakan isyarat guru, tindakan bantuan, dan target objek diri sendiri.',
                'reference_image' => 'module-references/kalimat_guru_membantu_saya.png',
            ],
            [
                'title' => 'Kalimat: Halo selamat pagi',
                'level_type' => 'kalimat',
                'target_gesture' => 'HALO_SELAMAT_PAGI',
                'description' => 'Sapaan pembuka di pagi hari, mengombinasikan lambaian tangan dan isyarat waktu matahari terbit.',
                'reference_image' => 'module-references/kalimat_halo_selamat_pagi.png',
            ],
            [
                'title' => 'Kalimat: Belajar kosa kata',
                'level_type' => 'kalimat',
                'target_gesture' => 'BELAJAR_KOSA_KATA',
                'description' => 'Isyarat tingkat lanjut untuk menguji kemampuan merangkai pembentukan kata bahasa isyarat dinamis.',
                'reference_image' => 'module-references/kalimat_belajar_kosa_kata.png',
            ],
        ];

        foreach ($sentenceData as $kalimat) {
            Module::create([
                'title' => $kalimat['title'],
                'level_type' => $kalimat['level_type'],
                'target_gesture' => $kalimat['target_gesture'],
                'description' => $kalimat['description'],
                'reference_image' => $kalimat['reference_image'],
                'is_active' => true,
            ]);
        }
    }
}