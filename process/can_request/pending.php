<?php 
session_start();
include '../conn.php';

$method = $_POST['method'];

if ($method == 'fetch_pro_can') {
	$category = $_POST['category'];
	$query = "SELECT process FROM m_process WHERE category = '$category' ORDER BY process ASC";
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

function count_pending($search_arr, $conn) {
	if (!empty($search_arr['category'] )) {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$processName = $_POST['processName'];

	$query = "SELECT count(auth_no) AS total FROM (";

	$query = $query . "SELECT a.auth_no";

	if ($search_arr['category'] == 'Final') {
		$query = $query . " FROM t_f_process";
	}else if ($search_arr['category'] == 'Initial') {
		$query = $query . " FROM t_i_process";
	}

	$query = $query . " a
						LEFT JOIN t_employee_m b  ON a.emp_id = b.emp_id  
						JOIN m_process c ON a.process = c.process
						where a.r_status = 'Pending' ";
	if (!empty($search_arr['emp_id'])) {
				$query = $query . " AND (b.emp_id = '".$search_arr['emp_id']."' OR b.emp_id_old = '".$search_arr['emp_id']."')";
			}
	if (!empty($search_arr['fullname'])) {
		$query = $query . " AND b.fullname LIKE'".$search_arr['fullname']."%'";
	}
	if (!empty($search_arr['processName'])) {
		$query = $query . " AND c.process LIKE'".$search_arr['processName']."%'";
	}

	
	$query = $query . "GROUP BY a.auth_no ORDER BY SUBSTRING_INDEX(a.up_date_time, '/', -1) DESC";

	$query = $query . ") AS asub";

	$stmt = $conn->prepare($query);
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

if ($method == 'count_pending') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$processName = $_POST['processName'];

	$search_arr = array(
		"emp_id" => $emp_id, 
		"fullname" => $fullname, 
		"category" => $category,
		"processName" => $processName
	);

	echo count_pending($search_arr, $conn);
}

if ($method == 'search_pending_pagination') {
	$emp_id = $_POST['emp_id'];
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$processName = $_POST['processName'];

	$search_arr = array(
		"emp_id" => $emp_id, 
		"fullname" => $fullname, 
		"category" => $category,
		"processName" => $processName
	);

	$results_per_page = 100;

	$number_of_result = intval(count_pending($search_arr, $conn));

	//determine the total number of pages available  
	$number_of_page = ceil($number_of_result / $results_per_page);

	for ($page = 1; $page <= $number_of_page; $page++) {
		echo '<option value="'.$page.'">'.$page.'</option>';
    }

}



if ($method == 'fetch_category') {
	$emp_id = $_POST['emp_id']; 
	$fullname = $_POST['fullname'];
	$category = $_POST['category'];
	$processName = $_POST['processName'];
	$current_page = intval($_POST['current_page']);
	$c = 0;

	if (!empty($category)) {

		$results_per_page = 100;

		//determine the sql LIMIT starting number for the results on the displaying page
		$page_first_result = ($current_page-1) * $results_per_page;

		// For row numbering
		$c = $page_first_result;

		$query = "SELECT a.r_approve_by,a.id,a.auth_no,a.auth_year,a.date_authorized,a.expire_date,a.r_of_cancellation,a.d_of_cancellation,a.remarks,a.up_date_time,a.r_status,b.fullname,b.agency,a.dept,b.batch,b.emp_id,c.category,c.process";

		if ($category == 'Final') {
			$query = $query . " FROM t_f_process";
		}else if ($category == 'Initial') {
			$query = $query . " FROM t_i_process";
		}
		$query = $query . " a
							LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id  
							JOIN m_process c ON a.process = c.process
							where a.r_status = 'Pending' ";
		if (!empty($emp_id)) {
			$query = $query . " AND (b.emp_id = '$emp_id' OR b.emp_id_old = '$emp_id')";
		}

		if (!empty($fullname)) {
			$query = $query . " AND b.fullname LIKE'$fullname%'";
		}
		if (!empty($processName)) {
			$query = $query . " AND c.process LIKE'$processName%'";
		}
		$query = $query . "GROUP BY a.auth_no ASC ORDER BY SUBSTRING_INDEX(a.up_date_time, '/', -1) DESC LIMIT ".$page_first_result.", ".$results_per_page;
		$stmt = $conn->prepare($query);
		$stmt->execute();
		if ($stmt->rowCount() > 0) {
			foreach($stmt->fetchAll() as $j){
				$c++;
			
				
					echo '<tr style="cursor:pointer;" class="modal-trigger" data-toggle="modal" data-target="#view_p" onclick="p_details(&quot;'.$j['id'].'~!~'.$j['auth_year'].'~!~'.$j['date_authorized'].'~!~'.$j['expire_date'].'~!~'.$j['remarks'].'~!~'.$j['r_of_cancellation'].'~!~'.$j['d_of_cancellation'].'~!~'.$j['up_date_time'].'~!~'.$j['fullname'].'~!~'.$j['auth_no'].'&quot;)">';
					

					echo '<td>';
	                echo '<p>
	                        <label>
	                            <input type="checkbox" name="" id="" class="singleCheck" onclick="get_checked_length();" value="'.$j['auth_no'].'">
	                            <span></span>
	                        </label>
	                    </p>';
	                echo '</td>';
	                echo '<td>'.$c.'</td>';
					echo '<td>'.$j['process'].'</td>';
					echo '<td>'.$j['auth_no'].'</td>';
					echo '<td>'.$j['fullname'].'</td>';
					echo '<td>'.$j['emp_id'].'</td>';
					echo '<td>'.$j['r_of_cancellation'].'</td>';
					echo '<td>'.$j['d_of_cancellation'].'</td>';
					echo '<td>'.$j['up_date_time'].'</td>';
					echo '<td>'.$j['dept'].'</td>';
					echo '<td>'.$j['r_status'].'</td>';
					echo '<td>'.$j['remarks'].'</td>';
					
					
				echo '</tr>';
			}
		
		}else{
				echo '<tr>';
					echo '<td style="text-align:center;" colspan="4">No Result</td>';
				echo '</tr>';
			}
	}else {
		echo '<script>alert("Please select category ");</script>';
	}
}

if ($method == 'qc_view') {
    $fullname = $_POST['fullname'];
    $auth_no = $_POST['auth_no'];
    $category = $_POST['category'];

    $c = 0;

    $query = "SELECT a.id, a.auth_no, a.auth_year, a.date_authorized, a.expire_date, a.r_of_cancellation, a.d_of_cancellation, a.remarks, a.up_date_time, a.r_status, a.r_review_by, b.fullname, b.emp_id, c.category ";

    if ($category == 'Final') {
        $query .= "FROM t_f_process a ";
    } else if ($category == 'Initial') {
        $query .= "FROM t_i_process a ";
    }

    $query .= "LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id ";
    $query .= "JOIN m_process c ON a.process = c.process ";
    $query .= "WHERE a.auth_no = :auth_no";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':auth_no', $auth_no);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        foreach ($stmt->fetchAll() as $j) {
            $c++;
            echo '<tr>';
            echo '<td>' . $j['auth_year'] . '</td>';
            echo '<td>' . $j['date_authorized'] . '</td>';
            echo '<td>' . $j['expire_date'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr>';
        echo '<td style="text-align:center;" colspan="4">No Result</td>';
        echo '</tr>';
    }
}


if ($method == 'qc_review') {
	$category = $_POST['category'];
	$arr = [];
	$arr = $_POST['arr'];

	$count = count($arr);
	foreach ($arr as $auth_no) {

		$query = "UPDATE";
		if ($category == 'Final') {
			$query = $query . " t_f_process";
		}else if ($category == 'Initial') {
			$query = $query . " t_i_process";
		}
		$query = $query . " SET r_status = 'Reviewed', status = 'Qualified', r_review_by = '".$_SESSION['fname']. "/ " .$server_date_time."' WHERE auth_no = '$auth_no' ";
		$stmt = $conn->prepare($query);
		$stmt -> execute();
		$count--;
	}

	if ($count == 0) {
		echo 'success';
	} else {
		echo 'Error';
	}

}

if ($method == 'qc_disreview') {
    $category = $_POST['category'];
    $arr = $_POST['arr'];

    $count = count($arr);
    foreach ($arr as $auth_no) {
        $query = "UPDATE";
        if ($category == 'Final') {
            $query .= " t_f_process";
        } else if ($category == 'Initial') {
            $query .= " t_i_process";
        }
        $query .= " SET r_status = 'Disapproved', status = 'Qualified',  r_review_by = '".$_SESSION['fname']. "/ " .$server_date_time."', r_of_cancellation = NULL, d_of_cancellation = NULL WHERE auth_no = '$auth_no'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $count--;
    }

    if ($count == 0) {
        echo 'success';
    } else {
        echo 'Error';
    }
}





?>