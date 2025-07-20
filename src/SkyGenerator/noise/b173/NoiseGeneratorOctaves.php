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

class NoiseGeneratorOctaves extends NoiseGenerator {
    private $noiseGenerators;
    private $totalNoiseGenerators;

    public function __construct(Random $var1, $var2) {
        $this->totalNoiseGenerators = $var2;
        $this->noiseGenerators = [];

        for ($var3 = 0; $var3 < $var2; ++$var3) {
            $this->noiseGenerators[$var3] = new NoiseGeneratorPerlin($var1);
        }
    }

    public function generateNoiseForCoordinate($var1, $var3) {
        $var5 = 0.0;
        $var7 = 1.0;

        for ($var9 = 0; $var9 < $this->totalNoiseGenerators; ++$var9) {
            $var5 += $this->noiseGenerators[$var9]->a($var1 * $var7, $var3 * $var7) / $var7;
            $var7 /= 2.0;
        }
        return $var5;
    }

    public function generateNoise($var1, $var2, $var4, $var6, $var8, $var9, $var10, $var11, $var13, $var15) {
        $size = $var8 * $var9 * $var10;
        if ($var1 === null || count($var1) < $size) {
            $var1 = array_fill(0, $size, 0.0);
        } else {
            $var1 = array_slice($var1, 0, $size);
            for ($i = 0; $i < $size; $i++) {
                $var1[$i] = 0.0;
            }
        }

        $var20 = 1.0;

        for ($var19 = 0; $var19 < $this->totalNoiseGenerators; ++$var19) {
            $this->noiseGenerators[$var19]->fill($var1, $var2, $var4, $var6, $var8, $var9, $var10, $var11 * $var20, $var13 * $var20, $var15 * $var20, $var20);
            $var20 /= 2.0;
        }
        return $var1;
    }

    public function generateNoise2($var1, $var2, $var3, $var4, $var5, $var6, $var8, $var10) {
        return $this->generateNoise($var1, $var2, 10.0, $var3, $var4, 1, $var5, $var6, 1.0, $var8);
    }
}