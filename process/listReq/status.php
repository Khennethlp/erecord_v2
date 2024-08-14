<?php
session_start();
include '../conn.php';

$method = $_POST['method'];

if ($method == 'fetch_pro_cert') {
	$category = $_POST['category'];
	$query = "SELECT `process` FROM `m_process` WHERE category = '$category' ORDER BY process ASC";
	$stmt = $conn->prepare($query);
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

function count_cert($search_arr, $conn)
{
	if (!empty($search_arr['category'])) {
		$emp_id = $_POST['emp_id'];
		$fullname = $_POST['fullname'];
		$category = $_POST['category'];
		$i_status = $_POST['i_status'];
		$processName_cert = $_POST['processName_cert'];
		$query = "SELECT count(a.id) as total";

		if ($category == 'Final') {
			$query = $query . " FROM `t_f_process`";
		} else if ($category == 'Initial') {
			$query = $query . " FROM `t_i_process`";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id 
							JOIN `m_process` c ON a.process = c.process
							where a.i_status ='" . $search_arr['i_status'] . "' AND a.up_date_time LIKE '%" . $_SESSION['fname'] . "%'";
		if (!empty($search_arr['emp_id'])) {
			$query = $query . " AND (b.emp_id = '" . $search_arr['emp_id'] . "' OR b.emp_id_old = '" . $search_arr['emp_id'] . "')";
		}

		if (!empty($search_arr['fullname'])) {
			$query = $query . " AND b.fullname LIKE '" . $search_arr['fullname'] . "%'";
		}
		if (!empty($search_arr['processName_cert'])) {
			$query = $query . " AND c.process LIKE '" . $search_arr['processName_cert'] . "%'";
		}
		$query = $query . " ORDER BY SUBSTRING_INDEX(a.up_date_time , '/', -1) DESC";

		$stmt = $conn->prepare($query);
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

if ($method == 'count_cert') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$i_status = $_POST['i_status'];
	$processName_cert = $_POST['processName_cert'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"i_status" => $i_status,
		"processName_cert" => $processName_cert
	);

	echo count_cert($search_arr, $conn);
}

if ($method == 'fetch_cert_pagination') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$i_status = $_POST['i_status'];
	$processName_cert = $_POST['processName_cert'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"i_status" => $i_status,
		"processName_cert" => $processName_cert
	);

	$results_per_page = 100;

	$number_of_result = intval(count_cert($search_arr, $conn));

	//determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="' . $page . '">' . $page . '</option>';
	}
}


if ($method == 'fetch_status_cert') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$i_status = $_POST['i_status'];
	$processName_cert = $_POST['processName_cert'];
	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;

		$page_first_result = ($current_page - 1) * $results_per_page;
		$c = $page_first_result;
		$query = "SELECT a.up_date_time,a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.i_status,a.i_review_by,b.fullname,b.agency,a.dept,b.batch,b.emp_id,c.category,c.process";

		if ($category == 'Final') {
			$query = $query . " FROM `t_f_process`";
		} else if ($category == 'Initial') {
			$query = $query . " FROM `t_i_process`";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id 
							JOIN `m_process` c ON a.process = c.process
							WHERE a.i_status = '$i_status' AND a.up_date_time LIKE '%" . $_SESSION['fname'] . "%'";
		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE '$fullname%' ";
		}
		if (!empty($processName_cert)) {
			$query = $query . " AND c.process LIKE '$processName_cert%' ";
		}
		
		$query = $query . "  ORDER BY SUBSTRING_INDEX(a.up_date_time , '/', -1) DESC LIMIT " . $page_first_result . ", " . $results_per_page;
		$stmt = $conn->prepare($query);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchAll() as $j) {
				$c++;
				$i_status = $j['i_status'];
				echo '<tr style="cursor:pointer;" class="modal-trigger" data-toggle="modal" data-target="#disapproved" onclick="rec_disapproved(&quot;' . $j['id'] . '~!~' . $j['auth_year'] . '~!~' . $j['date_authorized'] . '~!~' . $j['expire_date'] . '~!~' . $j['remarks'] . '~!~' . $j['dept'] . '~!~' . $j['up_date_time'] . '~!~' . $j['fullname'] . '~!~' . $j['auth_no'] . '~!~' . $j['category'] .'~!~' . $j['i_status'].  '&quot;)">';
				echo '<td>' . $c . '</td>';
				echo '<td>' . $j['process'] . '</td>';
				echo '<td>' . $j['auth_no'] . '</td>';
				echo '<td>' . $j['fullname'] . '</td>';
				echo '<td>' . $j['emp_id'] . '</td>';
				echo '<td>' . $j['auth_year'] . '</td>';
				echo '<td>' . $j['date_authorized'] . '</td>';
				echo '<td>' . $j['expire_date'] . '</td>';
				echo '<td>' . $j['r_of_cancellation'] . '</td>';
				echo '<td>' . $j['d_of_cancellation'] . '</td>';
				echo '<td>' . $j['up_date_time'] . '</td>';
				echo '<td>' . $j['i_review_by'] . '</td>';
				echo '<td>' . $j['i_approve_by'] . '</td>';
				echo '<td>' . $j['i_status'] . '</td>';
				echo '<td>' . $j['dept'] . '</td>';
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



if ($method == 'ds_update') {
	$auth_no = $_POST['auth_no'];
	$auth_year = $_POST['auth_year'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$remarks = $_POST['remarks'];
	$dept = $_POST['dept'];
	$up_date_time = $_POST['up_date_time'];
	$id = $_POST['id'];
	$category = $_POST['category'];
	$c = 0;

	$error = 0;

	$query = "SELECT id FROM ";
	if ($category == 'Final') {
		$query .= "`t_f_process`";
	} else if ($category == 'Initial') {
		$query .= "`t_i_process`";
	}
	$query .= " WHERE id = '$id' AND  auth_no='$auth_no'  AND auth_year = '$auth_year' AND date_authorized = '$date_authorized' AND expire_date = '$expire_date' AND remarks = '$remarks' AND dept = '$dept'";

	$stmt = $conn->prepare($query);
	$stmt->execute();

	if ($stmt->rowCount() < 1) {
		$query = "UPDATE ";
		if ($category == 'Final') {
			$query .= "`t_f_process`";
		} else if ($category == 'Initial') {
			$query .= "`t_i_process`";
		}
		$query .= " SET remarks = '$remarks', auth_year = '$auth_year', date_authorized = '$date_authorized', expire_date = '$expire_date', dept = '$dept', i_status = 'Pending', up_date_time = '" . $_SESSION['fname'] . "/ " . $server_date_time . "' WHERE id = '$id'";
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
}
