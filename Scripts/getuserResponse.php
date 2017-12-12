<?php

require('get_db.php');
header('Content-Type: application/json');

if(isset($_GET['content_id'])) {

	$content_id  = $_GET['content_id'];
	$user_id  = $_GET['user_id'];
	$type  = $_GET['type'];
	$conn = DBConn();

	$sql  = 'SELECT count(*) FROM response WHERE user_id ='.$user_id.' AND response_type = "'.$type.'" AND content_id = '.$content_id;

	if ($result = $conn->query($sql)) {
		if($row = $result->fetch_row())
			echo json_encode(array('error' => false, 'count' => $row[0]));
		else
			echo json_encode(array('error' => false, 'count' => 0, 'message' => "Unable to fetch data.", 'sql' => $sql));
	} else 
		echo json_encode(array('error' => true, 'message' => "Query Error", 'sql' => $sql));
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => 'wrong params')));
}

?>



