<?php
include '../conn.php';
include '../session.php';

$method = $_POST['method'];


// New Authrization
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

if ($method == 'get_fullname_by_emp_no') {
    $emp_id = $_POST['emp_id'];
    $message = '';

    if (!empty($emp_id)) {
        $query = "SELECT emp_id, batch, fullname FROM t_employee_m WHERE emp_id = '$emp_id'";

        $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $batch = $row['batch'];
            $fullname = $row['fullname'];
            $message = 'success';
        } else {
            $message = 'Employee Not Found';
        }
    } else {
        $message = 'Employee ID Not Provided';
    }

    $response_arr = array(
        'batch' => $batch,
        'fullname' => $fullname,
        'message' => $message
    );

    echo json_encode($response_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}


if ($method == 'add_new_autho') {
    $pro = $_POST['pro'];
    $auth_no = $_POST['auth_no'];
    $emp_id = $_POST['emp_id'];
    $auth_year = $_POST['auth_year'];
    $date_authorized = $_POST['date_authorized'];
    $expire_date = $_POST['expire_date'];
    $remarks = $_POST['remarks'];
    $dept = $_POST['dept'];
    $batch = $_POST['batch'];
    $fullname = $_POST['fullname'];
    $category = $_POST['category'];
    $up_date = $fname . '/ ' . $server_date_time;
    
    $query = "SELECT a.*, b.fullname, b.emp_id ";
    if ($category == 'Final') {
        $query = $query . " FROM t_f_process";
    } else if ($category == 'Initial') {
        $query = $query . " FROM t_i_process";
    }
    $query = $query . " a LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id WHERE a.process = '$pro' AND a.auth_no = '$auth_no' AND a.emp_id = '$emp_id' AND a.auth_year = '$auth_year' AND a.date_authorized = '$date_authorized' AND a.expire_date ='$expire_date' AND a.remarks = '$remarks' AND a.dept ='$dept' AND a.batch ='$batch' ";
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo 'duplicate';
    } else {
        $insert = "";
        if ($category == 'Final') {
            $insert = $insert . "INSERT INTO t_f_process";
        } else if ($category == 'Initial') {
            $insert = $insert . "INSERT INTO t_i_process";
        }

        $insert = $insert . "(process, auth_no, emp_id, auth_year, date_authorized, expire_date, remarks, up_date_time, i_status, dept, batch) VALUES ('$pro', '$auth_no', '$emp_id', '$auth_year', '$date_authorized', '$expire_date', '$remarks', '$up_date', 'Pending', '$dept', '$batch')";
        $stmt = $conn->prepare($insert);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
}


// Renew Authorization
if ($method == 'get_auth_no_by_emp_no') {
    $auth_no = $_POST['auth_no'];
    $message = '';
    $emp_id = null;
    $fullname = null;
    $dept = null;
    $batch = null;
    $category = null;
    $process = null;

    if (!empty($auth_no)) {
        try {
            $query = "SELECT d.emp_id, d.fullname, a.dept, a.batch, c.category, a.process 
                      FROM t_f_process a
                      LEFT JOIN t_employee_m d ON a.emp_id = d.emp_id
                      LEFT JOIN m_process c ON c.process = a.process
                      WHERE a.auth_no = '$auth_no'";

            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $emp_id = $result['emp_id'];
                $fullname = $result['fullname'];
                $dept = $result['dept'];
                $batch = $result['batch'];
                $category = $result['category'];
                $process = $result['process'];
                $message = 'success';
            } else {
                $query = "SELECT d.emp_id, d.fullname, b.dept, b.batch, c.category, b.process 
                          FROM t_i_process b
                          LEFT JOIN t_employee_m d ON b.emp_id = d.emp_id
                          LEFT JOIN m_process c ON c.process = b.process
                          WHERE b.auth_no = :auth_no";

                $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->bindParam(':auth_no', $auth_no);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $emp_id = $result['emp_id'];
                    $fullname = $result['fullname'];
                    $dept = $result['dept'];
                    $batch = $result['batch'];
                    $category = $result['category'];
                    $process = $result['process'];
                    $message = 'success';
                } else {
                    $message = 'Not Found';
                }
            }
        } catch (PDOException $e) {
            $message = 'Database Error: ' . $e->getMessage();
        }
    } else {
        $message = 'Authorization Number Not Provided';
    }

    $response_arr = array(
        'emp_id' => $emp_id,
        'fullname' => $fullname,
        'dept' => $dept,
        'batch' => $batch,
        'category' => $category,
        'process' => $process,
        'message' => $message
    );

    echo json_encode($response_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}


// ADD RENEWAL AUTHORIZATION

if ($method == 'add_emp_pro') {
    $pro = $_POST['pro'];
    $auth_no = $_POST['auth_no'];
    $emp_id = $_POST['emp_id'];
    $auth_year = $_POST['auth_year'];
    $date_authorized = $_POST['date_authorized'];
    $expire_date = $_POST['expire_date'];
    $remarks = $_POST['remarks'];
    $dept = $_POST['dept'];
    $batch = $_POST['batch'];
    $fullname = $_POST['fullname'];
    $category = $_POST['category'];
    $up_date = $fname . '/ ' . $server_date_time;


    $query = "SELECT a.*, b.fullname, b.emp_id ";

    if ($category == 'Final') {
        $query = $query . " FROM t_f_process";
    } else if ($category == 'Initial') {
        $query = $query . " FROM t_i_process";
    }

    $query = $query . " a LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id WHERE a.process = '$pro' AND a.auth_no = '$auth_no' AND a.emp_id = '$emp_id' AND a.auth_year = '$auth_year' AND a.date_authorized = '$date_authorized' AND a.expire_date ='$expire_date' AND a.remarks = '$remarks' AND a.dept ='$dept' AND a.batch ='$batch' ";
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo 'duplicate';
    } else {
        $insert = "";
        if ($category == 'Final') {
            $insert = $insert . "INSERT INTO t_f_process";
        } else if ($category == 'Initial') {
            $insert = $insert . "INSERT INTO t_i_process";
        }

        $insert = $insert . "(process, auth_no, emp_id, auth_year, date_authorized, expire_date, remarks, up_date_time, i_status, dept, batch) VALUES ('$pro', '$auth_no', '$emp_id', '$auth_year', '$date_authorized', '$expire_date', '$remarks', '$up_date', 'Pending', '$dept', '$batch')";
        $stmt = $conn->prepare($insert);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
}
