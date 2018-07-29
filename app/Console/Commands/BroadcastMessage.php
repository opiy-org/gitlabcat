<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use App\Models\Project;
use App\Services\TgBotService;
use Illuminate\Console\Command;

class BroadcastMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bmessage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to all projects channels';

    protected $message;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        if (!$this->message) {
            return;
        }

        $tgService = new TgBotService();

        $channels = Project::unique('channel')->pluck('channel')->toArray();
        foreach ($channels as $channel) {
            $tgService->doSay($channel, $this->message);
        }
    }

}
