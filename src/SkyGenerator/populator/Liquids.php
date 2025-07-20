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

class Liquids {
    
    private $liquidId;
    
    public function __construct(int $liquidId) {
        $this->liquidId = $liquidId;
    }
    
    public function generate($level, Random $random, int $centerX, int $centerY, int $centerZ): bool {
        $above = $level->getBlockIdAt($centerX, $centerY + 1, $centerZ);
        $below = $level->getBlockIdAt($centerX, $centerY - 1, $centerZ);
        $current = $level->getBlockIdAt($centerX, $centerY, $centerZ);
        
        if($above !== Block::STONE) return false;
        if($below !== Block::STONE) return false;
        if($current !== Block::AIR && $current !== Block::STONE) return false;
        
        $solidSides = 0;
        $solidSides += (int)($level->getBlockIdAt($centerX-1, $centerY, $centerZ) === Block::STONE);
        $solidSides += (int)($level->getBlockIdAt($centerX+1, $centerY, $centerZ) === Block::STONE);
        $solidSides += (int)($level->getBlockIdAt($centerX, $centerY, $centerZ-1) === Block::STONE);
        $solidSides += (int)($level->getBlockIdAt($centerX, $centerY, $centerZ+1) === Block::STONE);
        
        $airSides = 0;
        $airSides += (int)($level->getBlockIdAt($centerX-1, $centerY, $centerZ) === Block::AIR);
        $airSides += (int)($level->getBlockIdAt($centerX+1, $centerY, $centerZ) === Block::AIR);
        $airSides += (int)($level->getBlockIdAt($centerX, $centerY, $centerZ-1) === Block::AIR);
        $airSides += (int)($level->getBlockIdAt($centerX, $centerY, $centerZ+1) === Block::AIR);
        
        if($solidSides === 3 && $airSides === 1) {
            $level->setBlockIdAt($centerX, $centerY, $centerZ, $this->liquidId);
            return true;
        }
        
        return false;
    }
}