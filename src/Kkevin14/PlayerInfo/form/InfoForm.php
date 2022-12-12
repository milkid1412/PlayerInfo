<?php
declare(strict_types=1);

namespace Kkevin14\PlayerInfo\form;

use Kkevin14\PlayerInfo\Main;
use Kkevin14\FamePlugin\Main as Fame;
use Kkevin14\FriendPlugin\Main as Friend;
use onebone\economyapi\EconomyAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;

class InfoForm implements Form
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
        $str = '§b▶ §f닉네임: ' . $this->target->getName();
        $str .= "\n" . '§b▶ §f보유중인 돈: ' . EconomyAPI::getInstance()->myMoney($this->target) . '원';
        $str .= "\n" . '§b▶ §f인기도: ' . Fame::getInstance()->getFame($this->target) . ' (§b' . Fame::getInstance()->getRank($this->target) . '위)';
        return [
            'type' => 'form',
            'title' => $this->owner->title,
            'content' => $str . "\n\n",
            'buttons' => [
                [
                    'text' => '§g◆ §f돈 지불'
                ],
                [
                    'text' => '§g◆ §f인기도 올리기'
                ],
                [
                    'text' => '§g◆ §f친구 추가'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if($data === 0){
            $player->sendForm(new EnterMoneyForm($this->owner, $this->target));
        }elseif($data === 1){
            if(Fame::getInstance()->getFameLeftCount($player) < 1){
                $this->owner->msg($player, '남은 인기도 포인트가 없습니다.');
                return;
            }
            Fame::getInstance()->giveFame($player, $this->target);
            $this->owner->msg($player, '인기도를 지급했습니다.');
            $this->owner->msg($this->target, $player->getName() . '님이 인기도를 올려줬습니다.');
        }elseif($data === 2){
            if(Friend::getInstance()->isFriend($player, $this->target)){
                $this->owner->msg($player, $this->target->getName() . '님과는 이미 친구입니다.');
                return;
            }
            Friend::getInstance()->requestFriend($player, $this->target);
        }
    }
}