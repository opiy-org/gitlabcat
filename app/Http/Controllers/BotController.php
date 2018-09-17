<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 23.01.2018
 * Time: 2:44
 */

namespace App\Http\Controllers;

use App\Helpers\DFApi;
use App\Helpers\l;
use App\Models\User;
use App\References\UserReference;
use App\Services\BotAdminService;
use App\Services\BotCoderService;
use App\Services\BotGuestService;
use App\Services\TgBotService;
use App\Services\UserService;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;

class BotController
{

    /**
     * @var BotMan $botman
     */
    protected $botman;
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var string $bot_name
     */
    protected $bot_name;

    /**
     *  Process commands to bot
     *  (UI controller)
     *
     * @param Request|null $request
     * @return string
     */
    public function index(Request $request = null)
    {
        $tgService = new TgBotService($request);
        $this->botman = $tgService->init();
        $this->bot_name = config('telegram_bot.bot_name');

        try {
            $this->botman->hears('{phrase}', function (BotMan $bot, $phrase) {
                //get user from inbound message
                $user_obj = $bot->getUser();
                $id = $user_obj->getId();
                $username = $user_obj->getUsername();

                //detect is user registered in our db?
                $user = false;
                if ($username) {
                    /** @var User $user */
                    $user = User::where('name', $username)->first();
                } elseif ($id) {
                    /** @var User $user */
                    $user = User::where('uid', $id)->first();
                }


                $userService = new UserService($user);
                //update user data if it need
                $this->user = $userService->actualizeUserInfo([
                    'uid' => $id,
                    'name' => $username,
                ]);

                //is message got on channel or in private?
                $is_on_channel = false;
                if (strpos($phrase, '@' . $this->bot_name)) {
                    $phrase = str_replace('@' . $this->bot_name, '', $phrase);
                    $is_on_channel = true;
                }
                $replied = false;

                //proceed message/command
                preg_match('/^\/([\W\w0-9\s]+)$/mui', $phrase, $matches);
                $command = array_get($matches, 1);

                //not command message and quiet mode ON - do nothing
                if (!$command and config('telegram_bot.quiet_mode')) {
                    return;
                } elseif (!$command) {
                    $df = new DFApi();
                    $ai_answer = $df->query($phrase);
                    if ($ai_answer) {
                        $bot->reply($ai_answer);
                        $replied = true;
                    }
                }

                //not registered? go away!
                if (!$user) {
                    $bot->reply('Кто вы такие? Я вас не звал');
                    $replied = true;
                    l::debug('Forbidden for :', $username, $this);
                    return;
                }

                $executed = false;
                if ($command) {
                    //l::debug('got command', $command);

                    //if admin or coder and command
                    if (($user->is_coder) && in_array($command, UserReference::CODER_COMMANDS)) {
                        $coderBotSerivce = new BotCoderService($user, $bot);
                        $coderBotSerivce->proceedInboundMessage($command, $is_on_channel);
                        $executed = true;
                    }

                    //if admin command and user is admin
                    if ($user->is_admin && in_array($command, UserReference::ADMIN_COMMANDS)) {
                        $adminBotSerivce = new BotAdminService($user, $bot);
                        $adminBotSerivce->proceedInboundMessage($command, $is_on_channel);
                        $executed = true;
                    }

                    //guest commands
                    if ((!$user->is_admin && !$user->is_coder) && in_array($command, UserReference::GUEST_COMMANDS)) {
                        $guestBotSerivce = new BotGuestService($user, $bot);
                        $guestBotSerivce->proceedInboundMessage($command, $is_on_channel);
                        $executed = true;
                    }


                }

                if (!$executed && !$replied) {
                    if (rand(0, 10) == 5) {
                        $bot->reply('Мяу, йопта!');
                    } else {
                        $bot->reply('Мяу');
                    }
                }

            });

            $this->botman->listen();
        } catch (\Exception $exception) {
            l::exc($this, $exception, 'TG callback exception');
        }

        //required by TG
        return 'ok';
    }


}