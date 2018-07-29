<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 28.07.2018
 * Time: 23:03
 */

namespace App\Services;


use App\Helpers\GitLabApi;
use App\Helpers\Meow;
use App\Models\Domain;
use App\Models\Instance;

class BotCoderService extends AbstractBotService
{


    /**
     * @param string $message
     * @param bool $isOnChannel
     */
    public function proceedInboundMessage(string $message, bool $isOnChannel = true)
    {
        switch ($message) {
            case 'issues':
                $retval = $this->getIssues();
                $this->bot->reply($retval, [
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => true
                ]);
                break;
            case 'instances':
                $retval = $this->getInstanceList();
                $this->bot->reply($retval);
                break;
            case 'domains':
                $retval = $this->getDomainList();
                $this->bot->reply($retval, [
                    'parse_mode' => 'Markdown',
                    'disable_web_page_preview' => true
                ]);
                break;
            case 'help':
                $message = 'Привет, ' . $this->user->name . '!' . "\n" . "\n";
                $message .= 'Тебе доступны следующие комманды: ' . "\n";
                $message .= '/issues - список твоих тасок ' . "\n";
                $message .= '--------------------------------- ' . "\n";
                $message .= '/domains - список доменов ' . "\n";
                $message .= '/instances - список инстансов ' . "\n";
                $message .= '/showmethecat - 🐾';
                $this->bot->reply($message);
                break;

            case 'showmethecat':
                $cat = new Meow();
                $data = $cat->getCat();
                $message = 'Ну, ок.' . "\n" . array_get($data, 'file');
                $this->bot->reply($message);
                break;
        }
    }


    /**
     *  Get user issues
     *
     * @return string
     */
    protected function getIssues()
    {
        $gla = new GitLabApi($this->user->api_key);
        $issues = $gla->getMyIssues();

        if (count($issues) > 0) {
            $message = 'Твои тасочки на текущий момент таковы: ' . "\n\n";

            foreach ($issues as $issue) {
                $labels = '';
                $labels_data = array_get($issue, 'labels', []);
                foreach ($labels_data as $label_d) {
                    $l_title = mb_ereg_replace("[\s\n\r]+", '', $label_d);
                    $labels .= '#' . $l_title . ' ';
                }

                $title = array_get($issue, 'title');
                $title = str_replace('[', '*', $title);
                $title = str_replace(']', '* ', $title);
                $id = array_get($issue, 'id');

                $message .= ' [' . $id . '](' . array_get($issue, 'web_url') . ').  ' . $title . "\n" . $labels . "\n\n";
            }
        } else {
            $message = 'У тебя нет тасок! o_O ' . "\n";
        }

        return $message;
    }

    /**
     *  Get list of monitoring instances
     *
     * @return null|string
     */
    protected function getInstanceList()
    {
        $instances = Instance::orderBy('project_id')->get();
        if (!count($instances)) {
            return 'Пока еще нет ни одного инстанса';
        }

        $retval = '';
        foreach ($instances as $instance) {
            $retval .= '     💡' . $instance->project->name . "\n" . '🖥️ ' . $instance->name . "\n";
        }

        return $retval;
    }


    /**
     *  Get domains list
     *
     * @return null|string
     */
    protected function getDomainList()
    {
        $domains = Domain::orderBy('project_id')->get();
        if (!count($domains)) {
            return 'Пока еще нет доменов';
        }

        $retval = '';
        foreach ($domains as $domain) {
            $retval .= '🌎 http://' . $domain->name . "\t   ⏰ " . $domain->expiration . "\n";
        }

        return $retval;
    }


}