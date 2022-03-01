<?php

$status = $_POST["status"];
$firstname = $_POST["firstname"];
$amount = $_POST["amount"]; //Please use the amount value from database
$txnid = $_POST["txnid"];
$posted_hash = $_POST["hash"];
$key = $_POST["key"];
$productinfo = $_POST["productinfo"];
$email = $_POST["email"];
/*
 * User Defined Parameter
 */
$udf1 = $_POST["udf1"];
$udf2 = $_POST["udf2"];
$udf3 = $_POST["udf3"];
$udf4 = $_POST["udf4"];
$udf5 = $_POST["udf5"];
$salt = "6igOC55O"; //Please change the value with the live salt for production environment




$dateCurrent = date("Y-m-d H:i:s");
$fp = fopen('hdfcLog.txt', 'a+');
fwrite($fp, "============================$dateCurrent=========================\n\n");
fwrite($fp, "Response\n");
fwrite($fp, print_r($_POST, true));
fwrite($fp, "\r\n\r\n");
fclose($fp);

//Validating the reverse hash
If (isset($_POST["additionalCharges"])) {
    $additionalCharges = $_POST["additionalCharges"];
    $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
} else {

    $retHashSeq = $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
}
$hash = hash("sha512", $retHashSeq);

if ($hash != $posted_hash) {
    echo "Transaction has been tampered. Please try again";
} else {

    echo "<h3>Thank You, " . $firstname . ".Your order status is " . $status . ".</h3>";
    echo "<h4>Your Transaction ID for this transaction is " . $txnid . ".</h4>";
}
?>	