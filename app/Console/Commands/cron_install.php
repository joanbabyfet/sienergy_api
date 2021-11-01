<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class cron_install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '安装应用';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time_start = microtime(true);

        //執行腳本,将任务放入异步队列中
        //Artisan::call("jwt:secre");

        $size = memory_get_usage();
        $unit = array('b','kb','mb','gb','tb','pb');
        $memory = @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
        $time = microtime(true) - $time_start;
        $date = date('Y-m-d H:i:s');
        echo "[{$date}] {$this->signature} Done in $time seconds\t $memory\n";
    }
}
