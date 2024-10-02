<?php
require 'process/conn.php';

// Get input parameters with default values
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

$delimiter = ",";
$datenow = date('Y-m-d');
$filename = "E-Record_Data - " . $datenow . ".csv";

// Set headers to download file rather than displayed 
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '";');
header('Pragma: no-cache');
header('Expires: 0');

// Output the UTF-8 BOM
echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel compatibility

// Set column headers 
$fields = array('#', 'Process Name', 'Authorization No.', 'Authorization Year', 'Date Authorized', 'Expire Date', 'Employee Name', 'Employee No.', 'Batch No.', 'Department', 'Remarks', 'Reason of Cancellation', 'Date of Cancellation');
echo implode($delimiter, $fields) . "\n"; // Output headers directly

// Begin SQL query
$query = "SELECT a.batch, a.process, a.auth_no, a.auth_year, a.date_authorized, a.expire_date, 
          a.r_of_cancellation, a.d_of_cancellation, a.remarks, a.i_status, a.r_status, 
          b.fullname, b.agency, a.dept, b.emp_id, c.category
FROM ";

if ($category == 'Final') {
    $query .= "t_f_process a ";
} else if ($category == 'Initial') {
    $query .= "t_i_process a ";
}

$query .= "LEFT JOIN t_employee_m b ON a.emp_id = b.emp_id AND a.batch = b.batch
JOIN m_process c ON a.process = c.process
WHERE a.i_status = 'Approved' ";

$conditions = [];
if (!empty($emp_id)) {
    $conditions[] = "(b.emp_id = :emp_id OR b.emp_id_old = :emp_id_old)";
}
if (!empty($fullname)) {
    $conditions[] = "b.fullname LIKE :fullname";
}
if (!empty($pro)) {
    // Trim the 'pro' parameter and search for any process containing 'pro'
    $pro = trim($pro);
    $conditions[] = "a.process LIKE :pro";
    // echo "Value of pro: " . $pro . "\n"; // Debugging output
}
if (!empty($date)) {
    $conditions[] = "a.expire_date = :expire_date";
}
if (!empty($date_authorized)) {
    $conditions[] = "a.date_authorized = :date_authorized";
}

// Append conditions to the query
if (count($conditions) > 0) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY a.process ASC, b.fullname ASC, a.auth_year DESC";

// Prepare and bind parameters
$stmt = $conn->prepare($query);
if (!empty($emp_id)) {
    $stmt->bindValue(':emp_id', $emp_id);
    $stmt->bindValue(':emp_id_old', $emp_id);
}
if (!empty($fullname)) {
    $stmt->bindValue(':fullname', $fullname . '%'); // Add wildcard for LIKE
}
if (!empty($pro)) {
    $stmt->bindValue(':pro', '%' . $pro . '%'); // Add wildcard for LIKE
}
if (!empty($date)) {
    $stmt->bindValue(':expire_date', $date);
}
if (!empty($date_authorized)) {
    $stmt->bindValue(':date_authorized', $date_authorized);
}

// Execute the query
$stmt->execute();

// Stream output directly to the browser
$c = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $c++;
    // $lineData = array($c, $row['process'], $row['auth_no'], $row['auth_year'], $row['date_authorized'], $row['expire_date'], '"'.$row['fullname'].'"' , $row['emp_id'], $row['batch'], $row['dept'], '"'.$row['remarks'].'"', '"'.$row['r_of_cancellation'].'"', '"'.$row['d_of_cancellation'].'"');
    $lineData = array(
        $c,
        $row['process'],
        $row['auth_no'],
        $row['auth_year'],
        $row['date_authorized'],
        $row['expire_date'],
        '"' . str_replace('"', '""', $row['fullname']) . '"', // Escape any double quotes in fullname
        $row['emp_id'],
        $row['batch'],
        $row['dept'],
        '"' . str_replace('"', '""', $row['remarks']) . '"', // Escape any double quotes in remarks
        '"' . str_replace('"', '""', $row['r_of_cancellation']) . '"', // Escape any double quotes in r_of_cancellation
        '"' . str_replace('"', '""', $row['d_of_cancellation']) . '"'  // Escape any double quotes in d_of_cancellation
    );
    echo implode($delimiter, $lineData) . "\n"; // Output each line directly
}

// Close the connection
$conn = null;
exit; // Make sure to exit after outputting
