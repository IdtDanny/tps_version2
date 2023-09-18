<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # error and success alerts
    $agent_errorMessage = "";
    $agent_deleteErrorMessage = "";
    $update_errorMessage = "";
    $agent_successMessage = "";
    $agent_deleteSuccessMessage = "";
    $update_successMessage = "";

    # Getting Information of Signed in User
    $admin_username = $_SESSION['sessionToken']->admin_username;
    $admin_ID = $_SESSION['sessionToken']->admin_ID;
    $admin_name = $_SESSION['sessionToken']->admin_name;

    # Calculating Each Number of Users, Cards, business, agents and so on...
    $sql_agent = 'SELECT * FROM agent';
    $sql_client = 'SELECT * FROM client';
    $sql_business = 'SELECT * FROM business';
    $sql_business_gas = 'SELECT * FROM `business` WHERE `business_type` = :btype';
    $sql_business_others = 'SELECT * FROM `business` WHERE `business_type` = :otype';
    // $usedCardsSql = 'SELECT * FROM `client` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    $statement_business = $pdo->prepare($sql_business);
    $statement_business -> execute();

    $statement_business_gas = $pdo->prepare($sql_business_gas);
    $statement_business_gas -> execute([
        'btype' => 'gas'
    ]);

    $statement_business_others = $pdo->prepare($sql_business_others);
    $statement_business_others -> execute([
        'otype' => 'others'
    ]);

    # Getting The number of Agents, Cards, Business...
    $agentsCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();
    $registered_business = $statement_business -> rowCount();
    $gas_business = $statement_business_gas -> rowCount();
    $others_business = $statement_business_others -> rowCount();

    # Fetching business info ...

    $business_FetchQuery = 'SELECT * FROM `business` ORDER BY `Date` DESC';
    $business_FetchStatement = $pdo->prepare($business_FetchQuery);
    $business_FetchStatement->execute();
    $business_Result = $business_FetchStatement->fetchAll();

    # Fetching agents info ...

    $agent_FetchQuery = 'SELECT * FROM `agent` ORDER BY `created_at` DESC';
    $agent_FetchStatement = $pdo->prepare($agent_FetchQuery);
    $agent_FetchStatement->execute();
    $agent_Result = $agent_FetchStatement->fetchAll();


    # Getting Admin Info. for update form...

    $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
    $adminFetchStatement = $pdo->prepare($adminFetchQuery);
    $adminFetchStatement->execute([
        'adminid' => $admin_ID
    ]);
    $adminResults = $adminFetchStatement->fetch();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='agent.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='agent.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # register agent form

    if (isset($_POST['agentApply'])) {

        $agent_name = $_POST['agent_name'];
        $agent_uname = $_POST['agent_uname'];
        $agent_mail = $_POST['agent_mail'];
        $agent_tel = $_POST['agent_tel'];
        $agent_gender = $_POST['agender'];
        $agent_district = $_POST['agent_district'];
        $agent_sector = $_POST['agent_sector'];
        $date_Sent = date('Y-m-d h:i:s');
        $agent_pin = rand(1000,9999);
        $password= $agent_uname.'-'.$agent_pin;
        $hashed_Password = md5($password);

        # checking if agent exists
        $agent_existFetchQuery = 'SELECT * FROM `agent` WHERE `agent_name` = :agent_name';
        $agent_existFetchStatement = $pdo->prepare($agent_existFetchQuery);
        $agent_existFetchStatement->execute([
            'agent_name' => $agent_name
        ]);
        $agent_existResults = $agent_existFetchStatement->fetch();

        # if exist, pop some message
        if ($agent_existResults) {
            $agent_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # otherwise proceed with registration process
        else {
            $target_dir = "../public/profile/";
            $target_file = $target_dir . basename($_FILES['agent_profile']['name']);
            $agent_profile = $_FILES['agent_profile']['name'];
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            # Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["agent_profile"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            }
            else {
                $agent_errorMessage = " File is not an image.";
                $uploadOk = 0;
            }
            
            # Check file size
            if ($_FILES["agent_profile"]["size"] > 400000) {
                $agent_errorMessage = " Sorry, your file is too large." . $errorRefreshMessage;
                $uploadOk = 0;
            }
            
            # Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                $agent_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $errorRefreshMessage;
                $uploadOk = 0;
            }
            
            # Check if $uploadOk is set to 0 by an error
            else if ($uploadOk == 0) {
                $agent_errorMessage = " Sorry, your file was not uploaded." . $errorRefreshMessage;
            } 
            else {
                if (move_uploaded_file($_FILES["agent_profile"]["tmp_name"], $target_file)) {
                    
                    # Inserting Business...

                    $sql_insert_agent = " INSERT INTO `agent`(`created_at`, `agent_name`, `agent_gender`, `agent_username`, `agent_tel`, `agent_mail`, `agent_password`, `agent_pin`, `photo`, `agent_balance`, `status`) VALUES(:adate, :agent_name, :agent_gender, :agent_uname, :agent_tel, :agent_mail, :agent_password, :agent_pin, :photo, :balance, :bstatus)";

                    $agent_InsertStatement = $pdo->prepare($sql_insert_agent);
                    $agent_InsertStatement->execute([
                        'adate'             =>  $date_Sent,
                        'agent_name'        =>  $agent_name,
                        'agent_gender'      =>  $agent_gender,
                        'agent_uname'       =>  $agent_uname,
                        'agent_tel'         =>  $agent_tel,  
                        'agent_mail'        =>  $agent_mail,
                        'agent_password'    =>  $hashed_Password,
                        'agent_pin'         =>  $agent_pin,
                        'photo'             =>  $agent_profile,
                        'balance'           =>  '0',
                        'bstatus'           =>  'inactive'
                    ]);

                    if ($sql_insert_agent) {

                        # Getting Admin Info. for update form...

                        $agent_locationFetchQuery = 'SELECT * FROM `agent` WHERE `agent_pin` = :apin';
                        $agent_locationFetchStatement = $pdo->prepare($agent_locationFetchQuery);
                        $agent_locationFetchStatement->execute([
                            'apin' => $agent_pin
                        ]);
                        $agent_locationResults = $agent_locationFetchStatement->fetch();
                        $aID = $agent_locationResults->aID;

                        $sql_insert_location = "  INSERT INTO `agent_location`(`aID`, `agent_name`, `district`, `sector`) VALUES(:aid, :agent_name, :district, :sector) ";
                        $location_InsertStatement = $pdo->prepare($sql_insert_location);
                        $location_InsertStatement->execute([
                                'aid'           =>  $aID,
                                'agent_name'    =>  $agent_name,
                                'district'      =>  $agent_district,
                                'sector'        =>  $agent_sector
                        ]);
                        if ($sql_insert_agent && $sql_insert_location) {
                                $agent_successMessage = " Registered Pin: ". $agent_pin . $successRefreshMessage;
                        }
                    }
                    else {
                        $agent_errorMessage = " Could not register" . $errorRefreshMessage;
                    }
                } 
                else {
                    $agent_errorMessage = " Something went wrong" . $errorRefreshMessage;
                }
            }
        }
    }

    # getting agent delete response

    if (isset($_GET['daID'])) {
        $daID = $_GET['daID'];
        $sql_adelete = 'DELETE FROM `agent` WHERE aID = :aid';
        $sql_lodelete = 'DELETE FROM `agent_location` WHERE aID = :aid';

        # PDO Prep & Exec..
        $delete_agent = $pdo->prepare($sql_adelete);
        $delete_agent->execute([
            'aid'  =>  $daID
        ]);

        $delete_agent_location = $pdo->prepare($sql_lodelete);
        $delete_agent_location->execute([
            'aid'  =>  $daID
        ]);

        if ($sql_adelete && $sql_lodelete) {
            $agent_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $agent_deleteErrorMessage = " Could not delete, check agent id" . $errorRefreshMessage;
        }

    }

    # getting agent activation response

    if (isset($_GET['AaID'])) {
        $daID = $_GET['AaID'];
        $sql_active = 'UPDATE `agent` SET `status` =:active WHERE aID = :aid';

        # PDO Prep & Exec..
        $active_agent = $pdo->prepare($sql_active);
        $active_agent->execute([
            'active' => 'active',
            'aid'    =>  $daID
        ]);

        if ($sql_active) {
            $update_successMessage = " Activated Successful" . $successRefreshMessage;
        }
        else {
            $update_errorMessage = " Could not activate, check agent id" . $errorRefreshMessage;
        }

    }

    # Recharge agent Operation...

    if (isset($_POST['rechargeAgent'])) {

        $cpin = $_POST['cpin'];
        $agent_username = $_POST['agent_username'];
        $ramount = $_POST['ramount'];

        # checking admin confirmation pin ...

        if ($adminResults->admin_pin != $cpin){
            $update_errorMessage = " Unknown Pin" . $errorRefreshMessage;
        }

        # once confirmation pin confirmed ...

        else {

            # Checking for agent existing ...

            $fetch_UserQuery='SELECT * FROM `agent` WHERE `agent_username` = :agent_name';
            $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
            $fetch_UserStatement->execute([
                'agent_name' => $agent_username
            ]);

            $agent_Info = $fetch_UserStatement -> fetch();

            $agentCount = $fetch_UserStatement->rowCount();

            if ($agentCount > 0 ) {

                # admin balance ...

                $admin_balance = $adminResults->Balance;

                # checking admin balance to top up ...

                if ($admin_balance <= 0 || $admin_balance < $ramount) {
                    $update_errorMessage = " Not enough balance" . $errorRefreshMessage;
                }
                
                # with enough balance to top up ...

                else {

                    # modifying admin balance ...

                    $admin_balance -= $ramount;

                    $admin_UpdateQuery = ' UPDATE `admin`
                                        SET `Balance` = :admin_balance
                                        WHERE `admin_pin` = :admin_pin
                    ';

                    $admin_UpdateStatement = $pdo->prepare($admin_UpdateQuery);
                    $admin_UpdateStatement->execute([
                        'admin_balance'   =>  $admin_balance,
                        'admin_pin'       =>  $cpin
                    ]);

                    # Modifying Agent ...

                    $balance = $agent_Info->agent_balance;

                    $balance += $ramount;

                    $agent_UpdateQuery = ' UPDATE `agent`
                                        SET `agent_balance` = :agent_balance
                                        WHERE `agent_username` = :agent_username
                    ';

                    $agent_UpdateStatement = $pdo->prepare($agent_UpdateQuery);
                    $agent_UpdateStatement->execute([
                        'agent_balance'   =>  $balance,
                        'agent_username'  =>  $agent_username
                    ]);

                    if ($agent_UpdateQuery && $admin_UpdateQuery) {

                        # notifications

                        $sender_id = 'admin';
                        $receiver_id = $agent_Info->agent_pin;
                        $amount = $ramount;
                        $date_Sent = date('Y-m-d h:i:s');
                        $time_Sent = date('h:i:s');

                        $sql_insert_notification = " INSERT INTO `notification_all`(`date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES (:date_sent, :time_sent, :receiver_id, :sender_id, :amount, :naction, :astatus)";

                        $notification_InsertStatement = $pdo->prepare($sql_insert_notification);
                        $notification_InsertStatement->execute([
                            'date_sent'     =>  $date_Sent,
                            'time_sent'     =>  $time_Sent,
                            'receiver_id'   =>  $receiver_id,
                            'sender_id'     =>  $sender_id,
                            'amount'        =>  $amount,
                            'naction'       =>  'recharge',
                            'astatus'       =>  'unread'
                        ]);

                        if ($sql_insert_notification) {
                            $update_successMessage = " Recharged Successful" . $successRefreshMessage;
                        }
                    }
                    else {
                        $update_errorMessage = " Failed to recharge" . $errorRefreshMessage;
                    }
                }
            }
            else {
                $update_errorMessage = " Unknown Agent" . $errorRefreshMessage;
            }
        }

    }

    # withdraw agent Operation...

    if (isset($_POST['withdrawAgent'])) {

        $cpin = $_POST['cpin'];
        $ramount = $_POST['ramount'];

        # checking agent activation key from request made ...

        $requestFetchQuery = 'SELECT * FROM `request` WHERE `activation_key` = :cpin AND `amount` = :ramount';
        $requestFetchStatement = $pdo->prepare($requestFetchQuery);
        $requestFetchStatement->execute([
            'cpin'    => $cpin,
            'ramount' => $ramount
        ]);
        $requestResults = $requestFetchStatement->fetch();

        # once activation key confirmed ...

        if ($requestFetchQuery) {

            # checking if it is not confirmed ...

            if ($requestResults->status == 'confirmed') {
                $update_errorMessage = " No request made" . $errorRefreshMessage;
            }

            # otherwise proceed with operation ...

            else {

                # checking if 24 hours haven't passed ...

                $request_date = $requestResults->request_date . ' ' . $requestResults->request_time;

                $now = strtotime(date('Y-m-d h:i:s'));
                $cdate = strtotime($request_date);
                $day_diff = $now - $cdate;
                $hours = floor($day_diff / 3600);
                
                if ($hours >= 24) {
                    $update_errorMessage = " Request expired" . $errorRefreshMessage;
                }

                # otherwise proceed with operation ...

                else {

                    # getting agent info from request ...

                    $user_id = $requestResults->user_id;
                    
                    # Checking for agent existing and his id meet with request ...

                    $fetch_UserQuery='SELECT * FROM `agent` WHERE `agent_pin` = :agent_pin';
                    $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
                    $fetch_UserStatement->execute([
                        'agent_pin' => $user_id
                    ]);

                    $agent_Info = $fetch_UserStatement -> fetch();

                    $agentCount = $fetch_UserStatement->rowCount();

                    # proceed with withdraw if agent info meet with request ...

                    if ($agentCount > 0 ) {

                        # agent balance ...

                        $agent_balance = $agent_Info->agent_balance;

                        # checking agent balance to withdraw ...

                        if ($agent_balance <= 0 || $agent_balance < $ramount) {
                            $update_errorMessage = " Not enough balance" . $errorRefreshMessage;
                        }
                        
                        # with enough balance to top up ...

                        else {

                            # modifying admin balance ...

                            $admin_balance = $adminResults->Balance;

                            $admin_pin = $adminResults->admin_pin;

                            $admin_balance += $ramount;

                            $admin_UpdateQuery = ' UPDATE `admin`
                                                SET `Balance` = :admin_balance
                                                WHERE `admin_pin` = :admin_pin
                            ';

                            $admin_UpdateStatement = $pdo->prepare($admin_UpdateQuery);
                            $admin_UpdateStatement->execute([
                                'admin_balance'   =>  $admin_balance,
                                'admin_pin'       =>  $admin_pin
                            ]);

                            # Modifying Agent ...

                            $balance = $agent_Info->agent_balance;

                            $balance -= $ramount;

                            $agent_UpdateQuery = ' UPDATE `agent`
                                                SET `agent_balance` = :agent_balance
                                                WHERE `agent_pin` = :agent_pin
                            ';

                            $agent_UpdateStatement = $pdo->prepare($agent_UpdateQuery);
                            $agent_UpdateStatement->execute([
                                'agent_balance' =>  $balance,
                                'agent_pin'     =>  $user_id
                            ]);

                            if ($agent_UpdateQuery && $admin_UpdateQuery) {

                                $sender_id = 'admin';
                                $receiver_id = $agent_Info->agent_pin;
                                $amount = $ramount;
                                $date_Sent = date('Y-m-d h:i:s');
                                $time_Sent = date('h:i:s');

                                # confirming the request ...

                                $sql_confirm_request = " UPDATE `request` SET `confirmed_date` = :confirm_date, 
                                                                            `confirmed_time` =:confirm_time, 
                                                                            `status` =:bstatus
                                                                        WHERE `activation_key` = :activation_key";

                                $request_confirmStatement = $pdo->prepare($sql_confirm_request);
                                $request_confirmStatement->execute([
                                    'confirm_date'   =>  $date_Sent,
                                    'confirm_time'   =>  $time_Sent,
                                    'bstatus'        =>  'confirmed',
                                    'activation_key' =>  $cpin
                                ]);

                                # notifications ...

                                $sql_insert_notification = " INSERT INTO `notification_all`(`date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES (:date_sent, :time_sent, :receiver_id, :sender_id, :amount, :naction, :astatus)";

                                $notification_InsertStatement = $pdo->prepare($sql_insert_notification);
                                $notification_InsertStatement->execute([
                                    'date_sent'     =>  $date_Sent,
                                    'time_sent'     =>  $time_Sent,
                                    'receiver_id'   =>  $receiver_id,
                                    'sender_id'     =>  $sender_id,
                                    'amount'        =>  $amount,
                                    'naction'       =>  'transfer',
                                    'astatus'       =>  'unread'
                                ]);

                                if ($sql_insert_notification && $sql_confirm_request) {
                                    $update_successMessage = " Withdraw Successful" . $successRefreshMessage;
                                }

                                else {
                                    $update_errorMessage = " Failed to confirm" . $errorRefreshMessage;
                                }
                            }

                            else {
                                $update_errorMessage = " Failed to withdraw" . $errorRefreshMessage;
                            }
                        }
                    }

                    # otherwise cancel the process ...

                    else {
                        $update_errorMessage = " Amount not match" . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise wrong activation key 

        else {
            $update_errorMessage = " No request made" . $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/agent_front.html';
?>