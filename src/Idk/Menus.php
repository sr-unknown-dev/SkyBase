<?php

namespace Idk;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\SimpleInvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class Menus
{
    private string $woolColor = "WHITE";
    private string $glassColor = "WHITE";

    public function getWoolColor(): string
    {
        return $this->woolColor;
    }

    public function getGlassColor(): string
    {
        return $this->glassColor;
    }

    private function getDyeColorByName(string $name): ?DyeColor
    {
        foreach (DyeColor::getAll() as $dyeColor) {
            if ($dyeColor->name() === $name) {
                return $dyeColor;
            }
        }
        return null;
    }

    public function openColorSelectionMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("Selecciona el color de la lana");

        $colors = [
            "WHITE", "ORANGE", "MAGENTA", "LIGHT_BLUE", "YELLOW", "LIME", "PINK", "GRAY",
            "LIGHT_GRAY", "CYAN", "PURPLE", "BLUE", "BROWN", "GREEN", "RED", "BLACK"
        ];

        foreach ($colors as $index => $color) {
            $dyeColor = $this->getDyeColorByName($color);
            if ($dyeColor !== null) {
                $item = VanillaBlocks::WOOL()->setColor($dyeColor)->asItem();
                $item->setCustomName($color);
                $menu->getInventory()->setItem($index, $item);
            }
        }

        $menu->setListener(function (SimpleInvMenuTransaction $transaction) use ($colors): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();
            $this->woolColor = $itemClicked->getCustomName();
            $this->openGlassSelectionMenu($player);
            return $transaction->discard();
        });

        $menu->send($player, "§gSelect the wool color");
    }

    public function openGlassSelectionMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("Selecciona el color del cristal");

        $colors = [
            "WHITE", "ORANGE", "MAGENTA", "LIGHT_BLUE", "YELLOW", "LIME", "PINK", "GRAY",
            "LIGHT_GRAY", "CYAN", "PURPLE", "BLUE", "BROWN", "GREEN", "RED", "BLACK"
        ];

        foreach ($colors as $index => $color) {
            $dyeColor = $this->getDyeColorByName($color);
            if ($dyeColor !== null) {
                $item = VanillaBlocks::STAINED_GLASS()->setColor($dyeColor)->asItem();
                $item->setCustomName($color);
                $menu->getInventory()->setItem($index, $item);
            }
        }

        $menu->setListener(function (SimpleInvMenuTransaction $transaction) use ($colors): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();
            $this->glassColor = $itemClicked->getCustomName();
            $player->sendMessage("Color de lana seleccionado: " . $this->woolColor);
            $player->sendMessage("Color de cristal seleccionado: " . $this->glassColor);
            Loader::getInstance()->runSkyBase($player);
        
            $inventory = $player->getInventory();
            foreach ($inventory->getContents() as $slot => $item) {
                if ($item->getTypeId() === VanillaItems::GOLDEN_HOE()->getTypeId() && $item->getCustomName() === "§3SkyBase Selector") {
                    $inventory->clear($slot);
                    break;
                }
            }
        
            return $transaction->discard();
        });

        $menu->send($player, "§gSelect the glass color");
    }
}