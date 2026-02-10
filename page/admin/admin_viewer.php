<?php include 'plugins/navbar.php'; ?>
<?php include 'plugins/sidebar/sidebar.php'; ?>
<?php include '../../process/conn.php'; ?>

<style>
    th {
        white-space: nowrap;
    }
    td {
        white-space: nowrap;
    }
</style>
<div class="content-wrapper" style="background: #FFF;">
    <section class="content">
        <h3>Admin Viewer</h3>
        <div class="col-md-12">
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="">Category</label>
                    <!-- <input type="text" class="form-control" placeholder="Search by Emp ID"> -->
                    <select name="" id="category" class="form-control" onchange="load_data()">
                        <option value=""></option>
                        <option value="Initial">Initial</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">&nbsp;</label>
                    <input type="text" id="searchData" class="form-control" placeholder="Search by Emp ID">
                </div>
                <div class="col-md-2">
                    <label for="">&nbsp;</label>
                    <button class="form-control btn-dark" id="searchDataBtn" onclick="load_data()">Search</button>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12" style="width: auto; height: 600px; overflow:auto;">
                    <table class="table table-striped table-hover">
                        <thead class="sticky-top bg-dark text-white">
                            <th>#</th>
                            <th>Emp ID</th>
                            <th>Emp ID Old</th>
                            <th>Process</th>
                            <th>Auth NO</th>
                            <th>Auth Year</th>
                            <th>Date Authorized</th>
                            <th>Expire Date</th>
                            <th>Remarks</th>
                            <th>Reason of Cancellation</th>
                            <th>Date of Cancellation</th>
                            <th>Updated By Date & Time</th>
                            <th>Updated By</th>
                            <th>Record Status</th>
                            <th>Record Review By</th>
                            <th>Record Approve By</th>
                            <th>Initial Status</th>
                            <th>Initial Review By</th>
                            <th>Initial Approve By</th>
                            <th>Dept</th>
                            <th>Batch</th>
                            <th>Code</th>
                            <th>Status</th>
                        </thead>
                        <tbody id="admin_viewer_table"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>

<?php
include 'plugins/footer.php';
include 'plugins/js/admin_viewer_script.php';
?>