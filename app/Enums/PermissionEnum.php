<?php

namespace Modules\School\Enums;

/**
 * Single source of truth for every permission the School module owns.
 *
 * Use ::value (e.g. PermissionEnum::SCHOOLS_VIEW_ANY->value) wherever
 * Spatie permission names are required: middleware, route guards,
 * MenuService registrations, FormRequests, Policies. Never bare strings.
 *
 * RolesAndPermissionsSeeder reads ::values() to seed the database.
 */
enum PermissionEnum: string
{
    // ----- schools -----
    case SCHOOLS_VIEW = 'schools.view';
    case SCHOOLS_VIEW_ANY = 'schools.view_any';
    case SCHOOLS_CREATE = 'schools.create';
    case SCHOOLS_UPDATE = 'schools.update';
    case SCHOOLS_DELETE = 'schools.delete';
    case SCHOOLS_RESTORE = 'schools.restore';
    case SCHOOLS_FORCE_DELETE = 'schools.force_delete';
    case SCHOOLS_EXPORT = 'schools.export';
    case SCHOOLS_IMPORT = 'schools.import';
    case SCHOOLS_MANAGE_DEPARTMENTS = 'schools.manage_departments';
    case SCHOOLS_MANAGE_PROGRAMS = 'schools.manage_programs';
    case SCHOOLS_VIEW_STATISTICS = 'schools.view_statistics';
    case SCHOOLS_VIEW_ANALYTICS = 'schools.view_analytics';
    case SCHOOLS_MANAGE_SETTINGS = 'schools.manage_settings';

    // ----- departments -----
    case DEPARTMENTS_VIEW = 'departments.view';
    case DEPARTMENTS_VIEW_ANY = 'departments.view_any';
    case DEPARTMENTS_CREATE = 'departments.create';
    case DEPARTMENTS_UPDATE = 'departments.update';
    case DEPARTMENTS_DELETE = 'departments.delete';
    case DEPARTMENTS_RESTORE = 'departments.restore';
    case DEPARTMENTS_FORCE_DELETE = 'departments.force_delete';
    case DEPARTMENTS_EXPORT = 'departments.export';
    case DEPARTMENTS_IMPORT = 'departments.import';
    case DEPARTMENTS_GENERATE_QR = 'departments.generate_qr';
    case DEPARTMENTS_SCAN_QR = 'departments.scan_qr';
    case DEPARTMENTS_ASSIGN_LOCATION = 'departments.assign_location';
    case DEPARTMENTS_VIEW_ANALYTICS = 'departments.view_analytics';
    case DEPARTMENTS_MANAGE_GEOFENCE = 'departments.manage_geofence';
    case DEPARTMENTS_VIEW_EMPLOYEES = 'departments.view_employees';
    case DEPARTMENTS_ASSIGN_HEAD = 'departments.assign_head';

    // ----- classrooms -----
    case CLASSROOMS_VIEW = 'classrooms.view';
    case CLASSROOMS_VIEW_ANY = 'classrooms.view_any';
    case CLASSROOMS_CREATE = 'classrooms.create';
    case CLASSROOMS_UPDATE = 'classrooms.update';
    case CLASSROOMS_DELETE = 'classrooms.delete';
    case CLASSROOMS_RESTORE = 'classrooms.restore';
    case CLASSROOMS_FORCE_DELETE = 'classrooms.force_delete';
    case CLASSROOMS_EXPORT = 'classrooms.export';
    case CLASSROOMS_IMPORT = 'classrooms.import';
    case CLASSROOMS_ASSIGN_DEPARTMENT = 'classrooms.assign_department';
    case CLASSROOMS_MANAGE_SCHEDULE = 'classrooms.manage_schedule';
    case CLASSROOMS_VIEW_CAPACITY = 'classrooms.view_capacity';

    // ----- courses -----
    case COURSES_VIEW = 'courses.view';
    case COURSES_VIEW_ANY = 'courses.view_any';
    case COURSES_CREATE = 'courses.create';
    case COURSES_UPDATE = 'courses.update';
    case COURSES_DELETE = 'courses.delete';
    case COURSES_RESTORE = 'courses.restore';
    case COURSES_FORCE_DELETE = 'courses.force_delete';
    case COURSES_EXPORT = 'courses.export';
    case COURSES_IMPORT = 'courses.import';
    case COURSES_ASSIGN_PROGRAM = 'courses.assign_program';
    case COURSES_MANAGE_SCHEDULE = 'courses.manage_schedule';

    // ----- programs -----
    case PROGRAMS_VIEW = 'programs.view';
    case PROGRAMS_VIEW_ANY = 'programs.view_any';
    case PROGRAMS_CREATE = 'programs.create';
    case PROGRAMS_UPDATE = 'programs.update';
    case PROGRAMS_DELETE = 'programs.delete';
    case PROGRAMS_RESTORE = 'programs.restore';
    case PROGRAMS_FORCE_DELETE = 'programs.force_delete';
    case PROGRAMS_EXPORT = 'programs.export';
    case PROGRAMS_IMPORT = 'programs.import';
    case PROGRAMS_ASSIGN_COURSES = 'programs.assign_courses';
    case PROGRAMS_VIEW_STATISTICS = 'programs.view_statistics';

    // ----- inventories -----
    case INVENTORIES_VIEW = 'inventories.view';
    case INVENTORIES_VIEW_ANY = 'inventories.view_any';
    case INVENTORIES_CREATE = 'inventories.create';
    case INVENTORIES_UPDATE = 'inventories.update';
    case INVENTORIES_DELETE = 'inventories.delete';
    case INVENTORIES_RESTORE = 'inventories.restore';
    case INVENTORIES_FORCE_DELETE = 'inventories.force_delete';
    case INVENTORIES_EXPORT = 'inventories.export';
    case INVENTORIES_IMPORT = 'inventories.import';
    case INVENTORIES_BULK_DELETE = 'inventories.bulk_delete';
    case INVENTORIES_TOGGLE_STATUS = 'inventories.toggle_status';
    case INVENTORIES_ASSIGN_LOCATION = 'inventories.assign_location';
    case INVENTORIES_ASSIGN_USER = 'inventories.assign_user';
    case INVENTORIES_TRANSFER = 'inventories.transfer';
    case INVENTORIES_VIEW_HISTORY = 'inventories.view_history';

    // ----- equipment -----
    case EQUIPMENT_VIEW = 'equipment.view';
    case EQUIPMENT_VIEW_ANY = 'equipment.view_any';
    case EQUIPMENT_CREATE = 'equipment.create';
    case EQUIPMENT_UPDATE = 'equipment.update';
    case EQUIPMENT_DELETE = 'equipment.delete';
    case EQUIPMENT_RESTORE = 'equipment.restore';
    case EQUIPMENT_FORCE_DELETE = 'equipment.force_delete';
    case EQUIPMENT_EXPORT = 'equipment.export';
    case EQUIPMENT_IMPORT = 'equipment.import';
    case EQUIPMENT_ASSIGN_CLASSROOM = 'equipment.assign_classroom';
    case EQUIPMENT_TRACK_STATUS = 'equipment.track_status';

    /**
     * Plain string values for every case. Used by the seeder.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
