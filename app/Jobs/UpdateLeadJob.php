<?php

namespace App\Jobs;

use App\Http\Controllers\Sends\UpdateLeadController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $options;

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
            $UpdateLeadController = new UpdateLeadController($this->options);
            $UpdateLeadController->sendDealToAmoCRM();
        }
    }
}
