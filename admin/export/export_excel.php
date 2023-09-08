<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../../index.php");
    }

    # Includes...
    require_once '../../public/config/connection.php';

    # extracting business list

    # fetching current business tin from database
        
    $tin = 'SELECT * FROM `business` ORDER BY `Date` DESC';
    $business_FetchStatement = $pdo->prepare($tin);
    $business_FetchStatement->execute();
    // $business_listTin = $business_FetchStatement->fetch();
    
    $output .= '                        
        <table bordered = "1">
            <caption>List of Registered businesses</caption>
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Created_at</th>
                <th scope="col">Business Name</th>
                <th scope="col">TIN</th>
                <th scope="col">Type</th>
                <th scope="col">Pin</th>
                <th scope="col">Balance</th>
                <th scope="col">Status</th>
                </tr>
            </thead>';
    $no = 1;
    while ($business = $business_FetchStatement->fetch()) {
        $output .= '
                <tr>
                <th scope="row">'. $no .'</th>
                <td>'. $business->Date .'</td>
                <td>' . $business->business_name . '</td>
                <td>'. $business->business_tin .'</td>
                <td>'. $business->business_type .'</td>
                <td>'. $business->business_pin .'</td>
                <td>'. number_format($business->balance) .'</td>
                <td>' . $business->status . '</td> 
                </tr>';
        $no++; 
    }
    $output .= '</tbody></table';
    
    #exporting pdf document
    header('Content-Disposition: attachment; filename=business_list_on_'. date('Y-m-d h:i:s') .'.xlsx');
    header('Content-Type: application/vnd.ms-excel');
    echo $output;
?>