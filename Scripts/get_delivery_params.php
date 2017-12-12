<?php

require('get_db.php');
header('Content-type: application/json');


function getDel(){

	global $conn;
	global $id;

	$reqs = array();

	$sql = 'SELECT type, file_id, user_id FROM forward WHERE forward_id= "'.$id.'"';

	if ($result = $conn->query($sql)) {
		if($row = $result->fetch_row()) {
			$type = $row[0];
			$file_id = $row[1];
			$fuid = $row[2];
			$answer_id = -1;
			if ($row[0] == 'Question') {
				$sql = 'SELECT answer_id FROM question_answer WHERE question_id= "'.$file_id.'"';
				if ($result1 = $conn->query($sql)) {
					if($row1 = $result1->fetch_row()) {
						$answer_id = $row1[0];
					} else
						return array('error' => true, 'message' => "Fetch Error", 'sql' => $sql);
				} else
					return array('error' => true, 'message' => "Query Error", 'sql' => $sql);

			}
			return array('error' => false, 'type' => $type, 'file_id' => $file_id, 'fuid' => $fuid, 'answer_id' => $answer_id);

		}else
			return array('error' => true, 'message' => "Fetch Error", 'sql' => $sql);
	} else 
		return array('error' => true, 'message' => "Query Error", 'sql' => $sql);
}


if(isset($_GET['id'])) {

	$id  = $_GET['id'];
	$conn = DBConn();

	echo json_encode(array('result' => getDel()));

	$conn->close();
}else {
	echo json_encode(array('result' => array('error' => true, 'message' => "Params incomplete!")));
}
?>



