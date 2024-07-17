
<head>
    <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="">
   <meta name="author" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>EduHub - @yield('title')</title>

  {{-- <link rel="stylesheet" media="screen, print" href="{{ asset('assets/src/css/datagrid/datatables/datatables.bundle.css') }}"> --}}
  {{-- <link rel="stylesheet" href="{{ asset('assets/assets/vendor_components/datatable/datatables.css') }}"> --}}
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/css-skeletons@1.0.3/css/css-skeletons.min.css"/> --}}
  <link rel="stylesheet" href="https://unpkg.com/css-skeletons@1.0.3/css/css-skeletons.min.css" />

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  
   <style>
    @page {
       size: A4 potrait; /* reduced height for A5 size in landscape orientation */
       margin: 0cm;
     }
 
     * {
         margin: 0;
         padding: 0;
         border: 0;
         outline: 0;
         vertical-align: baseline;
         background: transparent;
         font-size: 9px;
         padding: 1px;   
     }
     h2,h3,p {
         margin: 0;
         padding: 0;
         border: 0;
         outline: 0;
         vertical-align: baseline;
         background: transparent;
         font-size: 9px;
     }

     .table-fit-content {
    width: auto;         /* Fit to content, rather than stretching to full width */
    max-width: 30%;     /* Ensure it doesn't overflow the parent container */
    border-collapse: collapse;
    margin: auto;        /* Center the table if smaller than the parent width */
}


     /* Base table styles */
table {
    width: 100%;            /* Make the table take up the full width */
    border-collapse: collapse; /* Remove gaps between cells */
    font-size: 16px;        /* Set base font size */
    margin-bottom: 20px;   /* Add space below the table */
}

     /* Headers */
th {
    background-color: #f4f4f4;  /* Light gray background */
    font-weight: bold;      /* Bold font for headers */
    text-align: left;       /* Left-align header text */
    padding: 5px;          /* Add padding */
    border: 1px solid #ddd; /* Light gray border */
}

/* Cells */
td {
    padding: 5px;          /* Add padding to cells */
    border: 1px solid #ddd; /* Light gray border */
    vertical-align: top;    /* Align content to top */
}

/* Rows */
tr:nth-child(even) {
    background-color: #ffffff; /* Alternate row color for better readability */
}

tr:hover {
    background-color: #ffffff; /* Highlight row on hover */
}
     </style>
  </head>
  
  
  
 <body>
    <div class="container">
        <!-- BEGIN INVOICE -->
        <div class="col-12">
            <div class="card mb-3" id="stud_info">
                <div class="card-header">
                <b>Hostel Report</b>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="form-group mt-3">
                                <table class="w-100 table table-bordered display margin-top-10 w-p100">
                                    <thead>
                                        <tr>
                                            <tr>
                                                <th colspan="3">
                                                    LAPORAN ASRAMA PELAJAR
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>
                                                    TEMPAT
                                                </th>
                                                <th>
                                                    BLOK
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                        </tr>
                                    </thead>
                                    <tbody id="table">
                                        @foreach($data['block'] as $key => $blk)
                                        <tr>
                                            <td>
                                              {{ $blk->location }}
                                            </td>
                                            <td>
                                              {{ $blk->name }}
                                            </td>
                                            <td>
                                              <table class="w-100 table table-bordered display margin-top-10 w-p100">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            NO. UNIT
                                                        </th>
                                                        <th>
                                                            KAPASITI
                                                        </th>
                                                        <th>
                                                            JUMLAH PENGHUNI
                                                        </th>
                                                        <th>
                                                            KEKOSONGAN
                                                        </th>
                                                        <th>
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
                                                        <td>
                                                          {{ $ut->no_unit }}
                                                        </td>
                                                        <td>
                                                          {{ $ut->capacity }}
                                                        </td>
                                                        <td>
                                                          {{ $data['resident'][$key][$key2]->total_resident }}
                                                        </td>
                                                        <td>
                                                          {{ $ut->capacity - $data['resident'][$key][$key2]->total_resident }}
                                                        </td>
                                                        <td>
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
                                                        <td>
                                                        TOTAL AMOUNT :
                                                        </td>
                                                        <td>
                                                        {{ $sum1 }}
                                                        </td>
                                                        <td>
                                                        {{ $sum2 }} 
                                                        </td>
                                                        <td>
                                                        {{ $sum3 }}
                                                        </td>
                                                        <td></td>
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
                                <table class="w-100 table table-bordered display margin-top-10 w-p100">
                                    <thead>
                                        <tr>
                                            <th colspan="5">
                                                RINGKASAN LAPORAN ASRAMA PELAJAR
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>
                                              
                                            </th>
                                            <th>
                                                LOKASI
                                            </th>
                                            <th>
                                                KAPASITI
                                            </th>
                                            <th>
                                                JUMLAH PENGHUNI
                                            </th>
                                            <th>
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
                                            <td>
                                              {{ $sm->resident }}
                                            </td>
                                            <td>
                                              {{ $sm->location }}
                                            </td>
                                            <td>
                                              {{ $data['capacity'][$key]->total }}
                                            </td>
                                            <td>
                                              {{ $data['resident2'][$key]->total }}
                                            </td>
                                            <td>
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
                                          <td colspan="2">
                                            <b>TOTAL AMOUNT :</b>
                                          </td>
                                          <td>
                                            <b>{{ $total1 }}</b>
                                          </td>
                                          <td>
                                            <b>{{ $total2 }}</b> 
                                          </td>
                                          <td>
                                            <b>{{ $total3 }}</b>
                                          </td>
                                      </tr>
                                  </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12 mt-3">
                          <div class="form-group mt-3">
                              <table class="w-100 table table-bordered display margin-top-10 w-p100">
                                  <thead>
                                      <tr>
                                          <th>
                                            
                                          </th>
                                          <th>
                                              KAPASITI
                                          </th>
                                          <th>
                                              JUMLAH PENGHUNI
                                          </th>
                                          <th>
                                              KEKOSONGAN
                                          </th>
                                      </tr>
                                  </thead>
                                  <tbody id="table">
                                      @php
                                      $total12 = 0;
                                      $total22 = 0;
                                      $total32 = 0;
                                      @endphp
                                      @foreach($data['summary2'] as $key => $sm)
                                      <tr>
                                          <td>
                                            {{ $sm->location }}
                                          </td>
                                          <td>
                                            {{ $data['capacity2'][$key]->total ?? 0 }}
                                          </td>
                                          <td>
                                            {{ $data['resident3'][$key]->total ?? 0 }}
                                          </td>
                                          <td>
                                            {{ $data['vacancy2'][$key] }}
                                          </td>
                                      </tr>
                                      @php
                                      $total12 += $data['capacity2'][$key]->total;
                                      $total22 += $data['resident3'][$key]->total;
                                      $total32 += $data['vacancy2'][$key];
                                      @endphp
                                      @endforeach
                                  </tbody>
                                  <tfoot>
                                    <tr>
                                        <td>
                                          <b>TOTAL AMOUNT :</b>
                                        </td>
                                        <td>
                                          <b>{{ $total12 }}</b>
                                        </td>
                                        <td>
                                          <b>{{ $total22 }}</b> 
                                        </td>
                                        <td>
                                          <b>{{ $total32 }}</b>
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
        <!-- END INVOICE -->
    </div>
 </body>
 <script type="text/javascript">
 
    $(document).ready(function () {
        window.print();
    });
    
    </script>