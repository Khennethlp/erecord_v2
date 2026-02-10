<?php
include '../conn.php';

$method = $_POST['method'] ?? '';

if ($method === 'fetch_records') {
    $category = $_POST['category'] ?? '';
    $searchData = $_POST['searchData'] ?? '';

    if (!empty($category)) {
        $sql = "SELECT *";

        if ($category === 'Final') {
            $sql .= " FROM t_f_process";
        } elseif ($category === 'Initial') {
            $sql .= " FROM t_i_process";
        } else {
            die("Invalid category.");
        }

        if (!empty($searchData)) {
            $sql .= " WHERE (emp_id = '$searchData' OR emp_id_old = '$searchData')";
        }

        $sql .= " ORDER BY process ASC OFFSET 0 ROWS FETCH NEXT 100 ROWS ONLY";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $c = 0;
            foreach ($result as $i) {
                $c++;
                echo "<tr onclick='getViewerData(\" {$i['id']}&{$i['emp_id']}&{$i['emp_id_old']}&{$i['process']}&{$i['auth_no']}&{$i['auth_year']}&{$i['date_authorized']}&{$i['expire_date']}&{$i['remarks']}&{$i['r_of_cancellation']}&{$i['d_of_cancellation']}&{$i['up_date_time']}&{$i['updated_by']}&{$i['r_status']}&{$i['r_review_by']}&{$i['r_approve_by']}&{$i['i_status']}&{$i['i_review_by']}&{$i['i_approve_by']}&{$i['dept']}&{$i['batch']}&{$i['status']} \");' style='cursor: pointer;' data-toggle='modal' data-target='#admin_viewer'>
                        <td>{$c}</td>
                        <td>{$i['emp_id']}</td>
                        <td>{$i['emp_id_old']}</td>
                        <td>{$i['process']}</td>
                        <td>{$i['auth_no']}</td>
                        <td>{$i['auth_year']}</td>
                        <td>{$i['date_authorized']}</td>
                        <td>{$i['expire_date']}</td>
                        <td>{$i['remarks']}</td>
                        <td>{$i['r_of_cancellation']}</td>
                        <td>{$i['d_of_cancellation']}</td>
                        <td>{$i['up_date_time']}</td>
                        <td>{$i['updated_by']}</td>
                        <td>{$i['r_status']}</td>
                        <td>{$i['r_review_by']}</td>
                        <td>{$i['r_approve_by']}</td>
                        <td>{$i['i_status']}</td>
                        <td>{$i['i_review_by']}</td>
                        <td>{$i['i_approve_by']}</td>
                        <td>{$i['dept']}</td>
                        <td>{$i['batch']}</td>
                        <td>{$i['code']}</td>
                        <td>{$i['status']}</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found.</td></tr>";
        }
    } else {
        echo "<tr><td colspan='14' style='text-align:center;'>Category is required.</td></tr>";
    }
}
