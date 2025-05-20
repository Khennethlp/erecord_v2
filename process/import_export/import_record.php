<?php
// error_reporting(0);
require '../conn.php';
include '../session.php';

function fetch_pro($category, $conn)
{
    $arr = array();
    $query = "SELECT process FROM m_process WHERE category = '$category' ORDER BY process ASC";
    $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        foreach ($stmt->fetchAll() as $row) {
            array_push($arr, $row['process']);
        }
    }
    return $arr;
}



function check_csv($file, $category, $conn)
{
    // READ FILE
    $csvFile = fopen($file, 'r');

    // SKIP FIRST LINE
    fgets($csvFile);

    $pro_arr = fetch_pro($category, $conn);

    $hasError = 0;
    $hasBlankError = 0;
    $isDuplicateOnCsv = 0;

    $hasBlankErrorArr = array();
    $isDuplicateOnCsvArr = array();
    $dup_temp_arr = array();

    $row_valid_arr = array(0, 0);

    $notExistsProArr = array();
    $existsAuthNoEmpNoArr = array();

    $message = "";
    $check_csv_row = 1;

    while (($line = fgetcsv($csvFile)) !== false) {
        $check_csv_row++;

        if (empty(implode('', $line))) {
            continue;
        }

        $pro = $line[0];
        $emp_id = $line[1];
        $auth_no = $line[2];

        if ($line[0] == '' || $line[1] == '' || $line[2] == '' || $line[3] == '' || $line[4] == '' || $line[5] == '') {

            $hasBlankError++;
            $hasError = 1;
            array_push($hasBlankErrorArr, $check_csv_row);
        }



        // CHECK ROW VALIDATION
        if (!in_array($pro, $pro_arr)) {
            $hasError = 1;
            $row_valid_arr[0] = 1;
            array_push($notExistsProArr, $check_csv_row);
        }

        $sql = "SELECT emp_id_old FROM t_employee_m WHERE emp_id = '$emp_id'";
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            foreach ($stmt->fetchALL() as $x) {
                $emp_id_old = $x['emp_id_old'];
            }
        }

        $sql = "SELECT auth_no";
        if ($category == 'final') {
            $sql = $sql . " FROM t_f_process";
        } else if ($category == 'initial') {
            $sql = $sql . " FROM t_i_process";
        }
        $sql = $sql . " WHERE (emp_id != '$emp_id' AND emp_id != '$emp_id_old') AND process = '$pro' AND auth_no = '$auth_no'";
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $hasError = 1;
            $row_valid_arr[1] = 1;
            array_push($existsAuthNoEmpNoArr, $check_csv_row);
        }

        // Joining all row values for checking duplicated rows
        $whole_line = join(',', $line);

        // CHECK ROWS IF IT HAS DUPLICATE ON CSV
        if (isset($dup_temp_arr[$whole_line])) {
            $isDuplicateOnCsv = 1;
            $hasError = 1;
            array_push($isDuplicateOnCsvArr, $check_csv_row);
        } else {
            $dup_temp_arr[$whole_line] = 1;
        }
    }

    fclose($csvFile);

    if ($hasError == 1) {
        if ($row_valid_arr[0] == 1) {
            $message = $message . 'Process Name doesn\'t exists on row/s ' . implode(", ", $notExistsProArr) . '. ';
        }
        if ($row_valid_arr[1] == 1) {
            $message = $message . 'Authorization No. exists on different Employee No. on row/s ' . implode(", ", $existsAuthNoEmpNoArr) . '. ';
        }

        if ($hasBlankError >= 1) {
            $message = $message . 'Blank Cell Exists on row/s ' . implode(", ", $hasBlankErrorArr) . '. ';
        }
        if ($isDuplicateOnCsv == 1) {
            $message = $message . 'Duplicated Record/s on row/s ' . implode(", ", $isDuplicateOnCsvArr) . '. ';
        }
    }
    return $message;
}

$page = '';
if ($_SESSION['role'] == 'admin_reviewer') {
    $page = 'admin_reviewer';
} else if ($_SESSION['role'] == 'admin') {
    $page = 'admin';
}

if (isset($_POST['upload'])) {
    $id_number_record = $_POST['id_number_record'];
    $category = $_POST['category'];
    $pro_arr = fetch_pro($category, $conn);
    $csvMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );

    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $chkCsvMsg = check_csv($_FILES['file']['tmp_name'], $category, $conn);

            if ($chkCsvMsg == '') {
                $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
                fgetcsv($csvFile); // Skip header

                while (($line = fgetcsv($csvFile)) !== false) {
                    if (empty(implode('', $line))) {
                        continue;
                    }

                    list($pro, $emp_id, $auth_no, $auth_year, $date_authorized, $expire_date, $remarks, $dept, $batch) = $line;
                    $date_authorized = date('Y-m-d', strtotime($date_authorized));
                    $expire_date = date('Y-m-d', strtotime($expire_date));
                    $up_date = $fname . '/ ' . $server_date_time;
                    
                    if (empty($emp_id) || empty($batch)) {
                        echo '<script>alert("Error: Employee No. or Batch cannot be empty!"); location.replace("../../page/' . $page . '/import_exportpage.php");</script>';
                        fclose($csvFile);
                        exit();
                    }

                    // Check if record exists
                    $checkQuery = "SELECT id FROM " . ($category == 'final' ? "t_f_process" : "t_i_process") . " 
                                   WHERE emp_id = ? AND process = ? AND auth_year = ? 
                                   AND date_authorized = ? AND expire_date = ?";
                    $stmt = $conn->prepare($checkQuery);
                    $stmt->execute([$emp_id, $pro, $auth_year, $date_authorized, $expire_date]);
                    $existingData = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingData) {
                        // Update existing record
                        // added i_status = 'Pending' to reset status, remove if not needed or something went wrong
                        $updateQuery = "UPDATE " . ($category == 'final' ? "t_f_process" : "t_i_process") . " 
                                        SET auth_no = ?, remarks = ?, up_date_time = ?, dept = ?, batch = ?, i_status = 'Pending' 
                                        WHERE id = ?";
                        $stmt = $conn->prepare($updateQuery);
                        $stmt->execute([$auth_no, $remarks, $up_date, $dept, $batch, $existingData['id']]);
                    } else {
                        // Insert new record
                        $insertQuery = "INSERT INTO " . ($category == 'final' ? "t_f_process" : "t_i_process") . "
                                        (emp_id, process, auth_no, auth_year, date_authorized, expire_date, up_date_time, dept, batch, i_status, remarks) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
                        $stmt = $conn->prepare($insertQuery);
                        $stmt->execute([$emp_id, $pro, $auth_no, $auth_year, $date_authorized, $expire_date, $up_date, $dept, $batch, $remarks]);
                    }
                }
                fclose($csvFile);
                echo '<script>alert("File uploaded successfully."); location.replace("../../page/' . $page . '/import_exportpage.php");</script>';
            } else {
                echo '<script>alert("' . $chkCsvMsg . '"); location.replace("../../page/' . $page . '/import_exportpage.php");</script>';
            }
        } else {
            echo '<script>alert("CSV FILE NOT UPLOADED!"); location.replace("../../page/' . $page . '/import_exportpage.php");</script>';
        }
    } else {
        echo '<script>alert("INVALID FILE FORMAT!"); location.replace("../../page/' . $page . '/import_exportpage.php");</script>';
    }
}


// KILL CONNECTION
$conn = null;
