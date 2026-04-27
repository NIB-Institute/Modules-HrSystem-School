<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\BulkDeleteInventoriesAction;
use Modules\School\Actions\Dashboard\V1\CreateInventoryAction;
use Modules\School\Actions\Dashboard\V1\DeleteInventoryAction;
use Modules\School\Actions\Dashboard\V1\GetInventoryCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetInventoryEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetInventoryIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetInventoryShowDataAction;
use Modules\School\Actions\Dashboard\V1\UpdateInventoryAction;
use Modules\School\Http\Requests\Dashboard\V1\BulkDeleteInventoriesRequest;
use Modules\School\Http\Requests\Dashboard\V1\InventoryRequest;
use Modules\School\Http\Resources\Dashboard\V1\InventoryResource;
use Modules\School\Models\Inventory;

class InventoryController extends Controller
{
    public function __construct(
        protected GetInventoryIndexDataAction $getIndexDataAction,
        protected GetInventoryShowDataAction $getShowDataAction,
        protected GetInventoryCreateDataAction $getCreateDataAction,
        protected GetInventoryEditDataAction $getEditDataAction,
        protected CreateInventoryAction $createAction,
        protected UpdateInventoryAction $updateAction,
        protected DeleteInventoryAction $deleteAction,
        protected BulkDeleteInventoriesAction $bulkDeleteAction,
    ) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only([
            'search', 'status', 'condition',
            'equipment_id', 'classroom_id', 'department_id',
        ]);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Inventory/Index', $data);
    }

    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::Dashboard/V1/Inventory/Create', $data)
            ->baseRoute('school.inventories.index');
    }

    public function store(InventoryRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.inventories.index')
            ->with('success', 'Inventory item created successfully.');
    }

    public function show(Inventory $inventory): Response
    {
        $data = $this->getShowDataAction->execute($inventory);

        return Inertia::render('school::Dashboard/V1/Inventory/Show', $data);
    }

    public function edit(Inventory $inventory): Modal
    {
        $data = $this->getEditDataAction->execute($inventory);

        return Inertia::modal('school::Dashboard/V1/Inventory/Edit', $data)
            ->baseRoute('school.inventories.index');
    }

    public function update(InventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $this->updateAction->execute($inventory, $request->validated());

        return redirect()
            ->route('school.inventories.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function confirmDelete(Inventory $inventory): Modal
    {
        $inventory->load(['equipment', 'classroom', 'department', 'assignedTo']);

        return Inertia::modal('school::Dashboard/V1/Inventory/Delete', [
            'inventory' => (new InventoryResource($inventory))->resolve(),
        ])->baseRoute('school.inventories.index');
    }

    public function destroy(Inventory $inventory): RedirectResponse
    {
        $this->deleteAction->execute($inventory);

        return redirect()
            ->route('school.inventories.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function confirmBulkDelete(Request $request): Modal
    {
        $uuids = $request->input('uuids', []);

        $inventories = Inventory::whereIn('uuid', $uuids)->with('equipment')->get();

        return Inertia::modal('school::Dashboard/V1/Inventory/BulkDelete', [
            'inventories' => InventoryResource::collection($inventories)->resolve(),
        ])->baseRoute('school.inventories.index');
    }

    public function bulkDelete(BulkDeleteInventoriesRequest $request): RedirectResponse
    {
        $result = $this->bulkDeleteAction->execute($request->validated('uuids'));

        $message = "{$result['deleted']} inventory item(s) deleted successfully.";
        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} item(s) could not be found.";
        }

        return redirect()
            ->route('school.inventories.index')
            ->with('success', $message);
    }
}
