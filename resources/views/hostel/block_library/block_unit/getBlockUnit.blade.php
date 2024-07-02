  <div class="modal-header">

  </div>
  <div class="modal-body">
    <div class="row col-md-12">
      <div class="col-md-12" id="block-card">
        <div class="form-group">
          <label class="form-label" for="blocks">Block</label>
          <select class="form-select" id="blocks" name="blocks">
            <option value="-" selected disabled>-</option>
            @foreach($data['block'] as $blk)
            <option value="{{ $blk->id }}" {{ ($data['blockUnit']->block_id == $blk->id) ? 'selected' : '' }}>{{ $blk->name }} - {{ $blk->location }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-md-12" id="unit-card">
        <div class="form-group">
          <label class="form-label" for="unit">No. Unit</label>
          <input type="text" id="units" name="units" class="form-control" value="{{ $data['blockUnit']->no_unit }}">
        </div>
      </div>
      <div class="col-md-12" id="capacity-card">
        <div class="form-group">
          <label class="form-label" for="capacity">Capacity</label>
          <input type="text" id="capacitys" name="capacitys" class="form-control" value="{{ $data['blockUnit']->capacity }}">
        </div>
      </div>
      <div class="col-md-12" id="resident-card">
        <div class="form-group">
          <label class="form-label" for="resident">Resident</label>
          <select class="form-select" id="residents" name="residents">
            <option value="-" selected disabled>-</option>
            @foreach($data['resident'] as $rs)
            <option value="{{ $rs->id }}" {{ ($data['blockUnit']->resident_id == $rs->id) ? 'selected' : '' }}>{{ $rs->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div>
        <div class="form-group">
            <label class="form-label" for="status">Unit Status</label>
            <select class="form-select" id="status" name="status">
            <option value="-" selected disabled>-</option>
            <option value="OK" {{ ($data['blockUnit']->status == 'OK') ? 'selected' : '' }}>OK</option>
            <option value="UNDER MAINTENANCE" {{ ($data['blockUnit']->status == 'UNDER MAINTENANCE') ? 'selected' : '' }}>UNDER MAINTENANCE</option>
            </select>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
      <div class="form-group pull-right">
          <input type="submit" class="form-controlwaves-effect waves-light btn btn-primary btn-sm pull-right" value="submit" onclick="add2()">
      </div>
  </div>

<script type="text/javascript">
  function add2()
  {

    var formData = new FormData();

    getInput = {
      block : $('#blocks').val(),
      unit : $('#units').val(),
      capacity : $('#capacitys').val(),
      resident : $('#residents').val(),
      status : $('#status').val()
    };

    let id = "{{ $data['blockUnit']->id }}";
    
    formData.append('storeBlockUnit', JSON.stringify(getInput));

    $.ajax({
        headers: {'X-CSRF-TOKEN':  $('meta[name="csrf-token"]').attr('content')},
        url: "/hostel/blockUnit/store?idS=" + id,
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
                    alert("Success! Block has been updated!");
                    
                    $('#uploadModal').modal('hide');
                    
                    // Start with an empty table structure
                    var newTable = "<table id='table_projectprogress_course' class='table table-striped projects display dataTable no-footer' style='width: 100%;'>" +
                        "<thead class='thead-themed'>" +
                        "<tr>" +
                        "<th>No.</th>" +
                        "<th>Block</th>" +
                        "<th>No. Unit</th>" +
                        "<th>Capacity</th>" +
                        "<th>Total Resident</th>" +
                        "<th>Resident</th>" +
                        "<th>Unit Status</th>" +
                        "<th></th>" +
                        "</tr>" +
                        "</thead>" +
                        "<tbody>";

                    // Loop through each block unit data item
                    $.each(res.data, function(i, item) {
                        var newRow = "<tr>" +
                            "<td>" + (i + 1) + "</td>" +
                            "<td>" + item.block + "</td>" +
                            "<td>" + item.no_unit + "</td>" +
                            "<td>" + item.capacity + "</td>" +
                            "<td>" + item.total_student + "/" + item.capacity + "</td>" + // Adjust according to your data structure
                            "<td>" + item.resident + "</td>" +
                            "<td>" + item.status + "</td>" +
                            "<td class='project-actions text-right' style='text-align: center;'>" +
                            "<a class='btn btn-info btn-sm pr-2' href='#' onclick='getBlockUnit(\"" + item.id + "\")'>" +
                            "<i class='ti-pencil-alt'></i> Edit" +
                            "</a>" +
                            "<a class='btn btn-danger btn-sm' href='#' onclick='deleteBlockUnit(\"" + item.id + "\")'>" +
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
                        "</tr>" +
                        "</tfoot>" +
                        "</table>";

                    // Replace the contents of an HTML element with the ID 'unit-table' with the new table
                    $('#unit-table').html(newTable);

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
</script>