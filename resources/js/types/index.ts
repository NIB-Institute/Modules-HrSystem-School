// School Module Types

export type SchoolType = 'university' | 'institute' | 'college' | 'school';

export interface School {
    id: number;
    uuid: string;
    name: string;
    code: string | null;
    type: SchoolType;
    type_label: string;
    description: string | null;
    address: string | null;
    city: string | null;
    country: string | null;
    phone: string | null;
    email: string | null;
    website: string | null;
    logo: string | null;
    established_year: number | null;
    accreditation: string | null;
    school_lavel: string | null;
    currency: number | null;
    education_system: string | null;
    tuition_fee_base: number | null;
    total_students: number;
    total_staff: number;
    status: boolean;
    departments_count: number | null;
    programs_count: number | null;
    employees_count: number | null;
    courses_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface SchoolStats {
    total: number;
    active: number;
    inactive: number;
    universities: number;
    institutes: number;
    colleges: number;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

export interface SchoolFilters {
    status?: string;
    search?: string;
    type?: string;
}

export interface SchoolFormData {
    name: string;
    code: string;
    type: SchoolType;
    description: string;
    address: string;
    city: string;
    country: string;
    phone: string;
    email: string;
    website: string;
    logo: string;
    established_year: number | null;
    accreditation: string;
    school_lavel: string;
    currency: number | null;
    education_system: string;
    tuition_fee_base: number | null;
    total_students: number;
    total_staff: number;
    status: boolean;
}

export interface SchoolTypeOption {
    value: SchoolType;
    label: string;
}

export interface SchoolIndexProps {
    schools: PaginatedResponse<School>;
    filters: SchoolFilters;
    stats: SchoolStats;
    types: Record<SchoolType, string>;
}

export interface SchoolShowProps {
    school: School;
    departments: any[];
    programs: any[];
    stats: {
        departments_count: number;
        programs_count: number;
        employees_count: number;
        courses_count: number;
    };
}

export interface SchoolCreateProps {
    types: Record<SchoolType, string>;
}

export interface SchoolEditProps {
    school: School;
    types: Record<SchoolType, string>;
}

export interface SchoolDeleteProps {
    school: School;
}

// Department Types

export interface SchoolOption {
    value: string;
    label: string;
}

export interface DepartmentLocation {
    id: number;
    uuid: string;
    name: string;
    code: string | null;
    type: string;
    latitude: number;
    longitude: number;
    geofence_radius: number;
    geofence_type: string;
    polygon_coordinates: [number, number][] | null;
    enforce_geofence: boolean;
    timezone: string;
    is_active: boolean;
    google_maps_url: string;
}

export interface Department {
    id: number;
    uuid: string;
    school_id: number;
    name: string;
    code: string | null;
    description: string | null;
    head_of_department: string | null;
    email: string | null;
    phone: string | null;
    office_location: string | null;
    established_year: number | null;
    staff_count: number;
    total_students: number | null;
    total_staff: number | null;
    status: boolean;
    // Linked Location (preferred geofence source)
    location_id: number | null;
    location?: DepartmentLocation | null;
    // Geofence fields (derived from location or fallback)
    latitude: number | null;
    longitude: number | null;
    geofence_radius: number;
    enforce_geofence: boolean;
    timezone: string | null;
    has_geofence: boolean;
    google_maps_url: string | null;
    // Relations
    school_name: string | null;
    school?: {
        id: number;
        uuid: string;
        name: string;
        code: string | null;
    };
    programs_count: number | null;
    courses_count: number | null;
    employees_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface DepartmentStats {
    total: number;
    active: number;
    inactive: number;
}

export interface DepartmentFilters {
    status?: string;
    search?: string;
    school_id?: string;
}

export interface DepartmentFormData {
    school_id: string | number;
    name: string;
    code: string;
    description: string;
    head_of_department: string;
    email: string;
    phone: string;
    office_location: string;
    established_year: number | null;
    total_students: number | null;
    total_staff: number | null;
    status: boolean;
    // Location linking (preferred geofence source)
    location_id: number | null;
    // Geofence fields (fallback if no location linked)
    latitude: number | null;
    longitude: number | null;
    geofence_radius: number;
    enforce_geofence: boolean;
    timezone: string | null;
}

export interface DepartmentIndexProps {
    departments: PaginatedResponse<Department>;
    filters: DepartmentFilters;
    stats: DepartmentStats;
    schools: SchoolOption[];
}

export interface DepartmentShowProps {
    department: Department;
    programs: any[];
    stats: {
        programs_count: number;
        courses_count: number;
        employees_count: number;
    };
}

export interface LocationOption {
    value: string;
    label: string;
    type: string;
    city: string | null;
    geofence_type: string;
    geofence_radius: number;
}

export interface DepartmentCreateProps {
    schools: SchoolOption[];
    availableLocations: LocationOption[];
}

export interface DepartmentEditProps {
    department: Department;
    schools: SchoolOption[];
    availableLocations: LocationOption[];
}

export interface DepartmentDeleteProps {
    department: Department;
}

// Course Types

export type CourseType = 'required' | 'elective' | 'core';

export interface Course {
    id: number;
    uuid: string;
    department_id: number | null;
    program_id: number | null;
    instructor_id: number | null;
    name: string;
    code: string | null;
    description: string | null;
    credits: number;
    type: CourseType;
    type_label: string;
    semester: number | null;
    year: number | null;
    max_students: number;
    current_enrollment: number;
    available_seats: number | null;
    schedule: string | null;
    room: string | null;
    prerequisites: string[] | null;
    syllabus: string | null;
    status: boolean;
    department_name: string | null;
    program_name: string | null;
    instructor_name: string | null;
    has_available_seats: boolean;
    created_at: string;
    updated_at: string;
}

export interface CourseStats {
    total: number;
    active: number;
    inactive: number;
    required: number;
    elective: number;
    core: number;
}

export interface CourseFilters {
    status?: string;
    search?: string;
    type?: string;
    department_id?: string;
    program_id?: string;
}

export interface CourseFormData {
    department_id: number | null;
    program_id: number | null;
    instructor_id: number | null;
    name: string;
    code: string;
    description: string;
    credits: number;
    type: CourseType;
    semester: number | null;
    year: number | null;
    max_students: number;
    current_enrollment: number;
    schedule: string;
    room: string;
    prerequisites: string[];
    syllabus: string;
    status: boolean;
}

export interface DepartmentOption {
    id: number;
    name: string;
}

export interface ProgramOption {
    id: number;
    name: string;
}

export interface CourseIndexProps {
    courses: PaginatedResponse<Course>;
    filters: CourseFilters;
    stats: CourseStats;
    types: Record<CourseType, string>;
    departments: DepartmentOption[];
    programs: ProgramOption[];
}

export interface CourseShowProps {
    course: Course;
    stats: {
        credits: number;
        max_students: number;
        current_enrollment: number;
        available_seats: number;
    };
}

export interface CourseCreateProps {
    types: Record<CourseType, string>;
    departments: DepartmentOption[];
    programs: ProgramOption[];
}

export interface CourseEditProps {
    course: Course;
    types: Record<CourseType, string>;
    departments: DepartmentOption[];
    programs: ProgramOption[];
}

export interface CourseDeleteProps {
    course: Course;
}

// Program Types

export type DegreeLevel = 'certificate' | 'diploma' | 'associate' | 'bachelor' | 'master' | 'doctorate';

export interface Program {
    id: number;
    uuid: string;
    school_id: number | null;
    department_id: number | null;
    name: string;
    code: string | null;
    description: string | null;
    degree_level: DegreeLevel;
    degree_level_label: string;
    duration_years: number | null;
    credits_required: number | null;
    tuition_fee: string | null;
    admission_requirements: string | null;
    program_coordinator: string | null;
    max_students: number | null;
    current_enrollment: number | null;
    accreditation_status: string | null;
    status: boolean;
    school_name: string | null;
    department_name: string | null;
    courses_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface ProgramStats {
    total: number;
    active: number;
    inactive: number;
    bachelor: number;
    master: number;
    doctorate: number;
}

export interface ProgramFilters {
    status?: string;
    search?: string;
    degree_level?: string;
    school_id?: number | string;
    department_id?: number | string;
}

export interface ProgramFormData {
    school_id: number | null;
    department_id: number | null;
    name: string;
    code: string;
    description: string;
    degree_level: DegreeLevel;
    duration_years: number | null;
    credits_required: number | null;
    tuition_fee: number | null;
    admission_requirements: string;
    program_coordinator: string;
    max_students: number | null;
    current_enrollment: number | null;
    accreditation_status: string;
    status: boolean;
}

export interface ProgramSchoolOption {
    id: number;
    name: string;
}

export interface ProgramDepartmentOption {
    id: number;
    name: string;
    school_id: number;
}

export interface ProgramIndexProps {
    programs: PaginatedResponse<Program>;
    filters: ProgramFilters;
    stats: ProgramStats;
    degreeLevels: Record<DegreeLevel, string>;
    schools: ProgramSchoolOption[];
    departments: ProgramDepartmentOption[];
}

export interface ProgramShowProps {
    program: Program;
    courses: any[];
    stats: {
        courses_count: number;
        current_enrollment: number;
        max_students: number;
        credits_required: number;
    };
}

export interface ProgramCreateProps {
    degreeLevels: Record<DegreeLevel, string>;
    schools: ProgramSchoolOption[];
    departments: ProgramDepartmentOption[];
}

export interface ProgramEditProps {
    program: Program;
    degreeLevels: Record<DegreeLevel, string>;
    schools: ProgramSchoolOption[];
    departments: ProgramDepartmentOption[];
}

export interface ProgramDeleteProps {
    program: Program;
}

// Classroom Types

export type ClassroomType = 'lecture_hall' | 'classroom' | 'lab' | 'seminar' | 'auditorium' | 'workshop';

export interface ClassroomEquipmentOption {
    value: number;
    label: string;
    category: string;
}

export interface ClassroomEquipmentItem {
    id: number;
    name: string;
    quantity: number;
    value: string | null;
    notes: string | null;
}

export interface Classroom {
    id: number;
    uuid: string;
    department_id: number;
    name: string;
    code: string | null;
    building: string | null;
    floor: number | null;
    capacity: number;
    type: ClassroomType;
    type_label: string;
    equipment: string[];
    equipment_ids: number[];
    description: string | null;
    has_projector: boolean;
    has_whiteboard: boolean;
    has_ac: boolean;
    is_available: boolean;
    status: boolean;
    department_name: string | null;
    school_name: string | null;
    full_location: string | null;
    courses_count: number | null;
    equipment_items_count: number | null;
    equipment_items: ClassroomEquipmentItem[] | null;
    department?: {
        id: number;
        name: string;
        school_id: number;
        school?: {
            id: number;
            name: string;
        };
    };
    created_at: string;
    updated_at: string;
}

export interface ClassroomStats {
    total: number;
    active: number;
    available: number;
    by_type?: {
        lecture_hall: number;
        classroom: number;
        lab: number;
        seminar: number;
        auditorium: number;
        workshop: number;
    };
}

export interface ClassroomFilters {
    status?: string;
    search?: string;
    type?: string;
    department_id?: string;
}

export interface ClassroomFormData {
    department_id: number | string | null;
    name: string;
    code: string;
    building: string;
    floor: number | null;
    capacity: number;
    type: ClassroomType;
    equipment: string[];
    equipment_ids: number[];
    description: string;
    has_projector: boolean;
    has_whiteboard: boolean;
    has_ac: boolean;
    is_available: boolean;
    status: boolean;
}

export interface ClassroomIndexProps {
    classrooms: PaginatedResponse<Classroom>;
    filters: ClassroomFilters;
    stats: ClassroomStats;
    types: Record<ClassroomType, string>;
    departments: DepartmentOption[];
}

export interface ClassroomShowProps {
    classroom: Classroom;
    stats: {
        capacity: number;
        courses_count: number;
        equipment_count: number;
    };
}

export interface ClassroomCreateProps {
    types: Record<ClassroomType, string>;
    departments: DepartmentOption[];
    equipmentOptions: ClassroomEquipmentOption[];
}

export interface ClassroomEditProps {
    classroom: Classroom;
    types: Record<ClassroomType, string>;
    departments: DepartmentOption[];
    equipmentOptions: ClassroomEquipmentOption[];
}

export interface ClassroomDeleteProps {
    classroom: Classroom;
}

// Equipment Types

export type EquipmentCategory = 'technology' | 'furniture' | 'safety' | 'accessibility' | 'other';

export interface Equipment {
    id: number;
    uuid: string;
    name: string;
    slug: string;
    icon: string | null;
    description: string | null;
    category: EquipmentCategory;
    category_label: string;
    qty: number;
    price_total: number | null;
    status: boolean;
    classrooms_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface EquipmentStats {
    total: number;
    active: number;
    by_category: {
        technology: number;
        furniture: number;
        safety: number;
        accessibility: number;
        other: number;
    };
}

export interface EquipmentFilters {
    status?: string;
    search?: string;
    category?: string;
}

export interface EquipmentFormData {
    name: string;
    slug: string;
    icon: string;
    description: string;
    category: EquipmentCategory;
    qty: number;
    price_total: number | null;
    status: boolean;
}

export interface EquipmentIndexProps {
    equipment: PaginatedResponse<Equipment>;
    filters: EquipmentFilters;
    stats: EquipmentStats;
    categories: Record<EquipmentCategory, string>;
}

export interface EquipmentShowProps {
    equipment: Equipment;
}

export interface EquipmentCreateProps {
    categories: Record<EquipmentCategory, string>;
}

export interface EquipmentEditProps {
    equipment: Equipment;
    categories: Record<EquipmentCategory, string>;
}

export interface EquipmentDeleteProps {
    equipment: Equipment;
}
