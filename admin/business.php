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
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='business.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='business.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # Registering new business

    if (isset($_POST['registerbusiness'])) {
        $business_name = $_POST['business_name'];
        $business_mail = $_POST['business_mail'];
        $business_type = $_POST['business_type'];
        $business_tin = $_POST['business_tin'];
        $business_district = $_POST['business_district'];
        $business_sector = $_POST['business_sector'];
        $password = $business_tin;
        $hashed_Password = md5($password);
        $date_Sent = date('Y-m-d h:i:s');
        $business_pin = rand(1000,9999);

        $target_dir = "../public/profile/";
        $target_file = $target_dir . basename($_FILES['business_profile']['name']);
        $business_profile = $_FILES['business_profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["business_profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $busy_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check file size
        if ($_FILES["business_profile"]["size"] > 400000) {
            $busy_errorMessage = " Sorry, your file is too large." . $errorRefreshMessage;
            $uploadOk = 0;
        }
        
        # Allow certain file formats
        else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $busy_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $errorRefreshMessage;
            $uploadOk = 0;
        }
        
        # Check if $uploadOk is set to 0 by an error
        else if ($uploadOk == 0) {
            $busy_errorMessage = " Sorry, your file was not uploaded." . $errorRefreshMessage;
        } 
        else {
            if (move_uploaded_file($_FILES["business_profile"]["tmp_name"], $target_file)) {

                # Inserting Business...

                $sql_insert_business = " INSERT INTO `business`(`Date`, `business_name`, `business_tin`, `business_mail`, `business_password`, `business_pin`, `business_type`, `balance`, `status`, `photo`) VALUES(:bdate, :businessname, :businesstin, :businessmail, :businesspass, :businesspin, :businesstype, :balance, :bstatus, :photo)";

                $business_InsertStatement = $pdo->prepare($sql_insert_business);
                $business_InsertStatement->execute([
                    'bdate'          =>  $date_Sent,
                    'businessname'   =>  $business_name,
                    'businesstin'    =>  $business_tin,
                    'businessmail'   =>  $business_mail,
                    'businesspass'   =>  $hashed_Password,
                    'businesspin'    =>  $business_pin,
                    'businesstype'   =>  $business_type,
                    'balance'        =>  '0',
                    'bstatus'        =>  'Active',
                    'photo'          =>  $business_profile
                ]);

                if ($sql_insert_business) {

                    # Getting Admin Info. for update form...

                    $busy_locationFetchQuery = 'SELECT * FROM `business` WHERE `business_tin` = :businesstin';
                    $busy_locationFetchStatement = $pdo->prepare($busy_locationFetchQuery);
                    $busy_locationFetchStatement->execute([
                        'businesstin' => $business_tin
                    ]);
                    $busy_locationResults = $busy_locationFetchStatement->fetch();
                    $bID = $busy_locationResults->bID;

                    $sql_insert_location = "  INSERT INTO `business_location`(`bID`, `business_tin`, `district`, `sector`) VALUES(:bid, :businesstin, :district, :sector) ";
                    $location_InsertStatement = $pdo->prepare($sql_insert_location);
                    $location_InsertStatement->execute([
                            'bid'           =>  $bID,
                            'businesstin'   =>  $business_tin,
                            'district'      =>  $business_district,
                            'sector'        =>  $business_sector
                    ]);
                    if ($sql_insert_business && $sql_insert_location) {
                            $busy_successMessage = " Business Registered, TIN: ". $business_tin . $successRefreshMessage;
                    }
                }
                else {
                    $busy_errorMessage = " Could not register" . $errorRefreshMessage;
                }
            } 
            else {
                $busy_errorMessage = " Something went wrong" . $errorRefreshMessage;
            }
        }
    }

    # getting business delete response
    if (isset($_GET['dbID'])) {
        $dbID = $_GET['dbID'];
        $sql_bdelete = 'DELETE FROM `business` WHERE bID = :bid';
        $sql_ldelete = 'DELETE FROM `business_location` WHERE bID = :bid';

        # PDO Prep & Exec..
        $delete_BusinessR = $pdo->prepare($sql_bdelete);
        $delete_BusinessR->execute([
            'bid'  =>  $dbID
        ]);

        $delete_BusinessL = $pdo->prepare($sql_ldelete);
        $delete_BusinessL->execute([
            'bid'  =>  $dbID
        ]);

        if ($sql_bdelete && $sql_ldelete) {
            $busy_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $busy_deleteErrorMessage = " Could not delete, check business id" . $errorRefreshMessage;
        }

    }

    # Update Business Operation...

    if (isset($_POST['updatebusiness'])) {

        $old_btin = $_POST['old_btin'];
        $nbusiness_name = $_POST['nbusiness_name'];
        $nbusiness_type = $_POST['nbusiness_type'];
        $nbusiness_tin = $_POST['nbusiness_tin'];
        $nbusiness_district = $_POST['nbusiness_district'];
        $nbusiness_sector = $_POST['nbusiness_sector'];

        # Checking for businessTin ...

        $fetch_UserQuery='SELECT * FROM `business` WHERE `business_tin` = :pin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'pin'       => $old_btin
        ]);

        $business_Info = $fetch_UserStatement -> fetch();

        $businessCount = $fetch_UserStatement->rowCount();

        if ($businessCount > 0 ) {

            # Modifying Agent ...

            $business_UpdateQuery = ' UPDATE `business`
                                SET `business_name` = :business_NewName,
                                    `business_tin` = :business_NewTin,
                                    `business_type` = :business_NewType
                                WHERE `business_tin` = :businesstin
            ';

            $location_UpdateQuery = ' UPDATE `business_location`
                                SET `business_tin` = :business_NewTin,
                                    `district` = :nbusiness_district,
                                    `sector` = :nbusiness_sector
                                WHERE `business_tin` = :businesstin
            ';

            $location_UpdateStatement = $pdo->prepare($location_UpdateQuery);
            $location_UpdateStatement->execute([
                'business_NewTin'       =>  $nbusiness_tin,
                'nbusiness_district'    =>  $nbusiness_district,
                'nbusiness_sector'      =>  $nbusiness_sector,
                'businesstin'           =>  $old_btin
            ]);

            $business_UpdateStatement = $pdo->prepare($business_UpdateQuery);
            $business_UpdateStatement->execute([
                'business_NewName'      =>  $nbusiness_name,
                'business_NewTin'       =>  $nbusiness_tin,
                'business_NewType'      =>  $nbusiness_type,
                'businesstin'           =>  $old_btin
            ]);

            if ($business_UpdateQuery && $location_UpdateQuery) {
                $update_successMessage = " Updated Successful" . $successRefreshMessage;
            }
        }
        else {
            $update_errorMessage = " Unknown Tin" . $errorRefreshMessage;
        }

    }
?>

<?php 
    include 'include/business_front.html';
?>