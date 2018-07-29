<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use App\Helpers\l;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GetTgWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gettgwh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get TG webhook';


    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $api_url = 'https://api.telegram.org/bot' . config('telegram_bot.token') . '/getWebhookInfo';

        try {
            $client = new Client([
                'verify' => false,
            ]);
            $response = $client->get($api_url);
            $responce_array = json_decode($response->getBody(),true);

            print_r($responce_array, true);
            l::debug('gettgwh', $responce_array);
        } catch (Exception $exception) {
            l::exc($this, $exception);
            echo '- error: ' . $exception->getMessage();
        }
    }

}
