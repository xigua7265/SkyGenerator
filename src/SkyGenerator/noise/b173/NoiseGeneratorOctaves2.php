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

class NoiseGeneratorOctaves2 {
    private $a;
    private $b;

    public function __construct(Random $random, $i) {
        $this->b = $i;
        $this->a = [];

        for ($j = 0; $j < $i; ++$j) {
            $this->a[$j] = new NoiseGenerator2($random);
        }
    }

    public function generateNoise($adouble, $d0, $d1, $i, $j, $d2, $d3, $d4) {
        return $this->a($adouble, $d0, $d1, $i, $j, $d2, $d3, $d4, 0.5);
    }

    public function a($adouble, $d0, $d1, $i, $j, $d2, $d3, $d4, $d5) {
        $scaledD2 = $d2 / 1.5;
        $scaledD3 = $d3 / 1.5;
        
        if ($adouble !== null && count($adouble) >= $i * $j) {
            for ($k = 0; $k < count($adouble); ++$k) {
                $adouble[$k] = 0.0;
            }
        } else {
            $adouble = array_fill(0, $i * $j, 0.0);
        }

        $d6 = 1.0;
        $d7 = 1.0;

        for ($l = 0; $l < $this->b; ++$l) {
            $this->a[$l]->generateNoise($adouble, $d0, $d1, $i, $j, $scaledD2 * $d7, $scaledD3 * $d7, 0.55 / $d6);
            $d7 *= $d4;
            $d6 *= $d5;
        }
        return $adouble;
    }
}