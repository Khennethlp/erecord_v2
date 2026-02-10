<script>
    document.querySelectorAll('#searchData').forEach(input => {
        input.addEventListener("keyup", e => {
            if (e.which === 13) {
                load_data();
            }
        });
    });

    const load_data = () => {
        const category = document.getElementById('category').value;
        const searchData = document.getElementById('searchData').value;

        $.ajax({
            type: "POST",
            url: "../../process/viewer/admin_viewer.php",
            data: {
                method: 'fetch_records',
                category: category,
                searchData: searchData
            },
            success: function(response) {
                $('#admin_viewer_table').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }
    load_data();

    const getViewerData = (param) => {
        const data = param.split('&');
        
        const recordId = data[0].trim();
        const empId = data[1].trim();
        const empIdOld = data[2].trim();
        const process = data[3].trim();
        const authNo = data[4].trim();
        const authYear = data[5].trim();
        const dateAuthorized = data[6].trim();
        const expireDate = data[7].trim();
        const remarks = data[8].trim();
        const reasonOfCancellation = data[9].trim();
        const dateOfCancellation = data[10].trim();
        const updatedDateTime = data[11].trim();
        const updatedBy = data[12].trim();
        const recordStatus = data[13].trim();
        const recordReviewBy = data[14].trim();
        const recordApproveBy = data[15].trim();
        const initialStatus = data[16].trim();
        const initialReviewBy = data[17].trim();
        const initialApproveBy = data[18].trim();
        const dept = data[19].trim();
        const batch = data[20].trim();
        const status = data[21].trim();

        console.log(data);

        $('#av_dataID').val(recordId);
        $('#av_empID').val(empId);
        $('#av_empIDOld').val(empIdOld);
        $('#av_process').val(process);
        $('#av_authNo').val(authNo);
        $('#av_authYear').val(authYear);
        $('#av_dateAuthorized').val(dateAuthorized);
        $('#av_expireDate').val(expireDate);
        $('#av_remarks').val(remarks);
        $('#av_rOfCancellation').val(reasonOfCancellation);
        $('#av_dOfCancellation').val(dateOfCancellation);
        $('#av_upDateTime').val(updatedDateTime);
        $('#av_updatedBy').val(updatedBy);
        $('#av_rStatus').val(recordStatus);
        $('#av_rReviewBy').val(recordReviewBy);
        $('#av_rApproveBy').val(recordApproveBy);
        $('#av_iStatus').val(initialStatus);
        $('#av_iReviewBy').val(initialReviewBy);
        $('#av_iApproveBy').val(initialApproveBy);
        $('#av_dept').val(dept);
        $('#av_batch').val(batch);
        $('#av_status').val(status);

    }

    const postDataModal = () => {
        const dataID = document.getElementById('av_dataID').value;
        const empID = document.getElementById('av_empID').value;

        $.ajax({
            type: "POST",
            url: "../../process/viewer/admin_viewer.php",
            data: {
                method: 'fetch_data',
                dataID: dataID,
                empID: empID
            },
            success: function(response) {
                $('#details').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    }
</script>