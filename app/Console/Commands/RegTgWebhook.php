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

class RegTgWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regtgwh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reg TG webhook';


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

        $wh_url = config('app.url') . '/cb/';

        $api_url = 'https://api.telegram.org/bot' . config('telegram_bot.token') . '/setWebhook';

        try {
            $client = new Client([
                'verify' => false,
            ]);
            $response = $client->post($api_url, ['query' => [
                'url' => $wh_url
            ]]);
            $responce_array = json_decode($response->getBody(), true);

            print_r($responce_array, true);
            l::debug('regtgwh', $responce_array);
        } catch (Exception $exception) {
            l::exc($this, $exception);
            echo '- error: ' . $exception->getMessage();
        }
    }

}
