<?php
// Description: DB credentials, DO NOT do this in real life
// Author: Charlie Hutcheson
// Date: 2026-11-03

	$DEBUG = false;
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
