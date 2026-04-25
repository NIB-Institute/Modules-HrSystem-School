import { z } from 'zod';

export const departmentSchema = z.object({
    school_id: z.union([z.string(), z.number()]).refine(val => val !== '' && val !== null && val !== undefined, {
        message: 'Please select a school',
    }),
    name: z.string().min(1, 'Department name is required').max(255, 'Name must be less than 255 characters'),
    code: z.string().max(50, 'Code must be less than 50 characters').nullable().optional(),
    description: z.string().max(2000, 'Description must be less than 2000 characters').nullable().optional(),
    head_of_department: z.string().max(255, 'Head of department must be less than 255 characters').nullable().optional(),
    email: z.string().email('Please enter a valid email').max(255).nullable().optional().or(z.literal('')),
    phone: z.string().max(20, 'Phone must be less than 20 characters').nullable().optional(),
    office_location: z.string().max(255, 'Office location must be less than 255 characters').nullable().optional(),
    established_year: z.number().min(1800, 'Year must be at least 1800').max(new Date().getFullYear(), 'Year cannot be in the future').nullable().optional(),
    total_students: z.number().min(0, 'Total students cannot be negative').nullable().optional(),
    total_staff: z.number().min(0, 'Total staff cannot be negative').nullable().optional(),
    status: z.boolean(),

    // Location linking (preferred geofence source)
    location_id: z.number().nullable().optional(),

    // Geofence fields (fallback if no location linked)
    latitude: z.number().min(-90, 'Latitude must be between -90 and 90').max(90, 'Latitude must be between -90 and 90').nullable().optional(),
    longitude: z.number().min(-180, 'Longitude must be between -180 and 180').max(180, 'Longitude must be between -180 and 180').nullable().optional(),
    geofence_radius: z.number().min(10, 'Radius must be at least 10 meters').max(5000, 'Radius cannot exceed 5000 meters').optional().default(100),
    enforce_geofence: z.boolean().optional().default(false),
    timezone: z.string().max(50, 'Timezone must be less than 50 characters').nullable().optional(),
});

export type DepartmentSchemaType = z.infer<typeof departmentSchema>;
