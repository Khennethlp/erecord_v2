<?php
require '../conn.php';

// Retrieve and sanitize the GET parameters
$emp_id = $_GET['emp_id'] ?? '';
$category = $_GET['category'] ?? '';
$pro = $_GET['pro'] ?? '';
$date = $_GET['date'] ?? '';
$date_authorized = $_GET['date_authorized'] ?? '';
$fullname = $_GET['fullname'] ?? '';

if (empty($category)) {
    echo 'Please select a category.';
    exit;
}

$c = 0;
$delimiter = ",";
$datenow = date('Y-m-d');
$filename = "E-Record_Data_" . $datenow . ".csv";

// Create a file pointer
$f = fopen('php://memory', 'w');

// Output the UTF-8 BOM for Excel compatibility
fputs($f, "\xEF\xBB\xBF");

// Set column headers
$fields = array('#', 'Process Name', 'Authorization No.', 'Authorization Year', 'Date Authorized', 'Expire Date', 'Employee Name', 'Employee No.', 'Batch No.', 'Department', 'Remarks', 'Reason of Cancellation', 'Date of Cancellation');
fputcsv($f, $fields, $delimiter);

// Build the SQL query
$query = "SELECT a.batch, a.process, a.auth_no, a.auth_year, a.date_authorized, a.expire_date, 
                 a.r_of_cancellation, a.d_of_cancellation, a.remarks, a.i_status, a.r_status, 
                 b.fullname, b.agency, a.dept, b.emp_id, c.category
          FROM ";

if ($category == 'Final') {
    $query .= "t_f_process a ";
} elseif ($category == 'Initial') {
    $query .= "t_i_process a ";
} else {
    echo 'Invalid category selected.';
    exit;
}

$query .= "LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
JOIN m_process c ON a.process = c.process
WHERE (a.i_status = 'Approved' OR a.i_status = 'Pending')";

$conditions = [];
$params = [];

// Append conditions based on the filters
if (!empty($emp_id)) {
    $conditions[] = "(b.emp_id = :emp_id OR b.emp_id_old = :emp_id)";
    $params[':emp_id'] = $emp_id;
}
if (!empty($fullname)) {
    $conditions[] = "b.fullname LIKE :fullname";
    $params[':fullname'] = $fullname . '%';
}
if (!empty($pro)) {
    $conditions[] = "a.process LIKE :pro";
    $params[':pro'] = '%' . $pro . '%';
}
if (!empty($date)) {
    $conditions[] = "a.expire_date = :expire_date";
    $params[':expire_date'] = $date;
}
if (!empty($date_authorized)) {
    $conditions[] = "a.date_authorized = :date_authorized";
    $params[':date_authorized'] = $date_authorized;
}

// Append the conditions to the query if they exist
if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

// Order the results
$query .= " ORDER BY a.process ASC, b.fullname ASC, a.auth_year DESC";

// Prepare the query
$stmt = $conn->prepare($query);

// Bind parameters to the prepared statement
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value, PDO::PARAM_STR);
}

// Execute the query
try {
    $stmt->execute();
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    exit;
}

// Stream output directly to the browser in chunks
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $c++;
    // Sanitize line breaks and spaces in fields
    foreach ($row as $key => $value) {
        $row[$key] = str_replace(["\r", "\n"], " ", $value);
    }

    // Prepare data for CSV
    $lineData = array(
        $c,
        $row['process'],
        $row['auth_no'],
        $row['auth_year'],
        $row['date_authorized'],
        $row['expire_date'],
        $row['fullname'],
        $row['emp_id'],
        $row['batch'],
        $row['dept'],
        $row['remarks'],
        $row['r_of_cancellation'],
        $row['d_of_cancellation']
    );
    fputcsv($f, $lineData, $delimiter);
}

// Move back to the beginning of the file
fseek($f, 0);

// Set headers for download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '";');
header('Pragma: no-cache');
header('Expires: 0');

// Output all remaining data on a file pointer
fpassthru($f);

// Close the connection
$conn = null;
exit;
?>
