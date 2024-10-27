<?php

namespace Ghost\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

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

        $player->getInventory()->addItem(VanillaItems::GOLDEN_HOE()->setCustomName("§3SkyBase Selector"));
        $player->sendMessage("§aUsa clic izquierdo y derecho para seleccionar las posiciones.");
    }
}