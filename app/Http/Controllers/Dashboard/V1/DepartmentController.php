<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\BulkDeleteDepartmentsAction;
use Modules\School\Actions\Dashboard\V1\CreateDepartmentAction;
use Modules\School\Actions\Dashboard\V1\DeleteDepartmentAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetDepartmentShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleDepartmentStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateDepartmentAction;
use Modules\School\Http\Requests\Dashboard\V1\BulkDeleteDepartmentsRequest;
use Modules\School\Http\Requests\Dashboard\V1\StoreDepartmentRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateDepartmentRequest;
use Modules\School\Http\Resources\Dashboard\V1\DepartmentResource;
use Modules\School\Models\Department;

class DepartmentController extends Controller
{
    public function __construct(
        protected GetDepartmentIndexDataAction $getIndexDataAction,
        protected GetDepartmentShowDataAction $getShowDataAction,
        protected GetDepartmentCreateDataAction $getCreateDataAction,
        protected GetDepartmentEditDataAction $getEditDataAction,
        protected CreateDepartmentAction $createAction,
        protected UpdateDepartmentAction $updateAction,
        protected DeleteDepartmentAction $deleteAction,
        protected ToggleDepartmentStatusAction $toggleStatusAction,
        protected BulkDeleteDepartmentsAction $bulkDeleteAction,
    ) {}

    /**
     * Display a listing of departments.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'school_id']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Department/Index', $data);
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): Response
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::render('school::Dashboard/V1/Department/Create', $data);
    }

    /**
     * Store a newly created department.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): Response
    {
        $data = $this->getShowDataAction->execute($department);

        return Inertia::render('school::Dashboard/V1/Department/Show', $data);
    }

    /**
     * Show the form for editing the department.
     */
    public function edit(Department $department): Response
    {
        $data = $this->getEditDataAction->execute($department);

        return Inertia::render('school::Dashboard/V1/Department/Edit', $data);
    }

    /**
     * Update the specified department.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $this->updateAction->execute($department, $request->validated());

        return redirect()
            ->route('school.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Department $department): Modal
    {
        $department->loadCount(['programs', 'courses', 'employees']);

        return Inertia::modal('school::Dashboard/V1/Department/Delete', [
            'department' => (new DepartmentResource($department))->resolve(),
        ])->baseRoute('school.departments.index');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        $this->deleteAction->execute($department);

        return redirect()
            ->route('school.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Show bulk delete confirmation modal.
     */
    public function confirmBulkDelete(Request $request): Modal
    {
        $uuids = $request->input('uuids', []);

        $departments = Department::whereIn('uuid', $uuids)
            ->with('school:id,name')
            ->get();

        return Inertia::modal('school::Dashboard/V1/Department/BulkDelete', [
            'departments' => DepartmentResource::collection($departments)->resolve(),
        ])->baseRoute('school.departments.index');
    }

    /**
     * Bulk delete departments.
     */
    public function bulkDelete(BulkDeleteDepartmentsRequest $request): RedirectResponse
    {
        $result = $this->bulkDeleteAction->execute($request->validated('uuids'));

        $message = "{$result['deleted']} department(s) deleted successfully.";

        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} department(s) could not be found.";
        }

        return redirect()
            ->route('school.departments.index')
            ->with('success', $message);
    }

    /**
     * Toggle department status.
     */
    public function toggleStatus(Department $department): RedirectResponse
    {
        $this->toggleStatusAction->execute($department);

        $status = $department->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Department {$status} successfully.");
    }

    /**
     * Get departments for API/dropdown.
     */
    public function getDepartments(Request $request): JsonResponse
    {
        $schoolId = $request->input('school_id');

        $query = Department::where('status', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $departments = $query->orderBy('name')->get(['id', 'name', 'code', 'school_id']);

        return response()->json([
            'departments' => $departments,
        ]);
    }

    /**
     * Show QR code page for department attendance.
     */
    public function qrCode(Department $department): Response
    {
        $department->load(['school:id,name', 'location']);

        // Build QR data including geofence info from linked location
        $location = $department->location;
        $qrDataArray = [
            'type' => 'department',
            'department_id' => $department->id,
            'department_code' => $department->code,
            'department_name' => $department->name,
            'school_name' => $department->school?->name,
            'scan_type' => 'attendance',
        ];

        // Include geofence data if location is linked
        if ($location) {
            $qrDataArray['geofence'] = [
                'location_id' => $location->id,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'radius' => $location->geofence_radius,
                'type' => $location->geofence_type,
                'enforce' => $location->enforce_geofence,
            ];
        }

        $qrData = json_encode($qrDataArray);

        return Inertia::render('school::Dashboard/V1/Department/QrCode', [
            'department' => [
                'id' => $department->id,
                'uuid' => $department->uuid,
                'name' => $department->name,
                'code' => $department->code,
                'school_name' => $department->school?->name,
            ],
            'location' => $location ? [
                'id' => $location->id,
                'uuid' => $location->uuid,
                'name' => $location->name,
                'code' => $location->code,
                'type' => $location->type,
                'geofence_type' => $location->geofence_type,
                'geofence_radius' => $location->geofence_radius,
                'enforce_geofence' => $location->enforce_geofence,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'is_active' => $location->is_active,
                'city' => $location->city,
            ] : null,
            'qrData' => $qrData,
        ]);
    }
}
