<?PHP

set_time_limit(30);

require('get_db.php');
header('Content-Type: application/json');

if( isset($_GET['question_id'])) {

	$question_id = $_GET['question_id'];

    $conn = DBConn();

	$sql = 'UPDATE question SET question_public_user = 1 WHERE question_id = "'.$question_id.'"';

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