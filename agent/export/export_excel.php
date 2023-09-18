<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../../index.php");
    }

    # Includes...
    require_once '../../public/config/connection.php';

    # Getting Information of Signed in User
    $agent_username = $_SESSION['sessionToken']->agent_username;
    $aID = $_SESSION['sessionToken']->aID;
    $agent_name = $_SESSION['sessionToken']->agent_name;
    $agent_pin = $_SESSION['sessionToken']->agent_pin;


    # extracting client list

    # fetching current client tin from database
        
    $client_fetch = 'SELECT * FROM `client` ORDER BY `created_at` DESC';
    $client_FetchStatement = $pdo->prepare($client_fetch);
    $client_FetchStatement->execute([
    ]);
    // $client_listTin = $client_FetchStatement->fetch();
    
    $output .= '                        
        <table bordered = "1">
            <caption>List of Registered clientes</caption>
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Created_at</th>
                <th scope="col">Client ID</th>
                <th scope="col">Client Name</th>
                <th scope="col">Client Mail</th>
                <th scope="col">Client Tel</th>
                <th scope="col">Balance</th>
                </tr>
            </thead>';
    $no = 1;
    while ($client = $client_FetchStatement->fetch()) {
        $output .= '
                <tr>
                <th scope="row">'. $no .'</th>
                <td>'. $client->created_at .'</td>
                <td>' . $client->client_id . '</td>
                <td>' . $client->client_name . '</td>
                <td>' . $client->client_mail . '</td>
                <td>'. $client->client_tel .'</td>
                <td>'. number_format($client->client_balance) .'</td>
                </tr>';
        $no++; 
    }
    $output .= '</tbody></table';
    
    #exporting pdf document
    header('Content-Disposition: attachment; filename=client_list_on_'. date('Y-m-d h:i:s') .'.xls');
    header('Content-Type: application/vnd.ms-excel');
    echo $output;
?>