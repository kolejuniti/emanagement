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
        <h4 class="page-title">Hostel Registration</h4>
        <div class="d-inline-block align-items-center">
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
              <li class="breadcrumb-item" aria-current="page">Dashboard</li>
              <li class="breadcrumb-item active" aria-current="page">Hostel Registration</li>
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
                <h3 class="card-title">Hostel Registration</h3>
              </div>
              <!-- /.card-header -->
              <div class="card mb-3">
                <div class="card-body">
                    <div class="card mb-3" id="stud_info">
                        <div class="card-header">
                        <b>Hostel Registration</b>
                        </div>
                        <div class="card-body">
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <p>Block &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; {{ $data['unit']->name }} - {{ $data['unit']->location }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <p>No. Unit &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp; {{ $data['unit']->no_unit }}</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
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
                              <a type="button" class="waves-effect waves-light btn btn-info btn-sm" onclick="registerStudent()">
                                REGISTER
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
                                        Register Date
                                    </th>
                                    <th style="width: 10%">
                                        Status
                                    </th>
                                    <th style="width: 5%">
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table">
                            @foreach ($data['student'] as $key=> $std)
                              <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <th>
                                    {{ $std->name }}
                                </td>
                                <td>
                                    {{ $std->entry_date }}
                                </td>
                                <td>
                                    {{ $std->status }}
                                </td>
                                <td class="project-actions text-right" style="text-align: center;">
                                  <a class="btn btn-danger btn-sm" href="#" onclick="deleteStudent('{{ $std->id }}')">
                                      <i class="ti-trash">
                                      </i>
                                      Delete
                                  </a>
                                </td>
                              </tr>
                            @endforeach
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
            url      : "/hostel/register/" + id + "/getStudentInfo",
            method   : 'POST',
            data 	 : {student: student},
            error:function(err){
                alert("Error");
                console.log(err);
            },
            success  : function(response){
                var data = response.data;

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


                }
            });
}

function registerStudent(){

    var id = "{{ request()->id }}";

    var formData = new FormData();

    getInput = {
      student : $('#student').val(),
      id : "{{ request()->id }}"
    };
    
    formData.append('storeStudent', JSON.stringify(getInput));

    $.ajax({
        headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
        url: "/hostel/register/" + id + "/registerStudent",
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

</script>
@endsection
