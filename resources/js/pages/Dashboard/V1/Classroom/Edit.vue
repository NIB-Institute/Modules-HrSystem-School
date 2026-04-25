<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import ClassroomForm from '../../../../Components/Dashboard/V1/ClassroomForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { classroomSchema } from '@school/validation/classroomSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { ClassroomFormData, ClassroomEditProps, ClassroomType } from '@school/types';

const props = defineProps<ClassroomEditProps>();

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

const form = useForm<ClassroomFormData>({
    department_id: props.classroom.department_id || '',
    name: props.classroom.name,
    code: props.classroom.code || '',
    building: props.classroom.building || '',
    floor: props.classroom.floor,
    capacity: props.classroom.capacity,
    type: props.classroom.type,
    equipment: props.classroom.equipment || [],
    equipment_ids: props.classroom.equipment_ids || [],
    description: props.classroom.description || '',
    has_projector: props.classroom.has_projector,
    has_whiteboard: props.classroom.has_whiteboard,
    has_ac: props.classroom.has_ac,
    is_available: props.classroom.is_available,
    status: props.classroom.status,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    classroomSchema,
    ['department_id', 'name', 'type', 'capacity']
);

const getFormData = () => ({
    department_id: form.department_id,
    name: form.name,
    code: form.code || null,
    building: form.building || null,
    floor: form.floor,
    capacity: form.capacity,
    type: form.type,
    equipment: form.equipment,
    equipment_ids: form.equipment_ids,
    description: form.description || null,
    has_projector: form.has_projector,
    has_whiteboard: form.has_whiteboard,
    has_ac: form.has_ac,
    is_available: form.is_available,
    status: form.status,
});

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/classrooms/${props.classroom.uuid}`, {
            onSuccess: () => {
                toast.success('Classroom updated successfully.');
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
        title="Edit Classroom"
        :description="`Editing: ${classroom.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <ClassroomForm
            v-model="form"
            :types="props.types"
            :departments="props.departments"
            :equipment-options="props.equipmentOptions"
            mode="edit"
        />
    </ModalForm>
</template>
