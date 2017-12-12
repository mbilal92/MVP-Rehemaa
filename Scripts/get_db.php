<?PHP

set_time_limit(60);
ini_set("default_socket_timeout", 6000);

function DBConn(){

	$servername = '127.0.0.1';
	$username = 'root';
	$password = '';
	$port	  = '';

	$conn = new mysqli($servername, $username, $password, "mvp");
	
	if ($conn->connect_error) {
	    die('Connection failed: ' . $conn->connect_error);
	}

	return $conn;
}

?>