<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;


use App\Models\Domain;
use App\Models\Project;
use App\References\DomainReference;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;


class AddDomainConversation extends AbstractConversation
{

    protected $cur_proj;
    public function run()
    {
        $this->rules = DomainReference::RULES;
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

        $question = Question::create('Для какого проекта домен?')
            ->fallback('Не понимаю вас')
            ->callbackId('selectWhomProject')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == 0) {
                    return;
                }

                $this->askDomainName((int)$selectedValue);
            }
        });
    }

    /**
     * @param int|null $project_id
     */
    protected function askDomainName(int $project_id = null)
    {
        if ($project_id) {
            $this->cur_proj = Project::where('id', $project_id)->first();
        }
        if (!$this->cur_proj) {
            $this->say('Нет такого проекта. Странно...');
            return;
        }

        $this->ask('Введите имя домена (без http://) : ', function (Answer $answer) {
            $this->doAsk($answer, 'name', 'askDomainName', 'doFinish');
            return;
        });
    }



    /**
     * Create domain from with data
     */
    protected function doFinish()
    {
        $newDomain = Domain::firstOrCreate([
            'project_id' => $this->cur_proj->id,
            'name' => $this->data['name'],
        ]);

        if ($newDomain) {
            $this->say('Теперь я отслеживаю протухание домена http://' . $newDomain->name);

        } else {
            $this->say('Не смог добавить домен');
        }
    }

}