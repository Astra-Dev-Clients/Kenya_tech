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
                <a href="<?=$url?>" class="btn text-white" style="background-color:#061a60;">Create an Event</a>
            </div>
        </div>
    </nav>
 
    <!-- Hero Section -->
    <div class="container-fluid hero position-relative " style="background: rgb(255,255,255);
      background: linear-gradient(73deg, rgba(255,255,255,1) 8%, #061a6058 87%); height: 100vh;">
      <div class="overlay"></div>

            <!-- overlay -->
        <!-- <section class="main-container">
                <div class="main">
                  <div class="big-circle">
                    <div class="icon-block">
                      <img src="./assets/img/logos/html.png" alt="web design icon" />
                    </div>
                    <div class="icon-block">
                      <img src="./assets/img/logos/python.png" alt="game design icon" />
                    </div>
                    <div class="icon-block">
                      <img src="./assets/img/logos/css.png" alt="game dev icon" />
                    </div>
                    <div class="icon-block">
                      <img src="./assets/img/logos/c++.png" alt="ui-ux icon" />
                    </div>
                  </div>
                  <div class="circle">
                    <div class="icon-block">
                      <img src="./assets/img/logos/java.png" alt="app icon" />
                    </div>
                    <div class="icon-block">
                      <img src="./assets/img/logos/chatgpt.png" alt="blockchain icon" />
                    </div>
                    <div class="icon-block">
                      <img src="./assets/img/logos/kotlin.png" alt="ar-vr icon" />
                    </div>
                    <div class="icon-block">
                      <img src="./assets/img/logos/sql.png" alt="artificial intelligence icon" />
                    </div>
                  </div>
                  <div class="center-logo">
                    <img src="./assets/img/logos/vscode.png" style="border-radius:10px" alt="logo" />
                  </div>
                </div>
        </section> -->


        <div class="container px-4 py-5">




            <div class="row flex-lg-row-reverse align-items-center justify-content-center g-2 py-5">
                <div id="carouselExampleIndicators" class="carousel slide col-10 col-sm-8 col-lg-6" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="Assets/Img/hero-1.jpg" class="d-block mx-lg-auto img-fluid" style="scale: 70%; border-radius: 2rem 0 2rem;" alt="Helping" loading="lazy">
                        </div>
                        <div class="carousel-item">
                            <img src="Assets/Img/hero-2.jpg" class="d-block mx-lg-auto img-fluid" style="scale: 70%; border-radius: 2rem 0 2rem;" alt="Cross" loading="lazy">
                        </div>
                        <div class="carousel-item">
                            <img src="Assets/Img/hero-3.jpg" class="d-block mx-lg-auto img-fluid" style="scale: 70%; border-radius: 2rem 0 2rem;" alt="Baptism" loading="lazy">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold text-body-emphasis lh-2 mb-3">Empowering Tech Innovation in Kenya</h1>
                    <p class="lead">Bringing together tech enthusiasts, startups, and industry leaders to collaborate, learn, and drive the future of technology. </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a type="button" class="btn text-white btn-lg px-4 me-md-2" style="background-color: #061a60;" href="#events">Explore Events<span class="blink"></span></a>
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4">Learn More</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Our partners -->
    <h2 class="text-center fw-bold lead my-5">Our Partners / Our Clients</h2>
    <div class="container my-5">
        <section class="customer-logos slider d-flex justify-content-center">
            <div class="slide"><a href="#"><img src="https://mma.prnewswire.com/media/1671157/Hedera_Black_Logo.jpg?p=twitter"></a></div>
            <div class="slide"><a href="#"><img src="https://files.readme.io/695d6b0a6c7d3a71b65689505b4710ccb787e8cef0e40132ebd749dc9c1d5ee3-Full-Logo_Slogan_Colour.png"></a></div>
            <div class="slide"><a href="#"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSlKV47wBBy95j2eRdBTxlLXcGv-SJSod1YXQ&s"></a></div>
            <div class="slide"><a href="#"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRkouMHiIzU49kQjd4-GbvY6kI0vG2KsUbtMw&s"></a></div>
            <div class="slide"><a href="#"><img src="https://mma.prnewswire.com/media/2354935/Daytona_logotype_black_Logo.jpg?p=facebook"></a></div>
        </section>
    </div>
    



    <!-- add image overlay to hero section between back and the contnet -->
      <style>
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:url(Assets/Img/nairobi-hack-1.jpg);
            background-position: center;
            background-size: cover;
            background-blend-mode: overlay;
            opacity: 0.5;
            z-index: -2;
        }

                .blink {
        display: inline-block;
        width: 10px;
        height: 10px;
        margin-left: 8px;
        background-color: red;
        border-radius: 50%;
        animation: blinker 1s infinite;
        }

        @keyframes blinker {
        50% {
            opacity: 0;
        }
        }


        
      </style>


    <!-- Adaptive learning section -->
    <section class="section my-20">
        <div class="container">
            <div class="flex flex-col lg:flex-row items-center gap-x-20 gap-y-12">
                <!-- Emergency image -->
                <div>
                    <img class="hidden lg:block" src="./assets/Img/team-4.jpg" style="width: 550px; height: auto; border-radius: 2rem 0 2rem;" alt="About section image">
                    <img class="lg:hidden" src="./assets/img/team-3.jpg" style="width: 550px; height: auto;" alt="About section image mobile">
                </div>
                <!-- Emergency Contents -->
                <div class="flex flex-col gap-y-4 text-start lg:text-start">
                    <!-- Title -->
                    <h4 class="text-sm  font-bold" style="color: #061a60;">Join us today and be part of Kenya's tech revolution!</h4>
                    <!-- Subtitle -->
                    <p class="text-slate-800 text-4xl leading-snug font-bold sm:max-w-screen-sm">Connecting Innovators, Shaping the Future</p>
                    <!-- Description -->
                    <p class="max-w-lg  text-start text-slate-800/50">At Kenya Tech Events, we are dedicated to fostering innovation and collaboration within Kenya's vibrant tech ecosystem. Our platform brings together the best tech conferences, workshops, hackathons, and networking events across the country, making it easy for tech enthusiasts, professionals, and innovators to stay connected.</p>
                    <!-- Button Call to Action -->
                    <a class="btn btn-lg w-50 text-white d-flex align-items-center justify-content-center" href="#contact" style="background-color: #061a60; text-decoration: none;">Join Our Community</a>
                </div>
            </div>
        </div>
    </section>


    <!-- Events Section -->
<section class="container my-5">
    <h2 class="text-center fw-bold display-5 my-5" id="events">Discover Kenya’s Top Tech Events</h2>
    <p class="text-center text-muted">Stay updated with the latest tech conferences, hackathons, and networking events across Kenya. Filter events to find the perfect opportunity!</p>

    <!-- Filter Section -->
    <div class="row my-5">
        <div class="col-md-4">
            <input type="text" class="form-control" placeholder="Search events...">
        </div>




        <div class="col-md-2">
            <select class="form-select">
                <option selected>Category</option>
                <option>Workshops</option>
                <option>Hackathons</option>
                <option>Conferences</option>
                <option>Meetups</option>
                <option>Startups</option>
            </select>
        </div>



        <div class="col-md-2">
            <input type="date" class="form-control">
        </div>
        <div class="col-md-2">
            <select class="form-select">
                <option selected>Location</option>
                <option>Nairobi</option>
                <option>Mombasa</option>
                <option>Kisumu</option>
                <option>Eldoret</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select">
                <option selected>Sort By</option>
                <option>Newest</option>
                <option>Most Popular</option>
                <option>Upcoming</option>
            </select>
        </div>
    </div>

    <!-- Events Cards -->
    <div class="row">


             <?php

                $sql = "SELECT * FROM events ORDER BY Event_Date ASC LIMIT 10";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Extract data from the row
                        $poster = $row['poster'];
                        $title = $row['title'];
                        $location = $row['location'];
                        $description = $row['description'];
                        $event_date = date('F j, Y', strtotime($row['Event_Date']));
                        $event_id = $row['event_id'];
                        $mode = $row['mode'];
                        $organizer_id = $row['organizer_id'];
                        $created_at = $row['created_at'];
                
                        echo '<div class="col-md-4">';
                        echo '  <div class="card">';
                        echo '      <img src="assets/img/events/' . htmlspecialchars($poster) . '" class="card-img-top" alt="Event Image">';
                        echo '      <div class="card-body">';
                        echo '          <h5 class="card-title">' . htmlspecialchars($title) . '</h5>';
                        echo '          <p class="card-text text-capitalize"><i class="bi bi-calendar"></i> ' . $event_date . ' | ' . htmlspecialchars($location) . ' | ' . htmlspecialchars($mode) . '</p>';
                        echo '          <p class="text-muted text-capitalize">' . htmlspecialchars($description) . '</p>';
                        echo '          <a href="event_details.php?id=' . urlencode($event_id) . '" class="btn" style="background-color: #061a60; color: #fff;">Register Now</a>';
                        echo '      </div>';
                        echo '  </div>';
                        echo '</div>';
                    }
                } else {
                    echo "<div class='col-12'><p>No events found.</p></div>";
                }
                
                $conn->close();

             ?>

        </div>
    </div>
</section>




<!-- Features Section -->
<section class="section py-16">
    <div class="container">
        <!-- Feature Container -->
        <div class="p-10 sm:p-12 md:px-16 md:py-[3.5rem] lg:px-32 lg:py-20 xl:py-24 xl:px-60 rounded-3xl" style="background-color: #061a6010;">
            <!-- Feature info -->
            <div class="flex flex-col gap-y-4 mb-12 sm:mb-14 md:mb-16 lg:mb-20 text-center lg:text-start">
                <!-- Title -->
                <h4 class="text-sm font-bold" style="color: #061a60;">Features</h4>
                <!-- Subtitle -->
                <p class="text-slate-800 text-4xl leading-snug font-bold">Innovative Events for Every Tech Enthusiast!</p>
                <!-- Description -->
                <p class="text-[15px] font-medium text-slate-800/50">Kenya Tech Events brings together the latest conferences, workshops, and networking events to keep you at the forefront of technology. Find the events that matter most to you and your career!</p>
            </div>
            <!-- Features -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-14">
                <!-- Feature item -->
                <div class="flex items-center flex-col xs:flex-row sm:flex-col lg:flex-row gap-4">
                    <!-- Feature icon -->
                    <div class="w-16 h-16 shrink-0">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                    <!-- Feature Content -->
                    <div class="text-center xs:text-start sm:text-center lg:text-start">
                        <!-- Feature item title -->
                        <p class="text-slate-800 font-bold mb-1 text-start">Event Registration Made Easy</p>
                        <!-- Feature item Description -->
                        <p class="text-sm font-medium text-slate-800/50 text-start">Seamlessly register for tech events in Kenya directly from our platform with just a few clicks!</p>
                    </div>
                </div>
                <!-- Feature item -->
                <div class="flex items-center flex-col xs:flex-row sm:flex-col lg:flex-row gap-4">
                    <!-- Feature icon -->
                    <div class="w-16 h-16 shrink-0">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                    <!-- Feature Content -->
                    <div class="text-center xs:text-start sm:text-center lg:text-start">
                        <!-- Feature item title -->
                        <p class="text-slate-800 font-bold mb-1 text-start">Location-Based Filters</p>
                        <!-- Feature item Description -->
                        <p class="text-sm font-medium text-slate-800/50 text-start">Filter events based on location to find the nearest tech events happening in Kenya's major cities.</p>
                    </div>
                </div>
                <!-- Feature item -->
                <div class="flex items-center flex-col xs:flex-row sm:flex-col lg:flex-row gap-4">
                    <!-- Feature icon -->
                    <div class="w-16 h-16 shrink-0">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <!-- Feature Content -->
                    <div class="text-center xs:text-start sm:text-center lg:text-start">
                        <!-- Feature item title -->
                        <p class="text-slate-800 font-bold mb-1 text-start">Networking Opportunities</p>
                        <!-- Feature item Description -->
                        <p class="text-sm font-medium text-slate-800/50 text-start">Connect with fellow tech enthusiasts, entrepreneurs, and innovators at various events.</p>
                    </div>
                </div>
                <!-- Feature item -->
                <div class="flex items-center flex-col xs:flex-row sm:flex-col lg:flex-row gap-4">
                    <!-- Feature icon -->
                    <div class="w-16 h-16 shrink-0">
                        <i class="fas fa-cogs fa-2x"></i>
                    </div>
                    <!-- Feature Content -->
                    <div class="text-center xs:text-start sm:text-center lg:text-start">
                        <!-- Feature item title -->
                        <p class="text-slate-800 font-bold mb-1 text-start">Stay Updated with the Latest Events</p>
                        <!-- Feature item Description -->
                        <p class="text-sm font-medium text-slate-800/50 text-start">Receive real-time updates on new events, deadlines, and special offers to never miss out on important events.</p>
                    </div>
                </div>
            </div>
        </div>
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


    <!-- Boostrap Js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- <script src="./assets/dist/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>