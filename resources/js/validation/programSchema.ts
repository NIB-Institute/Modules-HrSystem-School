import { z } from 'zod';

export const programSchema = z.object({
    school_id: z.number({
        required_error: 'Please select a school',
        invalid_type_error: 'Please select a school',
    }).min(1, 'Please select a school'),
    department_id: z.number().nullable().optional(),
    name: z.string().min(1, 'Program name is required').max(255, 'Name must be less than 255 characters'),
    code: z.string().max(50, 'Code must be less than 50 characters').nullable().optional(),
    description: z.string().max(2000, 'Description must be less than 2000 characters').nullable().optional(),
    degree_level: z.enum(['certificate', 'diploma', 'associate', 'bachelor', 'master', 'doctorate'], {
        errorMap: () => ({ message: 'Please select a valid degree level' }),
    }),
    duration_years: z.number().min(1, 'Duration must be at least 1 year').max(10, 'Duration cannot exceed 10 years').nullable().optional(),
    credits_required: z.number().min(0, 'Credits cannot be negative').max(500, 'Credits cannot exceed 500').nullable().optional(),
    tuition_fee: z.number().min(0, 'Tuition fee cannot be negative').max(999999.99, 'Tuition fee is too high').nullable().optional(),
    admission_requirements: z.string().max(2000, 'Admission requirements must be less than 2000 characters').nullable().optional(),
    program_coordinator: z.string().max(255, 'Coordinator name must be less than 255 characters').nullable().optional(),
    max_students: z.number().min(0, 'Max students cannot be negative').max(10000, 'Max students cannot exceed 10000').nullable().optional(),
    current_enrollment: z.number().min(0, 'Current enrollment cannot be negative').max(10000, 'Current enrollment cannot exceed 10000').nullable().optional(),
    accreditation_status: z.string().max(255, 'Accreditation status must be less than 255 characters').nullable().optional(),
    status: z.boolean(),
});

export type ProgramSchemaType = z.infer<typeof programSchema>;
