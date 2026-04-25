<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import DepartmentForm from '../../../../Components/Dashboard/V1/DepartmentForm.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';
import { departmentSchema } from '@school/validation/departmentSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import { ChevronLeft } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { DepartmentFormData, DepartmentEditProps } from '@school/types';

const props = defineProps<DepartmentEditProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: '/dashboard/departments' },
    { title: props.department.name, href: `/dashboard/departments/${props.department.uuid}` },
    { title: 'Edit', href: `/dashboard/departments/${props.department.uuid}/edit` },
];

const form = useForm<DepartmentFormData>({
    school_id: String(props.department.school_id),
    name: props.department.name,
    code: props.department.code || '',
    description: props.department.description || '',
    head_of_department: props.department.head_of_department || '',
    email: props.department.email || '',
    phone: props.department.phone || '',
    office_location: props.department.office_location || '',
    established_year: props.department.established_year,
    total_students: props.department.total_students,
    total_staff: props.department.total_staff,
    status: props.department.status,
    // Location linking for geofence
    location_id: props.department.location_id ?? null,
    latitude: props.department.latitude,
    longitude: props.department.longitude,
    geofence_radius: props.department.geofence_radius ?? 100,
    enforce_geofence: props.department.enforce_geofence ?? false,
    timezone: props.department.timezone ?? null,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    departmentSchema,
    ['name', 'school_id']
);

const getFormData = () => ({
    school_id: form.school_id,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    head_of_department: form.head_of_department || null,
    email: form.email || null,
    phone: form.phone || null,
    office_location: form.office_location || null,
    established_year: form.established_year,
    total_students: form.total_students,
    total_staff: form.total_staff,
    status: form.status,
    // Location linking (preferred)
    location_id: form.location_id,
    // Manual geofence fields (fallback if no location_id)
    latitude: form.latitude,
    longitude: form.longitude,
    geofence_radius: form.geofence_radius,
    enforce_geofence: form.enforce_geofence,
    timezone: form.timezone,
});

watch([() => form.name, () => form.school_id], () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/departments/${props.department.uuid}`, {
            onSuccess: () => {
                toast.success('Department updated successfully.');
                router.visit('/dashboard/departments');
            },
        });
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit ${props.department.name}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/dashboard/departments" class="text-muted-foreground hover:text-foreground">
                    <ChevronLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-xl font-semibold">Edit Department</h1>
                    <p class="text-sm text-muted-foreground">Editing: {{ props.department.name }}</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <DepartmentForm
                    :form="form"
                    :schools="props.schools"
                    :available-locations="props.availableLocations"
                    mode="edit"
                />

                <!-- Actions at Bottom -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button type="button" variant="outline" as-child>
                        <Link href="/dashboard/departments">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="isFormInvalid || form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
