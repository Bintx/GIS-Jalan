<?php

namespace App\Services;

class NaiveBayesClassifier
{
    // Probabilitas prioritas (Prior Probabilities)
    // Ini adalah probabilitas umum untuk setiap kategori prioritas
    // Tanpa melihat fitur lain.
    private array $priorProbabilities = [
        'tinggi' => 0.3,
        'sedang' => 0.5,
        'rendah' => 0.2,
    ];

    // Probabilitas likelihood (Likelihood Probabilities)
    // Ini adalah P(Fitur | Prioritas) - probabilitas fitur tertentu
    // diberikan kategori prioritas. Data ini hipotesis/contoh.

    private array $likelihoods = [
        'tingkat_kerusakan' => [
            'tinggi' => ['ringan' => 0.1, 'sedang' => 0.3, 'berat' => 0.6],
            'sedang' => ['ringan' => 0.4, 'sedang' => 0.4, 'berat' => 0.2],
            'rendah' => ['ringan' => 0.7, 'sedang' => 0.2, 'berat' => 0.1],
        ],
        'tingkat_lalu_lintas' => [
            'tinggi' => ['rendah' => 0.1, 'sedang' => 0.3, 'tinggi' => 0.6],
            'sedang' => ['rendah' => 0.4, 'sedang' => 0.4, 'tinggi' => 0.2],
            'rendah' => ['rendah' => 0.7, 'sedang' => 0.2, 'tinggi' => 0.1],
        ],
        // Untuk 'panjang_ruas_rusak', kita akan mendiskritisasi nilai numerik
        // dan menggunakan probabilitas ini.
        'panjang_ruas_rusak_category' => [
            'tinggi' => ['pendek' => 0.1, 'menengah' => 0.3, 'panjang' => 0.6],
            'sedang' => ['pendek' => 0.4, 'menengah' => 0.4, 'panjang' => 0.2],
            'rendah' => ['pendek' => 0.7, 'menengah' => 0.2, 'panjang' => 0.1],
        ],
    ];

    /**
     * Mengklasifikasikan prioritas perbaikan jalan berdasarkan fitur-fitur yang diberikan.
     *
     * @param string $tingkatKerusakan ['ringan', 'sedang', 'berat']
     * @param string $tingkatLaluLintas ['rendah', 'sedang', 'tinggi']
     * @param float $panjangRuasRusak Panjang ruas yang rusak dalam meter
     * @return string Prioritas klasifikasi ['tinggi', 'sedang', 'rendah']
     */
    public function classify(string $tingkatKerusakan, string $tingkatLaluLintas, float $panjangRuasRusak): string
    {
        $scores = [];
        $categories = ['tinggi', 'sedang', 'rendah'];

        // Diskritisasi panjang_ruas_rusak
        $panjangRuasRusakCategory = $this->discretizePanjangRuasRusak($panjangRuasRusak);

        foreach ($categories as $category) {
            // Inisialisasi score dengan probabilitas prioritas
            $score = $this->priorProbabilities[$category];

            // Kalikan dengan likelihood untuk tingkat_kerusakan
            if (isset($this->likelihoods['tingkat_kerusakan'][$category][$tingkatKerusakan])) {
                $score *= $this->likelihoods['tingkat_kerusakan'][$category][$tingkatKerusakan];
            } else {
                // Handle kasus jika fitur tidak ada di data pelatihan (misalnya, berikan probabilitas kecil)
                $score *= 0.001; // Smoothing Laplace atau default kecil
            }

            // Kalikan dengan likelihood untuk tingkat_lalu_lintas
            if (isset($this->likelihoods['tingkat_lalu_lintas'][$category][$tingkatLaluLintas])) {
                $score *= $this->likelihoods['tingkat_lalu_lintas'][$category][$tingkatLaluLintas];
            } else {
                $score *= 0.001;
            }

            // Kalikan dengan likelihood untuk panjang_ruas_rusak_category
            if (isset($this->likelihoods['panjang_ruas_rusak_category'][$category][$panjangRuasRusakCategory])) {
                $score *= $this->likelihoods['panjang_ruas_rusak_category'][$category][$panjangRuasRusakCategory];
            } else {
                $score *= 0.001;
            }

            $scores[$category] = $score;
        }

        // Temukan kategori dengan skor tertinggi
        arsort($scores); // Urutkan array dari nilai tertinggi ke terendah
        return key($scores); // Ambil kunci (nama kategori) dari elemen pertama (tertinggi)
    }

    /**
     * Mendiskritisasi nilai panjang_ruas_rusak menjadi kategori.
     *
     * @param float $panjangRuasRusak
     * @return string ['pendek', 'menengah', 'panjang']
     */
    private function discretizePanjangRuasRusak(float $panjangRuasRusak): string
    {
        if ($panjangRuasRusak <= 100) {
            return 'pendek';
        } elseif ($panjangRuasRusak <= 500) { // Antara 100.01 dan 500
            return 'menengah';
        } else { // Lebih dari 500
            return 'panjang';
        }
    }
}
