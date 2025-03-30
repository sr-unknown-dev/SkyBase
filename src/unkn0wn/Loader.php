<?php

namespace unkn0wn;

use unkn0wn\Command\SkyBaseCommand;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase implements Listener
{
    private array $positions = [];
    private Menus $menus;
    use SingletonTrait;

    public function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void
    {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        $this->menus = new Menus();
        $this->getServer()->getCommandMap()->register("skybase", new SkyBaseCommand);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("SkyBase plugin enabled!");
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getNamedTag()->getTag("") && $item->getNamedTag()->getString("Airdrop_Item") === "Airdrop") {
            $position = $event->getBlock()->getPosition();

            if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                $this->positions[$player->getName()]['pos1'] = $position;
                $player->sendMessage("§aFirst position placed");
            } elseif ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                $this->positions[$player->getName()]['pos2'] = $position;
                $player->sendMessage("§aSecond position placed");
            }

            if (isset($this->positions[$player->getName()]['pos1']) && isset($this->positions[$player->getName()]['pos2'])) {
                $pos1 = $this->positions[$player->getName()]['pos1'];
                $pos2 = $this->positions[$player->getName()]['pos2'];

                $sizeX = abs($pos1->getX() - $pos2->getX()) + 1;
                $sizeZ = abs($pos1->getZ() - $pos2->getZ()) + 1;

                if ($sizeX > 50 || $sizeZ > 50) {
                    $player->sendMessage("§cNo se pudo crear la SkyBase porque la selección es mayor a 50 bloques.");
                    return;
                }
                
                if (isset($this->positions[$player->getName()]['pos1']) && isset($this->positions[$player->getName()]['pos2']) || isset($this->positions[$player->getName()]['pos2']) && isset($this->positions[$player->getName()]['pos1'])) {
                    $this->runSkyBase($player);
                }
            }
        }
    }

    public function runSkyBase(Player $player): void
    {
        if (!isset($this->positions[$player->getName()]['pos1']) || !isset($this->positions[$player->getName()]['pos2'])) {
            $player->sendMessage("§cNo se pudo crear la SkyBase porque las posiciones no están definidas.");
            return;
        }

        SkyBase::run(
            $player->getWorld(),
            $this->positions[$player->getName()]['pos1'],
            $this->positions[$player->getName()]['pos2'],
            $this->menus->getWoolColor(),
            $this->menus->getGlassColor()
        );
        $player->sendMessage("§aSkyBase creada.");
    }
}