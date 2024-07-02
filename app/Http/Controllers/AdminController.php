<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{

    public function indexAdmin() 
    {

        //dd(Session::get('User'));

        $users = User::all()->sortBy('type');

        //dd($users);

        return view('admin.admin',['users'=>$users]);
    }

    public function create()
    {
        // $faculty = DB::table('tblfaculty')->get();

        return view('admin.create');
    }

    public function store()
    {
        //this will validate the requested data
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'nostaf' => ['required', 'string', 'max:45'],
            // 'ic' => ['required', 'string', 'max:12'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'usrtype' => ['required'],
            // 'faculty' => ['required'],
        ]);

        //dd(array_values(array_filter(request()->prg,function($v){return !is_null($v);})));

        //this will create data in table [Please be noted that model need to be fillable with the same data]
        User::create([
            'name' => $data['name'],
            // 'no_staf' => $data['nostaf'],
            // 'ic' => $data['ic'],
            'email' => $data['email'],
            'password' => Hash::make('12345678'),
            'type' => $data['usrtype'],
            // 'faculty' => $data['faculty'],
            // 'start' => request()->from,
            // 'end' => request()->to
        ]);

        // if(isset(request()->program))
        // {
        //     foreach(request()->program as $prg)
        //     {
        //         DB::table('user_program')->insert([
        //             'user_ic' => $data['ic'],
        //             'program_id' => $prg
        //         ]);
        //     }
        // }

        // if(isset(request()->academic))
        // {
        //     $pgname = array_values(array_filter(request()->prg,function($v){return !is_null($v);}));

        //     $uniname = array_values(array_filter(request()->uni,function($v){return !is_null($v);}));

        //     foreach(request()->academic as $key => $ac)
        //     {
        //         DB::table('tbluser_academic')->insert([
        //             'user_ic' => $data['ic'],
        //             'academic_id' => $ac,
        //             'academic_name' => $pgname[$key],
        //             'university_name' => $uniname[$key]
        //         ]);
        //     }
        // }

        return redirect('/admin/index');
    }

    public function edit(User $id)
    {
        // $faculty = DB::table('tblfaculty')->get();

        //dd($id);

        // $academics = array("DP|DIPLOMA", 'DG|DEGREE', 'MS|MASTER', 'PHD|PHD');

        // foreach($academics as $ac)
        // {
        //     $ace = explode('|', $ac);

        //     $academic[] = DB::table('tbluser_academic')->where('user_ic', $id->ic)->where('academic_id', $ace[0])->first();
        // }

        //dd($academic);


        return view('admin.edit' , compact('id'));
    }

    public function update(User $id)
    {
        $data = request()->validate([
            'name' => 'required',
            // 'ic' => 'required',
            'type' => 'required',
            'email' => 'required',
        ]);

        // $data2 = [
        //             'no_staf' => request()->nostaf,
        //             'faculty' => request()->faculty,
        //             'start' => request()->from,
        //             'end' => request()->to,
        //             'status' => request()->status,
        //             'comment' => request()->comments
        //          ];

        // //this is to check if image is not empty
        // if(request('image'))
        // {
        //     $imageName = request('image')->getClientOriginalName(); 

        //     $filepath = "storage/";

        //     //this is to store file/image in specific folder
        //     //$imagePath = request('image')->storeAs('storage', $imageName, 'linode', 'public');

        //     Storage::disk('linode')->putFileAs(
        //         $filepath,
        //         request('image'),
        //         $imageName,
        //         'public'
        //       );

        //       $imagePath = $filepath . $imageName;

        //     //dd($imagePath);

        //     //this is to resize image  Image need to be declared with 'use Intervention\Image\Facades\Image;'
        //     $image = Image::make(Storage::disk('linode')->url($imagePath))->fit(1000, 1000);
        //     //dd($image);
        //     $image->save($imagePath);

        //     //store path in image parameter and store in $imageArray variable
        //     $imageArray = ['image' => $imagePath];
        // }

        // //array_merge is a function to merge two variable together
        User::where('id', $id->id)->update(
            $data
        );

        // //dd(request()->program);

        // if(request()->program != null)
        // {
        //     DB::table('user_program')->where('user_ic', $data['ic'])->delete();
            
        //     foreach(request()->program as $prg)
        //     {

        //         DB::table('user_program')->insert([
        //             'user_ic' => $data['ic'],
        //             'program_id' => $prg
        //         ]);
        //     }
        // }

        // if(request()->academic != null)
        // {
        //     $pgname = $pgname = array_values(array_filter(request()->prg,function($v){return !is_null($v);}));

        //     $uniname = array_values(array_filter(request()->uni,function($v){return !is_null($v);}));

        //     DB::table('tbluser_academic')->where('user_ic', $data['ic'])->delete();

        //     foreach(request()->academic as $key => $ac)
        //     {
        //         DB::table('tbluser_academic')->insert([
        //             'user_ic' => $data['ic'],
        //             'academic_id' => $ac,
        //             'academic_name' => $pgname[$key],
        //             'university_name' => $uniname[$key]
        //         ]);
        //     }
        // }

        return redirect("/admin/index");
    }

    public function delete(Request $request)
    {

        User::where('id', $request->id)->delete();

        return true;
        
    }
}
