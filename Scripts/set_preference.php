<?php

require('get_db.php');
header('Content-Type: application/json');

function check(){
	
	global $conn, $uid, $fileid;

	$sql = 'SELECT * FROM `preference` WHERE `uid` = '.$uid.' AND fileid = '.$fileid;

	if ($result = $conn->query($sql)) {

		if ($result->num_rows <= 0){
			return array('error' => false, 'exists' => false, 'inserted' => false);
		}else
			return array('error' => false, 'exists' => true, "count"=>$result->num_rows, 'inserted' => false);
	}
	return array('error' => true, 'message' => "DB Select Preference Error", 'inserted' => false);
}

function insert(){

	global $conn, $uid, $callid, $fileid, $pref;

	$sql = "INSERT INTO preference(fileid, Call_ID, uid, preference) VALUES (".$fileid.",".$callid.",".$uid.",'".$pref."')";

	$conn->query($sql);

	if ($conn->affected_rows > 0) {
		return array('error' => false, 'message' => 'inserted', 'inserted' => true);
	} else {
		return array('error' => true, 'message' => 'DB Insert Preference Error', 'inserted' => false);
	}
	return array('error' => true, 'message' => "DB Insert Preference Error", 'inserted' => false);
}

if (isset($_GET["uid"]) && isset($_GET["callid"]) && isset($_GET["pref"]) && isset($_GET["fileid"]) ) {

	$uid    = $_GET["uid"];
	$callid = $_GET["callid"];
	$pref     = $_GET["pref"];
	$fileid   = $_GET["fileid"];

	$conn = DBConn();

	$result = array('error' => true, 'message' => 'Some Error In Script.');

	$exists = check();

	if(!$exists["error"]){
		if ($exists["exists"]) {
			$result = $exists;	
		}else{
			$result = insert();
		}
	}else{
		$result = $exists;
	}

	echo json_encode(array('result' => $result ) );

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>