<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;

use App\Helpers\l;
use App\Models\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class DelUserConversation extends AbstractConversation
{
    public function run()
    {
        $this->askWhom();
    }

    /**
     * Select bot
     */
    protected function askWhom()
    {
        $buttons = [];
        $users = User::whereRaw('id > (select min(id) from users)')
            ->get();
        if (!count($users)) {
            $this->say('У вас некого удалять. Сначала создайте юзера.');
            return;
        }

        foreach ($users as $user) {
            $buttons[] = Button::create('👤 ' . $user->name . ' ' . $user->phone)->value($user->name);
        }

        $question = Question::create('Кого удалять будем?')
            ->fallback('Не понимаю вас')
            ->callbackId('shouldDelNames')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->delUser($selectedValue);
            }
        });
    }

    /**
     * Delete user
     * @param string $userName
     */
    protected function delUser(string $userName)
    {
        $user = User::where('name', $userName)->first();
        if (!$user) {
            $this->say('Нет такого юзера. Странно...');
            return;
        }
        try {
            $del = $user->delete();
        } catch (\Exception $exception) {
            l::exc($this, $exception);
        }

        if ($del) {
            $this->say('Нет больше такого юзера.');

        } else {
            $this->say('Не смог удалить юзера');
        }
    }

}