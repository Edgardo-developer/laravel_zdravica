<?php

namespace App\Jobs;

use App\Http\Controllers\SendToAmoCRM;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        if ($this->options){
            Log::info('LeadDBID: '.$this->options['leadDBId'] . ' CREATE');
            Log::info('This is job of creating ' . date('H:i:s'));
            $SendToAmoCRM = new SendToAmoCRM($this->options);
            $SendToAmoCRM->sendDealToAmoCRM();
        }
    }
}
