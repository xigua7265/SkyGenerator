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

namespace SkyGenerator\populator;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class Lakes{

    private $liquidId;

    public function __construct(int $liquidId) {
        $this->liquidId = $liquidId;
    }

    public function generate($level, Random $random, int $centerX, int $centerY, int $centerZ): bool {
        $centerX -= 8;
        $centerZ -= 8;

        while ($centerY > 0 && $level->getBlockIdAt($centerX, $centerY, $centerZ) === Block::AIR) {
            $centerY--;
        }

        $centerY -= 4;

        $lakeMask = array_fill(0, 2048, false);

        for ($i = 0; $i < $random->nextBoundedInt(4) + 4; $i++) {
            $xRadius = $random->nextBoundedInt(6) + 3;
            $yRadius = $random->nextBoundedInt(4) + 2;
            $zRadius = $random->nextBoundedInt(6) + 3;

            $xRange = 16 - $xRadius - 2;
            $yRange = 8 - $yRadius - 4;
            $zRange = 16 - $zRadius - 2;
            
            if ($xRange <= 0 || $yRange <= 0 || $zRange <= 0) {
                continue;
            }

            $xOffset = $random->nextBoundedInt($xRange) + 1 + (int)($xRadius / 2);
            $yOffset = $random->nextBoundedInt($yRange) + 2 + (int)($yRadius / 2);
            $zOffset = $random->nextBoundedInt($zRange) + 1 + (int)($zRadius / 2);

            for ($dx = 1; $dx < 15; $dx++) {
                for ($dz = 1; $dz < 15; $dz++) {
                    for ($dy = 1; $dy < 7; $dy++) {
                        $normalizedX = (($dx - $xOffset) / ($xRadius / 2)) ** 2;
                        $normalizedY = (($dy - $yOffset) / ($yRadius / 2)) ** 2;
                        $normalizedZ = (($dz - $zOffset) / ($zRadius / 2)) ** 2;

                        if ($normalizedX + $normalizedY + $normalizedZ <= 1) {
                            $lakeMask[($dx * 16 + $dz) * 8 + $dy] = true;
                        }
                    }
                }
            }
        }

        for ($dx = 0; $dx < 16; $dx++) {
            for ($dz = 0; $dz < 16; $dz++) {
                for ($dy = 0; $dy < 8; $dy++) {
                    if (!$lakeMask[($dx * 16 + $dz) * 8 + $dy]) {
                        continue;
                    }

                    $blockX = $centerX + $dx;
                    $blockY = $centerY + $dy;
                    $blockZ = $centerZ + $dz;

                    if ($dy >= 4 && $level->getBlockIdAt($blockX, $blockY, $blockZ) === $this->liquidId) {
                        return false;
                    }

                    if ($dy < 4 && !$this->isBuildable($level->getBlockIdAt($blockX, $blockY, $blockZ)) && $level->getBlockIdAt($blockX, $blockY, $blockZ) !== $this->liquidId) {
                        return false;
                    }
                }
            }
        }

        for ($dx = 0; $dx < 16; $dx++) {
            for ($dz = 0; $dz < 16; $dz++) {
                for ($dy = 0; $dy < 8; $dy++) {
                    if ($lakeMask[($dx * 16 + $dz) * 8 + $dy]) {
                        $blockX = $centerX + $dx;
                        $blockY = $centerY + $dy;
                        $blockZ = $centerZ + $dz;

                        $level->setBlockIdAt($blockX, $blockY, $blockZ, $dy >= 4 ? Block::AIR : $this->liquidId);
                    }
                }
            }
        }

        for ($dx = 0; $dx < 16; $dx++) {
            for ($dz = 0; $dz < 16; $dz++) {
                for ($dy = 4; $dy < 8; $dy++) {
                    if ($lakeMask[($dx * 16 + $dz) * 8 + $dy] && $level->getBlockIdAt($centerX + $dx, $centerY + $dy - 1, $centerZ + $dz) === Block::DIRT) {
                        $level->setBlockIdAt($centerX + $dx, $centerY + $dy - 1, $centerZ + $dz, Block::GRASS);
                    }
                }
            }
        }

        if ($this->liquidId === Block::LAVA) {
            for ($dx = 0; $dx < 16; $dx++) {
                for ($dz = 0; $dz < 16; $dz++) {
                    for ($dy = 0; $dy < 8; $dy++) {
                        if (!$lakeMask[($dx * 16 + $dz) * 8 + $dy] && ($dy < 4 || $random->nextBoundedInt(2) !== 0) && $this->isBuildable($level->getBlockIdAt($centerX + $dx, $centerY + $dy, $centerZ + $dz))) {
                            $level->setBlockIdAt($centerX + $dx, $centerY + $dy, $centerZ + $dz, Block::STONE);
                        }
                    }
                }
            }
        }

        return true;
    }

    private function isBuildable(int $blockId): bool {
        return in_array($blockId, [Block::STONE, Block::DIRT, Block::GRASS]);
    }
}

