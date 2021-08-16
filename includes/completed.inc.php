<?php
    session_start();
    //require "../main_schedule.php";
?>
<?php
    $user = 'root'; 
    $pass = '';
    $db='orbital247';
    $conn = mysqli_connect('localhost', $user, $pass, $db);

    $userid = $_SESSION["userid"];
    $startTimeHour = (int) $_POST['startHour'];
    $startTimeMin = (int) $_POST['startMin'];

    echo($startTimeHour);
    echo($startTimeMin);

    $updateSql = "UPDATE fixedtaskwindow SET completed=1 WHERE userid=$userid AND startTimeHour=$startTimeHour AND startTimeMin=$startTimeMin;";

    mysqli_query($conn, $updateSql);

    //header("location:../main_schedule.php");
    // $userid = $_SESSION["userid"];
    // $cat = (int) $_POST['cat'];
    // $addTime = (int) $_POST['addTime'];
    // $currYear = 
    // $currMonth = 
    // $currDate = 

    // $selectSql = "SELECT * FROM totalTime WHERE  userid = $userid AND currYear = $currYear AND currMonth = $currMonth AND currDate = $currDate AND taskCat= $cat;";

    // $currResult = mysqli_query($conn, $selectSql);

    // echo 'console.log("I come till here");';

    // if ($currResult) {
    //     echo 'console.log("this is correct");';
    //     $resultCheck = mysqli_num_rows($currResult);
    //     $data = array();
    //     if ($resultCheck > 0) {
    //         echo 'console.log("I have at least 1 result");';
    //         while ($row = mysqli_fetch_assoc($currResult)) {
    //             $data[] = $row;   
    //         }   
    //         foreach($data as $single) {
    //             //$currYear = (int) $single["currYear"];
    //             //$currMonth = (int) $single["currMonth"];
    //             //$currDate = (int) $single["currDate"];
    //             $timeSpent = (int) $single["timeSpent"];

    //             //echo "console.log($remainder);";

    //             $timeSpent = $timeSpent + $addTime;

    //             //echo "console.log($remainder);";

    //             $updateSql = "UPDATE totalTime SET timeSpent=$timeSpent WHERE currYear=$currYear AND currMonth=$currMonth AND currDate=$currDate AND userid=$userid AND taskCat=$cat;"; 

    //             mysqli_query($conn, $updateSql);
    //         }
    //     }
    // } else {
    //     $insertSql = "INSERT INTO totalTime(currYear, currMonth, currDate, taskCat, timeSpent) VALUES ($currYear, $currMonth, $currDate, $cat, $timeSpent);";

    //     mysqli_query($conn, $insertSql);
    // }

    header("location:../main_schedule.php");
?>