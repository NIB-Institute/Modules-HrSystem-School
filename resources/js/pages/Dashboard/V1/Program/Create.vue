<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import ProgramForm from '../../../../Components/Dashboard/V1/ProgramForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { programSchema } from '@school/validation/programSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { ProgramFormData, ProgramCreateProps, DegreeLevel } from '@school/types';

const props = defineProps<ProgramCreateProps>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const form = useForm<ProgramFormData>({
    school_id: null,
    department_id: null,
    name: '',
    code: '',
    description: '',
    degree_level: 'bachelor',
    duration_years: null,
    credits_required: null,
    tuition_fee: null,
    admission_requirements: '',
    program_coordinator: '',
    max_students: null,
    current_enrollment: null,
    accreditation_status: '',
    status: true,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    programSchema,
    ['name', 'degree_level', 'school_id']
);

const getFormData = () => ({
    school_id: form.school_id,
    department_id: form.department_id || null,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    degree_level: form.degree_level,
    duration_years: form.duration_years,
    credits_required: form.credits_required,
    tuition_fee: form.tuition_fee,
    admission_requirements: form.admission_requirements || null,
    program_coordinator: form.program_coordinator || null,
    max_students: form.max_students,
    current_enrollment: form.current_enrollment,
    accreditation_status: form.accreditation_status || null,
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
        form.post('/dashboard/programs', {
            onSuccess: () => {
                toast.success('Program created successfully.');
                setTimeout(() => {
                    close();
                    redirect();
                }, 100);
            },
        });
    });
};

const handleCancel = () => {
    close();
    redirect();
};

</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Create Program"
        description="Add a new academic program"
        mode="create"
        size="lg"
        submit-text="Create Program"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <ProgramForm
            v-model="form"
            :degreeLevels="props.degreeLevels"
            :schools="props.schools"
            :departments="props.departments"
        />
    </ModalForm>
</template>
