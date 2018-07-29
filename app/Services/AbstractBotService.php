<?php
/**
 * Created by PhpStorm.
 * User: opiy
 * Date: 28.07.2018
 * Time: 23:03
 */

namespace App\Services;

use App\Models\User;
use BotMan\BotMan\BotMan;

abstract class AbstractBotService
{

    /**
     * @var User $user
     */
    protected $user;
    /**
     * @var BotMan $bot
     */
    protected $bot;

    /**
     * BotService constructor.
     * @param User $user
     * @param BotMan $bot
     */
    public function __construct(User $user, BotMan $bot)
    {
        $this->user = $user;
        $this->bot = $bot;
    }

    /**
     * @param string $message
     * @param bool $isOnChannel
     */
    public function proceedInboundMessage(string $message, bool $isOnChannel = true)
    {
    }


}