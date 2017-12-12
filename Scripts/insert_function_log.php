<?php

require('get_db.php');
header('Content-type: application/json');

if(isset($_GET['functionName']) && isset($_GET['action']) && isset($_GET['callid'])) {

	$functionName     = $_GET['functionName'];
	$action = $_GET['action'];
	$callid = $_GET['callid'];
	$userid = $_GET['userid'];

	$conn = DBConn();

	$sql = 'INSERT INTO functionlog (functionName, action, callid, userid) VALUES("'.$functionName.'","'.$action.'", "'.$callid.'","'.$userid.'")';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'id' => $conn->insert_id, 'message' => "Comment Created")));
	}else{
		echo json_encode(array('result' =>  array('error' => true, 'message' => "DB Insert Error", 'sql' => $sql)));
	}

	$conn->close();
}else {
	var_dump($_GET);
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}

?>