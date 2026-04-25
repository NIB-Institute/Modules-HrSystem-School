<?php

namespace Modules\School\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\School\Actions\Dashboard\V1\BulkDeleteCoursesAction;
use Modules\School\Actions\Dashboard\V1\CreateCourseAction;
use Modules\School\Actions\Dashboard\V1\DeleteCourseAction;
use Modules\School\Actions\Dashboard\V1\GetCourseCreateDataAction;
use Modules\School\Actions\Dashboard\V1\GetCourseEditDataAction;
use Modules\School\Actions\Dashboard\V1\GetCourseIndexDataAction;
use Modules\School\Actions\Dashboard\V1\GetCourseShowDataAction;
use Modules\School\Actions\Dashboard\V1\ToggleCourseStatusAction;
use Modules\School\Actions\Dashboard\V1\UpdateCourseAction;
use Modules\School\Http\Requests\Dashboard\V1\BulkDeleteCoursesRequest;
use Modules\School\Http\Requests\Dashboard\V1\StoreCourseRequest;
use Modules\School\Http\Requests\Dashboard\V1\UpdateCourseRequest;
use Modules\School\Http\Resources\Dashboard\V1\CourseResource;
use Modules\School\Models\Course;

class CourseController extends Controller
{
    public function __construct(
        protected GetCourseIndexDataAction $getIndexDataAction,
        protected GetCourseShowDataAction $getShowDataAction,
        protected GetCourseCreateDataAction $getCreateDataAction,
        protected GetCourseEditDataAction $getEditDataAction,
        protected CreateCourseAction $createAction,
        protected UpdateCourseAction $updateAction,
        protected DeleteCourseAction $deleteAction,
        protected ToggleCourseStatusAction $toggleStatusAction,
        protected BulkDeleteCoursesAction $bulkDeleteAction,
    ) {}

    /**
     * Display a listing of courses.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'type', 'department_id', 'program_id']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        return Inertia::render('school::Dashboard/V1/Course/Index', $data);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): Modal
    {
        $data = $this->getCreateDataAction->execute();

        return Inertia::modal('school::Dashboard/V1/Course/Create', $data)
            ->baseRoute('school.courses.index');
    }

    /**
     * Store a newly created course.
     */
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('school.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): Response
    {
        $data = $this->getShowDataAction->execute($course);

        return Inertia::render('school::Dashboard/V1/Course/Show', $data);
    }

    /**
     * Show the form for editing the course.
     */
    public function edit(Course $course): Modal
    {
        $data = $this->getEditDataAction->execute($course);

        return Inertia::modal('school::Dashboard/V1/Course/Edit', $data)
            ->baseRoute('school.courses.index');
    }

    /**
     * Update the specified course.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->updateAction->execute($course, $request->validated());

        return redirect()
            ->route('school.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Course $course): Modal
    {
        $course->load(['department', 'program', 'instructor']);

        return Inertia::modal('school::Dashboard/V1/Course/Delete', [
            'course' => (new CourseResource($course))->resolve(),
        ])->baseRoute('school.courses.index');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $this->deleteAction->execute($course);

        return redirect()
            ->route('school.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Toggle course status.
     */
    public function toggleStatus(Course $course): RedirectResponse
    {
        $this->toggleStatusAction->execute($course);

        $status = $course->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Course {$status} successfully.");
    }

    /**
     * Show bulk delete confirmation modal.
     */
    public function confirmBulkDelete(Request $request): Modal
    {
        $uuids = $request->input('uuids', []);

        $courses = Course::whereIn('uuid', $uuids)->get();

        return Inertia::modal('school::Dashboard/V1/Course/BulkDelete', [
            'courses' => CourseResource::collection($courses)->resolve(),
        ])->baseRoute('school.courses.index');
    }

    /**
     * Bulk delete courses.
     */
    public function bulkDelete(BulkDeleteCoursesRequest $request): RedirectResponse
    {
        $result = $this->bulkDeleteAction->execute($request->validated('uuids'));

        $message = "{$result['deleted']} course(s) deleted successfully.";

        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} course(s) could not be found.";
        }

        return redirect()
            ->route('school.courses.index')
            ->with('success', $message);
    }
}
