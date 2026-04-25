import { z } from 'zod';

export const equipmentSchema = z.object({
    name: z.string().min(1, 'Equipment name is required').max(100, 'Name must be less than 100 characters'),
    slug: z.string().max(100, 'Slug must be less than 100 characters').nullable().optional(),
    icon: z.string().max(50, 'Icon must be less than 50 characters').nullable().optional(),
    description: z.string().nullable().optional(),
    category: z.enum(['technology', 'furniture', 'safety', 'accessibility', 'other'], {
        errorMap: () => ({ message: 'Please select a valid category' }),
    }),
    qty: z.number().min(0, 'Quantity cannot be negative').default(0),
    price_total: z.number().min(0, 'Price cannot be negative').nullable().optional(),
    status: z.boolean().default(true),
});

export type EquipmentSchemaType = z.infer<typeof equipmentSchema>;
