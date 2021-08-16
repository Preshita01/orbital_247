<?php
    //session_start();
    //include "../includes/add_daily_task.inc.php";
    //include "../includes/insertWindow_helper.inc.php";
    $user = 'root'; 
    $pass = '';
    $db='orbital247';
    $conn = mysqli_connect('localhost', $user, $pass, $db);

    $test = array("2", "8");
    $possibleRejection = "SELECT * FROM remainingtime WHERE userid = $test[1];";
    $rejections = mysqli_query($conn, $possibleRejection);
    
    // Lawyer is available and has not rejected beneficiary before

    echo ("I make it here");
    if ($rejections) {
        echo ("I make it here");
        $resultCheck3 = mysqli_num_rows($rejections);
        $data3= array();
        $result = array("currYear", "currMonth", "currDate", "remainder", "userid");

        if ($resultCheck3 > 0) {
            echo 'console.log("I have at least 1 result");';
            while ($row = mysqli_fetch_assoc($rejections)) {
                $data3[] = $row;   
            }
            foreach($data3 as $single) {
                echo ("I make it here");
                echo ($single[$result[3]]);
                //array_push($result, $single);
            }
            //print_r($result[0]);
        }
    }

    $event = "CREATE EVENT reset
    ON SCHEDULE
      EVERY 1 MINUTE
        DO
        update remainingtime 
        set remainder = 5 
        where userid = -5;";
    mysqli_query($conn, $event);
?>