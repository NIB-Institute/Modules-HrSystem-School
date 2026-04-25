import { z } from 'zod';

export const schoolSchema = z.object({
    name: z.string().min(1, 'School name is required').max(255, 'Name must be less than 255 characters'),
    code: z.string().max(50, 'Code must be less than 50 characters').nullable().optional(),
    type: z.enum(['university', 'institute', 'college', 'school'], {
        errorMap: () => ({ message: 'Please select a valid school type' }),
    }),
    description: z.string().max(2000, 'Description must be less than 2000 characters').nullable().optional(),
    address: z.string().max(500, 'Address must be less than 500 characters').nullable().optional(),
    city: z.string().max(100, 'City must be less than 100 characters').nullable().optional(),
    country: z.string().max(100, 'Country must be less than 100 characters').nullable().optional(),
    phone: z.string().max(20, 'Phone must be less than 20 characters').nullable().optional(),
    email: z.string().email('Please enter a valid email').max(255).nullable().optional().or(z.literal('')),
    website: z.string().url('Please enter a valid URL').max(255).nullable().optional().or(z.literal('')),
    logo: z.string().nullable().optional(),
    established_year: z.number().min(1800, 'Year must be at least 1800').max(new Date().getFullYear(), 'Year cannot be in the future').nullable().optional(),
    accreditation: z.string().max(255, 'Accreditation must be less than 255 characters').nullable().optional(),
    school_lavel: z.string().max(100, 'School level must be less than 100 characters').nullable().optional(),
    currency: z.number().nullable().optional(),
    education_system: z.string().max(100, 'Education system must be less than 100 characters').nullable().optional(),
    tuition_fee_base: z.number().min(0, 'Tuition fee cannot be negative').nullable().optional(),
    total_students: z.number().min(0, 'Total students cannot be negative').default(0),
    total_staff: z.number().min(0, 'Total staff cannot be negative').default(0),
    status: z.boolean(),
});

export type SchoolSchemaType = z.infer<typeof schoolSchema>;
