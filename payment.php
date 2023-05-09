<?php
require 'autoload.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

// ***************************************************************************
// ***************************************************************************
// grab our payment data first
$amount = $_POST['amount'];
$name = $_POST['name'];
$street = $_POST['street'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$card = $_POST['card'];
$card_exp_month = $_POST['card_exp_month'];
$card_exp_year = $_POST['card_exp_year'];
$ex_date = $card_exp_year.'-'.$card_exp_month;
$cvv = $_POST['cvv'];
$email = $_POST['email'];
// ***************************************************************************
// ***************************************************************************

define("AUTHORIZENET_LOG_FILE","phplog");

// Common setup for API credentials
  $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
  $merchantAuthentication->setName("Your Api Login ID");
  $merchantAuthentication->setTransactionKey("Your Transaction Key");
  $refId = 'ref' . time();

  // ***************************************************************************
  // ***************************************************************************
  // Create the payment data for a credit card
    $creditCard = new AnetAPI\CreditCardType();
    $creditCard->setCardNumber($card);
    $creditCard->setExpirationDate($ex_date);
    $creditCard->setCardCode("127");  // add in
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setCreditCard($creditCard);

    // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setEmail($email);
        // Order info
      $order = new AnetAPI\OrderType();
      $order->setInvoiceNumber("698712");
      $order->setDescription("Test Product");

    		// Set the customer's Bill To address add this section in
    	 $customerAddress = new AnetAPI\CustomerAddressType();
    	 $customerAddress->setFirstName($name);
    	 $customerAddress->setAddress($street);
    	 $customerAddress->setCity($city);
    	 $customerAddress->setState($state);
    	 $customerAddress->setZip($zip);
    	 $customerAddress->setCountry("USA");
    // end of customer billing info code

  // Create a transaction
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction");
    $transactionRequestType->setAmount($amount);
    $transactionRequestType->setOrder($order); // add in
    $transactionRequestType->setCustomer($customerData); // add in
  	$transactionRequestType->setBillTo($customerAddress); // add in
    $transactionRequestType->setPayment($paymentOne);
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setRefId($refId);
    $request->setTransactionRequest($transactionRequestType);
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    // ***************************************************************************
    // ***************************************************************************

if ($response != null)
{
  $tresponse = $response->getTransactionResponse();
  if (($tresponse != null) && ($tresponse->getResponseCode()=="1"))
  {
    header("Location: ./?m=1");
    die();
  }
  else
  {
    header("Location: ./?m=2");
      die();
  }
}
else
{
  header("Location: ./?m=2");
    die();
}
?>
