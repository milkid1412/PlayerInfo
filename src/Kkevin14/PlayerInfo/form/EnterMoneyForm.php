<?php
declare(strict_types=1);

namespace Kkevin14\PlayerInfo\form;

use Kkevin14\PlayerInfo\Main;
use onebone\economyapi\EconomyAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;

class EnterMoneyForm implements Form
{
    private Main $owner;

    private Player $target;

    public function __construct(Main $owner, Player $target)
    {
        $this->owner = $owner;
        $this->target = $target;
    }

    public function jsonSerialize()
    {
        return [
            'type' => 'custom_form',
            'title' => $this->owner->title,
            'content' => [
                [
                    'type' => 'input',
                    'text' => '지불하실 금액을 입력해주세요.'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null || !is_numeric($data[0]) || intval($data[0]) <= 0){
            $this->owner->msg($player, '금액은 1원 이상으로 입력해주세요.');
            return;
        }
        $data = intval($data[0]);
        if($data > EconomyAPI::getInstance()->myMoney($player)){
            $this->owner->msg($player, '보유한 돈이 부족합니다.');
            return;
        }
        $player->sendForm(new CheckPayForm($this->owner, $this->target, $data));
    }
}