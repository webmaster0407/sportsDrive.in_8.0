<?php
// Merchant key here as provided by Payu
$MERCHANT_KEY = "Wc1WCl"; //Please change this value with live key for production
$hash_string = '';
// Merchant Salt as provided by Payu
$SALT = "6igOC55O"; //Please change this value with live salt for production
// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://test.payu.in";

$siteURL = "https://www.sportsdrive.in/payment_gateway/";
$action = '';

$baseUrl = "https://www.sportsdrive.in/payment_gateway/";

$posted = array();
if (!empty($_POST)) {

    $payu_key = $_POST['key'];
    $txnid = $_POST['txnid'];
    $amount = $_POST['amount'];
    $product_info = $_POST['productinfo'];
    $firstName = $_POST['firstname'];
    $email = $_POST['email'];
    $udf1 = $_POST['udf1'];
    $udf2 = $_POST['udf2'];
    $udf3 = $_POST['udf3'];
    $udf4 = $_POST['udf4'];
    $udf5 = $_POST['udf5'];


    foreach ($_POST as $key => $value) {
        $posted[$key] = $value;
    }
}

$formError = 0;

if (empty($posted['txnid'])) {
    // Generate random transaction id
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
    $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|||||";


if (empty($posted['hash']) && sizeof($posted) > 0) {
    if (
            empty($posted['key']) || empty($posted['txnid']) || empty($posted['amount']) || empty($posted['firstname']) || empty($posted['email']) || empty($posted['phone']) || empty($posted['productinfo'])
    ) {
        $formError = 1;
    } else {

        $hashVarsSeq = explode('|', $hashSequence);
        echo '<pre>';


        foreach ($hashVarsSeq as $hash_var) {
            // echo $hash_var."<br>";
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }

        $hash_string .= $SALT;


        $hash = strtolower(hash('sha512', $hash_string));

        $dateCurrent = date("Y-m-d H:i:s");
        $fp = fopen('hdfcLog.txt', 'a+');
        fwrite($fp, "============================$dateCurrent=========================\n\n");
        fwrite($fp, "Request\n");
        fwrite($fp, print_r($hash_string, true));
        fwrite($fp, "\r\n\r\n");
        fwrite($fp, print_r($posted, true));
        fwrite($fp, "\r\n\r\n");
        fwrite($fp, print_r($hash, true));
        fwrite($fp, "\r\n\r\n");
        fclose($fp);

        $action = $PAYU_BASE_URL . '/_payment';
    }
} elseif (!empty($posted['hash'])) {
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}
?>
<html>
    <head>
        <script>
            var hash = '<?php echo $hash ?>';
            function submitPayuForm() {
                if (hash == '') {
                    return;
                }
                var payuForm = document.forms.payuForm;
                payuForm.submit();
            }
        </script>
    </head>
    <body onload="submitPayuForm()">
        <h2>PayU Form</h2>
        <br/>
<?php if ($formError) { ?>
            <span style="color:red">Please fill all mandatory fields.</span>
            <br/>
            <br/>
<?php } ?>
        <form action="<?php echo $action; ?>" method="post" name="payuForm" >
            <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
            <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
            <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />

            <input type="hidden" name="surl" value="<?php echo $baseUrl; ?>response.php" />   <!--Please change this parameter value with your success page absolute url like http://mywebsite.com/response.php. -->
            <input type="hidden" name="furl" value="<?php echo $baseUrl; ?>response.php" /><!--Please change this parameter value with your failure page absolute url like http://mywebsite.com/response.php. -->





            <table>
                <tr>
                    <td><b>Mandatory Parameters</b></td>
                </tr>
                <tr>
                    <td>Amount: </td>
                    <td><input name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
                    <td>First Name: </td>
                    <td><input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
                </tr>
                <tr>
                    <td>Email: </td>
                    <td><input name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
                    <td>Phone: </td>
                    <td><input name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
                </tr>
                <tr>
                    <td>Product Info: </td>
                    <td colspan="3"><textarea name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
                </tr>




                <tr>
                    <td><b>Optional Parameters</b></td>
                </tr>
                <tr>
                    <td>Last Name: </td>
                    <td><input name="lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" /></td>
                    <td>Cancel URI: </td>
                    <td><input name="curl" value="" /></td>
                </tr>
                <tr>
                    <td>Address1: </td>
                    <td><input name="address1" value="<?php echo (empty($posted['address1'])) ? '' : $posted['address1']; ?>" /></td>
                    <td>Address2: </td>
                    <td><input name="address2" value="<?php echo (empty($posted['address2'])) ? '' : $posted['address2']; ?>" /></td>
                </tr>
                <tr>
                    <td>City: </td>
                    <td><input name="city" value="<?php echo (empty($posted['city'])) ? '' : $posted['city']; ?>" /></td>
                    <td>State: </td>
                    <td><input name="state" value="<?php echo (empty($posted['state'])) ? '' : $posted['state']; ?>" /></td>
                </tr>
                <tr>
                    <td>Country: </td>
                    <td><input name="country" value="<?php echo (empty($posted['country'])) ? '' : $posted['country']; ?>" /></td>
                    <td>Zipcode: </td>
                    <td><input name="zipcode" value="<?php echo (empty($posted['zipcode'])) ? '' : $posted['zipcode']; ?>" /></td>
                </tr>
                <tr>
                    <td>UDF1: </td>
                    <td><input name="udf1" value="<?php echo (empty($posted['udf1'])) ? '' : $posted['udf1']; ?>" /></td>
                    <td>UDF2: </td>
                    <td><input name="udf2" value="<?php echo (empty($posted['udf2'])) ? '' : $posted['udf2']; ?>" /></td>
                </tr>
                <tr>
                    <td>UDF3: </td>
                    <td><input name="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3']; ?>" /></td>
                    <td>UDF4: </td>
                    <td><input name="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4']; ?>" /></td>
                </tr>
                <tr>
                    <td>UDF5: </td>
                    <td><input name="udf5" value="<?php echo (empty($posted['udf5'])) ? '' : $posted['udf5']; ?>" /></td>
                    <td>PG: </td>
                    <td><input name="pg" value="<?php echo (empty($posted['pg'])) ? '' : $posted['pg']; ?>" /></td>
                </tr>
                <tr>
<?php if (!$hash) { ?>
                        <td colspan="4"><input type="submit" value="Submit" /></td>
<?php } ?>
                </tr>
            </table>
        </form>
    </body>
</html>
