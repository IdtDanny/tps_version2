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
    $agent_username = $_SESSION['sessionToken']->agent_username;
    $aID = $_SESSION['sessionToken']->aID;
    $agent_name = $_SESSION['sessionToken']->agent_name;
    $agent_pin = $_SESSION['sessionToken']->agent_pin;

    # error and success alerts
    $client_successMessage = ""; 
    $client_deleteSuccessMessage = ""; 
    $update_successMessage = "";
    $client_errorMessage = ""; 
    $client_deleteErrorMessage = ""; 
    $update_errorMessage = ""; 

    # Calculating Each Number of Users, Cards, agent, agents and so on...
    $sql_agent = 'SELECT * FROM agent';
    $sql_client = 'SELECT * FROM client';

    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    # Getting The number of Agents, Cards, agent...
    $agentsCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();

    # Fetching client info ...

    $client_FetchQuery = 'SELECT * FROM `client` WHERE `referral_agent` =:agentpin ORDER BY `created_at` DESC';
    $client_FetchStatement = $pdo->prepare($client_FetchQuery);
    $client_FetchStatement->execute([ 'agentpin' => $agent_pin ]);
    $client_Result = $client_FetchStatement->fetchAll();

    # Getting user notifications

    $agent_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :agent_pin OR `sender_id` =:sagent_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $agent_notifyFetchStatement = $pdo->prepare($agent_notifyFetchQuery);
    $agent_notifyFetchStatement->execute([
        'agent_pin'     => $agent_pin,
        'sagent_pin'    => $agent_pin
    ]);
    $agent_notifyResults = $agent_notifyFetchStatement->fetchAll();

    # Getting Signed Agent Info. for update form...

    $agentFetchQuery = 'SELECT * FROM `agent` WHERE `aID` = :agentid';
    $agentFetchStatement = $pdo->prepare($agentFetchQuery);
    $agentFetchStatement->execute([
        'agentid' => $aID
    ]);
    $agentResults = $agentFetchStatement->fetch();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='client.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='client.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # Registering new agent

    if (isset($_POST['registerclient'])) {

        $client_id = $_POST['client_id'];
        $client_name = $_POST['client_name'];
        $client_mail = $_POST['client_mail'];
        $client_tel = $_POST['client_tel'];
        $client_district = $_POST['client_district'];
        $client_sector = $_POST['client_sector'];
        $date_Sent = date('Y-m-d h:i:s');
        $client_pin = rand(10000,99999);
        # $password= $agent_uname.'-'.$agent_pin;
        # $hashed_Password = md5($password);

        # checking if client exists
        $client_existFetchQuery = 'SELECT * FROM `client` WHERE `client_id` =:client_id';
        $client_existFetchStatement = $pdo->prepare($client_existFetchQuery);
        $client_existFetchStatement->execute([
            'client_id' => $client_id
        ]);
        $client_existResults = $client_existFetchStatement->fetch();

        # if exist, pop some message
        if ($client_existResults) {
            $client_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # otherwise proceed with registration process
        else {      
            # Inserting client...
            
            $sql_insert_client = " INSERT INTO `client`(`created_at`, `client_id`, `client_name`, `client_tel`, `client_mail`, `client_balance`, `referral_agent`, `status`, `approve`, `client_pin`) VALUES (:created_at, :client_id, :client_name, :client_tel, :client_mail, :client_balance, :referral_agent, :bstatus, :approve, :client_pin)";
            
            $client_InsertStatement = $pdo->prepare($sql_insert_client);
            $client_InsertStatement->execute([
                'created_at'        =>  $date_Sent,
                'client_id'         =>  $client_id,
                'client_name'       =>  $client_name,
                'client_tel'        =>  $client_tel,
                'client_mail'       =>  $client_mail,
                'client_balance'    =>  '0',
                'referral_agent'    =>  $agent_pin,
                'bstatus'           =>  'active',
                'approve'           =>  'Approved',
                'client_pin'        =>  $client_pin
            ]);
            
            if ($sql_insert_client) {

                # Getting client Info. for update form...

                $client_existFetchQuery = 'SELECT * FROM `client` WHERE `client_id` =:client_id';
                $client_existFetchStatement = $pdo->prepare($client_existFetchQuery);
                $client_existFetchStatement->execute([
                    'client_id' => $client_id
                ]);
                $client_existResults = $client_existFetchStatement->fetch();
                $cID = $client_existResults->cID;

                $sql_insert_location = "  INSERT INTO `client_location`(`cID`, `client_name`, `district`, `sector`) VALUES(:aid, :client_name, :district, :sector) ";
                $location_InsertStatement = $pdo->prepare($sql_insert_location);
                $location_InsertStatement->execute([
                        'aid'           =>  $cID,
                        'client_name'   =>  $client_name,
                        'district'      =>  $client_district,
                        'sector'        =>  $client_sector
                ]);
                if ($sql_insert_location) {
                        $client_successMessage = " Client Registered, Pin: ". $client_pin . $successRefreshMessage;
                }
            }
            else {
                $client_errorMessage = " Could not register" . $errorRefreshMessage;
            }
        }
    }

    # getting client delete response

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

    # topup client Operation...

    if (isset($_POST['topclient'])) {

        $confirm_top = $_POST['confirm_top'];
        $client_id = $_POST['client_id'];
        $ramount = $_POST['ramount'];

        # confirming agent ...

        $fetch_agentQuery='SELECT * FROM `agent` WHERE `agent_pin` = :agent_pin';
        $fetch_agentStatement = $pdo->prepare($fetch_agentQuery);
        $fetch_agentStatement->execute([
            'agent_pin' => $confirm_top
        ]);

        $agent_Info = $fetch_agentStatement -> fetch();

        $agentCount = $fetch_agentStatement->rowCount();

        # if agent is confirmed 

        if ($agentCount > 0 ) {

            # check the float agent balance to top up ...

            $agent_balance = $agent_Info->agent_balance;

            # if agent does not have enough balance to top up

            if ($agent_balance <= 0 || $agent_balance < $ramount) {
                $update_errorMessage = " Not enough balance" . $errorRefreshMessage;
            }

            # otherwise agent can top up

            else {

                # Checking for client to top ...

                $fetch_UserQuery='SELECT * FROM `client` WHERE `client_id` = :client_id';
                $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
                $fetch_UserStatement->execute([
                    'client_id' => $client_id
                ]);

                $client_Info = $fetch_UserStatement -> fetch();

                $clientCount = $fetch_UserStatement->rowCount();

                # if client found

                if ($clientCount > 0 ) {

                    # Modifying client ...

                    $client_name = $client_Info->client_name;

                    $balance = $client_Info->client_balance;

                    $balance += $ramount;

                    $client_UpdateQuery = ' UPDATE `client`
                                        SET `client_balance` = :client_balance
                                        WHERE `client_id` = :client_id
                    ';

                    $client_UpdateStatement = $pdo->prepare($client_UpdateQuery);
                    $client_UpdateStatement->execute([
                        'client_balance'   =>  $balance,
                        'client_id'        =>  $client_id
                    ]);

                    # done top up

                    if ($client_UpdateQuery) {

                        # notification

                        $sender_id = $confirm_top;
                        $receiver_id = $client_id;
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
                            'naction'       =>  'topup',
                            'astatus'       =>  'unread'
                        ]);

                        if ($sql_insert_notification){

                            # notification via email

                            $client_mail = $client_Info->client_mail;

                            # check if email is not optional@smtp.com

                            if ($client_mail == 'optional@tps.com') {
                                $update_successMessage = " Recharged Successful" . $successRefreshMessage;
                            }

                            # else send email to actual email

                            else {

                                $notify_message = "Tps M-Card: You have been recharged with ". number_format($ramount)." RWF from ". $agent_name ." at ". $date_Sent . ". Your new balance: ". number_format($balance) ." RWF.";


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
                                $mail->Subject = 'TOP UP CONFIRMATION';
                                $mail->setFrom('tap.pay.holder@gmail.com', 'Tap and Pay');
                                $mail->addAddress($client_mail);
                    
                                $mail->Body = $notify_message;

                                # if email sent
                    
                                if ($mail->Send()) {
                                    $update_successMessage = " Recharged Successful" . $successRefreshMessage;
                                } 

                                # email not sent

                                else {
                                    $update_errorMessage = " Could not send" . $errorRefreshMessage . $mail->ErrorInfo;
                                }
                            }
                        } 
                    }
                }
                else {
                    $update_errorMessage = " Unknown Client" . $errorRefreshMessage;
                }
            }
        }
        else {
            $update_errorMessage = " Incorrect Pin" . $errorRefreshMessage;
        }
    }

    # withdraw client Operation...

    if (isset($_POST['withdrawclient'])) {

        $client_confirm = $_POST['client_confirm'];
        $confirm_top = $_POST['confirm_top'];
        $client_id = $_POST['client_id'];
        $ramount = $_POST['ramount'];

        # confirming agent ...

        $fetch_agentQuery='SELECT * FROM `agent` WHERE `agent_pin` = :agent_pin';
        $fetch_agentStatement = $pdo->prepare($fetch_agentQuery);
        $fetch_agentStatement->execute([
            'agent_pin' => $confirm_top
        ]);

        $agent_Info = $fetch_agentStatement -> fetch();

        $agentCount = $fetch_agentStatement->rowCount();

        # if agent is confirmed 

        if ($agentCount > 0 ) {

            # agent info

            $agent_balance = $agent_Info->agent_balance;
            $agent_pin = $agent_Info->agent_pin;

            # Checking for client to top ...

            $fetch_UserQuery='SELECT * FROM `client` WHERE `client_id` = :client_id AND `client_pin` = :client_pin';
            $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
            $fetch_UserStatement->execute([
                'client_id' => $client_id,
                'client_pin' => $client_confirm
            ]);

            $client_Info = $fetch_UserStatement -> fetch();

            $clientCount = $fetch_UserStatement->rowCount();

            # when client is found

            if ($clientCount > 0 ) {

                # checking if client has enough money to withdraw

                $client_balance = $client_Info->client_balance;

                if ($client_balance <= 0 || $client_balance < $ramount) {
                    $update_errorMessage = " Not enought balance" . $errorRefreshMessage;
                }

                # else client having enough balance to top up

                else {

                    # modifying agent ...

                    $agent_balance += $ramount;

                    $agent_UpdateQuery = ' UPDATE `agent`
                                        SET `agent_balance` = :agent_balance
                                        WHERE `agent_pin` = :agent_pin
                    ';

                    $agent_UpdateStatement = $pdo->prepare($agent_UpdateQuery);
                    $agent_UpdateStatement->execute([
                        'agent_balance' =>  $agent_balance,
                        'agent_pin'     =>  $agent_pin
                    ]);

                    # Modifying client ...

                    $balance = $client_Info->client_balance;

                    $balance -= $ramount;

                    $client_UpdateQuery = ' UPDATE `client`
                                        SET `client_balance` = :client_balance
                                        WHERE `client_id` = :client_id
                    ';

                    $client_UpdateStatement = $pdo->prepare($client_UpdateQuery);
                    $client_UpdateStatement->execute([
                        'client_balance'   =>  $balance,
                        'client_id'        =>  $client_id
                    ]);

                    # if client and agent update is done

                    if ($client_UpdateQuery && $agent_UpdateQuery) {

                        # notification

                        $sender_id = $confirm_top;
                        $receiver_id = $client_id;
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
                            'naction'       =>  'withdraw',
                            'astatus'       =>  'unread'
                        ]);

                        if ($sql_insert_notification){

                            # notification via email

                            $client_mail = $client_Info->client_mail;

                            # check if email is not optional@smtp.com

                            if ($client_mail == 'optional@tps.com') {
                                $update_successMessage = " Withdraw Successful" . $successRefreshMessage;
                            }

                            # else send email to actual email

                            else {

                                $notify_message = "Tps M-Card: ". number_format($ramount)." RWF withdrawn from your account ". $client_id ." at ". $date_Sent . ". Your new balance: ". number_format($balance) ." RWF.";


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
                                $mail->Subject = 'TPS WITHDRAW';
                                $mail->setFrom('tap.pay.holder@gmail.com', 'Tap and Pay');
                                $mail->addAddress($client_mail);

                                $mail->Body = $notify_message;

                                # if email sent

                                if ($mail->Send()) {
                                    $update_successMessage = " Withdraw Successful" . $successRefreshMessage;
                                } 

                                # email not sent

                                else {
                                    $update_errorMessage = " Could not send" . $errorRefreshMessage . $mail->ErrorInfo;
                                }
                            }
                        }
                        else {
                            $update_errorMessage = " Could not notify" . $errorRefreshMessage;
                        }
                    }
                    else {
                        $update_errorMessage = " Could not update agent or client" . $errorRefreshMessage;
                    }
                }
            }

            # when client is not found

            else {
                $update_errorMessage = " Unknown Client" . $errorRefreshMessage;
            }
        }

        # when agent is not confirmed

        else {
            $update_errorMessage = " Incorrect Pin" . $errorRefreshMessage;
        }
    }

    # modifying current agent

    if (isset($_POST['modifyclient'])) {

        $client_id = $_POST['client_id'];
        $client_name = $_POST['client_name'];
        $client_mail = $_POST['client_mail'];
        $client_tel = $_POST['client_tel'];
        $client_district = $_POST['client_district'];
        $client_sector = $_POST['client_sector'];
        $date_Sent = date('Y-m-d h:i:s');
        # $password= $agent_uname.'-'.$agent_pin;
        # $hashed_Password = md5($password);

        # checking if client exists

        $client_existFetchQuery = 'SELECT * FROM `client` WHERE `client_id` =:client_id';
        $client_existFetchStatement = $pdo->prepare($client_existFetchQuery);
        $client_existFetchStatement->execute([
            'client_id' => $client_id
        ]);
        $client_existResults = $client_existFetchStatement->fetch();

        # if exist, update client with provided credentials ...

        if ($client_existResults) {

            # updating client...

            $sql_update_client = " UPDATE `client` SET `client_name` =:client_name, `client_tel` =:client_tel, `client_mail` =:client_mail WHERE `client_id` =:client_id";
                        
            $client_updateStatement = $pdo->prepare($sql_update_client);
            $client_updateStatement->execute([
                'client_name'       =>  $client_name,
                'client_tel'        =>  $client_tel,
                'client_mail'       =>  $client_mail,
                'client_id'         =>  $client_id
            ]);

            # after updating client

            if ($sql_update_client) {

                # updating client location ...

                $cID = $client_existResults->cID;

                $sql_update_location = "  UPDATE `client_location` SET `client_name` =:client_name, `district` =:district, `sector` =:sector WHERE `cID` =:cid";
                $location_updateStatement = $pdo->prepare($sql_update_location);
                $location_updateStatement->execute([
                    'client_name'   =>  $client_name,
                    'district'      =>  $client_district,
                    'sector'        =>  $client_sector,
                    'cid'           =>  $cID
                ]);
                if ($sql_update_location) {
                        $client_successMessage = " Client Updated, ". $successRefreshMessage;
                }
            }
            else {
                $client_errorMessage = " Could not register" . $errorRefreshMessage;
            }
        }

        # otherwise they should register first

        else {      
            $client_errorMessage = " Not registered" . $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/records_front.html';
?>