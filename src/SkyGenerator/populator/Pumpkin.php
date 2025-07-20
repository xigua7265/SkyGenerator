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

class Pumpkin {
    
    public function generate($level, Random $random, int $centerX, int $centerY, int $centerZ): bool {
        for($i = 0; $i < 64; $i++) {
            $x = $centerX + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
            $y = $centerY + $random->nextBoundedInt(4) - $random->nextBoundedInt(4);
            $z = $centerZ + $random->nextBoundedInt(8) - $random->nextBoundedInt(8);
            
            if($level->getBlockIdAt($x, $y, $z) !== Block::AIR) continue;
            if($level->getBlockIdAt($x, $y - 1, $z) !== Block::GRASS) continue;
            
            $level->setBlockIdAt($x, $y, $z, Block::PUMPKIN);
            $level->setBlockDataAt($x, $y, $z, $random->nextBoundedInt(4));
        }
        
        return true;
    }
}