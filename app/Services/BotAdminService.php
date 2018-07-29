<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 28.07.2018
 * Time: 23:03
 */

namespace App\Services;


use App\Models\Project;
use App\Models\User;
use App\Services\Conversations\AddDomainConversation;
use App\Services\Conversations\AddInstanceConversation;
use App\Services\Conversations\AddProjectConversation;
use App\Services\Conversations\AddUserConversation;
use App\Services\Conversations\DelDomainConversation;
use App\Services\Conversations\DelInstanceConversation;
use App\Services\Conversations\DelProjectConversation;
use App\Services\Conversations\DelUserConversation;

class BotAdminService extends AbstractBotService
{

    /**
     * @param string $message
     * @param bool $isOnChannel
     */
    public function proceedInboundMessage(string $message, bool $isOnChannel = true)
    {
        switch ($message) {
            case '/addinstance':
                $this->bot->startConversation(new AddInstanceConversation());
                break;

            case '/delinstance':
                $this->bot->startConversation(new DelInstanceConversation());
                break;
            case '/adddomain':
                $this->bot->startConversation(new AddDomainConversation());
                break;

            case '/deldomain':
                $this->bot->startConversation(new DelDomainConversation());
                break;

            case '/projects':
                $retval = $this->getProjectsList();
                $this->bot->reply($retval);
                break;

            case '/addproject':
                $this->bot->startConversation(new AddProjectConversation());
                break;

            case '/delproject':
                $this->bot->startConversation(new DelProjectConversation());
                break;

            case '/users':
                if ($isOnChannel) {
                    $this->bot->reply('Такими вещами лучше занимться в личке...');
                }
                $retval = $this->getUsersList();
                $this->bot->reply($retval);
                break;

            case '/adduser':
                if ($isOnChannel) {
                    $this->bot->reply('Такими вещами лучше занимться в личке...');
                }
                $this->bot->startConversation(new AddUserConversation());
                break;

            case '/deluser':
                if ($isOnChannel) {
                    $this->bot->reply('Такими вещами лучше занимться в личке...');
                }
                $this->bot->startConversation(new DelUserConversation());
                break;

            case '/help':
                $message = '/adddomain - добавить домен ' . "\n";
                $message .= '/deldomain  - удалить домен' . "\n";

                $message .= '/addinstance - добавить инстанс ' . "\n";
                $message .= '/delinstance  - удалить инстанс' . "\n";

                $message .= '/projects - список проектов ' . "\n";
                $message .= '/addproject - добавить проект ' . "\n";
                $message .= '/delproject  - удалить проект' . "\n";

                $message .= '--------------------------------- ' . "\n";

                $message .= '/users - список юзеров ' . "\n";
                $message .= '/adduser - добавить юзера ' . "\n";
                $message .= '/deluser  - удалить ' . "\n";

                $this->bot->reply($message);
                break;
        }
    }


    /**
     *  Get users list
     *
     * @return string
     */
    protected function getUsersList()
    {
        $users = User::orderBy('name')->get();
        if (!count($users)) {
            return 'Нет ни одного юзера. Это очень странно...';
        }

        $retval = '';
        foreach ($users as $user) {
            $retval .= '👤 ' . $user->name . "\t (" . $user->gitlab_name . ") \n";
        }

        return $retval;
    }


    /**
     *  Get projects list
     *
     * @return string
     */
    protected function getProjectsList()
    {
        $projects = Project::orderBy('name')->get();
        if (!count($projects)) {
            return 'Нет ни одного проекта.';
        }

        $retval = '';
        foreach ($projects as $project) {
            $retval .= '💡 ' . $project->name . "\n";
        }

        return $retval;
    }

}