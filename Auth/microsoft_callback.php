<?php
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Dotenv\Dotenv;

session_start();

if (isset($_GET['error']) || !isset($_GET['code'])) {
    echo 'Some Error Occurred';
    exit();
}

$authCode = $_GET['code'];

$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

$clientId = $_ENV['MICROSOFT_CLIENT_ID'] ?? null;
$clientSecret = $_ENV['MICROSOFT_CLIENT_SECRET'] ?? null;
$redirectUri = $_ENV['MICROSOFT_REDIRECT_URI'] ?? null;

if (!$clientId || !$clientSecret || !$redirectUri) {
    exit('Client ID, Client Secret, or Redirect URI not set in environment variables');
}

$client = new Client();
$apiUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

$data = [
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'code' => $authCode,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code',
];

try {
    $response = $client->post($apiUrl, [
        'form_params' => $data,
        'headers' => [
            'Accept' => 'application/json'
        ]
    ]);

    if ($response->getStatusCode() == 200) {
        $tokenData = json_decode($response->getBody()->getContents(), true);
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            exit('Failed to retrieve access token');
        }

        // Use the access token to get user information
        $userResponse = $client->get('https://graph.microsoft.com/v1.0/me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json'
            ]
        ]);

        if ($userResponse->getStatusCode() == 200) {
            $userData = json_decode($userResponse->getBody()->getContents(), true);
            $firstName = htmlspecialchars($userData['givenName'], ENT_QUOTES, 'UTF-8');
            $lastName = htmlspecialchars($userData['surname'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($userData['mail'], ENT_QUOTES, 'UTF-8');
            $profileImage = ''; // Microsoft Graph API does not provide profile image in this endpoint
            $phone = ''; // Microsoft Graph API does not provide phone in this endpoint
            $password = ''; // Set password to empty or handle accordingly
            $regDate = date('Y-m-d H:i:s'); // Current date and time

            // Insert or update user information in the database
            include('../Database/db.php');
            $stmt = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Phone, Email, Avatar, Pass, Reg_Date) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE First_Name = VALUES(First_Name), Last_Name = VALUES(Last_Name), Phone = VALUES(Phone), Avatar = VALUES(Avatar)");
            $stmt->bind_param("sssssss", $firstName, $lastName, $phone, $email, $profileImage, $password, $regDate);
            $stmt->execute();

            // Get the user ID
            $id = $stmt->insert_id ? $stmt->insert_id : $conn->query("SELECT SN FROM users WHERE Email = '$email'")->fetch_object()->SN;

            // Store user information in session
            $_SESSION['microsoft_auth'] = $id;

            // Redirect to home page
            header('Location: ../portal/home.php');
            exit();
        } else {
            exit('Failed to get user information');
        }
    } else {
        exit('Failed to get access token');
    }
} catch (RequestException $e) {
    echo 'Request Exception: ' . $e->getMessage();
    exit();
}
?>