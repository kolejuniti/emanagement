<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login/custom', [App\Http\Controllers\LoginController::class, 'login'])->name('login.custom');

Route::group(['middleware' => ['auth']], function() {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/admin/index', [App\Http\Controllers\AdminController::class, 'indexAdmin'])->name('admin.index');
    Route::get('/admin/{id}/edit', [App\Http\Controllers\AdminController::class, 'edit'])->name('admin.edit');
    Route::patch('/admin/{id}', [App\Http\Controllers\AdminController::class, 'update'])->name('admin.update');
    Route::get('/admin/create', [App\Http\Controllers\AdminController::class, 'create'])->name('admin.create');
    Route::post('/admin/store', [App\Http\Controllers\AdminController::class, 'store'])->name('admin.store');
    Route::delete('/admin/delete', [App\Http\Controllers\AdminController::class, 'delete'])->name('admin.delete');

    Route::post('/hostel/getStudentList', [App\Http\Controllers\HostelController::class, 'getStudentList']);
    Route::get('/hostel/block', [App\Http\Controllers\HostelController::class, 'indexBlock'])->name('hostel.block');
    Route::post('/hostel/block/store', [App\Http\Controllers\HostelController::class, 'storeBlock'])->name('hostel.block.store');
    Route::post('/hostel/block/getBlock', [App\Http\Controllers\HostelController::class, 'getBlock'])->name('hostel.block.store');
    Route::post('/hostel/block/delete', [App\Http\Controllers\HostelController::class, 'deleteBlock'])->name('hostel.block.delete');
    Route::get('/hostel/blockUnit', [App\Http\Controllers\HostelController::class, 'indexBlockUnit'])->name('hostel.blockUnit');
    Route::post('/hostel/blockUnit/store', [App\Http\Controllers\HostelController::class, 'storeBlockUnit'])->name('hostel.blockUnit.store');
    Route::post('/hostel/blockUnit/getBlockUnits', [App\Http\Controllers\HostelController::class, 'getBlockUnits']);
    Route::post('/hostel/blockUnit/getBlockUnit', [App\Http\Controllers\HostelController::class, 'getBlockUnit'])->name('hostel.blockUnit.store');
    Route::post('/hostel/blockUnit/delete', [App\Http\Controllers\HostelController::class, 'deleteBlockUnit'])->name('hostel.blockUnit.delete');
    Route::get('/hostel/resident', [App\Http\Controllers\HostelController::class, 'indexResident'])->name('hostel.resident');
    Route::post('/hostel/resident/store', [App\Http\Controllers\HostelController::class, 'storeResident'])->name('hostel.resident.store');
    Route::post('/hostel/resident/getResident', [App\Http\Controllers\HostelController::class, 'getResident'])->name('hostel.resident.store');
    Route::post('/hostel/resident/delete', [App\Http\Controllers\HostelController::class, 'deleteResident'])->name('hostel.resident.delete');
    Route::get('/hostel/register', [App\Http\Controllers\HostelController::class, 'hostelRegister'])->name('hostel.register');
    Route::post('/hostel/register/getBlockUnitList', [App\Http\Controllers\HostelController::class, 'getBlockUnitList']);
    Route::get('/hostel/register/{id}', [App\Http\Controllers\HostelController::class, 'hostelRegisterStudent'])->name('hostel.register.student');
    Route::post('/hostel/register/{id}/getStudentInfo', [App\Http\Controllers\HostelController::class, 'getStudentInfo']);
    Route::post('/hostel/register/{id}/registerStudent', [App\Http\Controllers\HostelController::class, 'registerStudent']);
    Route::post('/hostel/register/{id}/deleteStudent', [App\Http\Controllers\HostelController::class, 'deleteStudent']);
    Route::get('/hostel/student/list', [App\Http\Controllers\HostelController::class, 'studentList'])->name('hostel.student.list');
    Route::post('/hostel/student/list/getStudentListIndex', [App\Http\Controllers\HostelController::class, 'getStudentListIndex'])->name('hostel.student.list.getStudentListIndex');
    Route::get('/hostel/student/view', [App\Http\Controllers\HostelController::class, 'studentView'])->name('hostel.student.view');
    Route::post('/hostel/student/view/getStudentListIndex2', [App\Http\Controllers\HostelController::class, 'getStudentListIndex2'])->name('hostel.student.view.getStudentListIndex2');
    Route::get('/hostel/student/view/{ic}', [App\Http\Controllers\HostelController::class, 'getStudentDetails']);
    Route::get('/hostel/student/checkout', [App\Http\Controllers\HostelController::class, 'hostelCheckout'])->name('hostel.student.checkout');
    Route::post('/hostel/student/checkout/getStudentInfo2', [App\Http\Controllers\HostelController::class, 'getStudentInfo2']);
    Route::post('/hostel/student/checkout/checkoutStudent', [App\Http\Controllers\HostelController::class, 'checkoutStudent']);
    Route::get('/hostel/student/printStudentSlip/{student}', [App\Http\Controllers\HostelController::class, 'printStudentSlip'])->name('hostel.student.printSlip');
    Route::get('/hostel/debit', [App\Http\Controllers\HostelController::class, 'debitNote'])->name('hostel.student.debit');
    Route::post('/hostel/debit/getStudent', [App\Http\Controllers\HostelController::class, 'getStudentDebit']);
    Route::post('/hostel/debit/storeDebit', [App\Http\Controllers\HostelController::class, 'storeDebit']);
    Route::get('/hostel/report/studentHostelReport', [App\Http\Controllers\HostelController::class, 'studentHostelReport'])->name('hostel.report.studentHostelReport');
    Route::post('/hostel/report/studentHostelReport2', [App\Http\Controllers\HostelController::class, 'studentHostelReport'])->name('hostel.report.studentHostelReport2');
    Route::get('/hostel/report/unitStatus', [App\Http\Controllers\HostelController::class, 'unitStatus'])->name('hostel.report.unitStatus');
    Route::get('/hostel/report/unitStatus/getBlockList', [App\Http\Controllers\HostelController::class, 'getBlockList'])->name('hostel.report.unitStatus.getBlockList');
    Route::post('/hostel/report/unitStatus/getUnitList', [App\Http\Controllers\HostelController::class, 'getUnitList'])->name('hostel.report.unitStatus.getUnitList');
    Route::post('/hostel/report/unitStatus/getResidentList', [App\Http\Controllers\HostelController::class, 'getResidentList'])->name('hostel.report.unitStatus.getResidentList');
    Route::get('/hostel/report/studentReport', [App\Http\Controllers\HostelController::class, 'studentReport'])->name('hostel.report.studentReport');
    Route::get('/hostel/report/reportRs', [App\Http\Controllers\HostelController::class, 'studentReportRs'])->name('hostel.report.reportR');
    Route::get('/hostel/report/reportRs/getStudentReportR', [App\Http\Controllers\HostelController::class, 'getStudentReportRs']);
    
    Route::get('/user/index', [App\Http\Controllers\UserController::class, 'indexUser'])->name('user.index');
    Route::post('/user/index/getStudentTableIndex', [App\Http\Controllers\UserController::class, 'getStudentTableIndex']);
    Route::get('/user/edit', [App\Http\Controllers\UserController::class, 'editUser'])->name('user.editStudent');
    Route::post('/user/edit/getStudentTableIndex2', [App\Http\Controllers\UserController::class, 'getStudentTableIndex2']);
    Route::get('/user/edit/{ic}', [App\Http\Controllers\UserController::class, 'editForm'])->name('user.editForm');
    Route::post('/user/edit/update', [App\Http\Controllers\UserController::class, 'updateUser'])->name('user.update');
    Route::post('/user/edit/eligible', [App\Http\Controllers\UserController::class, 'eligibleUser'])->name('user.eligible');
    Route::post('/user/edit/rejected', [App\Http\Controllers\UserController::class, 'rejectedUser'])->name('user.rejected');
    Route::post('/user/edit/rejected/submit', [App\Http\Controllers\UserController::class, 'submitRejectedUser'])->name('user.rejected.submit');
    Route::get('/user/create', [App\Http\Controllers\UserController::class, 'createUser'])->name('user.create');
    Route::post('/user/create/search', [App\Http\Controllers\UserController::class, 'createSearch'])->name('user.create.search');
    Route::post('/user/store', [App\Http\Controllers\UserController::class, 'storeUser'])->name('user.storeStudent');
    Route::get('/user/spm/{ic}', [App\Http\Controllers\UserController::class, 'spmIndex'])->name('user.spm');
    Route::post('/user/spm/{ic}/store', [App\Http\Controllers\UserController::class, 'spmStore'])->name('user.spm.store');
    
    Route::prefix('all')->group(function () {
        Route::get('/student/announcements/getannoucement', [App\Http\Controllers\UserController::class, 'indexAnnouncements']);
        Route::post('/student/announcements/post', [App\Http\Controllers\UserController::class, 'storeAnnouncements']);
        Route::get('/student/announcements/get/{id}', [App\Http\Controllers\UserController::class, 'showAnnouncements']);
        Route::put('/student/announcements/put/{id}', [App\Http\Controllers\UserController::class, 'updateAnnouncements']);
        Route::delete('/student/announcements/delete/{id}', [App\Http\Controllers\UserController::class, 'destroyAnnouncements']);
        Route::get('/student/announcements/getBannerAnnouncement', [App\Http\Controllers\UserController::class, 'getBannerAnnouncement']);
    });

    Route::get('/all/student/announcements', function () {
        return view('hostel.student.announcements.index');
    })->name('all.student.announcements');

});


Route::get("/logout/custom",[App\Http\Controllers\LogoutController::class,"logout"])->name('custom_logout');