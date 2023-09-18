<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $business_username = $_SESSION['sessionToken']->business_username;
    $bID = $_SESSION['sessionToken']->bID;
    $business_name = $_SESSION['sessionToken']->business_name;
    $business_tin = $_SESSION['sessionToken']->business_tin;

    # notification variables ...
    $key_errorMessage = "";
    $key_successMessage = "";
    $successMessage = "";    
    $errorMessage = "";
    $photo_errorMessage = "";    
    $photo_successMessage = "";

    # Calculating Each Number of Users, Cards, business, agents and so on...
    $sql_agent = 'SELECT * FROM agent';
    $sql_client = 'SELECT * FROM client';
    $sql_business = 'SELECT * FROM business';
    $sql_business_notify = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :business_tin OR `sender_id` = :nbusiness_tin';
    $sql_business_record = 'SELECT * FROM `records` WHERE `rID` = :business_tin';
    // $usedCardsSql = 'SELECT * FROM `client` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    $statement_business = $pdo->prepare($sql_business);
    $statement_business -> execute();

    $statement_business_notify = $pdo->prepare($sql_business_notify);
    $statement_business_notify -> execute([
        'business_tin'  => $business_tin,
        'nbusiness_tin' => $business_tin
    ]);

    $statement_business_record = $pdo->prepare($sql_business_record);
    $statement_business_record -> execute([
        'business_tin'  => $business_tin
    ]);

    # Getting The number of Agents, Cards, Business...
    $agentsCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();
    $registered_business = $statement_business -> rowCount();
    $business_notifyCount = $statement_business_notify -> rowCount();
    $business_recordCount = $statement_business_record -> rowCount();

    # Fetching business info ...

    $business_FetchQuery = 'SELECT * FROM `business` ORDER BY `Date` DESC';
    $business_FetchStatement = $pdo->prepare($business_FetchQuery);
    $business_FetchStatement->execute();
    $business_Result = $business_FetchStatement->fetchAll();

    # Getting business Info. for update form...

    $businessFetchQuery = 'SELECT * FROM `business` WHERE `bID` = :businessid';
    $businessFetchStatement = $pdo->prepare($businessFetchQuery);
    $businessFetchStatement->execute([
        'businessid' => $bID
    ]);
    $businessResults = $businessFetchStatement->fetch();

    # Getting user notifications

    $business_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :business_tin OR `sender_id` = :sbusiness_tin ORDER BY `date_sent` AND `time_sent` DESC';
    $business_notifyFetchStatement = $pdo->prepare($business_notifyFetchQuery);
    $business_notifyFetchStatement->execute([
        'business_tin'     => $business_tin,
        'sbusiness_tin'    => $business_tin
    ]);
    $business_notifyResults = $business_notifyFetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";
 

    # Updating business Information...

    if (isset($_POST['editinfo'])) {
        $new_business_Name = $_POST['business-name'];
        $business_Old_Password = $_POST['old-password'];
        $business_New_Password = $_POST['new-password'];
        $business_Confirm_password = $_POST['confirm-password'];

        # Checking for Password fields(if they are empty, It will only update the username or name only)...

        if (empty($business_Old_Password)) {

            # Updating Query...

            $business_Update_Query = 'UPDATE `business`
                                    SET `business_name` = :businessname
                                    WHERE `bID` = :businessid
            ';

            $business_Update_stmt = $pdo->prepare($business_Update_Query);
            $business_Update_stmt->execute([
                'businessname'     =>  $new_business_Name,
                'businessid'       =>  $bID
            ]);
            $successMessage = " Username Edited Successfully";
        }
        else {

            # Checking if the old password match...

            $hashedpass = md5($business_Old_Password);
            
            // $hashedpass = $business_Old_Password;

            if ($businessResults->business_password == $hashedpass || $businessResults->business_password == $business_Old_Password ) {

                if ($business_New_Password == $business_Confirm_password) {

                    # Update Query Including Passwords...

                    $business_Update_Query = 'UPDATE `business`
                                            SET `business_name` = :businessname,
                                                `business_password` = :businesspassword
                                            WHERE `bID` = :businessid
                    ';

                    $business_Update_stmt = $pdo->prepare($business_Update_Query);
                    $business_Update_stmt->execute([
                        'businessname'     =>  $new_business_Name,
                        'businesspassword' =>  md5($business_New_Password),
                        'businessid'       =>  $bID
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
        $target_file = $target_dir . basename($_FILES["business-profile"]["name"]);
        $photo = $_FILES['business-profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["business-profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $photo_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check if file already exists
        // if (file_exists($target_file)) {
        //     $photo_errorMessage = " Sorry, file already exists.";
        //     $uploadOk = 0;
        // }
        
        # Check file size
        if ($_FILES["business-profile"]["size"] > 4000000) {
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
            if (move_uploaded_file($_FILES["business-profile"]["tmp_name"], $target_file)) {        
                
                # Updating business profile...
                $profile_update = 'UPDATE `business` 
                                    SET `photo` = :photo 
                                    WHERE `bID` = :businessid
                                ';
        
                $business_updateStatement = $pdo->prepare($profile_update);
                $business_updateStatement->execute([
                                    'photo'     =>  $photo,
                                    'businessid'   =>  $businessResults->bID
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

        if ($businessResults->business_pin == $cpin) {

            # checking if agent has enough balance to withdraw ...

            $business_balance = $businessResults->balance;
            $business_tin = $businessResults->business_tin;

            if ($business_balance <= 0 || $business_balance < $amount) {
                $key_errorMessage = " Not enough balance, ". $errorRefreshMessage;
            }

            # otherwise proceed with operation ...

            else {

                # create a request ...

                $request_date = date('Y-m-d');
                $request_time = date('h:i:s');
                $request_type = 'withdraw';
                $user_id = $business_tin;
                $amount;
                $activation_key = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$&?", 5)),0 , 10);
                $status = 'waiting';

                # check if he exist in request table ...

                $requestFetchQuery = 'SELECT * FROM `request` WHERE `user_id` = :requestid';
                $requestFetchStatement = $pdo->prepare($requestFetchQuery);
                $requestFetchStatement->execute([
                    'requestid' => $user_id
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