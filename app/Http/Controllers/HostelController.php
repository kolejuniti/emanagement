<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Exception;

class HostelController extends Controller
{

    public function getStudentList(Request $request)
    {
        $students = DB::connection('mysql2')->table('students')->where('name', 'LIKE', "%".$request->search."%")
                                         ->orwhere('ic', 'LIKE', "%".$request->search."%")
                                         ->orwhere('no_matric', 'LIKE', "%".$request->search."%")->get();

        $content = "";

        $content .= "<option value='0' selected disabled>-</option>";
        foreach($students as $std){

            $content .= "<option data-style=\"btn-inverse\"
            data-content=\"<div class='row'>
                <div class='col-md-2'>
                <div class='d-flex justify-content-center'>
                    <img src='' 
                        height='auto' width='70%' class='bg-light ms-0 me-2 rounded-circle'>
                        </div>
                </div>
                <div class='col-md-10 align-self-center lh-lg'>
                    <span><strong>". $std->name ."</strong></span><br>
                    <span>". $std->email ." | <strong class='text-fade'>". $std->ic ."</strong></span><br>
                    <span class='text-fade'></span>
                </div>
            </div>\" value='". $std->ic ."' ></option>";

        }
        
        return $content;

    }

    public function indexBlock()
    {

        $data['block'] = DB::table('tblblock')->get();

        return view('hostel.block_library.block.index', compact('data'));

    }

    public function storeBlock(Request $request)
    {

        if(!isset($request->idS))
        {
            $data = json_decode($request->storeBlock);

            if($data->block != null && $data->location)
            {

                try{ 
                    DB::beginTransaction();
                    DB::connection()->enableQueryLog();

                    try{

                        DB::table('tblblock')->insert([
                            'name' => $data->block,
                            'location' => $data->location
                        ]);

                    }catch(QueryException $ex){
                        DB::rollback();
                        if($ex->getCode() == 23000){
                            return ["message"=>"Class code already existed inside the system"];
                        }else{
                            Log::debug($ex);
                            return ["message"=>"DB Error"];
                        }
                    }

                    DB::commit();
                }catch(Exception $ex){
                    return ["message"=>"Error"];
                }

            }else{

                return ["message"=>"Please select all required field!"];

            }

            $datas = DB::table('tblblock')->get();

            return response()->json(['message' => 'Success', 'data' => $datas]);

        }else{

            $data = json_decode($request->storeBlock);

            DB::table('tblblock')->where('id', $request->idS)
            ->update([
                'name' => $data->block,
                'location' => $data->location
            ]);

            $datas = DB::table('tblblock')->get();

            return response()->json(['message' => 'Success', 'data' => $datas]);

        }
    }

    public function deleteBlock(Request $request)
    {

        DB::table('tblblock')->where('id', $request->id)->delete();

        $datas = DB::table('tblblock')->get();

        return response()->json(['message' => 'Success', 'data' => $datas]);

    }

    public function getBlock(Request $request)
    {

        $data['block'] = DB::table('tblblock')->where('id', $request->id)->first();

        return view('hostel.block_library.block.getBlock', compact('data'));

    }

    public function indexBlockUnit()
    {
        $data['block'] = DB::table('tblblock')->get();

        $data['resident'] = DB::table('tblresident')->get();

        return view('hostel.block_library.block_unit.index', compact('data'));

    }

    
    public function getBlockUnits(Request $request)
    {

        $datas = DB::table('tblblock_unit')
        ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
        ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
        ->where('tblblock_unit.block_id', $request->block)
        ->select('tblblock_unit.*', 'tblblock.name AS block', 'tblresident.name AS resident')
        ->get();

        foreach ($datas as $key => $bu) {
            // Fetch total student count for each block unit
            $totalStudents = DB::table('tblstudent_hostel')
                ->where('block_unit_id', $bu->id)
                ->where('status', 'IN')
                ->count();  // Count directly since we need only one value
        
            // Assign total student count to $datas
            $datas[$key]->total_student = $totalStudents;
        }

        return response()->json(['data' => $datas]);

    }

    public function storeBlockUnit(Request $request)
    {

        if(!isset($request->idS))
        {
            $data = json_decode($request->storeBlockUnit);

            if($data->block != null && $data->unit != null && $data->capacity != null && $data->resident != null)
            {

                try{ 
                    DB::beginTransaction();
                    DB::connection()->enableQueryLog();

                    try{

                        DB::table('tblblock_unit')->insert([
                            'block_id' => $data->block,
                            'no_unit' => $data->unit,
                            'capacity' => $data->capacity,
                            'resident_id' => $data->resident,
                            'status' => 'OK'
                        ]);

                    }catch(QueryException $ex){
                        DB::rollback();
                        if($ex->getCode() == 23000){
                            return ["message"=>"Class code already existed inside the system"];
                        }else{
                            Log::debug($ex);
                            return ["message"=>"DB Error"];
                        }
                    }

                    DB::commit();
                }catch(Exception $ex){
                    return ["message"=>"Error"];
                }

            }else{

                return ["message"=>"Please select all required field!"];

            }

            $datas = DB::table('tblblock_unit')
            ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
            ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
            ->where('tblblock_unit.block_id', $data->block)
            ->select('tblblock_unit.*', 'tblblock.name AS block', 'tblresident.name AS resident')
            ->get();

            foreach ($datas as $key => $bu) {
                // Fetch total student count for each block unit
                $totalStudents = DB::table('tblstudent_hostel')
                    ->where('block_unit_id', $bu->id)
                    ->where('status', 'IN')
                    ->count();  // Count directly since we need only one value
            
                // Assign total student count to $datas
                $datas[$key]->total_student = $totalStudents;
            }

            return response()->json(['message' => 'Success', 'data' => $datas]);

        }else{

            $data = json_decode($request->storeBlockUnit);

            DB::table('tblblock_unit')->where('id', $request->idS)
            ->update([
                'block_id' => $data->block,
                'no_unit' => $data->unit,
                'capacity' => $data->capacity,
                'resident_id' => $data->resident,
                'status' => $data->status
            ]);

            $datas = DB::table('tblblock_unit')
            ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
            ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
            ->where('tblblock_unit.block_id', $data->block)
            ->select('tblblock_unit.*', 'tblblock.name AS block', 'tblresident.name AS resident')
            ->get();

            foreach ($datas as $key => $bu) {
                // Fetch total student count for each block unit
                $totalStudents = DB::table('tblstudent_hostel')
                    ->where('block_unit_id', $bu->id)
                    ->where('status', 'IN')
                    ->count();  // Count directly since we need only one value
            
                // Assign total student count to $datas
                $datas[$key]->total_student = $totalStudents;
            }


            return response()->json(['message' => 'Success', 'data' => $datas]);

        }
    }

    public function deleteBlockUnit(Request $request)
    {

        DB::table('tblblock_unit')->where('id', $request->id)->delete();

        $datas = DB::table('tblblock_unit')
        ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
        ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
        ->select('tblblock_unit.*', 'tblblock.name AS block', 'tblresident.name AS resident')
        ->get();

        foreach ($datas as $key => $bu) {
            // Fetch total student count for each block unit
            $totalStudents = DB::table('tblstudent_hostel')
                ->where('block_unit_id', $bu->id)
                ->where('status', 'IN')
                ->count();  // Count directly since we need only one value
        
            // Assign total student count to $datas
            $datas[$key]->total_student = $totalStudents;
        }

        return response()->json(['message' => 'Success', 'data' => $datas]);

    }


    public function getBlockUnit(Request $request)
    {

        $data['block'] = DB::table('tblblock')->get();

        $data['resident'] = DB::table('tblresident')->get();

        $data['blockUnit'] = DB::table('tblblock_unit')->where('id', $request->id)->first();

        return view('hostel.block_library.block_unit.getBlockUnit', compact('data'));

    }

    public function indexResident()
    {

        $data['resident'] = DB::table('tblresident')->get();

        return view('hostel.block_library.resident.index', compact('data'));

    }

    public function storeResident(Request $request)
    {

        if(!isset($request->idS))
        {
            $data = json_decode($request->storeResident);

            if($data->resident)
            {

                try{ 
                    DB::beginTransaction();
                    DB::connection()->enableQueryLog();

                    try{

                        DB::table('tblresident')->insert([
                            'name' => $data->resident
                        ]);

                    }catch(QueryException $ex){
                        DB::rollback();
                        if($ex->getCode() == 23000){
                            return ["message"=>"Class code already existed inside the system"];
                        }else{
                            Log::debug($ex);
                            return ["message"=>"DB Error"];
                        }
                    }

                    DB::commit();
                }catch(Exception $ex){
                    return ["message"=>"Error"];
                }

            }else{

                return ["message"=>"Please select all required field!"];

            }

            $datas = DB::table('tblresident')->get();

            return response()->json(['message' => 'Success', 'data' => $datas]);

        }else{

            $data = json_decode($request->storeResident);

            DB::table('tblresident')->where('id', $request->idS)
            ->update([
                'name' => $data->resident
            ]);

            $datas = DB::table('tblresident')->get();

            return response()->json(['message' => 'Success', 'data' => $datas]);

        }
    }

    public function deleteResident(Request $request)
    {

        DB::table('tblresident')->where('id', $request->id)->delete();

        $datas = DB::table('tblresident')->get();

        return response()->json(['message' => 'Success', 'data' => $datas]);

    }

    public function getResident(Request $request)
    {

        $data['resident'] = DB::table('tblresident')->where('id', $request->id)->first();

        return view('hostel.resident.getResident', compact('data'));

    }

    public function hostelRegister()
    {

        $data['block'] = DB::table('tblblock')->get();

        return view('hostel.student.hostel_registration.hostelRegister', compact('data'));

    }

    public function getBlockUnitList(Request $request)
    {

        $data['unit'] = DB::table('tblblock_unit')
                        ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                        ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                        ->where('tblblock_unit.block_id', $request->id)
                        ->select('tblblock_unit.*', 'tblresident.name AS resident')
                        ->get();

        foreach($data['unit'] as $key => $ut)
        {

            $data['resident'][$key] = DB::table('tblstudent_hostel')
                                ->where('block_unit_id', $ut->id)
                                ->where('status', 'IN')
                                ->select(DB::raw('COUNT(id) AS total_student'))
                                ->first();

        }

        return view('hostel.student.hostel_registration.getBlockUnitList', compact('data'));

    }

    public function hostelRegisterStudent(Request $request)
    {

        $data['unit'] = DB::table('tblblock_unit')
                        ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                        ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                        ->where('tblblock_unit.id', $request->id)
                        ->select('tblblock_unit.*', 'tblblock.name', 'tblblock.location' ,'tblresident.name AS resident', 'tblblock_unit.no_unit')
                        ->first();

        // Step 1: Fetch the student data from the mysql2 connection
        $students = DB::connection('mysql2')->table('students')
        ->select('ic', 'name')
        ->get();

        // Step 2: Create an array of student_ic to use in the next query
        $studentIcs = $students->pluck('ic')->toArray();

        // Step 3: Fetch the hostel data and join with the block unit table
        $hostelData = DB::table('tblstudent_hostel')
        ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
        ->where('tblstudent_hostel.block_unit_id', $request->id)
        ->whereIn('tblstudent_hostel.student_ic', $studentIcs)
        ->select('tblstudent_hostel.*', 'tblblock_unit.no_unit')
        ->get();

        // Step 4: Add the student names to the hostel data
        $hostelData->map(function($hostel) use ($students) {
        $student = $students->firstWhere('ic', $hostel->student_ic);
        $hostel->name = $student ? $student->name : null;
        return $hostel;
        });

        $data['student'] = $hostelData;


        return view('hostel.student.hostel_registration.registerStudent', compact('data'));              

    }

    public function getRemarkData($ic)
    {
        // Fetch the data based on the ic value
        $data = DB::connection('mysql2')->table('students')
                ->where('ic',  $ic)
                ->get();


        // Return the data as a JSON response
        return response()->json($data);
    }


    public function getStudentInfo(Request $request)
    {

        $data = DB::connection('mysql2')->table('students')
        ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
        ->join('tblprogramme', 'students.program', 'tblprogramme.id')
        ->join('sessions AS t1', 'students.intake', 't1.SessionID')
        ->join('sessions AS t2', 'students.session', 't2.SessionID')
        ->select('students.*', 'tblstudent_status.name AS status', 'tblprogramme.progname AS program', 'students.program AS progid', 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
        ->where('ic', $request->student)->first();

        // return back()->with(['data' => $student]);

        // Return the data as part of the response
        return response()->json([
            'data' => $data,
        ]);

    }

    public function registerStudent(Request $request)
    {

        $data = json_decode($request->storeStudent);

            if($data->student)
            {

                try{ 
                    DB::beginTransaction();
                    DB::connection()->enableQueryLog();

                    try{

                        $baseQuery = function() use ($data){

                            return DB::table('tblstudent_hostel')
                                   ->leftjoin('tblblock_unit', 'tblstudent_hostel.block_unit_id', 'tblblock_unit.id')
                                   ->where('block_unit_id', $data->id);

                        };
                                 
                        $count = ($baseQuery)()->where('tblstudent_hostel.status', 'IN')->select(DB::raw('COUNT(tblstudent_hostel.id) AS total_student'))
                                 ->first();

                        $detail = DB::table('tblblock_unit')->where('id', $data->id)->first();

                        if($count->total_student < $detail->capacity)
                        {

                            if(DB::table('tblstudent_hostel')->where([['student_ic', $data->student], ['block_unit_id', $data->id,], ['status', 'IN']])->exists())
                            {

                                $alert = "Student already exist in this unit!";

                            }else{

                                if(DB::table('tblstudent_hostel')->where([['student_ic', $data->student], ['status', 'IN']])->exists())
                                {

                                    $alert = "Student already exist in another unit!";

                                }else{

                                    DB::table('tblstudent_hostel')->insert([
                                        'student_ic' => $data->student,
                                        'block_unit_id' => $data->id,
                                        'entry_date' => now(),
                                        'status' => 'IN',
                                        'exit_date' => null
                                    ]);
        
                                    $alert = "Success";

                                }

                            }

                        }else{

                            $alert = "Number of student in the unit cannot exceed " . $detail->capacity;

                        }

                    }catch(QueryException $ex){
                        DB::rollback();
                        if($ex->getCode() == 23000){
                            return ["message"=>"Class code already existed inside the system"];
                        }else{
                            Log::debug($ex);
                            return ["message"=>"DB Error"];
                        }
                    }

                    DB::commit();
                }catch(Exception $ex){
                    return ["message"=>"Error"];
                }

            }else{

                return ["message"=>"Please select all required field!"];

            }

            // Step 1: Fetch the student data from the mysql2 connection
            $students = DB::connection('mysql2')->table('students')
            ->select('ic', 'name')
            ->get();

            // Step 2: Create an array of student_ic to use in the next query
            $studentIcs = $students->pluck('ic')->toArray();

            // Step 3: Fetch the hostel data and join with the block unit table
            $hostelData = DB::table('tblstudent_hostel')
            ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
            ->where('tblstudent_hostel.block_unit_id', $data->id)
            ->whereIn('tblstudent_hostel.student_ic', $studentIcs)
            ->select('tblstudent_hostel.*', 'tblblock_unit.no_unit')
            ->get();

            // Step 4: Add the student names to the hostel data
            $hostelData->map(function($hostel) use ($students) {
            $student = $students->firstWhere('ic', $hostel->student_ic);
            $hostel->name = $student ? $student->name : null;
            return $hostel;
            });

            $datas = $hostelData;

            return response()->json(['message' => $alert, 'data' => $datas]);

    }

    public function deleteStudent(Request $request)
    {

        DB::table('tblstudent_hostel')->where('id', $request->ids)->delete();

        // Step 1: Fetch the student data from the mysql2 connection
        $students = DB::connection('mysql2')->table('students')
        ->select('ic', 'name')
        ->get();

        // Step 2: Create an array of student_ic to use in the next query
        $studentIcs = $students->pluck('ic')->toArray();

        // Step 3: Fetch the hostel data and join with the block unit table
        $hostelData = DB::table('tblstudent_hostel')
        ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
        ->where('tblstudent_hostel.block_unit_id', $request->id)
        ->whereIn('tblstudent_hostel.student_ic', $studentIcs)
        ->select('tblstudent_hostel.*', 'tblblock_unit.no_unit')
        ->get();

        // Step 4: Add the student names to the hostel data
        $hostelData->map(function($hostel) use ($students) {
        $student = $students->firstWhere('ic', $hostel->student_ic);
        $hostel->name = $student ? $student->name : null;
        return $hostel;
        });

        $datas = $hostelData;

        return response()->json(['message' => "Success", 'data' => $datas]);

    }

    public function studentList()
    {
        $year = DB::connection('mysql2')->table('tblyear')->get();
        
        $program = DB::connection('mysql2')->table('tblprogramme')->get();

        $session = DB::connection('mysql2')->table('sessions')->get();

        $semester = DB::connection('mysql2')->table('semester')->get();

        $status = DB::connection('mysql2')->table('tblstudent_status')->get();

        return view('hostel.student.list.index', compact('program', 'session', 'semester', 'year', 'status'));
    }

    public function getStudentListIndex(Request $request)
    {
        $student = DB::connection('mysql2')->table('students')
            ->join('tblprogramme', 'students.program', 'tblprogramme.id')
            ->join('sessions AS a', 'students.intake', 'a.SessionID')
            ->join('sessions AS b', 'students.session', 'b.SessionID')
            ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
            ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
            ->leftJoin('tblqualification_std', 'tblstudent_personal.qualification', 'tblqualification_std.id')
            ->join('tblsex', 'tblstudent_personal.sex_id', 'tblsex.id')
            ->select('students.*', 'tblprogramme.progcode', 'a.SessionName AS intake', 
                     'b.SessionName AS session', 'tblstudent_status.name AS status',
                     'tblstudent_personal.no_tel', 'tblsex.code AS gender', 'tblqualification_std.name AS qualification');

        if(!empty($request->program) && $request->program != '-')
        {
            $student->where('students.program', $request->program);
        }
        
        if(!empty($request->input('session')) && $request->input('session') != '-')
        {
            $student->where('students.session', $request->input('session'));
        }
        
        if(!empty($request->year) && $request->year != '-')
        {
            $student->where('b.Year', $request->year);
        }
        
        if(!empty($request->semester) && $request->semester != '-')
        {
            $student->where('students.semester', $request->semester);
        }
        
        if(!empty($request->status) && $request->status != '-')
        {
            $student->where('students.status', $request->status);
        }

        $students = $student->get();

        foreach($students as $key => $std)
        {

            $sponsor_id[$key] = DB::connection('mysql2')->table('tblpayment')
                                ->where([
                                    ['student_ic', $std->ic],
                                    ['sponsor_id', '!=', null]
                                    ])
                                ->latest('id')->first();

            if($sponsor_id[$key] != null)
            {

                $sponsor[$key] = DB::connection('mysql2')->table('tblpayment')
                                ->join('tblsponsor_library', 'tblpayment.payment_sponsor_id', 'tblsponsor_library.id')
                                ->where('tblpayment.id', $sponsor_id[$key]->sponsor_id)->pluck('tblsponsor_library.code')->first();

            }else{

                $sponsor[$key] = 'SENDIRI';

            }

            if($std->student_status == 1)
            {

                $student_status[$key] = 'Holding';

            }elseif($std->student_status == 2)
            {

                $student_status[$key] = 'Kuliah';

            }elseif($std->student_status == 4)
            {

                $student_status[$key] = 'Latihan Industri';

            }

        }

        $content = "";
        $content .= '<thead>
                        <tr>
                            <th style="width: 1%">
                                No.
                            </th>
                            <th>
                                Name
                            </th>
                            <th>
                                Gender
                            </th>
                            <th>
                                No. IC
                            </th>
                            <th>
                                No. Matric
                            </th>
                            <th>
                                Program
                            </th>
                            <th>
                                Intake
                            </th>
                            <th>
                                Current Session
                            </th>
                            <th>
                                Semester
                            </th>
                            <th>
                                Sponsorship
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                No. Phone
                            </th>
                            <th>
                                Campus
                            </th>
                            <th>
                                Qualification
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table">';
                    
        foreach($students as $key => $student){
            //$registered = ($student->status == 'ACTIVE') ? 'checked' : '';
            $content .= '
            <tr>
                <td style="width: 1%">
                '. $key+1 .'
                </td>
                <td>
                '. $student->name .'
                </td>
                <td>
                '. $student->gender .'
                </td>
                <td>
                '. $student->ic .'
                </td>
                <td>
                '. $student->no_matric .'
                </td>
                <td>
                '. $student->progcode .'
                </td>
                <td>
                '. $student->intake .'
                </td>
                <td>
                '. $student->session .'
                </td>
                <td>
                '. $student->semester .'
                </td>
                <td>
                '. $sponsor[$key] .'
                </td>
                <td>
                '. $student->status .'
                </td>
                <td>
                '. $student->no_tel .'
                </td>
                <td>
                '. $student_status[$key] .'
                </td>
                <td>
                '. $student->qualification .'
                </td>';
                

                // if (isset($request->edit)) {
                //     $content .= '<td class="project-actions text-right" >
                //                 <a class="btn btn-info btn-sm btn-sm mr-2 mb-2" href="/pendaftar/view/'. $student->ic .'">
                //                     <i class="ti-pencil-alt">
                //                     </i>
                //                     View
                //                 </a>
                //                 <a class="btn btn-info btn-sm btn-sm mr-2 mb-2" href="/pendaftar/edit/'. $student->ic .'">
                //                     <i class="ti-pencil-alt">
                //                     </i>
                //                     Edit
                //                 </a>
                //                 <a class="btn btn-primary btn-sm btn-sm mr-2 mb-2" href="/pendaftar/spm/'. $student->ic .'">
                //                     <i class="ti-ruler-pencil">
                //                     </i>
                //                     SPM/SVM/SKM
                //                 </a>
                //                 <a class="btn btn-secondary btn-sm btn-sm mr-2 mb-2" href="#" onclick="getProgram(\''. $student->ic .'\')">
                //                     <i class="ti-eye">
                //                     </i>
                //                     Program History
                //                 </a>
                //                 <a class="btn btn-secondary btn-sm btn-sm mr-2 mb-2" target="_blank" href="/AR/student/getSlipExam?student='. $student->ic .'">
                //                     <i class="fa fa-info">
                //                     </i>
                //                     Slip Exam
                //                 </a>
                //                 <!-- <a class="btn btn-danger btn-sm" href="#" onclick="deleteMaterial('. $student->ic .')">
                //                     <i class="ti-trash">
                //                     </i>
                //                     Delete
                //                 </a> -->
                //                 </td>
                            
                //             ';
                // }else{
                //     $content .= '<td class="project-actions text-right" >
                //     <a class="btn btn-secondary btn-sm btn-sm mr-2" href="#" onclick="getProgram(\''. $student->ic .'\')">
                //         <i class="ti-eye">
                //         </i>
                //         Program History
                //     </a>
                //     </td>
                
                // ';

                // }
            }
            $content .= '</tr></tbody>';

            return $content;

    }

    public function studentView()
    {

        return view('hostel.student.view.index');

    }

    public function getStudentListIndex2(Request $request)
    {
        $students = DB::connection('mysql2')->table('students')
            ->join('tblprogramme', 'students.program', 'tblprogramme.id')
            ->join('sessions AS a', 'students.intake', 'a.SessionID')
            ->join('sessions AS b', 'students.session', 'b.SessionID')
            ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
            ->select('students.*', 'tblprogramme.progname', 'a.SessionName AS intake', 
                     'b.SessionName AS session', 'tblstudent_status.name AS status')
            ->where('students.name', 'LIKE', "%".$request->search."%")
            ->orwhere('students.ic', 'LIKE', "%".$request->search."%")
            ->orwhere('students.no_matric', 'LIKE', "%".$request->search."%")->get();

        foreach($students as $key => $std)
        {

            $sponsor_id[$key] = DB::connection('mysql2')->table('tblpayment')
                                ->where([
                                    ['student_ic', $std->ic],
                                    ['sponsor_id', '!=', null]
                                    ])
                                ->latest('id')->first();

            if($sponsor_id[$key] != null)
            {

                $sponsor[$key] = DB::connection('mysql2')->table('tblpayment')
                                ->join('tblsponsor_library', 'tblpayment.payment_sponsor_id', 'tblsponsor_library.id')
                                ->where('tblpayment.id', $sponsor_id[$key]->sponsor_id)->pluck('tblsponsor_library.name')->first();

            }else{

                $sponsor[$key] = 'SENDIRI';

            }

            if($std->student_status == 1)
            {

                $student_status[$key] = 'Holding';

            }elseif($std->student_status == 2)
            {

                $student_status[$key] = 'Kuliah';

            }elseif($std->student_status == 4)
            {

                $student_status[$key] = 'Latihan Industri';

            }

        }

        $content = "";
        $content .= '<thead>
                        <tr>
                            <th style="width: 1%">
                                No.
                            </th>
                            <th>
                                Name
                            </th>
                            <th>
                                No. IC
                            </th>
                            <th>
                                No. Matric
                            </th>
                            <th>
                                Program
                            </th>
                            <th>
                                Intake
                            </th>
                            <th>
                                Current Session
                            </th>
                            <th>
                                Semester
                            </th>
                            <th>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table">';
                    
        foreach($students as $key => $student){
            //$registered = ($student->status == 'ACTIVE') ? 'checked' : '';
            $content .= '
            <tr>
                <td style="width: 1%">
                '. $key+1 .'
                </td>
                <td>
                '. $student->name .'
                </td>
                <td>
                '. $student->ic .'
                </td>
                <td>
                '. $student->no_matric .'
                </td>
                <td>
                '. $student->progname .'
                </td>
                <td>
                '. $student->intake .'
                </td>
                <td>
                '. $student->session .'
                </td>
                <td>
                '. $student->semester .'
                </td>';
                

            
                $content .= '<td class="project-actions text-right" >
                            <a class="btn btn-success btn-sm btn-sm mr-2 mb-2" href="/hostel/student/view/'. $student->ic .'">
                                <i class="ti-info-alt">
                                </i>
                                View
                            </a>
                            ';
                $content .= '</td>';
           
            }
            $content .= '</tr></tbody>';

            return $content;

    }

    public function getStudentDetails()
    {
        $student = DB::connection('mysql2')->table('students')
                   ->leftjoin('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                   ->leftjoin('tblstudent_address', 'students.ic', 'tblstudent_address.student_ic')
                   ->leftjoin('tblstudent_pass', 'students.ic', 'tblstudent_pass.student_ic')
                   ->leftjoin('student_form', 'students.ic', 'student_form.student_ic')
                   ->join('sessions', 'students.session', 'sessions.SessionID')
                   ->select('students.*', 'tblstudent_personal.*', 'tblstudent_address.*', 'tblstudent_pass.*', 'student_form.*', 'tblstudent_personal.state_id AS place_birth', 'sessions.SessionName AS session')
                   ->where('ic',request()->ic)->first();

        $data['waris'] = DB::connection('mysql2')->table('tblstudent_waris')->where('student_ic', $student->ic)->get();

        //dd($data['waris']);

        $program = DB::connection('mysql2')->table('tblprogramme')->get();

        $session = DB::connection('mysql2')->table('sessions')->get();

        $data['batch'] = DB::connection('mysql2')->table('tblbatch')->get();

        $data['state'] = DB::connection('mysql2')->table('tblstate')->orderBy('state_name')->get();

        $data['gender'] = DB::connection('mysql2')->table('tblsex')->get();

        $data['race'] = DB::connection('mysql2')->table('tblnationality')->orderBy('nationality_name')->get();

        $data['relationship'] = DB::connection('mysql2')->table('tblrelationship')->get();

        $data['wstatus'] = DB::connection('mysql2')->table('tblwaris_status')->get();

        $data['religion'] =  DB::connection('mysql2')->table('tblreligion')->orderBy('religion_name')->get();

        $data['CL'] = DB::connection('mysql2')->table('tblcitizenship_level')->get();

        $data['citizen'] = DB::connection('mysql2')->table('tblcitizenship')->get();

        $data['mstatus'] = DB::connection('mysql2')->table('tblmarriage')->get();

        $data['EA'] = DB::connection('mysql2')->table('tbledu_advisor')->get();

        $data['pass'] = DB::connection('mysql2')->table('tblpass_type')->get();

        $data['country'] = DB::connection('mysql2')->table('tblcountry')->get();
        
        $data['dun'] = DB::connection('mysql2')->table('tbldun')->orderBy('name')->get();

        $data['parlimen'] = DB::connection('mysql2')->table('tblparlimen')->orderBy('name')->get();

        $data['qualification'] = DB::connection('mysql2')->table('tblqualification_std')->get();

        return view('hostel.student.view.getStudentView', compact(['student','program','session','data']));

    }

    public function hostelCheckout()
    {

        return view('hostel.student.hostel_checkout.checkoutStudent');

    }

    public function debitNote()
    {

        return view('hostel.student.debit.debit');

    }

    public function getStudentDebit(Request $request)
    {

        $data['student'] = DB::connection('mysql2')->table('students')
                           ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
                           ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                           ->join('sessions AS t1', 'students.intake', 't1.SessionID')
                           ->join('sessions AS t2', 'students.session', 't2.SessionID')
                           ->select('students.*', 'tblstudent_status.name AS status', 'tblprogramme.progname AS program', 'students.program AS progid', 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
                           ->where('ic', $request->student)->first();

        // if(Auth::user()->usrtype == "AR")
        // {
        //     $items = [9,10,11,12,13,14,15,16,17,20,21,22,24,25,26,27];

        //     $data['type'] = DB::connection('mysql2')->table('tblstudentclaim')->whereNotIn('id', $items)->get();

        // }elseif(Auth::user()->usrtype == "HEA")
        // {
            $items = [31];

            $data['type'] = DB::connection('mysql2')->table('tblstudentclaim')->whereIn('id', $items)->get();

        // }else{

        //     $data['type'] = DB::connection('mysql2')->table('tblstudentclaim')->get();

        // }
        
        // $data['type'] = DB::connection('mysql2')->table('tblstudentclaim')->get();

        return view('hostel.student.debit.debitGetStudent', compact('data'));

    }

    public function storeDebit(Request $request)
    {

        $paymentData = $request->paymentData;

        $validator = Validator::make($request->all(), [
            'paymentData' => 'required'
        ]);

        if ($validator->fails()) {
            return ["message"=>"Field Error", "error" => $validator->messages()->get('*')];
        }

        try{ 
            DB::beginTransaction();
            DB::connection()->enableQueryLog();

            try{
                $payment = json_decode($paymentData);
                
                if($payment->type != null && $payment->unit != null && $payment->amount != null && $payment->remark != null)
                {
                    $stddetail = DB::connection('mysql2')->table('students')->where('ic', $payment->ic)->first();

                    $ref_no = DB::connection('mysql2')->table('tblref_no')
                      ->where('process_type_id', 4)->first();

                    DB::connection('mysql2')->table('tblref_no')->where('id', $ref_no->id)->update([
                        'ref_no' => $ref_no->ref_no + 1
                    ]);

                    $id = DB::connection('mysql2')->table('tblclaim')->insertGetId([
                        'student_ic' => $payment->ic,
                        'date' => date('Y-m-d'),
                        'ref_no' => $ref_no->code . $ref_no->ref_no + 1,
                        'session_id' => $stddetail->session,
                        'semester_id' => $stddetail->semester,
                        'program_id' => $stddetail->program,
                        'process_status_id' => 2,
                        'process_type_id' => 4,
                        'remark' => $payment->remark,
                        'add_staffID' => '000000000001',
                        'add_date' => date('Y-m-d'),
                        'mod_staffID' => '000000000001',
                        'mod_date' => date('Y-m-d')
                    ]);

                    DB::connection('mysql2')->table('tblclaimdtl')->insert([
                        'claim_id' => $id,
                        'claim_package_id' => $payment->type,
                        'price' => $payment->amount,
                        'unit' => $payment->unit,
                        'amount' => $payment->amount * $payment->unit,
                        'add_staffID' => '000000000001',
                        'add_date' => date('Y-m-d'),
                        'mod_staffID' => '000000000001',
                        'mod_date' => date('Y-m-d')
                    ]);
                
                }else{
                    return ["message" => "Please fill all required field!"];
                }
                
            }catch(QueryException $ex){
                DB::rollback();
                if($ex->getCode() == 23000){
                    return ["message"=>"Class code already existed inside the system"];
                }else{
                    Log::debug($ex);
                    return ["message"=>"DB Error"];
                }
            }

            DB::commit();
        }catch(Exception $ex){
            return ["message"=>"Error"];
        }

        return ["message" => "Success"];

    }

    public function getStudentInfo2(Request $request)
    {

        $data = DB::connection('mysql2')->table('students')
                ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
                ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                ->join('sessions AS t1', 'students.intake', 't1.SessionID')
                ->join('sessions AS t2', 'students.session', 't2.SessionID')
                ->select('students.*', 'tblstudent_status.name AS status', 'tblprogramme.progname AS program', 'students.program AS progid', 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
                ->where('ic', $request->student)->first();

        $info = DB::table('tblstudent_hostel')
            ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
            ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
            ->where([
                ['tblstudent_hostel.student_ic', $request->student],
                ['tblstudent_hostel.status', 'IN']
            ])
            ->select('tblstudent_hostel.id', 'tblblock.name', 'tblblock.location', 'tblblock_unit.no_unit')
            ->first();

        if (!$info) {
            $info = (object) [
                'name' => null,
                'location' => null,
                'no_unit' => null
            ];
        }

        $history = DB::table('tblstudent_hostel')
                ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
                ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
                ->join(DB::connection('mysql2')->getDatabaseName() . '.students as students', 'tblstudent_hostel.student_ic', '=', 'students.ic')
                ->where('tblstudent_hostel.student_ic', $request->student)
                ->orderBy('tblstudent_hostel.entry_date', 'DESC')
                ->select('tblblock.name as block', 'tblblock.location', 'tblblock_unit.no_unit', 'tblstudent_hostel.status', 'students.name')
                ->get();
            

        // return back()->with(['data' => $student]);

        // Return the data as part of the response
        return response()->json([
            'data' => $data,
            'info' => $info,
            'history' => $history,
        ]);

    }

    public function checkoutStudent(Request $request)
    {
        $json = json_decode($request->storeStudent, true);

        // Students that registered
        $check = DB::table('tblstudent_hostel')
                ->where([
                    ['student_ic', $json['student']],
                    ['status', 'IN']
                ])->first();

        // Update hostel status
        DB::table('tblstudent_hostel')
            ->where([
                ['student_ic', $json['student']],
                ['status', 'IN']
            ])
            ->update([
                'status' => 'OUT',
                'exit_date' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

        $history = DB::table('tblstudent_hostel')
            ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
            ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
            ->where('tblstudent_hostel.student_ic', $json['student'])
            ->select('tblstudent_hostel.id', 'tblstudent_hostel.student_ic', 'tblblock.name AS block', 'tblblock.location', 'tblblock_unit.no_unit', 'tblstudent_hostel.status')
            ->get();

        $history->map(function($hostel) {
            $student = DB::connection('mysql2')->table('students')->where('ic', $hostel->student_ic)->first();
            $hostel->name = $student ? $student->name : null;
            return $hostel;
        });

        $data = DB::connection('mysql2')->table('students')
                ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
                ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                ->join('sessions AS t1', 'students.intake', 't1.SessionID')
                ->join('sessions AS t2', 'students.session', 't2.SessionID')
                ->select('students.*', 'tblstudent_status.name AS statusName', 'tblprogramme.progname AS program', 'students.program AS progid', 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
                ->where('ic', $json['student'])->first();

        $info = DB::table('tblstudent_hostel')
            ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
            ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
            ->where([
                ['tblstudent_hostel.id', $json['id']]
            ])
            ->select('tblstudent_hostel.id', 'tblblock.name', 'tblblock.location', 'tblblock_unit.no_unit')
            ->first();

        return response()->json(['message' => "Success", 'data' => $data, 'info' => $info, 'history' => $history]);
    }

    public function printStudentSlip($student)
    {
        // Get student information
        $data['student'] = DB::connection('mysql2')->table('students')
                ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
                ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                ->join('sessions AS t1', 'students.intake', 't1.SessionID')
                ->join('sessions AS t2', 'students.session', 't2.SessionID')
                ->select('students.*', 'tblstudent_status.name AS statusName', 'tblprogramme.progname AS program', 
                         'tblprogramme.progcode AS program_code', 'students.program AS progid', 
                         't1.SessionName AS intake_name', 't2.SessionName AS session_name')
                ->where('ic', $student)->first();

        // Get hostel information
        $data['hostel'] = DB::table('tblstudent_hostel')
            ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
            ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
            ->where([
                ['tblstudent_hostel.student_ic', $student],
                ['tblstudent_hostel.status', 'IN']
            ])
            ->select('tblstudent_hostel.id', 'tblblock.name', 'tblblock.location', 'tblblock_unit.no_unit', 
                    'tblstudent_hostel.entry_date', 'tblstudent_hostel.exit_date')
            ->first();

        if (!$data['hostel']) {
            // If the student has been checked out, get the most recent record
            $data['hostel'] = DB::table('tblstudent_hostel')
                ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
                ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
                ->where('tblstudent_hostel.student_ic', $student)
                ->orderBy('tblstudent_hostel.exit_date', 'desc')
                ->select('tblstudent_hostel.id', 'tblblock.name', 'tblblock.location', 'tblblock_unit.no_unit', 
                        'tblstudent_hostel.entry_date', 'tblstudent_hostel.exit_date')
                ->first();
        }

        return view('hostel.student.printStudentSlip', compact('data'));
    }

    public function quotationStudentSlip($student)
    {
        $data = DB::connection('mysql2')->table('students')
            ->leftJoin('tblpayment_method', 'tblpayment_method.id', '=', DB::raw('1')) // Assuming you want the method with id 1
            ->leftJoin('tblstudentclaim', 'tblstudentclaim.id', '=', DB::raw('79')) // Assuming you want the type with id 79
            ->where('students.ic', $student)
            ->select('students.*', 'tblpayment_method.name AS payment_method_id', 'tblstudentclaim.name AS claim_type_id')
            ->first();

        return response()->json(['data' => $data]);
    }

    public function chargeStudentSlip($student)
    {
        
        $stddetail = DB::connection('mysql2')->table('students')->where('ic', $student)->first();

        // Check if payment has already been made today
        $existingPayment = DB::connection('mysql2')->table('tblpayment')
            ->where('student_ic', $student)
            ->whereDate('date', date('Y-m-d'))
            ->first();

        if ($existingPayment) {
            return response()->json(['message' => "Error: Payment has already been made today."], 400);
        }

        $ref_no = DB::connection('mysql2')->table('tblref_no')
                      ->where('process_type_id', 8)
                      ->select('tblref_no.*')->first();

        $payment = DB::connection('mysql2')->table('tblpayment')->insertGetId([
            'student_ic' => $student,
            'date' => date('Y-m-d'),
            'ref_no' => $ref_no->code . $ref_no->ref_no + 1,
            'program_id' => $stddetail->program,
            'session_id' => $stddetail->session,
            'semester_id' => $stddetail->semester,
            'amount' => 5,
            'process_status_id' => 2,
            'process_type_id' => 8,
            'add_staffID' => '123455432123',
            'add_date' => date('Y-m-d'),
            'mod_staffID' => '123455432123',
            'mod_date' => date('Y-m-d')
        ]);

        DB::connection('mysql2')->table('tblpaymentmethod')->insertGetId([
            'payment_id' => $payment,
            'claim_method_id' => 1,
            'bank_id' => null,
            'no_document' => null,
            'amount' => 5,
            'add_staffID' => '123455432123',
            'add_date' => date('Y-m-d'),
            'mod_staffID' => '123455432123',
            'mod_date' => date('Y-m-d')
        ]);

        DB::connection('mysql2')->table('tblpaymentdtl')->insert([
            'payment_id' => $payment,
            'claim_type_id' => 79,
            'amount' => 5,
            'add_staffID' => '123455432123',
            'add_date' => date('Y-m-d'),
            'mod_staffID' => '123455432123',
            'mod_date' => date('Y-m-d')
        ]);

        DB::connection('mysql2')->table('tblref_no')->where('id', $ref_no->id)->update([
            'ref_no' => $ref_no->ref_no + 1
        ]);

        return response()->json(['message' => "Success", 'id' => $payment]);

    }

    public function getReceipt(Request $request)
    {
        $data['payment'] = DB::connection('mysql2')->table('tblpayment')->where('tblpayment.id', $request->id)
                           ->join('sessions AS A2', 'tblpayment.session_id', 'A2.SessionID')
                           ->join('tblprogramme', 'tblpayment.program_id', 'tblprogramme.id')
                           ->select('tblpayment.*', 'tblprogramme.progname AS program', 'A2.SessionName AS session')
                           ->first();

        $data['staff'] = DB::connection('mysql2')->table('users')->where('ic', $data['payment']->add_staffID)->first();

        $detail = DB::connection('mysql2')->table('tblpaymentdtl')
                          ->join('tblstudentclaim', 'tblpaymentdtl.claim_type_id', 'tblstudentclaim.id')
                          ->where('tblpaymentdtl.payment_id', $request->id)
                          ->where('tblpaymentdtl.amount', '!=', 0.00)
                          ->select('tblpaymentdtl.id', 'tblpaymentdtl.payment_id', 'tblpaymentdtl.claim_type_id', 
                                  'tblpaymentdtl.add_staffID', 'tblpaymentdtl.add_date', 'tblpaymentdtl.mod_staffID',
                                  'tblpaymentdtl.mod_date', DB::raw('SUM(tblpaymentdtl.amount) AS total_amount'), 
                                  'tblstudentclaim.name', 'tblstudentclaim.groupid')
                          ->groupBy('tblpaymentdtl.id', 'tblpaymentdtl.payment_id', 'tblpaymentdtl.claim_type_id',
                                   'tblpaymentdtl.add_staffID', 'tblpaymentdtl.add_date', 'tblpaymentdtl.mod_staffID',
                                   'tblpaymentdtl.mod_date', 'tblstudentclaim.name', 'tblstudentclaim.groupid');
                          
        $data['detail'] = $detail->get();

        $data['total'] = DB::connection('mysql2')->table('tblpaymentdtl')
                        ->where('tblpaymentdtl.payment_id', $request->id)
                        ->sum('tblpaymentdtl.amount');


        $method = DB::connection('mysql2')->table('tblpaymentmethod')
                          ->join('tblpayment_method', 'tblpaymentmethod.claim_method_id', 'tblpayment_method.id')
                          ->leftjoin('tblpayment_bank', 'tblpaymentmethod.bank_id', 'tblpayment_bank.id')
                          ->where('tblpaymentmethod.payment_id', $request->id)
                          ->select('tblpaymentmethod.id', 'tblpaymentmethod.claim_method_id', 'tblpaymentmethod.bank_id', 
                                  'tblpaymentmethod.no_document', 'tblpayment_method.name AS method', 
                                  'tblpayment_bank.name AS bank', DB::raw('SUM(tblpaymentmethod.amount) AS amount'))
                          ->groupBy('tblpaymentmethod.id', 'tblpaymentmethod.claim_method_id', 'tblpaymentmethod.bank_id',
                                   'tblpaymentmethod.no_document', 'tblpayment_method.name', 'tblpayment_bank.name');
                          
        $data['method'] = $method->get();

        $data['total2'] = $method->sum('tblpaymentmethod.amount');

        $data['student'] = DB::connection('mysql2')->table('students')
                           ->join('sessions AS A1', 'students.intake', 'A1.SessionID')
                           ->join('sessions AS A2', 'students.session', 'A2.SessionID')
                           ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                           ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
                           ->select('students.*', 'tblprogramme.progname AS program', 'tblstudent_status.name AS status', 'A1.SessionName AS intake', 'A2.SessionName AS session')
                           ->where('students.ic', $data['payment']->student_ic)
                           ->first();

        $data['date'] = Carbon::createFromFormat('Y-m-d', $data['payment']->date)->format('d/m/Y');

        return view('hostel.student.receipt', compact('data'));

    }

    public function studentHostelReport()
    {

        $data['block'] = DB::table('tblblock')->get();

        foreach($data['block'] as $key => $blk)
        {

            $data['unit'][$key] = DB::table('tblblock_unit')
                                  ->leftjoin('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                                  ->where('tblblock_unit.block_id', $blk->id)
                                  ->select('tblblock_unit.*', 'tblresident.name AS resident')
                                  ->get();


            foreach($data['unit'][$key] as $key2 => $ut)
            {

                $data['resident'][$key][$key2] = DB::table('tblstudent_hostel')
                                    ->where('block_unit_id', $ut->id)
                                    ->where('status', 'IN')
                                    ->select(DB::raw('COUNT(student_ic) AS total_resident'))
                                    ->first();

            }

        }

        
        //summary

        $data['summary'] = DB::table('tblblock_unit')
                           ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                           ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                           ->groupBy('tblresident.name', 'tblblock.location')
                           ->where('tblresident.name', 'LIKE', '%LELAKI%')
                           ->orWhere('tblresident.name', 'LIKE', '%PEREMPUAN%')
                           ->select('tblresident.name AS resident', 'tblblock.location')
                           ->get();

        foreach($data['summary'] as $key => $sm)
        {

            $data['capacity'][$key] = DB::table('tblblock_unit')
                                    ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                                    ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                                    ->where([
                                        ['tblresident.name', $sm->resident],
                                        ['tblblock.location', $sm->location]
                                    ])
                                    ->select(DB::raw('SUM(tblblock_unit.capacity) AS total'))
                                    ->first();

            $data['resident2'][$key] = DB::table('tblblock_unit')
                                    ->join('tblstudent_hostel', 'tblblock_unit.id', 'tblstudent_hostel.block_unit_id')
                                    ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                                    ->join('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                                    ->where([
                                        ['tblresident.name', $sm->resident],
                                        ['tblblock.location', $sm->location],
                                        ['tblstudent_hostel.status', 'IN']
                                    ])
                                    ->select(DB::raw('COUNT(tblstudent_hostel.student_ic) AS total'))
                                    ->first();

            $data['vacancy'][$key] = $data['capacity'][$key]->total - $data['resident2'][$key]->total;

            

        }

        
        $data['summary2'] = DB::table('tblblock')
                            ->groupBy('tblblock.location')
                            ->select('tblblock.location AS location')
                            ->get();

                        

        foreach($data['summary2'] as $key => $sm)
        {

            $baseQuery = function() use ($sm){

                return DB::table('tblblock_unit')
                    ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                    ->where([
                        ['tblblock.location', $sm->location]
                    ]);

            };

            $data['capacity2'][$key] = ($baseQuery)()
                                ->select(DB::raw('SUM(tblblock_unit.capacity) AS total'))
                                ->first();

            $data['resident3'][$key] = ($baseQuery)()
                                ->join('tblstudent_hostel', 'tblblock_unit.id', 'tblstudent_hostel.block_unit_id')
                                ->where('tblstudent_hostel.status', 'IN')
                                ->select(DB::raw('COUNT(tblstudent_hostel.student_ic) AS total'))
                                ->first();

            $data['vacancy2'][$key] = $data['capacity2'][$key]->total - $data['resident3'][$key]->total;

        }


        //dd($data['resident3']);

        if(isset(request()->print))
        {

            return view('hostel.report.student_hostel.printStudentHostelReport', compact('data'));

        }else{

            return view('hostel.report.student_hostel.index', compact('data'));

        }

    }

    public function unitStatus()
    {

        return view('hostel.report.unit_status.unitStatus');

    }

    public function getBlockList()
    {

        $block = DB::table('tblblock')->where('name', 'LIKE', "%".request()->search."%")->get();

        return response()->json($block);

    }

    public function getUnitList(Request $request)
    {

        $data['unit'] = DB::table('tblblock_unit')
                                  ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                                  ->leftjoin('tblresident', 'tblblock_unit.resident_id', 'tblresident.id')
                                  ->where('tblblock_unit.block_id', $request->block)
                                  ->select('tblblock_unit.*', 'tblblock.name','tblresident.name AS resident')
                                  ->get();


        foreach($data['unit'] as $key => $ut)
        {

            $data['resident'][$key] = DB::table('tblstudent_hostel')
                                ->where('block_unit_id', $ut->id)
                                ->where('status', 'IN')
                                ->select(DB::raw('COUNT(student_ic) AS total_resident'))
                                ->first();

        }

        $content = "";
        $content .='<thead>
                        <tr>
                            <th>
                                No.
                            </th>
                            <th>
                                Blok
                            </th>
                            <th>
                                No. Unit
                            </th>
                            <th>
                                Jumlah Penghuni
                            </th>
                            <th>
                                Kapasiti
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="unit-table">';
        foreach($data['unit'] as $key => $ut){
            $content .= '<tr>
                <td style="width: 1%">
                '. $key+1 .'
                </td>
                <td>
                '. $ut->name .'
                </td>
                <td>
                '. $ut->no_unit .'
                </td>
                <td>
                '. $data['resident'][$key]->total_resident .'
                </td>
                <td>
                '. $ut->capacity .'
                </td>
                <td>
                '. (($data['resident'][$key]->total_resident < $ut->capacity) ? "ADA KEKOSONGAN" : "PENUH") .'
                </td>';
                
                $content .= '<td class="project-actions text-right" >
                            <a class="btn btn-info btn-sm btn-sm mr-2 mb-2" href="#" onclick="getResidentList(\''. $ut->id .'\')">
                                <i class="ti-ruler-pencil">
                                </i>
                                Senarai Penghuni
                            </a>
                            </td>
            </tr>';
           
        }
        $content .=' </tbody>';

        return $content;

    }

    public function getResidentList(Request $request)
    {

        // Step 1: Fetch the student data from the mysql2 connection
        $students = DB::connection('mysql2')->table('students')
        ->select('ic', 'name')
        ->get();

        $students = DB::connection('mysql2')->table('students')
        ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
        ->join('tblprogramme', 'students.program', 'tblprogramme.id')
        ->join('sessions AS t1', 'students.intake', 't1.SessionID')
        ->join('sessions AS t2', 'students.session', 't2.SessionID')
        ->select('students.ic', 'students.name', 'students.no_matric', 'students.semester', 'tblstudent_status.name AS status', 
                 'tblprogramme.progcode AS program', 'students.program AS progid', 
                 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
        ->get();

        // Step 2: Create an array of student_ic to use in the next query
        $studentIcs = $students->pluck('ic')->toArray();

        // Step 3: Fetch the hostel data and join with the block unit table
        $hostelData = DB::table('tblstudent_hostel')
        ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
        ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
        ->where('tblstudent_hostel.block_unit_id', $request->id)
        ->where('tblstudent_hostel.status', 'IN')
        ->whereIn('tblstudent_hostel.student_ic', $studentIcs)
        ->select('tblstudent_hostel.*', 'tblblock_unit.no_unit', DB::raw('CONCAT(tblblock.name, " - ", tblblock.location) AS block_unit'))
        ->get();

        // Step 4: Add the student names to the hostel data
        $hostelData->map(function($hostel) use ($students) {
        $student = $students->firstWhere('ic', $hostel->student_ic);
        $hostel->name = $student ? $student->name : null;
        $hostel->no_matric = $student ? $student->no_matric : null;
        $hostel->program = $student ? $student->program : null;
        $hostel->session_name = $student ? $student->session_name : null;
        $hostel->semester = $student ? $student->semester : null;
        return $hostel;
        });

        $data['student'] = $hostelData;

        $content = "";
        $content .='<div class="card-body p-0">
          <table id="student_list" class="table table-striped projects display dataTable"><thead>
                        <tr>
                            <th>
                                No.
                            </th>
                            <th>
                                Nama Pelajar
                            </th>
                            <th>
                                No. Matriks
                            </th>
                            <th>
                                Program
                            </th>
                            <th>
                                Sesi Semasa
                            </th>
                            <th>
                                Semester
                            </th>
                            <th>
                                Block
                            </th>
                            <th>
                                Unit
                            </th>
                        </tr>
                    </thead>
                    <tbody id="unit-table">';
        foreach($data['student'] as $key => $std){
            $content .= '<tr>
                <td style="width: 1%">
                '. $key+1 .'
                </td>
                <td>
                '. $std->name .'
                </td>
                <td>
                '. $std->no_matric .'
                </td>
                <td>
                '. $std->program .'
                </td>
                <td>
                '. $std->session_name .'
                </td>
                <td>
                '. $std->semester .'
                </td>
                <td>
                '. $std->block_unit .'
                </td>
                <td>
                '. $std->no_unit .'
                </td>
            </tr>';
           
        }
        $content .=' </tbody>
        </table>
        </div>';

        return $content;

    }

    public function studentReport()
    {
        $data['ms1'] = [];

        $data['sum'] = [];

        $data['program'] = DB::connection('mysql2')->table('tblprogramme')
                           ->join('tblfaculty', 'tblprogramme.facultyid', 'tblfaculty.id')
                           ->select('tblprogramme.*', 'tblfaculty.facultyname', 'tblfaculty.facultycode')->get();

        $data['faculty'] = DB::connection('mysql2')->table('tblfaculty')->get();

        // $data['sessions'] = DB::connection('mysql2')->table('sessions')
        //                     ->join('students', 'sessions.SessionID', 'students.intake')
        //                     ->where('students.semester', 1)
        //                     ->where('students.status', 2)
        //                     ->groupBy('sessions.SessionID')
        //                     ->select('sessions.*')
        //                     ->get();

        //dd($data['sessions']);

        foreach($data['faculty'] as $fcl)
        {

            $data['count'][] = count(DB::connection('mysql2')->table('tblprogramme')->where('facultyid', $fcl->id)->get());

        }

        //dd($data['count']);

        foreach($data['program'] as $key => $prg)
        {

            $data['sum'][$key] = count(DB::connection('mysql2')->table('students')
                                       ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                       ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                                       ->join('tblfaculty', 'tblprogramme.facultyid', 'tblfaculty.id')
                                       ->where('tblfaculty.id', $prg->facultyid)
                                       ->get());


            $data['holding_m1'][$key] = count(DB::connection('mysql2')->table('students')
                                       ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                       ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                       ->where([
                                       ['students.program', $prg->id],
                                    //    ['students.status', 2],
                                    //    ['students.student_status', 1],
                                    //    ['tblstudent_personal.sex_id', 1],
                                       ['tblstudent_hostel.status', 'IN']
                                       ])->get());
   
            $data['holding_f1'][$key] = count(DB::connection('mysql2')->table('students')
                                       ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                       ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                       ->where([
                                       ['students.program', $prg->id],
                                       ['students.status', 2],
                                       ['students.student_status', 1],
                                       ['tblstudent_personal.sex_id', 2]
                                       ])->get());

            $data['ms1'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                 ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                 ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 1],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());
            
            $data['fs1'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                 ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                 ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 1],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());
            
            $data['ms2'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 2],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs2'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 2],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['ms3'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 3],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs3'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 3],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());
                                    
            $data['ms4'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 4],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs4'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 4],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['ms5'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                     ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 5],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs5'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                     ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 5],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['ms6'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 6],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs6'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 6],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['ms7'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 7],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs7'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 7],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['ms8'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 8],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 1],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['fs8'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->join('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.semester', 8],
                                    // ['students.status', 2],
                                    // ['students.student_status', 2],
                                    ['tblstudent_personal.sex_id', 2],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['industry'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    // ['students.status', 2],
                                    // ['students.student_status', 4],
                                    // ['students.campus_id', 1],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['active'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->orderBy('tblstudent_hostel.id', 'DESC')
                                    ->where([
                                    ['students.program', $prg->id],
                                    // ['students.status', 2],
                                    // ['students.campus_id', 1],
                                    // ['students.student_status', 2],
                                    ['tblstudent_hostel.status', 'IN']
                                    ])->get());

            $data['active_leave'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.status', 2],
                                    ['students.campus_id', 0]
                                    ])->whereIn('students.student_status', [2,4])->get());
                                    
            $data['postpone'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.status', 6]
                                    ])->get());

            $data['dismissed'][$key] = count(DB::connection('mysql2')->table('students')
            ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
                                    ->where([
                                    ['students.program', $prg->id],
                                    ['students.status', 4]
                                    ])->get());

                                 

        }

        // foreach($data['sessions'] as $key => $ses)
        // {

        //     $data['holding'][$key] = count(DB::connection('mysql2')->table('students')
        //     ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
        //                                    ->where([
        //                                     ['students.semester', 1],
        //                                     ['students.status', 2],
        //                                     ['students.student_status', 1],
        //                                     ['students.campus_id', 1],
        //                                     ['students.intake', $ses->SessionID]
        //                                    ])->get());

        //     $data['kuliah'][$key] = count(DB::connection('mysql2')->table('students')
        //     ->join(DB::connection()->getDatabaseName() . '.tblstudent_hostel', 'students.ic', '=', 'tblstudent_hostel.student_ic')
        //                                    ->where([
        //                                     ['students.semester', 1],
        //                                     ['students.status', 2],
        //                                     ['students.student_status', 2],
        //                                     ['students.campus_id', 1],
        //                                     ['students.intake', $ses->SessionID]
        //                                    ])->get());

        // }

        return view('hostel.report.student_report.studentReport', compact('data'));

    }

    public function studentReportRs()
    {

        $data['session'] = DB::connection('mysql2')->table('sessions')->get();

        return view('hostel.report.reportRs.reportRs', compact('data'));

    }

    public function getStudentReportRs(Request $request)
    {

        if($request->from && $request->to)
        {

            
            $data['R1M'] = 0;
            $data['R1F'] = 0;

            $data['R2M'] = 0;
            $data['R2F'] = 0;

            $data['WM'] = 0;
            $data['WF'] = 0;

            $data['NAM'] = 0;
            $data['NAF'] = 0;

            // Define a function to create the base query
            $baseQuery = function () use ($request) {
                return DB::connection('mysql2')->table('students')
                    ->leftjoin('tblstudent_personal', 'students.ic', '=', 'tblstudent_personal.student_ic')
                    ->leftjoin('sessions', 'students.intake', '=', 'sessions.SessionID')
                    ->leftjoin('tblprogramme', 'students.program', '=', 'tblprogramme.id')
                    ->leftjoin('tbledu_advisor', 'tblstudent_personal.advisor_id', '=', 'tbledu_advisor.id')
                    ->leftjoin('tblsex', 'tblstudent_personal.sex_id', '=', 'tblsex.id')
                    ->leftjoin('tblstudent_status', 'students.status', '=', 'tblstudent_status.id')
                    ->where('students.semester', 1)
                    ->whereBetween('students.date_offer', [$request->from, $request->to])
                    ->select(
                        'students.*', 'tblstudent_personal.no_tel', 'tblstudent_personal.qualification', 'sessions.SessionName',
                        'tblprogramme.progcode', 'tbledu_advisor.name AS ea', 'tblsex.code AS sex',
                        'tblstudent_status.name AS status'
                    );
            };

            // Use the base query for studentOne
            $studentOneQuery = ($baseQuery)()->where('students.status', 1);
            $data['studentR1'] = $studentOneQuery->get();

            // Use the base query for studentR2
            $data['studentR2'] = ($baseQuery)()
                ->wherein('students.status', [2,6,16,17])
                ->get();

            // Use the base query for studentR2
            $data['withdraw'] = ($baseQuery)()
                ->wherein('students.status', [4])
                ->get();

            // Use the base query for studentR2
            $data['notActive'] = ($baseQuery)()
                ->wherein('students.status', [14])
                ->get();

            $data['ref1'] = [];
            $data['ref2'] = [];

            foreach($data['studentR1'] as $key => $student)
            {

                $results = [];

                $data['resultR1'][] = DB::connection('mysql2')->table('tblpayment')
                                ->leftjoin('tblpaymentdtl', 'tblpayment.id', 'tblpaymentdtl.payment_id')
                                ->leftjoin('tblstudentclaim', 'tblpaymentdtl.claim_type_id', 'tblstudentclaim.id')
                                ->where('tblpayment.student_ic', $student->ic)
                                ->where('tblpayment.process_status_id', 2)
                                ->whereNotIn('tblpayment.process_type_id', [8])
                                ->whereNotIn('tblstudentclaim.groupid', [4,5])
                                ->select(

                                    DB::connection('mysql2')->raw('CASE
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) < 250 THEN "R"
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) >= 250 THEN "R1"
                                            END AS group_alias'),
                                    DB::connection('mysql2')->raw('IFNULL(SUM(tblpaymentdtl.amount), 0) AS amount')

                                )->first();

                if($student->sex == 'L')
                {
                    $data['R1M'] = $data['R1M'] + 1;

                }elseif($student->sex == 'P') 
                {

                    $data['R1F'] = $data['R1F'] + 1;
                    
                }

                $data['quaR1'][$key] = DB::connection('mysql2')->table('tblqualification_std')->where('id', $student->qualification)->value('name');

            }

            foreach($data['studentR2'] as $key => $student)
            {

                $results = [];

                $data['resultR2'][] = DB::connection('mysql2')->table('tblpayment')
                                    ->leftjoin('tblpaymentdtl', 'tblpayment.id', 'tblpaymentdtl.payment_id')
                                    ->leftjoin('tblstudentclaim', 'tblpaymentdtl.claim_type_id', 'tblstudentclaim.id')
                                    ->where('tblpayment.student_ic', $student->ic)
                                    ->where('tblpayment.process_status_id', 2)
                                    ->whereNotIn('tblpayment.process_type_id', [8])
                                    ->whereNotIn('tblstudentclaim.groupid', [4,5])
                                    ->select(

                                    DB::raw('CASE
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) < 250 THEN "R"
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) >= 250 THEN "R1"
                                                END AS group_alias'),
                                    DB::raw('IFNULL(SUM(tblpaymentdtl.amount), 0) AS amount')

                                    )->first();

                if($student->sex == 'L')
                {
                    $data['R2M'] = $data['R2M'] + 1;

                }elseif($student->sex == 'P') 
                {

                    $data['R2F'] = $data['R2F'] + 1;
                    
                }

                $data['quaR2'][$key] = DB::connection('mysql2')->table('tblqualification_std')->where('id', $student->qualification)->value('name');

            }

            foreach($data['withdraw'] as $key => $student)
            {

                $results = [];

                $data['resultWithdraw'][] = DB::connection('mysql2')->table('tblpayment')
                                ->leftjoin('tblpaymentdtl', 'tblpayment.id', 'tblpaymentdtl.payment_id')
                                ->leftjoin('tblstudentclaim', 'tblpaymentdtl.claim_type_id', 'tblstudentclaim.id')
                                ->where('tblpayment.student_ic', $student->ic)
                                ->where('tblpayment.process_status_id', 2)
                                ->whereNotIn('tblpayment.process_type_id', [8])
                                ->whereNotIn('tblstudentclaim.groupid', [4,5])
                                ->select(

                                    DB::raw('CASE
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) < 250 THEN "R"
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) >= 250 THEN "R1"
                                            END AS group_alias'),
                                    DB::raw('IFNULL(SUM(tblpaymentdtl.amount), 0) AS amount')

                                )->first();

                if($student->sex == 'L')
                {
                    $data['WM'] = $data['WM'] + 1;

                }elseif($student->sex == 'P') 
                {

                    $data['WF'] = $data['WF'] + 1;
                    
                }

                $data['quaW'][$key] = DB::connection('mysql2')->table('tblqualification_std')->where('id', $student->qualification)->value('name');

            }

            foreach($data['notActive'] as $key => $student)
            {

                $results = [];

                $data['resultNA'][] = DB::connection('mysql2')->table('tblpayment')
                                ->leftjoin('tblpaymentdtl', 'tblpayment.id', 'tblpaymentdtl.payment_id')
                                ->leftjoin('tblstudentclaim', 'tblpaymentdtl.claim_type_id', 'tblstudentclaim.id')
                                ->where('tblpayment.student_ic', $student->ic)
                                ->where('tblpayment.process_status_id', 2)
                                ->whereNotIn('tblpayment.process_type_id', [8])
                                ->whereNotIn('tblstudentclaim.groupid', [4,5])
                                ->select(

                                    DB::raw('CASE
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) < 250 THEN "R"
                                                WHEN IFNULL(SUM(tblpaymentdtl.amount), 0) >= 250 THEN "R1"
                                            END AS group_alias'),
                                    DB::raw('IFNULL(SUM(tblpaymentdtl.amount), 0) AS amount')

                                )->first();

                if($student->sex == 'L')
                {
                    $data['NAM'] = $data['NAM'] + 1;

                }elseif($student->sex == 'P') 
                {

                    $data['NAF'] = $data['NAF'] + 1;
                    
                }

                $data['quaNA'][$key] = DB::connection('mysql2')->table('tblqualification_std')->where('id', $student->qualification)->value('name');

            }

            return view('hostel.report.reportRs.getReportRs', compact('data'));

        }

    }

    public function receiptList()
    {

        return view('hostel.student.receipt_list.receiptList');

    }

    public function getReceiptList(Request $request)
    {

        if($request->refno != '')
        {
            // For OTR, HEA, TS users - only get tblclaim data
            $data['student'] = DB::connection('mysql2')->table('tblclaim')
            ->join('students', 'tblclaim.student_ic', 'students.ic')
            ->join('tblprocess_status', 'tblclaim.process_status_id', 'tblprocess_status.id')
            ->join('tblclaimdtl', 'tblclaim.id', 'tblclaimdtl.claim_id')
            ->where('tblclaim.ref_no', 'LIKE', $request->refno."%")
            ->where('tblclaim.process_status_id', 2)
            ->groupBy('tblclaim.id', 'tblclaim.remark', 'tblclaim.date', 'tblclaim.ref_no', 'tblclaim.process_type_id', 'tblprocess_status.name', 'students.no_matric', 'students.name', 'students.ic')
            ->select('tblclaim.id', 'tblclaim.remark', 'tblclaim.date AS unified_date', 'tblclaim.ref_no','tblclaim.date AS date', 'tblclaim.process_type_id', DB::raw('SUM(tblclaimdtl.amount) AS amount'), 'tblprocess_status.name AS status', 'students.no_matric', 'students.name AS name', 'students.ic')
            ->orderBy('unified_date', 'desc')
            ->get();

        }elseif($request->search != '')
        {
            // For OTR, HEA, TS users - only get tblclaim data
            $data['student'] = DB::connection('mysql2')->table('tblclaim')
            ->join('students', 'tblclaim.student_ic', 'students.ic')
            ->leftjoin('tblprocess_status', 'tblclaim.process_status_id', 'tblprocess_status.id')
            ->leftjoin('tblclaimdtl', 'tblclaim.id', 'tblclaimdtl.claim_id')
            ->where('students.name', 'LIKE', $request->search."%")
            ->orwhere('students.ic', 'LIKE', $request->search."%")
            ->orwhere('students.no_matric', 'LIKE', $request->search."%")
            ->where('tblclaim.process_status_id', 2)
            ->groupBy('tblclaim.id')
            ->select('tblclaim.id', 'tblclaim.remark', 'tblclaim.date AS unified_date', 'tblclaim.ref_no','tblclaim.date AS date', 'tblclaim.process_type_id', DB::raw('SUM(tblclaimdtl.amount) AS amount'), 'tblprocess_status.name AS status', 'students.no_matric', 'students.name AS name', 'students.ic')
            ->orderBy('unified_date', 'desc')
            ->get();

        }else{

            return false;

        }

        if(isset($request->cancel))
        {

            return view('hostel.student.receipt_list.getReceiptList', compact('data'));

        }else{

            return view('hostel.student.receipt_list.getReceiptList', compact('data'));

        }

    }

}
