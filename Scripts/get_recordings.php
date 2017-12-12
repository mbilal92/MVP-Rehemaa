<?php

require('get_db.php');
header('Content-Type: application/json');

	$conn = DBConn();

	function getTypes(){

		global $conn;

		$sql = "SELECT DISTINCT type FROM files";

		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$types = array();
		    while($row = $result->fetch_assoc()) {
		    	$types[] = $row["type"];
		    }
		    return $types;
		} else {
		    return false;
		}

	}

	function getRecordings($type){

		global $conn;

		$sql = "SELECT file_name as file, prompt, id FROM files WHERE type = '".$type."' ORDER BY RAND() ";
		//echo "$sql \n";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$recs = array();
		    while($row = $result->fetch_assoc()) {
		        $recs[] = array($row["file"], $row["prompt"], $row["id"]);
		    }
		    return $recs;
		} else {
		    return false;
		}
	}

	$result= array(); 
	$types = getTypes();

	if ($types) {
		foreach ($types as $type) {
			$result[$type] = getRecordings($type);
		}
		echo json_encode(array('result' => array('error' => false, 'types' => $types, 'recs' => $result)));
	}else{
		echo json_encode(array('result' => array('error' => true, 'message' => "No types returned.")));
	}
	

	$conn->close();


?>