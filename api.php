<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
 header("Access-Control-Allow-Origin: *");
$con = mysqli_connect("localhost","root","password","trainproblem");

// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit;
}

//$sql = "select t.train_name,c.coach_no,c.no_of_seat from train t, coach c, train_coach_mapping tcm where tcm.train_no = '$trainNo', t.train_no = tcm.train_no, c.coach_no = tcm.coach_no, tcm.coach_no='$coachNo'";
$trainNo = 1;
$isSeatAvailable = 1;
$coachNo = 1;
$userId = 1;
$seatBooked = 0;
if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="reserve"){
    //check if count is available 
    //if yes book them
    //if no return error msg
    $_REQUEST["data"] = json_decode($_REQUEST["data"]);
    $numRequired = $_REQUEST["data"]->requiredSeats;
    $trainNo = intval($_REQUEST["data"]->train);
    $coachNo = intval($_REQUEST["data"]->coach);
    $userId = intval($_REQUEST["data"]->user);
    $sql = "select max(seat_no) as reserved_till from user_reservation where train_no='1'";
    $rs = mysqli_query($con,$sql);
    $rowData =mysqli_fetch_assoc($rs);
    if((70 - $rowData["reserved_till"]) < $numRequired){
        $isSeatAvailable = 0;
    }else{
        while($numRequired > 0){
            $seat  = $rowData["reserved_till"] + $numRequired;
            $sql = "insert into user_reservation set train_no = '$trainNo',
            coach_no='$coachNo',
            user_id='$userId',seat_no='$seat'";
            if(!mysqli_query($con,$sql)){
                echo "exiting";exit;
            }
            $numRequired--;
        }
        $seatBooked = 1;
    }

}
$trainSeats = array();
$tempArr = array();
foreach(range(1,70) as $index=>$seat_no){
    $tempArr['seat_no'] = $seat_no;
    $tempArr['status'] = 'Available';
    $tempArr['user_id'] = 0;
    array_push($trainSeats,$tempArr);
}

$sql = "select * from user_reservation where train_no ='$trainNo'";
if($rs = mysqli_query($con,$sql)){
    while($row = mysqli_fetch_assoc($rs)){
        $key = array_search($row['seat_no'], array_column($trainSeats, 'seat_no'));
        $trainSeats[$key]["status"] = "Reserved";
    }
}else{
    echo json_encode(["status"=>0,"msg"=>"Failed to query server","data"=>[]]);
}

echo json_encode([
    "status"=>1,
    "data"=>$trainSeats,
    "msg"=>"",
    "booked"=>$seatBooked,
    "available"=>$isSeatAvailable
]);
exit;
?>