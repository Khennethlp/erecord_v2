<?php
session_start();
include '../conn.php';

$method = $_POST['method'];

// History for Admin Reviewer 
function count_history_admin_r($search_arr, $conn)
{
	if (!empty($search_arr['category'])) {
		$emp_id = $_POST['emp_id'];
		$fullname = $_POST['fullname'];
		$category = $_POST['category'];
		$date_authorized = $_POST['date_authorized'];
		$expire_date = $_POST['expire_date'];
		$review_date_f = $_POST['review_date_f'];
		$review_date_t = $_POST['review_date_t'];
		$processName_h = $_POST['processName_h'];

		$query = "SELECT count(a.id) as total";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process
							WHERE (a.i_status = 'Approved' OR a.i_status = 'Reviewed' OR a.i_status = 'Disapproved')";

		if (!empty($search_arr['emp_id'])) {
			$query = $query . " AND (b.emp_id = '" . $search_arr['emp_id'] . "' OR b.emp_id_old = '" . $search_arr['emp_id'] . "')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE'" . $search_arr['fullname'] . "%'";
		}
		if (!empty($search_arr['expire_date'])) {
			$query = $query . " AND a.expire_date = '" . $search_arr['expire_date'] . "' ";
		}
		if (!empty($search_arr['date_authorized'])) {
			$query = $query . " AND a.date_authorized = '" . $search_arr['date_authorized'] . "' ";
		}
		if (!empty($processName_h)) {
			$query = $query . " AND c.process LIKE'" . $search_arr['processName_h'] . "%'";
		}
		if (!empty($search_arr['review_date_f']) && !empty($search_arr['review_date_t'])) {
			$query .= " AND CONVERT(DATE, SUBSTRING(a.i_review_by, CHARINDEX('/', a.i_review_by) + 1, LEN(a.i_review_by) - CHARINDEX('/', a.i_review_by))) 
						BETWEEN '$review_date_f' AND '$review_date_t'";
		}
		// $query = $query . "ORDER BY CONVERT(DATE, SUBSTRING(a.i_review_by, CHARINDEX('/', a.i_review_by) + 1, LEN(a.i_review_by) - CHARINDEX('/', a.i_review_by))) DESC ";

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

if ($method == 'count_history_admin') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$category = $_POST['category'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$review_date_f = $_POST['review_date_f'];
	$review_date_t = $_POST['review_date_t'];
	$processName_h = $_POST['processName_h'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"date_authorized" => $date_authorized,
		"expire_date" => $expire_date,
		"review_date_f" => $review_date_f,
		"review_date_t" => $review_date_t,
		"processName_h" => $processName_h,
	);

	echo count_history_admin_r($search_arr, $conn);
}

if ($method == 'history_pagination_admin_r') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$review_date_f = $_POST['review_date_f'];
	$review_date_t = $_POST['review_date_t'];
	$processName_h = $_POST['processName_h'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"date_authorized" => $date_authorized,
		"expire_date" => $expire_date,
		"review_date_f" => $review_date_f,
		"review_date_t" => $review_date_t,
		"processName_h" => $processName_h,
	);

	$results_per_page = 100;

	$number_of_result = intval(count_history_admin_r($search_arr, $conn));

	//determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="' . $page . '">' . $page . '</option>';
	}
}

if ($method == 'history_admin_r') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$review_date_f = $_POST['review_date_f'];
	$review_date_t = $_POST['review_date_t'];
	$processName_h = $_POST['processName_h'];

	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;

		//determine the sql LIMIT starting number for the results on the displaying page
		$page_first_result = ($current_page - 1) * $results_per_page;

		// For row numbering
		$c = $page_first_result;

		$query = "SELECT a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.i_status,a.i_review_by,a.i_approve_by,b.fullname,b.agency,a.dept,b.batch,b.emp_id,c.category,c.process";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process 
							WHERE (a.i_status = 'Approved' OR a.i_status = 'Reviewed' OR a.i_status = 'Disapproved')";

		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE'$fullname%'";
		}
		if (!empty($expire_date)) {
			$query = $query . " AND a.expire_date = '$expire_date' ";
		}
		if (!empty($date_authorized)) {
			$query = $query . " AND a.date_authorized = '$date_authorized' ";
		}
		if (!empty($processName_h)) {
			$query = $query . " AND c.process = '$processName_h' ";
		}
		if (!empty($review_date_f) && !empty($review_date_t)) {
			// $query = $query . " AND DATE(SUBSTRING(a.i_review_by, '/', -1)) BETWEEN '$review_date_f' AND '$review_date_t' ";
			$query .= " AND CONVERT(DATE, SUBSTRING(a.i_review_by, CHARINDEX('/', a.i_review_by) + 1, LEN(a.i_review_by) - CHARINDEX('/', a.i_review_by))) 
						BETWEEN '$review_date_f' AND '$review_date_t'";
		}

		$query = $query . " AND b.fullname LIKE '$fullname%'";

		$query = $query . " ORDER BY SUBSTRING(a.i_review_by, CHARINDEX('/', a.i_review_by) + 1, LEN(a.i_review_by) - CHARINDEX('/', a.i_review_by)) DESC 
							OFFSET $page_first_result ROWS FETCH NEXT $results_per_page ROWS ONLY";


		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchAll() as $j) {
				$c++;
				$row_class = "";
				if ($j['i_status'] == 'Disapproved') {
					$row_class = " bg-maroon";
				}
				echo '<tr class="' . $row_class . '">';
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
				echo '<td>' . $j['dept'] . '</td>';
				echo '<td>' . $j['i_status'] . '</td>';
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




// History for Approver
function count_history_approver($search_arr, $conn)
{
	if (!empty($search_arr['category'])) {
		$emp_id = $_POST['emp_id'];
		$fullname = $_POST['fullname'];
		$category = $_POST['category'];
		$date_authorized = $_POST['date_authorized'];
		$expire_date = $_POST['expire_date'];
		$approved_date_f = $_POST['approved_date_f'];
		$approved_date_t = $_POST['approved_date_t'];
		$processName_h = $_POST['processName_h'];

		$query = "SELECT count(a.id) as total";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process
							WHERE (a.i_status = 'Approved' OR a.i_status = 'Disapproved')";

		if (!empty($search_arr['emp_id'])) {
			$query = $query . " AND (b.emp_id = '" . $search_arr['emp_id'] . "' OR b.emp_id_old = '" . $search_arr['emp_id'] . "')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE'" . $search_arr['fullname'] . "%'";
		}
		if (!empty($search_arr['expire_date'])) {
			$query = $query . " AND a.expire_date = '" . $search_arr['expire_date'] . "' ";
		}
		if (!empty($search_arr['date_authorized'])) {
			$query = $query . " AND a.date_authorized = '" . $search_arr['date_authorized'] . "' ";
		}
		if (!empty($search_arr['processName_h'])) {
			$query = $query . " AND c.process LIKE '" . $search_arr['processName_h'] . "%' ";
		}
		if (!empty($search_arr['approved_date_f']) && !empty($search_arr['approved_date_t'])) {
			// $query = $query . "AND DATE(SUBSTRING_INDEX(a.i_approve_by, '/', -1)) BETWEEN '$approved_date_f' AND '$approved_date_t' ";
			$query .= " AND CONVERT(DATE, SUBSTRING(a.i_review_by, CHARINDEX('/', a.i_review_by) + 1, LEN(a.i_review_by) - CHARINDEX('/', a.i_review_by))) 
						BETWEEN '$approved_date_f' AND '$approved_date_t'";
		}
		// $query = $query . " ORDER BY SUBSTRING(a.i_approve_by, CHARINDEX('/', a.i_approve_by) + 1, LEN(a.i_approve_by) - CHARINDEX('/', a.i_approve_by)) DESC ";
		// $query = $query . " ORDER BY SUBSTRING_INDEX(a.i_approve_by , '/', -1) DESC";

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

if ($method == 'count_history_app') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$category = $_POST['category'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$approved_date_f = $_POST['approved_date_f'];
	$approved_date_t = $_POST['approved_date_t'];
	$processName_h = $_POST['processName_h'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"date_authorized" => $date_authorized,
		"expire_date" => $expire_date,
		"approved_date_f" => $approved_date_f,
		"approved_date_t" => $approved_date_t,
		"processName_h" => $processName_h,
	);

	echo count_history_approver($search_arr, $conn);
}

if ($method == 'history_pagination_approver') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$approved_date_f = $_POST['approved_date_f'];
	$approved_date_t = $_POST['approved_date_t'];
	$processName_h = $_POST['processName_h'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"fullname" => $fullname,
		"category" => $category,
		"date_authorized" => $date_authorized,
		"expire_date" => $expire_date,
		"approved_date_f" => $approved_date_f,
		"approved_date_t" => $approved_date_t,
		"processName_h" => $processName_h,
	);

	$results_per_page = 100;

	$number_of_result = intval(count_history_approver($search_arr, $conn));

	//determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="' . $page . '">' . $page . '</option>';
	}
}
if ($method == 'history_approver') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$approved_date_f = $_POST['approved_date_f'];
	$approved_date_t = $_POST['approved_date_t'];
	$processName_h = $_POST['processName_h'];

	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;

		//determine the sql LIMIT starting number for the results on the displaying page
		$page_first_result = ($current_page - 1) * $results_per_page;

		// For row numbering
		$c = $page_first_result;

		$query = "SELECT a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.i_status,a.i_review_by,a.i_approve_by,b.fullname,b.agency,a.dept,b.batch,b.emp_id,c.category,c.process";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process
							WHERE (a.i_status = 'Approved' OR a.i_status = 'Disapproved')";

		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE'$fullname%'";
		}
		if (!empty($expire_date)) {
			$query = $query . " AND a.expire_date = '$expire_date' ";
		}
		if (!empty($date_authorized)) {
			$query = $query . " AND a.date_authorized = '$date_authorized' ";
		}
		if (!empty($processName_h)) {
			$query = $query . " AND c.process LIKE '$processName_h%' ";
		}
		if (!empty($approved_date_f) && !empty($approved_date_t)) {
			// $query = $query . "AND DATE(SUBSTRING_INDEX(a.i_approve_by, '/', -1)) BETWEEN '$approved_date_f' AND '$approved_date_t' ";
			$query .= " AND CONVERT(DATE, SUBSTRING(a.i_approve_by, CHARINDEX('/', a.i_approve_by) + 1, LEN(a.i_approve_by) - CHARINDEX('/', a.i_approve_by))) 
						BETWEEN '$approved_date_f' AND '$approved_date_t'";
		}

		$query = $query . " AND b.fullname LIKE '$fullname%'";
		// $query = $query . "ORDER BY SUBSTRING_INDEX(a.i_approve_by , '/', -1) DESC LIMIT " . $page_first_result . ", " . $results_per_page;
		$query = $query . " ORDER BY SUBSTRING(a.i_approve_by, CHARINDEX('/', a.i_approve_by) + 1, LEN(a.i_approve_by) - CHARINDEX('/', a.i_approve_by)) DESC 
							OFFSET $page_first_result ROWS FETCH NEXT $results_per_page ROWS ONLY";

		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchAll() as $j) {
				$c++;
				$row_class = "";
				if ($j['i_status'] == 'Disapproved') {
					$row_class = " bg-maroon";
				}
				echo '<tr class="' . $row_class . '">';
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
				echo '<td>' . $j['dept'] . '</td>';
				echo '<td>' . $j['i_status'] . '</td>';
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
