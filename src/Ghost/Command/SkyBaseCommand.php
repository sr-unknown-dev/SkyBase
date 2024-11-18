<?php

namespace Ghost\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use hcf\HCFLoader;

class SkyBaseCommand extends Command
{
    public function __construct()
    {
        parent::__construct("skybase", "Crea una base en el cielo", "/skybase");
        $this->setPermission("skybase.command");
    }

    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player)
        return;

        if (HCFLoader::getInstance()->getSessionManager()->getSession($player->getXuid())->getCooldown("skybase.cooldown") !== null) {
            $player->sendMessage(TextFormat::RED . "You have cooldown of: ".TextFormat::WHITE.HCFLoader::getInstance()->getSessionManager()->getSession($player->getXuid())->getCooldown("skybase.cooldown"));
        }else {
            $player->getInventory()->addItem(VanillaItems::GOLDEN_HOE()->setCustomName("§3SkyBase Selector"));
            $player->sendMessage("§aUsa clic izquierdo y derecho para seleccionar las posiciones.");
            HCFLoader::getInstance()->getSessionManager()->getSession($player->getXuid())->addCooldown('skybase.cooldown', '', 60, false, false);
        }
    }
}
