<?php

require('get_db.php');
header('Content-Type: application/json');

//  uid type callid qname question answer

if (isset($_GET["uid"]) && isset($_GET["callid"]) && isset($_GET["type"]) && isset($_GET["qname"]) && isset($_GET["question"]) && isset($_GET["answer"]) && isset($_GET["correct"])) {

	$uid     = $_GET["uid"];
	$callid  = $_GET["callid"];
	$type    = $_GET["type"];
	$qname   = $_GET["qname"];
	$ques    = $_GET["question"];
	$answer  = $_GET["answer"];
	$correct = $_GET["correct"];

	$conn = DBConn();

	$sql = "INSERT INTO quiz(type, Call_ID, uid, name, question, answer, correct) VALUES ('".$type."','".$callid."','".$uid."','".$qname."','".$ques."','".$answer."','".$correct."'".
		")";
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