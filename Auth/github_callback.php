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

$client = new Client();
$apiUrl = 'https://github.com/login/oauth/access_token';

$data = [
    'client_id' => $_ENV['GITHUB_CLIENT_ID'],
    'client_secret' => $_ENV['GITHUB_CLIENT_SECRET'],
    'code' => $authCode,
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
        $accessToken = $tokenData['access_token'];

        // Use the access token to get user information
        $userResponse = $client->get('https://api.github.com/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json'
            ]
        ]);

        if ($userResponse->getStatusCode() == 200) {
            $userData = json_decode($userResponse->getBody()->getContents(), true);
            $firstName = htmlspecialchars($userData['name'], ENT_QUOTES, 'UTF-8');
            $lastName = ''; // GitHub does not provide last name, set it to empty or handle accordingly
            $profileImage = htmlspecialchars($userData['avatar_url'], ENT_QUOTES, 'UTF-8');
            $phone = ''; // GitHub does not provide phone, set it to empty or handle accordingly
            $password = ''; // Set password to empty or handle accordingly
            $regDate = date('Y-m-d H:i:s'); // Current date and time

            // Get the user's email addresses if not provided in the initial response
            $email = htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8');
            if (empty($email)) {
                $emailResponse = $client->get('https://api.github.com/user/emails', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Accept' => 'application/json'
                    ]
                ]);

                if ($emailResponse->getStatusCode() == 200) {
                    $emailData = json_decode($emailResponse->getBody()->getContents(), true);
                    foreach ($emailData as $emailInfo) {
                        if ($emailInfo['primary'] && $emailInfo['verified']) {
                            $email = htmlspecialchars($emailInfo['email'], ENT_QUOTES, 'UTF-8');
                            break;
                        }
                    }
                }
            }

            if (empty($email)) {
                echo 'No verified primary email found.';
                exit();
            }

            // Insert or update user information in the database
            include('../Database/db.php');
            $stmt = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Email, Avatar, Pass, Reg_Date) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE First_Name = VALUES(First_Name), Last_Name = VALUES(Last_Name), Avatar = VALUES(Avatar)");
            $stmt->bind_param("ssssss", $firstName, $lastName, $email, $profileImage, $password, $regDate);
            $stmt->execute();

            // Get the user ID
            $id = $stmt->insert_id ? $stmt->insert_id : $conn->query("SELECT SN FROM users WHERE Email = '$email'")->fetch_object()->SN;

            // Store user information in session
            $_SESSION['github_auth'] = $id;

            // Redirect to home page
            header('Location: ../portal/home.php');
            exit();
        }
    }
    echo 'Failed to get user information';
    exit();
} catch (RequestException $e) {
    echo 'Request Exception: ' . $e->getMessage();
    exit();
}
?>
