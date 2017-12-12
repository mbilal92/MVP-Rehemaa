<?PHP

set_time_limit(60);

function getDB($db){

	$servername = 'localhost';
	$username = 'root';
	$password = '';

	$conn = new mysqli($servername, $username, $password, $db);
	
	if ($conn->connect_error) {
	    die('Connection failed: ' . $conn->connect_error);
	}

	return $conn;
}

?>