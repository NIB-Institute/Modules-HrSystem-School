<?php

namespace Modules\School\Http\Middleware;

use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Modules\School\Enums\PermissionEnum;

/**
 * Registers the School module's primary sidebar items on every
 * dashboard request, before HandleInertiaRequests serializes the
 * menu tree for Inertia. Permissions reference PermissionEnum so
 * the strings stay in sync with RolesAndPermissionsSeeder.
 */
class DashboardMiddlewareHandle
{
    public function handle(Request $request, Closure $next)
    {
        MenuService::addMenuItem(
            menu: 'primary',
            id: 'school',
            title: __('Schools'),
            url: route('school.schools.index'),
            icon: 'GraduationCap',
            order: 40,
            permissions: PermissionEnum::VIEW_SCHOOLS->value,
            route: 'school.*',
        );

        MenuService::addSubmenuItem(
            'primary',
            'school',
            __('All Schools'),
            route('school.schools.index'),
            10,
            PermissionEnum::VIEW_SCHOOLS->value,
            'school.schools.*',
            'Building2',
        );

        MenuService::addSubmenuItem(
            'primary',
            'school',
            __('Departments'),
            route('school.departments.index'),
            20,
            PermissionEnum::VIEW_DEPARTMENTS->value,
            'school.departments.*',
            'Layers',
        );

        MenuService::addSubmenuItem(
            'primary',
            'school',
            __('Programs'),
            route('school.programs.index'),
            30,
            PermissionEnum::VIEW_PROGRAMS->value,
            'school.programs.*',
            'BookOpen',
        );

        MenuService::addSubmenuItem(
            'primary',
            'school',
            __('Courses'),
            route('school.courses.index'),
            40,
            PermissionEnum::VIEW_COURSES->value,
            'school.courses.*',
            'FileText',
        );

        MenuService::addSubmenuItem(
            'primary',
            'school',
            __('Classrooms'),
            route('school.classrooms.index'),
            50,
            PermissionEnum::VIEW_CLASSROOMS->value,
            'school.classrooms.*',
            'DoorOpen',
        );

        MenuService::addSubmenuItem(
            'primary',
            'school',
            __('Equipment'),
            route('school.equipment.index'),
            60,
            PermissionEnum::VIEW_EQUIPMENT->value,
            'school.equipment.*',
            'Wrench',
        );

        return $next($request);
    }
}
