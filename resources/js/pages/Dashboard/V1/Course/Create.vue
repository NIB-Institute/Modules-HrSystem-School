<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import CourseForm from '../../../../Components/Dashboard/V1/CourseForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { courseSchema } from '@school/validation/courseSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { CourseFormData, CourseCreateProps, CourseType } from '@school/types';

const props = defineProps<CourseCreateProps>();

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

const form = useForm<CourseFormData>({
    department_id: null,
    program_id: null,
    instructor_id: null,
    name: '',
    code: '',
    description: '',
    credits: 3,
    type: 'required',
    semester: null,
    year: null,
    max_students: 30,
    current_enrollment: 0,
    schedule: '',
    room: '',
    prerequisites: [],
    syllabus: '',
    status: true,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    courseSchema,
    ['name', 'credits', 'type']
);

const getFormData = () => ({
    department_id: form.department_id,
    program_id: form.program_id,
    instructor_id: form.instructor_id,
    name: form.name,
    code: form.code || null,
    description: form.description || null,
    credits: form.credits,
    type: form.type,
    semester: form.semester,
    year: form.year,
    max_students: form.max_students,
    current_enrollment: form.current_enrollment,
    schedule: form.schedule || null,
    room: form.room || null,
    prerequisites: form.prerequisites.length > 0 ? form.prerequisites : null,
    syllabus: form.syllabus || null,
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
        form.post('/dashboard/courses', {
            onSuccess: () => {
                toast.success('Course created successfully.');
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
        title="Create Course"
        description="Add a new course to the curriculum"
        mode="create"
        size="lg"
        submit-text="Create Course"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <CourseForm
            v-model="form"
            :types="props.types"
            :departments="props.departments"
            :programs="props.programs"
        />
    </ModalForm>
</template>
