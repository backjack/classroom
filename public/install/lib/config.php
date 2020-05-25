<?php
@session_start();
set_time_limit(3600);
//define wizard steps
$wizardSteps = [
    [
        'label'=>'Database',
        'page'=>'index',
    ],
    [
        'label'=>'Account',
        'page'=>'account'
    ],
    [
        'label'=>'Site',
        'page'=>'site'
    ],
    [
        'label'=>'Complete',
        'page'=>'complete'
    ]
];

//create mysql connection
if(isset($_SESSION['db'])){
    $db = $_SESSION['db'];

    try {
        $conn = new PDO("mysql:host=".$db['host'].";dbname=".$db['database'], $db['username'], $db['password']);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }
    catch(PDOException $e)
    {
        unset($_SESSION['db']);
        header('Location: index.php');
    }

}

function validateDb()
{
    if(!isset($_SESSION['db'])){
        header('Location: index.php');
    }
}
?>
