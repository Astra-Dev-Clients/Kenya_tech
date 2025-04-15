<?php
use Dotenv\Dotenv;
require '../vendor/autoload.php';

require_once '../Database/db.php';

// Load environment variables
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

// Configs
$consumerKey = $_ENV['MPESA_CONSUMER_KEY'];
$consumerSecret = $_ENV['MPESA_CONSUMER_SECRET'];
$BusinessShortCode = '174379'; // Test short code
$Passkey = $_ENV['MPESA_PASS_KEY'];
$phone = $_POST['phone']; // format: 2547XXXXXXXX
$amount = $_GET['amount']; // Change as needed
$event_id = $_GET['event']; // Change as 

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("s", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();


// get event details
if ($event) {
  $event_date = $event['Event_Date'];
  $event_poster = $event['poster'];
  $event_title = $event['title'];
  $event_location = $event['location'];
  $event_description = $event['description'];
  $event_general = $event['General_Admission'];
  $event_vip = $event['VIP'];
  $event_early = $event['Early_Bird'];
  $event_mode = $event['mode'];
  $general_privileges = $event['General_Admission_previledges'];
  $vip_privileges = $event['VIP_previledges'];
  $early_privileges = $event['Early_Bird_previledges'];

  $created_at = $event['created_at'];
} else {
  echo "Failed to Fetch Event Details";
  exit;
}



// 1. Get Access Token
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials = base64_encode("$consumerKey:$consumerSecret");

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '.$credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$access_token = json_decode($response)->access_token;

// 2. Send STK Push
$stkUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode . $Passkey . $timestamp);

$data = [
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $amount,
  'PartyA' => $phone,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $phone,
  'CallBackURL' => 'https://fe4b-41-203-221-125.ngrok-free.app/clients/kenya_tech/payment/mpesa_callback.php',
  'AccountReference' =>  $event_title,
  'TransactionDesc' => 'Event Payment'
];

$ch = curl_init($stkUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Authorization: Bearer '.$access_token
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
