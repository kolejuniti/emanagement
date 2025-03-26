@extends('layouts.hostel')

@section('main')
<style>
    a[data-toggle="modal"][data-target="#uploadModal"]:hover {
    color: blue;
}

</style>
<!-- Content Header (Page header) -->
<div class="content-wrapper" style="min-height: 695.8px;">
  <div class="container-full">
  <!-- Content Header (Page header) -->	  
  <div class="content-header">
    <div class="d-flex align-items-center">
      <div class="me-auto">
        <h4 class="page-title">Hostel Checkout</h4>
        <div class="d-inline-block align-items-center">
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
              <li class="breadcrumb-item" aria-current="page">Dashboard</li>
              <li class="breadcrumb-item active" aria-current="page">Hostel Checkout</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div id="printableArea">
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Hostel Checkout</h3>
              </div>
              <!-- /.card-header -->
              <div class="card mb-3">
                <div class="card-body">
                    <div class="card mb-3" id="stud_info">
                        <div class="card-header">
                        <b>Hostel Checkout</b>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label class="form-label" for="name">Name / No. IC / No. Matric</label>
                                    <input type="text" class="form-control" id="search" placeholder="Search..." name="search">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                      <label class="form-label" for="student">Student</label>
                                      <select class="form-select" id="student" name="student">
                                        <option value="-" selected disabled>-</option>
                                      </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3" id="stud_info">
                                <div class="card-header">
                                <b>Student Information</b>
                                </div>
                                <div class="card-body">
                                    <div id="student-info"></div>
                                </div>
                            </div>
                            <div>
                                <input type="text" id="idS" hidden>
                            </div>
                            {{-- <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <p>Student Name &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; {{ $data['student']->name }}</p>
                                    </div>
                                    <div class="form-group">
                                        <p>No. IC / No. Passport &nbsp; &nbsp;: &nbsp;&nbsp; {{ $data['student']->ic }}</p>
                                    </div>
                                    <div class="form-group">
                                        <p>Program &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; {{ $data['student']->program }}</p>
                                    </div>
                                    <div class="form-group">
                                        <p>No. Matric &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; {{ $data['student']->no_matric }}</p>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    <div class="row mb-3 d-flex">
                        <div class="col-md-12 mb-3">
                          <div class="pull-right">
                              <a type="button" class="waves-effect waves-light btn btn-info btn-sm" onclick="checkoutStudent()">
                                CHECKOUT
                              </a>
                              <a type="button" class="waves-effect waves-light btn btn-success btn-sm" onclick="openPaymentModal()" id="paymentBtn" style="display:none;">
                                <i class="ti-money"></i> PAYMENT
                              </a>
                              <a type="button" class="waves-effect waves-light btn btn-primary btn-sm" onclick="printStudentSlip()" id="printBtn" style="display:none;">
                                <i class="ti-printer"></i> PRINT SLIP
                              </a>
                          </div>
                        </div>
                    </div>

                    <div id="student-table">
                        <table id="myTable" class="table table-striped projects display dataTable">
                            <thead>
                                <tr>
                                    <th style="width: 1%">
                                        #
                                    </th>
                                    <th style="width: 10%">
                                        Name
                                    </th>
                                    <th style="width: 10%">
                                        Room Unit
                                    </th>
                                    <th style="width: 10%">
                                        Room No.
                                    </th>
                                    <th style="width: 10%">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table">
                            
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div>
    </section>
  </div>
    <!-- /.content -->
  </div>
</div>

<script src="{{ asset('assets/assets/vendor_components/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/31.0.0/classic/ckeditor.js"></script>
<script type="text/javascript">

$('#search').keyup(function(event){
    if (event.keyCode === 13) { // 13 is the code for the "Enter" key
        var searchTerm = $(this).val();
        getStudent(searchTerm);
    }
});

$('#student').on('change', function(){
    var selectedStudent = $(this).val();
    getStudInfo(selectedStudent);
});

function getStudent(search)
{

    return $.ajax({
            headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
            url      : "{{ url('hostel/getStudentList') }}",
            method   : 'POST',
            data 	 : {search: search},
            error:function(err){
                alert("Error");
                console.log(err);
            },
            success  : function(data){
                $('#student').html(data);
                $('#student').selectpicker('refresh');

            }
        });
    
}

function getStudInfo(student)
{
    var id = "{{ request()->id }}";

    return $.ajax({
            headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
            url      : "/hostel/student/checkout/getStudentInfo2",
            method   : 'POST',
            data 	 : {student: student},
            error:function(err){
                alert("Error");
                console.log(err);
            },
            success  : function(response){
                var data = response.data;

                var info = response.info;

                var students = response.history;

                $('#idS').val(info.id);
                
                // Show the print button
                $('#printBtn').show();
                
                // Show the payment button
                $('#paymentBtn').show();

                // Construct the HTML content dynamically
                var newContent = "<div class='row mb-5'>" +
                    "<div class='col-md-6'>" +
                    "<div class='form-group'>" +
                    "<p>Student Name &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.name + "</p>" +
                    "</div>" +
                    "<div class='form-group'>" +
                    "<p>Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.statusName + "</p>" +
                    "</div>" +
                    "<div class='form-group'>" +
                    "<p>Program &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.program + "</p>" +
                    "</div>" +
                    "<div class='form-group'>" +
                    "<p>Current Session &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.session_name + "</p>" +
                    "</div>" +
                    "<div class='form-group'>" +
                    "<p>Current Room &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + info.name + " - " + info.location  + "</p>" +
                    "</div>" +
                    "<div class='form-group'>" +
                    "<p>No. Unit &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + info.no_unit + "</p>" +
                    "</div>" +
                    "</div>" +
                    "<div class='col-md-6'>" +
                    "<div class='form-group'>" +
                    "<p>No. Matric &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.no_matric + "</p>" +
                    "</div>" +
                    "<div class='form-group'>" +
                    "<p>Semester &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.semester + "</p>" +
                    "</div>" +
                    "</div>" +
                    "</div>";

                // Assuming you have a div with an id of 'student-info' to insert this content
                $('#student-info').html(newContent);

                var tbody = $('#table');
                tbody.empty(); // Clear the table body

                $.each(students, function(index, student) {
                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + student.name + '</td>' +
                        '<td>' + student.block + '-' + student.location + '</td>' +
                        '<td>' + student.no_unit + '</td>' +
                        '<td>' + student.status + '</td>' +
                    '</tr>';
                    tbody.append(row);
                });

            }
        });
}

function checkoutStudent(){

    var formData = new FormData();

    getInput = {
      id : $('#idS').val(),
      student : $('#student').val(),
    };
    
    formData.append('storeStudent', JSON.stringify(getInput));

    $.ajax({
        headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
        url: "/hostel/student/checkout/checkoutStudent",
        type: 'POST',
        data: formData,
        cache : false,
        processData: false,
        contentType: false,
        error:function(err){
            console.log(err);
        },
        success:function(res){
            try{
                if(res.message == "Success"){
                    alert("Success! Student has been registered!");
                    
                    var data = res.data;

                    var info = res.info;

                    var students = res.history;
                    
                    // Ensure print button remains visible
                    $('#printBtn').show();
                    
                    // Ensure payment button remains visible
                    $('#paymentBtn').show();

                    // Construct the HTML content dynamically
                    var newContent = "<div class='row mb-5'>" +
                        "<div class='col-md-6'>" +
                        "<div class='form-group'>" +
                        "<p>Student Name &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.name + "</p>" +
                        "</div>" +
                        "<div class='form-group'>" +
                        "<p>Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.statusName + "</p>" +
                        "</div>" +
                        "<div class='form-group'>" +
                        "<p>Program &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.program + "</p>" +
                        "</div>" +
                        "<div class='form-group'>" +
                        "<p>Current Session &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.session_name + "</p>" +
                        "</div>" +
                        "<div class='form-group'>" +
                        "<p>Current Room &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + info.name + " - " + info.location  + "</p>" +
                        "</div>" +
                        "<div class='form-group'>" +
                        "<p>No. Unit &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + info.no_unit + "</p>" +
                        "</div>" +
                        "</div>" +
                        "<div class='col-md-6'>" +
                        "<div class='form-group'>" +
                        "<p>No. Matric &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.no_matric + "</p>" +
                        "</div>" +
                        "<div class='form-group'>" +
                        "<p>Semester &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; " + data.semester + "</p>" +
                        "</div>" +
                        "</div>" +
                        "</div>";

                    // Assuming you have a div with an id of 'student-info' to insert this content
                    $('#student-info').html(newContent);

                    var tbody = $('#table');
                    tbody.empty(); // Clear the table body

                    $.each(students, function(index, student) {
                        var row = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + student.name + '</td>' +
                            '<td>' + student.block + '-' + student.location + '</td>' +
                            '<td>' + student.no_unit + '</td>' +
                            '<td>' + student.status + '</td>' +
                        '</tr>';
                        tbody.append(row);
                    });

                }else{
                    $('.error-field').html('');
                    if(res.message == "Field Error"){
                        for (f in res.error) {
                            $('#'+f+'_error').html(res.error[f]);
                        }
                    }
                    else if(res.message == "Group code already existed inside the system"){
                        $('#classcode_error').html(res.message);
                    }
                    else{
                        alert(res.message);
                    }
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                }
            }catch(err){
                alert("Ops sorry, there is an error");
            }
        }
    });

}

function deleteStudent(ids)
{

  var id = "{{ request()->id }}";

  Swal.fire({
    title: "Are you sure?",
    text: "This will be permanent",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it!"
  }).then(function(res){
    
    if (res.isConfirmed){
              $.ajax({
                  headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
                  url    : "/hostel/register/" + id + "/deleteStudent",
                  method   : 'POST',
                  data 	 : {ids: ids},
                  error:function(err){
                      alert("Error");
                      console.log(err);
                  },
                  success  : function(res){
                    try{
                        if(res.message == "Success"){
                            alert("Success! Student has been registered!");
                            
                            // Assuming 'res.data' is an array containing student data
                            var newTable = "<table id='myTable' class='table table-striped projects display dataTable' style='width: 100%;'>" +
                                "<thead>" +
                                "<tr>" +
                                "<th style='width: 1%'>" +
                                "#" +
                                "</th>" +
                                "<th style='width: 10%'>" +
                                "Name" +
                                "</th>" +
                                "<th style='width: 10%'>" +
                                "Register Date" +
                                "</th>" +
                                "<th style='width: 10%'>" +
                                "Status" +
                                "</th>" +
                                "<th style='width: 5%'>" +
                                "</th>" +
                                "</tr>" +
                                "</thead>" +
                                "<tbody>";

                            // Loop through each student data item
                            $.each(res.data, function(i, item) {
                                var newRow = "<tr>" +
                                    "<td>" + (i + 1) + "</td>" +
                                    "<td>" + item.name + "</td>" +
                                    "<td>" + item.entry_date + "</td>" +
                                    "<td>" + item.status + "</td>" +
                                    "<td class='project-actions text-right' style='text-align: center;'>" +
                                    "<a class='btn btn-danger btn-sm' href='#' onclick='deleteStudent(\"" + item.id + "\")'>" +
                                    "<i class='ti-trash'></i> Delete" +
                                    "</a>" +
                                    "</td>" +
                                    "</tr>";
                                newTable += newRow;
                            });

                            // Close table structure
                            newTable += "</tbody>" +
                                "</table>";

                            // Replace the contents of a div with id 'add-student-div' with the new table
                            $('#student-table').html(newTable);

                        }else{
                            $('.error-field').html('');
                            if(res.message == "Field Error"){
                                for (f in res.error) {
                                    $('#'+f+'_error').html(res.error[f]);
                                }
                            }
                            else if(res.message == "Group code already existed inside the system"){
                                $('#classcode_error').html(res.message);
                            }
                            else{
                                alert(res.message);
                            }
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                        }
                    }catch(err){
                        alert("Ops sorry, there is an error");
                    }
                  }
              });
          }
      });

}

function printStudentSlip() {
    var studentId = $('#student').val();
    var idS = $('#idS').val();
    
    if (studentId && idS) {
        window.open('/hostel/student/printStudentSlip/' + studentId, '_blank');
    } else {
        alert('Please select a student first');
    }
}

function openPaymentModal() {
    var studentId = $('#student').val();
    
    if (!studentId) {
        alert('Please select a student first');
        return;
    }
    
    // Fetch the student's payment information from the quotationStudentSlip endpoint
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: "/hostel/student/quotationStudentSlip/" + studentId,
        method: 'GET',
        error: function(err) {
            console.log(err);
            alert("Error fetching payment information");
        },
        success: function(response) {
            if (response.data) {
                // Fill student information in the modal
                $('#payment-student-name').text(response.data.name);
                $('#payment-student-ic').text(response.data.ic);
                $('#payment-student-matric').text(response.data.no_matric);
                
                // Show the payment method and type from the response if available
                if (response.data.payment_method_id) {
                    $('#payment-method').text(response.data.payment_method_id);
                }
                
                if (response.data.claim_type_id) {
                    $('#payment-type').text(response.data.claim_type_id);
                }
                
                // Open the modal
                $('#paymentModal').modal('show');
            } else {
                alert("No student data found");
            }
        }
    });
}

function confirmPayment() {
    var studentId = $('#student').val();
    
    if (!studentId) {
        alert('Please select a student first');
        return;
    }
    
    // Log the request URL for debugging
    console.log("Request URL:", "/hostel/student/chargeStudentSlip/" + studentId);
    
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: "/hostel/student/chargeStudentSlip/" + studentId,
        type: 'GET',
        success: function(response) {
            console.log("Success response:", response);
            if (response.message === "Success") {
                alert("Success! Student has been charged!");
                
                // Check if id is returned for the receipt
                if (response.id) {
                    // Open the receipt in a new window
                    window.open('/hostel/student/receipt/' + response.id, '_blank');
                }
            } else {
                alert("Error charging student: " + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log("Error status:", status);
            console.log("Error thrown:", error);
            console.log("Response text:", xhr.responseText);
            console.log("Status code:", xhr.status);
            
            // Try to parse the JSON response if available
            try {
                var errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse && errorResponse.message) {
                    alert(errorResponse.message);
                } else {
                    alert("Error charging student. Status: " + xhr.status);
                }
            } catch (e) {
                // If parsing fails, just show the status code
                alert("Error charging student. Status: " + xhr.status);
            }
        }
    }).always(function() {
        // Close the modal
        $('#paymentModal').modal('hide');
    });
}

</script>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Student Information</h6>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td width="40%">Student Name</td>
                                                <td id="payment-student-name"></td>
                                            </tr>
                                            <tr>
                                                <td>IC / Passport</td>
                                                <td id="payment-student-ic"></td>
                                            </tr>
                                            <tr>
                                                <td>No. Matric</td>
                                                <td id="payment-student-matric"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <h6 class="card-title mt-4">Payment Details</h6>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td width="40%">Payment Method</td>
                                                <td id="payment-method">Cash</td>
                                            </tr>
                                            <tr>
                                                <td>Payment Type</td>
                                                <td id="payment-type">Hostel Checkout Fee</td>
                                            </tr>
                                            <tr>
                                                <td>Amount</td>
                                                <td>RM 5.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmPayment()">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>
@endsection
