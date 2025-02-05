<?php
include '../conn.php';
include '../session.php';

$page = '';
if ($_SESSION['role'] == 'admin_reviewer') {
    $page = 'admin_reviewer';
} else if ($_SESSION['role'] == 'admin') {
    $page = 'admin';
}

if (isset($_POST['upload'])) {
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    $fileName = $_FILES['file']['name'];
    if (preg_match('/^import_employee.*\.csv$/', $fileName)) {

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
                $employeesToUpdate = array(); // Array to hold employees to update automatically

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

                    // Check if the employee already exists
                    $prevQuery = "SELECT id, emp_id, emp_id_old, fullname, batch FROM t_employee_m WHERE emp_id IN (?, ?) AND batch = ?";
                    $res = $conn->prepare($prevQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                    if (!$res->execute([$emp_id, $emp_id_old, $batch])) {
                        $error++;
                        continue;
                    }

                    if ($res->rowCount() > 0) {
                        // If employee exists, check the conditions to update
                        $row = $res->fetch(PDO::FETCH_ASSOC);
                        $id = $row['id'];
                        $emp_id_ref = $row['emp_id'];
                        $emp_id_old_ref = $row['emp_id_old'];
                        $db_fullname = $row['fullname'];
                        $db_batch = $row['batch'];

                        // Check if all details are the same
                        // if ($fullname == $db_fullname && $batch == $db_batch && $emp_id == $emp_id_ref && $emp_id_old == $emp_id_old_ref) {
                        //     $errorMessages[] = "Employee record with ID $emp_id already exists (Full record match). Skipping update.";
                        //     continue;
                        // }

                        // Check the conditions: fullname, batch, and emp_old_id
                        $employeesToUpdate[] = [
                            'id' => $id,
                            'emp_id' => $emp_id,
                            'emp_id_old' => $emp_id_old,
                            'emp_id_ref' => $emp_id_ref,
                            'emp_id_old_ref' => $emp_id_old_ref,
                            'fullname' => $fullname,
                            'm_name' => $m_name,
                            'db_m_name' => $db_m_name,
                            'batch' => $batch,
                            'agency' => $agency,
                            'emp_status' => $emp_status
                        ];
                        // if ($fullname == $db_fullname && $batch == $db_batch && $emp_id_old == $emp_id_ref) {
                        // }
                    } else {
                        // Insert new employee if no match found
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

                foreach ($employeesToUpdate as $employee) {
                    // Call the updateEmployee function
                    $updateResult = updateEmployee($conn, $employee['id'], $employee['emp_id'], $employee['emp_id_old'], $employee['emp_id_ref'], $employee['emp_id_old_ref'], $employee['m_name'], $employee['db_m_name'], $employee['fullname'], $employee['batch'], $employee['agency'], $employee['emp_status']);
                    echo "<script>$updateResult</script>";
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
    } else {
        echo '<script>
         alert("Invalid file. Please, upload the correct file.");
         location.replace("../../page/' . $page . '/manpowerpage.php");
       </script>';
    }
}

function updateEmployee($conn, $id, $emp_id, $emp_id_old, $emp_id_ref, $emp_id_old_ref, $m_name, $db_m_name, $fullname, $batch, $agency, $emp_status)
{
    // Initialize the update query with emp_id and emp_old_id, which are always updated
    $updateQuery = "UPDATE t_employee_m SET emp_id = ?, emp_id_old = ?";
    $params = [$emp_id, $emp_id_old];

    // Update m_name if it's provided (not empty) and different from the existing value
    if (!empty($m_name) && $m_name != $db_m_name) {
        $updateQuery .= ", m_name = ?";
        $params[] = $m_name;
    }

    // Update fullname if it's provided (not empty)
    if (!empty($fullname)) {
        $updateQuery .= ", fullname = ?";
        $params[] = $fullname;
    }

    // Update batch if it's provided (not empty)
    if (!empty($batch)) {
        $updateQuery .= ", batch = ?";
        $params[] = $batch;
    }

    // Update agency if it's provided (not empty)
    if (!empty($agency)) {
        $updateQuery .= ", agency = ?";
        $params[] = $agency;
    }

    // Update emp_status if it's provided (not empty)
    if (!empty($emp_status)) {
        $updateQuery .= ", emp_status = ?";
        $params[] = $emp_status;
    }

    // Add the condition to update the record in the database
    $updateQuery .= " WHERE id = ?";
    $params[] = $id;

    // Execute the update query
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt->execute($params)) {
        return 'alert("Failed to update employee with ID ' . $emp_id . '");';
    }

    // Update related processes in t_f_process and t_i_process (only for emp_id and emp_old_id)
    $updateFQuery = "UPDATE t_f_process SET emp_id_old = ?, emp_id = ? WHERE emp_id = ?";
    $stmt = $conn->prepare($updateFQuery);
    if (!$stmt->execute([$emp_id_old, $emp_id, $emp_id_ref])) {
        return 'alert("Failed to update Final process for employee ID ' . $emp_id . '");';
    }

    $updateIQuery = "UPDATE t_i_process SET emp_id_old = ?, emp_id = ? WHERE emp_id = ?";
    $stmt = $conn->prepare($updateIQuery);
    if (!$stmt->execute([$emp_id_old, $emp_id, $emp_id_ref])) {
        return 'alert("Failed to update Initial process for employee ID ' . $emp_id . '");';
    }

    //return 'alert("Employee with ID ' . $emp_id . ' successfully updated.");';
}
