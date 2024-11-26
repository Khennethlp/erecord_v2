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

                    $insertQuery = "INSERT INTO t_employee_m (fullname, m_name, emp_id, agency, batch, emp_status) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insertQuery);
                    if (!$stmt->execute([$fullname, $m_name, $emp_id, $agency, $batch, $emp_status])) {
                        $error++;
                        $errorMessages[] = "Failed to insert new employee with ID $emp_id";
                        continue;
                    }
                }

                fclose($csvFile);

                // Now, handle updates for existing employees
                // foreach ($employeesToUpdate as $employee) {
                //     echo '<script>
                //         var updateConfirmed = confirm("Employee with ID ' . $employee['emp_id'] . ' already exists. Do you want to update the record?");
                //         if (updateConfirmed) {
                //             ' . updateEmployee($conn, $employee['id'], $employee['fullname'], $employee['m_name'], $employee['emp_id'], $employee['emp_id_old'], $employee['batch'], $employee['agency'], $employee['emp_status'], $employee['emp_id_ref'], $employee['emp_id_old_ref']) . '
                //         }
                //     </script>';
                // }

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

