<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use App\Helpers\l;
use App\Models\Instance;
use App\Services\TgBotService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class InstanceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instancechecker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check instances health!';


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
        $instances = Instance::all();

        $tgService = new TgBotService();

        foreach ($instances as $instance) {
            if (!$this->checkHealth($instance)) {
                $message = '😿 😿 😿 Sick my cat :( 😿 😿 😿' . "\n" .
                    'Инстанс ' . $instance->name . ' приболел. Проверьте, что там с ним такое...';
                $tgService->doSay($instance->project->channel, $message);
            }
        }
    }


    /**
     *  Ping instance and parse response
     *
     * @param Instance $instance
     * @return bool
     */
    protected function checkHealth(Instance $instance)
    {
        $client = new Client([
            'verify' => false,
        ]);

        try {
            $responseData = $client->request('GET', $instance->url);
            $responce_array = json_decode($responseData->getBody(), true);

            if (array_get($responce_array, 'status', false) === 'success') {
                return true;
            }
        } catch (\Exception $exception) {
            l::exc($this, $exception);
        }

        return false;
    }


}
