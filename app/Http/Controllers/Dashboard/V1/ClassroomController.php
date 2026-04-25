<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\BulkDeleteClassroomsAction;
use Modules\School\Actions\Dashboard\V1\CreateClassroomAction;
use Modules\School\Actions\Dashboard\V1\DeleteClassroomAction;
use Modules\School\Actions\Dashboard\V1\GetClassroomCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetClassroomEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetClassroomIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetClassroomShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleClassroomStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateClassroomAction;
use Modules\School\Http\Requests\Dashboard\V1\BulkDeleteClassroomsRequest;
use Modules\School\Http\Requests\Dashboard\V1\StoreClassroomRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateClassroomRequest;
use Modules\School\Http\Resources\Dashboard\V1\ClassroomResource;
use Modules\School\Models\Classroom;

class ClassroomController extends Controller
{
    public function __construct(
        protected GetClassroomIndexDataAction $getIndexDataAction,
        protected GetClassroomShowDataAction $getShowDataAction,
        protected GetClassroomCreateDataAction $getCreateDataAction,
        protected GetClassroomEditDataAction $getEditDataAction,
        protected CreateClassroomAction $createAction,
        protected UpdateClassroomAction $updateAction,
        protected DeleteClassroomAction $deleteAction,
        protected ToggleClassroomStatusAction $toggleStatusAction,
        protected BulkDeleteClassroomsAction $bulkDeleteAction,
    ) {}

    /**
     * Display a listing of classrooms.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'type', 'school_id']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Classroom/Index', $data);
    }

    /**
     * Show the form for creating a new classroom.
     */
    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::Dashboard/V1/Classroom/Create', $data)
            ->baseRoute('school.classrooms.index');
    }

    /**
     * Store a newly created classroom.
     */
    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.classrooms.index')
            ->with('success', 'Classroom created successfully.');
    }

    /**
     * Display the specified classroom.
     */
    public function show(Classroom $classroom): Response
    {
        $data = $this->getShowDataAction->execute($classroom);

        return Inertia::render('school::Dashboard/V1/Classroom/Show', $data);
    }

    /**
     * Show the form for editing the classroom.
     */
    public function edit(Classroom $classroom): Modal
    {
        $data = $this->getEditDataAction->execute($classroom);

        return Inertia::modal('school::Dashboard/V1/Classroom/Edit', $data)
            ->baseRoute('school.classrooms.index');
    }

    /**
     * Update the specified classroom.
     */
    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $this->updateAction->execute($classroom, $request->validated());

        return redirect()
            ->route('school.classrooms.index')
            ->with('success', 'Classroom updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Classroom $classroom): Modal
    {
        $classroom->loadCount(['courses']);

        return Inertia::modal('school::Dashboard/V1/Classroom/Delete', [
            'classroom' => (new ClassroomResource($classroom))->resolve(),
        ])->baseRoute('school.classrooms.index');
    }

    /**
     * Remove the specified classroom.
     */
    public function destroy(Classroom $classroom): RedirectResponse
    {
        $this->deleteAction->execute($classroom);

        return redirect()
            ->route('school.classrooms.index')
            ->with('success', 'Classroom deleted successfully.');
    }

    /**
     * Toggle classroom status.
     */
    public function toggleStatus(Classroom $classroom): RedirectResponse
    {
        $this->toggleStatusAction->execute($classroom);

        $status = $classroom->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Classroom {$status} successfully.");
    }

    /**
     * Show QR code page for classroom attendance.
     */
    public function qrCode(Classroom $classroom): Response
    {
        $classroom->load(['department.school']);

        $qrData = json_encode([
            'type' => 'classroom',
            'classroom_id' => $classroom->id,
            'classroom_code' => $classroom->code,
            'classroom_name' => $classroom->name,
            'department_id' => $classroom->department_id,
            'department_name' => $classroom->department?->name,
            'school_name' => $classroom->department?->school?->name,
            'building' => $classroom->building,
            'floor' => $classroom->floor,
            'scan_type' => 'attendance',
        ]);

        return Inertia::render('school::Dashboard/V1/Classroom/QrCode', [
            'classroom' => [
                'id' => $classroom->id,
                'name' => $classroom->name,
                'code' => $classroom->code,
                'building' => $classroom->building,
                'floor' => $classroom->floor,
                'full_location' => $classroom->full_location,
                'department_name' => $classroom->department?->name,
                'school_name' => $classroom->department?->school?->name,
            ],
            'qrData' => $qrData,
        ]);
    }

    /**
     * Show bulk delete confirmation modal.
     */
    public function confirmBulkDelete(Request $request): Modal
    {
        $uuids = $request->input('uuids', []);

        $classrooms = Classroom::whereIn('uuid', $uuids)->get();

        return Inertia::modal('school::Dashboard/V1/Classroom/BulkDelete', [
            'classrooms' => ClassroomResource::collection($classrooms)->resolve(),
        ])->baseRoute('school.classrooms.index');
    }

    /**
     * Bulk delete classrooms.
     */
    public function bulkDelete(BulkDeleteClassroomsRequest $request): RedirectResponse
    {
        $result = $this->bulkDeleteAction->execute($request->validated('uuids'));

        $message = "{$result['deleted']} classroom(s) deleted successfully.";

        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} classroom(s) could not be found.";
        }

        return redirect()
            ->route('school.classrooms.index')
            ->with('success', $message);
    }
}
