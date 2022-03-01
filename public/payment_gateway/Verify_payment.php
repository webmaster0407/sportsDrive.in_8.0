<?php
// Merchant key here as provided by Payu

if(isset($_GET['txtid']) && !empty($_GET['txtid'])){

$key = "q1S51f";
$salt = "EJQ6VWbg";

$command = "verify_payment";

$var1 = $_GET['txtid'];


$hash_str = $key  . '|' . $command . '|' . $var1 . '|' . $salt ;
$hash = strtolower(hash('sha512', $hash_str));


    $r = array('key' => $key , 'hash' =>$hash , 'var1' => $var1, 'command' => $command);
    $qs= http_build_query($r);
//$wsUrl = "https://test.payu.in/merchant/postservice.php?form=1";
$wsUrl = "https://info.payu.in/merchant/postservice?form=1";

                $dateCurrent = date("Y-m-d H:i:s");
                $fp = fopen('hdfcLog.txt', 'a+');
                fwrite($fp, "============================$dateCurrent=========================\n\n");
                fwrite($fp, "Curl Data Request\n");                
                fwrite($fp, print_r($r,true));
                fwrite($fp, "\r\n\r\n");
                fclose($fp);

    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $wsUrl);
    curl_setopt($c, CURLOPT_POST, 1);
    curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    $o = curl_exec($c);
    
    echo "<pre>";
    print_r( $o);
    echo 123;
    die;
    if (curl_errno($c)) {
      $sad = curl_error($c);
      throw new Exception($sad);
    }
    curl_close($c);

    $valueSerialized = @unserialize($o);
    $dateCurrent = date("Y-m-d H:i:s");
                $fp = fopen('hdfcLog.txt', 'a+');
                fwrite($fp, "============================$dateCurrent=========================\n\n");
                fwrite($fp, "Payment Response\n");                
                fwrite($fp, print_r($valueSerialized,true));
                fwrite($fp, "\r\n\r\n");
                fclose($fp);
    if($o === 'b:0;' || $valueSerialized !== false) {
      print_r($valueSerialized);
    }
    print_r($o);
    
}else{
    echo "Please enter transaction ID.";
}
?>

