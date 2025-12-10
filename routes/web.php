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

// --- AUTHENTICATION ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- DASHBOARD ---
Route::get('/dashboard/admin', [AuthController::class, 'adminDashboard'])->name('dashboard.admin')->middleware(['auth']);
Route::get('/dashboard/user', [AuthController::class, 'userDashboard'])->name('dashboard.user')->middleware(['auth']);

// --- MIDDLEWARE AUTH GROUP ---
Route::middleware(['auth'])->group(function () {

    // 1. Form Fields & Settings
    Route::resource('form_fields', FormFieldController::class)->except(['show']);
    Route::get('form_fields/settings', [FormFieldController::class, 'index'])->name('form_fields.settings');
    Route::post('form_fields/settings', [FormFieldController::class, 'updateSettings'])->name('form_fields.update');
    Route::post('form_fields/update-all', [FormFieldController::class, 'updateAll'])->name('form_fields.update_all'); // <-- Ini yang bikin error sebelumnya
    
    Route::post('form_fields/store-category', [FormFieldController::class, 'storeCategory']);
    Route::post('form_fields/update-category/{oldCategory}', [FormFieldController::class, 'updateCategory']);
    Route::post('form_fields/destroy-category/{category}', [FormFieldController::class, 'destroyCategory']);
    Route::post('form_fields/store-field', [FormFieldController::class, 'storeField'])->name('form_fields.storeField');
    Route::post('form_fields/{id}/delete', [FormFieldController::class, 'destroyField'])->name('form_fields.destroyField');

    // 2. Academic Years
    Route::resource('academic_years', AcademicYearController::class)->except(['show']);
    Route::post('academic_years/{academic_year}/set-active', [AcademicYearController::class, 'setActive'])->name('academic_years.setActive');
    Route::post('academic_years/{academic_year}/unset-active', [AcademicYearController::class, 'unsetActive'])->name('academic_years.unsetActive');
    Route::delete('academic_years/bulk-delete', [AcademicYearController::class, 'bulkDelete'])->name('academic_years.bulkDelete');

    // 3. Announcements
    Route::resource('announcements', AnnouncementController::class)->except(['show']);
    Route::post('announcements/{announcement}/toggle-login', [AnnouncementController::class, 'toggleShowOnLogin'])->name('announcements.toggleShowOnLogin');

    // 4. Users (Peserta)
    // --- BULK DELETE USERS (Letakkan SEBELUM resource) ---
    Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk_delete');
    // -----------------------------------------------------
    // *. Letakkan route export di ATAS resource users
    Route::get('users/export-excel', [UserController::class, 'exportExcel'])->name('users.export_excel');
    // **. Baru route resource di BAWAHNYA
    Route::resource('users', UserController::class)->except(['create', 'store']);

    // 5. Fitur Siswa (Download & Cetak)
    Route::get('user/download-registration-form', [UserController::class, 'downloadRegistrationForm'])->name('user.downloadRegistrationForm');
    Route::get('/user/cetak-bukti-lulus', [UserController::class, 'printAcceptance'])->name('user.print_acceptance');

    // 6. Dokumen User
    Route::get('user/documents', [UserDocumentController::class, 'index'])->name('user.documents.index');
    Route::post('user/documents', [UserDocumentController::class, 'store'])->name('user.documents.store');
    Route::delete('user/documents/{type}', [UserDocumentController::class, 'destroy'])->name('user.documents.destroy');
});

// --- ADMIN EXAM ROUTES ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('exam-questions', \App\Http\Controllers\Admin\ExamQuestionController::class)->except(['edit', 'update']);
    Route::post('exam-answers/{answer}/update', [\App\Http\Controllers\Admin\ExamQuestionController::class, 'updateAnswer'])->name('exam-answers.update');
});

// --- USER EXAM ROUTES ---
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/exam', [\App\Http\Controllers\User\ExamController::class, 'index'])->name('exam.index');
    Route::get('/exam/{question}/download', [\App\Http\Controllers\User\ExamController::class, 'download'])->name('exam.download');
    Route::post('/exam/{question}/submit', [\App\Http\Controllers\User\ExamController::class, 'submit'])->name('exam.submit');
});

Route::get('users/test', function() { return 'OK'; })->middleware('auth');

// Route untuk Download Excel/CSV
Route::get('users/export-excel', [App\Http\Controllers\UserController::class, 'exportExcel'])->name('users.export_excel');