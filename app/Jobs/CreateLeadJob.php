<?php

namespace App\Jobs;

use App\Http\Controllers\SendToAmoCRM;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $options;
    /**
     * Create a new job instance.
     */
    public function __construct($options)
    {
       $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $SendToAmoCRM = new SendToAmoCRM($this->options);
        $SendToAmoCRM->sendDealToAmoCRM();
    }
}
