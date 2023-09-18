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
    $photo = $_SESSION['sessionToken']->photo;

    # error and success alerts

    $notify_updateSuccessMessage = "";
    $notify_updateErrorMessage = "";
    $notify_deleteSuccessMessage = "";
    $notify_deleteErrorMessage = "";

    # Getting agent Info. for update form...

    $agentFetchQuery = 'SELECT * FROM `agent` WHERE `aID` = :aID';
    $agentFetchStatement = $pdo->prepare($agentFetchQuery);
    $agentFetchStatement->execute([
        'aID' => $aID
    ]);
    $agentResults = $agentFetchStatement->fetch();

    # Calculating Each Number of Users, Cards, agent, agents and so on...
    
    $sql_agent = 'SELECT * FROM agent';
    $sql_client = 'SELECT * FROM client';
    $sql_agent = 'SELECT * FROM agent';

    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    $statement_agent = $pdo->prepare($sql_agent);
    $statement_agent -> execute();

    # Getting The number of Agents, Cards, agent...
    $agentsCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();
    $registered_agent = $statement_agent -> rowCount();

    # Fetching agent info ...

    $agent_FetchQuery = 'SELECT * FROM `agent` ORDER BY `created_at` DESC';
    $agent_FetchStatement = $pdo->prepare($agent_FetchQuery);
    $agent_FetchStatement->execute();
    $agent_Result = $agent_FetchStatement->fetchAll();

    # Getting user notifications

    $agent_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :agent_pin OR `sender_id` =:sagent_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $agent_notifyFetchStatement = $pdo->prepare($agent_notifyFetchQuery);
    $agent_notifyFetchStatement->execute([
        'agent_pin'     => $agent_pin,
        'sagent_pin'    => $agent_pin
    ]);
    $agent_notifyResults = $agent_notifyFetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='notification.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='notification.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # getting agent delete response
    if (isset($_GET['vnID'])) {
        $dnID = $_GET['vnID'];
        $sql_nupdate = 'UPDATE `notification` SET `status` =:nread WHERE `nID` = :nid';

        # PDO Prep & Exec..
        $update_notify = $pdo->prepare($sql_nupdate);
        $update_notify->execute([
            'nread'    => 'read',
            'nid'       =>  $dnID
        ]);

        if ($sql_nupdate) {
            $notify_updateSuccessMessage = " Viewed" . $successRefreshMessage;
        }
        else {
            $notify_updateErrorMessage = " Could not update, check agent id" . $errorRefreshMessage;
        }

    }

    # getting notification delete response

    if (isset($_GET['dnID'])) {
        $dnID = $_GET['dnID'];
        $sql_ndelete = 'DELETE FROM `notification_all` WHERE nID = :nid';

        # PDO Prep & Exec..
        $delete_notify = $pdo->prepare($sql_ndelete);
        $delete_notify->execute([
            'nid'  =>  $dnID
        ]);

        if ($sql_ndelete) {
            $notify_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $notify_deleteErrorMessage = " Could not delete, check agent id" . $errorRefreshMessage;
        }
    }

    # view notification response

    if (isset($_GET['vnID'])) {
        $vnID = $_GET['vnID'];
        $sql_nview = 'UPDATE `notification_all` SET `status` = :nstatus WHERE `nID` = :nid';

        # PDO Prep & Exec..
        $view_notify = $pdo->prepare($sql_nview);
        $view_notify->execute([
            'nstatus'  =>  'read',
            'nid'      =>  $vnID
        ]);

        if ($sql_nview) {
            $notify_viewSuccessMessage = " Viewed Successful" . $successRefreshMessage;
        }
        else {
            $notify_viewErrorMessage = " Could not view, check id" . $errorRefreshMessage;
        }
    }
    
?>

<?php 
    include 'include/notification_front.html';
?>