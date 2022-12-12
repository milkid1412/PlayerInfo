<?php
declare(strict_types=1);

namespace Kkevin14\PlayerInfo\form;

use Kkevin14\PlayerInfo\Main;
use onebone\economyapi\EconomyAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;

class CheckPayForm implements Form
{
    private Main $owner;

    private Player $target;

    private int $amount;

    public function __construct(Main $owner, Player $target, int $amount)
    {
        $this->owner = $owner;
        $this->target = $target;
        $this->amount = $amount;
    }

    public function jsonSerialize()
    {
        return [
            'type' => 'modal',
            'title' => $this->owner->title,
            'content' => $this->target->getName() . '님에게 §b' . $this->amount . '원§f을 보냅니다.',
            'button1' => '§l§a✔ §r§f예',
            'button2' => '§c✖ §f아니요'
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data){
            EconomyAPI::getInstance()->reduceMoney($player, $this->amount);
            EconomyAPI::getInstance()->addMoney($this->target, $this->amount);
            $this->owner->msg($player, $this->target->getName() . '님께 ' . $this->amount . '원을 보냈습니다.');
            $this->owner->msg($this->target, $player->getName() . '님이 ' . $this->amount . '원을 보냈습니다.');
        }else{
            $this->owner->msg($player, '작업을 취소했습니다.');
        }
    }
}