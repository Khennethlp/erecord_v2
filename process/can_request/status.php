<?php
session_start();
include '../conn.php';

$method = $_POST['method'];

if ($method == 'fetch_pro') {
	$category = $_POST['category'];
	$query = "SELECT process FROM m_process WHERE category = '$category' ORDER BY process ASC";
	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
		echo '<option value="">Please select a process.....</option>';
		foreach ($stmt->fetchAll() as $row) {
			echo '<option>' . htmlspecialchars($row['process']) . '</option>';
		}
	} else {
		echo '<option>Please select a process.....</option>';
	}
}

if ($method == 'fetch_pro_can') {
	$category = $_POST['category'];
	$query = "SELECT process FROM m_process WHERE category = '$category' ORDER BY process ASC";
	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
		echo '<option value="">Please select a process.....</option>';
		foreach ($stmt->fetchAll() as $row) {
			echo '<option>' . htmlspecialchars($row['process']) . '</option>';
		}
	} else {
		echo '<option>Please select a process.....</option>';
	}
}

function count_can($search_arr, $conn)
{
	if (!empty($search_arr['category'])) {
		$emp_id = $_POST['emp_id'];
		$fullname = $_POST['fullname'];
		$category = $_POST['category'];
		$i_status = $_POST['r_status'];
		$processName_can = $_POST['processName_can'];
		$date_authorized_can = $_POST['date_authorized_can'];
		$query = "SELECT  COUNT(DISTINCT a.auth_no) as total";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id 
							JOIN m_process c ON a.process = c.process
							where a.r_status ='" . $search_arr['r_status'] . "'";
		if (!empty($search_arr['emp_id'])) {
			$query = $query . " AND (b.emp_id = '" . $search_arr['emp_id'] . "' OR b.emp_id_old = '" . $search_arr['emp_id'] . "')";
		}
		if (!empty($search_arr['processName_can'])) {
			$query = $query . " AND c.process = '" . $search_arr['processName_can'] . "'";
		}
		if (!empty($search_arr['date_authorized_can'])) {
			$query = $query . " AND a.date_authorized = '" . $search_arr['date_authorized_can'] . "'";
		}

		$query = $query . " AND b.fullname LIKE '" . $search_arr['fullname'] . "%'";
		// $query = $query . " ORDER BY SUBSTRING_INDEX(a.up_date_time , '/', -1) DESC";

		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchALL() as $j) {
				$total = $j['total'];
			}
		} else {
			$total = 0;
		}
		return $total;
	}
}

if ($method == 'count_can') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$r_status = $_POST['r_status'];
	$processName_can = $_POST['processName_can'];
	$date_authorized_can = $_POST['date_authorized_can'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"r_status" => $r_status,
		"processName_can" => $processName_can,
		"date_authorized_can" => $date_authorized_can,
	);

	echo count_can($search_arr, $conn);
}
if ($method == 'fetch_can_pagination') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$r_status = $_POST['r_status'];
	$processName_can = $_POST['processName_can'];
	$date_authorized_can = $_POST['date_authorized_can'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"r_status" => $r_status,
		"processName_can" => $processName_can,
		"date_authorized_can" => $date_authorized_can,
	);

	$results_per_page = 100;

	$number_of_result = intval(count_can($search_arr, $conn));

	// Determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	// Echo the pagination options
	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="' . $page . '">' . $page . '</option>';
	}
}

if ($method == 'fetch_status_can') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$r_status = $_POST['r_status'];
	$processName_can = $_POST['processName_can'];
	$date_authorized_can = $_POST['date_authorized_can'];
	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;
		$page_first_result = ($current_page - 1) * $results_per_page;
		$c = $page_first_result;
		$query = "SELECT a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.r_status,a.r_review_by,a.r_approve_by,b.fullname,b.agency,a.dept,b.batch,b.emp_id,c.category,c.process";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id  
							JOIN m_process c ON a.process = c.process
							where a.r_status = '$r_status' AND a.up_date_time LIKE '%" . $_SESSION['fname'] . "%'";
		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE'$fullname%'";
		}
		if (!empty($processName_can)) {
			$query = $query . " AND c.process LIKE'$processName_can%'";
		}
		if (!empty($date_authorized_can)) {
			$query = $query . " AND DATE(a.date_authorized) = '$date_authorized_can' ";
		}

		// $query = $query . "GROUP BY a.auth_no ASC ORDER BY SUBSTRING_INDEX(a.up_date_time , '/', -1) DESC LIMIT " . $page_first_result . ", " . $results_per_page;
		$query = $query . " ORDER BY SUBSTRING(a.up_date_time, LEN(a.up_date_time) - CHARINDEX('/', REVERSE(a.up_date_time)) + 2, LEN(a.up_date_time)) DESC
                    OFFSET " . $page_first_result . " ROWS FETCH NEXT " . $results_per_page . " ROWS ONLY";

		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchAll() as $j) {
				$c++;
				$r_status = $j['r_status'];

				// echo '<tr style="cursor:pointer;" class="modal-trigger" data-toggle="modal" data-target="#qc_disapproved" onclick="rec_qc_disapproved(&quot;' . $j['id'] . '~!~' . $j['auth_year'] . '~!~' . $j['date_authorized'] . '~!~' . $j['expire_date'] . '~!~' . $j['remarks'] . '~!~' . $j['dept'] .'~!~' . $j['r_of_cancellation'] .'~!~' . $j['d_of_cancellation'] . '~!~' . $j['up_date_time'] . '~!~' . $j['fullname'] . '~!~' . $j['auth_no'] . '~!~' . $j['category'] .'~!~' . $j['r_status'].  '&quot;)">';
				echo '<tr>';
				echo '<td>' . $c . '</td>';
				echo '<td>' . $j['process'] . '</td>';
				echo '<td>' . $j['auth_no'] . '</td>';
				echo '<td>' . $j['fullname'] . '</td>';
				echo '<td>' . $j['emp_id'] . '</td>';
				echo '<td>' . $j['r_of_cancellation'] . '</td>';
				echo '<td>' . $j['d_of_cancellation'] . '</td>';
				echo '<td>' . $j['up_date_time'] . '</td>';
				echo '<td>' . $j['r_review_by'] . '</td>';
				echo '<td>' . $j['r_approve_by'] . '</td>';
				echo '<td>' . $j['dept'] . '</td>';
				echo '<td>' . $j['r_status'] . '</td>';
				echo '<td>' . $j['remarks'] . '</td>';
				echo '</tr>';
			}
		} else {
			echo '<tr>';
			echo '<td style="text-align:center;" colspan="4">No Result</td>';
			echo '</tr>';
		}
	} else {
		echo '<script>alert("Please select category ");</script>';
	}
}



if ($method == 'ds_qc_update') {
	$auth_no = $_POST['auth_no'];
	$dept = $_POST['dept'];
	$r_of_cancellation = $_POST['r_of_cancellation'];
	$d_of_cancellation = $_POST['d_of_cancellation'];
	$up_date_time = $_POST['up_date_time'];
	$id = $_POST['id'];
	$category = $_POST['category'];
	$c = 0;

	$error = 0;

	$query = "UPDATE ";
	if ($category == 'Final') {
		$query .= "t_f_process";
	} else if ($category == 'Initial') {
		$query .= "t_i_process";
	}


	if ($r_of_cancellation == 'NULL' && $d_of_cancellation == 'NULL') {
		$query .= " SET r_of_cancellation = NULL, d_of_cancellation = NULL,  r_status = 'Pending', up_date_time = '" . $_SESSION['fname'] . "/ " . $server_date_time . "' WHERE id = '$id'";
	} else {
		$query .= " SET r_of_cancellation = '$r_of_cancellation', d_of_cancellation = '$d_of_cancellation',  r_status = 'Pending', up_date_time = '" . $_SESSION['fname'] . "/ " . $server_date_time . "' WHERE id = '$id'";
	}

	$stmt = $conn->prepare($query);
	if (!$stmt->execute()) {
		$error++;
	}

	if ($error == 0) {
		echo 'success';
	} else {
		echo 'error';
	}
}
