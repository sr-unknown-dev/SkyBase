<?php

namespace unkn0wn\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use hcf\player\Player;
use pocketmine\utils\TextFormat;
use hcf\Loader;
use hcf\utils\time\Timer;

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

        $session = Loader::getInstance()->getSessionManager()->getSession($player->getName());
        $cooldown = $session->getCooldown("skybase.cooldown");

        if ($cooldown !== null) {
            $player->sendMessage(TextFormat::RED . "You have cooldown of: " . TextFormat::WHITE . Timer::convert((int)$session->getCooldown("skybase.cooldown")));
        } else {
            $selector = VanillaItems::GOLDEN_HOE()->setCustomName("§3SkyBase Selector");
            $selector->getNamedTag()->setString("skybase", "selector");
            $player->getInventory()->addItem($selector);
            $player->sendMessage("§aUsa clic izquierdo y derecho para seleccionar las posiciones.");
            $session->addCooldown('skybase.cooldown', '', 60, false, false);
        }
    }
}