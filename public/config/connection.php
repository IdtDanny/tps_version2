<?php

    # Database Properties Declaration...

    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'tpay';

    # Setting The DSN...

    $dsn = 'mysql:host='. $host . ';dbname='. $dbname;

    # Instantiating The PDO Class...
    $pdo = new PDO($dsn, $user, $pass);

    # Setting Default Attributes for PDO...
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);



    # Testing The Connection...

    if(!$pdo) echo "<script>alert('Problems with Connections')</script>";

?>
