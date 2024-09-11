<?php include 'plugins/navbar.php'; ?>
<?php include 'plugins/sidebar/sidebar.php'; ?>
<?php include '../../process/conn.php'; ?>
<div class="content-wrapper" style="background: #FFF;">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">List of Request</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="list_of_req.php">E-Record System</a></li>
            <li class="breadcrumb-item active">List of Request</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->


  <!-- Main content -->
  <section class="content">
    <div class="col-md-12">
      <div class="card card-light" style="background: #fff; border-top: 2px solid #1e96fc;">
        <div class="card-header">
          <h3 class="card-title"><img src="../../dist/img/files.png" style="height:28px;">&ensp;List of Request Table</h3>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-light card-tabs" style="background: #fcfcfc">
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="record-tab" role="tablist">
                  <!-- nav link request -->
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Request</a>
                  </li>
                  <!-- nav link history-->
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">History</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <!-- first tab -->
                  <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                    <!-- main content -->
                    <div class="row ">
                      <div class="col-sm-1">
                        <!-- approve button -->
                        <label for="">&nbsp;</label>
                        <button class="btn btn-block form-control  d-flex justify-content-left" data-toggle="modal" data-target="#i_approve" style="color:#fff;height:34px;border-radius:.25rem;background: #28a745;font-size:15px;font-weight:normal;"><img src="../../dist/img/check (1).png" style="height:19px;">&nbsp;&nbsp;Approve</button>
                      </div>
                      <div class="col-sm-1"></div>
                      <!-- category -->
                      <div class="col-sm-2">
                        <label for="">Category:</label>
                        <select class="form-control btn bg-info" recquired name="category" id="categoryy">
                          <option value="">Select Category</option>
                          <option>Initial</option>
                          <option>Final</option>
                        </select>
                      </div>
                      <div class="col-sm-2">
                        <label for="">Process Name:</label>
                        <select class=" form-control  " name="processName" id="processName">
                          <option value="">Select Process</option>
                        </select>
                      </div>
                      <div class="col-sm-2">
                        <label for="">Date Authorized:</label>
                        <input class="form-control" type="date" id="date_authorized">
                      </div>
                      <div class="col-sm-2">
                        <label for="">Employee ID:</label>
                        <input class="form-control" placeholder="Type here..." type="text" id="emp_id_r">
                      </div>
                      <div class="col-sm-2">
                        <label for="">Employee Name:</label>
                        <input class="form-control" placeholder="Type here..." type="text" id="fullname_r">
                      </div>
                      <div class="col-sm-2 ml-auto">
                        <!-- search button -->
                        <label for="">&nbsp;</label>
                        <button class="btn btn-block d-flex justify-content-left" id="search_btn" onclick="search_rev(1)" style="color:#fff;height:37px;border-radius:.25rem;background: var(--info);font-size:15px;font-weight:normal;"><img src="../../dist/img/search.png" style="height:19px;">&nbsp;&nbsp;Search</button>
                      </div>
                    </div>
                    <br>

                    <div class="col-12">
                      <div class="card-body table-responsive p-0" style="height: 600px;">
                        <table class="table table-head-fixed text-nowrap" id="employee_data">
                          <thead>
                            <tr>
                              <th style="text-align:center;">
                                <input type="checkbox" name="" id="check_all_for_auth" onclick="select_all_func()">
                              </th>
                              <th>#</th>
                              <th>Process Name</th>
                              <th>Authorization&nbsp;No.</th>
                              <th>Employee&nbsp;Name</th>
                              <th>Employee No</th>
                              <th>Authorization Year</th>
                              <th>Date Authorized</th>
                              <th>Expire&nbsp;Date</th>
                              <th>Reason Of Cancellation</th>
                              <th>Date of Cancellation</th>
                              <th>Prepared By/ Date/ Time</th>
                              <th>Review By/ Date/ Time</th>
                              <th>Department</th>
                              <th>Status</th>
                              <th>Remarks</th>
                            </tr>
                          </thead>
                          <tbody id="rev_list">
                          </tbody>
                        </table>
                      </div>
                      <div class="row mt-3">
                        <div class="col-sm-12 col-md-9 col-9">
                          <div class="dataTables_info" id="count_rows_display1" role="status1" aria-live="polite"></div>
                          <input type="hidden" id="count_rows1">
                        </div>
                        <div class="col-sm-12 col-md-1 col-1">
                          <button type="button" id="btnPrevPage1" class="btn bg-gray-dark btn-block" onclick="get_prev_page1()">Prev</button>
                        </div>
                        <div class="col-sm-12 col-md-1 col-1">
                          <input list="rev_list_paginations1" class="form-control" id="rev_list_pagination1" maxlength="255">
                          <datalist id="rev_list_paginations1"></datalist>
                        </div>
                        <div class="col-sm-12 col-md-1 col-1">
                          <button type="button" id="btnNextPage1" class="btn bg-gray-dark btn-block" onclick="get_next_page1()">Next</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                    <div class="row">
                      <!-- <div class="col-sm-1"></div> -->
                      <!-- category -->
                      <div class="col-sm-2 mt-2">
                        <label for="">Category:</label>
                        <select class="form-control btn bg-info" rname="category" id="categoryyy" required>
                          <option value="">Select Category</option>
                          <option>Initial</option>
                          <option>Final</option>
                        </select>
                      </div>
                      <div class="col-sm-2 mt-2">
                        <label for="">Process Name:</label>
                        <select class=" form-control" name="processName" id="processName_h">
                          <option value="">Select Process </option>
                        </select>
                      </div>
                      <div class="col-sm-2 mt-2">
                        <label for="">Employee ID:</label>
                        <input class="form-control" placeholder="Type here..." type="text" id="emp_id_h" autocomplete="off">
                      </div>
                      <div class="col-sm-2 mt-2">
                        <label for="">Employee Name:</label>
                        <input class="form-control" placeholder="Type here..." type="text" id="fullname_h" autocomplete="off">
                      </div>
                      <div class="col-sm-2 mt-2">
                        <label for="">Date Authorized:</label>
                        <input class="form-control" type="text" placeholder="Date Authorized" onfocus="(this.type='date')" onblur="(this.type='text')" id="date_authorized_h">
                      </div>
                      <div class="col-sm-2 mt-2">
                        <label for="">Expire Date:</label>
                        <input class="form-control" type="text" placeholder="Expire Date" onfocus="(this.type='date')" onblur="(this.type='text')" id="expire_date_h">
                      </div>
                      <div class="col-sm-2 ml-auto mt-2">
                        <label for="">Date From:</label>
                        <input class="form-control" type="date" placeholder="Date From" value="<?= $date_month_before ?>" id="approved_date_f">
                      </div>
                      <div class="col-sm-2 mt-2">
                        <label for="">Date To:</label>
                        <input class="form-control" type="date" placeholder="Date To" value="<?= $server_date_only ?>" id="approved_date_t">
                      </div>
                      <div class="col-sm-2 mt-2">
                        <!-- search button -->
                        <label for="">&nbsp;</label>
                        <button class="btn btn-block d-flex justify-content-center" id="search_btn" onclick="search_history(1)" style="color:#fff;height:37px;border-radius:.25rem;background: var(--info);font-size:15px;font-weight:normal;"><img src="../../dist/img/search.png" style="height:19px;">&nbsp;&nbsp;Search</button>
                      </div>
                    </div>
                    <br>
                    <div class="col-12">
                      <div class="card-body table-responsive p-0" style="height: 600px;">
                        <table class="table table-head-fixed text-nowrap" id="employee_data">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Process Name</th>
                              <th>Authorization&nbsp;No.</th>
                              <th>Employee&nbsp;Name</th>
                              <th>Employee No</th>
                              <th>Authorization Year</th>
                              <th>Date Authorized</th>
                              <th>Expire&nbsp;Date</th>
                              <th>Reason Of Cancellation</th>
                              <th>Date of Cancellation</th>
                              <th>Prepared By/ Date/ Time</th>
                              <th>Review By/ Date/ Time</th>
                              <th>Approved By/ Date/ Time</th>
                              <th>Department</th>
                              <th>Status</th>
                              <th>Remarks</th>
                            </tr>
                          </thead>
                          <tbody id="history_list">
                          </tbody>
                        </table>
                      </div>
                      <div class="row mt-3">
                        <div class="col-sm-12 col-md-9 col-9">
                          <div class="dataTables_info" id="count_rows_display2" role="status" aria-live="polite"></div>
                          <input type="hidden" id="count_rows2">
                        </div>
                        <div class="col-sm-12 col-md-1 col-1">
                          <button type="button" id="btnPrevPage2" class="btn bg-gray-dark btn-block" onclick="get_prev_page2()">Prev</button>
                        </div>
                        <div class="col-sm-12 col-md-1 col-1">
                          <input list="history_list_paginations2" class="form-control" id="history_list_pagination2" maxlength="255">
                          <datalist id="history_list_paginations2"></datalist>
                        </div>
                        <div class="col-sm-12 col-md-1 col-1">
                          <button type="button" id="btnNextPage2" class="btn bg-gray-dark btn-block" onclick="get_next_page2()">Next</button>
                        </div>
                      </div>
                    </div>
                  </div>
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
<!-- /.content-wrapper -->
<?php include 'plugins/footer.php'; ?>
<?php include 'plugins/js/list_of_req_script.php'; ?>