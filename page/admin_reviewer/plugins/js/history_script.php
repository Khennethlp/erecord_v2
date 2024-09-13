<script type="text/javascript" >

document.querySelectorAll('#fullname_h, #emp_id_h').forEach(input => {
  input.addEventListener("keyup", e => {
    if (e.which === 13) {
      search_history(1);
    }
  });
});

$(document).ready(function(){
  $("#categoryyy").change(function() {
      var category = document.getElementById("categoryyy").value;
      $.ajax({
        url: '../../process/can_request/history.php',
        type: 'POST',
        cache: false,
        data: {
          method: 'fetch_pro',
          category: category
        },
        success: function(response) {
          $('#processName_h').html(response);
        }
      });
    });
})

document.getElementById("history_list_pagination2").addEventListener("keyup", e => {
  var current_page = document.getElementById("history_list_pagination2").value.trim();
  let total = sessionStorage.getItem('count_rows2');
  var last_page = parseInt(sessionStorage.getItem('last_page'));
  if (e.which === 13) {
    e.preventDefault();
    console.log(total);
    if (current_page != 0 && current_page <= last_page && total > 0) {
      search_history(current_page);
    }
  }
});

const get_prev_page2 = () => {
    var current_page = parseInt(sessionStorage.getItem('history_list_pagination2'));
    let total = sessionStorage.getItem('count_rows2');
    var prev_page = current_page - 1;
    if (prev_page > 0 && total > 0) {
        search_history(prev_page);
    }
}

const get_next_page2 = () => {
    var current_page = parseInt(sessionStorage.getItem('history_list_pagination2'));
    let total = sessionStorage.getItem('count_rows2');
    var last_page = parseInt(sessionStorage.getItem('last_page'));
    var next_page = current_page + 1;
    if (next_page <= last_page && total > 0) {
        search_history(next_page);
    }
}

 const search_history =  current_page=>{
  var emp_id = document.getElementById('emp_id_h').value;
  var fullname = document.getElementById('fullname_h').value;
  var category = document.getElementById('categoryyy').value;
  var date_authorized = document.getElementById('date_authorized_h').value;
  var expire_date = document.getElementById('expire_date_h').value;
  var processName = document.getElementById('processName_h').value;

   if (category == 'Category') {
    category = '';
  }
  $.ajax({
    url:'../../process/can_request/history.php',
    type:'POST',
    cache:false,
    data:{
    method:'history_admin_reviwer',
    emp_id:emp_id,
    fullname:fullname,
    category:category,
    date_authorized:date_authorized,
    expire_date:expire_date,
    processName:processName,
    current_page:current_page 
    
    },success:function(response){
      $('#history_list').html(response);
      sessionStorage.setItem('history_list_pagination2', current_page);
      count_history();
    }
  });
}

 const count_history = ()=>{
  var emp_id = document.getElementById('emp_id_h').value;
  var fullname = document.getElementById('fullname_h').value;
  var category = document.getElementById('categoryyy').value;
  var date_authorized = document.getElementById('date_authorized_h').value;
  var expire_date = document.getElementById('expire_date_h').value;
  var processName = document.getElementById('processName_h').value;

   if (category == 'Category') {
    category = '';
  }
  $.ajax({
    url:'../../process/can_request/history.php',
    type:'POST',
    cache:false,
    data:{
    method:'count_history_admin_reviwer',
    emp_id:emp_id,
    fullname:fullname,
    date_authorized:date_authorized,
    expire_date:expire_date,
    processName:processName,
    category:category
    
    },success:function(response){
            sessionStorage.setItem('count_rows2', response);
            var count = `Total: ${response}`;
            $('#count_rows_display2').html(count);

            if (response > 0) {
              history_pagination();
              document.getElementById('btnPrevPage2').disabled = false;
              document.getElementById('btnNextPage2').disabled = false;
              document.getElementById('history_list_pagination2').disabled = false;
            } else {
              document.getElementById('btnPrevPage2').disabled = true;
              document.getElementById('btnNextPage2').disabled = true;
              document.getElementById('history_list_pagination2').disabled = true;
            }
        }
    });
}
const history_pagination  = ()=>{
  var emp_id = document.getElementById('emp_id_h').value;
  var fullname = document.getElementById('fullname_h').value;
  var category = document.getElementById('categoryyy').value;
  var date_authorized = document.getElementById('date_authorized_h').value;
  var expire_date = document.getElementById('expire_date_h').value;
  var processName = document.getElementById('processName_h').value;
  var current_page = sessionStorage.getItem('history_list_pagination2');
 
   if (category == 'Category') {
    category = '';
  }
  $.ajax({
    url:'../../process/can_request/history.php',
    type:'POST',
    cache:false,
    data:{
    method:'history_pagination_admin_r',
    emp_id:emp_id,
    fullname:fullname,
    date_authorized:date_authorized,
    expire_date:expire_date,
    processName:processName,
    category:category
    
    },success:function(response){
            $('#history_list_paginations2').html(response);
            $('#history_list_pagination2').val(current_page);
            sessionStorage.setItem('history_list_pagination2', current_page);
            let last_page_check = document.getElementById("history_list_paginations2").innerHTML;
            if (last_page_check != '') {
                let last_page = document.getElementById("history_list_paginations2").lastChild.text;
                sessionStorage.setItem('last_page', last_page);
            }
        }
    });
}

</script>