<?php

if (!function_exists('findPairsGreaterThan')) {
    function findPairsGreaterThan(array $numbers, $target) {
        $result = [];
        $length = count($numbers);

        for ($i = 0; $i < $length; $i++) {
            for ($j = $i + 1; $j < $length; $j++) {
                if ($numbers[$i] + $numbers[$j] > $target) {
                    $result[] = [$numbers[$i], $numbers[$j]];
                }
            }
        }

        return $result;
    }
}