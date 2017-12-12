<?php

require('get_db.php');
header('Content-type: application/json');

if(isset($_GET['user_id'])) {

	$user_id     = $_GET['user_id'];


	$conn = DBConn();

	$sql = 'SELECT id  FROM uservotepromptorder WHERE user_id= "'.$user_id.'"';

	if ($result = $conn->query($sql)) {
		if($row = $result->fetch_row())
			echo json_encode(array('error' => false, 'id' => $row[0]));
		else {
			$sql = 'INSERT INTO uservotepromptorder (user_id) VALUES('.$user_id.')';

			if ($result = $conn->query($sql)) {
				echo json_encode(array('error' => false, 'id' => $conn->insert_id, 'message' => "Entery done"));
			}else{
				echo json_encode(array('error' => true, 'message' => "DB Insert Error", 'sql' => $sql));
			}
		}
	} else 
		echo json_encode(array('error' => true, 'message' => "Query Error", 'sql' => $sql));


	$conn->close();
}else {
	echo json_encode(array('error' => true, 'message' => "Params incomplete!"));
}

?>