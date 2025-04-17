<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Dotenv\Dotenv;

session_start();

// Step 1: Validate Google callback code
if (!isset($_GET["code"])) {
    exit("Login failed: No code provided.");
}

// Step 2: Restore event-related data from session
$event = $_SESSION['pending_event'] ?? null;
$amount = $_SESSION['pending_amount'] ?? null;
$event_title = $_SESSION['pending_title'] ?? null;

if (!$event || !$amount || !$event_title) {
    exit("Missing event information. Please try again.");
}

// Clear these values from session after use
unset($_SESSION['pending_event'], $_SESSION['pending_amount'], $_SESSION['pending_title']);

// Step 3: Load environment variables
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

// Step 4: Exchange code for access token
$client = new Client();
$tokenUrl = 'https://oauth2.googleapis.com/token';

$tokenData = [
    'client_id' => $_ENV['ATTENDEE_GOOGLE_CLIENT_ID'],
    'client_secret' => $_ENV['ATTENDEE_GOOGLE_CLIENT_SECRET'],
    'code' => $_GET['code'],
    'grant_type' => 'authorization_code',
    'redirect_uri' => $_ENV['ATTENDEE_GOOGLE_REDIRECT_URI'],
];

try {
    $tokenResponse = $client->post($tokenUrl, [
        'form_params' => $tokenData,
        'headers' => ['Accept' => 'application/json']
    ]);

    if ($tokenResponse->getStatusCode() !== 200) {
        exit("Failed to retrieve access token.");
    }

    $tokenBody = json_decode($tokenResponse->getBody()->getContents(), true);
    $accessToken = $tokenBody['access_token'];

    // Step 5: Use token to fetch user profile
    $userResponse = $client->get('https://www.googleapis.com/oauth2/v1/userinfo?alt=json', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json'
        ]
    ]);

    if ($userResponse->getStatusCode() !== 200) {
        exit("Failed to retrieve user profile.");
    }

    $userInfo = json_decode($userResponse->getBody()->getContents(), true);
    $givenName = htmlspecialchars($userInfo['given_name'], ENT_QUOTES, 'UTF-8');
    $familyName = htmlspecialchars($userInfo['family_name'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($userInfo['email'], ENT_QUOTES, 'UTF-8');
    $avatar = htmlspecialchars($userInfo['picture'], ENT_QUOTES, 'UTF-8');

    // Step 6: Handle DB logic
    include('../Database/db.php');

    // Check if user exists
    $stmt = $conn->prepare("SELECT SN FROM users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Existing user
        $_SESSION['google_auth'] = $user['SN'];
    } else {
        // New user — insert
        $defaultPassword = 'portfolio1234'; // Consider hashing if needed
        $insertStmt = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Email, Avatar, Pass, Reg_Date) VALUES (?, ?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("sssss", $givenName, $familyName, $email, $avatar, $defaultPassword);
        if ($insertStmt->execute()) {
            $_SESSION['google_auth'] = $insertStmt->insert_id;
        } else {
            exit("Error inserting new user.");
        }
    }

    // Step 7: Redirect user to the target page with event details
    header("Location: ./index.php?amount=$amount&event=$event&title=" . urlencode($event_title));
    exit;

} catch (RequestException $e) {
    exit('Error during authentication: ' . $e->getMessage());
}
