<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;


use App\Models\User;
use App\References\UserReference;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class AddUserConversation extends AbstractConversation
{

    protected $user_id;

    public function run()
    {
        $this->rules = UserReference::RULES;
        $this->askName();
    }

    protected function askName()
    {
        $this->ask('Введите username (без @): ', function (Answer $answer) {
            $this->doAsk($answer, 'name', 'askName', 'askGlName');
            return;
        });
    }

    protected function askGlName()
    {
        $this->ask('Введите username в гитлабе: ', function (Answer $answer) {
            $this->doAsk($answer, 'gitlab_name', 'askGlName', 'isCorrect');
            return;
        });
    }


    protected function isCorrect()
    {
        $this->say('Юзер будет создан со следующими данными: ');
        $this->say(print_r($this->data, true));

        $question = Question::create('Всё верно?')
            ->fallback('Не понимаю вас')
            ->callbackId('isCorrectData')
            ->addButtons([
                Button::create('Да')->value('да'),
                Button::create('Нет, давайте заново')->value('нет'),
                Button::create('Нет, передумал создавать')->value('выход'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                if ($selectedValue == 'да') {
                    $this->createUser();
                } elseif ($selectedValue == 'нет') {
                    $this->run();
                } else {
                    return;
                }

            }
        });
    }


    /**
     * Create user from with data
     */
    protected function createUser()
    {
        $this->data['name'] = trim($this->data['name'], '@# ');
        $this->data['rights'] = 10;

        /** @var User $newuser */

        $newuser = User::create($this->data);

        if ($newuser) {
            $this->say('Юзер успешно создан!');

        } else {
            $this->say('Не смог добавить юзера');
        }
    }

}