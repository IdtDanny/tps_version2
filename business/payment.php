<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # including PHPMailer
    # PHP Mailer

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../PHPMailer/PHPMailer.php';
    require '../PHPMailer/SMTP.php';
    require '../PHPMailer/Exception.php';

    # Getting Information of Signed in User
    $business_username = $_SESSION['sessionToken']->business_username;
    $bID = $_SESSION['sessionToken']->bID;
    $business_name = $_SESSION['sessionToken']->business_name;
    $business_tin = $_SESSION['sessionToken']->business_tin;
    $business_pin = $_SESSION['sessionToken']->business_pin;
    $business_balance = $_SESSION['sessionToken']->balance;

    # error and success alerts
    $client_successMessage = ""; 
    $client_deleteSuccessMessage = ""; 
    $update_successMessage = "";
    $client_errorMessage = ""; 
    $client_deleteErrorMessage = ""; 
    $update_errorMessage = ""; 

    # Calculating Each Number of Users, Cards, business, businesss and so on...
    $sql_business = 'SELECT * FROM business';
    $sql_client = 'SELECT * FROM client';

    $statement = $pdo->prepare($sql_business);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    # Getting The number of businesss, Cards, business...
    $businesssCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();

    # Getting Signed business records Info. for update form...

    $recordFetchQuery = 'SELECT * FROM `records` WHERE `rID` = :recordid';
    $recordFetchStatement = $pdo->prepare($recordFetchQuery);
    $recordFetchStatement->execute([
        'recordid' => $business_tin
    ]);
    $recordResults = $recordFetchStatement->fetchAll();

    # Getting Signed business Info. for update form...

    $businessFetchQuery = 'SELECT * FROM `business` WHERE `bID` = :businessid';
    $businessFetchStatement = $pdo->prepare($businessFetchQuery);
    $businessFetchStatement->execute([
        'businessid' => $bID
    ]);
    $businessResults = $businessFetchStatement->fetch();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='payment.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='payment.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # payment client Operation...

    if (isset($_POST['client_pay'])) {

        $confirm_pay = $_POST['confirm_pay'];
        $client_id = $_POST['client_id'];
        $pamount = $_POST['pamount'];

        # confirming client ...

        $fetch_clientQuery='SELECT * FROM `client` WHERE `client_pin` = :client_pin';
        $fetch_clientStatement = $pdo->prepare($fetch_clientQuery);
        $fetch_clientStatement->execute([
            'client_pin' => $confirm_pay
        ]);

        $client_Info = $fetch_clientStatement -> fetch();

        $clientCount = $fetch_clientStatement->rowCount();

        # if client is confirmed 

        if ($clientCount > 0 ) {

            # check the float client balance to pay ...

            $client_balance = $client_Info->client_balance;

            # if client does not have enough balance to top up

            if ($client_balance <= 0 || $client_balance < $pamount) {
                $update_errorMessage = " Not enough balance" . $errorRefreshMessage;
            }

            # otherwise client can pay

            else {

                # getting and modifying business details ...

                $business_tin = $businessResults->business_tin;
                $business_name = $businessResults->business_name;
                $business_balance = $businessResults->balance;
                $business_balance += $pamount;

                $business_UpdateQuery = ' UPDATE `business`
                                    SET `balance` = :balance
                                    WHERE `business_tin` = :business_tin
                ';

                $business_UpdateStatement = $pdo->prepare($business_UpdateQuery);
                $business_UpdateStatement->execute([
                    'balance'       =>  $business_balance,
                    'business_tin'  =>  $business_tin
                ]);

                # Modifying client ...

                $client_name = $client_Info->client_name;

                $client_balance = $client_Info->client_balance;

                $client_balance -= $pamount;

                $client_UpdateQuery = ' UPDATE `client`
                                    SET `client_balance` = :client_balance
                                    WHERE `client_id` = :client_id
                ';

                $client_UpdateStatement = $pdo->prepare($client_UpdateQuery);
                $client_UpdateStatement->execute([
                    'client_balance'   =>  $client_balance,
                    'client_id'        =>  $client_id
                ]);

                # done with transactions ...

                if ($client_UpdateQuery) {

                    
                    $sender_id = $client_id;
                    $receiver_id = $business_tin;
                    $amount = $pamount;
                    $date_Sent = date('Y-m-d h:i:s');
                    $time_Sent = date('h:i:s');

                    # record keeping

                    $record_insertQuery = ' INSERT INTO `records`(`rdate`, `rtime`, `rID`, `client_id`, `client_name`, `amount`, `action`, `status`) 
                    VALUES(:rdate, :rtime, :rID, :client_id, :client_name, :amount, :raction, :rstatus)';

                    $record_insertStatement = $pdo->prepare($record_insertQuery);
                    $record_insertStatement->execute([
                        'rdate'         =>  $date_Sent,
                        'rtime'         =>  $time_Sent,
                        'rID'           =>  $business_tin,
                        'client_id'     =>  $client_id,
                        'client_name'   =>  $client_name,
                        'amount'        =>  $pamount,
                        'raction'       =>  'paid',
                        'rstatus'       =>  'approved'
                    ]);

                    # notification ...

                    $sql_insert_notification = " INSERT INTO `notification_all`(`date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES (:date_sent, :time_sent, :receiver_id, :sender_id, :amount, :naction, :astatus)";

                    $notification_InsertStatement = $pdo->prepare($sql_insert_notification);
                    $notification_InsertStatement->execute([
                        'date_sent'     =>  $date_Sent,
                        'time_sent'     =>  $time_Sent,
                        'receiver_id'   =>  $receiver_id,
                        'sender_id'     =>  $sender_id,
                        'amount'        =>  $amount,
                        'naction'       =>  'payment',
                        'astatus'       =>  'unread'
                    ]);

                    if ($sql_insert_notification && $record_insertQuery){

                        # notification via email

                        $client_mail = $client_Info->client_mail;

                        # check if email is not optional@smtp.com

                        if ($client_mail == 'optional@tps.com') {
                            $update_successMessage = " Paid Successful" . $successRefreshMessage;
                        }

                        # else send email to actual email

                        else {

                            $notify_message = "Tps M-Card: You have paid ". number_format($pamount) ." RWF to ". $business_name ." (". $business_tin .") at ". $date_Sent . ". Your new balance: ". number_format($client_balance) ." RWF.";


                            $mail = new PHPMailer();
                            $mail->isSMTP();
                
                            $mail->SMTPDebug = 0;
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = '587';
                            $mail->Username = 'tap.pay.holder@gmail.com';
                            $mail->Password = 'zkwulxaitrxcovfh';
                
                            $mail->isHTML(true);
                            $mail->Subject = 'PAYMENT CONFIRMATION';
                            $mail->setFrom('tap.pay.holder@gmail.com', 'Tap and Pay');
                            $mail->addAddress($client_mail);
                
                            $mail->Body = $notify_message;

                            # if email sent
                
                            if ($mail->Send()) {
                                $update_successMessage = " Paid Successful" . $successRefreshMessage;
                            } 

                            # email not sent

                            else {
                                $update_errorMessage = " Could not send" . $errorRefreshMessage . $mail->ErrorInfo;
                            }
                        }
                    } 
                }
            }
        }
        else {
            $update_errorMessage = " Incorrect Pin" . $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/payment_front.html';
?>