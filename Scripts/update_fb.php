<?PHP

set_time_limit(30);

require('get_db.php');
header('Content-Type: application/json');

if( isset($_GET['record_id'])) {

	$record_id = $_GET['record_id'];

	$conn = getDB('baithak');

	$sql = 'UPDATE user_feedback SET is_recorded = 1 WHERE id = "'.$record_id.'"';

	if ($result = $conn->query($sql)) {
		echo json_encode(array('result' =>  array('error' => false, 'message' => "Updated Query Executed")));
	}else{
		echo json_encode(array('result' =>  array('error' =>  true, 'message' => "DB Update Error", "sql" => $sql)));
	}

	$conn->close();
}else{
	echo json_encode(array('result' => array('error' => true, 'message' => "Parameters Incomplete")));
}


?>