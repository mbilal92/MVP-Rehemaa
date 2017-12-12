<?php

require('get_db.php');
header('Content-Type: application/json');

if (isset($_GET["uid"]) && isset($_GET["callid"]) && isset($_GET["fileid"]) && isset($_GET["action"]) ) {

	$uid    = $_GET["uid"];
	$callid = $_GET["callid"];
	$id     = $_GET["fileid"];
	$type   = $_GET["action"];

	$conn = DBConn();

	$sql = "INSERT INTO log(fileid, Call_ID, uid, action) VALUES (".$id.",".$callid.",".$uid.",'".$type."')";
	//echo $sql;

	$conn->query($sql);

	if ($conn->affected_rows > 0) {
		echo json_encode(array('result' => array('error' => false, 'message' => 'inserted')));
	} else {
		echo json_encode(array('result' => array('error' => true, 'message' => 'DB Error')));
	}

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>