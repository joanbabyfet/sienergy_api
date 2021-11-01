<?php

namespace App\Jobs;

use App\models\mod_sys_mail;

class job_send_mail extends Job
{
    protected $to           = [];
    protected $subject      = '';
    protected $view_data    = [];
    protected $view         = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        //這里暫不用靜態定義,避免報錯
        $this->to           = isset($data['to']) ? $data['to']:[];
        $this->subject      = isset($data['subject']) ? $data['subject']:'';
        $this->view_data    = isset($data['view_data']) ? $data['view_data']:[];
        $this->view         = isset($data['view']) ? $data['view']:'';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->to))   //有收件人才發送
        {
            mod_sys_mail::_send_mail([
                'to'        => $this->to,
                'subject'   => $this->subject,
                'view'      => $this->view,
                'view_data' => $this->view_data,
            ]);
        }
    }
}
