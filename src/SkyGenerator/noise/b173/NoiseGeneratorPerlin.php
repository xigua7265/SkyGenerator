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

class NoiseGeneratorPerlin {
    private $d = [];
    public $a;
    public $b;
    public $c;

    public function __construct(Random $var1 = null) {
        $var1 = $var1 ?? new Random();
        $this->d = array_fill(0, 512, 0);
        $this->a = $var1->nextFloat() * 256.0;
        $this->b = $var1->nextFloat() * 256.0;
        $this->c = $var1->nextFloat() * 256.0;

        for ($var2 = 0; $var2 < 256; $var2++) {
            $this->d[$var2] = $var2;
        }

        for ($var2 = 0; $var2 < 256; ++$var2) {
            $var3 = $var1->nextRange(0, 255 - $var2) + $var2;
            $var4 = $this->d[$var2];
            $this->d[$var2] = $this->d[$var3];
            $this->d[$var3] = $var4;
            $this->d[$var2 + 256] = $this->d[$var2];
        }
    }

    public function a($var1, $var3, $var5 = 0.0) {
        $var7 = $var1 + $this->a;
        $var9 = $var3 + $this->b;
        $var11 = $var5 + $this->c;
        $var13 = (int)$var7;
        $var14 = (int)$var9;
        $var15 = (int)$var11;

        if ($var7 < $var13) --$var13;
        if ($var9 < $var14) --$var14;
        if ($var11 < $var15) --$var15;

        $var16 = $var13 & 255;
        $var17 = $var14 & 255;
        $var18 = $var15 & 255;
        $var7 -= $var13;
        $var9 -= $var14;
        $var11 -= $var15;
        $var19 = $var7 * $var7 * $var7 * ($var7 * ($var7 * 6.0 - 15.0) + 10.0);
        $var21 = $var9 * $var9 * $var9 * ($var9 * ($var9 * 6.0 - 15.0) + 10.0);
        $var23 = $var11 * $var11 * $var11 * ($var11 * ($var11 * 6.0 - 15.0) + 10.0);
        $var25 = $this->d[$var16] + $var17;
        $var26 = $this->d[$var25] + $var18;
        $var27 = $this->d[$var25 + 1] + $var18;
        $var28 = $this->d[$var16 + 1] + $var17;
        $var29 = $this->d[$var28] + $var18;
        $var30 = $this->d[$var28 + 1] + $var18;

        return $this->lerp($var23, 
            $this->lerp($var21, 
                $this->lerp($var19, 
                    $this->grad3($this->d[$var26], $var7, $var9, $var11), 
                    $this->grad3($this->d[$var29], $var7 - 1.0, $var9, $var11)
                ),
                $this->lerp($var19, 
                    $this->grad3($this->d[$var27], $var7, $var9 - 1.0, $var11), 
                    $this->grad3($this->d[$var30], $var7 - 1.0, $var9 - 1.0, $var11)
                )
            ),
            $this->lerp($var21, 
                $this->lerp($var19, 
                    $this->grad3($this->d[$var26 + 1], $var7, $var9, $var11 - 1.0), 
                    $this->grad3($this->d[$var29 + 1], $var7 - 1.0, $var9, $var11 - 1.0)
                ),
                $this->lerp($var19, 
                    $this->grad3($this->d[$var27 + 1], $var7, $var9 - 1.0, $var11 - 1.0), 
                    $this->grad3($this->d[$var30 + 1], $var7 - 1.0, $var9 - 1.0, $var11 - 1.0)
                )
            )
        );
    }

    private function lerp($t, $a, $b) {
        return $a + $t * ($b - $a);
    }

    private function grad($hash, $x, $y) {
        $h = $hash & 15;
        $u = (1 - (($h & 8) >> 3)) * $x;
        $v = $h < 4 ? 0 : (($h != 12 && $h != 14) ? $y : $x);
        return (($h & 1) === 0 ? $u : -$u) + (($h & 2) === 0 ? $v : -$v);
    }

    private function grad3($hash, $x, $y, $z) {
        $h = $hash & 15;
        $u = $h < 8 ? $x : $y;
		$v = ($h < 4) ? $y : (($h != 12 && $h != 14) ? $z : $x);
        return (($h & 1) === 0 ? $u : -$u) + (($h & 2) === 0 ? $v : -$v);
    }

    public function a2($var1, $var3) {
        return $this->a($var1, $var3, 0.0);
    }

	public function fill(&$var1, $var2, $var4, $var6, $var8, $var9, $var10, $var11, $var13, $var15, $var17) {
		if ($var9 == 1) {
			$var33 = 0;
			$var42 = 1.0 / $var17;

			for ($var44 = 0; $var44 < $var8; ++$var44) {
				$var21 = ($var2 + $var44) * $var11 + $this->a;
				$var45 = (int)$var21;
				if ($var21 < $var45) --$var45;
				$var46 = $var45 & 255;
				$var21 -= $var45;
				$var23 = $var21 * $var21 * $var21 * ($var21 * ($var21 * 6.0 - 15.0) + 10.0);

				for ($var27 = 0; $var27 < $var10; ++$var27) {
					$var25 = ($var6 + $var27) * $var15 + $this->c;
					$var30 = (int)$var25;
					if ($var25 < $var30) --$var30;
					$var31 = $var30 & 255;
					$var25 -= $var30;
					$var28 = $var25 * $var25 * $var25 * ($var25 * ($var25 * 6.0 - 15.0) + 10.0);

					$var19 = $this->d[$var46] + 0;
					$var47 = $this->d[$var19] + $var31;
					$var48 = $this->d[$var46 + 1] + 0;
					$var20 = $this->d[$var48] + $var31;

					$var38 = $this->lerp($var23, 
						$this->grad($this->d[$var47], $var21, $var25),
						$this->grad3($this->d[$var20], $var21 - 1.0, 0.0, $var25)
					);
					$var40 = $this->lerp($var23, 
						$this->grad3($this->d[$var47 + 1], $var21, 0.0, $var25 - 1.0),
						$this->grad3($this->d[$var20 + 1], $var21 - 1.0, 0.0, $var25 - 1.0)
					);
					$var49 = $this->lerp($var28, $var38, $var40);
					$var1[$var33++] += $var49 * $var42;
				}
			}
		} else {
			$var19 = 0;
			$var66 = 1.0 / $var17;
			$var20 = -1;
			$var42 = 0.0;
			$var21 = 0.0;
			$var69 = 0.0;
			$var23 = 0.0;

			for ($var27 = 0; $var27 < $var8; ++$var27) {
				$var25 = ($var2 + $var27) * $var11 + $this->a;
				$var30 = (int)$var25;
				if ($var25 < $var30) --$var30;
				$var31 = $var30 & 255;
				$var25 -= $var30;
				$var28 = $var25 * $var25 * $var25 * ($var25 * ($var25 * 6.0 - 15.0) + 10.0);

				for ($var46 = 0; $var46 < $var10; ++$var46) {
					$var70 = ($var6 + $var46) * $var15 + $this->c;
					$var71 = (int)$var70;
					if ($var70 < $var71) --$var71;
					$var50 = $var71 & 255;
					$var70 -= $var71;
					$var51 = $var70 * $var70 * $var70 * ($var70 * ($var70 * 6.0 - 15.0) + 10.0);

					for ($var53 = 0; $var53 < $var9; ++$var53) {
						$var54 = ($var4 + $var53) * $var13 + $this->b;
						$var56 = (int)$var54;
						if ($var54 < $var56) --$var56;
						$var57 = $var56 & 255;
						$var54 -= $var56;
						$var58 = $var54 * $var54 * $var54 * ($var54 * ($var54 * 6.0 - 15.0) + 10.0);

						if ($var53 === 0 || $var57 !== $var20) {
							$var20 = $var57;
							$var60 = $this->d[$var31] + $var57;
							$var61 = $this->d[$var60] + $var50;
							$var62 = $this->d[$var60 + 1] + $var50;
							$var63 = $this->d[$var31 + 1] + $var57;
							$var33 = $this->d[$var63] + $var50;
							$var64 = $this->d[$var63 + 1] + $var50;

							$var42 = $this->lerp($var28, 
								$this->grad3($this->d[$var61], $var25, $var54, $var70), 
								$this->grad3($this->d[$var33], $var25 - 1.0, $var54, $var70)
							);
							$var21 = $this->lerp($var28, 
								$this->grad3($this->d[$var62], $var25, $var54 - 1.0, $var70), 
								$this->grad3($this->d[$var64], $var25 - 1.0, $var54 - 1.0, $var70)
							);
							$var69 = $this->lerp($var28, 
								$this->grad3($this->d[$var61 + 1], $var25, $var54, $var70 - 1.0), 
								$this->grad3($this->d[$var33 + 1], $var25 - 1.0, $var54, $var70 - 1.0)
							);
							$var23 = $this->lerp($var28, 
								$this->grad3($this->d[$var62 + 1], $var25, $var54 - 1.0, $var70 - 1.0), 
								$this->grad3($this->d[$var64 + 1], $var25 - 1.0, $var54 - 1.0, $var70 - 1.0)
							);
						}

						$var72 = $this->lerp($var58, $var42, $var21);
						$var73 = $this->lerp($var58, $var69, $var23);
						$var74 = $this->lerp($var51, $var72, $var73);
						$var1[$var19++] += $var74 * $var66;
					}
				}
			}
		}
	}
}