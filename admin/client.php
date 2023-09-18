<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $admin_username = $_SESSION['sessionToken']->admin_username;
    $admin_ID = $_SESSION['sessionToken']->admin_ID;
    $admin_name = $_SESSION['sessionToken']->admin_name;

    #notification variables ...

    $client_errorMessage = "";
    $client_successMessage = "";
    $client_deleteSuccessMessage = "";
    $client_deleteErrorMessage = "";
    $update_errorMessage = "";
    $update_successMessage = "";

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

    # Fetching agents info ...

    $client_FetchQuery = 'SELECT * FROM `client` ORDER BY `created_at` DESC';
    $client_FetchStatement = $pdo->prepare($client_FetchQuery);
    $client_FetchStatement->execute();
    $client_Result = $client_FetchStatement->fetchAll();

    # Getting Admin Info. for update form...

    $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
    $adminFetchStatement = $pdo->prepare($adminFetchQuery);
    $adminFetchStatement->execute([
        'adminid' => $admin_ID
    ]);
    $adminResults = $adminFetchStatement->fetch();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='client.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='client.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # Registering new agent

    if (isset($_POST['registerAgent'])) {

        $agent_name = $_POST['agent_name'];
        $agent_uname = $_POST['agent_uname'];
        $agent_mail = $_POST['agent_mail'];
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

                    $sql_insert_agent = " INSERT INTO `agent`(`created_at`, `agent_name`, `agent_username`, `agent_mail`, `agent_password`, `agent_pin`, `photo`, `agent_balance`, `status`) VALUES(:adate, :agent_name, :agent_uname, :agent_mail, :agent_password, :agent_pin, :photo, :balance, :bstatus)";

                    $agent_InsertStatement = $pdo->prepare($sql_insert_agent);
                    $agent_InsertStatement->execute([
                        'adate'             =>  $date_Sent,
                        'agent_name'        =>  $agent_name,
                        'agent_uname'       =>  $agent_uname,
                        'agent_mail'        =>  $agent_mail,
                        'agent_password'    =>  $hashed_Password,
                        'agent_pin'         =>  $agent_pin,
                        'photo'             =>  $agent_profile,
                        'balance'           =>  '0',
                        'bstatus'           =>  'active'
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
                                $agent_successMessage = " Agent Registered, Pin: ". $agent_pin . $successRefreshMessage;
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
    if (isset($_GET['dcID'])) {
        $daID = $_GET['dcID'];
        $sql_adelete = 'DELETE FROM `client` WHERE cID = :cid';
        $sql_lodelete = 'DELETE FROM `client_location` WHERE cID = :cid';

        # PDO Prep & Exec..
        $delete_client = $pdo->prepare($sql_adelete);
        $delete_client->execute([
            'cid'  =>  $daID
        ]);

        $delete_client_location = $pdo->prepare($sql_lodelete);
        $delete_client_location->execute([
            'cid'  =>  $daID
        ]);

        if ($sql_adelete && $sql_lodelete) {
            $client_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $client_deleteErrorMessage = " Could not delete, check agent id" . $errorRefreshMessage;
        }

    }

    # Recharge agent Operation...

    if (isset($_POST['rechargeAgent'])) {

        $cpin = $_POST['cpin'];
        $agent_username = $_POST['agent_username'];
        $ramount = $_POST['ramount'];

        # Checking for businessTin ...

        $fetch_UserQuery='SELECT * FROM `agent` WHERE `agent_username` = :agent_name AND `agent_pin` = :cpin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'agent_name' => $agent_username,
            'cpin'       => $cpin
        ]);

        $agent_Info = $fetch_UserStatement -> fetch();

        $agentCount = $fetch_UserStatement->rowCount();

        if ($agentCount > 0 ) {

            # Modifying Agent ...

            $balance = $agent_Info->agent_balance;

            $balance += $ramount;

            $agent_UpdateQuery = ' UPDATE `agent`
                                SET `agent_balance` = :agent_balance
                                WHERE `agent_pin` = :agent_pin
            ';

            $agent_UpdateStatement = $pdo->prepare($agent_UpdateQuery);
            $agent_UpdateStatement->execute([
                'agent_balance'   =>  $balance,
                'agent_pin'       =>  $cpin
            ]);

            if ($agent_UpdateQuery) {
                $update_successMessage = " Recharged Successful" . $successRefreshMessage;
            }
        }
        else {
            $update_errorMessage = " Unknown Pin" . $errorRefreshMessage;
        }

    }

    # getting agent activation response

    if (isset($_GET['AcID'])) {
        $daID = $_GET['AcID'];
        $sql_active = 'UPDATE `client` SET `status` =:active WHERE cID = :aid';

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
?>

<?php 
    include 'include/client_front.html';
?>