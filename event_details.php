<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

include './vendor/autoload.php';
require_once './Database/db.php';

session_start();

$dotenv = Dotenv::createImmutable('./');
$dotenv->load();

$client = new Google\Client;
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope("email");
$client->addScope("profile");
$url = $client->createAuthUrl();

// Check if the user is logged in

$event_id = $_GET['id'];

// Fetch event details from the database
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("s", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// check event details eg date, location, description, cost, mode, created_at

if ($event) {
    $event_date = $event['Event_Date'];
    $event_poster = $event['poster'];
    $event_title = $event['title'];
    $event_location = $event['location'];
    $event_description = $event['description'];
    $event_cost = $event['cost'];
    $event_mode = $event['mode'];
    $created_at = $event['created_at'];
} else {
    echo "Event not found.";
    exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kenya Tech Events - Your go-to platform for the latest tech events in Kenya. Stay updated with conferences, workshops, and networking opportunities.">
    <meta name="keywords" content="Kenya, Tech Events, Conferences, Workshops, Networking, Innovation, Technology, Startups">
    <meta name="author" content="Kenya Tech Events Team">
    <meta name="theme-color" content="#061a60">
    <link rel="icon" href="Assets/Img/logo-1.png" type="image/png">
    <link rel="apple-touch-icon" href="Assets/Img/logo-1.png" type="image/png">
    <title>kenya Tech Events</title>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="Assets/CSS/styles.css">
    <link rel="stylesheet" href="Assets/CSS/ribbon.css">
    <!-- Boostrap CSS -->
    <!-- Stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="stylesheet" href="./Assets/dist/css/bootstrap.min.css">

        <!-- jQuery (required for Slick.js) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Slick Carousel CSS & JS -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>


    <link rel="stylesheet" href="./Assets/CSS/rotate.css">
  
    <!-- boostrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    

     <!-- include font-awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-light container-fluid">
<!-- Header -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top position-sticky">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center justify-content-center" href="#"> <img src="Assets/Img/logo-1.png" alt="" class="img-fluid me-2" style="width: 39px; height: auto;"> <div class="d-flex flex-column">Kenya Tech Events<br><small style="font-size: x-small;">Shaping Kenya's Digital Future</small></div> </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto ms-auto my-2 my-lg-0 gap-4 navbar-nav-scroll">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">About</a>
                    </li>
                 
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Events
                         </a>
                         <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Upcoming Events</a></li>
                            <li><a class="dropdown-item" href="#">Past Events</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Featured Events</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="">Community</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Resources</a>
                    </li>
                </ul>
                <a href="<?=$url?>" class="btn text-white" style="background-color:#061a60;">Get Started</a>
            </div>
        </div>
    </nav>


    <!-- hero Buy tickets -->
      
<section class="hero-section py-5 text-center">
    <div class="container">
        <h1 class="display-4">Buy Tickets for <?= $event_title?></h1>
    </div>
</section>
 

<!-- Event Purchase Section -->
<section class="container my-4">

     <!-- event poster -->
   
     <div class="container">
      
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
        <img src="admin/<?=$event_poster?>" alt="Event Poster" class="img-fluid rounded shadow-sm">
        </div>
    </div>
    </div>
    </div>

  <div class="text-center mb-4">
    <h1 class="display-4">About </h1>
    <h2><?= $event_title?></h2>
    <p><?=$event_description?></p>

    <div class="container text-start mb-4">
        <p class="h4 my-3">Event Details</p>
            <p><strong>Date:</strong> <?= $event_date?></p>
            <p><strong>Location:</strong> <?= $event_location?></p>
            <p><strong>Mode:</strong> <?= $event_mode?></p>
            <p><strong>Created At:</strong> <?= $created_at?></p>
            <p><strong>Cost:</strong> <?= $event_cost?></p>
            <p><strong>Tickets Available:</strong> 100</p>
    </div>
  </div>
  <!-- Ticket Categories -->
  <div class="row g-4">
    <div class="col-md-4">


      <div class="card border-primary h-100">
        <div class="card-body text-center">
          <h5 class="card-title">General Admission</h5>
          <p class="card-text">Access to all main areas. Perfect for individuals.</p>
          <h6 class="mb-3">$25</h6>
          <a href="#" class="btn btn-primary">Buy Now</a>
        </div>
      </div>
    </div>


    <div class="col-md-4">
      <div class="card border-success h-100">
        <div class="card-body text-center">
          <h5 class="card-title">VIP Pass</h5>
          <p class="card-text">Exclusive seating, meals & backstage access.</p>
          <h6 class="mb-3">$75</h6>
          <a href="#" class="btn btn-success">Buy Now</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-warning h-100">
        <div class="card-body text-center">
          <h5 class="card-title">Group Package</h5>
          <p class="card-text">Best for teams and families. Up to 5 people.</p>
          <h6 class="mb-3">$100</h6>
          <a href="#" class="btn btn-warning">Buy Now</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Share Buttons -->
  <div class="text-center my-4">
    <h5>Share this event:</h5>
    <a href="#" class="btn btn-outline-primary me-2"><i class="bi bi-facebook"></i> Facebook</a>
    <a href="#" class="btn btn-outline-info me-2"><i class="bi bi-twitter-x"></i> Twitter</a>
    <a href="#" class="btn btn-outline-danger me-2"><i class="bi bi-instagram"></i> Instagram</a>
    <a href="#" class="btn btn-outline-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
  </div>
</section>













    <!-- footer -->
    <footer class="py-3 my-4">
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">About</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Events</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">News</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Community</a></li>
        </ul>
        <p class="text-center text-muted">© 2025 All rights reserved</p>
    </footer>


    <style>
        /* Hero Section */
.hero-section {
    background: linear-gradient(to right, #6a11cb, #2575fc);
    color: white;
    padding-top: 5rem;
    padding-bottom: 5rem;
    margin-bottom: 2rem;
}

.hero-section h1 {
    font-weight: bold;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
}

/* Event Poster */
img.img-fluid.rounded {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

img.img-fluid.rounded:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Event Details */
.text-center h2 {
    font-weight: 600;
    color: #333;
}

.text-center p {
    color: #555;
    max-width: 700px;
    margin: 0 auto;
}

/* Ticket Cards */
.card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.card .card-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.card .card-text {
    font-size: 0.95rem;
    color: #666;
}

.card h6 {
    font-size: 1.2rem;
    font-weight: bold;
}

/* Buttons */
.btn {
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: 500;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn:hover {
    opacity: 0.9;
}

    </style>


    <!-- Boostrap Js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- <script src="./assets/dist/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>