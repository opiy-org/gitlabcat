<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;

use App\Helpers\l;
use App\Models\Instance;
use App\Models\Project;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class DelInstanceConversation extends AbstractConversation
{

    protected $proj;

    public function run()
    {
        $this->askWhom();
    }

    protected function askWhom()
    {
        $buttons = [];
        $projects = Project::get();
        if (!count($projects)) {
            $this->say('Нет проектов. Сначала создайте проект.');
            return;
        }

        foreach ($projects as $project) {
            $buttons[] = Button::create('💡 ' . $project->name)->value($project->id);
        }

        $question = Question::create('Из какого проекта удалить инстанс?')
            ->fallback('Не понимаю вас')
            ->callbackId('selectWhomProject')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->askInstanceName((int)$selectedValue);
            }
        });
    }

    /**
     * Select instance
     *
     * @param int|null $project_id
     */
    protected function askInstanceName(int $project_id = null)
    {
        if ($project_id) {
            $this->proj = Project::where('id', $project_id)->first();
        }
        if (!$this->proj) {
            $this->say('Проект не существует. Странно...');
            return;
        }

        $buttons = [];
        $instances = Instance::where('project_id', $this->proj->id)->get();
        if (!count($instances)) {
            $this->say('У проекта ' . $this->proj->name . ' еще нет инстансов!');
            return;
        }

        foreach ($instances as $instance) {
            $buttons[] = Button::create('🖥️  ' . $instance->name)->value($instance->id);
        }


        $question = Question::create('Какой инстанс удаляем ?')
            ->fallback('Не понимаю вас')
            ->callbackId('shouldDelInstances')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->doFinish($selectedValue);
            }
        });
    }


    /**
     *  Del instance
     *
     * @param string $instanceId
     * @throws \Exception
     */
    protected function doFinish(string $instanceId)
    {
        $instance = Instance::where('project_id', $this->proj->id)
            ->where('id', (int)$instanceId)->first();
        if (!$instance) {
            $this->say('Нет такого инстанса в проекте. Странно...');
            return;
        }
        try {
            $del = $instance->delete();
        } catch (\Exception $exception) {
            l::exc($this, $exception);
        }

        if ($del) {
            $this->say('Удолил!!111');

        } else {
            $this->say('Не смог удалить');
        }
    }

}