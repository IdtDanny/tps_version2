<?php

    session_start();
    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../../index.php");
    }

    # Includes...
    require_once '../../public/config/connection.php';

    # Getting The Sent business ID for delete...
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
            header("location: ../business.php?y");
            $busy_deleteSuccessMessage = " Deleted Successful";
        }
        else {
            header("location: ../business.php?n");
            $busy_deleteErrorMessage = " Could not delete, check business id";
        }
    }
?>