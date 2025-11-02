<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDocumentController;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard.admin');
        } else {
            return redirect()->route('dashboard.user');
        }
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/dashboard/admin', [AuthController::class, 'adminDashboard'])->name('dashboard.admin')->middleware(['auth']);
Route::get('/dashboard/user', [AuthController::class, 'userDashboard'])->name('dashboard.user');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::resource('form_fields', \App\Http\Controllers\FormFieldController::class)->except(['show']);
    Route::resource('academic_years', AcademicYearController::class)->except(['show']);
    Route::resource('announcements', AnnouncementController::class)->except(['show']);
    Route::post('announcements/{announcement}/toggle-login', [AnnouncementController::class, 'toggleShowOnLogin'])->name('announcements.toggleShowOnLogin');
    Route::post('academic_years/{academic_year}/set-active', [AcademicYearController::class, 'setActive'])->name('academic_years.setActive');
    Route::post('academic_years/{academic_year}/unset-active', [AcademicYearController::class, 'unsetActive'])->name('academic_years.unsetActive');
    Route::delete('academic_years/bulk-delete', [AcademicYearController::class, 'bulkDelete'])->name('academic_years.bulkDelete');
    Route::resource('users', UserController::class)->except(['create', 'store']);
    Route::get('user/download-registration-form', [UserController::class, 'downloadRegistrationForm'])->name('user.downloadRegistrationForm');
    Route::get('form_fields/settings', [\App\Http\Controllers\FormFieldController::class, 'index'])->name('form_fields.settings');
    Route::post('form_fields/settings', [\App\Http\Controllers\FormFieldController::class, 'updateSettings'])->name('form_fields.update');
    Route::get('user/documents', [\App\Http\Controllers\UserDocumentController::class, 'index'])->name('user.documents.index');
    Route::post('user/documents', [\App\Http\Controllers\UserDocumentController::class, 'store'])->name('user.documents.store');
    Route::delete('user/documents/{type}', [\App\Http\Controllers\UserDocumentController::class, 'destroy'])->name('user.documents.destroy');
    
    Route::post('form_fields/store-category', [\App\Http\Controllers\FormFieldController::class, 'storeCategory']);
    Route::post('form_fields/update-category/{oldCategory}', [\App\Http\Controllers\FormFieldController::class, 'updateCategory']);
    Route::post('form_fields/destroy-category/{category}', [\App\Http\Controllers\FormFieldController::class, 'destroyCategory']);
    Route::post('form_fields/store-field', [\App\Http\Controllers\FormFieldController::class, 'storeField'])->name('form_fields.storeField');
    Route::post('form_fields/{id}/delete', [\App\Http\Controllers\FormFieldController::class, 'destroyField'])->name('form_fields.destroyField');
});

// Admin Exam Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('exam-questions', \App\Http\Controllers\Admin\ExamQuestionController::class)->except(['edit', 'update']);
    Route::post('exam-answers/{answer}/update', [\App\Http\Controllers\Admin\ExamQuestionController::class, 'updateAnswer'])->name('exam-answers.update');
});

// User Exam Routes
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/exam', [\App\Http\Controllers\User\ExamController::class, 'index'])->name('exam.index');
    Route::get('/exam/{question}/download', [\App\Http\Controllers\User\ExamController::class, 'download'])->name('exam.download');
    Route::post('/exam/{question}/submit', [\App\Http\Controllers\User\ExamController::class, 'submit'])->name('exam.submit');
});
Route::get('users/test', function() { return 'OK'; })->middleware('auth');

// routes/web.php

// ... (route Anda yang lain)
// TAMBAHKAN INI:
Route::post('form_fields/update-all', [FormFieldController::class, 'updateAll'])->name('form_fields.update_all');