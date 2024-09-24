<?php
include '../conn.php';
include '../session.php';

// $page = '';
// if ($_SESSION['role'] == 'admin_reviewer') {
//     $page = 'admin_reviewer';
// } else if ($_SESSION['role'] == 'admin') {
//     $page = 'admin';
// }

// if (isset($_POST['upload'])) {
//     $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

//     if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
//         if (is_uploaded_file($_FILES['file']['tmp_name'])) {
//             $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
//             if (!$csvFile) {
//                 die("Error opening file");
//             }

//             // Skip the first row
//             fgetcsv($csvFile);

//             $error = 0;
//             $errorMessages = array();

//             while (($line = fgetcsv($csvFile)) !== false) {
//                 if (empty(array_filter($line))) {
//                     continue;
//                 }

//                 $fullname = $line[0];
//                 $m_name = $line[1];
//                 $emp_id = $line[2];
//                 $emp_id_old = $line[3];
//                 $agency = $line[4];
//                 $batch = $line[5];
//                 $emp_status = $line[6];

//                 if (empty($fullname) || empty($emp_id) || empty($agency) || empty($batch)) {
//                     $error++;
//                     $errorMessages[] = "Row skipped: Missing required fields for employee ID $emp_id";
//                     continue;
//                 }

//                 $prevQuery = "SELECT id, emp_id, emp_id_old FROM t_employee_m WHERE emp_id IN (?, ?)";
//                 $res = $conn->prepare($prevQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
//                 if (!$res->execute([$emp_id, $emp_id_old])) {
//                     $error++;
//                     $errorMessages[] = "Database error: Failed to check existing employee for ID $emp_id";
//                     continue;
//                 }

//                 if ($res->rowCount() > 0) {
//                     // Update existing employee
//                     $row = $res->fetch(PDO::FETCH_ASSOC);
//                     $id = $row['id'];
//                     $emp_id_ref = $row['emp_id'];
//                     $emp_id_old_ref = $row['emp_id_old'];

//                     $updateQuery = "UPDATE t_employee_m SET fullname = ?, emp_id = ?, batch = ?, m_name = ?, agency = ?, emp_status = ?";
//                     $params = [$fullname, $emp_id, $batch, $m_name, $agency, $emp_status];

//                     if (!empty($emp_id_old) && $emp_id_old != $emp_id_old_ref) {
//                         $updateQuery .= ", emp_id_old = ?";
//                         $params[] = $emp_id_old;
//                     }

//                     $updateQuery .= " WHERE id = ?";
//                     $params[] = $id;

//                     $stmt = $conn->prepare($updateQuery);
//                     if (!$stmt->execute($params)) {
//                         $error++;
//                         $errorMessages[] = "Failed to update employee with ID $emp_id";
//                         continue;
//                     }

//                     // Update related processes
//                     $updateFQuery = "UPDATE t_f_process SET emp_id_old = ?, emp_id = ? WHERE emp_id = ?";
//                     $stmt = $conn->prepare($updateFQuery);
//                     if (!$stmt->execute([$emp_id_old, $emp_id, $emp_id_ref])) {
//                         $error++;
//                         $errorMessages[] = "Failed to update F process for employee ID $emp_id";
//                     }

//                     $updateIQuery = "UPDATE t_i_process SET emp_id_old = ?, emp_id = ? WHERE emp_id = ?";
//                     $stmt = $conn->prepare($updateIQuery);
//                     if (!$stmt->execute([$emp_id_old, $emp_id, $emp_id_ref])) {
//                         $error++;
//                         $errorMessages[] = "Failed to update I process for employee ID $emp_id";
//                     }
//                 } else {
//                     // Insert new employee
//                     $insertQuery = "INSERT INTO t_employee_m (fullname, m_name, emp_id, agency, batch, emp_status) VALUES (?, ?, ?, ?, ?, ?)";
//                     $stmt = $conn->prepare($insertQuery);
//                     if (!$stmt->execute([$fullname, $m_name, $emp_id, $agency, $batch, $emp_status])) {
//                         $error++;
//                         $errorMessages[] = "Failed to insert new employee with ID $emp_id";
//                         continue;
//                     }
//                 }
//             }

//             fclose($csvFile);

//             $message = ($error == 0) ? "SUCCESS! All records processed." : "COMPLETED WITH ERRORS! Number of errors: $error";
//             $errorDetails = implode("\\n", $errorMessages);

//             echo '<script>
//                     var message = "' . $message . '";
//                     var errorDetails = "' . $errorDetails . '";
//                     if (errorDetails) {
//                         message += "\\n\\nError Details:\\n" + errorDetails;
//                     }
//                     alert(message);
//                     location.replace("../../page/' . $page . '/manpowerpage.php");
//                   </script>';
//         } else {
//             echo '<script>
//                     alert("CSV FILE NOT UPLOADED!");
//                     location.replace("../../page/' . $page . '/manpowerpage.php");
//                   </script>';
//         }
//     } else {
//         echo '<script>
//                 alert("INVALID FILE FORMAT!");
//                 location.replace("../../page/' . $page . '/manpowerpage.php");
//               </script>';
//     }
// }


$page = '';
if ($_SESSION['role'] == 'admin_reviewer') {
    $page = 'admin_reviewer';
} else if ($_SESSION['role'] == 'admin') {
    $page = 'admin';
}

if (isset($_POST['upload'])) {
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            if (!$csvFile) {
                die("Error opening file");
            }

            // Skip the first row
            fgetcsv($csvFile);

            $error = 0;
            $errorMessages = array();
            $employeesToUpdate = array(); // Array to hold employees to update after user confirmation

            while (($line = fgetcsv($csvFile)) !== false) {
                if (empty(array_filter($line))) {
                    continue;
                }

                $fullname = $line[0];
                $m_name = $line[1];
                $emp_id = $line[2];
                $emp_id_old = $line[3];
                $agency = $line[4];
                $batch = $line[5];
                $emp_status = $line[6];

                if (empty($fullname) || empty($emp_id) || empty($agency) || empty($batch)) {
                    $error++;
                    $errorMessages[] = "Missing required fields for employee ID $emp_id";
                    continue;
                }

                // Check for Duplicate Employee IDs in the File
                $empIdsInFile = [];
                if (in_array($emp_id, $empIdsInFile)) {
                    $error++;
                    $errorMessages[] = "Duplicate employee ID $emp_id in the file.";
                    continue;
                } else {
                    $empIdsInFile[] = $emp_id;
                }

                // Validate if Employee status are within the array
                // $validStatuses = ['Resigned', 'Retired', 'Dismiss'];
                // if (!in_array($emp_status, $validStatuses)) {
                //     $error++;
                //     $errorMessages[] = "Invalid employee status.";
                //     continue;
                // }

                // // checks if uploaded file have more than 6 column
                // if (count($line) != 6) { 
                //     $error++;
                //     $errorMessages[] = "Invalid number of columns.";
                //     continue;
                // }
                
                // Check if the employee already exists
                $prevQuery = "SELECT id, emp_id, emp_id_old FROM t_employee_m WHERE emp_id IN (?, ?)";
                $res = $conn->prepare($prevQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                if (!$res->execute([$emp_id, $emp_id_old])) {
                    $error++;
                    $errorMessages[] = "Database error: Failed to check existing employee for ID $emp_id";
                    continue;
                }

                if ($res->rowCount() > 0) {
                    // If employee exists, store the info for confirmation and possible update
                    $row = $res->fetch(PDO::FETCH_ASSOC);
                    $id = $row['id'];
                    $emp_id_ref = $row['emp_id'];
                    $emp_id_old_ref = $row['emp_id_old'];

                    // Add this employee to the update list for user confirmation
                    $employeesToUpdate[] = [
                        'id' => $id,
                        'fullname' => $fullname,
                        'm_name' => $m_name,
                        'emp_id' => $emp_id,
                        'emp_id_old' => $emp_id_old,
                        'agency' => $agency,
                        'batch' => $batch,
                        'emp_status' => $emp_status,
                        'emp_id_ref' => $emp_id_ref,
                        'emp_id_old_ref' => $emp_id_old_ref
                    ];
                } else {
                    // Insert new employee
                    $insertQuery = "INSERT INTO t_employee_m (fullname, m_name, emp_id, agency, batch, emp_status) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insertQuery);
                    if (!$stmt->execute([$fullname, $m_name, $emp_id, $agency, $batch, $emp_status])) {
                        $error++;
                        $errorMessages[] = "Failed to insert new employee with ID $emp_id";
                        continue;
                    }
                }
            }

            fclose($csvFile);

            // Now, handle updates for existing employees
            foreach ($employeesToUpdate as $employee) {
                echo '<script>
                    var updateConfirmed = confirm("Employee with ID ' . $employee['emp_id'] . ' already exists. Do you want to update the record?");
                    if (updateConfirmed) {
                        ' . updateEmployee($conn, $employee['id'], $employee['fullname'], $employee['m_name'], $employee['emp_id'], $employee['emp_id_old'], $employee['batch'], $employee['agency'], $employee['emp_status'], $employee['emp_id_ref'], $employee['emp_id_old_ref']) . '
                    }
                </script>';
            }

            // Final success or error message
            $message = ($error == 0) ? "SUCCESS! All records processed." : "COMPLETED WITH ERRORS! Number of errors: $error";
            $errorDetails = implode("\\n", $errorMessages);

            echo '<script>
                    var message = "' . $message . '";
                    var errorDetails = "' . $errorDetails . '";
                    if (errorDetails) {
                        message += "\\n\\nError Details:\\n" + errorDetails;
                    }
                    alert(message);
                    location.replace("../../page/' . $page . '/manpowerpage.php");
                  </script>';
        } else {
            echo '<script>
                    alert("CSV FILE NOT UPLOADED!");
                    location.replace("../../page/' . $page . '/manpowerpage.php");
                  </script>';
        }
    } else {
        echo '<script>
                alert("INVALID FILE FORMAT!");
                location.replace("../../page/' . $page . '/manpowerpage.php");
              </script>';
    }
}

function updateEmployee($conn, $id, $fullname, $m_name, $emp_id, $emp_id_old, $batch, $agency, $emp_status, $emp_id_ref, $emp_id_old_ref)
{
    // Update existing employee
    $updateQuery = "UPDATE t_employee_m SET fullname = ?, emp_id = ?, batch = ?, m_name = ?, agency = ?, emp_status = ?";
    $params = [$fullname, $emp_id, $batch, $m_name, $agency, $emp_status];

    if (!empty($emp_id_old) && $emp_id_old != $emp_id_old_ref) {
        $updateQuery .= ", emp_id_old = ?";
        $params[] = $emp_id_old;
    }

    $updateQuery .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $conn->prepare($updateQuery);
    if (!$stmt->execute($params)) {
        return 'alert("Failed to update employee with ID ' . $emp_id . '");';
    }

    // Update related processes
    $updateFQuery = "UPDATE t_f_process SET emp_id_old = ?, emp_id = ? WHERE emp_id = ?";
    $stmt = $conn->prepare($updateFQuery);
    if (!$stmt->execute([$emp_id_old, $emp_id, $emp_id_ref])) {
        return 'alert("Failed to update F process for employee ID ' . $emp_id . '");';
    }

    $updateIQuery = "UPDATE t_i_process SET emp_id_old = ?, emp_id = ? WHERE emp_id = ?";
    $stmt = $conn->prepare($updateIQuery);
    if (!$stmt->execute([$emp_id_old, $emp_id, $emp_id_ref])) {
        return 'alert("Failed to update I process for employee ID ' . $emp_id . '");';
    }

    return 'alert("Employee with ID ' . $emp_id . ' successfully updated.");';
}
