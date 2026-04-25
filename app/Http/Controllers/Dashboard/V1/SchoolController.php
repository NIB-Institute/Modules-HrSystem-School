<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\CreateSchoolAction;
use Modules\School\Actions\Dashboard\V1\DeleteSchoolAction;
use Modules\School\Actions\Dashboard\V1\GetSchoolCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetSchoolEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetSchoolIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetSchoolShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleSchoolStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateSchoolAction;
use Modules\School\Http\Requests\Dashboard\V1\StoreSchoolRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateSchoolRequest;
use Modules\School\Http\Resources\Dashboard\V1\SchoolResource;
use Modules\School\Models\School;
use Modules\School\Services\SchoolService;

class SchoolController extends Controller
{
    public function __construct(
        protected SchoolService $schoolService,
        protected GetSchoolIndexDataAction $getIndexDataAction,
        protected GetSchoolShowDataAction $getShowDataAction,
        protected GetSchoolCreateDataAction $getCreateDataAction,
        protected GetSchoolEditDataAction $getEditDataAction,
        protected CreateSchoolAction $createAction,
        protected UpdateSchoolAction $updateAction,
        protected DeleteSchoolAction $deleteAction,
        protected ToggleSchoolStatusAction $toggleStatusAction,
    ) {}

    /**
     * Display a listing of schools.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'type']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/School/Index', $data);
    }

    /**
     * Show the form for creating a new school.
     */
    public function create(): Response
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::render('school::Dashboard/V1/School/Create', $data);
    }

    /**
     * Store a newly created school.
     */
    public function store(StoreSchoolRequest $request): RedirectResponse
    {
        $school = $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.schools.show', $school)
            ->with('success', 'School created successfully.');
    }

    /**
     * Display the specified school.
     */
    public function show(School $school): Response
    {
        $data = $this->getShowDataAction->execute($school);

        return Inertia::render('school::Dashboard/V1/School/Show', $data);
    }

    /**
     * Show the form for editing the school.
     */
    public function edit(School $school): Response
    {
        $data = $this->getEditDataAction->execute($school);

        return Inertia::render('school::Dashboard/V1/School/Edit', $data);
    }

    /**
     * Update the specified school.
     */
    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        $this->updateAction->execute($school, $request->validated());

        return redirect()
            ->route('school.schools.show', $school)
            ->with('success', 'School updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(School $school): Modal
    {
        $school->loadCount(['departments', 'programs', 'employees']);

        return Inertia::modal('school::Dashboard/V1/School/Delete', [
            'school' => (new SchoolResource($school))->resolve(),
        ])->baseRoute('school.schools.index');
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school): RedirectResponse
    {
        $this->deleteAction->execute($school);

        return redirect()
            ->route('school.schools.index')
            ->with('success', 'School deleted successfully.');
    }

    /**
     * Toggle school status.
     */
    public function toggleStatus(School $school): RedirectResponse
    {
        $this->toggleStatusAction->execute($school);

        $status = $school->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "School {$status} successfully.");
    }
}
