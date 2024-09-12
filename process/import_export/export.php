<?php 
include '../conn.php';

$method = $_POST['method'];

if ($method == 'fetch_pro') {
	$category = $_POST['category'];
	$query = "SELECT process FROM m_process WHERE category = '$category' ORDER BY process ASC";
	$stmt = $conn -> prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt -> execute();
	if ($stmt -> rowCount() > 0) {
		echo '<option value="">Please select a process.....</option>';
		foreach($stmt -> fetchAll() as $row) {
			echo '<option>'.htmlspecialchars($row['process']).'</option>';
		}
	} else {
		echo '<option>Please select a process.....</option>';
	}
}

function count_category($search_arr, $conn) {
	if (!empty($search_arr['category'] )) {
	$query = "SELECT count(a.id) as total";

	if ($search_arr['category'] == 'Final') {
		$query = $query . " FROM t_f_process";
	}else if ($search_arr['category'] == 'Initial') {
		$query = $query . " FROM t_i_process";
	}

	$query = $query . " a
						LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id 
						JOIN m_process c ON a.process = c.process
						WHERE a.i_status = 'Approved'";

	if (!empty($search_arr['emp_id'])) {
		$query = $query . " AND (b.emp_id = '".$search_arr['emp_id']."' OR b.emp_id_old = '".$search_arr['emp_id']."')";
	}
	if (!empty($search_arr['pro'])) {
		$query = $query . " AND a.process LIKE '".$search_arr['pro']."'";
	}
	if (!empty($search_arr['date'])) {
		$query = $query . " AND a.expire_date = '".$search_arr['date']."' ";
	}
	
	// $query = $query ." ORDER BY a.process ASC, b.fullname ASC, a.auth_year DESC";

	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
		foreach($stmt->fetchALL() as $j){
			$total = $j['total'];
		}
	}else{
		$total = 0;
	}
	return $total;
}
}

if ($method == 'count_category') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$date = $_POST['date'];

	$search_arr = array(
		"emp_id" => $emp_id, 
		"pro" => $pro, 
		"category" => $category,
		"date" => $date
	);

	echo count_category($search_arr, $conn);
}

if ($method == 'fetch_category_pagination') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$date = $_POST['date'];

	$search_arr = array(
		"emp_id" => $emp_id, 
		"pro" => $pro, 
		"category" => $category,
		"date" => $date
	);

	$results_per_page = 100;

	$number_of_result = intval(count_category($search_arr, $conn));

	//determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="'.$page.'">'.$page.'</option>';
    }

}

if ($method == 'fetch_category') {
	$emp_id = $_POST['emp_id'];
	$pro = $_POST['pro'];
	$category = $_POST['category'];
	$date = $_POST['date'];
	$current_page = intval($_POST['current_page']);

	$c = 0;


		if (!empty($category)) {
		$results_per_page = 100;

		//determine the sql LIMIT starting number for the results on the displaying page
		$page_first_result = ($current_page-1) * $results_per_page;

		// For row numbering
		$c = $page_first_result;

		$query = "SELECT a.batch, a.process,a.auth_no,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.code,a.status,a.r_status,a.i_status,b.fullname,b.m_name,b.agency,a.dept,b.batch,b.emp_id,c.category";

	if ($category == 'Final') {
				$query = $query . " FROM t_f_process";
		}else if ($category == 'Initial') {
				$query = $query . " FROM t_i_process";
		}
	$query = $query . " a
						LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
						JOIN m_process c ON a.process = c.process
						WHERE a.i_status = 'Approved'";

	if (!empty($emp_id)) {
		$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
	}
	if (!empty($pro)) {
		$query = $query . " AND a.process LIKE '$pro'";
	}

	if (!empty($date)) {
		$query = $query . " AND a.expire_date = '$date'";
	}
	$query = $query ." ORDER BY process ASC, fullname ASC, auth_year DESC OFFSET " . $page_first_result . " ROWS FETCH NEXT " . $results_per_page . " ROWS ONLY";
	$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	$stmt->execute();
	if ($stmt->rowCount() > 0) {
		foreach($stmt->fetchAll() as $j){
			$c++;
			$row_class = "";
			$i_status = $j['i_status'];
			if ($j['status'] == 'Not Qualified') {
				$row_class = " bg-warning";
			}

			echo '<tr class="modal-trigger'.$row_class.'">';
				echo '<td>'.$c.'</td>';
				echo '<td>'.$j['code'].'</td>';
				echo '<td>'.$j['process'].'</td>';
				echo '<td>'.$j['expire_date'].'</td>';
				echo '<td>'.$j['auth_no'].'</td>';
				echo '<td>'.$j['fullname'].'</td>';
				echo '<td>'.$j['m_name'].'</td>';
				echo '<td>'.$j['emp_id'].'</td>';
				echo '<td>'.$j['batch'].'</td>';
				echo '<td>'.$j['dept'].'</td>';
				echo '<td>'.$j['status'].'</td>';
				echo '<td>'.$j['remarks'].'</td>';				
			echo '</tr>';
	}
	}else{
		echo '<tr>';
			echo '<td style="text-align:center;" colspan="4">No Result</td>';
		echo '</tr>';
	}
   } else {
		echo '<script>alert("Please select category and process");</script>';
	}

	
}



?>