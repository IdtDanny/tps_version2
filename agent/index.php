<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $agent_username = $_SESSION['sessionToken']->agent_username;
    $aID = $_SESSION['sessionToken']->aID;
    $agent_name = $_SESSION['sessionToken']->agent_name;
    $agent_pin = $_SESSION['sessionToken']->agent_pin;

    # error and success alerts
    $photo_errorMessage = "";
    $photo_successMessage = "";
    $errorMessage = "";
    $successMessage = "";
    $key_errorMessage = "";
    $key_successMessage = "";

    # Calculating Each Number of Users, Cards, agent, agents and so on...
    $sql_agent = 'SELECT * FROM agent';
    $sql_client = 'SELECT * FROM client';
    $sql_notify = 'SELECT * FROM `notification_all` WHERE `receiver_id` =:agent_pin OR `sender_id`';
    $statement_notify = $pdo->prepare($sql_notify);
    $statement_notify -> execute([ 'agent_pin' => $agent_pin ]); 
    
    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    # Getting The number of Agents, Cards, agent...
    $agentsCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();
    $registered_notify = $statement_notify->rowCount();

    # Fetching agent info ...

    $agent_FetchQuery = 'SELECT * FROM `agent` ORDER BY `created_at` DESC';
    $agent_FetchStatement = $pdo->prepare($agent_FetchQuery);
    $agent_FetchStatement->execute();
    $agent_Result = $agent_FetchStatement->fetchAll();

    # Getting agent Info. for update form...

    $agentFetchQuery = 'SELECT * FROM `agent` WHERE `aID` = :agentid';
    $agentFetchStatement = $pdo->prepare($agentFetchQuery);
    $agentFetchStatement->execute([
        'agentid' => $aID
    ]);
    $agentResults = $agentFetchStatement->fetch();

    # Getting user notifications

    $agent_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :agent_pin OR `sender_id` = :sagent_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $agent_notifyFetchStatement = $pdo->prepare($agent_notifyFetchQuery);
    $agent_notifyFetchStatement->execute([
        'agent_pin'     => $agent_pin,
        'sagent_pin'    => $agent_pin
    ]);
    $agent_notifyResults = $agent_notifyFetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";
 

    # Updating agent Information...

    if (isset($_POST['editinfo'])) {
        $new_agent_Name = $_POST['agent-name'];
        $new_agent_Mail = $_POST['agent-mail'];
        $new_agent_Username = $_POST['agent-username'];
        $agent_Old_Password = $_POST['old-password'];
        $agent_New_Password = $_POST['new-password'];
        $agent_Confirm_password = $_POST['confirm-password'];

        # Checking for Password fields(if they are empty, It will only update the username or name only)...

        if (empty($agent_Old_Password)) {

            # Updating Query...

            $agent_Update_Query = 'UPDATE `agent`
                                    SET `agent_name` = :agentname,
                                        `agent_username` = :agentusername
                                        `agent_mail` = :agent_mail,
                                    WHERE `aID` = :agentid
            ';

            $agent_Update_stmt = $pdo->prepare($agent_Update_Query);
            $agent_Update_stmt->execute([
                'agentname'     =>  $new_agent_Name,
                'agentusername' =>  $new_agent_Username,
                'agent_mail'    =>  $new_agent_Mail,
                'agentid'       =>  $aID
            ]);
            $successMessage = " Username Edited Successfully";
        }
        else {

            # Checking if the old password match...

            $hashedpass = md5($agent_Old_Password);
            
            // $hashedpass = $agent_Old_Password;

            if ($agentResults->agent_password == $hashedpass || $agentResults->agent_password == $agent_Old_Password ) {

                if ($agent_New_Password == $agent_Confirm_password) {

                    # Update Query Including Passwords...

                    $agent_Update_Query = 'UPDATE `agent`
                                            SET `agent_name` = :agentname,
                                                `agent_username` = :agentusername,
                                                `agent_mail` = :agent_mail,
                                                `agent_password` = :agentpassword
                                            WHERE `aID` = :agentid
                    ';

                    $agent_Update_stmt = $pdo->prepare($agent_Update_Query);
                    $agent_Update_stmt->execute([
                        'agentname'     =>  $new_agent_Name,
                        'agentusername' =>  $new_agent_Username,
                        'agent_mail'    =>  $new_agent_Mail,
                        'agentpassword' =>  md5($agent_New_Password),
                        'agentid'       =>  $aID
                    ]);
                    $successMessage = " Data Edited Successfully";
                }
                else{
                    $errorMessage = " New Password Does not Match";
                }

            }
            else{
                $errorMessage = " Current Password is Incorrect";
            }

        }
    }

    # Updating profile photo

    if(isset($_POST["submit-profile"])) {
        $target_dir = "../public/profile/";
        $target_file = $target_dir . basename($_FILES["agent-profile"]["name"]);
        $photo = $_FILES['agent-profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["agent-profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $photo_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check file size
        if ($_FILES["agent-profile"]["size"] > 4000000) {
            $photo_errorMessage = " Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        # Allow certain file formats
        else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $photo_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        # Check if $uploadOk is set to 0 by an error
        else if ($uploadOk == 0) {
            $photo_errorMessage = " Sorry, your file was not uploaded.";
            # if everything is ok, try to upload file 
        } 
        else {
            if (move_uploaded_file($_FILES["agent-profile"]["tmp_name"], $target_file)) {        
                
                # Updating agent profile...
                $profile_update = 'UPDATE `agent` 
                                    SET `photo` = :photo 
                                    WHERE `aID` = :agentid
                                ';
        
                $agent_updateStatement = $pdo->prepare($profile_update);
                $agent_updateStatement->execute([
                                    'photo'     =>  $photo,
                                    'agentid'   =>  $agentResults->aID
                                ]);
            
                if ($profile_update) {
                    $photo_successMessage = $successRefreshMessage;
                }
            } 
            else {
                $photo_errorMessage = " Sorry, there was an error uploading your file.";
            }
        }
    }

    # generating activation key passcode

    if (isset($_POST['generateKey'])) {
        $cpin = $_POST['cpin'];
        $amount = $_POST['ramount'];

        # check if agent pin are same ... 

        if ($agentResults->agent_pin == $cpin) {

            # checking if agent has enough balance to withdraw ...

            $agent_balance = $agentResults->agent_balance;

            if ($agent_balance <= 0 || $agent_balance < $amount) {
                $key_errorMessage = " Not enough balance, ". $errorRefreshMessage;
            }

            # otherwise proceed with operation ...

            else {

                # create a request ...

                $request_date = date('Y-m-d');
                $request_time = date('h:i:s');
                $request_type = 'withdraw';
                $user_id = $cpin;
                $amount;
                $activation_key = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$&?", 5)),0 , 10);
                $status = 'waiting';

                # check if he exist in request table ...

                $requestFetchQuery = 'SELECT * FROM `request` WHERE `user_id` = :requestid';
                $requestFetchStatement = $pdo->prepare($requestFetchQuery);
                $requestFetchStatement->execute([
                    'requestid' => $cpin
                ]);
                $requestResults = $requestFetchStatement->fetch();

                $requestCount = $requestFetchStatement->rowCount();

                # update request row if he/she already exists ...

                if ($requestCount > 0) {

                    $sql_updateRequest = 'UPDATE `request` SET `request_date`=:request_date,`request_time`=:request_time,`confirmed_date`=:confirmed_date,`confirmed_time`=:confirmed_time,`request_type`=:request_type,`user_id`=:ruser_id,`amount`=:amount,`activation_key`=:activation_key,`status`=:rstatus WHERE `user_id` =:rauser_id';

                    # PDO Prep & Exec..
                    $update_requestStatement = $pdo->prepare($sql_updateRequest);
                    $update_requestStatement->execute([
                        'request_date'   => $request_date,
                        'request_time'   => $request_time,
                        'confirmed_date' => NULL,
                        'confirmed_time' => NULL,
                        'request_type'   => $request_type,
                        'ruser_id'       => $user_id,
                        'amount'         => $amount,
                        'activation_key' => $activation_key,
                        'rstatus'        => $status,
                        'rauser_id'      => $user_id
                    ]);

                    if ($sql_updateRequest) {
                        $key_successMessage = "  Key: ". $activation_key . $errorRefreshMessage;
                    }
                }

                # create a request row for agent if he/she does not exist ...

                else {

                    $sql_insertRequest = 'INSERT INTO `request`(`request_date`, `request_time`, `request_type`, `user_id`, `amount`, `activation_key`, `status`) VALUES (:request_date, :request_time, :request_type, :ruser_id, :amount, :activation_key, :rstatus)';

                    # PDO Prep & Exec..
                    $insert_requestStatement = $pdo->prepare($sql_insertRequest);
                    $insert_requestStatement->execute([
                        'request_date'   => $request_date,
                        'request_time'   => $request_time,
                        'request_type'   => $request_type,
                        'ruser_id'       => $user_id,
                        'amount'         => $amount,
                        'activation_key' => $activation_key,
                        'rstatus'        => $status
                    ]);

                    if ($sql_insertRequest) {
                        $key_successMessage = "  Key: ". $activation_key . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise cancel everything

        else {
            $key_errorMessage = " Wrong Pin, ". $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/index_front.html';
?>