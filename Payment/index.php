<?php
use Dotenv\Dotenv;
require '../vendor/autoload.php';

require_once '../Database/db.php';

// Load environment variables
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

// Decode the JSON response
$result = json_decode($response, true);

// Show a bootstrap alert based on response
if (isset($result['ResponseCode']) && $result['ResponseCode'] === "0") {
  $message = "You’ve been prompted to enter your M-Pesa PIN on your phone. Complete the payment to proceed.";
  $alertType = "success";
} else {
  $errorMessage = isset($result['errorMessage']) ? $result['errorMessage'] : 'An unknown error occurred during STK Push.';
  $message = "Payment failed: " . $errorMessage;
  $alertType = "danger";
}

// Show the alert
echo "
<div class='container mt-4'>
  <div class='alert alert-$alertType alert-dismissible fade show' role='alert'>
    $message
    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  </div>
</div>
";


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment Options | Kenya Tech Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .payment-card {
      transition: transform 0.2s ease-in-out;
      cursor: pointer;
    }
    .payment-card:hover {
      transform: scale(1.03);
    }
    .selected {
      border: 3px solid #007bff;
      background-color: #e6f0ff;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center mb-4">Choose Your Payment Method</h2>
  <div class="row justify-content-center">

     <!-- M-Pesa Option -->
    <div class="col-md-4 mb-3">
      <div class="card payment-card text-center p-4" id="mpesaOption">
        <img src="../assets/img/logos/mpesa-logo.png" alt="M-Pesa" class="mb-3" width="60" height="auto">
        <h5>Pay with M-Pesa</h5>
      </div>
    </div>


    <!-- Credit Card Option -->
    <div class="col-md-4 mb-3">
      <div class="card payment-card text-center p-4" id="creditCardOption">
        <img src="../assets/img/logos/creditcard-logo.jpg" alt="Credit Card" class="mb-3" width="60" height="auto">
        <h5>Pay with Credit/Debit Card</h5>
      </div>
    </div>


  </div>

  <!-- Placeholder for selected form -->
  <div id="paymentForm" class="mt-4"></div>
</div>

<script>
  const creditCardOption = document.getElementById('creditCardOption');
  const mpesaOption = document.getElementById('mpesaOption');
  const paymentForm = document.getElementById('paymentForm');

  const clearSelection = () => {
    creditCardOption.classList.remove('selected');
    mpesaOption.classList.remove('selected');
  };

  creditCardOption.addEventListener('click', () => {
    clearSelection();
    creditCardOption.classList.add('selected');
    paymentForm.innerHTML = `
      <div class="card p-4">
        <h5 class="mb-3">Credit/Debit Card Details</h5>
        <form>
          <div class="mb-3">
            <label for="cardNumber" class="form-label">Card Number</label>
            <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
          </div>
          <div class="mb-3 row">
            <div class="col">
              <label for="expiry" class="form-label">Expiry Date</label>
              <input type="text" class="form-control" id="expiry" placeholder="MM/YY">
            </div>
            <div class="col">
              <label for="cvv" class="form-label">CVV</label>
              <input type="text" class="form-control" id="cvv" placeholder="123">
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100">Pay Now</button>
        </form>
      </div>`;
  });

  mpesaOption.addEventListener('click', () => {
    clearSelection();
    mpesaOption.classList.add('selected');
    paymentForm.innerHTML = `
   <div class="card p-4">
    <h5 class="mb-3">M-Pesa Payment</h5>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?event=<?= $_GET['event'] ?>&amount=<?= (float)$_GET['amount'] ?>" method="POST">
      <div class="mb-3">
        <label for="mpesaNumber" class="form-label">M-Pesa Phone Number</label>
        <input type="text" name="phone" class="form-control" id="mpesaNumber" placeholder="07XX XXX XXX">
      </div>
      <button type="submit" class="btn btn-success w-100">Pay with M-Pesa</button>
    </form>
  </div>`;
  });
</script>

</body>
</html>

