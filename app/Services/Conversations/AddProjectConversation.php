<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;

use App\Models\Project;
use App\References\ProjectReference;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Validator;

class AddProjectConversation extends AbstractConversation
{


    public function run()
    {
        $this->rules = ProjectReference::RULES;
        $this->askName();
    }

    protected function askName()
    {
        $this->ask('Введите навзание проекта: ', function (Answer $answer) {
            $this->doAsk($answer, 'name', 'askName', 'askChannel');
            return;
        });
    }


    protected function askChannel()
    {
        $this->ask('Введите навзание канала проекта: ', function (Answer $answer) {
            $this->doAsk($answer, 'channel', 'askChannel', 'askGlName');
            return;
        });
    }


    protected function askGlName()
    {
        $this->ask('Введите навзание проекта в гитлабе: ', function (Answer $answer) {
            $this->doAsk($answer, 'gitlab_name', 'askGlName', 'isCorrect');
            return;
        });
    }


    protected function isCorrect()
    {
        $this->say('Проект будет создан со следующими данными: ');
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
                    $this->createProj();
                } elseif ($selectedValue == 'нет') {
                    $this->run();
                } else {
                    return;
                }

            }
        });
    }


    /**
     * Create proj from with data
     */
    protected function createProj()
    {
        $this->data['channel'] = trim($this->data['channel'], '@# ');

        $newproj = Project::firstOrCreate(
            $this->data
        );

        if ($newproj) {
            $this->say('Проект успешно создан!');
        } else {
            $this->say('Не смог создать проект');
        }
    }

}