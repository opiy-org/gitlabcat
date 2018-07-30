<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 29.07.2018
 * Time: 15:58
 */

namespace App\Services;

use App\Helpers\l;
use App\Models\Project;
use App\Models\User;

class GitLabWHService
{

    /**
     * @var TgBotService $tgService
     */
    protected $tgService;

    protected $whook_data;

    protected $type;
    protected $subtype;
    protected $object_attributes;
    protected $status;

    protected $project;

    public function __construct(array $whook_data)
    {
        $this->whook_data = $whook_data;
        $this->tgService = new TgBotService();
    }


    /**
     * Proceed inbound call from gitlab,
     * parse data and send to TG channel via bot
     */
    public function proceedWebhook()
    {
        l::debug('wh-data', $this->whook_data);

        $this->type = array_get($this->whook_data, 'object_kind');

        $this->object_attributes = array_get($this->whook_data, 'object_attributes');
        $this->subtype = array_get($this->object_attributes, 'action');
        $this->status = array_get($this->object_attributes, 'status');

        $g_project = array_get($this->whook_data, 'project.name');
        $project = Project::where('gitlab_name', $g_project)->first();
        if (!$project) {
            l::error($this, 'project unknown: ' . $g_project);
            return;
        }
        $this->project = $project;

        switch ($this->type) {
            case 'pipeline':
                $this->gotPipeline();
                break;

            case 'issue':
                $this->gotIssue();
                break;

            case 'note':
                $this->gotNote();
                break;

            case 'merge_request':
                $this->gotMr();
                break;

            default:
                $pr = Project::inRandomOrder()->first();
                $rand = rand(1, 100);
                if ($rand == 35) {
                    $this->tgService->doSay($pr->channel, 'Мяу.');
                } elseif ($rand == 64) {
                    $this->tgService->doSay($pr->channel, '🐈');
                } elseif ($rand == 87) {
                    $this->tgService->doSay($pr->channel, 'Мяу, йопта');
                }
                break;
        }
    }


    /**
     * Got info about pipeline
     */
    protected function gotPipeline()
    {
        $user_name = $this->getUsername();

        $ref = array_get($this->object_attributes, 'ref');
        $url = array_get($this->whook_data, 'commit.url');

        if ($this->status === 'success') {
            $message = '🏴󠁴󠁶󠁦󠁵󠁮󠁿 На *' . $ref . '* стенде проекта *' . $this->project->name . '* [труба-линия](' . $url . ') завершилась с успехом. ' . "\n\n" . 'Запускатором был: ' . $user_name . " \n\n";
            $this->tgService->doSay($this->project->channel, $message);
        } elseif ($this->status === 'failed') {
            $message = '🚾 На *' . $ref . '* стенде проекта *' . $this->project->name . '* [труба-линия](' . $url . ') успешно провалилась :(. ' . "\n\n" . 'Запускатором был: ' . $user_name . " \n\n";
            $this->tgService->doSay($this->project->channel, $message);
        }
    }


    /**
     * Got info about MR
     */
    protected function gotMr()
    {
        //todo other types
        if (!$this->subtype == 'open') return;

        $title = $this->clearTitle(array_get($this->object_attributes, 'title', ''));

        $message = 'Эй! Тут новый МР подвезли: ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') 💢' . "\n\n";
        $this->tgService->doSay($this->project->channel, $message);
    }

    /**
     * Got new note in issue
     */
    protected function gotNote()
    {
        $title = $this->clearTitle(array_get($this->whook_data, 'issue.title', ''));
        $user_name = $this->getUsername();

        $message = $user_name . ' прокомментировал таску ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') 🗯' . "\n\n";
        $message .= array_get($this->object_attributes, 'note');

        $this->tgService->doSay($this->project->channel, $message);
    }

    /**
     * Got new info about issue
     */
    protected function gotIssue()
    {
        $title = $this->clearTitle(array_get($this->object_attributes, 'title', ''));
        $user_name = $this->getUsername();

        $labels = '';
        $labels_array = array_get($this->whook_data, 'labels', []);
        foreach ($labels_array as $lbl) {
            $label_title = mb_ereg_replace("[\s\n\r]+", '', $lbl['title']);
            $labels .= '#' . $label_title . ' ';
        }

        $asignees = '';
        $asignees_array = array_get($this->whook_data, 'assignees', []);
        foreach ($asignees_array as $asd) {

            $asd_name = array_get($asd, 'username');
            $a_user = User::where('gitlab_name', $asd_name)
                ->first();
            $a_user_name = array_get($a_user, 'name') ? '@' . array_get($a_user, 'name') : $asd_name;

            $asignees .= $a_user_name . ' ';
        }

        $send = true;
        switch ($this->subtype) {
            case 'open':
                $message = 'Новая таска ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') 🆕' . "\n\n";
                $message .= 'Автор: ' . $user_name . "\n";
                break;
            case 'update':
                $changes = array_get($this->whook_data, 'changes', []);
                unset($changes['description'],
                    $changes['due-date'],
                    $changes['updated_at'],
                    $changes['updated_by_id']
                );

                if (!count($changes)) {
                    $send = false;
                }

                $title = str_replace('[', '', $title);
                $title = str_replace(']', ' ', $title);

                $message = 'Таска ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') обновлена ⬆' . "\n\n";

                break;
            case 'close':
                $message = 'Таска ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') закрыта ⛔' . "\n\n";
                break;

            case 'reopen':
                $message = 'Таска ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') переоткрыта 🔃' . "\n\n";
                break;

            default:
                $message = 'С таской ' . ' [' . $title . '](' . array_get($this->object_attributes, 'url') . ') что-то произошло ☣' . "\n\n";
                break;
        }

        if (count($labels_array)) {
            $message .= 'Тэги: ' . $labels . "\n";
        }

        if (count($asignees_array)) {
            $message .= 'Исполнители: ' . $asignees . "\n";
        }

        if ($send) {
            $this->tgService->doSay($this->project->channel, $message);
        }
    }

    /**
     * Parse & get username
     * @return mixed|string
     */
    private function getUsername()
    {
        $g_user = array_get($this->whook_data, 'user.username', '-none-');
        $user = User::where('gitlab_name', $g_user)->first();
        return array_get($user, 'name') ? '@' . array_get($user, 'name') : $g_user;
    }


    /**
     *  Remove TG special symbols from title
     * @param string $title
     * @return mixed|string
     */
    private function clearTitle(string $title)
    {
        $title = str_replace('[', '', $title);
        $title = str_replace(']', ' ', $title);
        return $title;
    }

}