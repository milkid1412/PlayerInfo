<?php
declare(strict_types=1);

namespace Kkevin14\PlayerInfo;

use Kkevin14\PlayerInfo\form\InfoForm;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    public string $title = '§l§7[ §f정보 §7]';

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function msg(?Player $player, string $msg)
    {
        if($player === null || !$player->isOnline()) return;
        $player->sendMessage('§b◈ §f' . $msg);
    }

    public function onPlayerTouch(EntityDamageByEntityEvent $event)
    {
        $player = $event->getDamager();
        $target = $event->getEntity();
        if(!$player instanceof Player || !$target instanceof Player || !$player->isSneaking()) return;
        $player->sendForm(new InfoForm($this, $target));
    }
}