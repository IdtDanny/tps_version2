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

    // $stmt = $pdo->prepare($usedCardsSql);
    // $stmt2 = $pdo->prepare($usedCardsSql);
    // $stmt->execute([
        // 'approve' => 'Approved'
    // ]);
    // $stmt2->execute([
        // 'approve' => 'Approved'
    // ]);

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

# Updating Admin Information...

if (isset($_POST['editinfo'])) {
    $new_Admin_Name = $_POST['admin-name'];
    $new_Admin_Username = $_POST['admin-username'];
    $admin_Old_Password = $_POST['old-password'];
    $admin_New_Password = $_POST['new-password'];
    $admin_Confirm_password = $_POST['confirm-password'];

    # Checking for Password fields(if they are empty, It will only update the username or name only)...

    if (empty($admin_Old_Password)) {

        # Updating Query...

        $admin_Update_Query = 'UPDATE `admin`
                                SET `admin_name` = :adminname,
                                    `admin_username` = :adminusername
                                WHERE `admin_ID` = :adminid
        ';

        $admin_Update_stmt = $pdo->prepare($admin_Update_Query);
        $admin_Update_stmt->execute([
            'adminname'     =>  $new_Admin_Name,
            'adminusername' =>  $new_Admin_Username,
            'adminid'       =>  $admin_ID
        ]);
        $successMessage = " Username Edited Successfully";
    }
    else {

        # Checking if the old password match...

        $hashedpass = md5($admin_Old_Password);
        
        // $hashedpass = $admin_Old_Password;

        if ($adminResults->admin_password == $hashedpass || $adminResults->admin_password == $admin_Old_Password ) {

            if ($admin_New_Password == $admin_Confirm_password) {

                # Update Query Including Passwords...

                $admin_Update_Query = 'UPDATE `admin`
                                        SET `admin_name` = :adminname,
                                            `admin_username` = :adminusername,
                                            `admin_password` = :adminpassword
                                        WHERE `admin_ID` = :adminid
                ';

                $admin_Update_stmt = $pdo->prepare($admin_Update_Query);
                $admin_Update_stmt->execute([
                    'adminname'     =>  $new_Admin_Name,
                    'adminusername' =>  $new_Admin_Username,
                    'adminpassword' =>  md5($admin_New_Password),
                    'adminid'       =>  $admin_ID
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
    $target_file = $target_dir . basename($_FILES["admin-profile"]["name"]);
    $photo = $_FILES['admin-profile']['name'];
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    # Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["admin-profile"]["tmp_name"]);
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
    if ($_FILES["admin-profile"]["size"] > 400000) {
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
        if (move_uploaded_file($_FILES["admin-profile"]["tmp_name"], $target_file)) {        
            
            # Updating admin profile...
            $profile_update = 'UPDATE `admin` 
                                SET `admin_profile` = :photo 
                                WHERE `admin_ID` = :adminid
                            ';
      
            $admin_updateStatement = $pdo->prepare($profile_update);
            $admin_updateStatement->execute([
                                'photo'     =>  $photo,
                                'adminid'   =>  $adminResults->admin_ID
                            ]);
        
            if ($profile_update) {
                $photo_successMessage = " Profile Edited";
            }
        } 
        else {
            $photo_errorMessage = " Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>

    <!-- Icon Header -->
    <link rel="shortcut icon" type="image/png" href="../public/img/card_Header.png">

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="../node_modules/css/main.min.css">

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
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-xxl">
            <a href="#dashboard" class="navbar-brand">
                <span class="text-altlight fw-medium ps-4 display-6">Tap & Pay</span>
            </a>
            <!-- Toggle button for mobile nav -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- navbar links -->
            <div class="collapse navbar-collapse justify-content-end align-center ps-4" id="main-nav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="#dashboard" class="nav-link">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a href="#client" class="nav-link">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a href="#business" class="nav-link">Business</a>
                    </li>
                    <li class="nav-item">
                        <a href="#agent" class="nav-link">Agents</a>
                    </li>
                    <li class="nav-item d-md-none">
                        <a href="#notify" class="nav-link">Notification</a>
                    </li>
                    <!-- <li class="nav-item ms-2 d-none d-md-inline"> -->
                    <li class="nav-item ms-2 d-md-inline">
                        <a href="logout.php" class="btn btn-light">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Profile -->
    <section id="dashboard bg-light">
        <div class="container-md">
            <div class="text-center">
                <h2>Welcome <span class="text-altprimary fst-italic fw-bold"><?php echo $adminResults->admin_name ?></span> </h2>
                <p class="lead text-muted">A quick glance at your profile</p>
            </div>

            <div class="row my-0 g-3 justify-content-around align-items-center">
                <div class="col-6 col-lg-4">

                    <!-- Notifying about profile change | Error -->
                    <?php if($photo_errorMessage): ?>
                    <div class="alert alert-warning alert-dismissible fade show alert-danger" role="alert">
                        <strong class="bi bi-exclamation-triangle"><?php echo $photo_errorMessage; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif ?>

                    <!-- Success message - successful changed profile -->
                    <?php if($photo_successMessage): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong class="bi bi-check2-all"><?php echo $photo_successMessage; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif ?>

                    <!-- browsing profile picture or else user svg used -->
                    <?php if($adminResults->admin_profile): ?>
                    <img src="../public/profile/<?php echo $adminResults->admin_profile ?>" alt="eBook" class="rounded-5 img-thumbnail border-2 border-dark">
                    <?php else: ?>
                    <img src="../public/img/user-profile.svg" alt="eBook" class="rounded-5 img-thumbnail p-5 border-2 border-dark">
                    <?php endif ?>

                    <form method="post" enctype="multipart/form-data" class="pt-3 input-group">
                        <input type="file" name="admin-profile" class="form-control ms-2" id="inputGroupFile02" aria-describedby="inputGroupFileAddon02" aria-label="Upload">
                        <button class="btn btn-dark" name="submit-profile" type="submit" id="inputGroupFileAddon02"><i class="bi bi-cloud-upload-fill"></i></button>
                        <a href="../admin/" class="btn btn-gold me-2" title="Refresh to see the changes"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </form>

                </div>

                <!-- accordion -->
                <div class="col-lg-6">
                    <div class="accordion accordion-flush" id="chapters">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button text-light bi-person-fill-gear bg-dark" type="button" data-bs-toggle="collapse" data-bs-target="#chapter-1" aria-expanded="true" aria-controls="chapter-1">&nbsp;Personal Profile</button>
                            </h2>
                            <div id="chapter-1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#chapters">
                                <div class="accordion-body">
                                    <p class="text-muted text-start fst-italic">Review your profile and login credentials <span class="badge bg-gold float-end"><i class="bi bi-person-fill"></i></span></p>
                                    <p>Full Name: <span class="fw-medium float-end"><?php echo $adminResults->admin_name ?></span></p>
                                    <p>Username: <span class="fw-light float-end"><?php echo $adminResults->admin_username ?></span></p>
                                    <p>Email: <span class="fw-light fst-italic float-end"><?php echo $adminResults->admin_email ?></span></p>
                                    <p>Balance: <span class="fw-bold float-end"><?php echo number_format($adminResults->Balance) ?> rwf</span></p>
                                    <a href="#edit" class="btn btn-gold bi-pencil-square"><small> Edit</small></a>
                                </div>
                            </div>
                        </div>

                        <!-- Business info -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button text-dark bi-building-fill collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#chapter-2" aria-expanded="false" aria-controls="chapter-2">&nbsp;Business Info</button>
                            </h2>
                            <div id="chapter-2" class="accordion-collapse collapse collapse" aria-labelledby="heading2" data-bs-parent="#chapters">
                                <div class="accordion-body">
                                    <p class="text-muted fst-italic">
                                        Apparently there are <?php echo number_format($registered_business) ?> Registered Business
                                        <span class="badge bg-gold float-end">
                                            <i class="bi bi-building"></i>
                                        </span>
                                    </p>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item fw-medium">
                                            Gas Stations:
                                            <span class="fw-light float-end">
                                                <?php echo $gas_business ?> Stations
                                            </span> 
                                        </li>
                                        <li class="list-group-item fw-medium">
                                            Other Type: 
                                            <span class="fw-light float-end">
                                                <?php echo number_format($others_business) ?> Businesses
                                            </span> 
                                        </li>
                                    </ul>
                                    <a href="#business" class="btn btn-gold bi-view-list mt-2"><small> View Details</small></a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button collapsed bi-journal-bookmark-fill" type="button" data-bs-toggle="collapse" data-bs-target="#chapter-3" aria-expanded="false" aria-controls="chapter-3">&nbsp;Agents Info</button>
                            </h2>
                            <div id="chapter-3" class="accordion-collapse collapse collapse" aria-labelledby="heading3" data-bs-parent="#chapters">
                                <div class="accordion-body">
                                    <p class="text-muted">Info related to registered business and their current performance</p>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading4 client">
                                <button class="accordion-button collapsed bi-credit-card-fill" type="button" data-bs-toggle="collapse" data-bs-target="#chapter-4" aria-expanded="false" aria-controls="chapter-4">&nbsp;Card Info</button>
                            </h2>
                            <div id="chapter-4" class="accordion-collapse collapse collapse" aria-labelledby="heading3" data-bs-parent="#chapters">
                                <div class="accordion-body">
                                    <p class="text-muted">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Vero, deleniti eius cumque quae esse, aliquam rerum sit provident non necessitatibus ducimus neque asperiores tempora quia a mollitia dolorem. Neque, eligendi?
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Business review -->
    <section id="business" class="bg-light">
        <div class="container-lg">
            <div class="text-center text-gold">
                <h2><i class="bi bi-stars"></i> Business Reviews</h2>
                <p class="lead text-muted">Different business and their current progress</p>
            </div>

            <div class="row justify-content-center my-5">
                <div class="col-lg-8">
                    <div class="list-group">
                    <?php foreach ($business_Result as $busy_count => $business){ ?>
                        <?php 
                            # To list few of agents. 
                                if ($busy_count == 3) {
                                    break;
                                }
                                else {
                        ?>
                        <div class="list-group-item py-3">
                            <h5 class="mb-1 text-uppercase">
                                <?php if ($business->business_type == "gas") { ?>
                                    <i class="bi-fuel-pump text-dark-emphasis"></i>
                                <?php } else { ?>
                                    <i class="bi-shop text-dark-emphasis"></i>
                                <?php } ?>
                                &nbsp;
                                <?php 
                                    if ($business->status == "Active") {
                                        echo "<span class='text-success'>". $business->business_name . "</span>";
                                ?>
                                    <span class="badge fw-light bg-success float-end"><small><?php echo "$business->status" ?></small></span>
                                <?php 
                                    } else {
                                        echo "<span class='text-danger'>". $business->business_name . "</span>";
                                ?>
                                    <span class="badge fw-light bg-danger float-end"><small><?php echo "$business->status" ?></small></span>
                                <?php } ?>
                            </h5>
                            <p class="mb-1 fw-medium">Location:
                                <span class="fw-light">
                                <?php
                                    # Fetching business location info ...

                                    $business_tin = $business->business_tin;

                                    $buslocation_FetchQuery = 'SELECT * FROM `business_location` WHERE `business_tin` = :btin';
                                    $buslocation_FetchStatement = $pdo->prepare($buslocation_FetchQuery);
                                    $buslocation_FetchStatement->execute([
                                                'btin'  =>  $business_tin
                                            ]);
                                    $buslocation_Result = $buslocation_FetchStatement->fetch(); 

                                    echo $buslocation_Result->district . " District - ";
                                    echo $buslocation_Result->sector . " Sector";
                                ?>
                                </span>
                            </p>
                            <p class="mb-1 fw-medium">TIN:
                                <span class="fw-light">
                                    <?php echo $business->business_tin ?>
                                </span>
                            </p>
                            <small class="fw-lighter fst-italic">Registered by <?php echo $admin_username ?></small>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    </div>
                    <a href="../admin/business.php" class="btn btn-gold float-end mt-3 bi-database" title="View More Details"> View More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Agent info -->
    <section id="agent">
        <div class="container-lg">
            <div class="text-center">
                <h2>Your current agents</h2>
                <p class="lead text-muted">Interact and reach out to agents.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-6 col-lg-4">
                    <div class="card border-dark border-2 rounded-3">
                        <div class="card-header text-center text-dark">Most Recent</div>
                        <div class="card-body">

                            <!-- Listing user agents -->
                            <div class="list-group list-group-flush">
                            <?php foreach($agent_Result as $list_count => $agent){ ?>
                                <?php 
                                # To list few of agents. 
                                    if ($list_count == 4) {
                                        break;
                                    }
                                    else {
                                ?>
                                <a href="#?<?php echo $agent->aID ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            <img src="../public/profile/<?php echo $agent->photo ?>" class="img-fluid rounded-1" style="width: 30px;">&nbsp;
                                            <?php echo $agent->agent_name ?>
                                        </h5>
                                        <small class="text-body-secondary">
                                            <?php
                                                # Calculating date differences for agents from database
                                                $now = time();
                                                $cdate = strtotime($agent->created_at);
                                                $day_diff = $now - $cdate;
                                                $hours = floor($day_diff / 3600);
                                                $minutes = ceil($day_diff / 60 % 60);
                                                $seconds = ceil($day_diff % 60);
                                                $day_passed = floor($hours / 24);
                                                $month = floor($day_passed / 30);
                                                $years = floor($month / 12);
                                                if ($years == 0){
                                                    if ($month == 0){
                                                        if ($day_passed == 0) {
                                                            if ($hours == 0) {
                                                                if ($minutes == 0) {
                                                                    echo $seconds . " seconds ago";
                                                                }
                                                                else{
                                                                    echo $minutes . " minutes ago";
                                                                }
                                                            }
                                                            else{
                                                                echo $hours . " hours ago";
                                                            }
                                                        }
                                                        else {
                                                            echo $day_passed . " days ago";
                                                        }
                                                    }
                                                    else {
                                                        echo $month . " months ago";
                                                    }
                                                }
                                                else {
                                                    echo $years . " years ago";
                                                }
                                            ?>
                                        </small>
                                    </div>
                                    <p class="mb-1">Holds accountable using <em class="fw-light"><?php echo $agent->agent_username ?></em> as username.</p>
                                    <small class="text-body-secondary">User_ID: <?php echo $agent->aID ?></small>
                                </a>
                                <?php }?>
                            <?php }?>
                            </div>                            

                            <a href="agent.php" class="btn btn-altlight float-end mt-3">View More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile edit form -->
    <section id="edit" class="bg-light">
        <div class="container-lg">
            <div class="text-center">
                <h2 class="fw-medium text-gold">Modify your Profile</h2>
                <p class="lead">Review and modify whatever you wish!</p>
            </div>

            <div class="row justify-content-center my-5">
                <div class="col-lg-6">

                    <?php
                    if($errorMessage): ?>
                        <div class="alert alert-warning alert-dismissible fade show alert-danger" role="alert">
                            <strong class="bi bi-exclamation-triangle"><?php echo $errorMessage; ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endif ?>

                        <?php
                    if($successMessage): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong class="bi bi-check2-all"><?php echo $successMessage; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif ?>

                            <form method="post">
                                <label for="email" class="form-label">Email address:</label>
                                <div class="mb-4 input-group">
                                    <span class="input-group-text">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                                    <input type="text" name="admin-name" id="email" class="form-control" placeholder="<?php echo $adminResults->admin_name ?>">

                                    <!-- tooltips -->
                                    <span class="input-group-text">
                                <span class="tt" data-bs-placement="bottom" title="Re-new your email address.">
                                <i class="bi bi-info"></i>
                                </span>
                                    </span>
                                </div>

                                <label for="name" class="form-label">Username:</label>
                                <div class="mb-4 input-group">
                                    <span class="input-group-text">
                                <i class="bi bi-person-fill"></i>
                            </span>
                                    <input type="text" name="admin-username" id="name" class="form-control" placeholder="<?php  echo $adminResults->admin_username ?>" required>

                                    <!-- tooltips -->
                                    <span class="input-group-text">
                                <span class="tt" data-bs-placement="bottom" title="Re-new your name!">
                                <i class="bi bi-info"></i>
                                </span>
                                    </span>
                                </div>

                                <label for="name" class="form-label">Current Password:</label>
                                <div class="mb-4 input-group">
                                    <span class="input-group-text">
                                <i class="bi bi-key-fill"></i>
                            </span>
                                    <input type="password" name="old-password" id="opassword" class="form-control" placeholder="old-password" value="<?php echo $adminResults->admin_password ?>" required>

                                    <!-- tooltips -->
                                    <span class="input-group-text">
                                <span class="tt" data-bs-placement="bottom" title="How should we call you?">
                                <i class="bi bi-info"></i>
                                </span>
                                    </span>
                                </div>

                                <label for="name" class="form-label">Set Password:</label>
                                <div class="mb-4 input-group">
                                    <span class="input-group-text">
                                <i class="bi bi-key-fill"></i>
                            </span>
                                    <input type="password" name="new-password" id="opassword" class="form-control" placeholder="New" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                        required>

                                    <input type="password" name="confirm-password" id="npassword" class="form-control" placeholder="Confirm" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                        required>
                                    <!-- tooltips -->
                                    <span class="input-group-text">
                                <span class="tt" data-bs-placement="bottom" title="Make sure they match!">
                                <i class="bi bi-info"></i>
                                </span>
                                    </span>
                                </div>

                                <div class="mb-4 text-center">
                                    <button type="submit" name="editinfo" class="btn btn-gold float-end">Submit</button>
                                </div>
                            </form>
                </div>
            </div>
        </div>
    </section>

    <!-- get updates / modal trigger -->
    <section class="bg-altlight">
        <div class="container">
            <div class="text-center">
                <!-- <h2>Stay in the Loop</h2> -->
                <p class="lead">Copyrights &copy; 2023</p>
            </div>
        </div>
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