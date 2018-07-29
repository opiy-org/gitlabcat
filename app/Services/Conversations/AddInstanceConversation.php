<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;


use App\Models\Instance;
use App\Models\Project;
use App\References\InstanceReference;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class AddInstanceConversation extends AbstractConversation
{

    protected $cur_proj;

    public function run()
    {
        $this->rules = InstanceReference::RULES;
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

        $buttons[] = Button::create('<- Назад')->value(0);

        $question = Question::create('Для какого проекта инстанс?')
            ->fallback('Не понимаю вас')
            ->callbackId('selectWhomProject')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == 0) {
                    return;
                }

                $this->askInstanceName((int)$selectedValue);
            }
        });
    }

    /**
     * @param int|null $project_id
     */
    protected function askInstanceName(int $project_id = null)
    {
        if ($project_id) {
            $this->cur_proj = Project::where('id', $project_id)->first();
        }
        if (!$this->cur_proj) {
            $this->say('Нет такого проекта. Странно...');
            return;
        }

        $this->ask('Введите имя инстанса: ', function (Answer $answer) {
            $this->doAsk($answer, 'name', 'askInstanceName', 'askInstanceUrl');
            return;
        });
    }

    /**
     * @param int|null $project_id
     */
    protected function askInstanceUrl(int $project_id = null)
    {
        if ($project_id) {
            $this->cur_proj = Project::where('id', $project_id)->first();
        }
        if (!$this->cur_proj) {
            $this->say('Нет такого проекта. Странно...');
            return;
        }

        $this->ask('Введите url инстанса: ', function (Answer $answer) {
            $this->doAsk($answer, 'url', 'askInstanceUrl', 'doFinish');
            return;
        });
    }


    /**
     * Create instance from with data
     */
    protected function doFinish()
    {
        $newInstance = Instance::firstOrCreate([
            'project_id' => $this->cur_proj->id,
            'name' => $this->data['name'],
            'url' => $this->data['url'],
        ]);

        if ($newInstance) {
            $this->say('Теперь я отслеживаю здоровье' . $newInstance->name);

        } else {
            $this->say('Не смог добавить инстанс');
        }
    }

}