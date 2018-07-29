<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use App\Helpers\GitLabApi;
use App\Models\Project;
use App\Models\User;
use App\Services\TgBotService;
use Illuminate\Console\Command;

class GetOpenTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getopentasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run me!';


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
        $gitLabApi = new GitLabApi(config('gitlab.api_key'));

        $tgService = new TgBotService();

        $projects = Project::where('gitlab_id', '>', 0)->get();
        foreach ($projects as $project) {
            $sprint = $gitLabApi->getOpenIssues($project->gitlab_id, config('gitlab.sprint_backlog_tags'));
            $in_work = $gitLabApi->getOpenIssues($project->gitlab_id, config('gitlab.sprint_inwork_tags'));
            $in_test = $gitLabApi->getOpenIssues($project->gitlab_id, config('gitlab.sprint_intest_tags'));

            $assignees = [];

            $all_tasks = array_merge($sprint, $in_work, $in_test);
            foreach ($all_tasks as $sp) {
                $ass = array_get($sp, 'assignee');
                $ass_uname = array_get($ass, 'username');

                $cur = array_get($assignees, $ass_uname);
                $cnt = array_get($cur, 'cnt', 0);

                $cnt++;

                $assignees[$ass_uname] = [
                    'uname' => $ass_uname,
                    'cnt' => $cnt
                ];
            }

            usort($assignees, "self::cmp");

            $ass_leader = array_shift($assignees);
            $ass_leader_gitlab_user = array_get($ass_leader, 'uname');

            $user = User::where('gitlab_name', $ass_leader_gitlab_user)->first();
            $user_name = array_get($user, 'name') ? '@' . array_get($user, 'name') : $ass_leader_gitlab_user;

            $message = '🐈 🐈 🐈  Итак, на сегодняшний день мы имеем следующие подвисшие таски: ' . "\n";
            $message .= '*В спринте*: ' . count($sprint) . "\n";
            $message .= '*В разработке*: ' . count($in_work) . "\n";
            $message .= '*В тесте*: ' . count($in_test) . "\n";
            $message .= "\n";
            $message .= '🎈 🎈 Поздравляем главного коллекционера тасок: ' . $user_name . "🎈 🎈" . "\n";


            $tgService->doSay($project->channel, $message);
        }
    }


    /**
     *  Array sorting
     *
     * @param $a
     * @param $b
     * @return bool
     */
    private static function cmp($a, $b)
    {
        return $b['cnt'] > $a['cnt'];
    }

}
