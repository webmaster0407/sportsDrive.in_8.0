<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrderController;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PaymentCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            Log::info("Cron is working fine new!");
            $key = "q1S51f";
            $salt = "EJQ6VWbg";
            $command = "verify_payment";
            $ordersNotCompletedInPast24Hours = Order::where("is_payment_proceed","Y")
                ->where("is_completed","Y")
                ->where("payment_status","!=",9)
                ->where("is_updated_by_cron","N")
                ->where('is_payment_proceed_on', '>=', Carbon::today()->subHours(24))
                ->get();
              
            $count = count($ordersNotCompletedInPast24Hours->toArray());
            foreach ($ordersNotCompletedInPast24Hours as $notCompletedOrder){
                $var1 = $notCompletedOrder->id;
                $hash_str = $key  . '|' . $command . '|' . $var1 . '|' . $salt ;
                $hash = strtolower(hash('sha512', $hash_str));
                $r = array('key' => $key , 'hash' =>$hash , 'var1' => $var1, 'command' => $command);
                $qs= http_build_query($r);
                $wsUrl = "https://info.payu.in/merchant/postservice?form=2";
                $c = curl_init();
                curl_setopt($c, CURLOPT_URL, $wsUrl);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
                curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
                $data = curl_exec($c);
                $jsonData = rtrim($data, "\0");
                $jsonData = json_decode($jsonData,true);
                $is_updated_by_cron = [
                    "is_updated_by_cron" => "Y",
                ];
                if($jsonData['status'] == 1){
                    $myRequest = new Request();
                    $myRequest->setMethod('POST');
                    $myRequest->request->add($jsonData['transaction_details'][$var1]);
                    if(trim($jsonData['transaction_details'][$var1]["status"])  == "success"){
                        OrderController::paymentResponseCron($myRequest);
                        Order::where('id', $var1)->update($is_updated_by_cron);
                    }elseif(trim($jsonData['transaction_details'][$var1]["status"])  == "pending"){
                        Log::info("order status is ".trim($jsonData['transaction_details'][$var1]["status"]));
                    }else{
                        OrderController::paymentResponseCron($myRequest);
                        Order::where('id', $var1)->update($is_updated_by_cron);
                    }
                }
            }
            Log::info("Total order processed : $count");
            exit;
        }catch (\Exception $exception){
            $data = [
                'input_params' => NULL,
                'action' => 'PAYMENT CRON RUN',
                'exception' => $exception->getMessage()
            ];
            Log::info(json_encode($data));
            abort(500);
        }
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
