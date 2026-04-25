<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import SchoolForm from '../../../../Components/Dashboard/V1/SchoolForm.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';
import { schoolSchema } from '@school/validation/schoolSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import { ChevronLeft } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { SchoolFormData, SchoolCreateProps } from '@school/types';

const props = defineProps<SchoolCreateProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Schools', href: '/dashboard/schools' },
    { title: 'Create', href: '/dashboard/schools/create' },
];

const form = useForm<SchoolFormData>({
    name: '',
    code: '',
    type: 'school',
    description: '',
    address: '',
    city: '',
    country: '',
    phone: '',
    email: '',
    website: '',
    logo: '',
    established_year: null,
    accreditation: '',
    school_lavel: '',
    currency: null,
    education_system: '',
    tuition_fee_base: null,
    total_students: 0,
    total_staff: 0,
    status: true,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    schoolSchema,
    ['name', 'type']
);

const getFormData = () => ({
    name: form.name,
    code: form.code || null,
    type: form.type,
    description: form.description || null,
    address: form.address || null,
    city: form.city || null,
    country: form.country || null,
    phone: form.phone || null,
    email: form.email || null,
    website: form.website || null,
    logo: form.logo || null,
    established_year: form.established_year,
    accreditation: form.accreditation || null,
    school_lavel: form.school_lavel || null,
    currency: form.currency,
    education_system: form.education_system || null,
    tuition_fee_base: form.tuition_fee_base,
    total_students: form.total_students,
    total_staff: form.total_staff,
    status: form.status,
});

watch(() => form.name, () => {
    if (form.name) {
        validateForm(getFormData());
    }
});

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/schools', {
            onSuccess: () => {
                toast.success('School created successfully.');
                router.visit('/dashboard/schools');
            },
        });
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Create School" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/dashboard/schools" class="text-muted-foreground hover:text-foreground">
                    <ChevronLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-xl font-semibold">Create School</h1>
                    <p class="text-sm text-muted-foreground">Add a new school or institution</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <SchoolForm
                    :form="form"
                    mode="create"
                    :types="props.types"
                />

                <!-- Actions at Bottom -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button type="button" variant="outline" as-child>
                        <Link href="/dashboard/schools">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="isFormInvalid || form.processing">
                        {{ form.processing ? 'Creating...' : 'Create School' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
