<?php

namespace App\Services;

class MooraService
{
    public function run($products, $criteria)
    {
        // Ambil field, bobot, dan tipe
        $kriteriaFields = $criteria->pluck('column_name')->toArray();
        $bobot          = $criteria->pluck('weight')->toArray();
        $tipe           = $criteria->pluck('type')->toArray();

        // -------------------------
        // 1. Bentuk Matriks Awal
        // -------------------------
        $matrix = [];
        foreach ($products as $product) {
            $row = [];
            foreach ($kriteriaFields as $f) {
                $nilai = floatval($product->$f ?? 0);
                $row[] = $nilai;
            }
            $matrix[] = $row;
        }

        // -------------------------
        // 2. Normalisasi
        // -------------------------
        $normal = [];

        foreach ($kriteriaFields as $index => $field) {

            $den = 0;

            // Hitung penyebut (denominator)
            foreach ($matrix as $row) {
                $v = floatval($row[$index]);

                if ($tipe[$index] === 'cost') {
                    $safe = max($v, 0.00001);
                    $den += pow(1 / $safe, 2);
                } else {
                    $den += pow($v, 2);
                }
            }

            $den = sqrt($den);

            // Normalisasi
            foreach ($matrix as $i => $row) {
                $v = floatval($row[$index]);

                if ($tipe[$index] === 'cost') {
                    $safe = max($v, 0.00001);
                    $normal[$i][$index] = (1 / $safe) / $den;
                } else {
                    $normal[$i][$index] = $den == 0 ? 0 : ($v / $den);
                }
            }
        }

        // -------------------------
        // 3. Hitung Nilai Yi
        // -------------------------
        $scores = [];

        foreach ($normal as $i => $row) {
            $benefit = 0;
            $cost = 0;

            foreach ($row as $j => $v) {
                if ($tipe[$j] === 'benefit') {
                    $benefit += $v * $bobot[$j];
                } else {
                    $cost += $v * $bobot[$j];
                }
            }

            $scores[$i] = $benefit - $cost;
        }

        // -------------------------
        // 4. Ranking
        // -------------------------
        $ranked = [];

        foreach ($scores as $i => $s) {
            $ranked[] = [
                'product' => $products[$i],
                'score'   => $s
            ];
        }

        usort($ranked, fn($a, $b) => $b['score'] <=> $a['score']);

        return $ranked;
    }
}
