<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;

use App\Helpers\l;
use App\Models\Domain;
use App\Models\Project;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class DelDomainConversation extends AbstractConversation
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

        $question = Question::create('Из какого проекта удалить домен?')
            ->fallback('Не понимаю вас')
            ->callbackId('selectWhomProject')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->askDomainName((int)$selectedValue);
            }
        });
    }

    /**
     * Select domain
     *
     * @param int|null $project_id
     */
    protected function askDomainName(int $project_id = null)
    {
        if ($project_id) {
            $this->proj = Project::where('id', $project_id)->first();
        }
        if (!$this->proj) {
            $this->say('Проект не существует. Странно...');
            return;
        }

        $buttons = [];
        $domains = Domain::where('project_id', $this->proj->id)->get();
        if (!count($domains)) {
            $this->say('У проекта ' . $this->proj->name . ' еще нет доменов!');
            return;
        }

        foreach ($domains as $domain) {
            $buttons[] = Button::create('🌎  ' . $domain->name)->value($domain->id);
        }

        $question = Question::create('Какой домен удаляем ?')
            ->fallback('Не понимаю вас')
            ->callbackId('shouldDelDomains')
            ->addButtons($buttons);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->doFinish($selectedValue);
            }
        });
    }


    /**
     *  Del domain
     *
     * @param string $domainId
     * @throws \Exception
     */
    protected function doFinish(string $domainId)
    {
        $domain = Domain::where('project_id', $this->proj->id)
            ->where('id', (int)$domainId)->first();
        if (!$domain) {
            $this->say('Нет такого домена в проекте. Странно...');
            return;
        }
        try {
            $del = $domain->delete();
        } catch (\Exception $exception) {
            l::exc($this, $exception);
        }

        if ($del) {
            $this->say('Удолил!!111');
        } else {
            $this->say('Не смог удалить домен');
        }
    }

}