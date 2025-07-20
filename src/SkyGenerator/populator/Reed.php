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
use pocketmine\utils\Random;

class Reed {
    
    public function generate($level, Random $random, int $centerX, int $centerY, int $centerZ): bool {
        for($i = 0; $i < 20; $i++) {
            $x = $centerX + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);
            $z = $centerZ + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);
            
            if($level->getBlockIdAt($x, $centerY, $z) !== Block::AIR) continue;
            
            $adjacentWater = false;
            $adjacentWater |= $level->getBlockIdAt($x-1, $centerY-1, $z) === Block::WATER;
            $adjacentWater |= $level->getBlockIdAt($x+1, $centerY-1, $z) === Block::WATER;
            $adjacentWater |= $level->getBlockIdAt($x, $centerY-1, $z-1) === Block::WATER;
            $adjacentWater |= $level->getBlockIdAt($x, $centerY-1, $z+1) === Block::WATER;
            
            if(!$adjacentWater) continue;
            
            $height = 2 + $random->nextBoundedInt($random->nextBoundedInt(3) + 1);
            for($j = 0; $j < $height; $j++) {
                $level->setBlockIdAt($x, $centerY + $j, $z, Block::SUGARCANE_BLOCK);
            }
        }
        
        return true;
    }
}