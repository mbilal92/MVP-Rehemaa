<?php

set_time_limit(60);

ini_set('error_reporting', E_ALL);

require('get_db.php');
header('Content-Type: application/json');

if( isset($_GET['callid']) && isset($_GET['uid'])) {

	$callid = $_GET['callid'];
	$uid    = $_GET['uid'];

	$conn = getDB('baithak');

	$sql = "INSERT INTO user_feedback (`uid`, `Call_ID`) VALUES($uid, $callid)";

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "FB Created")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error", 'sql' => $sql, 'error' => $conn->error)));
	}

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => "Parameters Incomplete")));
}
?>