<?php include 'plugins/navbar.php'; ?>
<?php include 'plugins/sidebar/sidebar.php'; ?>

<div class="content-wrapper" style="background: #FFF;">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">View Data</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="dashboard.php">E-Record System</a></li>
            <li class="breadcrumb-item active">View Data</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  <section class="content">
    <div class="col-md-12">
      <div class="card card-light" style="background: #fff; border-top: 2px solid #6c757d;">
        <div class="card-header">
          <h3 class="card-title"><img src="../../dist/img/view-list.png" style="height:28px;">&ensp;View Data Table</h3>
        </div>

        <div class="card-body" style="overflow: auto;">
          <!-- Main content -->
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-2">
                <label for="">Category</label>
                <select class="form-control btn bg-info" required name="category" id="category">
                  <option value="">Select Category</option>
                  <option>Initial</option>
                  <option>Final</option>
                </select>
              </div>
              <div class="col-sm-2">
                <label for="">Process Name</label>
                <select class="form-control btn" name="pro" required id="pro" style="width: 100%; border: 2px solid black;background-color: white;color: black;font-size: 16px;cursor: pointer; border-color: var(--info);" >
                  <option>Please select a process.....</option>
                  <option></option>
                </select>
              </div>
              <div class="col-sm-2">
              <label for="">Employee ID</label>
                <input class="form-control" placeholder="Type here..." type="text" id="emp_id_search">
              </div>
              <div class="col-sm-2">
              <label for="">Employee Name</label>
                <input class="form-control" placeholder="Type here..." type="text" id="fullname_search">
              </div>
              <div class="col-sm-2">
              <label for="">Date Authorized</label>
                <input class="form-control" type="text" placeholder="Select date..." onfocus="(this.type='date')" onblur="(this.type='text')" id="date_authorized_search">
              </div>
              <div class="col-sm-2">
              <label for="">Expire Date</label>
                <input class="form-control" type="text" placeholder="Select date..." onfocus="(this.type='date')" onblur="(this.type='text')" id="expire_date_search">
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-sm-8">
              </div>
              <div class="col-sm-2">
                <!-- search button -->
                <button class="btn btn-block d-flex justify-content-left" id="search_btn" onclick="search_data(1)" style="color:#fff;height:34px;border-radius:.25rem;background: var(--info);font-size:15px;font-weight:normal;"><img src="../../dist/img/search.png" style="height:19px;">&nbsp;&nbsp;Search</button>
              </div>
              <div class="col-sm-2">
                <!-- exportt button -->
                <a class="btn btn-block d-flex justify-content-left" onclick="export_data()" style="color:#fff;height:34px;border-radius:.25rem;background: #F29A35;font-size:15px;font-weight:normal;"><img src="../../dist/img/export.png" style="height:19px;">&nbsp;&nbsp;Export</a>
              </div>
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col-12">
              <div class="card-body table-responsive p-0" style="height: 600px;">
                <table class="table table-head-fixed text-nowrap" id="employee_data">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Process Name</th>
                      <th>Authorization No.</th>
                      <th>Authorization Year</th>
                      <th>Date Authorized</th>
                      <th>Expire&nbsp;Date</th>
                      <th>Employee Name</th>
                      <th>Employee No.</th>
                      <th>Batch No.</th>
                      <th>Department</th>
                      <th>Remarks</th>
                      <th>Reason of Cancellation</th>
                      <th>Date of Cancellation</th>
                    </tr>
                  </thead>
                  <tbody id="process_details"></tbody>
                </table>
                <br>
              </div>
              <div class="row mt-3">
                <div class="col-sm-12 col-md-9 col-9">
                  <div class="dataTables_info" id="count_rows_display" role="status" aria-live="polite"></div>
                  <input type="hidden" id="count_rows">
                </div>
                <div class="col-sm-12 col-md-1 col-1">
                  <button type="button" id="btnPrevPage" class="btn bg-gray-dark btn-block" onclick="get_prev_page()">Prev</button>
                </div>
                <div class="col-sm-12 col-md-1 col-1">
                  <input list="process_details_paginations" class="form-control" id="process_details_pagination" maxlength="255">
                  <datalist id="process_details_paginations"></datalist>
                </div>
                <div class="col-sm-12 col-md-1 col-1">
                  <button type="button" id="btnNextPage" class="btn bg-gray-dark btn-block" onclick="get_next_page()">Next</button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
</section>

<!-- /.content -->
</div>


<?php include 'plugins/footer.php'; ?>
<?php include 'plugins/js/view_script.php'; ?>