<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\BulkDeleteProgramsAction;
use Modules\School\Actions\Dashboard\V1\CreateProgramAction;
use Modules\School\Actions\Dashboard\V1\DeleteProgramAction;
use Modules\School\Actions\Dashboard\V1\GetProgramCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetProgramEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetProgramIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetProgramShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleProgramStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateProgramAction;
use Modules\School\Http\Requests\Dashboard\V1\BulkDeleteProgramsRequest;
use Modules\School\Http\Requests\Dashboard\V1\StoreProgramRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateProgramRequest;
use Modules\School\Http\Resources\Dashboard\V1\ProgramResource;
use Modules\School\Models\Program;
use Modules\School\Services\ProgramService;

class ProgramController extends Controller
{
    public function __construct(
        protected ProgramService $programService,
        protected GetProgramIndexDataAction $getIndexDataAction,
        protected GetProgramShowDataAction $getShowDataAction,
        protected GetProgramCreateDataAction $getCreateDataAction,
        protected GetProgramEditDataAction $getEditDataAction,
        protected CreateProgramAction $createAction,
        protected UpdateProgramAction $updateAction,
        protected DeleteProgramAction $deleteAction,
        protected ToggleProgramStatusAction $toggleStatusAction,
        protected BulkDeleteProgramsAction $bulkDeleteAction,
    ) {}

    /**
     * Display a listing of programs.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'degree_level', 'school_id', 'department_id']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Program/Index', $data);
    }

    /**
     * Show the form for creating a new program.
     */
    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::Dashboard/V1/Program/Create', $data)
            ->baseRoute('school.programs.index');
    }

    /**
     * Store a newly created program.
     */
    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program): Response
    {
        $data = $this->getShowDataAction->execute($program);

        return Inertia::render('school::Dashboard/V1/Program/Show', $data);
    }

    /**
     * Show the form for editing the program.
     */
    public function edit(Program $program): Modal
    {
        $data = $this->getEditDataAction->execute($program);

        return Inertia::modal('school::Dashboard/V1/Program/Edit', $data)
            ->baseRoute('school.programs.index');
    }

    /**
     * Update the specified program.
     */
    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $this->updateAction->execute($program, $request->validated());

        return redirect()
            ->route('school.programs.index')
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Program $program): Modal
    {
        $program->loadCount('courses');

        return Inertia::modal('school::Dashboard/V1/Program/Delete', [
            'program' => (new ProgramResource($program))->resolve(),
        ])->baseRoute('school.programs.index');
    }

    /**
     * Remove the specified program.
     */
    public function destroy(Program $program): RedirectResponse
    {
        $this->deleteAction->execute($program);

        return redirect()
            ->route('school.programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Toggle program status.
     */
    public function toggleStatus(Program $program): RedirectResponse
    {
        $this->toggleStatusAction->execute($program);

        $status = $program->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Program {$status} successfully.");
    }

    /**
     * Show bulk delete confirmation modal.
     */
    public function confirmBulkDelete(Request $request): Modal
    {
        $uuids = $request->input('uuids', []);

        $programs = Program::whereIn('uuid', $uuids)->get();

        return Inertia::modal('school::Dashboard/V1/Program/BulkDelete', [
            'programs' => ProgramResource::collection($programs)->resolve(),
        ])->baseRoute('school.programs.index');
    }

    /**
     * Bulk delete programs.
     */
    public function bulkDelete(BulkDeleteProgramsRequest $request): RedirectResponse
    {
        $result = $this->bulkDeleteAction->execute($request->validated('uuids'));

        $message = "{$result['deleted']} program(s) deleted successfully.";

        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} program(s) could not be found.";
        }

        return redirect()
            ->route('school.programs.index')
            ->with('success', $message);
    }
}
