<?php
/**
 * Created by PhpStorm.
 * Project: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;

use App\Helpers\l;
use App\Models\Project;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class DelProjectConversation extends AbstractConversation
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
        $projects = Project::get();
        if (!count($projects)) {
            $this->say('У вас нечего удалять. Сначала создайте проект.');
            return;
        }

        foreach ($projects as $project) {
            $buttons[] = Button::create('💡 ' . $project->name)->value($project->name);
        }

        $question = Question::create('Какой проект удалять будем?')
            ->fallback('Не понимаю вас')
            ->callbackId('shouldDelNames')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->delProject($selectedValue);
            }
        });
    }


    /**
     * Delete project
     * @param string $projectName
     */
    protected function delProject(string $projectName)
    {
        $project = Project::where('name', $projectName)->first();
        if (!$project) {
            $this->say('Нет такого проекта. Странно...');
            return;
        }

        try {
            $del = $project->delete();
        } catch (\Exception $exception) {
            l::exc($this, $exception);
        }

        if ($del) {
            $this->say('Нет больше такого проекта.');

        } else {
            $this->say('Не смог удалить проект');
        }
    }

}