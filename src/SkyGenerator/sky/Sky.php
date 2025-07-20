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
 * QQ:3614902642
 */

namespace SkyGenerator\sky;

use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\Generator;
use SkyGenerator\noise\b173\NoiseGeneratorOctaves;
use SkyGenerator\noise\b173\NoiseGeneratorOctaves2;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\populator\Cave;
use pocketmine\level\generator\populator\GroundCover;
use pocketmine\level\generator\populator\Ore;

use pocketmine\level\generator\populator\Flower;
use SkyGenerator\populator\Lakes;
use SkyGenerator\populator\Liquids;
use SkyGenerator\populator\Pumpkin;
use pocketmine\level\generator\populator\Sugarcane;

use pocketmine\level\generator\populator\Populator;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class Sky extends Generator {
    const NAME = "Sky";

    /** @var Populator[] */
    protected $populators = [];
    /** @var Populator[] */
    protected $generationPopulators = [];
    /** @var ChunkManager */
    protected $level;
    /** @var Random */
    protected $random;
    protected $waterHeight = 0;

    /** @var NoiseGeneratorOctaves */
    private $terrainNoise1Generator;
    /** @var NoiseGeneratorOctaves */
    private $terrainNoise2Generator;
    /** @var NoiseGeneratorOctaves */
    private $terrainNoise3Generator;
    /** @var NoiseGeneratorOctaves */
    private $sandAndGravelNoiseGenerator;
    /** @var NoiseGeneratorOctaves */
    private $stoneNoiseGenerator;
    /** @var NoiseGeneratorOctaves */
    private $terrainNoise4Generator;
    /** @var NoiseGeneratorOctaves */
    private $terrainNoise5Generator;
    /** @var NoiseGeneratorOctaves */
    private $treeCountNoise;
    
    private $terrainNoise = [];
    private $sandNoise = [];
    private $gravelNoise = [];
    private $stoneNoise = [];
    private $terrainNoise1 = [];
    private $terrainNoise2 = [];
    private $terrainNoise3 = [];
    private $terrainNoise4 = [];
    private $terrainNoise5 = [];
    private $snowNoise = [];
    
    private $e;
    private $f;
    private $g;
    private $c = [];
    
    private $biomeNoiseCache;

	/** @var BiomeSelector */
	protected $selector;

    public function __construct(array $options = []) {}

    public function init(ChunkManager $level, Random $random) {
        $this->level = $level;
        $this->random = $random;
        $this->random->setSeed($this->level->getSeed());

        $this->terrainNoise2Generator = new NoiseGeneratorOctaves($this->random, 16);
        $this->terrainNoise3Generator = new NoiseGeneratorOctaves($this->random, 16);
        $this->terrainNoise1Generator = new NoiseGeneratorOctaves($this->random, 8);
        $this->sandAndGravelNoiseGenerator = new NoiseGeneratorOctaves($this->random, 4);
        $this->stoneNoiseGenerator = new NoiseGeneratorOctaves($this->random, 4);
        $this->terrainNoise4Generator = new NoiseGeneratorOctaves($this->random, 10);
        $this->terrainNoise5Generator = new NoiseGeneratorOctaves($this->random, 16);
        $this->treeCountNoise = new NoiseGeneratorOctaves($this->random, 8);
        
        $this->e = new NoiseGeneratorOctaves2(new Random($level->getSeed() * 9871), 4);
        $this->f = new NoiseGeneratorOctaves2(new Random($level->getSeed() * 39811), 4);
        $this->g = new NoiseGeneratorOctaves2(new Random($level->getSeed() * 543321), 2);
        
        $cover = new GroundCover();
        $this->generationPopulators[] = $cover;
        
		$cave = new Cave();
		$this->populators[] = $cave;

		$ores = new Ore();
		$ores->setOreTypes([
			new OreType(Block::get(Block::COAL_ORE), 20, 16, 0, 128),
			new OreType(Block::get(Block::IRON_ORE), 20, 8, 0, 64),
			new OreType(Block::get(Block::REDSTONE_ORE), 8, 7, 0, 16),
			new OreType(Block::get(Block::LAPIS_ORE), 1, 6, 0, 32),
			new OreType(Block::get(Block::GOLD_ORE), 2, 8, 0, 32),
			new OreType(Block::get(Block::DIAMOND_ORE), 1, 7, 0, 16),
			new OreType(Block::get(Block::DIRT), 20, 32, 0, 128),
			new OreType(Block::get(Block::GRAVEL), 10, 16, 0, 128)
		]);
        $this->populators[] = $ores;
        
        $flower = new Flower();
		$flower->setBaseAmount(2);
		$flower->addType([Block::DANDELION, 0]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_POPPY]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_AZURE_BLUET]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_RED_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_WHITE_TULIP]);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_OXEYE_DAISY]); 
		$this->populators[] = $flower;
    }

    public function generateChunk($chunkX, $chunkZ) {
        $this->random->setSeed($chunkX * 341873128712 + $chunkZ * 132897987541);
        
        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        $this->generateBareTerrain($chunkX, $chunkZ, $chunk);
        $this->generateBiomeTerrain($chunkX, $chunkZ, $chunk);
    }
    
    private function generateBareTerrain(int $chunkX, int $chunkZ, $chunk) {
        $b0 = 2;
        $k = $b0 + 1;
        $b1 = 33;
        $l = $b0 + 1;
        
        $this->terrainNoise = $this->generateTerrainNoise(
            $this->terrainNoise, $chunkX * $b0, 0, $chunkZ * $b0, $k, $b1, $l
        );
        
        for ($i1 = 0; $i1 < $b0; ++$i1) {
            for ($j1 = 0; $j1 < $b0; ++$j1) {
                for ($k1 = 0; $k1 < 32; ++$k1) {
                    $d0 = 0.25;
                    $d1 = $this->terrainNoise[(($i1 + 0) * $l + $j1 + 0) * $b1 + $k1 + 0];
                    $d2 = $this->terrainNoise[(($i1 + 0) * $l + $j1 + 1) * $b1 + $k1 + 0];
                    $d3 = $this->terrainNoise[(($i1 + 1) * $l + $j1 + 0) * $b1 + $k1 + 0];
                    $d4 = $this->terrainNoise[(($i1 + 1) * $l + $j1 + 1) * $b1 + $k1 + 0];
                    $d5 = ($this->terrainNoise[(($i1 + 0) * $l + $j1 + 0) * $b1 + $k1 + 1] - $d1) * $d0;
                    $d6 = ($this->terrainNoise[(($i1 + 0) * $l + $j1 + 1) * $b1 + $k1 + 1] - $d2) * $d0;
                    $d7 = ($this->terrainNoise[(($i1 + 1) * $l + $j1 + 0) * $b1 + $k1 + 1] - $d3) * $d0;
                    $d8 = ($this->terrainNoise[(($i1 + 1) * $l + $j1 + 1) * $b1 + $k1 + 1] - $d4) * $d0;
                    
                    for ($l1 = 0; $l1 < 4; ++$l1) {
                        $d9 = 0.125;
                        $d10 = $d1;
                        $d11 = $d2;
                        $d12 = ($d3 - $d1) * $d9;
                        $d13 = ($d4 - $d2) * $d9;
                        
                        for ($i2 = 0; $i2 < 8; ++$i2) {
							$j2 = $i2 + $i1 * 8;
                            $zBase = $j1 * 8;
                            $yPos = $k1 * 4 + $l1;

                            $d14 = 0.125;
                            $d15 = $d10;
                            $d16 = ($d11 - $d10) * $d14;
                            
                            for ($k2 = 0; $k2 < 8; ++$k2) {
                                $x = $j2;
                                $z = $zBase + $k2;
                                $block = Block::AIR;
                                
                                if ($d15 > 0.0) {
                                    $block = Block::STONE;
                                }

                                $chunk->setBlockId($x, $yPos, $z, $block);
                                
                                $d15 += $d16;
                            }
                            
                            $d10 += $d12;
                            $d11 += $d13;
                        }
                        
                        $d1 += $d5;
                        $d2 += $d6;
                        $d3 += $d7;
                        $d4 += $d8;
                    }
                }
            }
        }
    }
    
    private function generateBiomeTerrain(int $chunkX, int $chunkZ, $chunk) {
        $d0 = 0.03125;
        $this->sandNoise = $this->sandAndGravelNoiseGenerator->generateNoise(
            $this->sandNoise, $chunkX * 16, $chunkZ * 16, 0.0, 
            16, 16, 1, $d0, $d0, 1.0
        );
        $this->gravelNoise = $this->sandAndGravelNoiseGenerator->generateNoise(
            $this->gravelNoise, $chunkX * 16, 109.0134, $chunkZ * 16,
            16, 1, 16, $d0, 1.0, $d0
        );
        $this->stoneNoise = $this->stoneNoiseGenerator->generateNoise(
            $this->stoneNoise, $chunkX * 16, $chunkZ * 16, 0.0,
            16, 16, 1, $d0 * 2.0, $d0 * 2.0, $d0 * 2.0
        );
        
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $index = $x + $z * 16;
                $i1 = (int) (($this->stoneNoise[$index] ?? 0) / 3.0 + 3.0 + $this->random->nextFloat() * 0.25);
                $j1 = -1;
                $topBlock = Block::GRASS;
                $groundBlock = Block::DIRT;
                
                for ($y = 127; $y >= 0; --$y) {
                    $blockId = $chunk->getBlockId($x, $y, $z);
                    
                    if ($blockId === Block::AIR) {
                        $j1 = -1;
                    } elseif ($blockId === Block::STONE) {
                        if ($j1 === -1) {
                            if ($i1 <= 0) {
                                $topBlock = Block::AIR;
                                $groundBlock = Block::STONE;
                            }
                            
                            $j1 = $i1;
                            if ($y >= 0) {
                                $chunk->setBlockId($x, $y, $z, $topBlock);
                            } else {
                                $chunk->setBlockId($x, $y, $z, $groundBlock);
                            }
                        } elseif ($j1 > 0) {
                            --$j1;
                            $chunk->setBlockId($x, $y, $z, $groundBlock);
                        }
                    }
                }
                
                //我现在明白了一个道理，就是不要做不必要的事（插件不再加入SkyBiome的支持了）
				//$biome = Biome::getBiome(Biome::VOID);
				//$chunk->setBiomeId($x, $z, $biome->getId());
				//$color = [0, 0, 0];
				//$bColor = $biome->getColor();
				//$color[0] += ($bColor >> 16);
				//$color[1] += (($bColor >> 8) & 0xff);
				//$color[2] += ($bColor & 0xff);

				//$chunk->setBiomeColor($x, $z, $color[0], $color[1], $color[2]);
				$chunk->setBiomeColor($x, $z, 159, 181, 117);//这是通过计算得到的颜色值（整数）
            }
        }
    }
    
    private function generateTerrainNoise($noise, int $fromX, int $fromY, int $fromZ, int $xLen, int $yLen, int $zLen): array {
        $size = $xLen * $yLen * $zLen;
        if ($noise === null || count($noise) < $size) {
            $noise = array_fill(0, $size, 0.0);
        } else {
            for ($i = 0; $i < $size; $i++) {
                $noise[$i] = 0.0;
            }
        }
        
        $d0 = 684.412;
        $d1 = 684.412;
        
        $this->terrainNoise4 = $this->terrainNoise4Generator->generateNoise2(
            $this->terrainNoise4, $fromX, $fromZ, $xLen, $zLen, 1.121, 1.121, 0.5
        );
        $this->terrainNoise5 = $this->terrainNoise5Generator->generateNoise2(
            $this->terrainNoise5, $fromX, $fromZ, $xLen, $zLen, 200.0, 200.0, 0.5
        );
        
        $d0 *= 2.0;
        $this->terrainNoise1 = $this->terrainNoise1Generator->generateNoise(
            $this->terrainNoise1, $fromX, $fromY, $fromZ, 
            $xLen, $yLen, $zLen, $d0 / 80.0, $d1 / 160.0, $d0 / 80.0
        );
        $this->terrainNoise2 = $this->terrainNoise2Generator->generateNoise(
            $this->terrainNoise2, $fromX, $fromY, $fromZ, 
            $xLen, $yLen, $zLen, $d0, $d1, $d0
        );
        $this->terrainNoise3 = $this->terrainNoise3Generator->generateNoise(
            $this->terrainNoise3, $fromX, $fromY, $fromZ, 
            $xLen, $yLen, $zLen, $d0, $d1, $d0
        );
        
        $k1 = 0;
        $l1 = 0;
        $i2 = 16 / $xLen;
        
        for ($j2 = 0; $j2 < $xLen; ++$j2) {
            $k2 = $j2 * $i2 + (int)($i2 / 2);
            
            for ($l2 = 0; $l2 < $zLen; ++$l2) {
                $i3 = $l2 * $i2 + (int)($i2 / 2);
                $d2 = 0.5;
                $d3 = 0.0 * $d2;
                $d4 = 1.0 - $d3;
                $d4 *= $d4;
                $d4 *= $d4;
                $d4 = 1.0 - $d4;
                $d5 = ($this->terrainNoise4[$l1] + 256.0) / 512.0;
                $d5 *= $d4;
                
                if ($d5 > 1.0) $d5 = 1.0;
                $d6 = $this->terrainNoise5[$l1] / 8000.0;
                
                if ($d6 < 0.0) $d6 = -$d6 * 0.3;
                $d6 = $d6 * 3.0 - 2.0;
                if ($d6 > 1.0) $d6 = 1.0;
                $d6 /= 8.0;
                $d6 = 0.0;
                if ($d5 < 0.0) $d5 = 0.0;
                
                $d5 += 0.5;
                $d6 = $d6 * $yLen / 16.0;
                ++$l1;
                $d7 = $yLen / 2.0;
                
                for ($j3 = 0; $j3 < $yLen; ++$j3) {
                    $d8 = 0.0;
                    $d9 = ($j3 - $d7) * 8.0 / $d5;
                    
                    if ($d9 < 0.0) $d9 *= -1.0;
                    
                    $d10 = $this->terrainNoise2[$k1] / 512.0;
                    $d11 = $this->terrainNoise3[$k1] / 512.0;
                    $d12 = ($this->terrainNoise1[$k1] / 10.0 + 1.0) / 2.0;
                    
                    if ($d12 < 0.0) {
                        $d8 = $d10;
                    } elseif ($d12 > 1.0) {
                        $d8 = $d11;
                    } else {
                        $d8 = $d10 + ($d11 - $d10) * $d12;
                    }
                    
                    $d8 -= 8.0;
                    $b0 = 32;
                    $d13 = 0.0;
                    
                    if ($j3 > $yLen - $b0) {
                        $d13 = (double)((float)($j3 - ($yLen - $b0)) / (float)($b0 - 1));
                        $d8 = $d8 * (1.0 - $d13) + -30.0 * $d13;
                    }
                    
                    $b0 = 8;
                    if ($j3 < $b0) {
                        $d13 = ($b0 - $j3) / ($b0 - 1);
                        $d8 = $d8 * (1.0 - $d13) + -30.0 * $d13;
                    }
                    
                    $noise[$k1] = $d8;
                    ++$k1;
                }
            }
        }
        
        return $noise;
    }

    public function populateChunk($chunkX, $chunkZ) {
        $k = $chunkX * 16;
        $l = $chunkZ * 16;
        
        $this->random->setSeed($this->level->getSeed());
        $i1 = $this->random->nextInt() / 2 * 2 + 1;
        $j1 = $this->random->nextInt() / 2 * 2 + 1;
        $this->random->setSeed((($chunkX * $i1) + ($chunkZ * $j1)) ^ $this->level->getSeed());
        
        if ($this->random->nextBoundedInt(4) === 0) {
            $x = $k + $this->random->nextBoundedInt(16) + 8;
            $y = $this->random->nextBoundedInt(128);
            $z = $l + $this->random->nextBoundedInt(16) + 8;
            $lakes = new Lakes(Block::WATER);
            $lakes->generate($this->level, $this->random, $x, $y, $z);
        }
        
        if ($this->random->nextBoundedInt(8) === 0) {
            $x = $k + $this->random->nextBoundedInt(16) + 8;
            $y = $this->random->nextBoundedInt($this->random->nextBoundedInt(120) + 8);
            $z = $l + $this->random->nextBoundedInt(16) + 8;
            if ($y < 64 || $this->random->nextBoundedInt(10) === 0) {
                $lakes = new Lakes(Block::LAVA);
				$lakes->generate($this->level, $this->random, $x, $y, $z);
            }
        }

        $treeCount = (int) (($this->treeCountNoise->generateNoiseForCoordinate($k * 0.5, $l * 0.5) / 8.0 + $this->random->nextFloat() * 4.0 + 4.0) / 9.0);

        if ($treeCount < 0) $treeCount = 0;
		for($i = 0; $i < $treeCount; ++$i){
			$tryCount = 0;
			$y = 0;
			while($y == 0 and $tryCount < 16){
				$x = $this->random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
				$z = $this->random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
				$y = $this->getHighestBlockAt($x, $z);
				$tryCount++;
			}
			if($y == 0){
				continue;
			}
			$tree = new OakTree();
			$tree->placeObject($this->level, $x, $y, $z, $this->random);
		}
        
        $reed = new Sugarcane();
		$reed->setBaseAmount(2);
		$this->populators[] = $reed;
        
        if ($this->random->nextBoundedInt(32) === 0) {
            $x = $k + $this->random->nextBoundedInt(16) + 8;
            $y = $this->random->nextBoundedInt(128);
            $z = $l + $this->random->nextBoundedInt(16) + 8;
            $pumpkin = new Pumpkin();
            $pumpkin->generate($this->level, $this->random, $x, $y, $z);
        }

        for ($i = 0; $i < 50; ++$i) {
            $x = $k + $this->random->nextBoundedInt(16) + 8;
            $y = $this->random->nextBoundedInt($this->random->nextBoundedInt(120) + 8);
            $z = $l + $this->random->nextBoundedInt(16) + 8;
            $waterSpring = new Liquids(Block::WATER);
            $waterSpring->generate($this->level, $this->random, $x, $y, $z);
        }
        
        for ($i = 0; $i < 20; ++$i) {
            $x = $k + $this->random->nextBoundedInt(16) + 8;
            $inner = $this->random->nextBoundedInt(112);
            if ($inner === 0) {
                $inner = 1;
            }
            $middle = $this->random->nextBoundedInt($inner) + 8;
            $y = $this->random->nextBoundedInt($middle);
            $z = $l + $this->random->nextBoundedInt(16) + 8;
            $lavaSpring = new Liquids(Block::LAVA);
            $lavaSpring->generate($this->level, $this->random, $x, $y, $z);
        }
        
        $globalStartX = $chunkX * 16 + 8;
        $globalStartZ = $chunkZ * 16 + 8;
        $this->snowNoise = $this->createNoise($this->snowNoise, $globalStartX, $globalStartZ, 16, 16);
        
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        for ($x = 0; $x < 16; ++$x) {
            for ($z = 0; $z < 16; ++$z) {
                $globalX = $chunkX * 16 + $x;
                $globalZ = $chunkZ * 16 + $z;
                $noiseIndex = $x + $z * 16;
                
    			$y = $this->getHighestBlockAt($globalX, $globalZ);
    			if($y == 0){
    				continue;
    			}
    			
                $d1 = $this->snowNoise[$noiseIndex] - ($y - 64) / 64.0 * 0.3;
                if ($d1 < 0.5 && $y > 0 && $y < 128) {
                    $aboveY = $y + 1;
                    if ($chunk->getBlockId($x, $aboveY, $z) === Block::AIR) {
                        $belowBlock = $chunk->getBlockId($x, $y, $z);
                        $belowSolid = Block::get($belowBlock)->isSolid();
                        $isIce = $belowBlock === Block::ICE;
                        
                        if ($belowSolid && !$isIce) {
                            $chunk->setBlockId($x, $aboveY, $z, Block::SNOW_LAYER);
                        }
                    }
                }
            }
        }
       
       
        foreach ($this->populators as $populator) {
            $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
        }
    }

    private function getHighestBlockAt(int $x, int $z): int {
        for ($y = 127; $y >= 0; $y--) {
            if ($this->level->getBlockIdAt($x, $y, $z) !== Block::AIR) {
                return $y;
            }
        }
        return 0;
    }

    public function getSpawn(): Vector3 {
        return new Vector3(128, 64, 128);
    }

    public function getName(): string {
        return self::NAME;
    }
    
    public function getSettings(): array {
        return [];
    }
    
    
    private function createNoise($into, $startX, $startZ, $sizeX, $sizeZ) {
        if ($into === null || count($into) < $sizeX * $sizeZ) {
            $into = array_fill(0, $sizeX * $sizeZ, 0.0);
        } else {
            $into = array_map(function() { return 0.0; }, $into);
        }
    
        $into = $this->e->generateNoise(
            $into, $startX, $startZ, $sizeX, $sizeZ, 0.025, 0.025, 0.25
        );
        
        $this->c = $this->g->generateNoise(
            $this->c, $startX, $startZ, $sizeX, $sizeZ, 0.25, 0.25, 0.5882352941176471
        );
        
        $index = 0;
        for ($x = 0; $x < $sizeX; ++$x) {
            for ($z = 0; $z < $sizeZ; ++$z) {
                $d0 = $this->c[$index] * 1.1 + 0.5;
                $d1 = 0.01;
                $d2 = 1.0 - $d1;
                $d3 = ($into[$index] * 0.15 + 0.7) * $d2 + $d0 * $d1;
                
                $d3 = 1.0 - (1.0 - $d3) * (1.0 - $d3);
                if ($d3 < 0.0) $d3 = 0.0;
                if ($d3 > 1.0) $d3 = 1.0;
                
                $into[$index] = $d3;
                $index++;
            }
        }
        
        return $into;
    }
}
