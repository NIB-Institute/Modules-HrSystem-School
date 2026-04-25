<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Dashboard\V1\ClassroomController;
use Modules\School\Http\Controllers\Dashboard\V1\CourseController;
use Modules\School\Http\Controllers\Dashboard\V1\DepartmentController;
use Modules\School\Http\Controllers\Dashboard\V1\EquipmentController;
use Modules\School\Http\Controllers\Dashboard\V1\ProgramController;
use Modules\School\Http\Controllers\Dashboard\V1\SchoolController;
use Modules\School\Http\Controllers\Dashboard\V1\SchoolImportExportController;
use Modules\School\Http\Controllers\Dashboard\V1\DepartmentTrashController;
use Modules\School\Http\Controllers\Dashboard\V1\ClassroomTrashController;
use Modules\School\Http\Controllers\Dashboard\V1\CourseTrashController;
use Modules\School\Http\Controllers\Dashboard\V1\ProgramTrashController;
use Modules\School\Http\Controllers\Dashboard\V1\EquipmentTrashController;
use Modules\School\Http\Controllers\Dashboard\V1\SchoolTrashController;

/*
|--------------------------------------------------------------------------
| School Module Dashboard Routes
|--------------------------------------------------------------------------
|
| Using 'auto.permission' middleware which automatically resolves permissions
| from route names. Route naming pattern: {resource}.{action}
|
| Permission mapping:
| - index -> view_any
| - create/store -> create
| - show -> view
| - edit/update -> update
| - destroy/delete -> delete
|
*/

Route::middleware(['auth', 'verified', 'auto.permission'])->prefix('dashboard')->group(function () {

    // ==================== IMPORT/EXPORT ROUTES (before parameterized routes) ====================

    // Departments Import/Export
    Route::get('departments/export', [SchoolImportExportController::class, 'exportDepartments'])->name('departments.export');
    Route::get('departments/import', [SchoolImportExportController::class, 'showImportDepartments'])->name('departments.import');
    Route::post('departments/import', [SchoolImportExportController::class, 'importDepartments'])->name('departments.import.store');
    Route::post('departments/import/preview', [SchoolImportExportController::class, 'previewDepartments'])->name('departments.import.preview');
    Route::get('departments/template', [SchoolImportExportController::class, 'downloadDepartmentsTemplate'])->name('departments.template');

    // Classrooms Import/Export
    Route::get('classrooms/export', [SchoolImportExportController::class, 'exportClassrooms'])->name('classrooms.export');
    Route::get('classrooms/import', [SchoolImportExportController::class, 'showImportClassrooms'])->name('classrooms.import');
    Route::post('classrooms/import', [SchoolImportExportController::class, 'importClassrooms'])->name('classrooms.import.store');
    Route::post('classrooms/import/preview', [SchoolImportExportController::class, 'previewClassrooms'])->name('classrooms.import.preview');
    Route::get('classrooms/template', [SchoolImportExportController::class, 'downloadClassroomsTemplate'])->name('classrooms.template');

    // Courses Import/Export
    Route::get('courses/export', [SchoolImportExportController::class, 'exportCourses'])->name('courses.export');
    Route::get('courses/import', [SchoolImportExportController::class, 'showImportCourses'])->name('courses.import');
    Route::post('courses/import', [SchoolImportExportController::class, 'importCourses'])->name('courses.import.store');
    Route::post('courses/import/preview', [SchoolImportExportController::class, 'previewCourses'])->name('courses.import.preview');
    Route::get('courses/template', [SchoolImportExportController::class, 'downloadCoursesTemplate'])->name('courses.template');

    // Programs Import/Export
    Route::get('programs/export', [SchoolImportExportController::class, 'exportPrograms'])->name('programs.export');
    Route::get('programs/import', [SchoolImportExportController::class, 'showImportPrograms'])->name('programs.import');
    Route::post('programs/import', [SchoolImportExportController::class, 'importPrograms'])->name('programs.import.store');
    Route::post('programs/import/preview', [SchoolImportExportController::class, 'previewPrograms'])->name('programs.import.preview');
    Route::get('programs/template', [SchoolImportExportController::class, 'downloadProgramsTemplate'])->name('programs.template');

    // Equipment Import/Export
    Route::get('equipment/export', [SchoolImportExportController::class, 'exportEquipment'])->name('equipment.export');
    Route::get('equipment/import', [SchoolImportExportController::class, 'showImportEquipment'])->name('equipment.import');
    Route::post('equipment/import', [SchoolImportExportController::class, 'importEquipment'])->name('equipment.import.store');
    Route::post('equipment/import/preview', [SchoolImportExportController::class, 'previewEquipment'])->name('equipment.import.preview');
    Route::get('equipment/template', [SchoolImportExportController::class, 'downloadEquipmentTemplate'])->name('equipment.template');

    // ==================== TRASH ROUTES ====================

    // Departments Trash
    Route::get('departments/trash', [DepartmentTrashController::class, 'index'])->name('departments.trash.index');
    Route::put('departments/{uuid}/restore', [DepartmentTrashController::class, 'restore'])->name('departments.trash.restore');
    Route::delete('departments/{uuid}/force-delete', [DepartmentTrashController::class, 'forceDelete'])->name('departments.trash.force-delete');
    Route::delete('departments/trash/empty', [DepartmentTrashController::class, 'empty'])->name('departments.trash.empty');
    Route::put('departments/trash/bulk-restore', [DepartmentTrashController::class, 'bulkRestore'])->name('departments.trash.bulk-restore');
    Route::delete('departments/trash/bulk-force-delete', [DepartmentTrashController::class, 'bulkForceDelete'])->name('departments.trash.bulk-force-delete');

    // Classrooms Trash
    Route::get('classrooms/trash', [ClassroomTrashController::class, 'index'])->name('classrooms.trash.index');
    Route::put('classrooms/{uuid}/restore', [ClassroomTrashController::class, 'restore'])->name('classrooms.trash.restore');
    Route::delete('classrooms/{uuid}/force-delete', [ClassroomTrashController::class, 'forceDelete'])->name('classrooms.trash.force-delete');
    Route::delete('classrooms/trash/empty', [ClassroomTrashController::class, 'empty'])->name('classrooms.trash.empty');
    Route::put('classrooms/trash/bulk-restore', [ClassroomTrashController::class, 'bulkRestore'])->name('classrooms.trash.bulk-restore');
    Route::delete('classrooms/trash/bulk-force-delete', [ClassroomTrashController::class, 'bulkForceDelete'])->name('classrooms.trash.bulk-force-delete');

    // Courses Trash
    Route::get('courses/trash', [CourseTrashController::class, 'index'])->name('courses.trash.index');
    Route::put('courses/{uuid}/restore', [CourseTrashController::class, 'restore'])->name('courses.trash.restore');
    Route::delete('courses/{uuid}/force-delete', [CourseTrashController::class, 'forceDelete'])->name('courses.trash.force-delete');
    Route::delete('courses/trash/empty', [CourseTrashController::class, 'empty'])->name('courses.trash.empty');
    Route::put('courses/trash/bulk-restore', [CourseTrashController::class, 'bulkRestore'])->name('courses.trash.bulk-restore');
    Route::delete('courses/trash/bulk-force-delete', [CourseTrashController::class, 'bulkForceDelete'])->name('courses.trash.bulk-force-delete');

    // Programs Trash
    Route::get('programs/trash', [ProgramTrashController::class, 'index'])->name('programs.trash.index');
    Route::put('programs/{uuid}/restore', [ProgramTrashController::class, 'restore'])->name('programs.trash.restore');
    Route::delete('programs/{uuid}/force-delete', [ProgramTrashController::class, 'forceDelete'])->name('programs.trash.force-delete');
    Route::delete('programs/trash/empty', [ProgramTrashController::class, 'empty'])->name('programs.trash.empty');
    Route::put('programs/trash/bulk-restore', [ProgramTrashController::class, 'bulkRestore'])->name('programs.trash.bulk-restore');
    Route::delete('programs/trash/bulk-force-delete', [ProgramTrashController::class, 'bulkForceDelete'])->name('programs.trash.bulk-force-delete');

    // Equipment Trash
    Route::get('equipment/trash', [EquipmentTrashController::class, 'index'])->name('equipment.trash.index');
    Route::put('equipment/{uuid}/restore', [EquipmentTrashController::class, 'restore'])->name('equipment.trash.restore');
    Route::delete('equipment/{uuid}/force-delete', [EquipmentTrashController::class, 'forceDelete'])->name('equipment.trash.force-delete');
    Route::delete('equipment/trash/empty', [EquipmentTrashController::class, 'empty'])->name('equipment.trash.empty');
    Route::put('equipment/trash/bulk-restore', [EquipmentTrashController::class, 'bulkRestore'])->name('equipment.trash.bulk-restore');
    Route::delete('equipment/trash/bulk-force-delete', [EquipmentTrashController::class, 'bulkForceDelete'])->name('equipment.trash.bulk-force-delete');

    // Schools Trash
    Route::get('schools/trash', [SchoolTrashController::class, 'index'])->name('schools.trash.index');
    Route::put('schools/{uuid}/restore', [SchoolTrashController::class, 'restore'])->name('schools.trash.restore');
    Route::delete('schools/{uuid}/force-delete', [SchoolTrashController::class, 'forceDelete'])->name('schools.trash.force-delete');
    Route::delete('schools/trash/empty', [SchoolTrashController::class, 'empty'])->name('schools.trash.empty');
    Route::put('schools/trash/bulk-restore', [SchoolTrashController::class, 'bulkRestore'])->name('schools.trash.bulk-restore');
    Route::delete('schools/trash/bulk-force-delete', [SchoolTrashController::class, 'bulkForceDelete'])->name('schools.trash.bulk-force-delete');

    // ==================== CRUD ROUTES ====================

    // Schools - using resource with explicit route ordering
    Route::get('schools/create', [SchoolController::class, 'create'])->name('schools.create');
    Route::post('schools', [SchoolController::class, 'store'])->name('schools.store');
    Route::get('schools', [SchoolController::class, 'index'])->name('schools.index');
    Route::get('schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
    Route::get('schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
    Route::put('schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
    Route::patch('schools/{school}', [SchoolController::class, 'update']);
    Route::put('schools/{school}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('schools.toggle-status');
    Route::get('schools/{school}/delete', [SchoolController::class, 'confirmDelete'])->name('schools.delete');
    Route::delete('schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');

    // Departments
    Route::get('departments/bulk-delete', [DepartmentController::class, 'confirmBulkDelete'])->name('departments.bulk-delete.confirm');
    Route::delete('departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])->name('departments.bulk-delete');
    Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('api/departments', [DepartmentController::class, 'getDepartments'])->name('departments.api');
    Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::get('departments/{department}/qr-code', [DepartmentController::class, 'qrCode'])->name('departments.qr-code');
    Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::patch('departments/{department}', [DepartmentController::class, 'update']);
    Route::put('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    Route::get('departments/{department}/delete', [DepartmentController::class, 'confirmDelete'])->name('departments.delete');
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Programs
    Route::get('programs/bulk-delete', [ProgramController::class, 'confirmBulkDelete'])->name('programs.bulk-delete.confirm');
    Route::delete('programs/bulk-delete', [ProgramController::class, 'bulkDelete'])->name('programs.bulk-delete');
    Route::get('programs/create', [ProgramController::class, 'create'])->name('programs.create');
    Route::post('programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::get('programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('api/programs', [ProgramController::class, 'getPrograms'])->name('programs.api');
    Route::get('programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
    Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
    Route::put('programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
    Route::patch('programs/{program}', [ProgramController::class, 'update']);
    Route::put('programs/{program}/toggle-status', [ProgramController::class, 'toggleStatus'])->name('programs.toggle-status');
    Route::get('programs/{program}/delete', [ProgramController::class, 'confirmDelete'])->name('programs.delete');
    Route::delete('programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');

    // Courses
    Route::get('courses/bulk-delete', [CourseController::class, 'confirmBulkDelete'])->name('courses.bulk-delete.confirm');
    Route::delete('courses/bulk-delete', [CourseController::class, 'bulkDelete'])->name('courses.bulk-delete');
    Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::patch('courses/{course}', [CourseController::class, 'update']);
    Route::put('courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    Route::get('courses/{course}/delete', [CourseController::class, 'confirmDelete'])->name('courses.delete');
    Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Classrooms
    Route::get('classrooms/bulk-delete', [ClassroomController::class, 'confirmBulkDelete'])->name('classrooms.bulk-delete.confirm');
    Route::delete('classrooms/bulk-delete', [ClassroomController::class, 'bulkDelete'])->name('classrooms.bulk-delete');
    Route::get('classrooms/create', [ClassroomController::class, 'create'])->name('classrooms.create');
    Route::post('classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
    Route::get('classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::get('classrooms/{classroom}', [ClassroomController::class, 'show'])->name('classrooms.show');
    Route::get('classrooms/{classroom}/qr-code', [ClassroomController::class, 'qrCode'])->name('classrooms.qr-code');
    Route::get('classrooms/{classroom}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
    Route::put('classrooms/{classroom}', [ClassroomController::class, 'update'])->name('classrooms.update');
    Route::patch('classrooms/{classroom}', [ClassroomController::class, 'update']);
    Route::put('classrooms/{classroom}/toggle-status', [ClassroomController::class, 'toggleStatus'])->name('classrooms.toggle-status');
    Route::get('classrooms/{classroom}/delete', [ClassroomController::class, 'confirmDelete'])->name('classrooms.delete');
    Route::delete('classrooms/{classroom}', [ClassroomController::class, 'destroy'])->name('classrooms.destroy');

    // Equipment
    Route::get('equipment/bulk-delete', [EquipmentController::class, 'confirmBulkDelete'])->name('equipment.bulk-delete.confirm');
    Route::delete('equipment/bulk-delete', [EquipmentController::class, 'bulkDelete'])->name('equipment.bulk-delete');
    Route::get('equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
    Route::post('equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
    Route::get('equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::put('equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::patch('equipment/{equipment}', [EquipmentController::class, 'update']);
    Route::get('equipment/{equipment}/delete', [EquipmentController::class, 'confirmDelete'])->name('equipment.delete');
    Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
});
