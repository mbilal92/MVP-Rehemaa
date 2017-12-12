<?php

include('get_db.php');
header('Content-type: application/json');

if(isset($_GET['user_id']) && isset($_GET['call_id'])) {

	$uid     = $_GET['user_id'];
	$call_id = $_GET['call_id'];
	$from = "ROBO"
	if (isset($_GET('from'))) {
		$from = "POLLY"
	}

	$sql = 'INSERT INTO `transfer log` (phNo, call_id, fromApp) VALUES("'.$uid.'","'.$call_id.'","'.$from.'")';

	$conn = DBConn();

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "Transfer Log Created")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error", 'sql' => $sql)));
	}
	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}

?>