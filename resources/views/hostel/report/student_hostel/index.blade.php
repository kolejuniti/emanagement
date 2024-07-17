@extends('layouts.hostel')

@section('main')
<style>
  @media print {

  @page {size: A4 landscape;max-height:100%; max-width:100%}

  /* use width if in portrait (use the smaller size to try 
    and prevent image from overflowing page... */
  img { height: 90%; margin: 0; padding: 0; }

  body{width:100%;
  height:100%;
  -webkit-transform: rotate(-90deg) scale(.68,.68); 
  -moz-transform:rotate(-90deg) scale(.58,.58) }    }
</style>
<!-- Content Header (Page header) -->
<div class="content-wrapper" style="min-height: 695.8px;">
  <div class="container-full">
  <!-- Content Header (Page header) -->	  
  <div class="content-header">
    <div class="d-flex align-items-center">
      <div class="me-auto">
        <h4 class="page-title">Student Hostel Report</h4>
        <div class="d-inline-block align-items-center">
          <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
              <li class="breadcrumb-item" aria-current="page">Dashboard</li>
              <li class="breadcrumb-item active" aria-current="page">Student Hostel Report</li>
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
                <h3 class="card-title">Student Hostel Report</h3>
                <button id="printButton" class="waves-effect waves-light btn btn-primary btn-sm">
                  <i class="ti-printer"></i>&nbsp Print
                </button>
              </div>
              <!-- /.card-header -->
              <div class="card mb-3">
                <div class="card-body">
                  {{-- <div class="row">
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
                  </div> --}}
                  <div id="form-student">
                    <div class="card mb-3" id="stud_info">
                      <div class="card-header">
                      <b>Hostel Report</b>
                      </div>
                      <div class="card-body">
                          <div class="row">
                              <div class="col-md-12 mt-3">
                                  <div class="form-group mt-3">
                                      <table class="w-100 table display margin-top-10 w-p100">
                                          <thead style="background-color: lightblue">
                                              <tr>
                                                  <th style="text-align: center; border: 1px solid black;" colspan="3">
                                                      LAPORAN ASRAMA PELAJAR
                                                  </th>
                                              </tr>
                                              <tr>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                      TEMPAT
                                                  </th>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                      BLOK
                                                  </th>
                                                  <th>
                                                  </th>
                                              </tr>
                                          </thead>
                                          <tbody id="table">
                                              @foreach($data['block'] as $key => $blk)
                                              <tr>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $blk->location }}
                                                  </td>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $blk->name }}
                                                  </td>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    <table class="w-100 table display margin-top-10 w-p100">
                                                      <thead style="background-color: lightblue">
                                                          <tr>
                                                              <th style="text-align: center; border: 1px solid black;">
                                                                  NO. UNIT
                                                              </th>
                                                              <th style="text-align: center; border: 1px solid black;">
                                                                  KAPASITI
                                                              </th>
                                                              <th style="text-align: center; border: 1px solid black;">
                                                                  JUMLAH PENGHUNI
                                                              </th>
                                                              <th style="text-align: center; border: 1px solid black;">
                                                                  KEKOSONGAN
                                                              </th>
                                                              <th style="text-align: center; border: 1px solid black;">
                                                                  STATUS
                                                              </th>
                                                          </tr>
                                                      </thead>
                                                      <tbody id="table">
                                                          @php
                                                            $sum1 = 0;
                                                            $sum2 = 0;
                                                            $sum3 = 0;    
                                                          @endphp
                                                          @foreach($data['unit'][$key] as $key2 => $ut)
                                                          <tr>
                                                              <td style="text-align: center; border: 1px solid black;">
                                                                {{ $ut->no_unit }}
                                                              </td>
                                                              <td style="text-align: center; border: 1px solid black;">
                                                                {{ $ut->capacity }}
                                                              </td>
                                                              <td style="text-align: center; border: 1px solid black;">
                                                                {{ $data['resident'][$key][$key2]->total_resident }}
                                                              </td>
                                                              <td style="text-align: center; border: 1px solid black;">
                                                                {{ $ut->capacity - $data['resident'][$key][$key2]->total_resident }}
                                                              </td>
                                                              <td style="text-align: center; border: 1px solid black;">
                                                                {{ $ut->resident }}
                                                              </td>
                                                          </tr>
                                                          @php
                                                          $sum1 += $ut->capacity;
                                                          $sum2 += $data['resident'][$key][$key2]->total_resident;
                                                          $sum3 += $ut->capacity - $data['resident'][$key][$key2]->total_resident;
                                                          @endphp
                                                          @endforeach
                                                          <div class="col-md-6" hidden>
                                                              <input type="text" class="form-control" name="sum2" id="sum2">
                                                          </div> 
                                                      </tbody>
                                                      <tfoot>
                                                        <tr>
                                                            <td style="text-align: center; border: 1px solid black;">
                                                            TOTAL AMOUNT :
                                                            </td>
                                                            <td style="text-align: center; border: 1px solid black;">
                                                            {{ $sum1 }}
                                                            </td>
                                                            <td style="text-align: center; border: 1px solid black;">
                                                            {{ $sum2 }} 
                                                            </td>
                                                            <td style="text-align: center; border: 1px solid black;">
                                                            {{ $sum3 }}
                                                            </td>
                                                            <td style="text-align: center; border: 1px solid black;"></td>
                                                        </tr>
                                                      </tfoot>
                                                    </table>
                                                  </td>
                                              </tr>
                                              @endforeach
                                              <tfoot>
                                                  {{-- <tr>
                                                      <td colspan="4" style="text-align:center">
                                                      TOTAL AMOUNT :
                                                      </td>
                                                      <td>
                                                      {{ number_format($data['sum1'], 2) }}
                                                      </td>
                                                      <td>
                                                      {{ number_format($data['sum2'], 2) }} 
                                                      </td>
                                                      <td>
                                                      {{ number_format($data['sum3'], 2) }}
                                                      </td>
                                                  </tr> --}}
                                              </tfoot>
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>

                    <div class="card mb-3" id="stud_info">
                      <div class="card-header">
                      <b>Student Hostel Report Summary</b>
                      </div>
                      <div class="card-body">
                          <div class="row">
                              <div class="col-md-12 mt-3">
                                  <div class="form-group mt-3">
                                      <table class="w-100 table display margin-top-10 w-p100">
                                          <thead style="background-color: lightblue">
                                              <tr>
                                                  <th style="text-align: center; border: 1px solid black;" colspan="5">
                                                      RINGKASAN LAPORAN ASRAMA PELAJAR
                                                  </th>
                                              </tr>
                                              <tr>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                    
                                                  </th>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                      LOKASI
                                                  </th>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                      KAPASITI
                                                  </th>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                      JUMLAH PENGHUNI
                                                  </th>
                                                  <th style="text-align: center; border: 1px solid black;">
                                                      KEKOSONGAN
                                                  </th>
                                              </tr>
                                          </thead>
                                          <tbody id="table">
                                              @php
                                              $total1 = 0;
                                              $total2 = 0;
                                              $total3 = 0;
                                              @endphp
                                              @foreach($data['summary'] as $key => $sm)
                                              <tr>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $sm->resident }}
                                                  </td>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $sm->location }}
                                                  </td>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $data['capacity'][$key]->total }}
                                                  </td>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $data['resident2'][$key]->total }}
                                                  </td>
                                                  <td style="text-align: center; border: 1px solid black;">
                                                    {{ $data['vacancy'][$key] }}
                                                  </td>
                                              </tr>
                                              @php
                                              $total1 += $data['capacity'][$key]->total;
                                              $total2 += $data['resident2'][$key]->total;
                                              $total3 += $data['vacancy'][$key];
                                              @endphp
                                              @endforeach
                                          </tbody>
                                          <tfoot>
                                            <tr>
                                                <td colspan="2" style="text-align: center; border: 1px solid black;">
                                                TOTAL AMOUNT :
                                                </td>
                                                <td style="text-align: center; border: 1px solid black;">
                                                {{ $total1 }}
                                                </td>
                                                <td style="text-align: center; border: 1px solid black;">
                                                {{ $total2 }} 
                                                </td>
                                                <td style="text-align: center; border: 1px solid black;">
                                                {{ $total3 }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class="row" id="confirm-card" hidden>
                    <div class="col-md-12 mt-3 text-center">
                        <div class="form-group mt-3">
                          <button type="submit" class="btn btn-primary mb-3" onclick="confirm()">Confirm</button>
                        </div>
                    </div>
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

$(document).ready(function() {
    $('#printButton').on('click', function(e) {
      e.preventDefault();
      printReport();
    });
  });

  function printReport() {
    var student = $('#student').val();

    return $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      url: "{{ url('hostel/report/studentHostelReport2?print=true') }}",
      method: 'POST',
      data: { student: student},
      error: function(err) {
        alert("Error");
        console.log(err);
      },
      success: function(data) {
        var newWindow = window.open();
        newWindow.document.write(data);
        newWindow.document.close();
      }
    });
  }

</script>
@endsection
