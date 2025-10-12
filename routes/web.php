<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrarController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssignmentDisplayController;

Route::get('/', function () {
    return view('welcome');
});

// public display of assignments
Route::get('/assignments', [AssignmentDisplayController::class, 'index'])->name('assignments.display');
Route::get('/assignments/{assignment}', [AssignmentDisplayController::class, 'show'])->name('assignments.display.show');

// Student submission endpoint
Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\AssignmentDisplayController::class, 'submit'])->middleware(['auth','role:student'])->name('assignments.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Student quiz routes
Route::prefix('student')->name('student.')->middleware(['auth','role:student'])->group(function(){
    Route::get('quizzes', [\App\Http\Controllers\Student\QuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [\App\Http\Controllers\Student\QuizController::class, 'show'])->name('quizzes.show');
    Route::post('quizzes/{quiz}/submit', [\App\Http\Controllers\Student\QuizController::class, 'submit'])->name('quizzes.submit');
    
    // Student enrollment routes
    Route::get('enrollments', [\App\Http\Controllers\Student\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('enrollments/create', [\App\Http\Controllers\Student\EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('enrollments', [\App\Http\Controllers\Student\EnrollmentController::class, 'store'])->name('enrollments.store');
    
    // New enrollment wizard routes
    Route::prefix('enrollment')->name('enrollment.')->group(function () {
        Route::get('wizard', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'index'])->name('wizard');
        Route::get('wizard/{step}', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'showStep'])->name('wizard.step');
        Route::post('wizard/personal-info', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'savePersonalInfo'])->name('wizard.personal-info');
        Route::post('wizard/documents', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'uploadDocument'])->name('wizard.documents');
        Route::post('wizard/course-selection', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'saveCourseSelection'])->name('wizard.course-selection');
        Route::post('wizard/submit', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'submit'])->name('wizard.submit');
        Route::get('status', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'status'])->name('status');
        Route::get('documents/{document}/download', [\App\Http\Controllers\Student\EnrollmentWizardController::class, 'downloadDocument'])->name('documents.download');
    });
    
    // Student announcements
    Route::get('announcements', [\App\Http\Controllers\Student\AnnouncementController::class, 'index'])->name('announcements.index');
});

// Role-based dashboards (organized controllers/views)
Route::middleware(['auth','role:student'])->get('/dashboard/student', [StudentController::class, 'index'])->name('dashboard.student');
Route::middleware(['auth','role:instructor'])->get('/dashboard/instructor', [InstructorController::class, 'index'])->name('dashboard.instructor');
Route::middleware(['auth','role:admin'])->get('/dashboard/admin', [AdminController::class, 'index'])->name('dashboard.admin');
Route::middleware(['auth','role:registrar'])->get('/dashboard/registrar', [RegistrarController::class, 'index'])->name('dashboard.registrar');

require __DIR__.'/auth.php';

// Admin area routes
Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Courses
    Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [\App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');

        // Assignments
        Route::resource('assignments', \App\Http\Controllers\Admin\AssignmentController::class)->only(['index','create','store','show','destroy']);

    // Attach students to an assignment from the admin assignment display
    Route::post('assignments/{assignment}/students', [\App\Http\Controllers\Admin\AssignmentController::class, 'addStudents'])->name('assignments.students');

        Route::post('assignments/{assignment}/questions', [\App\Http\Controllers\Admin\AssignmentQuestionController::class, 'store'])->name('assignments.questions.store');
        Route::get('assignments/{assignment}/questions/create', [\App\Http\Controllers\Admin\AssignmentQuestionController::class, 'create'])->name('assignments.questions.create');
        Route::delete('assignments/{assignment}/questions/{question}', [\App\Http\Controllers\Admin\AssignmentQuestionController::class, 'destroy'])->name('assignments.questions.destroy');
    // admin: view submissions and grade
    Route::get('assignments/{assignment}/submissions', [\App\Http\Controllers\Admin\AssignmentSubmissionController::class, 'index'])->name('assignments.submissions.index');
    Route::post('assignments/{assignment}/submissions/{submission}/grade', [\App\Http\Controllers\Admin\AssignmentSubmissionController::class, 'grade'])->name('assignments.submissions.grade');
    // Central grading dashboard (admin)
    Route::get('grading', [\App\Http\Controllers\Admin\GradingController::class, 'index'])->name('grading.index');
    
    // Quizzes
    Route::get('quizes', [\App\Http\Controllers\Admin\QuizController::class, 'index'])->name('quizes.index');
    Route::get('quizes/create', [\App\Http\Controllers\Admin\QuizController::class, 'create'])->name('quizes.create');
    Route::post('quizes', [\App\Http\Controllers\Admin\QuizController::class, 'store'])->name('quizes.store');
    
    // Enrollment management
    Route::get('enrollments', [\App\Http\Controllers\Admin\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('enrollments/{enrollmentRequest}', [\App\Http\Controllers\Admin\EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::get('enrollments/{enrollmentRequest}/documents/{document}/download', [\App\Http\Controllers\Admin\EnrollmentController::class, 'downloadDocument'])->name('enrollments.documents.download');
    Route::get('enrollments/{enrollmentRequest}/documents/{document}/preview', [\App\Http\Controllers\Admin\EnrollmentController::class, 'previewDocument'])->name('enrollments.documents.preview');
    Route::post('enrollments/{enrollmentRequest}/documents/{document}/verify', [\App\Http\Controllers\Admin\EnrollmentController::class, 'verifyDocument'])->name('enrollments.documents.verify');
    Route::post('enrollments/{enrollmentRequest}/approve', [\App\Http\Controllers\Admin\EnrollmentController::class, 'approve'])->name('enrollments.approve');
    Route::post('enrollments/{enrollmentRequest}/reject', [\App\Http\Controllers\Admin\EnrollmentController::class, 'reject'])->name('enrollments.reject');
    
    // Announcements
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
});
