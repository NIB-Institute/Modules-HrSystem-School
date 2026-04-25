import { z } from 'zod';

export const courseSchema = z.object({
    department_id: z.number().nullable().optional(),
    program_id: z.number().nullable().optional(),
    instructor_id: z.number().nullable().optional(),
    name: z.string().min(1, 'Course name is required').max(255, 'Name must be less than 255 characters'),
    code: z.string().max(50, 'Code must be less than 50 characters').nullable().optional(),
    description: z.string().max(2000, 'Description must be less than 2000 characters').nullable().optional(),
    credits: z.number().min(1, 'Credits must be at least 1').max(20, 'Credits cannot exceed 20'),
    type: z.enum(['required', 'elective', 'core'], {
        errorMap: () => ({ message: 'Please select a valid course type' }),
    }),
    semester: z.number().min(1, 'Semester must be at least 1').max(8, 'Semester cannot exceed 8').nullable().optional(),
    year: z.number().min(1, 'Year must be at least 1').max(10, 'Year cannot exceed 10').nullable().optional(),
    max_students: z.number().min(1, 'Max students must be at least 1').max(1000, 'Max students cannot exceed 1000').nullable().optional(),
    current_enrollment: z.number().min(0, 'Current enrollment cannot be negative').nullable().optional(),
    schedule: z.string().max(255, 'Schedule must be less than 255 characters').nullable().optional(),
    room: z.string().max(100, 'Room must be less than 100 characters').nullable().optional(),
    prerequisites: z.array(z.string()).nullable().optional(),
    syllabus: z.string().max(5000, 'Syllabus must be less than 5000 characters').nullable().optional(),
    status: z.boolean(),
});

export type CourseSchemaType = z.infer<typeof courseSchema>;
