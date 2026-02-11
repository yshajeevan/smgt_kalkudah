<?php
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebNotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AutherController;
use App\Http\Controllers\CadreController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\InstituteController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DsController;
use App\Http\Controllers\GnController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\StaffOfficerController;
use App\Http\Controllers\CadreSubjectController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\DegreeInstituteController;
use App\Http\Controllers\DegreeSubjectController;
use App\Http\Controllers\BuildingCategoryController;
use App\Http\Controllers\BuildingTypeController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BuildingRepairCategoryController;
use App\Http\Controllers\CompetencyController;
use App\Http\Controllers\SyllabusUnitController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\ItemAnalysisController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ExamReportController;
use App\Http\Controllers\StudentResponseController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentOptionalSubjectController;
use App\Http\Controllers\StudentController;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// ForgClassControllerot password routes
Route::get('request_otp', function () {return view('auth.request_otp');})->name('otp.request_otp');
Route::post('/send-otp', [LoginController::class, 'sendOtp'])->name('otp.send-otp');
Route::get('otp/verify/view', function () {return view('auth.otp_verify');})->name('otp.verify.view');
Route::post('otp/verify', [LoginController::class, 'verifyOTP'])->name('otp.verify');
Route::get('password/reset/view', function () {return view('auth.password_reset_form');})->name('password.reset.view');
Route::post('password/reset/view', [LoginController::class, 'updatePassword'])->name('otp_password.reset');

Route::get('/feedback/{scale}','App\Http\Controllers\ProcessController@feedback');
Route::post('update_feedback', 'App\Http\Controllers\ProcessController@updatefeedback');


Auth::routes();

Route::group( ['middleware' => 'auth'], function(){
    //Manage Student
    Route::get('manage-students', [StudentController::class, 'index'])->name('students.index');
    Route::get('manage-students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('manage-students', [StudentController::class, 'store'])->name('students.store');
    Route::get('manage-students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('manage-students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::get('manage-students/list', [StudentController::class, 'listAjax'])->name('students.list');
    // GN fetch
    Route::get('manage-students/ds/{dsId}/gnds', [StudentController::class, 'gndsByDs'])->name('students.gnds-by-ds');
    //Quick add
    Route::post('manage-students/quick-store', [StudentController::class, 'quickStore'])->name('students.quick-store');
    // Quick update (inline required fields)
    Route::post('manage-students/{student}/quick-update', [StudentController::class, 'quickUpdate'])->name('students.quick-update');
    // Update EWS color via AJAX
    Route::post('manage-students/{student}/update-ews', [StudentController::class, 'updateEwsColor'])->name('students.ews-update');
    // Delete - only super_admin allowed; controller enforces role
    Route::delete('manage-students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    //check admission number
    Route::post('manage-students/check-admission', [StudentController::class, 'checkAdmission'])->name('students.check-admission');
    // AJAX: return cadre lists for a given grade
    Route::get('manage-students/grade/{gradeId}/cadres', [App\Http\Controllers\StudentController::class, 'cadresByGrade'])
        ->name('students.cadres-by-grade');

    // Attendance
    Route::get('attencreate', 'App\Http\Controllers\AttendanceController@create')->name('attendance.create');
    Route::get('attencreate/{instid}', 'App\Http\Controllers\AttendanceController@createlink')->name('attendance.createlink');
    Route::post('attendance', 'App\Http\Controllers\AttendanceController@store')->name('attendance.store');
    
    Route::get('/gndetails/{id}','App\Http\Controllers\GnController@getgn');
        
    Route::get('employee','App\Http\Controllers\EmployeeController@index')->name('employee.index');
    Route::get('/employee-edit/{id}','App\Http\Controllers\EmployeeController@edit')->name('employee.edit');
    Route::put('/dummyemployee-edit/{id}','App\Http\Controllers\EmployeeController@dummy_store')->name('dummyemployee.store');
    
    Route::get('institute-edit/{id}','App\Http\Controllers\InstituteController@edit')->name('institute.edit');

    // Exam analysis related functions
    Route::get('/reports/exam-ol-final', [ExamReportController::class, 'showOlFinalResult'])->name('reports.ol.exam.final.result');
    Route::get('/reports/exam-ol-final/data', [ExamReportController::class, 'getOlFinalResultData'])->name('reports.ol_exam.final.result.data');

    Route::get('/reports/exam-ol-final-subject', [ExamReportController::class, 'olFinalsubjectresult'])->name('reports.ol.exam.final.subject.result');
    Route::get('/reports/exam-ol-final-subject/data', [ExamReportController::class, 'getOlFinalsubjectResultsData'])->name('reports.ol.exam.final.subject.result.data');

    Route::get('/reports/pass-percentage', [ExamReportController::class, 'pass'])->name('reports.pass');
    Route::get('/reports/pass-percentage/data', [ExamReportController::class, 'getData'])->name('reports.pass.data');

    Route::get('/reports/average', [ExamReportController::class, 'average'])->name('reports.average');
    Route::get('/reports/average/data', [ExamReportController::class, 'getAverageData'])->name('reports.average.data');

    Route::get('/reports/students-subject-wise-marks', [ExamReportController::class, 'studentSubjectMarksReport'])->name('reports.students.subject.marks');
    Route::get('/reports/students-subject-wise-marks/data', [ExamReportController::class, 'getStudentSubjectMarks'])->name('reports.students.subject.marks.data');

    Route::get('/reports/student-average-allsubject-marks', [ExamReportController::class, 'studentAverageAllSubject'])->name('reports.student.average.allsubject.marks');
    Route::get('/reports/student-average-allsubject-marks/data', [ExamReportController::class, 'getstudentAverageAllSubject'])->name('reports.student.average.allsubject.marks.data');
    Route::get('/students-by-exam', [ExamReportController::class, 'getStudentsByExam'])->name('reports.getStudentsByExam');
    Route::get('/reports/student/awards', [ExamReportController::class, 'getSubjectAwards'])->name('reports.student.awards');
    Route::get('/reports/student/best-improvement', [ExamReportController::class, 'getBestImprovement'])->name('reports.student.improvement');

    Route::get('/reports/subject/improvement', [ExamReportController::class, 'getSubjectImprovementData'])->name('reports.subject.improvement.data');
    Route::get('/reports/subject/improvement-awards', [ExamReportController::class, 'subjectImprovementAwards'])->name('reports.subject.improvement.awards');

    Route::get('/reports/subject-medal-winners', [ExamReportController::class, 'subjectMedalWinnersReport'])->name('reports.subject.medal.winners');
    Route::get('/reports/subject-medal-winners-data', [ExamReportController::class, 'getSubjectMedalWinnersData'])->name('reports.subject.medal.winners.data');

    Route::get('reports/student-marks', [ExamReportController::class, 'studentMarksTableReport'])->name('reports.student.marks.table');
    Route::get('reports/student-marks-data', [ExamReportController::class, 'getStudentMarksTableData'])->name('reports.student.marks.table.data');

    Route::get('reports/student-rank', [ExamReportController::class, 'studentRanksTableReport'])->name('reports.student.ranks.table');
    Route::get('reports/student-rank-data', [ExamReportController::class, 'getStudentRanksTableData'])->name('reports.student.ranks.table.data');

    Route::get('reports/student-marks-print', [ExamReportController::class, 'studentMarksPrintReport'])->name('reports.student.marks.print');
    Route::get('reports/student-marks-print-data', [ExamReportController::class, 'getStudentMarksPrintData'])->name('reports.student.marks.print.data');

    Route::get('reports/exam-attendance', [ExamReportController::class, 'examAttendanceReport'])->name('reports.exam.attendance');
    Route::get('reports/exam-attendance-data', [ExamReportController::class, 'getExamAttendanceData'])->name('reports.exam.attendance.data');

    Route::get('reports/student-attendance-report', [ExamReportController::class, 'attendanceScatterReport'])->name('reports.student.attendance');
    Route::get('reports/student-attendance-data', [ExamReportController::class, 'getAttendanceScatterData'])->name('reports.student.attendance.data');

    Route::get('reports/school-subject-analysis', [ExamReportController::class, 'schoolSubjectAnalysis'])->name('reports.school.subject.analysis');
    Route::get('reports/school-subject-analysis-data', [ExamReportController::class, 'getSchoolSubjectAnalysisData'])->name('reports.school.subject.analysis.data');
    Route::get('/reports/exam-subjects', [ExamReportController::class, 'getExamSubjects'])->name('reports.exam.subjects');

    Route::get('reports/school-overall-analysis', [ExamReportController::class, 'schoolOverallAnalysis'])->name('reports.school.overall.analysis');
    Route::get('reports/school-overall-analysis-data', [ExamReportController::class, 'getSchoolOverallAnalysisData'])->name('reports.school.overall.analysis.data');

    Route::get('reports/item-analysis', [ItemAnalysisController::class, 'itemAnalysis'])->name('reports.item.analysis');
    Route::post('/reports/item-analysis/subjects', [ItemAnalysisController::class, 'getSubjects'])->name('reports.item.analysis.subjects');
    Route::post('reports/item-analysis-data', [ItemAnalysisController::class, 'getItemAnalysisData'])->name('reports.item.analysis.data');
    Route::get('/reports/student-unit-analysis', [ItemAnalysisController::class, 'studentUnitAnalysis'])->name('reports.student.unit.analysis');
    Route::post('/reports/student-unit-data', [ItemAnalysisController::class, 'getStudentUnitData'])->name('reports.student.unit.data');

    // Student Responses
    Route::prefix('student-responses')->name('student_responses.')->group(function () {
        Route::get('/create', [StudentResponseController::class, 'create'])->name('create');
        Route::get('/load-subjects', [StudentResponseController::class, 'loadSubjects'])->name('loadSubjects');
        Route::get('/load-students', [StudentResponseController::class, 'loadStudents'])->name('loadStudents');
        Route::get('/load-questions', [StudentResponseController::class, 'loadQuestions'])->name('loadQuestions');
        Route::post('/store', [StudentResponseController::class, 'store'])->name('store');
        Route::get('/status', [StudentResponseController::class, 'status'])->name('status');
        Route::delete('/delete', [StudentResponseController::class, 'delete'])->name('delete');
    });

    Route::get('/students/optionals', [StudentOptionalSubjectController::class, 'index'])->name('students.optionals.index');
    Route::post('/students/{student}/optionals', [StudentOptionalSubjectController::class, 'update'])->name('students.optionals.update');

    Route::get('marks/create', [MarksController::class, 'createMarks'])->name('marks.create');
    Route::post('marks/store', [MarksController::class, 'storeMarks'])->name('marks.store');
    Route::get('/students/{studentId}/subjects', [MarksController::class, 'getStudentSubjects']);
    Route::get('/exam/{examId}/students-summary', [MarksController::class, 'studentsSummary']);

    Route::get('/questions/manage', [QuestionController::class, 'manage'])->name('questions.manage');
    Route::post('/questions/store', [QuestionController::class, 'store'])->name('questions.store');
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // AJAX routes
    Route::get('/questions/get-subjects', [QuestionController::class, 'getSubjects'])->name('questions.getSubjects');   
    Route::get('/questions/get-units/{subject}', [QuestionController::class, 'getUnits'])->name('questions.getUnits');
    Route::get('/questions/by-subject/{subject}', [QuestionController::class, 'getQuestionsBySubject'])->name('questions.bySubject');

    Route::get('/competencies', [CompetencyController::class, 'index'])->name('competencies.index');
    Route::get('/competencies/by-subject/{subjectId}', [CompetencyController::class, 'getBySubject']);
    Route::post('/competencies', [CompetencyController::class, 'store'])->name('competencies.store');
    Route::get('/competencies/{id}/edit', [CompetencyController::class, 'edit'])->name('competencies.edit');
    Route::put('/competencies/{id}', [CompetencyController::class, 'update'])->name('competencies.update');
    Route::delete('/competencies/{id}', [CompetencyController::class, 'destroy'])->name('competencies.destroy');

    Route::get('/syllabus-units', [SyllabusUnitController::class, 'index'])->name('syllabus_units.index');
    Route::get('/competencies/by-subject/{subject}', [SyllabusUnitController::class, 'getCompetencies']);
    Route::get('/syllabus-units/by-competency/{competency}', [SyllabusUnitController::class, 'getUnits']);
    Route::post('/syllabus-units', [SyllabusUnitController::class, 'store'])->name('syllabus_units.store');
    Route::get('/syllabus-units/by-competency/{competencyId}', [SyllabusUnitController::class, 'getByCompetency']);
    Route::get('/syllabus-units/{id}/edit', [SyllabusUnitController::class, 'edit'])->name('syllabus_units.edit');
    Route::put('/syllabus-units/{id}', [SyllabusUnitController::class, 'update']); // using POST + _method=PUT
    Route::delete('/syllabus-units/{id}', [SyllabusUnitController::class, 'destroy']);

    //Delete teaching subject
    Route::delete('teachsubject/{id}', [EmployeeController::class, 'destroy_teachsubject']);

});

Route::get('/', function () {
    $user = auth()->user();

    if ($user->hasRole('Sch_Admin')) {
        $controller = new \App\Http\Controllers\SchoolController();
        return $controller->home();
    }

    $controller = new \App\Http\Controllers\HomeController();
    return $controller->index();
})->middleware('auth')->name('/');

// Separate route for super_admin to access HomeController
Route::get('/result', function () {
    $user = auth()->user();

    if ($user->hasAnyRole(['super_admin', 'Sch_Admin'])) {
        return app(\App\Http\Controllers\SchoolController::class)->index();
    }

    abort(403, 'Unauthorized');
})->middleware('auth')->name('manage.result');

Route::group(['middleware' => ['auth','role:super_admin|User|Admin']], function(){

    Route::get('/message-attachments/{filename}', function ($filename) {
        $filePath = "private/attachments/{$filename}";
    
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found.');
        }
    
        $fileContent = Storage::disk('local')->get($filePath);
        $mimeType = Storage::disk('local')->mimeType($filePath);
    
        return response($fileContent, 200)->header('Content-Type', $mimeType);
    })->name('message.attachment');

    Route::resource('cadre-subject', CadreSubjectController::class);

    Route::resource('degrees', DegreeController::class);
    Route::resource('deg-institutes', DegreeInstituteController::class);
    Route::resource('deg-subjects', DegreeSubjectController::class);

    
    Route::resource('building-categories', BuildingCategoryController::class);
    Route::resource('building-types', BuildingTypeController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('building-repair-categories', BuildingRepairCategoryController::class);
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('rooms', RoomController::class);

    Route::resource('staff', StaffOfficerController::class);
    Route::get('staff/list_order', [StaffOfficerController::class, 'show'])->name('staff.list_order');
    Route::post('staff/list_order', [StaffOfficerController::class, 'updateListOrder'])->name('staff.list_order_store');
    
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/push-notificaiton', [WebNotificationController::class, 'index'])->name('push-notificaiton');
    Route::post('/store-token', [WebNotificationController::class, 'storeToken'])->name('store.token');
    Route::post('/send-web-notification', [WebNotificationController::class, 'sendWebNotification'])->name('send.web-notification');
    
    Route::get('notify', [HomeController::class,'notify']);
    
    //Virtual File
    Route::post('vfile-store','App\Http\Controllers\VirtualFileController@store')->name('vfile.store');
    Route::post('vfile-update/{id}','App\Http\Controllers\VirtualFileController@update')->name('vfile.update');

    //Manage Employees
    Route::get('employee-create','App\Http\Controllers\EmployeeController@create')->name('employee.create');
    Route::post('employee-create','App\Http\Controllers\EmployeeController@store')->name('employee.store');
    Route::put('/employee-edit/{id}','App\Http\Controllers\EmployeeController@update')->name('employee.update');
    Route::delete('employee/{id}','App\Http\Controllers\EmployeeController@destroy')->name('employee.destroy');
    Route::post('/photoupdate/{id}','App\Http\Controllers\EmployeeController@photoupdate')->name('employee.photoupdate');
    Route::get('teacherprofile','App\Http\Controllers\EmployeeController@profile')->name('employee.profile');
    Route::get('employee-show/{id}','App\Http\Controllers\EmployeeController@show')->name('employee.show');
    Route::get('employee-appendclk','App\Http\Controllers\EmployeeController@appendview')->name('employee.appendview');
    Route::post('employee-updateclk','App\Http\Controllers\EmployeeController@updateinstitute1')->name('employee.updateclerk');
    Route::post('autocomplete-user-search', 'App\Http\Controllers\EmployeeController@userSearch')->name('user.search');
    Route::get('/charts', [EmployeeController::class, 'showChart'])->name('employee.analysis');
    //Ignore dummy request in modal change checklist
    Route::delete('/employee/{id}/dummy-ignore', 'App\Http\Controllers\EmployeeController@ignoreDummy')->name('employee.dummy.ignore');

    //Maganage employee updation request
    Route::get('pending-approval','App\Http\Controllers\EmployeeController@dummy_index')->name('employee.dummy_index');
    Route::delete('pending-delete/{id}','App\Http\Controllers\EmployeeController@dummy_destroy')->name('employee.dummy_destroy');
    
    //Qualification
    Route::post('qualification/{id}', 'App\Http\Controllers\EmployeeController@destroy_qualification');
  
    Route::get('/desigcatg/{id}','App\Http\Controllers\EmployeeController@getdesigcatg');
    Route::get('export-employee','App\Http\Controllers\EmployeeController@export')->name('employee.export');
    Route::get('employee-transfer','App\Http\Controllers\EmployeeController@transfer')->name('employee.transfer');
    Route::get('/distrance','App\Http\Controllers\EmployeeController@getdistrance');
    Route::get('/servicehistory','App\Http\Controllers\EmployeeController@getservicehistory');
    
    Route::get('salary_import', [EmployeeController::class, 'salaryimportview'])->name('salaryimport.view');
    Route::post('salary_import', [EmployeeController::class, 'salaryimport'])->name('salary.import');
    Route::get('salary_crosscheck', [EmployeeController::class, 'checkwithsalary'])->name('crosscheckcheck.salary');
        
    Route::get('nemis_import', [EmployeeController::class, 'nemisimportview'])->name('nemisimport.view');
    Route::post('nemis_import', [EmployeeController::class, 'nemisimport'])->name('nemis.import');
    Route::get('nemis_crosscheck', [EmployeeController::class, 'checkwithnemis'])->name('crosscheckcheck.nemis');
    
    //Cadre Reporting
	Route::get('cadrerpt', 'App\Http\Controllers\CadreController@index')->name('cadre.index');
    Route::get('cadrexport', 'App\Http\Controllers\CadreController@cadrexport');
    Route::get('cadredetailed', 'App\Http\Controllers\CadreController@detailedCadre');
    Route::get('cadre-transfer', 'App\Http\Controllers\CadreController@transcadre');

    //Magage Designation
    Route::resource('designation', 'App\Http\Controllers\DesignationController');
    
    //Manage DS Division
    Route::get('ds-division', 'App\Http\Controllers\DsController@index')->name('dsdivision.index');
    Route::get('ds-create','App\Http\Controllers\DsController@create')->name('dsdivision.create');
    Route::post('ds-create','App\Http\Controllers\DsController@store')->name('dsdivision.store');
    Route::get('ds-edit/{id}','App\Http\Controllers\DsController@edit')->name('dsdivision.edit');
    Route::post('ds-edit/{id}','App\Http\Controllers\DsController@update')->name('dsdivision.update');
	Route::get('ds-delete/{id}', 'App\Http\Controllers\DsController@destroy')->name('dsdivision.delete');
	
	//Manage Gn Division 
    Route::get('gn-division', 'App\Http\Controllers\GnController@index')->name('gndivision.index');
    Route::get('gn-create','App\Http\Controllers\GnController@create')->name('gndivision.create');
    Route::post('gn-store','App\Http\Controllers\GnController@store')->name('gndivision.store');
    Route::get('gn-edit/{id}','App\Http\Controllers\GnController@edit')->name('gndivision.edit');
    Route::post('gn-update/{id}','App\Http\Controllers\GnController@update')->name('gndivision.update');
	Route::get('gn-delete/{id}', 'App\Http\Controllers\GnController@destroy')->name('gndivision.delete');

    //Submit Routes
    Route::get('submitform/index/{id}','App\Http\Controllers\SubmitController@index')->name('submitform.index');
    Route::post('/institute-search', 'App\Http\Controllers\SubmitController@instituteSearch')->name('institutes.searchInstitute');
    
    //Manage Institutes
	Route::get('institutes','App\Http\Controllers\InstituteController@index')->name('institute.index');
    Route::get('institute-create', 'App\Http\Controllers\InstituteController@create')->name('institute.create');
    Route::post('institute-create', 'App\Http\Controllers\InstituteController@store')->name('institute.store');
    Route::get('institute-show/{id}','App\Http\Controllers\InstituteController@show')->name('institute.show');
    Route::post('institute-edit/{id}','App\Http\Controllers\InstituteController@update')->name('institute.update');
    Route::delete('institutes/{id}','App\Http\Controllers\InstituteController@destroy')->name('institute.destroy');
    Route::get('instituteexport','App\Http\Controllers\InstituteController@export')->name('instituteexport.export');
    Route::post('institute/media', 'App\Http\Controllers\InstituteController@storeMedia')->name('institute.storeMedia');
    Route::get('insclerks', 'App\Http\Controllers\InstituteController@view_clerk')->name('inst.viewclerk');
    Route::get('clerk', 'App\Http\Controllers\InstituteController@clerks');
    Route::post('updateclerk', 'App\Http\Controllers\InstituteController@updatepk');
	Route::get('parallel-class','App\Http\Controllers\InstituteController@prlclass')->name('institute.prlclass');
	Route::get('students','App\Http\Controllers\InstituteController@students')->name('institute.students');

    //home
    Route::get('/profile','App\Http\Controllers\HomeController@profile')->name('admin-profile');
    Route::post('/profile/{id}','App\Http\Controllers\HomeController@profileUpdate')->name('profile-update');
    Route::get('/staffperf','App\Http\Controllers\HomeController@staffperf');

    //Notification
    Route::get('/notification/{id}','App\Http\Controllers\NotificationController@show')->name('admin.notification');
    Route::get('/notifications','App\Http\Controllers\NotificationController@index')->name('all.notification');
    Route::get('/notifshow/{id}','App\Http\Controllers\NotificationController@show')->name('notif.show');

    //Message
    Route::resource('message', MessageController::class);
    Route::delete('/message/{id}', [MessageController::class, 'destroy'])->name('message.destroy');
    Route::get('/message/five','App\Http\Controllers\MessageController@messageFive')->name('messages.five');
    Route::get('/mobiledetails', 'App\Http\Controllers\EmployeeController@getMobile');

    //Password Change
    Route::get('change-password', 'App\Http\Controllers\HomeController@changePassword')->name('change.password.form');
    Route::post('change-password', 'App\Http\Controllers\HomeController@changPasswordStore')->name('change.password');
	
    //User Route
    Route::get('users', [UsersController::class,'index'])->name('user.index');
    Route::get('user-create', [UsersController::class,'create'])->name('user.create');
    Route::post('user-create', [UsersController::class,'store'])->name('user.store');
    Route::get('user-edit/{id}', [UsersController::class,'edit'])->name('user.edit');
    Route::post('user-update/{id}', [UsersController::class,'update'])->name('user.update');
    Route::get('users-delete/{id}', [UsersController::class,'destroy'])->name('user.destroy');
    
    //Process Routes
    Route::get('/servicedetails', 'App\Http\Controllers\ProcessController@getService');
    Route::post('/autocomplete-search', [ProcessController::class, 'autocompleteSearch'])->name('employees.searchEmployees');

    Route::post('getprocess/{id}',[ProcessController::class, 'getprocess']);
    Route::resource('process', ProcessController::class);
    Route::get('process/index/{id}','App\Http\Controllers\ProcessController@index')->name('process.index');
    Route::post('/process/update/{id}/{cntprocess}/{cntres}','App\Http\Controllers\ProcessController@update')->name('process.update');
    Route::get('/processdel/{id}','App\Http\Controllers\ProcessController@destroy')->name('process.delete');
    Route::get('bulkupdate','App\Http\Controllers\ProcessController@bulkedit')->name('process.bulkedit');
    Route::post('bulkupdate','App\Http\Controllers\ProcessController@bulkupdate')->name('process.bulkupdate');
	
    //Services Routes
    Route::resource('service', ServiceController::class);
    Route::get('getservice','App\Http\Controllers\ServiceController@index')->name('services.index');
    Route::get('service-show/{id}','App\Http\Controllers\ServiceController@show')->name('services.show');
    Route::post('/services/{id}','App\Http\Controllers\ServiceController@update')->name('services.update');
    Route::get('/servicedel/{id}','App\Http\Controllers\ServiceController@destroy')->name('servicedel.destroy');

    //Checklist Routes
    Route::get('service-checklist/{id}','App\Http\Controllers\ChecklistController@index')->name('checklist.index');
    Route::get('service-checklist-create/{id}','App\Http\Controllers\ChecklistController@create')->name('checklist.create');
    Route::get('service-checklist-edit/{id}','App\Http\Controllers\ChecklistController@edit')->name('checklist.edit');
    Route::put('service-checklist-update/{id}','App\Http\Controllers\ChecklistController@update')->name('checklist.update');
    Route::post('service-checklist-store','App\Http\Controllers\ChecklistController@store')->name('checklist.store');
    Route::delete('service-checklist-delete/{id}','App\Http\Controllers\ChecklistController@destroy')->name('checklist.destroy');
    
    //File Manager
    Route::get('/file-manager',function(){
        return view('layouts.file-manager');
    })->name('file-manager');
    
    //Attendance Routes
    Route::get('zonalattendance', 'App\Http\Controllers\AttendanceController@index')->name('attendance.index');
    Route::get('schoolatten', 'App\Http\Controllers\AttendanceController@index');
    Route::get('attendance-schools', 'App\Http\Controllers\AttendanceController@index')->name('attendance.list');

    //User Role Routes
    Route::resource('roles', RoleController::class);

    //User Permissions
    Route::resource('permissions', PermissionsController::class);
    
    //Todo
    Route::get('todoleft', 'App\Http\Controllers\TodoController@index');
    Route::get('tododone', 'App\Http\Controllers\TodoController@donelist');
    Route::post('addtodo', 'App\Http\Controllers\TodoController@store');
    Route::post('edittodo', 'App\Http\Controllers\TodoController@update');
    Route::delete('deletetodo/{id}', 'App\Http\Controllers\TodoController@destroy');
    
    //News for website
    Route::resource('news', NewsController::class);
    Route::delete('/news/photo/{id}', [NewsController::class, 'destroyPhoto'])->name('news.photo.destroy');

    //File Manager
    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function (){
        \UniSharp\LaravelFilemanager\Lfm::routes();
        });
        
    //Automation-Transfer
    Route::get('transfer-list','App\Http\Controllers\TransferController@index')->name('transfer.index');
    Route::get('/print-transfer/{id}','App\Http\Controllers\ProcessController@print_transfer')->name('process.print_transfer');
    Route::post('/differ-transfer/{id}','App\Http\Controllers\ProcessController@differ_transfer')->name('process.differ_transfer');
    Route::get('send/mail', 'App\Http\Controllers\ProcessController@send_mail');
    
    Route::resource('programme', ProgrammeController::class);
    
    // Upload PDF routes
    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::get('/upload/create', [UploadController::class, 'create'])->name('upload.create');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::get('/upload/{upload}/edit', [UploadController::class, 'edit'])->name('upload.edit');
    Route::patch('/upload/{upload}', [UploadController::class, 'update'])->name('upload.update');
    Route::delete('/upload/{id}', [UploadController::class, 'destroy'])->name('upload.destroy');

});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
