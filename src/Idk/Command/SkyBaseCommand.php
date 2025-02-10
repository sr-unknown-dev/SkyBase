<?php

namespace Idk\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use hcf\Loader;

class SkyBaseCommand extends Command
{
    public function __construct()
    {
        parent::__construct("skybase", "Crea una base en el cielo", "/skybase");
        $this->setPermission("skybase.command");
    }

    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) {
            return;
        }

        $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());
        $cooldown = $session->getCooldown("skybase.cooldown");

        if ($cooldown !== null) {
            // Assuming getTimeRemaining() returns a string representation of the cooldown time
            $cooldownTime = $cooldown->getTimeRemaining();
            $player->sendMessage(TextFormat::RED . "You have cooldown of: " . TextFormat::WHITE . $cooldownTime);
        } else {
            $player->getInventory()->addItem(VanillaItems::GOLDEN_HOE()->setCustomName("§3SkyBase Selector"));
            $player->sendMessage("§aUsa clic izquierdo y derecho para seleccionar las posiciones.");
            $session->addCooldown('skybase.cooldown', '', 60, false, false);
        }
    }
}