<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 19.10.17
 * Time: 18:24
 */


namespace App\Console\Commands;

use Illuminate\Console\Command;

class Tmp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test....';


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
        $retval = '';
        return $retval;
    }


}
