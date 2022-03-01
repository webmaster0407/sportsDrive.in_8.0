<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendProductPromotionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $promotionId;
    protected $customers;
    /**
     * Create a new job instance.
     * SendProductPromotionEmail constructor.
     * @param $promotionId
     * @param $customers
     */
    public function __construct($promotionId,$customers)
    {
        $this->promotionId = $promotionId;
        $this->customers = $customers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customers = $this->customers;
        foreach ($customers as $customer){
            ProductPromotionEmail::dispatch($this->promotionId,$customer);
        }
    }
}
