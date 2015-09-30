<?php
	$mysql_host = "localhost";
	$mysql_database = "keweizho_dankmeet";
	$mysql_user = "keweizho_dm";
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
		if ($row) {
			$id = $row["id"];
			$query = "UPDATE users SET longi='{$longitude}', lat='{$latitude}', last_active=NOW() WHERE id={$id}";
			$result = mysqli_query($db_handle, $query);
		} else {
			// this shouldn't really happen
			// $query = "INSERT INTO users VALUES (DEFAULT, '{$longitude}', '{$latitude}', '{$s}', NOW(), 'nick1', 'info1')";
			// $result = mysqli_query($db_handle, $query);
		}
	} else if (isset($_REQUEST["sid"]) && isset($_REQUEST["nick"]) && isset($_REQUEST["info"])) {
		$sid = mysqli_real_escape_string($db_handle, $_REQUEST["sid"]);
		$nick = mysqli_real_escape_string($db_handle, $_REQUEST["nick"]);
		$info = mysqli_real_escape_string($db_handle, $_REQUEST["info"]);
		
		$query = "SELECT id FROM users WHERE session='{$sid}'";
		$result = mysqli_query($db_handle, $query);
		$row = $result->fetch_assoc();
		
		if ($row) {
			$id = $row["id"];
			$query = "UPDATE users SET last_active=NOW(), nick='{$nick}', info='{$info}' WHERE id={$id}";
			$result = mysqli_query($db_handle, $query);
		} else {
			$query = "INSERT INTO users VALUES (DEFAULT, '100', '100', '{$sid}', NOW(), '{$nick}', '{$info}')";
			$result = mysqli_query($db_handle, $query);
		}
	} else if (isset($_REQUEST["logout"])) {
		$query = "DELETE FROM users WHERE session='{$s}'";
		$result = mysqli_query($db_handle, $query);
	} else {
		$query = "SELECT * FROM users WHERE session<>'{$s}'";
		$result = mysqli_query($db_handle, $query);
		
		// Do not print older entries
		while ($row = $result->fetch_assoc()) {
			$sep = "!*JIu03A9"; // have to filter this out from user input
			if (abs(time() - strtotime($row["last_active"])) < 29000) // correct for incorrect clock
				echo $row["longi"].$sep.$row["lat"].$sep.$row["nick"].$sep.$row["info"].$sep;
		}
	}
?>
