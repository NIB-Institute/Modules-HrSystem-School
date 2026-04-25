<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\School\Exports\DepartmentsExport;
use Modules\School\Exports\ClassroomsExport;
use Modules\School\Exports\CoursesExport;
use Modules\School\Exports\ProgramsExport;
use Modules\School\Exports\EquipmentExport;
use Modules\School\Imports\DepartmentsImport;
use Modules\School\Imports\ClassroomsImport;
use Modules\School\Imports\CoursesImport;
use Modules\School\Imports\ProgramsImport;
use Modules\School\Imports\EquipmentImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SchoolImportExportController extends Controller
{
    protected array $duplicateOptions = [
        ['value' => 'skip', 'label' => 'Skip duplicates', 'description' => 'Skip rows with existing codes'],
        ['value' => 'update', 'label' => 'Update existing', 'description' => 'Update existing records with new data'],
        ['value' => 'fail', 'label' => 'Fail on duplicate', 'description' => 'Stop import if duplicates found'],
    ];

    // ==================== DEPARTMENTS ====================

    public function exportDepartments(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'school_id']);
        $filename = 'departments_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new DepartmentsExport($filters), $filename);
    }

    public function showImportDepartments(): Response
    {
        return Inertia::render('school::Dashboard/V1/Department/Import', [
            'duplicateOptions' => $this->duplicateOptions,
        ]);
    }

    public function previewDepartments(Request $request): JsonResponse
    {
        return $this->handlePreview($request, DepartmentsImport::class);
    }

    public function importDepartments(Request $request): RedirectResponse
    {
        return $this->handleImport($request, DepartmentsImport::class, 'school.departments.index', 'school.departments.import');
    }

    public function downloadDepartmentsTemplate(): BinaryFileResponse
    {
        $headers = ['Name', 'Code', 'School', 'Description', 'Email', 'Phone', 'Office Location', 'Established Year', 'Status'];
        $sampleData = ['Computer Science', 'CS', 'Royal University of Phnom Penh', 'Department of Computer Science', 'cs@school.edu', '+1234567890', 'Building A, Room 101', '2020', 'active'];
        $instructions = [
            'Name and Code are required fields',
            'School must match an existing school name exactly (case-sensitive)',
            'If School is empty, the default school will be used',
            'Code must be unique within the school',
            'Status: active or inactive (defaults to active)',
            'Established Year: YYYY format (e.g., 2020)',
        ];
        return $this->generateTemplate('departments', $headers, $sampleData, $instructions);
    }

    // ==================== CLASSROOMS ====================

    public function exportClassrooms(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'department_id', 'type']);
        $filename = 'classrooms_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new ClassroomsExport($filters), $filename);
    }

    public function showImportClassrooms(): Response
    {
        return Inertia::render('school::Dashboard/V1/Classroom/Import', [
            'duplicateOptions' => $this->duplicateOptions,
        ]);
    }

    public function previewClassrooms(Request $request): JsonResponse
    {
        return $this->handlePreview($request, ClassroomsImport::class);
    }

    public function importClassrooms(Request $request): RedirectResponse
    {
        return $this->handleImport($request, ClassroomsImport::class, 'school.classrooms.index', 'school.classrooms.import');
    }

    public function downloadClassroomsTemplate(): BinaryFileResponse
    {
        $headers = ['Name', 'Code', 'Department', 'Building', 'Floor', 'Capacity', 'Type', 'Has Projector', 'Has Whiteboard', 'Has AC', 'Description', 'Status'];
        $sampleData = ['Room 101', 'R101', 'Computer Science', 'Building A', '1', '30', 'classroom', 'yes', 'yes', 'no', 'Standard classroom', 'active'];
        $instructions = [
            'Name and Code are required fields',
            'Department must match an existing department name exactly (case-sensitive)',
            'Type: lecture_hall, classroom, lab, seminar, auditorium, workshop',
            'Has Projector/Whiteboard/AC: yes or no',
            'Status: active or inactive (defaults to active)',
        ];
        return $this->generateTemplate('classrooms', $headers, $sampleData, $instructions);
    }

    // ==================== COURSES ====================

    public function exportCourses(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'department_id', 'program_id', 'type']);
        $filename = 'courses_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new CoursesExport($filters), $filename);
    }

    public function showImportCourses(): Response
    {
        return Inertia::render('school::Dashboard/V1/Course/Import', [
            'duplicateOptions' => $this->duplicateOptions,
        ]);
    }

    public function previewCourses(Request $request): JsonResponse
    {
        return $this->handlePreview($request, CoursesImport::class);
    }

    public function importCourses(Request $request): RedirectResponse
    {
        return $this->handleImport($request, CoursesImport::class, 'school.courses.index', 'school.courses.import');
    }

    public function downloadCoursesTemplate(): BinaryFileResponse
    {
        $headers = ['Name', 'Code', 'Department', 'Program', 'Credits', 'Type', 'Semester', 'Year', 'Max Students', 'Schedule', 'Room', 'Description', 'Status'];
        $sampleData = ['Introduction to Programming', 'CS101', 'Computer Science', 'Computer Science BSc', '3', 'required', '1', '2024', '30', 'Mon/Wed 9:00-10:30', 'Room 101', 'Basic programming concepts', 'active'];
        $instructions = [
            'Name and Code are required fields',
            'Department must match an existing department name exactly (case-sensitive)',
            'Program must match an existing program name exactly (case-sensitive)',
            'Type: required, elective, core',
            'Credits: integer value',
            'Semester: 1 or 2',
            'Status: active or inactive (defaults to active)',
        ];
        return $this->generateTemplate('courses', $headers, $sampleData, $instructions);
    }

    // ==================== PROGRAMS ====================

    public function exportPrograms(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'school_id', 'department_id', 'degree_level']);
        $filename = 'programs_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new ProgramsExport($filters), $filename);
    }

    public function showImportPrograms(): Response
    {
        return Inertia::render('school::Dashboard/V1/Program/Import', [
            'duplicateOptions' => $this->duplicateOptions,
        ]);
    }

    public function previewPrograms(Request $request): JsonResponse
    {
        return $this->handlePreview($request, ProgramsImport::class);
    }

    public function importPrograms(Request $request): RedirectResponse
    {
        return $this->handleImport($request, ProgramsImport::class, 'school.programs.index', 'school.programs.import');
    }

    public function downloadProgramsTemplate(): BinaryFileResponse
    {
        $headers = ['Name', 'Code', 'School', 'Department', 'Degree Level', 'Duration Years', 'Credits Required', 'Tuition Fee', 'Max Students', 'Admission Requirements', 'Description', 'Status'];
        $sampleData = ['Computer Science BSc', 'CS-BSC', 'Royal University of Phnom Penh', 'Computer Science', 'bachelor', '4', '120', '5000', '50', 'High school diploma, Math proficiency', 'Bachelor of Science in Computer Science', 'active'];
        $instructions = [
            'Name and Code are required fields',
            'School must match an existing school name exactly (case-sensitive)',
            'If School is empty, the default school will be used',
            'Department must match an existing department name exactly',
            'Degree Level: certificate, diploma, associate, bachelor, master, doctorate',
            'Duration Years: integer value',
            'Status: active or inactive (defaults to active)',
        ];
        return $this->generateTemplate('programs', $headers, $sampleData, $instructions);
    }

    // ==================== EQUIPMENT ====================

    public function exportEquipment(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'category']);
        $filename = 'equipment_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new EquipmentExport($filters), $filename);
    }

    public function showImportEquipment(): Response
    {
        return Inertia::render('school::Dashboard/V1/Equipment/Import', [
            'duplicateOptions' => $this->duplicateOptions,
        ]);
    }

    public function previewEquipment(Request $request): JsonResponse
    {
        return $this->handlePreview($request, EquipmentImport::class);
    }

    public function importEquipment(Request $request): RedirectResponse
    {
        return $this->handleImport($request, EquipmentImport::class, 'school.equipment.index', 'school.equipment.import');
    }

    public function downloadEquipmentTemplate(): BinaryFileResponse
    {
        $headers = ['Name', 'Category', 'Icon', 'Description', 'Status'];
        $sampleData = ['Projector', 'technology', 'projector', 'HD Projector for presentations', 'active'];
        $instructions = [
            'Name is required',
            'Category: technology, furniture, safety, accessibility, other',
            'Icon: optional icon identifier',
            'Status: active or inactive (defaults to active)',
        ];
        return $this->generateTemplate('equipment', $headers, $sampleData, $instructions);
    }

    // ==================== SHARED METHODS ====================

    protected function handlePreview(Request $request, string $importClass): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $duplicateHandling = $request->input('duplicate_handling', 'skip');

            $import = new $importClass($duplicateHandling, true);
            Excel::import($import, $file);

            $previewData = $import->getPreviewData();
            $results = $import->getResults();

            return response()->json([
                'success' => true,
                'preview' => $previewData,
                'stats' => $results['preview_stats'],
                'total_rows' => count($previewData),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage(),
            ], 422);
        }
    }

    protected function handleImport(Request $request, string $importClass, string $successRoute, string $errorRoute): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $duplicateHandling = $request->input('duplicate_handling', 'skip');

            $import = new $importClass($duplicateHandling, false);
            Excel::import($import, $file);

            $results = $import->getResults();

            $messages = [];
            if ($results['imported'] > 0) $messages[] = "{$results['imported']} imported";
            if ($results['updated'] > 0) $messages[] = "{$results['updated']} updated";
            if ($results['skipped'] > 0) $messages[] = "{$results['skipped']} skipped";
            if ($results['failed'] > 0) $messages[] = "{$results['failed']} failed";

            $message = 'Import completed: ' . implode(', ', $messages);

            if ($results['failed'] > 0) {
                session()->flash('import_failed_rows', $results['failed_rows']);
                return redirect()->route($successRoute)->with('warning', $message);
            }

            return redirect()->route($successRoute)->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route($errorRoute)->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    protected function generateTemplate(string $name, array $headers, array $sampleData, array $instructions): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($name) . ' Import Template');

        // Add headers
        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
        }

        // Style header row
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ]);

        // Auto-size columns
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add sample data
        foreach ($sampleData as $index => $value) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '2', $value);
        }
        $sheet->getStyle('A2:' . $lastColumn . '2')->getFont()->setItalic(true);

        // Add instructions sheet
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');
        $instructionsSheet->setCellValue('A1', 'Import Instructions');
        $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $row = 3;
        foreach ($instructions as $index => $instruction) {
            $instructionsSheet->setCellValue('A' . $row, ($index + 1) . '. ' . $instruction);
            $row++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $tempFile = tempnam(sys_get_temp_dir(), $name . '_template_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $name . '_import_template.xlsx')->deleteFileAfterSend(true);
    }
}
