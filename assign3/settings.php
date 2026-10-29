<?php
	$DEBUG = true;
	if (!$DEBUG) {
		// prod settings
		$host = "feenix-mariadb.swin.edu.au";
		$user = "s106498627"; // your user name
		$pwd = "letmein"; // your password (date of birth ddmmyy unless changed)
		$sql_db = "s106498627_db"; // your database
	} else {
		// local dev config, see .devcintainer/docker-compose.yml for details
		$host = "127.0.0.1"; // loopback to local db
		$user = "s106498627"; // your user name
		$pwd = "letmein"; // your password (date of birth ddmmyy unless changed)
		$sql_db = "s106498627_db"; // your database
	}
?>
