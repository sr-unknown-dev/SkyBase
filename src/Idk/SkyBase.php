<?php

namespace Idk;

use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\utils\TextFormat;

class SkyBase
{
    public static int $height = 100;

    public static function run(World $world, Vector3 $pos1, Vector3 $pos2, string $woolColor, string $glassColor): void {
        $minX = min($pos1->getX(), $pos2->getX());
        $maxX = max($pos1->getX(), $pos2->getX());
        $minZ = min($pos1->getZ(), $pos2->getZ());
        $maxZ = max($pos1->getZ(), $pos2->getZ());
        $minY = self::$height;
        $maxY = self::$height + 15;

        $center = new Vector3(floor(($minX + $maxX) / 2), $minY, floor(($minZ + $maxZ) / 2));

        $dyeWoolColor = DyeColor::getAll()[$woolColor];
        $dyeGlassColor = DyeColor::getAll()[$glassColor];

        $woolBlock = VanillaBlocks::WOOL()->setColor($dyeWoolColor);
        $glassBlock = VanillaBlocks::STAINED_GLASS()->setColor($dyeGlassColor);

        // Create the skybase platform
        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $world->setBlock(new Vector3($x, $minY, $z), $woolBlock);
                $world->setBlock(new Vector3($x, $maxY, $z), $woolBlock);
            }
        }

        // Create the walls of the skybase
        for ($y = $minY + 1; $y < $maxY; $y++) {
            for ($x = $minX; $x <= $maxX; $x++) {
                $world->setBlock(new Vector3($x, $y, $minZ), $glassBlock);
                $world->setBlock(new Vector3($x, $y, $maxZ), $glassBlock);
            }
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $world->setBlock(new Vector3($minX, $y, $z), $glassBlock);
                $world->setBlock(new Vector3($maxX, $y, $z), $glassBlock);
            }
        }

        // Create the pillar down to the first solid block
        for ($y = $minY - 1; $y >= 0; $y--) {
            $currentBlock = $world->getBlock(new Vector3($center->x, $y, $center->z));
            if (!$currentBlock instanceof Air) {
                // Stop the pillar at the first non-air block
                $pillarBlock = VanillaBlocks::OAK_WALL_SIGN();
                $world->setBlock(new Vector3($center->x, $y, $center->z), $pillarBlock);

                $tile = $world->getTile(new Vector3($center->x, $y, $center->z));
                if ($tile instanceof Sign) {
                    $tile->setText(new SignText([TextFormat::colorize("&e[Elevator]"), TextFormat::colorize("&7up")]));
                }
                break;
            }

            $world->setBlock(new Vector3($center->x, $y, $center->z), $woolBlock);
        }
    }
}