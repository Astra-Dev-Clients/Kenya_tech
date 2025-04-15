 <?php
session_start();
if (!isset($_SESSION['google_auth']) && !isset($_SESSION['email_auth'])) {
   header('location: ../auth/index.php');
   exit();
}

include '../Database/db.php';

// Check which session variable is set and get the user ID
$id = isset($_SESSION['google_auth']) ? $_SESSION['google_auth'] : $_SESSION['email_auth'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE SN = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_object();

$profileImage = htmlspecialchars($details->Avatar, ENT_QUOTES, 'UTF-8'); // Sanitize output
$name = htmlspecialchars($details->First_Name, ENT_QUOTES, 'UTF-8'); // Sanitize output
$email = htmlspecialchars($details->Email, ENT_QUOTES, 'UTF-8'); // Sanitize output



// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // generate event id    
    $event_id = uniqid('event_'); // Generate a unique event ID
    // Check if the event ID already exists in the database
    $check_event_id = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $check_event_id->bind_param("s", $event_id);
    $check_event_id->execute();
    $result = $check_event_id->get_result();
    if ($result->num_rows > 0) {
        // If it exists, generate a new one
        $event_id = uniqid('event_');
    }
    $check_event_id->close();


    // Collect and sanitize input data
    $organizer_id = $id; // Assuming the organizer ID is the same as the logged-in user ID
    $title = trim($_POST['event-title']);
    $event_date = $_POST['event-date'];
    $location = trim($_POST['event-location']);
    $description = trim($_POST['event-description']);
    $general_price = $_POST['general-price'];
    $vip_price = $_POST['vip-price'];
    $early_price = $_POST['early-price'];
    $general_priv = trim($_POST['general-privileges']);
    $vip_priv = trim($_POST['vip-privileges']);
    $early_priv = trim($_POST['early-privileges']);
    $mode = $_POST['event-mode'];
    $status = $_POST['status'];

    // Handle file upload
    if (isset($_FILES['event-poster']) && $_FILES['event-poster']['error'] === 0) {
        $poster = $_FILES['event-poster'];
        $poster_name = uniqid('poster_') . "_" . basename($poster['name']);
        $target_dir = "../Assets/Img/events/"; // Make sure this folder exists
        $target_file = $target_dir . $poster_name;

        // Move uploaded file
        if (move_uploaded_file($poster["tmp_name"], $target_file)) {
            // File uploaded successfully
        } else {
            die("Failed to upload poster.");
        }
    } else {
        die("Poster upload error.");
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO events (
        Event_Date, event_id, organizer_id, poster, title, location, description,
        General_Admission, VIP, Early_Bird,
        General_Admission_previledges, VIP_previledges, Early_Bird_previledges,
        status, mode
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssisssssdddssss",
        $event_date,
        $event_id,
        $organizer_id,
        $poster_name,
        $title,
        $location,
        $description,
        $general_price,
        $vip_price,
        $early_price,
        $general_priv,
        $vip_priv,
        $early_priv,
        $status,
        $mode
    );

    // Execute
    if ($stmt->execute()) {
        echo "Event created successfully!";
        // Redirect or show success page
        header("Location: success.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Dashboard</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">


<style>
        body {
            font-family: Arial, sans-serif;
        }
        
        /* Button Style */
        .add-product-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }

        /* Popover Container */
        .popover-container {
            position: relative;
            display: inline-block;
        }

        /* Popover Box */
        .popover {
            display: none;
            position: absolute;
            top: 40px;
            left: 0;
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 5px;
            width: 250px;
            z-index: 1000;
        }

        /* Show Popover */
        .popover.show {
            display: block;
        }

        /* Popover Form Styles */
        .popover input {
            width: 100%;
            padding: 5px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .popover button {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            margin-top: 5px;
            width: 100%;
            border-radius: 3px;
        }


    .drop-zone {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }
    .drop-zone.dragover {
        background-color: #e3f2fd;
    }
    .drop-zone img {
        max-width: 100%;
        margin-top: 10px;
        max-height: 200px;
    }




    </style>




<!-- Button to Trigger Popover -->

<!-- add product popover  -->
    <div class="page-wrapper">
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="index.html">
                            <img src="./images/logo.png" style="width:179px; height:52px;" alt="Kenya Tech Events Logo" />
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        <nav class="navbar-mobile">
            <div class="container-fluid">
                <ul class="navbar-mobile__list list-unstyled">
                    <li>
                        <a href="dashboard.html">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="analytics.html">
                            <i class="fas fa-chart-bar"></i>Analytics
                        </a>
                    </li>
                    <li>
                        <a href="tickets.html">
                            <i class="fas fa-ticket-alt"></i>Tickets
                        </a>
                    </li>
                    <li>
                        <a href="attendees.html">
                            <i class="fas fa-users"></i>Attendees
                        </a>
                    </li>
                    <li>
                        <a href="events.html">
                            <i class="fas fa-calendar-alt"></i>Events
                        </a>
                    </li>
                    <li>
                        <a href="venues.html">
                            <i class="fas fa-map-marker-alt"></i>Venues
                        </a>
                    </li>
                    <li>
                        <a href="reports.html">
                            <i class="fas fa-file-alt"></i>Reports
                        </a>
                    </li>
                    <li>
                        <a href="settings.html">
                            <i class="fas fa-cogs"></i>Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        </header>
        <!-- END HEADER MOBILE-->

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
                    <img src="images/logo.png" style="width:179px; height:52px;" alt="Kenya Tech Events Logo" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
            <nav class="navbar-sidebar">
    <ul class="list-unstyled navbar__list">
        <li>
            <a href="dashboard.html">
                <i class="fas fa-tachometer-alt"></i>Dashboard
            </a>
        </li>
        <li>
            <a href="analytics.html">
                <i class="fas fa-chart-bar"></i>Analytics
            </a>
        </li>
        <li>
            <a href="tickets.html">
                <i class="fas fa-ticket-alt"></i>Tickets
            </a>
        </li>
        <li>
            <a href="attendees.html">
                <i class="fas fa-users"></i>Attendees
            </a>
        </li>
        <li>
            <a href="events.html">
                <i class="fas fa-calendar-alt"></i>Events
            </a>
        </li>
        <li>
            <a href="venues.html">
                <i class="fas fa-map-marker-alt"></i>Venues
            </a>
        </li>
        <li>
            <a href="reports.html">
                <i class="fas fa-file-alt"></i>Reports
            </a>
        </li>
        <li>
            <a href="settings.html">
                <i class="fas fa-cogs"></i>Settings
            </a>
        </li>
        <li class="has-sub">
            <a class="js-arrow" href="#">
                <i class="fas fa-user-circle"></i>Account
            </a>
            <ul class="list-unstyled navbar__sub-list js-sub-list">
                <li>
                    <a href="login.html">Login</a>
                </li>
                <li>
                    <a href="register.html">Register</a>
                </li>
                <li>
                    <a href="forget-pass.html">Forgot Password</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <form class="form-header" action="" method="POST">
                                <input class="au-input au-input--xl" type="text" name="search" placeholder="Search for datas &amp; reports..." />
                                <button class="au-btn--submit" type="submit">
                                    <i class="zmdi zmdi-search"></i>
                                </button>
                            </form>
                            <div class="header-button">
                                <div class="noti-wrap">
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-comment-more"></i>
                                        <span class="quantity">1</span>
                                        <div class="mess-dropdown js-dropdown">
                                            <div class="mess__title">
                                                <p>You have 2 news message</p>
                                            </div>
                                            <div class="mess__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-06.jpg" alt="Michelle Moreno" />
                                                </div>
                                                <div class="content">
                                                    <h6>Michelle Moreno</h6>
                                                    <p>Have sent a photo</p>
                                                    <span class="time">3 min ago</span>
                                                </div>
                                            </div>
                                            <div class="mess__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-04.jpg" alt="Diane Myers" />
                                                </div>
                                                <div class="content">
                                                    <h6>Diane Myers</h6>
                                                    <p>You are now connected on message</p>
                                                    <span class="time">Yesterday</span>
                                                </div>
                                            </div>
                                            <div class="mess__footer">
                                                <a href="#">View all messages</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-email"></i>
                                        <span class="quantity">1</span>
                                        <div class="email-dropdown js-dropdown">
                                            <div class="email__title">
                                                <p>You have 3 New Emails</p>
                                            </div>
                                            <div class="email__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-06.jpg" alt="Cynthia Harvey" />
                                                </div>
                                                <div class="content">
                                                    <p>Meeting about new dashboard...</p>
                                                    <span>Cynthia Harvey, 3 min ago</span>
                                                </div>
                                            </div>
                                            <div class="email__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-05.jpg" alt="Cynthia Harvey" />
                                                </div>
                                                <div class="content">
                                                    <p>Meeting about new dashboard...</p>
                                                    <span>Cynthia Harvey, Yesterday</span>
                                                </div>
                                            </div>
                                            <div class="email__item">
                                                <div class="image img-cir img-40">
                                                    <img src="images/icon/avatar-04.jpg" alt="Cynthia Harvey" />
                                                </div>
                                                <div class="content">
                                                    <p>Meeting about new dashboard...</p>
                                                    <span>Cynthia Harvey, April 12,,2018</span>
                                                </div>
                                            </div>
                                            <div class="email__footer">
                                                <a href="#">See all emails</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="noti__item js-item-menu">
                                        <i class="zmdi zmdi-notifications"></i>
                                        <span class="quantity">3</span>
                                        <div class="notifi-dropdown js-dropdown">
                                            <div class="notifi__title">
                                                <p>You have 3 Notifications</p>
                                            </div>
                                            <div class="notifi__item">
                                                <div class="bg-c1 img-cir img-40">
                                                    <i class="zmdi zmdi-email-open"></i>
                                                </div>
                                                <div class="content">
                                                    <p>You got a email notification</p>
                                                    <span class="date">April 12, 2018 06:50</span>
                                                </div>
                                            </div>
                                            <div class="notifi__item">
                                                <div class="bg-c2 img-cir img-40">
                                                    <i class="zmdi zmdi-account-box"></i>
                                                </div>
                                                <div class="content">
                                                    <p>Your account has been blocked</p>
                                                    <span class="date">April 12, 2018 06:50</span>
                                                </div>
                                            </div>
                                            <div class="notifi__item">
                                                <div class="bg-c3 img-cir img-40">
                                                    <i class="zmdi zmdi-file-text"></i>
                                                </div>
                                                <div class="content">
                                                    <p>You got a new file</p>
                                                    <span class="date">April 12, 2018 06:50</span>
                                                </div>
                                            </div>
                                            <div class="notifi__footer">
                                                <a href="#">All notifications</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image">
                                            <img src="<?php echo htmlspecialchars($profileImage); ?>" class="image" alt="Profile Picture" onerror="this.onerror=null;this.src='images/pic-1.jpg';">
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn" href="#"><?=$name?></a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="image">
                                                    <a href="#">
                                                    <img src="<?php echo htmlspecialchars($profileImage); ?>" class="image" alt="Profile Picture" onerror="this.onerror=null;this.src='images/pic-1.jpg';">
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a href="#"><?=$name?></a>
                                                    </h5>
                                                    <span class="email"><?=$email?></span>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__body">
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-account"></i>Account</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-settings"></i>Setting</a>
                                                </div>
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-money-box"></i>Billing</a>
                                                </div>
                                            </div>
                                            <div class="account-dropdown__footer">
                                                <a href="../../logout.php">
                                                    <i class="zmdi zmdi-power"></i>Logout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
            <div class="container-fluid d-block align-items-center justify-content-center">
                <div class="col-lg-8">

                <div id="alert-container"></div>


                    <div class="card">
                        <div class="card-header">Event Details</div>
                        <div class="card-body">
                            <div class="card-title">
                                <h3 class="text-center title-2">Create Event</h3>
                            </div>
                            <hr>
    <form action="add_event.php" method="post" enctype="multipart/form-data" novalidate="novalidate">
    <!-- Event Poster -->
    <div class="form-group">
        <label for="event-poster" class="control-label mb-1">Event Poster</label>
        <div class="drop-zone" id="drop-zone">
            <p>Drag & drop poster here or click to upload</p>
            <input type="file" name="event-poster" id="event-poster" class="form-control-file" accept="image/*" hidden required>
            <img id="preview" src="#" alt="Preview" style="display: none;" />
        </div>
        <small class="form-text text-muted">Recommended size: 2687px × 1535px</small>
    </div>

    <!-- Event Title -->
    <div class="form-group">
        <label for="event-title" class="control-label mb-1">Event Title</label>
        <input id="event-title" name="event-title" type="text" class="form-control" required placeholder="Enter event title">
    </div>

    <!-- Event Date -->
    <div class="form-group">
        <label for="event-date" class="control-label mb-1">Event Date</label>
        <input id="event-date" name="event-date" type="datetime-local" class="form-control" required>
    </div>

    <!-- Location -->
    <div class="form-group">
        <label for="event-location" class="control-label mb-1">Location</label>
        <input id="event-location" name="event-location" type="text" class="form-control" required placeholder="Event location">
    </div>

    <!-- Description -->
    <div class="form-group">
        <label for="event-description" class="control-label mb-1">Description</label>
        <textarea id="event-description" name="event-description" class="form-control" rows="4" required placeholder="Describe your event..."></textarea>
    </div>

    <!-- Ticket Prices -->
    <div class="form-group">
        <label for="general-price" class="control-label mb-1">General Admission Price (Ksh)</label>
        <input id="general-price" name="general-price" type="number" step="0.01" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="vip-price" class="control-label mb-1">VIP Price (Ksh)</label>
        <input id="vip-price" name="vip-price" type="number" step="0.01" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="early-price" class="control-label mb-1">Early Bird Price (Ksh)</label>
        <input id="early-price" name="early-price" type="number" step="0.01" class="form-control" required>
    </div>

    <!-- Ticket Privileges -->
    <div class="form-group">
        <label for="general-privileges" class="control-label mb-1">General Admission Privileges</label>
        <textarea id="general-privileges" name="general-privileges" class="form-control" rows="2" required></textarea>
    </div>

    <div class="form-group">
        <label for="vip-privileges" class="control-label mb-1">VIP Privileges</label>
        <textarea id="vip-privileges" name="vip-privileges" class="form-control" rows="2" required></textarea>
    </div>

    <div class="form-group">
        <label for="early-privileges" class="control-label mb-1">Early Bird Privileges</label>
        <textarea id="early-privileges" name="early-privileges" class="form-control" rows="2" required></textarea>
    </div>

    <!-- Mode -->
    <div class="form-group">
        <label for="event-mode" class="control-label mb-1">Mode</label>
        <select id="event-mode" name="event-mode" class="form-control" required>
            <option value="">-- Select Mode --</option>
            <option value="physical">Physical</option>
            <option value="online">Online</option>
        </select>
    </div>

    <!-- Status -->
    <div class="form-group">
        <label for="status" class="control-label mb-1">Event Status</label>
        <select id="status" name="status" class="form-control">
            <option value="upcoming" selected>Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
        </select>
    </div>

    <!-- Submit Button -->
    <div>
        <button id="event-submit-button" type="submit" class="btn btn-lg btn-primary btn-block">
            <i class="fa fa-calendar-check-o fa-lg"></i>&nbsp;
            <span id="submit-button-text">Submit Event</span>
        </button>
    </div>
</form>

                        </div>
                    </div>
                </div>
                
            </div>
            <!-- END MAIN CONTENT-->
            <!-- END PAGE CONTAINER-->
        </div>

    </div>




    

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>


<script>
    const dropZone = document.getElementById("drop-zone");
    const fileInput = document.getElementById("event-poster");
    const preview = document.getElementById("preview");

    // Open file picker on click
    dropZone.addEventListener("click", () => fileInput.click());

    // Handle file input change
    fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];
        if (file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    });

    // Drag-over effect
    dropZone.addEventListener("dragover", e => {
        e.preventDefault();
        dropZone.classList.add("dragover");
    });

    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("dragover");
    });

    // Drop event
    dropZone.addEventListener("drop", e => {
        e.preventDefault();
        dropZone.classList.remove("dragover");

        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith("image/")) {
            fileInput.files = e.dataTransfer.files;
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            alert("Please upload an image file.");
        }
    });


    fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];
    if (file && file.type.startsWith("image/")) {
        const img = new Image();
        const objectUrl = URL.createObjectURL(file);

        img.onload = function () {
            if (img.width === 2687 && img.height === 1535) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                alert("Image must be 2687x1535 pixels!");
                fileInput.value = ""; // Reset file input
                preview.style.display = "none";
            }
            URL.revokeObjectURL(objectUrl);
        };

        img.src = objectUrl;
    }
});

</script>









    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->
