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

namespace SkyGenerator;

use SkyGenerator\sky\Sky;
use pocketmine\plugin\PluginBase;
use pocketmine\level\generator\Generator;
use pocketmine\utils\Config;

class Loader extends PluginBase{

	public function onEnable(){
		Generator::addGenerator(Sky::class, "sky");
		@mkdir($this->getDataFolder(),0777,true);
		$config = new Config($this->getDataFolder()."worlds.yml", Config::YAML, 
			array(
				"worlds" => array()
			)
		);
		$worlds = $config->get("worlds");
		foreach($worlds as $name => $seed){
			if($this->getServer()->loadLevel($name) === false){
				$generator = Generator::getGenerator("sky");
				$options = [];					
				$this->getServer()->generateLevel($name, $seed, $generator, $options);
				$this->getServer()->loadLevel($name);
				$this->getServer()->getLogger()->info("§e正在创建名为 {$name} 的天域世界，进入世界需要加载片刻才能正常游玩");
			}else{
				$this->getServer()->getLogger()->info("§c名为 {$name} 的已被加载");
			}
		}
	}
}
