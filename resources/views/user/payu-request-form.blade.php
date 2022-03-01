<form action="<?php echo $action; ?>" method="post" name="payuForm" >
    <input type="hidden" name="key" value="<?php echo $posted['key'] ?>" />
    <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
    <input type="hidden" name="txnid" value="<?php echo $posted['txnid'] ?>" />

    <input type="hidden" name="surl" value="{{ENV('SITEURL')}}payment/response" />   <!--Please change this parameter value with your success page absolute url like http://mywebsite.com/response.php. -->
    <input type="hidden" name="furl" value="{{ENV('SITEURL')}}payment/response" /><!--Please change this parameter value with your failure page absolute url like http://mywebsite.com/response.php. -->

    <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" />
    <input  type="hidden"name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" />
    <input type="hidden" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" />
    <input type="hidden" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" />
    <input type="hidden" name="productinfo" value="<?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?>"> 
    <input  type="hidden" name="lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" />
    <input type="hidden" name="curl" value="{{ENV('SITEURL')}}payment/cancel" /> <!-- Cancel URL -->
    <input type="hidden" name="address1" value="<?php echo (empty($shippingAddress['address_line_1'])) ? '' : $shippingAddress['address_line_1']; ?>" />
    <input type="hidden" name="address2" value="<?php echo (empty($shippingAddress['address_line_2'])) ? '' : $shippingAddress['address_line_2']; ?>" />
    <input type="hidden" name="city" value="<?php echo (empty($shippingAddress['city'])) ? '' : $shippingAddress['city']; ?>" />
    <input type="hidden" name="state" value="<?php echo (empty($shippingAddress['state'])) ? '' : $shippingAddress['state']; ?>" />
    <input type="hidden" name="country" value="<?php echo (empty($shippingAddress['country'])) ? '' : $shippingAddress['country']; ?>" />
    <input type="hidden" name="zipcode" value="<?php echo (empty($shippingAddress['pin_code'])) ? '' : $shippingAddress['pin_code']; ?>" />
    <input type="hidden" name="udf1" value="<?php echo (empty($posted['udf1'])) ? '' : $posted['udf1']; ?>" />
    <input type="hidden" name="udf2" value="<?php echo (empty($posted['udf2'])) ? '' : $posted['udf2']; ?>" />
    <input type="hidden" name="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3']; ?>" />
    <input type="hidden" name="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4']; ?>" />
    <input type="hidden" name="udf5" value="<?php echo (empty($posted['udf5'])) ? '' : $posted['udf5']; ?>" />
    <input type="hidden" name="pg" value="<?php echo (empty($posted['pg'])) ? '' : $posted['pg']; ?>" />
</form>

        <div>
            <center>
                <img src="{{{ URL::asset('images/loading.gif')}}}" />
            </center>
        </div>

<script>
          
var payuForm = document.forms.payuForm;
payuForm.submit();

</script>