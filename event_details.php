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
  $event_general = $event['General_Admission'];
  $event_vip = $event['VIP'];
  $event_early = $event['Early_Bird'];
  $event_mode = $event['mode'];
  $general_privileges = $event['General_Admission_previledges'];
  $vip_privileges = $event['VIP_previledges'];
  $early_privileges = $event['Early_Bird_previledges'];

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
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
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
                <a href="<?=$url?>" class="btn text-white" style="background-color:#061a60;">Create Event</a>
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
<section class="container my-5">

  <!-- Hero Section -->
  <div class="text-center  mb-5 d-flex flex-column align-items-center justify-content-center">
    <img src="assets/img/events/<?=$event_poster?>" alt="Event Poster" class="img-fluid rounded-4 shadow mb-4" style="max-height: 400px; object-fit: cover;">
    <h1 class="display-5 fw-bold text-primary"><?= $event_title ?></h1>
    <p class="lead text-muted"><?= $event_description ?></p>
  </div>

  <!-- Event Info -->
  <div class="row justify-content-center mb-5">
    <div class="col-md-10 col-lg-8">
      <div class="bg-light rounded-4 shadow-sm p-4">
        <div class="row">
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">Date</p>
            <h5><?= $event_date ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">Location</p>
            <h5><?= $event_location ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">Mode</p>
            <h5><?= $event_mode ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">Created At</p>
            <h5><?= $created_at ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">General Admission</p>
            <h5>Ksh <?= number_format($event_general, 2) ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">VIP</p>
            <h5>Ksh <?= number_format($event_vip, 2) ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">Early Bird</p>
            <h5>Ksh <?= number_format($event_early, 2) ?></h5>
          </div>
          <div class="col-sm-6 mb-3">
            <p class="mb-1 text-muted">Tickets Available</p>
            <h5><span class="badge bg-success fs-6">100</span></h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Ticket Options -->
  <h3 class="text-center fw-bold mb-4 text-secondary">Ticket Categories</h3>
  <div class="row row-cols-1 row-cols-md-3 g-4">

    <!-- General Admission -->
    <div class="col">
      <div class="card h-100 shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 text-center">
          <h5 class="fw-semibold text-primary">General Admission</h5>
        </div>
        <div class="card-body text-center">
          <h2 class="text-primary mb-3">Ksh <?= number_format($event_general, 2) ?> <small class="text-muted fs-6">/ticket</small></h2>
          <ul class="list-unstyled mb-4">
            <?php foreach (explode("\n", $general_privileges) as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>

          <a href="./payment/index.php?event=<?= $event_id ?>&amount=<?= $event_general ?>&title=<?= urlencode($event_title) ?>&category=General" class="btn btn-outline-primary">Get Ticket</a>
        </div>
      </div>
    </div>

    <!-- VIP -->
    <div class="col">
      <div class="card h-100 shadow-sm border-warning rounded-4">
        <div class="card-header bg-warning text-white py-3 text-center">
          <h5 class="fw-semibold">VIP</h5>
        </div>
        <div class="card-body text-center">
          <h2 class="text-warning mb-3">Ksh <?= number_format($event_vip, 2) ?> <small class="text-muted fs-6">/ticket</small></h2>
          <ul class="list-unstyled mb-4">
            <?php foreach (explode("\n", $vip_privileges) as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>
  
          <a href="./payment/index.php?event=<?= $event_id ?>&amount=<?= $event_vip ?>&title=<?= urlencode($event_title) ?>&category=VIP" class="btn btn-outline-danger">Get VIP Ticket</a>
        </div>
      </div>
    </div>

    <!-- Early Bird -->
    <div class="col">
      <div class="card h-100 shadow-sm border-primary rounded-4">
        <div class="card-header bg-primary text-white py-3 text-center">
          <h5 class="fw-semibold">Early Bird</h5>
        </div>
        <div class="card-body text-center">
          <h2 class="text-primary mb-3">Ksh <?= number_format($event_early, 2) ?> <small class="text-warning fs-6">/ticket</small></h2>
          <ul class="list-unstyled mb-4 text-dark">
            <?php foreach (explode("\n", $early_privileges) as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>

          <a href="./payment/index.php?event=<?= $event_id ?>&amount=<?= $event_early ?>&title=<?= urlencode($event_title) ?>&category=Early Bird" class="btn btn-outline-warning">Get Early Bird Ticket</a>
        </div>
      </div>
    </div>

  </div>

</section>



  <!-- Share Buttons -->
  <div class="text-center my-4">
  <h5>Share this event:</h5>
  <a id="facebook-share" href="javascript:void(0)" target="_blank" class="btn btn-outline-primary me-2">
  <i class="bi bi-facebook"></i> Facebook
  </a>
  <a id="twitter-share" href="#" target="_blank" class="btn btn-outline-info me-2">
    <i class="bi bi-twitter-x"></i> Twitter
  </a>
  <a id="instagram-share" href="#" target="_blank" class="btn btn-outline-danger me-2">
    <i class="bi bi-instagram"></i> Instagram
  </a>
  <a id="whatsapp-share" href="#" target="_blank" class="btn btn-outline-success">
    <i class="bi bi-whatsapp"></i> WhatsApp
  </a>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const pageUrl = encodeURIComponent(window.location.href);
    const pageTitle = encodeURIComponent(document.title);
    const eventTitle = <?php echo json_encode($event_title); ?>;

    document.getElementById('facebook-share').href =
      `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`;

    document.getElementById('twitter-share').href =
      `https://twitter.com/intent/tweet?url=${pageUrl}&text=${pageTitle}`;

    document.getElementById('instagram-share').href =
      `https://www.instagram.com/`;

    document.getElementById('whatsapp-share').href =
      `https://api.whatsapp.com/send?text=Buy Tickets for ${encodeURIComponent(eventTitle)}%0A${pageUrl}`;
  });
</script>


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

</body>

</html>