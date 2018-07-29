<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 25.01.18
 * Time: 15:11
 */

namespace App\Services\Conversations;


use App\Helpers\l;
use App\Models\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Facades\Validator;

abstract class AbstractConversation extends Conversation
{

    protected $rules;
    protected $data;

    /**
     *  Ask string param
     *
     * @param Answer $answer
     * @param string $param
     * @param string $ifFail
     * @param string $ifOk
     */
    protected function doAsk(Answer $answer, string $param, string $ifFail, string $ifOk)
    {
        $value = trim($answer->getText());

        $validator = Validator::make([$param => $value], [$param => array_get($this->rules, $param, 'nullable')]);
        if ($validator->fails()) {
            $error_data = json_decode($validator->errors(), true);
            $error = "\n" . implode("\n", array_get($error_data, $param));
            $this->say('Ошибка! ' . $error);

            $this->$ifFail();
            return;
        }

        $this->say('Ок, пусть будет ' . $value);
        $this->data[$param] = $value;
        $this->$ifOk();
        return;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getUser()
    {
        $retval = null;

        try {
            if ($this->bot) {

                $username = trim($this->bot->getUser()->getUsername());
                if ($username) {
                    $retval = User::where('name', $username)->first();
                }

            }

        } catch (\Exception $exception) {
            l::exc($this, $exception);
        }

        return $retval;
    }


    /**
     * @return mixed|null
     */
    public function getUserId()
    {
        $oper = $this->getUser();
        if ($oper) {
            return $oper->id;
        }

        return null;
    }

}