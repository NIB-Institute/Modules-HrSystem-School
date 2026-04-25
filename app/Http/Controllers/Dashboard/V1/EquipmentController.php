<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\BulkDeleteEquipmentAction;
use Modules\School\Actions\Dashboard\V1\CreateEquipmentAction;
use Modules\School\Actions\Dashboard\V1\DeleteEquipmentAction;
use Modules\School\Actions\Dashboard\V1\GetEquipmentCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetEquipmentEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetEquipmentIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetEquipmentShowDataAction;
use Modules\School\Actions\Dashboard\V1\UpdateEquipmentAction;
use Modules\School\Http\Requests\Dashboard\V1\BulkDeleteEquipmentRequest;
use Modules\School\Http\Requests\Dashboard\V1\EquipmentRequest;
use Modules\School\Http\Resources\Dashboard\V1\EquipmentResource;
use Modules\School\Models\Equipment;

class EquipmentController extends Controller
{
    public function __construct(
        protected GetEquipmentIndexDataAction $getIndexDataAction,
        protected GetEquipmentShowDataAction $getShowDataAction,
        protected GetEquipmentCreateDataAction $getCreateDataAction,
        protected GetEquipmentEditDataAction $getEditDataAction,
        protected CreateEquipmentAction $createAction,
        protected UpdateEquipmentAction $updateAction,
        protected DeleteEquipmentAction $deleteAction,
        protected BulkDeleteEquipmentAction $bulkDeleteAction,
    ) {}

    /**
     * Display a listing of equipment.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'category']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Equipment/Index', $data);
    }

    /**
     * Show the form for creating new equipment.
     */
    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::Dashboard/V1/Equipment/Create', $data)
            ->baseRoute('school.equipment.index');
    }

    /**
     * Store newly created equipment.
     */
    public function store(EquipmentRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.equipment.index')
            ->with('success', 'Equipment created successfully.');
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment): Response
    {
        $data = $this->getShowDataAction->execute($equipment);

        return Inertia::render('school::Dashboard/V1/Equipment/Show', $data);
    }

    /**
     * Show the form for editing equipment.
     */
    public function edit(Equipment $equipment): Modal
    {
        $data = $this->getEditDataAction->execute($equipment);

        return Inertia::modal('school::Dashboard/V1/Equipment/Edit', $data)
            ->baseRoute('school.equipment.index');
    }

    /**
     * Update the specified equipment.
     */
    public function update(EquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        $this->updateAction->execute($equipment, $request->validated());

        return redirect()
            ->route('school.equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Equipment $equipment): Modal
    {
        $equipment->loadCount('classrooms');

        return Inertia::modal('school::Dashboard/V1/Equipment/Delete', [
            'equipment' => (new EquipmentResource($equipment))->resolve(),
        ])->baseRoute('school.equipment.index');
    }

    /**
     * Remove the specified equipment.
     */
    public function destroy(Equipment $equipment): RedirectResponse
    {
        $this->deleteAction->execute($equipment);

        return redirect()
            ->route('school.equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    /**
     * Show bulk delete confirmation modal.
     */
    public function confirmBulkDelete(Request $request): Modal
    {
        $uuids = $request->input('uuids', []);

        $equipment = Equipment::whereIn('uuid', $uuids)->get();

        return Inertia::modal('school::Dashboard/V1/Equipment/BulkDelete', [
            'equipment' => EquipmentResource::collection($equipment)->resolve(),
        ])->baseRoute('school.equipment.index');
    }

    /**
     * Bulk delete equipment.
     */
    public function bulkDelete(BulkDeleteEquipmentRequest $request): RedirectResponse
    {
        $result = $this->bulkDeleteAction->execute($request->validated('uuids'));

        $message = "{$result['deleted']} equipment item(s) deleted successfully.";

        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} equipment item(s) could not be found.";
        }

        return redirect()
            ->route('school.equipment.index')
            ->with('success', $message);
    }
}
