<?php

    session_start();

    # Including The Connection...
    require_once 'public/config/connection.php';

    # Variable Declaration...
    $sessionToken='';
    $error_message='';

    # Getting Data From Form...

    if (isset($_POST['signin'])) {

        # Form Variables...
        $username = $_POST['username'];
        $password = $_POST['password'];

        # Getting The Hashed Password...
        $hashedPassword = md5($password);

        $sessionToken = addslashes($sessionToken);

        # Checking for User Existence...
        $query = 'SELECT * FROM `admin` WHERE `admin_username` = :username';

        # PDO Prepare & Execution of the query...
        $statement = $pdo->prepare($query);
        $statement->execute([
            'username' => $username
        ]);
        $usersCount = $statement->rowCount();

        if ($usersCount > 0) {
            $admin = $statement->fetch();
            if ($username == $admin->admin_username && ($password == $admin->admin_password || $hashedPassword == $admin->admin_password)) {
                $page = 'admin/#dashboard';
                $_SESSION['sessionToken'] = $admin;
                header('location:'.$page);
            }
            else {
                $error_message=" Incorrect Username or Password";
            }
        }
        else if($usersCount == 0) {

            # Checking for Agent Account Existence...
            $query_2 = 'SELECT * FROM `agent` WHERE `agent_username` = :username';

            # PDO Prepare & Execution of the query...
            $statement = $pdo->prepare($query_2);
            $statement->execute([
                'username' => $username
            ]);
            $agentCount = $statement->rowCount();

            if ($agentCount > 0) {
                $agent = $statement->fetch();
                if ($username == $agent->agent_username && ($password == $agent->agent_password || $hashedPassword == $agent->agent_password)) {
                    $page = 'agent/#dashboard';
                    $_SESSION['sessionToken'] = $agent;
                    header('location:'.$page);
                }
                else {

                    $error_message=" Incorrect Username or Password";
                }

            }
            // else {
            //        $error_message="* Incorrect Username or Password";
            // }

        // }
        else {
            # Checking for Business Account Existence...
            $query_3 = 'SELECT * FROM `business` WHERE `business_tin` = :businesstin';
            
            $businessname = $username; 

            # PDO Prepare & Execution of the query...
            $statement = $pdo->prepare($query_3);
            $statement->execute([
                'businesstin' => $businessname
            ]);
            $businessCount = $statement->rowCount();
        
            if ($businessCount > 0) {
                $business = $statement->fetch();
                if ($username == $business -> business_tin && ($password == $business -> business_password || $hashedPassword == $business -> business_password)) {
                    $page = 'business/#dashboard';
                    $_SESSION['sessionToken'] = $business;
                    header('location:'.$page);
                }
                else {
        
                    $error_message=" Incorrect TIN or Password";
                }
        
            }
            else {
                $error_message=" Incorrect TIN or Password";
            }
        }
        
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap & Pay</title>

    <!-- Icon Header -->
    <link rel="shortcut icon" type="image/png" href="public/img/card_Header.png">

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">   
    <link rel="stylesheet" href="node_modules/css/main.min.css">

    <!-- Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>
<style>
    section {
        padding: 50px 0px;
    }
    
    .just {
        text-align: justify;
    }
</style>

<body>
    <!-- main page & intro cover -->
    <section id="intro" class="bg-altprimary">
        <div class="container-xxl">
            <div class="row justify-content-center align-items-center mt-5">
                <div class="col-md-5 text-center text-md-start text-light">
                    <h1>
                        <div class="display-1 fw-bold">Tap & Pay</div>
                        <!-- <div class="display-6 pt-3 text-muted">Welcome to the unlimitless world.</div> -->
                    </h1>
                    <p class="my-4 text-muted">&raquo; <em>We are proud to serve you in making the place of convenient, convenience for All. </em></p>
                    <p class="my-4 text-muted">&raquo; <em>Come on Just do it Yourself. </em></p>
                    <a href="#signin" class="btn btn-gold">Land a Job</a>
                    <a href="#signin" class="btn btn-gold">Become our client</a>
                    <a href="#signin" class="btn btn-outline-altlight">Sign in</a>
                </div>

                <!-- tooltip -->
                <div class="col-md-5 mt-md-5 text-center d-none d-md-block">
                    <span class="tt" data-bs-placement="bottom" title="World without Limit">
                        <img class="img-fluid" src="public/img/image.png" alt="">
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- login  -->
    <section id="signin" class="bg-light mt-1">
        <div class="container-lg">
            <div class="text-center">
                <h2>Log your credentials</h2>
                <p class="lead text-muted">We're glad to have you!</p>
            </div>

            <div class="row my-5 align-items-center justify-content-center g-0">

                <!-- login -->
                <div class="col-9 col-lg-4">
                    
                <?php
                if($error_message): ?>
                    <div class="alert alert-warning alert-dismissible fade show alert-danger" role="alert">
                        <strong class="bi bi-exclamation-triangle"><?php echo $error_message; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif ?>

                    <div class="card border-altprimary border-2 rounded-4">
                        <div class="card-header text-center text-gold">SIGN IN</div>
                        <div class="card-body text-center py-lg-5">
                            <h4 class="card-title fw-medium text-altprimary">BUSINESS - CLIENT</h4>
                            <p class="card-subtitle p-3"><em>Signin with your username and password</em></p>

                            <form action="" method="post">

                                <div class="mb-4 input-group">
                                    <span class="input-group-text">
                                    <i class="bi bi-person-circle"></i>
                                </span>
                                    <input type="text" name="username" id="email" class="form-control" placeholder="Peter">

                                    <!-- tooltips -->
                                    <span class="input-group-text">
                                    <span class="tt" data-bs-placement="bottom" title="Your registered username">
                                    <i class="bi bi-question-circle"></i>
                                    </span>
                                    </span>
                                </div>

                                <div class="mb-4 input-group">
                                    <span class="input-group-text">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                    <input type="password" name="password" id="email" class="form-control" placeholder="peT@123!nK?">

                                    <!-- tooltips -->
                                    <span class="input-group-text">
                                    <span class="tt" data-bs-placement="bottom" title="Your key">
                                    <i class="bi bi-question-circle"></i>
                                    </span>
                                    </span>
                                </div>
                                <a href="#" class="text-danger float-start fst-italic">forgot password?</a>

                                <button type="submit" class="btn btn-altprimary mt-3 text-altlight bi-door-open-fill float-end" name="signin">&nbsp; Sign in</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- get updates / modal trigger -->
    <section class="bg-altprimary">
        <footer class="lead text-altlight text-center">
            <p>
                &copy; Copyrights 2023
            </p>
        </footer>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        const tooltips = document.querySelectorAll('.tt')
        tooltips.forEach(t => {
            new bootstrap.Tooltip(t)
        })
    </script>
</body>

</html>