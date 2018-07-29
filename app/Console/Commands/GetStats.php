<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use App\Helpers\GitLabApi;
use App\Helpers\StrHelper;
use App\Models\Project;
use App\Services\TgBotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getstats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get task stats';


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
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $gitLabApi = new GitLabApi(config('gitlab.api_key'));

        $tgService = new TgBotService();

        $projects = Project::where('gitlab_id', '>', 0)->get();
        foreach ($projects as $project) {
            $z = $gitLabApi->getOpenedEvents($project->gitlab_id, $start, $end);
            $opened = count($z);

            $y = $gitLabApi->getClosedEvents($project->gitlab_id, $start, $end);
            $closed = count($y);

            if ($opened > $closed) {
                $message = '📉';
                $message_itog = '⬇️ Тенденция не очень. ';
            } else {
                $message = '📈';
                $message_itog = '⬆️️ Все ништяк! ';
            }

            $forms = [
                'тикет', 'тикета', 'тикетов'
            ];

            $message .= ' Стата по проекту за неделю: ' . "\n";

            $message .= ' Открыто: *' . $opened . '* ' . StrHelper::pluralForm($opened, $forms) . "\n";
            $message .= ' Закрыто: *' . $closed . '* ' . StrHelper::pluralForm($closed, $forms) . "\n" . "\n";
            $message .= $message_itog . "\n";

            $tgService->doSay($project->channel, $message);
        }
    }
}
