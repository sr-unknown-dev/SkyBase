<?php
namespace Idk;

use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\utils\TextFormat;
use pocketmine\block\Air;

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

        // Crear la plataforma de la skybase
        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $world->setBlock(new Vector3($x, $minY, $z), $woolBlock);
                $world->setBlock(new Vector3($x, $maxY, $z), $woolBlock);
            }
        }

        // Crear las paredes de la skybase
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

        // Crear el pilar hacia abajo hasta el primer bloque sólido
        for ($y = $minY - 1; $y >= 0; $y--) {
            $currentBlock = $world->getBlock(new Vector3($center->x, $y, $center->z));
            if (!$currentBlock instanceof Air) {
                // Colocar el cartel en la parte inferior
                $pillarBlock = VanillaBlocks::OAK_SIGN();
                $world->setBlock(new Vector3($center->x, $y + 1, $center->z), $pillarBlock);

                // Establecer el texto del cartel en la parte inferior
                $tile = $world->getTile(new Vector3($center->x, $y + 1, $center->z));
                if ($tile instanceof Sign) {
                    $tile->setText(new SignText([TextFormat::colorize("&e[Elevator]"), TextFormat::colorize("&7up")]));
                }

                // Colocar el cartel en la parte superior del pilar
                $world->setBlock(new Vector3($center->x, $y + 2, $center->z), $pillarBlock);

                // Establecer el texto del cartel en la parte superior
                $tileTop = $world->getTile(new Vector3($center->x, $y + 2, $center->z));
                if ($tileTop instanceof Sign) {
                    $tileTop->setText(new SignText([TextFormat::colorize("&e[Elevator]"), TextFormat::colorize("&7down")]));
                }
                break;
            }
            $world->setBlock(new Vector3($center->x, $y, $center->z), $woolBlock);
        }
    }
}