<?php
	$mysql_host = "mysql11.000webhost.com";
	$mysql_database = "a4545722_dm";
	$mysql_user = "a4545722_dm";
	$mysql_password = "6b3p34";
	
	$db_handle = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
	if (!$db_handle) {
		die("Connection Error: ".mysqli_connect_errno());
	}
	
	session_start();
	$s = session_id();
	if (isset($_REQUEST["long"]) && isset($_REQUEST["lat"])) {
		$longitude = mysqli_real_escape_string($db_handle, $_REQUEST["long"]);
		$latitude = mysqli_real_escape_string($db_handle, $_REQUEST["lat"]);
		
		$query = "SELECT id FROM users WHERE session='{$s}'";
		$result = mysqli_query($db_handle, $query);
		$row = $result->fetch_assoc();
		$id;
		if ($row) {
			$query = "DELETE FROM users WHERE session='{$s}'";
			$result = mysqli_query($db_handle, $query);
		}
		$query = "INSERT INTO users VALUES (DEFAULT, '{$longitude}', '{$latitude}', '{$s}', NOW())";
		$result = mysqli_query($db_handle, $query);
	} else {
		$query = "SELECT * FROM users WHERE session<>'{$s}'";
		$result = mysqli_query($db_handle, $query);
		
		// Do not print older entries
		while ($row = $result->fetch_assoc()) {
			// if (time() - strtotime($row["last_active"]) < 60)
				echo $row["long"]." ".$row["lat"]." ";
		}
	}
?>
