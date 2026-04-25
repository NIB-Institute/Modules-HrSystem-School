import { z } from 'zod';

export const classroomSchema = z.object({
    department_id: z.union([z.string(), z.number()]).refine(val => val !== '' && val !== null && val !== undefined, {
        message: 'Please select a department',
    }),
    name: z.string().min(1, 'Classroom name is required').max(255, 'Name must be less than 255 characters'),
    code: z.string().max(50, 'Code must be less than 50 characters').nullable().optional(),
    building: z.string().max(100, 'Building name must be less than 100 characters').nullable().optional(),
    floor: z.number().min(0, 'Floor must be at least 0').max(100, 'Floor cannot exceed 100').nullable().optional(),
    capacity: z.number().min(1, 'Capacity must be at least 1').max(10000, 'Capacity cannot exceed 10000'),
    type: z.enum(['lecture_hall', 'classroom', 'lab', 'seminar', 'auditorium', 'workshop'], {
        errorMap: () => ({ message: 'Please select a valid classroom type' }),
    }),
    equipment: z.array(z.string()).optional().default([]),
    equipment_ids: z.array(z.number()).optional().default([]),
    description: z.string().max(2000, 'Description must be less than 2000 characters').nullable().optional(),
    has_projector: z.boolean().default(false),
    has_whiteboard: z.boolean().default(true),
    has_ac: z.boolean().default(false),
    is_available: z.boolean().default(true),
    status: z.boolean().default(true),
});

export type ClassroomSchemaType = z.infer<typeof classroomSchema>;
