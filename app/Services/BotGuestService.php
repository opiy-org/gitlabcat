<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 28.07.2018
 * Time: 23:03
 */

namespace App\Services;


use App\Services\Conversations\RegUserConversation;

class BotGuestService  extends AbstractBotService
{

    /**
     * @param string $message
     * @param bool $isOnChannel
     */
    public function proceedInboundMessage(string $message, bool $isOnChannel = true)
    {
        switch ($message) {
            case 'reg':
                if ($isOnChannel) {
                    $this->bot->reply('Такими вещами лучше занимться в личке...');
                    return;
                }
                $this->bot->startConversation(new RegUserConversation());
                break;
            case 'help':
                $message = 'Тебе доступна только одна команда: ' . "\n";
                $message .= '/reg - регистрация' . "\n\n";
                $this->bot->reply($message);
                break;
            default:
                $this->bot->reply('Мяу');
                break;
        }
    }


}