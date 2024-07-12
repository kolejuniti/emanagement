<!-- form start -->
<div class="card-body">
    <div class="row">
        <div class="col-md-12 mt-3">
            <div class="form-group mt-3">
                <label class="form-label">Block Units</label>
                <table class="w-100 table table-bordered display margin-top-10 w-p100" id="voucher_table">
                    <thead id="voucher_list">
                        <tr>
                            <th style="width: 1%">
                                No.
                            </th>
                            <th>
                                No. Unit
                            </th>
                            <th>
                                Capacity
                            </th>
                            <th>
                                Resident
                            </th>
                            <th>
                                Availability
                            </th>
                            <th>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table">
                        @foreach ($data['unit'] as $key => $ut)
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td>
                                    {{ $ut->no_unit }}
                                </td>
                                <td>
                                    {{ $ut->capacity }}
                                </td>
                                <td>
                                    {{ $ut->resident }}
                                </td>
                                <td>
                                    {{ $data['resident'][$key]->total_student }}/{{ $ut->capacity }}
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm" href="/hostel/register/{{ $ut->id }}">
                                        Register
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
   