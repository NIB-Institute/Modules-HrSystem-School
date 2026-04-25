<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import CourseForm from '../../../../Components/Dashboard/V1/CourseForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { courseSchema } from '@school/validation/courseSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { CourseFormData, CourseEditProps, CourseType } from '@school/types';

const props = defineProps<CourseEditProps>();

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
    department_id: props.course.department_id,
    program_id: props.course.program_id,
    instructor_id: props.course.instructor_id,
    name: props.course.name,
    code: props.course.code || '',
    description: props.course.description || '',
    credits: props.course.credits,
    type: props.course.type,
    semester: props.course.semester,
    year: props.course.year,
    max_students: props.course.max_students,
    current_enrollment: props.course.current_enrollment,
    schedule: props.course.schedule || '',
    room: props.course.room || '',
    prerequisites: props.course.prerequisites || [],
    syllabus: props.course.syllabus || '',
    status: props.course.status,
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

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/courses/${props.course.uuid}`, {
            onSuccess: () => {
                toast.success('Course updated successfully.');
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
        title="Edit Course"
        :description="`Editing: ${course.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
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
            mode="edit"
        />
    </ModalForm>
</template>
