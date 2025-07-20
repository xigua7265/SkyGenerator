# 天 域（SkyGenerator）

> “天空不属于任何边界，它属于每一个敢于仰望的人。”

为 Minecraft PE 0.14.x 服务器打造的天域生成器

大部分移植自[OldGenerator](https://github.com/Spottedleaf/OldGenerator)项目（仅根据理解反混淆小部分）。

## ✨ 已完成的功能

- [x] 基础的裸地形生成
- [ ] 生物群系覆盖
- [ ] 完全原版的填充器（40%）
- [ ] 插件版本的完美适配

## 📥 安装（需要一定的 PMMP 基础）

1. 下载源代码
放入服务器 `plugins/` 目录

2. 将 `plugins/SkyGenerator/src/SkyGenerator/sky/SkyBiome.php` 文件复制到 `src/pocketmine/level/generator/sky/`（没有就创建一个目录）

3. 打开服务器核心的 `src/pocketmine/level/generator/biome/Biome.php` 文件

4. 在 `namespace pocketmine\level\generator\biome;` 下面加入 `pocketmine\level\generator\sky\SkyBiome;`

5. 在 `const MAX_BIOMES = 256;` 的上面加入 `const VOID = 127;` (如果已有就不需要加了)

6. 在 `init()` 函数的最后加入 `self::register(self::VOID, new SkyBiome());`，然后保存

## 🎮 使用（需要一定的 PMMP基础）

（不需要重新注册生成器了）

如果你想生成一个含有 SkyGenerator 的世界

可以使用以下代码（是插件配置，不是指令）

```php
//引用添加
use pocketmine\level\generator\Generator;

//世界生成
$name="sky";//名称
$seed=0;//种子
$gen=Generator::getGenerator("sky");
$opts=[];
$this->getServer()->generateLevel($name,$seed,$gen,$opts);
$this->getServer()->loadLevel($name);
```

## 🌎 英文支持

*不准备制作了（bushi*

## 🔧 支持版本

- Genisys 支持 API 2.0.0 版本为 Minecraft PE 0.14.x

## 🤗 特别鸣谢

- 感谢 [Sprouts340](https://github.com/sprouts340) 和 [Cat](https://space.bilibili.com/663009867) 帮助我测试以及支持此项目

## 一些想说的话

- 出现什么问题或者有什么优化建议可以联系我（QQ：3614902642，mail:3614902642@qq.com），**但请您不要鄙视我写的代码！！！！！**

- 这个天域生成器**xiguas服务端**（未公开）里提取的，所以插件版本的非常简陋（需要自己配置），以后我还会优化插件版本的使用体验的

- ~~弱弱的问一句：能不能在用我插件或者改我插件的时候保留名字（（（~~
