<?php




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Ready | Authentication Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Assets/css/login_animate.css">
    <style>
        .card {
            border: none;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .btn-outline-secondary {
            color: #555;
            border-color: #ddd;
        }
        .btn-outline-secondary:hover {
            background-color: rgb(184, 184, 184);
            border-color: #ccc;
        }
        .form-control {
            border-radius: 8px;
        }
    </style>
</head>
<body class="container-fluid d-flex justify-content-center align-items-center flex-column" style="height: 100vh;">

<div class="wave"></div>
<div class="wave"></div>
<div class="wave"></div>

    <div class="container d-flex justify-content-center align-items-center flex-column">
        <div class="card shadow-sm p-5 w-100" style="max-width: 450px;">
            <h2 class="text-center font-weight-bold">PortfolioReady</h2>
            <p class="text-center h4 mb-4">Sign In</p>
            <form id="signup-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="email">Email address<span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control py-2" id="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password<span class="text-danger">*</span></label>
                    <input type="password" name="pass" class="form-control py-2" id="password" placeholder="Enter your password" required>
                </div>
                <div class="my-2">
                    <small>Forgot Password? <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#resetModal">Reset</a></small>
                </div>
                <button type="submit" name="login-email" class="btn btn-success btn-block">Continue</button>
            </form>
            <div class="text-center my-3">
                <small>Don't have an account yet? <a href="register.php" class="text-success">Register</a></small><br>
            </div>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1 border-top"></div>
                <span class="mx-2 text-muted">OR</span>
                <div class="flex-grow-1 border-top"></div>
            </div>
            <div class="mt-3">
                <a href='<?= $url ?>' class="btn btn-outline-secondary btn-block mb-2 d-flex align-items-center justify-content-center">
                    <img src="google.png" width="20" class="mr-2">
                    Continue with Google Account
                </a>
                <a href="https://github.com/login/oauth/authorize?client_id=<?php echo $_ENV['GITHUB_CLIENT_ID']; ?>&scope=read:user user:email" class="btn btn-outline-secondary btn-block mb-2 d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-github text-dark mr-2" viewBox="0 0 16 16">
                        <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/>
                    </svg>
                    Continue with Github Account
                </a>
                <a href="https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=<?php echo $MS_clientId; ?>&response_type=code&redirect_uri=<?php echo $MS_redirectUri; ?>&scope=openid profile email User.Read" class="btn btn-outline-secondary btn-block d-flex align-items-center justify-content-center">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" width="20" class="mr-2">
                    Continue with Microsoft Account
                </a>
            </div>
        </div>
    </div>
    <style>
        @media (max-width: 480px) {
            .card {
                width: 100%;
            }
        }
    </style>

    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title fs-5" id="resetModalLabel">Enter your Email address below to receive the reset link!</p>
                </div>
                <div class="modal-body">
                    <form id="reset-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="form-group">
                            <label for="reset_email">Email address<span class="text-danger">*</span></label>
                            <input type="email" name="reset_email" class="form-control py-2" id="reset_email" placeholder="Enter your email" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" name="reset_password">Send Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const myModal = document.getElementById('myModal')
        const myInput = document.getElementById('myInput')

        myModal.addEventListener('shown.bs.modal', () => {
            myInput.focus()
        })
    </script>
</body>
</html>
