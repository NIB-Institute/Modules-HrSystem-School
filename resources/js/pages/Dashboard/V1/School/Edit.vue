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
import type { SchoolFormData, SchoolEditProps } from '@school/types';

const props = defineProps<SchoolEditProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Schools', href: '/dashboard/schools' },
    { title: props.school.name, href: `/dashboard/schools/${props.school.uuid}` },
    { title: 'Edit', href: `/dashboard/schools/${props.school.uuid}/edit` },
];

const form = useForm<SchoolFormData>({
    name: props.school.name,
    code: props.school.code || '',
    type: props.school.type,
    description: props.school.description || '',
    address: props.school.address || '',
    city: props.school.city || '',
    country: props.school.country || '',
    phone: props.school.phone || '',
    email: props.school.email || '',
    website: props.school.website || '',
    logo: props.school.logo || '',
    established_year: props.school.established_year,
    accreditation: props.school.accreditation || '',
    school_lavel: props.school.school_lavel || '',
    currency: props.school.currency,
    education_system: props.school.education_system || '',
    tuition_fee_base: props.school.tuition_fee_base,
    total_students: props.school.total_students,
    total_staff: props.school.total_staff,
    status: props.school.status,
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

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/schools/${props.school.uuid}`, {
            onSuccess: () => {
                toast.success('School updated successfully.');
                router.visit(`/dashboard/schools/${props.school.uuid}`);
            },
        });
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit ${school.name}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link :href="`/dashboard/schools/${school.uuid}`" class="text-muted-foreground hover:text-foreground">
                    <ChevronLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-xl font-semibold">Edit School</h1>
                    <p class="text-sm text-muted-foreground">{{ school.name }} - {{ school.code || 'No Code' }}</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <SchoolForm
                    :form="form"
                    mode="edit"
                    :types="props.types"
                />

                <!-- Actions at Bottom -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button type="button" variant="outline" as-child>
                        <Link :href="`/dashboard/schools/${school.uuid}`">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="isFormInvalid || form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
