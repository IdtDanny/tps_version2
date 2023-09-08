<?php

    session_start();

    # Including The Connection...
    require_once 'connection.php';

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
                $page = '../../admin/#dashboard';
                $_SESSION['sessionToken'] = $admin;
                header('location:'.$page);
            }
            else {
                $error_message="* Incorrect Username or Password";
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

                    $error_message="* Incorrect Username or Password";
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
        
                    $error_message="* Incorrect TIN or Password";
                }
        
            }
            else {
                $error_message="* Incorrect TIN or Password";
            }
        }
        
    }
}

?>