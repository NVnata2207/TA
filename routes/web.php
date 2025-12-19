<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\FormFieldController; 
use App\Http\Controllers\DocumentRequirementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

    // Pastikan menggunakan UserController::class
    Route::get('/notification/read/{id}', [\App\Http\Controllers\UserController::class, 'markNotificationRead'])->name('notification.read');
    Route::get('/notification/read-all', [\App\Http\Controllers\UserController::class, 'markAllRead'])->name('notification.readAll');

    // =========================================================================
    // BAGIAN PENTING: PENGATURAN FORMULIR (JANGAN DIUBAH LAGI)
    // =========================================================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // 1. Halaman Utama Pengaturan (URL: /admin/form-settings)
        Route::get('/form-settings', [FormFieldController::class, 'index'])->name('form_fields.index'); 

        // 2. Kelola Kategori (Group)
        Route::post('/category', [FormFieldController::class, 'storeCategory'])->name('category.store');
        Route::put('/category/{oldCategory}', [FormFieldController::class, 'updateCategory'])->name('category.update');
        Route::delete('/category/{category}', [FormFieldController::class, 'destroyCategory'])->name('category.destroy');

        // ... (Route index dan kategori di atasnya biarkan saja) ...

        // 3. Simpan Checklist Utama / Simpan Pengaturan (Update All)
        Route::post('/form-settings/update', [FormFieldController::class, 'updateAll'])->name('form.settings');

        // 4. Tambah Field Baru
        Route::post('/form-settings/field', [FormFieldController::class, 'storeField'])->name('form.storeField');

        // =================================================================
        //  👇 TAMBAHKAN BARIS INI DI SINI (Untuk Update/Edit Field) 👇
        // =================================================================
        Route::put('/form-settings/field/{id}', [FormFieldController::class, 'updateField'])->name('form.updateField');
        // =================================================================

        // 5. Hapus Field
        Route::delete('/form-settings/field/{id}', [FormFieldController::class, 'destroyField'])->name('form.destroyField');
    });

    // =========================================================================
    // BAGIAN LAINNYA
    // =========================================================================

    // Academic Years
    Route::resource('academic_years', AcademicYearController::class)->except(['show']);
    Route::post('academic_years/{academic_year}/set-active', [AcademicYearController::class, 'setActive'])->name('academic_years.setActive');
    Route::post('academic_years/{academic_year}/unset-active', [AcademicYearController::class, 'unsetActive'])->name('academic_years.unsetActive');
    Route::delete('academic_years/bulk-delete', [AcademicYearController::class, 'bulkDelete'])->name('academic_years.bulkDelete');

    // Announcements
    Route::resource('announcements', AnnouncementController::class)->except(['show']);
    Route::post('announcements/{announcement}/toggle-login', [AnnouncementController::class, 'toggleShowOnLogin'])->name('announcements.toggleShowOnLogin');

    // Users (Peserta)
    Route::delete('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk_delete');
    Route::get('users/export-excel', [UserController::class, 'exportExcel'])->name('users.export_excel');
    Route::resource('users', UserController::class)->except(['create', 'store']);

    // Fitur Siswa (Download & Cetak)
    Route::get('user/download-registration-form', [UserController::class, 'downloadRegistrationForm'])->name('user.downloadRegistrationForm');
    Route::get('/user/cetak-bukti-lulus', [UserController::class, 'printAcceptance'])->name('user.print_acceptance');
    // Route untuk membuka halaman isi formulir
    Route::get('/user/formulir-pendaftaran', [UserController::class, 'showForm'])->name('user.form');

    // Route untuk menyimpan data formulir (Sudah ada di jawaban sebelumnya, pastikan ada)
    Route::put('/user/update-profile', [UserController::class, 'updateProfile'])->name('user.update_profile');

    // Dokumen User
    Route::get('user/documents', [UserDocumentController::class, 'index'])->name('user.documents.index');
    Route::post('user/documents', [UserDocumentController::class, 'store'])->name('user.documents.store');
    Route::delete('user/documents/{type}', [UserDocumentController::class, 'destroy'])->name('user.documents.destroy');
});

// --- EXAM ROUTES ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('exam-questions', \App\Http\Controllers\Admin\ExamQuestionController::class)->except(['edit', 'update']);
    Route::post('exam-answers/{answer}/update', [\App\Http\Controllers\Admin\ExamQuestionController::class, 'updateAnswer'])->name('exam-answers.update');
});

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/exam', [\App\Http\Controllers\User\ExamController::class, 'index'])->name('exam.index');
    Route::get('/exam/{question}/download', [\App\Http\Controllers\User\ExamController::class, 'download'])->name('exam.download');
    Route::post('/exam/{question}/submit', [\App\Http\Controllers\User\ExamController::class, 'submit'])->name('exam.submit');
});

// --- PENGATURAN DOKUMEN ---
    Route::get('/document-settings', [App\Http\Controllers\DocumentRequirementController::class, 'index'])->name('admin.documents.index');
    Route::post('/document-settings', [App\Http\Controllers\DocumentRequirementController::class, 'store'])->name('admin.documents.store');
    Route::delete('/document-settings/{id}', [App\Http\Controllers\DocumentRequirementController::class, 'destroy'])->name('admin.documents.destroy');

Route::get('users/test', function() { return 'OK'; })->middleware('auth');