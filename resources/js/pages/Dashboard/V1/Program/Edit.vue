<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import ProgramForm from '../../../../Components/Dashboard/V1/ProgramForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { programSchema } from '@school/validation/programSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { ProgramFormData, ProgramEditProps, DegreeLevel } from '@school/types';

const props = defineProps<ProgramEditProps>();

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
    school_id: props.program.school_id,
    department_id: props.program.department_id,
    name: props.program.name,
    code: props.program.code || '',
    description: props.program.description || '',
    degree_level: props.program.degree_level,
    duration_years: props.program.duration_years,
    credits_required: props.program.credits_required,
    tuition_fee: props.program.tuition_fee ? parseFloat(props.program.tuition_fee) : null,
    admission_requirements: props.program.admission_requirements || '',
    program_coordinator: props.program.program_coordinator || '',
    max_students: props.program.max_students,
    current_enrollment: props.program.current_enrollment,
    accreditation_status: props.program.accreditation_status || '',
    status: props.program.status,
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

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/programs/${props.program.uuid}`, {
            onSuccess: () => {
                toast.success('Program updated successfully.');
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
        title="Edit Program"
        :description="`Editing: ${program.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
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
            mode="edit"
        />
    </ModalForm>
</template>
