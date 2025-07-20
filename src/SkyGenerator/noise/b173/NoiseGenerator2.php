<?php

/*
   ______        _____                      __          
  / __/ /____ __/ ___/__ ___  ___ _______ _/ /____  ____
 _\ \/  '_/ // / (_ / -_) _ \/ -_) __/ _ `/ __/ _ \/ __/
/___/_/\_\\_, /\___/\__/_//_/\__/_/  \_,_/\__/\___/_/   
         /___/                                         
        
 * Copyright (c) 2025 xigua7265 (xigua)
 * 
 * 基于 MIT 协议授权：
 * - 允许自由使用、复制、修改、合并、发布、分发
 * - 唯一要求：保留此版权声明和协议文本
 *
 * @link https://github.com/xigua7265/SkyGenerator
 *
 */

namespace SkyGenerator\noise\b173;

use pocketmine\utils\Random;

class NoiseGenerator2 {
    private static $d = [
        [1, 1, 0], [-1, 1, 0], [1, -1, 0], [-1, -1, 0],
        [1, 0, 1], [-1, 0, 1], [1, 0, -1], [-1, 0, -1],
        [0, 1, 1], [0, -1, 1], [0, 1, -1], [0, -1, -1]
    ];
    private $e = [];
    public $a;
    public $b;
    public $c;

    public function __construct(Random $random = null) {
        $random = $random ?? new Random();
        $this->e = array_fill(0, 512, 0);
        $this->a = $random->nextFloat() * 256.0;
        $this->b = $random->nextFloat() * 256.0;
        $this->c = $random->nextFloat() * 256.0;

        for ($i = 0; $i < 256; $i++) {
            $this->e[$i] = $i;
        }

        for ($i = 0; $i < 256; ++$i) {
            $j = $random->nextRange($i, 255);
            $k = $this->e[$i];
            $this->e[$i] = $this->e[$j];
            $this->e[$j] = $k;
            $this->e[$i + 256] = $this->e[$i];
        }
    }

    private static function a($d0) {
        return $d0 > 0.0 ? (int)$d0 : (int)$d0 - 1;
    }

    private static function dot($grad, $x, $y) {
        return $grad[0] * $x + $grad[1] * $y;
    }

    public function generateNoise(&$adouble, $d0, $d1, $i, $j, $d2, $d3, $d4) {
        $k = 0;
        $f = 0.5 * (sqrt(3.0) - 1.0);
        $g = (3.0 - sqrt(3.0)) / 6.0;

        for ($l = 0; $l < $i; ++$l) {
            $d5 = ($d0 + $l) * $d2 + $this->a;

            for ($i1 = 0; $i1 < $j; ++$i1) {
                $d6 = ($d1 + $i1) * $d3 + $this->b;
                $d7 = ($d5 + $d6) * $f;
                $j1 = self::a($d5 + $d7);
                $k1 = self::a($d6 + $d7);
                $d8 = ($j1 + $k1) * $g;
                $d9 = $j1 - $d8;
                $d10 = $k1 - $d8;
                $d11 = $d5 - $d9;
                $d12 = $d6 - $d10;

                if ($d11 > $d12) {
                    $b0 = 1;
                    $b1 = 0;
                } else {
                    $b0 = 0;
                    $b1 = 1;
                }

                $d13 = $d11 - $b0 + $g;
                $d14 = $d12 - $b1 + $g;
                $d15 = $d11 - 1.0 + 2.0 * $g;
                $d16 = $d12 - 1.0 + 2.0 * $g;
                $l1 = $j1 & 255;
                $i2 = $k1 & 255;
                $j2 = $this->e[$l1 + $this->e[$i2]] % 12;
                $k2 = $this->e[$l1 + $b0 + $this->e[$i2 + $b1]] % 12;
                $l2 = $this->e[$l1 + 1 + $this->e[$i2 + 1]] % 12;

                $d17 = 0.5 - $d11 * $d11 - $d12 * $d12;
                $d18 = $d17 < 0.0 ? 0.0 : ($d17 *= $d17) * $d17 * self::dot(self::$d[$j2], $d11, $d12);

                $d19 = 0.5 - $d13 * $d13 - $d14 * $d14;
                $d20 = $d19 < 0.0 ? 0.0 : ($d19 *= $d19) * $d19 * self::dot(self::$d[$k2], $d13, $d14);

                $d21 = 0.5 - $d15 * $d15 - $d16 * $d16;
                $d22 = $d21 < 0.0 ? 0.0 : ($d21 *= $d21) * $d21 * self::dot(self::$d[$l2], $d15, $d16);

                $adouble[$k++] += 70.0 * ($d18 + $d20 + $d22) * $d4;
            }
        }
    }
}