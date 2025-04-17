<?php
use Dotenv\Dotenv;
require '../vendor/autoload.php';
require_once '../Database/db.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Setup Google Client
$attendee = new Google\Client();
$attendee->setClientId($_ENV['ATTENDEE_GOOGLE_CLIENT_ID']);
$attendee->setClientSecret($_ENV['ATTENDEE_GOOGLE_CLIENT_SECRET']);
$attendee->setRedirectUri($_ENV['ATTENDEE_GOOGLE_REDIRECT_URI']);
$attendee->addScope("email");
$attendee->addScope("profile");
$url_attendee = $attendee->createAuthUrl();



// Start session
session_start();
// Check session for authentication
if (!isset($_SESSION['google_auth']) && !isset($_SESSION['email_auth'])) {
      if (isset($_GET['event']) && isset($_GET['amount'])) {
        $_SESSION['pending_event'] = $_GET['event'];
        $_SESSION['pending_amount'] = $_GET['amount'];
        $_SESSION['pending_title'] = $_GET['title'] ?? '';
    }
    header('Location: '.$url_attendee.'');
    exit();
}

// Get user ID from session
$id = $_SESSION['google_auth'] ?? $_SESSION['email_auth'];

// If you're using Google UID, change query accordingly
// For example: SELECT * FROM users WHERE google_uid = ?
$stmt = $conn->prepare("SELECT * FROM users WHERE SN = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_object();

// If user not found
if (!$details) {
    header('Location: ../index.php?error=user_not_found');
    exit();
}

// Sanitize output
$profileImage = htmlspecialchars($details->Avatar ?? '', ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars($details->First_Name ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($details->Email ?? '', ENT_QUOTES, 'UTF-8');

// Check if event and amount are set
if (!isset($_GET['event']) || !isset($_GET['amount'])) {
    header('Location: ../index.php');
    exit();
}


$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;
$event = isset($_GET['event']) ? htmlspecialchars($_GET['event']) : '';
$event_title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '';



// get event details
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
  'CallBackURL' => 'https://802c-41-212-26-7.ngrok-free.app/payment/mpesa_callback.php',
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
// refresh the page to show the payment status
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>M-Pesa Payment | Kenya Tech Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .payment-card {
      transition: transform 0.2s ease-in-out;
      cursor: pointer;
      border: 3px solid #007bff;
      background-color: #e6f0ff;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center mb-4">Pay with M-Pesa</h2>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card payment-card text-center p-4 mb-4">
        <img src="../assets/img/logos/mpesa-logo.png" alt="M-Pesa" class="mb-3" width="60">
        <h5>M-Pesa Payment for <?= $event_title ?> (KES <?= $amount ?>)</h5>
      </div>

      <div class="card p-4">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?event=<?= $event ?>&amount=<?= $amount ?>">
          <div class="mb-3">
            <label for="mpesaNumber" class="form-label">M-Pesa Phone Number</label>
            <input type="text" name="phone" class="form-control" id="mpesaNumber" placeholder="2547XX XXX XXX" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Pay with M-Pesa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- boostrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
