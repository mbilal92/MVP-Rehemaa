<?php

include('get_db.php');
header('Content-type: application/json');

if(isset($_GET['userid']) && isset($_GET['callid'])) {

	$userid     = $_GET['userid'];
	$call_id = $_GET['callid'];
	$start_or_end     = $_GET['start_or_end'];
	$menu_or_prompt = $_GET['menu_or_prompt'];

	$conn = DBConn();

	$sql = 'INSERT INTO polly_menu_log (call_id, user_id, start_or_end, menu_or_prompt) VALUES("'.$call_id.'","'.$userid.'","'.$start_or_end.'","'.$menu_or_prompt.'")';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "Log Created")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error", 'sql' => $sql)));
	}
	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}

?>