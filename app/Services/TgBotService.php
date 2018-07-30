<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 16.02.18
 * Time: 11:46
 */

namespace App\Services;


use App\Helpers\l;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Http\Curl;
use BotMan\Drivers\Telegram\TelegramDriver;
use Symfony\Component\HttpFoundation\Request;

class TgBotService
{

    /**
     * @var BotMan $botman
     */
    protected $botman;

    public function __construct(Request $request = null)
    {
        if (empty($request)) {
            $request = Request::createFromGlobals();
        }

        $config = [
            'telegram' => [
                'token' => config('telegram_bot.token'),
            ]
        ];

        $this->botman = BotManFactory::create($config, new LaravelCache(), $request);
        $this->botman->setDriver(new TelegramDriver($request, $config, new Curl()));
    }


    /**
     *  Return BotMan instance
     *
     * @return BotMan
     */
    public function init()
    {
        return $this->botman;
    }


    /**
     * Send message to channel
     *
     * @param string $chat_id
     * @param string $message
     * @return bool
     */
    public function doSay(string $chat_id, string $message)
    {
        try {
            $responce = $this->botman->sendRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
                //'reply_markup' => json_encode($reply),
            ]);

        } catch (\Exception $exception) {
            l::exc($this, $exception);
            return false;
        }

        return true;
    }


}