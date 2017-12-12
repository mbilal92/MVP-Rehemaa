<?php

require('get_db.php');
header('Content-Type: application/json');

if(isset($_GET['story_id'])) {

	$story_id  = $_GET['story_id'];
	$conn = DBConn();

	$sql  = 'SELECT comment_id FROM comment WHERE record_status = 1 AND approve_status = 1 AND story_id = '.$story_id.' ORDER BY comment_id ASC';

	if ($result = $conn->query($sql)) {

		$comments = array();
		
	    while($row = $result->fetch_row()) {
	    	$comments[] = $row[0];
	    }

	    echo json_encode(array('result' => array('error' => false, 'comments' => $comments, 'len' => sizeof($comments))));
	} else {
		echo json_encode(array('result' => array('error' => true, 'message' => 'Query Error', 'sql' => $sql)));
	}
	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>



