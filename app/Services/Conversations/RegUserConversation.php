<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 10:28
 */

namespace App\Services\Conversations;


use App\Helpers\GitLabApi;
use App\Models\User;
use App\References\UserReference;
use BotMan\BotMan\Messages\Incoming\Answer;

class RegUserConversation extends AbstractConversation
{

    protected $user_id;

    public function run()
    {
        $this->rules = UserReference::RULES;
        $this->user_id = $this->getUserId();
        $this->askApiKey();
    }

    protected function askApikey()
    {
        $this->ask('Введите ваш GitLab apikey: ', function (Answer $answer) {
            $this->doAsk($answer, 'api_key', 'askApikey', 'doFinish');
            return;
        });
    }


    /**
     * Create user from with data
     */
    protected function doFinish()
    {
        $user = User::where('id', $this->user_id)->first();
        if (!$user) {
            $this->say('Странно, но я тебя не знаю!');
        }

        $api_key = array_get($this->data, 'api_key');


        $gl = new GitLabApi($api_key);
        $gl_data = $gl->getMe();

        $gl_id = array_get($gl_data, 'id');

        if ($gl_id) {
            $newuser = $user->update([
                'gitlab_id' => $gl_id,
                'api_key' => $api_key,
                'rights' => 10
            ]);
        } else {
            $newuser = false;
        }

        if ($newuser) {
            $this->say('Принимать!');
        } else {
            $this->say('Отрицать :( ');
        }
    }

}