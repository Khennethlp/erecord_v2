<?php
require '../conn.php';

// Sanitize and retrieve variables from the GET request
$emp_id = isset($_GET['emp_id']) ? trim($_GET['emp_id']) : '';
$agency = isset($_GET['agency']) ? trim($_GET['agency']) : '';
$batch = isset($_GET['batch']) ? trim($_GET['batch']) : '';
$fullname = isset($_GET['fullname']) ? trim($_GET['fullname']) : '';
$emp_status = isset($_GET['emp_status']) ? trim($_GET['emp_status']) : '';

// Initialize variables
$c = 0;
$delimiter = ",";
$datenow = date('Y-m-d');
$filename = "E-Record_Masterlist_" . $datenow . ".csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

// UTF-8 BOM for special character compatibility
fputs($f, "\xEF\xBB\xBF");

// Set column headers
$fields = array('#', 'Employee Name', 'Maiden Name', 'Employee No.', 'Employee No. Old', 'Batch No.', 'Provider');
fputcsv($f, $fields, $delimiter);

// Build the query dynamically
$query = "SELECT fullname, m_name, emp_id, emp_id_old, agency, batch FROM t_employee_m WHERE (emp_id LIKE '$emp_id%' OR emp_id_old LIKE '$emp_id%') ";

// Use prepared statements for security
$params = [];

if (!empty($emp_id)) {
    $query .= " AND (emp_id LIKE :emp_id OR emp_id_old LIKE :emp_id)";
    $params[':emp_id'] = $emp_id . '%';
}
if (!empty($emp_status)) {
    $query .= " AND emp_status = :emp_status";
    $params[':emp_status'] = $emp_status;
}
if (!empty($fullname)) {
    $query .= " AND fullname LIKE :fullname";
    $params[':fullname'] = $fullname . '%';
}
if (!empty($agency)) {
    $query .= " AND agency = :agency";
    $params[':agency'] = $agency;
}
if (!empty($batch)) {
    $query .= " AND batch = :batch";
    $params[':batch'] = $batch;
}

// Add ordering
$query .= " ORDER BY fullname ASC";

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind parameters to the query
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value, PDO::PARAM_STR);
}

// Execute the query
$stmt->execute();

// Fetch and process rows
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $c++;

    // Sanitize line breaks and spaces in fields
    foreach ($row as $key => $value) {
        $row[$key] = str_replace(["\r", "\n"], " ", $value);
    }

    // Prepare data for CSV
    $lineData = array(
        $c,
        $row['fullname'], 
        $row['m_name'], 
        $row['emp_id'], 
        $row['emp_id_old'],
        $row['batch'], 
        $row['agency']
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
fclose($f);
$conn = null;
exit;
?>
