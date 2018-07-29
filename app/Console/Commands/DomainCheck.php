<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use App\Helpers\StrHelper;
use App\Models\Domain;
use App\Services\TgBotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DomainCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domainchecker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check domains expiration';


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
        $today = Carbon::now();

        $tgService = new TgBotService();

        $domains = Domain::all();
        foreach ($domains as $domain) {
            $expiration_date = $this->getExpiration($domain);

            $dates_diff = $today->diff($expiration_date)->format("%r%a");
            if (($dates_diff <= 20) && ($dates_diff > 0)) {

                $days = StrHelper::pluralForm($dates_diff, 'день', 'дня', 'дней');

                $message = '🔥🔥🔥 Алярм! 🔥🔥🔥' . "\n" . $domain->name . ' протухнет через ' . $dates_diff . ' ' . $days . '! Не забудьте продлить!';
                $tgService->doSay($domain->project->channel, $message);
            } elseif ($dates_diff <= 0) {
                $message = '💀💀💀 Алярм! 💀💀💀' . "\n" . $domain->name . ' протух ' . $expiration_date->toDateString() . ' !!!! ПРОДЛИТЬ ASAP !!!';
                $tgService->doSay($domain->project->channel, $message);
            }
        }
    }

    /**
     * @param Domain $domain
     * @return Carbon
     */
    protected function getExpiration(Domain $domain)
    {
        $whois_command = 'whois ' . trim($domain->name);
        $whois_responce = shell_exec($whois_command);

        $start = strpos($whois_responce, 'Expiry Date');
        if (!$start || !($start > 0)) {
            $start = strpos($whois_responce, 'paid-till');
        }

        $end = strpos($whois_responce, "\n", $start);
        $len = $end - $start;

        $raw = explode(':', substr($whois_responce, $start, $len));
        $raw_date = trim(array_get($raw, 1));

        $ddate = explode(' ', $raw_date);
        $raw_date = trim(array_get($ddate, 0));

        $ddate = explode('T', $raw_date);

        $ready_date = trim(array_get($ddate, 0));
        $ready_date = str_replace('/', '.', $ready_date);

        $date = Carbon::parse($ready_date);

        $domain->update([
            'expiration' => $date
        ]);

        return $date;
    }
}
