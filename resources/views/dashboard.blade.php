@extends((Auth::user()->type == "admin") ? 'layouts.admin' : ((Auth::user()->type == "hostel") ? 'layouts.hostel' : 'layouts.user'))

@section('main')
<!-- Content Header (Page header) -->
<div class="content-wrapper" style="min-height: 695.8px;">
  <div class="container-full">
    <!-- Content Header (Page header) -->	  
    <div class="content-header">
      <div class="d-flex align-items-center">
        <div class="me-auto">
          <h4 class="page-title">Dashboard</h4>
          <div class="d-inline-block align-items-center">
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="row">
        <div class="col-lg-12 col-12">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3 style="text-align:center">Selamat Datang ke</h3><br>
              <h3 style="text-align:center">Sistem Pengurusan Kolej Uniti!</h3>
            </div>
            {{-- <div class="icon">
              <i class="ion ion-bag"></i>
            </div> --}}
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
</div>

<script src="{{ asset('assets/src/js/pages/data-table.js') }}"></script>

@endsection
