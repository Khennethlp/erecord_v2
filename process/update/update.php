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
function count_category($search_arr, $conn)
{
	if (!empty($search_arr['category'])) {
		$query = "SELECT count(auth_no) AS total FROM (";

		$query = $query . "SELECT a.auth_no";

		if ($search_arr['category'] == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($search_arr['category'] == 'Initial') {
			$query = $query . " FROM t_i_process";
		}

		$query = $query . " a
						LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id 
						JOIN m_process c ON a.process = c.process
						WHERE a.i_status = 'Approved'";

		if (!empty($search_arr['emp_id'])) {
			$query = $query . " AND (b.emp_id = '" . $search_arr['emp_id'] . "' OR b.emp_id_old = '" . $search_arr['emp_id'] . "')";
		}
		if (!empty($search_arr['fullname'])) {
			$query = $query . " AND b.fullname LIKE'" . $search_arr['fullname'] . "%'";
		}

		if (!empty($search_arr['pro'])) {
			$query = $query . " AND a.process LIKE '" . $search_arr['pro'] . "'";
		}

		$query = $query . "GROUP BY a.auth_no";

		$query = $query . ") AS asub";

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

if ($method == 'count_category') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$fullname = $_POST['fullname'];
	$c = 0;

	$search_arr = array(
		"emp_id" => $emp_id,
		"pro" => $pro,
		"category" => $category,
		"fullname" => $fullname
	);

	echo count_category($search_arr, $conn);
}

if ($method == 'fetch_category_pagination') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$fullname = $_POST['fullname'];

	$search_arr = array(
		"emp_id" => $emp_id,
		"pro" => $pro,
		"category" => $category,
		"fullname" => $fullname
	);

	$results_per_page = 100;

	$number_of_result = intval(count_category($search_arr, $conn));

	//determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="' . $page . '">' . $page . '</option>';
	}
}

if ($method == 'fetch_category') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$fullname = $_POST['fullname'];
	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;

		//determine the sql LIMIT starting number for the results on the displaying page
		$page_first_result = ($current_page - 1) * $results_per_page;

		// For row numbering
		$c = $page_first_result;

		// $query = "SELECT b.batch,a.up_date_time,a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.r_status,b.fullname,b.m_name,b.agency,a.dept,b.batch,b.emp_id,c.category";

		// if ($category == 'Final') {
		// 	$query = $query . " FROM t_f_process";
		// } else if ($category == 'Initial') {
		// 	$query = $query . " FROM t_i_process";
		// }
		// $query = $query . " a
		// 					LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
		// 					JOIN m_process c ON a.process = c.process
		// 					WHERE a.i_status = 'Approved'";

		// if (!empty($emp_id)) {
		// 	$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		// }
		// if (!empty($fullname)) {
		// 	$query = $query . " AND b.fullname LIKE '$fullname%'";
		// }
		// if (!empty($pro)) {
		// 	$query = $query . " AND a.process LIKE '$pro'";
		// }
		// $query = $query . "ORDER BY b.fullname ASC OFFSET :page_first_result ROWS FETCH NEXT :results_per_page ROWS ONLY";


		$query = "
		SELECT 
			MAX(b.batch) AS batch, 
			MAX(a.up_date_time) AS up_date_time, 
			MAX(a.id) AS id, 
			MAX(a.auth_no) AS auth_no, 
			MAX(a.auth_year) AS auth_year, 
			MAX(a.date_authorized) AS date_authorized, 
			MAX(a.expire_date) AS expire_date, 
			MAX(a.r_of_cancellation) AS r_of_cancellation, 
			MAX(a.d_of_cancellation) AS d_of_cancellation, 
			MAX(a.remarks) AS remarks, 
			MAX(a.r_status) AS r_status, 
			MAX(b.fullname) AS fullname, 
			MAX(b.m_name) AS m_name, 
			MAX(b.agency) AS agency, 
			MAX(a.dept) AS dept, 
			MAX(b.batch) AS b_batch, 
			MAX(b.emp_id) AS emp_id, 
			MAX(c.category) AS category
		";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}

		$query = $query . "
			a
			LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
			JOIN m_process c ON a.process = c.process
			WHERE a.i_status = 'Approved'
		";

		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE '$fullname%'";
		}
		if (!empty($pro)) {
			$query = $query . " AND a.process LIKE '$pro'";
		}

		$query = $query . "
		GROUP BY a.auth_no
		ORDER BY fullname ASC
		OFFSET :page_first_result ROWS FETCH NEXT :results_per_page ROWS ONLY";

		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->bindValue(':page_first_result', $page_first_result, PDO::PARAM_INT);
		$stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);

		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchAll() as $j) {
				$c++;
				$row_class = "";
				if ($j['r_status'] == 'Approved') {
					$row_class = " bg-danger";
				}
				
				if (isset($j['r_of_cancellation']) && is_array($j['r_of_cancellation'])) {
					// Remove double quotes from the r_of_cancellation field
					$r_of_cancellation = array_map(function($value) {
						return str_replace('"', '', $value); // Remove double quotes
					}, $j['r_of_cancellation']);
				
					// Join the cleaned r_of_cancellation values into a string (if needed)
					$r_of_cancellation_string = implode('', $r_of_cancellation);
				} else {
					// Handle the case when r_of_cancellation is not set or not an array
					$r_of_cancellation_string = ''; // or set a default value
				}
				
				// Escape special characters in the string for safe use in HTML and JavaScript
				$r_of_cancellation_escaped = htmlspecialchars($r_of_cancellation_string);
				
				
				echo '<tr style="cursor:pointer;" class="modal-trigger' . $row_class . '" data-toggle="modal" data-target="#view" onclick="rec_details(\'' . $j['id'] . '~!~' . $j['auth_year'] . '~!~' . $j['date_authorized'] . '~!~' . $j['expire_date'] . '~!~' . $j['remarks'] . '~!~' . $r_of_cancellation_escaped . '~!~' . $j['d_of_cancellation'] . '~!~' . $j['up_date_time'] . '~!~' . $j['fullname'] . '~!~' . $j['m_name'] . '~!~' . $j['auth_no'] . '\')">';

				// echo '<td>'.$j['process'].'</td>';
				// echo '<td>'.$j['expire_date'].'</td>';
				echo '<td>' . $c . '</td>';
				echo '<td>' . $j['auth_no'] . '</td>';
				echo '<td>' . $j['fullname'] . '</td>';
				echo '<td>' . $j['m_name'] . '</td>';
				echo '<td>' . $j['emp_id'] . '</td>';
				echo '<td>' . $j['batch'] . '</td>';
				echo '<td>' . $j['dept'] . '</td>';
				// echo '<td>'.$j['up_date_time'].'</td>';
				// echo '<td>'.$j['r_of_cancellation'].'</td>';
				// echo '<td>'.$j['d_of_cancellation'].'</td>';

				echo '</tr>';
			}
		} else {
			echo '<tr>';
			echo '<td style="text-align:center;" colspan="4">No Result</td>';
			echo '</tr>';
		}
	} else {
		echo '<script>alert("Please select category and process");</script>';
	}
}

if ($method == 'view') {
	$fullname = $_POST['fullname'];
	$auth_no = $_POST['auth_no'];
	$category = $_POST['category'];

	$c = 0;

	$query = "SELECT a.batch, a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.r_status,a.i_status,b.fullname,b.emp_id,a.dept,c.category";

	if ($category == 'Final') {
		$query = $query . " FROM t_f_process";
	} else if ($category == 'Initial') {
		$query = $query . " FROM t_i_process";
	}
	$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process
							where a.auth_no = '$auth_no'";

	$query = $query . "ORDER BY auth_year DESC";

	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
		foreach ($stmt->fetchAll() as $j) {
			$c++;
			$row_class = "";
			$i_status = $j['i_status'];
			if ($j['r_status'] == 'Approved') {
				$row_class = " bg-danger";
			}

			$r_of_cancellation = $j['r_of_cancellation'];
			$r_of_cancellation_string = str_replace('"', '', $r_of_cancellation);
			
			if ($i_status == 'Approved') {
				echo '<tr style="cursor:pointer;" class="modal-trigger' . $row_class . '" data-toggle="modal" data-target="#update" onclick="rec_update(&quot;' . $j['id'] . '~!~' . $j['auth_year'] . '~!~' . $j['date_authorized'] . '~!~' . $j['expire_date'] . '~!~' . $j['remarks'] . '~!~' . $r_of_cancellation_string . '~!~' . $j['dept'] . '~!~' . $j['d_of_cancellation'] . '~!~' . $j['up_date_time'] . '~!~' . $j['fullname'] . '~!~' . $j['auth_no'] . '~!~' . $j['i_status'] . '~!~' . $j['batch'] . '~!~' . $j['emp_id'] . '&quot;)">';

				echo '<td>' . $j['auth_year'] . '</td>';
				echo '<td>' . $j['date_authorized'] . '</td>';
				echo '<td>' . $j['expire_date'] . '</td>';
				echo '<td>' . $j['remarks'] . '</td>';
				echo '<td>' . $j['r_of_cancellation'] . '</td>';
				echo '<td>' . $j['d_of_cancellation'] . '</td>';
				echo '<td>' . $j['dept'] . '</td>';
				echo '<td>' . $j['up_date_time'] . '</td>';


				echo '</tr>';
			}
		}
	} else {
		echo '<tr>';
		echo '<td style="text-align:center;" colspan="4">No Result</td>';
		echo '</tr>';
	}
}

if ($method == 'admin_update_batch') {
	$category = $_POST['category'];
	$id = $_POST['id'];
	$batch = $_POST['batch'];

	$query = "UPDATE ";
	if ($category == 'Final') {
		$query .= "t_f_process";
	} else if ($category == 'Initial') {
		$query .= "t_i_process";
	}
	$query .= " SET batch = '$batch' WHERE id = '$id'";

	$stmt = $conn->prepare($query);
	if ($stmt->execute()) {
		echo 'success';
	} else {
		echo 'error';
	}
}


if ($method == 'admin_update') {
	$auth_no = $_POST['auth_no'];
	$auth_year = $_POST['auth_year'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$remarks = $_POST['remarks'];
	$r_of_cancellation = $_POST['r_of_cancellation'];
	$dept = $_POST['dept'];
	$d_of_cancellation = $_POST['d_of_cancellation'];
	$up_date_time = $_POST['up_date_time'];
	$id = $_POST['id'];
	$category = $_POST['category'];
	$c = 0;

	$error = 0;


	if (!empty($r_of_cancellation) && !empty($d_of_cancellation)) {

		$query = "UPDATE ";
		if ($category == 'Final') {
			$query .= "t_f_process";
		} else if ($category == 'Initial') {
			$query .= "t_i_process";
		}
		$query .= " SET remarks = '$remarks', r_of_cancellation = '$r_of_cancellation', d_of_cancellation = '$d_of_cancellation', r_status = 'Pending', dept = '$dept', up_date_time = '" . $_SESSION['fname'] . "/ " . $server_date_time . "' WHERE auth_no = '$auth_no'";
	} else {

		$query = "UPDATE ";
		if ($category == 'Final') {
			$query .= "t_f_process";
		} else if ($category == 'Initial') {
			$query .= "t_i_process";
		}
		$query .= " SET remarks = '$remarks', auth_year = '$auth_year', date_authorized = '$date_authorized', expire_date = '$expire_date', dept = '$dept', i_status = 'Pending', up_date_time = '" . $_SESSION['fname'] . "/ " . $server_date_time . "' WHERE id = '$id'";
	}

	$stmt = $conn->prepare($query);
	if ($stmt->execute()) {
		echo 'success';
	} else {
		echo 'error';
	}
}



//minor update
if ($method == 'minor_update') {
	$auth_no = $_POST['auth_no'];
	$auth_year = $_POST['auth_year'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$remarks = $_POST['remarks'];
	$r_of_cancellation = $_POST['r_of_cancellation'];
	$dept = $_POST['dept'];
	$d_of_cancellation = $_POST['d_of_cancellation'];
	$up_date_time = $_POST['up_date_time'];
	$id = $_POST['id'];
	$category = $_POST['category'];
	$c = 0;

	$error = 0;

	$query = "SELECT id FROM ";
	if ($category == 'Final') {
		$query .= "t_f_process";
	} else if ($category == 'Initial') {
		$query .= "t_i_process";
	}
	$query .= " WHERE id = '$id' AND  auth_no='$auth_no'  AND auth_year = '$auth_year' AND date_authorized = '$date_authorized' AND expire_date = '$expire_date' AND remarks = '$remarks' AND dept = '$dept'";

	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() < 1) {
		$query = "UPDATE ";
		if ($category == 'Final') {
			$query .= "t_f_process";
		} else if ($category == 'Initial') {
			$query .= "t_i_process";
		}
		$query .= " SET remarks = '$remarks', auth_no='$auth_no', auth_year = '$auth_year', date_authorized = '$date_authorized', expire_date = '$expire_date', dept = '$dept', i_status = 'Approved' WHERE id = '$id'";
		$stmt = $conn->prepare($query);
		if (!$stmt->execute()) {
			$error++;
		}
	} else {
		$query = "UPDATE ";
		if ($category == 'Final') {
			$query .= "t_f_process";
		} else if ($category == 'Initial') {
			$query .= "t_i_process";
		}
		$query .= " SET remarks = '$remarks', r_of_cancellation = '$r_of_cancellation', d_of_cancellation = '$d_of_cancellation', r_status = 'Approved', dept = '$dept' WHERE auth_no = '$auth_no'";
		$stmt = $conn->prepare($query);
		if (!$stmt->execute()) {
			$error++;
		}
	}

	if ($error == 0) {
		echo 'success';
	} else {
		echo 'error';
	}
}

// for QC Cancellation
if ($method == 'qc_fetch_category') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$fullname = $_POST['fullname'];
	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;


		$page_first_result = ($current_page - 1) * $results_per_page;

		$c = $page_first_result;

		$query = "SELECT b.batch,a.up_date_time,a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.r_status,b.fullname,b.m_name,b.agency,a.dept,b.batch,b.emp_id,c.category";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		} else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process
							WHERE a.i_status = 'Approved'";

		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}
		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE '$fullname%'";
		}
		if (!empty($pro)) {
			$query = $query . " AND a.process LIKE '$pro'";
		}
		$query = $query . "ORDER BY b.fullname ASC OFFSET $page_first_result ROWS FETCH NEXT $results_per_page ROWS ONLY";
		$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach ($stmt->fetchAll() as $j) {
				$c++;
				$row_class = "";
				if ($j['r_status'] == 'Approved') {
					$row_class = " bg-danger";
				}

				echo '<tr style="cursor:pointer;" class="modal-trigger' . $row_class . '" data-toggle="modal" data-target="#view" onclick="qc_rec_details(&quot;' . $j['id'] . '~!~' . $j['auth_year'] . '~!~' . $j['date_authorized'] . '~!~' . $j['expire_date'] . '~!~' . $j['remarks'] . '~!~' . $j['r_of_cancellation'] . '~!~' . $j['d_of_cancellation'] . '~!~' . $j['up_date_time'] . '~!~' . $j['fullname'] . '~!~' . $j['m_name'] . '~!~' . $j['auth_no'] . '&quot;)">';
				echo '<td>' . $c . '</td>';
				echo '<td>' . $j['auth_no'] . '</td>';
				echo '<td>' . $j['fullname'] . '</td>';
				echo '<td>' . $j['m_name'] . '</td>';
				echo '<td>' . $j['emp_id'] . '</td>';
				echo '<td>' . $j['batch'] . '</td>';
				echo '<td>' . $j['dept'] . '</td>';
				echo '</tr>';
			}
		} else {
			echo '<tr>';
			echo '<td style="text-align:center;" colspan="4">No Result</td>';
			echo '</tr>';
		}
	} else {
		echo '<script>alert("Please select category and process");</script>';
	}
}

if ($method == 'qc_view') {
	$fullname = $_POST['fullname'];
	$auth_no = $_POST['auth_no'];
	$category = $_POST['category'];

	$c = 0;

	$query = "SELECT a.batch, a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.r_status,a.i_status,b.fullname,b.emp_id,a.dept,c.category";

	if ($category == 'Final') {
		$query = $query . " FROM t_f_process";
	} else if ($category == 'Initial') {
		$query = $query . " FROM t_i_process";
	}
	$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
							JOIN m_process c ON a.process = c.process
							where a.auth_no = '$auth_no'";

	$query = $query . "ORDER BY auth_year DESC";

	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
		foreach ($stmt->fetchAll() as $j) {
			$c++;
			$row_class = "";
			$i_status = $j['i_status'];
			if ($j['r_status'] == 'Approved') {
				$row_class = " bg-danger";
			}
			if ($i_status == 'Approved') {
				echo '<tr style="cursor:pointer;" class="modal-trigger' . $row_class . '" data-toggle="modal" data-target="#admine_r_update" onclick="qc_rec_update(&quot;' . $j['id'] . '~!~' . $j['auth_year'] . '~!~' . $j['date_authorized'] . '~!~' . $j['expire_date'] . '~!~' . $j['remarks'] . '~!~' . $j['r_of_cancellation'] . '~!~' . $j['dept'] . '~!~' . $j['d_of_cancellation'] . '~!~' . $j['up_date_time'] . '~!~' . $j['fullname'] . '~!~' . $j['auth_no'] . '~!~' . $j['i_status'] . '&quot;)">';

				echo '<td>' . $j['auth_year'] . '</td>';
				echo '<td>' . $j['date_authorized'] . '</td>';
				echo '<td>' . $j['expire_date'] . '</td>';
				echo '<td>' . $j['remarks'] . '</td>';
				echo '<td>' . $j['r_of_cancellation'] . '</td>';
				echo '<td>' . $j['d_of_cancellation'] . '</td>';
				echo '<td>' . $j['dept'] . '</td>';
				echo '<td>' . $j['up_date_time'] . '</td>';


				echo '</tr>';
			}
		}
	} else {
		echo '<tr>';
		echo '<td style="text-align:center;" colspan="4">No Result</td>';
		echo '</tr>';
	}
}




if ($method == 'qc_update') {
	$auth_no = $_POST['auth_no'];
	$auth_year = $_POST['auth_year'];
	$date_authorized = $_POST['date_authorized'];
	$expire_date = $_POST['expire_date'];
	$remarks = $_POST['remarks'];
	$r_of_cancellation = $_POST['r_of_cancellation'];
	$dept = $_POST['dept'];
	$d_of_cancellation = $_POST['d_of_cancellation'];
	$id = $_POST['id'];
	$category = $_POST['category'];
	$c = 0;

	$error = 0;

	$query = "SELECT id FROM ";
	if ($category == 'Final') {
		$query .= "t_f_process";
	} else if ($category == 'Initial') {
		$query .= "t_i_process";
	}
	$query .= " WHERE id = '$id' AND  auth_no='$auth_no'  AND auth_year = '$auth_year' AND date_authorized = '$date_authorized' AND expire_date = '$expire_date' AND remarks = '$remarks' AND dept = '$dept'";

	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	$query = "UPDATE ";
	if ($category == 'Final') {
		$query .= "t_f_process";
	} else if ($category == 'Initial') {
		$query .= "t_i_process";
	}
	$query .= " SET r_of_cancellation = '$r_of_cancellation', d_of_cancellation = '$d_of_cancellation', r_status = 'Pending', dept = '$dept', up_date_time = '" . $_SESSION['fname'] . "/ " . $server_date_time . "' WHERE auth_no = '$auth_no'";
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

// <!-- Delete func to remove after -->
if($method == 'remove_record'){
	$id = $_POST['id'];
	$emp_id = $_POST['emp_id'];
	$category = $_POST['category'];

	$del_sql = "DELETE FROM ";

	if ($category == 'Final') {
		$del_sql .= "t_f_process";
	} else if ($category == 'Initial') {
		$del_sql .= "t_i_process";
	}

	$del_sql .= " WHERE id = :id AND emp_id = :emp_id";
	$del_stmt = $conn->prepare($del_sql);
	$del_stmt->bindParam(':id',$id);
	$del_stmt->bindParam(':emp_id',$emp_id);
	$del_stmt->execute();

	if($del_stmt){
		echo 'success';
	}else{
		echo 'failed';
	}
}