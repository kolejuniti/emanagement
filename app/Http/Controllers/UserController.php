<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function indexUser()
    {
        $year = DB::connection('second_db')->table('tblyear')->get();
        
        $program = DB::connection('second_db')->table('tblprogramme')->get();

        $session = DB::connection('second_db')->table('sessions')->get();

        $semester = DB::connection('second_db')->table('semester')->get();

        $status = DB::connection('second_db')->table('tblstudent_status')->get();

        return view('user.student.index', compact('program', 'session', 'semester', 'year', 'status'));
    }

    public function editUser()
    {
        $year = DB::connection('second_db')->table('tblyear')->get();

        $program = DB::connection('second_db')->table('tblprogramme')->get();

        $session = DB::connection('second_db')->table('sessions')->get();

        $semester = DB::connection('second_db')->table('semester')->get();

        $status = DB::connection('second_db')->table('tblstudent_status')->get();

        return view('user.student.edit', compact('program', 'session', 'semester', 'year', 'status'));

    }

    public function delete(Request $request)
    {

        DB::table('students')->where('ic', $request->id)->delete();

        DB::table('tblstudent_address')->where('student_ic', $request->id)->delete();

        DB::table('tblstudent_personal')->where('student_ic', $request->id)->delete();

        return true;

    }

    public function getStudentTableIndex(Request $request)
    {
        $student = DB::table('unitilink.students')
        ->join('eduhub.tblstudent_status', 'unitilink.students.status', '=', 'eduhub.tblstudent_status.id')
        ->join('unitilink.tblstudent_personal', 'unitilink.students.ic', '=', 'unitilink.tblstudent_personal.student_ic')
        ->join('eduhub.tblsex', 'unitilink.tblstudent_personal.sex_id', '=', 'eduhub.tblsex.id')
        ->join('unitilink.users', 'unitilink.students.stafID_add', '=', 'unitilink.users.id')
        ->select('unitilink.students.*', 'eduhub.tblstudent_status.name AS status',
                 'unitilink.tblstudent_personal.no_tel', 'eduhub.tblsex.sex_name AS gender');
    

        if(!empty($request->program))
        {
            $student->where('unitilink.students.program', $request->program);
        }
        
        if(!empty($request->from) && !empty($request->to))
        {
            $student->whereBetween('unitilink.students.date_add', [$request->from, $request->to]);
        }

        $students = $student->where('unitilink.users.type', Auth::user()->type)->get();

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
                                Status
                            </th>
                            <th>
                                No. Phone
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
                '. $student->gender .'
                </td>
                <td>
                '. $student->ic .'
                </td>
                <td>
                '. $student->no_matric .'
                </td>
                <td>
                '. $student->status .'
                </td>
                <td>
                '. $student->no_tel .'
                </td>';
                

                if (isset($request->edit)) {
                    $content .= '<td class="project-actions text-right" >
                                <a class="btn btn-info btn-sm btn-sm mr-2 mb-2" href="/pendaftar/edit/'. $student->ic .'">
                                    <i class="ti-pencil-alt">
                                    </i>
                                    Edit
                                </a>
                                <a class="btn btn-primary btn-sm btn-sm mr-2 mb-2" href="/pendaftar/spm/'. $student->ic .'">
                                    <i class="ti-ruler-pencil">
                                    </i>
                                    SPM
                                </a>
                                <a class="btn btn-secondary btn-sm btn-sm mr-2 mb-2" href="#" onclick="getProgram(\''. $student->ic .'\')">
                                    <i class="ti-eye">
                                    </i>
                                    Program History
                                </a>
                                <!-- <a class="btn btn-danger btn-sm" href="#" onclick="deleteMaterial('. $student->ic .')">
                                    <i class="ti-trash">
                                    </i>
                                    Delete
                                </a> -->
                                </td>
                            
                            ';
                }else{
                    $content .= '<td class="project-actions text-right" >
                    <a class="btn btn-secondary btn-sm btn-sm mr-2" href="#" onclick="getProgram(\''. $student->ic .'\')">
                        <i class="ti-eye">
                        </i>
                        Program History
                    </a>
                    </td>
                
                ';

                }
            }
            $content .= '</tr></tbody>';

            return $content;

    }

    public function getStudentTableIndex2(Request $request)
    {
        $students = DB::table('unitilink.students')
            ->join('eduhub.tblstudent_status', 'unitilink.students.status', 'eduhub.tblstudent_status.id')
            ->join('unitilink.tblstudent_personal', 'unitilink.students.ic', 'unitilink.tblstudent_personal.student_ic')
            ->join('eduhub.tblsex', 'unitilink.tblstudent_personal.sex_id', 'eduhub.tblsex.id')
            ->join('unitilink.users', 'unitilink.students.stafID_add', '=', 'unitilink.users.id')
            ->select('unitilink.students.*',
                     'eduhub.tblstudent_status.name AS status',
                     'unitilink.tblstudent_personal.no_tel', 'eduhub.tblsex.sex_name AS gender')
            ->where('unitilink.students.name', 'LIKE', "%".$request->search."%")
            ->orwhere('unitilink.students.ic', 'LIKE', "%".$request->search."%")
            ->orwhere('unitilink.students.no_matric', 'LIKE', "%".$request->search."%")
            ->where('unitilink.users.type', Auth::user()->type)->get();

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
                </td>';
                

            
                $content .= '<td class="project-actions text-right" >
                            <a class="btn btn-info btn-sm btn-sm mr-2 mb-2" href="/user/edit/'. $student->ic .'">
                                <i class="ti-pencil-alt">
                                </i>
                                Edit
                            </a>
                            <a class="btn btn-primary btn-sm mr-2 mb-2" href="/user/spm/'. $student->ic .'">
                                <i class="ti-ruler-pencil">
                                </i>
                                SPM
                            </a>
                            <!-- <a class="btn btn-secondary btn-sm mr-2 mb-2" href="#" onclick="getProgram(\''. $student->ic .'\')">
                                <i class="ti-eye">
                                </i>
                                Program History
                            </a> -->
                            <a class="btn btn-danger btn-sm mr-2 mb-2" href="#" onclick="deleteMaterial(\''. $student->ic .'\')">
                                <i class="ti-trash">
                                </i>
                                Delete
                            </a>
                            </td>
                        
                        ';
           
            }
            $content .= '</tr></tbody>';

            return $content;

    }

    public function createUser()
    {

        $program = DB::connection('second_db')->table('tblprogramme')->get();

        $session = DB::connection('second_db')->table('sessions')->get();

        $data['batch'] = DB::connection('second_db')->table('tblbatch')->get();

        $data['state'] = DB::connection('second_db')->table('tblstate')->orderBy('state_name')->get();

        $data['gender'] = DB::connection('second_db')->table('tblsex')->get();

        $data['race'] = DB::connection('second_db')->table('tblnationality')->orderBy('nationality_name')->get();

        $data['relationship'] = DB::connection('second_db')->table('tblrelationship')->get();

        $data['wstatus'] = DB::connection('second_db')->table('tblwaris_status')->get();

        $data['religion'] =  DB::connection('second_db')->table('tblreligion')->orderBy('religion_name')->get();

        $data['CL'] = DB::connection('second_db')->table('tblcitizenship_level')->get();

        $data['citizen'] = DB::connection('second_db')->table('tblcitizenship')->get();

        $data['mstatus'] = DB::connection('second_db')->table('tblmarriage')->get();

        $data['pass'] = DB::connection('second_db')->table('tblpass_type')->get();

        $data['country'] = DB::connection('second_db')->table('tblcountry')->get();

        $data['qualification'] = DB::connection('second_db')->table('tblqualification_std')->get();

        return view('user.student.create', compact(['program','session','data']));

    }

    public function createSearch(Request $request)
    {

        $students = DB::table('students')->where('name', 'LIKE', "%".$request->search."%")
                                         ->orwhere('ic', 'LIKE', "%".$request->search."%")
                                         ->orwhere('no_matric', 'LIKE', "%".$request->search."%")->get();

        $content = "";
        $content .= '<thead>
                        <tr>
                            <th>
                                Name
                            </th>
                            <th>
                                IC
                            </th>
                            <th>
                                No. Matric
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table">';
                    
        foreach($students as $key => $std){
            //$registered = ($std->status == 'ACTIVE') ? 'checked' : '';
            $content .= '
            <tr>
                <td>
                '. $std->name .'
                </td>
                <td>
                '. $std->ic .'
                </td>
                <td>
                '. $std->no_matric .'
                </td>
            </tr>
            ';
            }
        $content .= '</tbody>';
                
         

        return $content;

    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string'],
            'program' => ['required'],
        ]);

        if($request->ic != '')
        {

            $data['id'] = $request->ic;

        }elseif($request->passport != '')
        {

            $data['id'] = $request->passport;

        }

        if(DB::table('students')->where('ic', $data['id'])->exists())
        {

            return false;

        }else{

            //dd($request);

            DB::table('students')->insert([
                'name' => $data['name'],
                'ic' => $data['id'],
                'no_matric' => null,
                'email' =>$request->email,
                'semester' => 1,
                'password' => Hash::make('12345678'),
                'status' => 1,
                'campus_id' => 0,
                'date_offer' => $request->dol,
                'student_status' => 1,
                'stafID_add' => Auth::user()->id,
                'date_add' => date('Y-m-d'),
                'stafID_mod' => Auth::user()->id,
                'date_mod' => date('Y-m-d')
            ]);

            DB::table('tblstudent_personal')->insert([
                'student_ic' => $data['id'],
                'date_birth' => $request->birth_date,
                'datetime' => $request->dt,
                'religion_id' => $request->religion,
                'nationality_id' => $request->race,
                'sex_id' => $request->gender,
                'state_id' => $request->birth_place,
                'marriage_id' => $request->mstatus,
                'statelevel_id' => $request->CL,
                'citizenship_id' => $request->citizen,
                'no_tel' => $request->np1,
                'no_tel2' => $request->np2,
                'no_telhome' => $request->np3,
                'qualification' => $request->qualification,
                'description' => $request->comment,
                'oku' => $request->oku,
                'no_jkm' => $request->jkm
            ]);

            DB::table('tblstudent_address')->insert([
                'student_ic' => $data['id'],
                'address1' => $request->address1,
                'address2' => $request->address2,
                'address3' => $request->address3,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'state_id' => $request->state,
                'country_id' => $request->country
            ]);

            DB::table('tblstudent_program')->insert([
                'student_ic' => $data['id'],
                'program_id_first' => $request->program[0],
                'status_first' => 0,
                'program_id_second' => $request->program[1],
                'status_second' => 0,
                'program_id_third' => $request->program[2],
                'status_third' => 0,
            ]);

        }

        return redirect(route('user.create'))->with('newStud', $data['id']);
    }

    public function editForm()
    {
        $student = DB::table('students')
                   ->leftjoin('tblstudent_personal', 'students.ic', 'tblstudent_personal.student_ic')
                   ->leftjoin('tblstudent_address', 'students.ic', 'tblstudent_address.student_ic')
                   ->leftjoin('tblstudent_pass', 'students.ic', 'tblstudent_pass.student_ic')
                   ->leftjoin('student_form', 'students.ic', 'student_form.student_ic')
                   ->select('students.*', 'tblstudent_personal.*', 'tblstudent_address.*', 'tblstudent_pass.*', 'student_form.*', 'tblstudent_personal.state_id AS place_birth')
                   ->where('ic',request()->ic)->first();

        $data['waris'] = DB::table('tblstudent_waris')->where('student_ic', $student->ic)->get();

        $data['program'] = DB::table('tblstudent_program')->where('student_ic', $student->ic)->first();

        //dd($data['waris']);

        $program = DB::connection('second_db')->table('tblprogramme')->get();

        $session = DB::connection('second_db')->table('sessions')->get();

        $data['batch'] = DB::connection('second_db')->table('tblbatch')->get();

        $data['state'] = DB::connection('second_db')->table('tblstate')->orderBy('state_name')->get();

        $data['gender'] = DB::connection('second_db')->table('tblsex')->get();

        $data['race'] = DB::connection('second_db')->table('tblnationality')->orderBy('nationality_name')->get();

        $data['relationship'] = DB::connection('second_db')->table('tblrelationship')->get();

        $data['wstatus'] = DB::connection('second_db')->table('tblwaris_status')->get();

        $data['religion'] =  DB::connection('second_db')->table('tblreligion')->orderBy('religion_name')->get();

        $data['CL'] = DB::connection('second_db')->table('tblcitizenship_level')->get();

        $data['citizen'] = DB::connection('second_db')->table('tblcitizenship')->get();

        $data['mstatus'] = DB::connection('second_db')->table('tblmarriage')->get();

        $data['pass'] = DB::connection('second_db')->table('tblpass_type')->get();

        $data['country'] = DB::connection('second_db')->table('tblcountry')->get();

        $data['qualification'] = DB::connection('second_db')->table('tblqualification_std')->get();

        return view('user.student.update', compact(['student','program','session','data']));

    }

    public function updateUser(Request $request)
    {

        //dd($request->name);

        $data = $request->validate([
            'name' => ['required','string'],
            'program' => ['required'],
        ]);

        if($request->ic != '')
        {

            $data['id'] = $request->ic;

        }elseif($request->passport != '')
        {

            $data['id'] = $request->passport;

        }

        // $oldstd = DB::table('students')->where('ic', $data['id'])->first();

        // if($oldstd->program != $data['program'])
        // {

        //     DB::table('student_program')->insert([
        //         'student_ic' => $oldstd->ic,
        //         'program_id' => $oldstd->program,
        //         'comment' => $request->comment
        //     ]);

        //     DB::table('students')->where('ic', $data['id'])->update([
        //         'status' => 1
        //     ]);

        // }

        DB::table('students')->where('ic', $data['id'])->update([
            'name' => $data['name'],
            'email' =>$request->email,
            'program' => $data['program']
        ]);

        DB::table('tblstudent_personal')->where('student_ic', $data['id'])->update([
            'student_ic' => $data['id'],
            'date_birth' => $request->birth_date,
            'datetime' => $request->dt,
            'religion_id' => $request->religion,
            'nationality_id' => $request->race,
            'sex_id' => $request->gender,
            'state_id' => $request->birth_place,
            'marriage_id' => $request->mstatus,
            'statelevel_id' => $request->CL,
            'citizenship_id' => $request->citizen,
            'no_tel' => $request->np1,
            'no_tel2' => $request->np2,
            'no_telhome' => $request->np3,
            'qualification' => $request->qualification,
            'oku' => $request->oku,
            'no_jkm' => $request->jkm
        ]);

        DB::table('tblstudent_address')->updateOrInsert(
            ['student_ic' => $data['id']],
            [
            'student_ic' => $data['id'],
            'address1' => $request->address1,
            'address2' => $request->address2,
            'address3' => $request->address3,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'state_id' => $request->state,
            'country_id' => $request->country
            ]
        );

        DB::table('tblstudent_program')->where('student_ic', $data['id'])->update([
            'program_id_first' => $request->program[0],
            'program_id_second' => $request->program[1],
            'program_id_third' => $request->program[2],
        ]);

        DB::table('tblstudent_waris')->where('student_ic', $data['id'])->delete();

        $numWaris = count($request->input('w_name'));
        for ($i = 0; $i < $numWaris; $i++) {
            
            if($request->input('w_name')[$i] != '')
            {
                DB::table('tblstudent_waris')->insert([
                    'student_ic' => $data['id'],
                    'name' => $request->input('w_name')[$i],
                    'ic' => $request->input('w_ic')[$i],
                    'home_tel' => $request->input('w_notel_home')[$i],
                    'phone_tel' => $request->input('w_notel')[$i],
                    'occupation' => $request->input('occupation')[$i],
                    'dependent_no' => $request->input('dependent')[$i],
                    'kasar' => $request->input('w_kasar')[$i],
                    'bersih' => $request->input('w_bersih')[$i],
                    'relationship' => $request->input('relationship')[$i],
                    'race' => $request->input('w_race')[$i],
                    'status' => $request->input('w_status')[$i]
                ]);
            }
        }

        return back();

    }

    public function spmIndex()
    {
        
        $data['student'] = DB::table('unitilink.students AS std')
                           ->join('eduhub.tblstudent_status as stdStatus', 'std.status', 'stdStatus.id')
                           ->join('eduhub.tblprogramme as prog', 'std.program', 'prog.id')
                           ->select('std.*', 'stdStatus.name AS status', 'prog.progname AS program', 'std.program AS progid')
                           ->where('ic', request()->ic)->first();

        $data['spm'] = DB::table('tblstudent_spm')
                       ->join('tblspm_dtl', 'tblstudent_spm.student_ic', 'tblspm_dtl.student_spm_ic')
                       ->where('tblstudent_spm.student_ic', request()->ic)->get();

        $data['info'] = DB::table('tblstudent_spm')->where('student_ic', request()->ic)->first();

        $data['subject'] = DB::connection('second_db')->table('tblsubject_spm')->get();

        $data['grade'] = DB::connection('second_db')->table('tblgrade_spm')->get();

        return view('user.student.spm.spm', compact('data'));

    }

    public function spmStore(Request $request)
    {

        //dd($request->subject);

        //$student = DB::table('tblstudent_spm')->where('student_ic', $request->ic)->first();

        //dd($request->grade);

        DB::table('tblstudent_spm')->updateOrInsert(
            ['student_ic' => $request->ic], 
            [
                'year' => $request->year,
                'number_turn' => $request->turn
            ]);

        $filter = array_filter($request->subject);

        if(count($filter) !== count(array_unique($filter)))
        {
            return back()->with('error', 'Cannot have same subject! Please check and re-submit.')->withInput();

        }else{

            if(DB::table('tblspm_dtl')->where('student_spm_ic',$request->ic)->exists())
            {
                
                DB::table('tblspm_dtl')->where('student_spm_ic',$request->ic)->delete();

            }

            foreach($request->subject as $key => $sub)
            {
                
                DB::table('tblspm_dtl')->insert([
                    'student_spm_ic' => $request->ic,
                    'subject_spm_id' => $sub,
                    'grade_spm_id' => $request->grade[$key]
                ]);

            }

            return back()->with('success', 'Successfully saved SPM data')->withInput();
        }

    }

    public function eligibleUser(Request $request)
    {
        if($request->type == 1)
        {

            DB::table('tblstudent_program')->where('id', $request->id)->update([
                'status_first' => 1
            ]);

        }elseif($request->type == 2){

            DB::table('tblstudent_program')->where('id', $request->id)->update([
                'status_second' => 1
            ]);

        }elseif($request->type == 3){

            DB::table('tblstudent_program')->where('id', $request->id)->update([
                'status_third' => 1
            ]);

        }

        return true;

    }

    public function rejectedUser(Request $request)
    {

        $data['id'] = $request->id;

        $data['type'] = $request->type;

        return view('user.student.getRejected', compact('data'));

    }

    public function submitRejectedUser(Request $request)
    {

        $data = $request->validate([
            'comment' => ['required','string'],
        ]);

        if($request->type == 1)
        {

            DB::table('tblstudent_program')->where('id', $request->id)->update([
                'status_first' => 2,
                'comment_first' => $data['comment']
            ]);

        }elseif($request->type == 2){

            DB::table('tblstudent_program')->where('id', $request->id)->update([
                'status_second' => 2,
                'comment_second' => $data['comment']
            ]);

        }elseif($request->type == 3){

            DB::table('tblstudent_program')->where('id', $request->id)->update([
                'status_third' => 2,
                'comment_third' => $data['comment']
            ]);

        }

        return back();

    }
}
