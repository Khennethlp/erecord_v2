<?php
	include 'login.php';


	if (isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$query = "SELECT * FROM accounts WHERE username = '$username'";
		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL)
	);
		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			foreach($stmt->fetchALL() as $i){
				$role = $i['role'];
				$fname= $i['fname'];
			}
		}
	}