<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
                            \Log::debug($ex);
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
                            \Log::debug($ex);
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
                            \Log::debug($ex);
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
                            \Log::debug($ex);
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

    public function hostelCheckout()
    {

        return view('hostel.student.hostel_checkout.checkoutStudent');

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

        $datas = json_decode($request->storeStudent);

            if($datas->id)
            {

                try{ 
                    DB::beginTransaction();
                    DB::connection()->enableQueryLog();

                    try{

                    DB::table('tblstudent_hostel')->where('id', $datas->id)
                    ->update([
                        'status' => 'OUT',
                        'exit_date' => now()
                    ]);

                    $alert = "Success";

                    }catch(QueryException $ex){
                        DB::rollback();
                        if($ex->getCode() == 23000){
                            return ["message"=>"Class code already existed inside the system"];
                        }else{
                            \Log::debug($ex);
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

            $data = DB::connection('mysql2')->table('students')
                ->join('tblstudent_status', 'students.status', 'tblstudent_status.id')
                ->join('tblprogramme', 'students.program', 'tblprogramme.id')
                ->join('sessions AS t1', 'students.intake', 't1.SessionID')
                ->join('sessions AS t2', 'students.session', 't2.SessionID')
                ->select('students.*', 'tblstudent_status.name AS status', 'tblprogramme.progname AS program', 'students.program AS progid', 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
                ->where('ic', $datas->student)->first();

            $info = DB::table('tblstudent_hostel')
                ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
                ->join('tblblock', 'tblblock_unit.block_id', '=', 'tblblock.id')
                ->where([
                    ['tblstudent_hostel.student_ic', $datas->student],
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
                    ->where('tblstudent_hostel.student_ic', $datas->student)
                    ->orderBy('tblstudent_hostel.entry_date', 'DESC')
                    ->select('tblblock.name as block', 'tblblock.location', 'tblblock_unit.no_unit', 'tblstudent_hostel.status', 'students.name')
                    ->get();
                

            // return back()->with(['data' => $student]);

            // Return the data as part of the response
            return response()->json([
                'message' => $alert,
                'data' => $data,
                'info' => $info,
                'history' => $history,
            ]);

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
                    ->join('tblstudent_hostel', 'tblblock_unit.id', 'tblstudent_hostel.block_unit_id')
                    ->join('tblblock', 'tblblock_unit.block_id', 'tblblock.id')
                    ->where([
                        ['tblblock.location', $sm->location]
                    ]);

            };

            $data['capacity2'][$key] = ($baseQuery)()
                                ->select(DB::raw('SUM(tblblock_unit.capacity) AS total'))
                                ->groupBy('tblblock_unit.id')
                                ->first();

            $data['resident3'][$key] = ($baseQuery)()
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
                 'tblprogramme.progname AS program', 'students.program AS progid', 
                 't1.SessionName AS intake_name', 't2.SessionName AS session_name')
        ->get();

        // Step 2: Create an array of student_ic to use in the next query
        $studentIcs = $students->pluck('ic')->toArray();

        // Step 3: Fetch the hostel data and join with the block unit table
        $hostelData = DB::table('tblstudent_hostel')
        ->join('tblblock_unit', 'tblstudent_hostel.block_unit_id', '=', 'tblblock_unit.id')
        ->where('tblstudent_hostel.block_unit_id', $request->id)
        ->where('tblstudent_hostel.status', 'IN')
        ->whereIn('tblstudent_hostel.student_ic', $studentIcs)
        ->select('tblstudent_hostel.*', 'tblblock_unit.no_unit')
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
            </tr>';
           
        }
        $content .=' </tbody>
        </table>
        </div>';

        return $content;

    }

}
