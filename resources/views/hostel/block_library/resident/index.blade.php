@extends('layouts.hostel')

@section('main')
<!-- Content Header (Page header) -->
<div class="content-wrapper" style="min-height: 695.8px;">
  <div class="container-full">
    <!-- Content Header (Page header) -->	  
    <div class="content-header">
      <div class="d-flex align-items-center">
        <div class="me-auto">
          <h4 class="page-title">Resident</h4>
          <div class="d-inline-block align-items-center">
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                <li class="breadcrumb-item" aria-current="page">Resident</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
      @if($errors->any())
      <a class="btn btn-danger btn-sm ml-2 ">
        <i class="ti-na">
        </i>
        {{$errors->first()}}
      </a>
      @endif
    </div>
  

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Add Resident</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6" id="resident-card">
                    <div class="form-group">
                      <label class="form-label" for="resident">Resident</label>
                      <input type="text" id="resident" name="resident" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mt-3">
                    <div class="form-group mt-3">
                      <button type="submit" class="btn btn-primary pull-right mb-3" onclick="add()">Add</button>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-12 mt-3">
                      <div class="form-group mt-3">
                          <label class="form-label">Resident List</label>
                          <div id="add-student-div">
                            <div class="col-12">
                              <div class="box">
                                <div class="card-header">
                                <h3 class="card-title d-flex">Registered Resident</h3>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <div id="complex_header_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                                        <div class="row">
                                            <div class="col-sm-12">
                                            <table id="table_projectprogress_course" class="table table-striped projects display dataTable no-footer " style="width: 100%;">
                                                <thead class="thead-themed">
                                                <tr>
                                                    <th>
                                                    No.
                                                    </th>
                                                    <th>
                                                    Resident
                                                    </th>
                                                    <th>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($data['resident'] as $keys=>$bl)
                                                    <tr>
                                                        <td>
                                                        {{ $keys+1 }}
                                                        </td>
                                                        <td >
                                                        {{ $bl->name }}
                                                        </td>
                                                        <td class="project-actions text-right" style="text-align: center;">
                                                          <a class="btn btn-info btn-sm pr-2" href="#" onclick="getResident('{{ $bl->id }}')">
                                                              <i class="ti-pencil-alt">
                                                              </i>
                                                              Edit
                                                          </a>
                                                          <a class="btn btn-danger btn-sm" href="#" onclick="deleteResident('{{ $bl->id }}')">
                                                              <i class="ti-trash">
                                                              </i>
                                                              Delete
                                                          </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot class="tfoot-themed">
                                                    <tr>
                                                        
                                                    </tr>
                                                </tfoot>
                                            </table>
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
              </div>
              <!-- /.card-body -->
              <div id="uploadModal" class="modal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- modal content-->
                    <div class="modal-content" id="getModal">
                    </div>
                </div>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">

  function add()
  {

    var formData = new FormData();

    getInput = {
      resident : $('#resident').val()
    };
    
    formData.append('storeResident', JSON.stringify(getInput));

    $.ajax({
        headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
        url: "{{ url('hostel/resident/store') }}",
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
                    alert("Success! Resident has been created!");
                    
                    // Start with an empty table structure
                    var newTable = "<table id='table_projectprogress_course' class='table table-striped projects display dataTable no-footer' style='width: 100%;'>" +
                        "<thead class='thead-themed'>" +
                        "<tr>" +
                            "<th>No.</th>" +
                            "<th>Resident</th>" +
                            "<th></th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    // Assuming res.data is an array containing resident data
                    $.each(res.data, function(i, item) {
                        var newRow = "<tr>" +
                            "<td>" + (i+1) + "</td>" +
                            "<td>" + item.name + "</td>" +
                            "<td class='project-actions text-right' style='text-align: center;'>" +
                              "<a class='btn btn-info btn-sm pr-2' href='#' onclick='getResident(\"" + item.id + "\")'>" +
                                  "<i class='ti-pencil-alt'></i> Edit" +
                              "</a>" +
                              "<a class='btn btn-danger btn-sm' href='#' onclick='deleteResident(\"" + item.id + "\")'>" +
                                  "<i class='ti-trash'></i> Delete" +
                              "</a>" +
                            "</td>" +
                        "</tr>";
                        newTable += newRow;
                    });

                    // Close table structure
                    newTable += "</tbody>" +
                                "<tfoot class='tfoot-themed'>" +
                                  "<tr>" +
                                  " " +               
                                  "</tr>" +
                                "</tfoot>" +
                                "</table>";

                    // Replace the div contents with the new table
                    $('#add-student-div').html(newTable);


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


  function getResident(id)
  {
    return $.ajax({
            headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
            url      : "{{ url('hostel/resident/getResident') }}",
            method   : 'POST',
            data 	 : {id: id},
            error:function(err){
                alert("Error");
                console.log(err);
            },
            success  : function(data){
                $('#getModal').html(data);
                $('#uploadModal').modal('show');
            }
        });

  }

  function deleteResident(id){     
      Swal.fire({
    title: "Are you sure?",
    text: "This will be permanent",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it!"
  }).then(function(res){
    
    if (res.isConfirmed){
              $.ajax({
                  headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
                  url      : "{{ url('hostel/resident/delete') }}",
                  method   : 'POST',
                  data 	 : {id:id},
                  error:function(err){
                      alert("Error");
                      console.log(err);
                  },
                  success  : function(res){
                    alert("Success! Resident has been deleted!");
                    
                    // Start with an empty table structure
                    var newTable = "<table id='table_projectprogress_course' class='table table-striped projects display dataTable no-footer' style='width: 100%;'>" +
                        "<thead class='thead-themed'>" +
                        "<tr>" +
                            "<th>No.</th>" +
                            "<th>Resident</th>" +
                            "<th></th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    // Assuming res.data is an array containing resident data
                    $.each(res.data, function(i, item) {
                        var newRow = "<tr>" +
                            "<td>" + (i+1) + "</td>" +
                            "<td>" + item.name + "</td>" +
                            "<td class='project-actions text-right' style='text-align: center;'>" +
                              "<a class='btn btn-info btn-sm pr-2' href='#' onclick='getResident(\"" + item.id + "\")'>" +
                                  "<i class='ti-pencil-alt'></i> Edit" +
                              "</a>" +
                              "<a class='btn btn-danger btn-sm' href='#' onclick='deleteResident(\"" + item.id + "\")'>" +
                                  "<i class='ti-trash'></i> Delete" +
                              "</a>" +
                            "</td>" +
                        "</tr>";
                        newTable += newRow;
                    });

                    // Close table structure
                    newTable += "</tbody>" +
                                "<tfoot class='tfoot-themed'>" +
                                  "<tr>" +
                                  " " +               
                                  "</tr>" +
                                "</tfoot>" +
                                "</table>";

                    // Replace the div contents with the new table
                    $('#add-student-div').html(newTable);
                  }
              });
          }
      });
  }

</script>
@endsection
