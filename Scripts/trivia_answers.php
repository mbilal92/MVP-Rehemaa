<?php

require('get_db.php');
header('Content-Type: application/json');

// uid type callid qname question answer

function getNumberOfUsersWithAttemptsOnQuiz(){

	global $conn, $uid, $type, $qname, $ques, $answer;

	$sql = "SELECT DISTINCT uid FROM quiz WHERE type = '$type' AND name = '$qname' AND uid != '$uid'";

	if ($result = $conn->query($sql)) {
		return array('error' => false, 'count' => $result->num_rows+1);
	} else {
		return array('error' => true, 'message' => 'Attemots on Quiz Error', 'sql' => $sql);
	}

}

function getNumberOfUsersWithAttemptsOnQuestion(){

	global $conn, $uid, $type, $qname, $ques, $answer;

	$sql = "SELECT DISTINCT uid FROM quiz WHERE type = '$type' AND name = '$qname' AND question = '$ques' AND uid != '$uid'";

	if ($result = $conn->query($sql)) {
		return array('error' => false, 'count' => $result->num_rows+1);
	} else {
		return array('error' => true, 'message' => 'Attempts on Question Error', 'sql' => $sql);
	}

}

function getNumberOfUsersWithCorrectAnswers(){

	global $conn, $uid, $type, $qname, $ques, $answer;

	$sql = "SELECT DISTINCT uid FROM quiz WHERE type = '$type' AND name = '$qname' AND question = '$ques' AND correct = 1 AND uid != '$uid'";

	if ($result = $conn->query($sql)) {
		return array('error' => false, 'count' => $result->num_rows);
	} else {
		return array('error' => true, 'message' => 'Correct Error', 'sql' => $sql);
	}

}

function getNumberOfUsersWithWrongAnswers(){
	
	global $conn, $uid, $type, $qname, $ques, $answer;

	$sql = "SELECT DISTINCT uid FROM quiz WHERE type = '$type' AND name = '$qname' AND question = '$ques' AND correct = 0 AND uid != '$uid'";

	if ($result = $conn->query($sql)) {
		return array('error' => false, 'count' => $result->num_rows);
	} else {
		return array('error' => true, 'message' => 'Wrong Error', 'sql' => $sql);
	}

}

function getNumberOfUsersWithSameScore(){

	global $conn, $uid, $type, $qname, $ques, $answer;

	$sql = 'SELECT uid FROM (SELECT uid, SUM(correct) as score FROM `quiz`  WHERE name = "'.$qname.'" AND  type = "'.$type.'" AND uid != "'.$uid.'" GROUP BY uid) tab1 WHERE score in (SELECT SUM(correct) as uscore FROM `quiz`  WHERE name = "'.$qname.'" AND  type = "'.$type.'" AND uid = "'.$uid.'")';

	if ($result = $conn->query($sql)) {
		return array('error' => false, 'count' => $result->num_rows);
	} else {
		return array('error' => true, 'message' => 'Same Score Error', 'sql' => $sql);
	}

}

if (isset($_GET["uid"]) && isset($_GET["callid"]) && isset($_GET["type"]) && isset($_GET["qname"]) && isset($_GET["question"]) && isset($_GET["answer"]) ) {

	$uid     = $_GET["uid"];
	$callid  = $_GET["callid"];
	$type    = $_GET["type"];
	$qname   = $_GET["qname"];
	$ques    = $_GET["question"];
	$answer  = $_GET["answer"];

	$conn = DBConn();

	$useraQ = getNumberOfUsersWithAttemptsOnQuiz();
	$useraq = getNumberOfUsersWithAttemptsOnQuestion();
	$right  = getNumberOfUsersWithCorrectAnswers();
	$wrong  = getNumberOfUsersWithWrongAnswers();
	$quiz   = getNumberOfUsersWithSameScore();

	if (!$useraQ["error"] && !$useraq["error"] && !$right["error"] && !$wrong["error"] && !$quiz["error"] ) {
		echo json_encode(array('result' => array('error' => false, 'right' => intval($right["count"]/$useraq["count"]*100), 'wrong' => intval($wrong["count"]/$useraq["count"]*100), 'quiz' => intval($quiz["count"]/$useraQ["count"]*100), '_right' => $right["count"], '_wrong' => $wrong["count"], '_quiz' => $quiz["count"], '_users questions attempts' => $useraq["count"], '_users quiz atempts' => $useraQ["count"]  ) ));
		//echo json_encode(array('result' => array('error' => false, 'right' => intval($right["count"]/$useraq*100), 'wrong' => intval($wrong/$useraq*100), 'quiz' => intval($quiz/$useraQ*100))));
	} else {
		echo json_encode(array('result' => array('error' => true, 'message' => 'DB Error', 'Attempts on Quiz' => $useraQ, 'Attempts on Questions' => $useraq, 'Correct' => $right, 'wrong' => $wrong, 'Same Score' => $quiz)));
	}

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>