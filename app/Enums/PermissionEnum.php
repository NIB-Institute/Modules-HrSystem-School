<?php

namespace Modules\School\Enums;

/**
 * Single source of truth for School module permission strings.
 *
 * Use ::value (e.g. PermissionEnum::VIEW_SCHOOLS->value) wherever
 * Spatie permission names are required - middleware, route guards,
 * MenuService registrations, etc. Values must match the permissions
 * seeded by RolesAndPermissionsSeeder.
 */
enum PermissionEnum: string
{
    case VIEW_SCHOOLS = 'schools.view_any';
    case VIEW_DEPARTMENTS = 'departments.view_any';
    case VIEW_PROGRAMS = 'programs.view_any';
    case VIEW_COURSES = 'courses.view_any';
    case VIEW_CLASSROOMS = 'classrooms.view_any';
    case VIEW_EQUIPMENT = 'equipment.view_any';
}
