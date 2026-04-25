<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import ClassroomForm from '../../../../Components/Dashboard/V1/ClassroomForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { classroomSchema } from '@school/validation/classroomSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { ClassroomFormData, ClassroomCreateProps, ClassroomType } from '@school/types';

const props = defineProps<ClassroomCreateProps>();

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
    department_id: '',
    name: '',
    code: '',
    building: '',
    floor: null,
    capacity: 30,
    type: 'classroom',
    equipment: [],
    equipment_ids: [],
    description: '',
    has_projector: false,
    has_whiteboard: true,
    has_ac: false,
    is_available: true,
    status: true,
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

watch(() => form.name, () => {
    if (form.name) {
        validateForm(getFormData());
    }
});

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.post('/dashboard/classrooms', {
            onSuccess: () => {
                toast.success('Classroom created successfully.');
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
        title="Create Classroom"
        description="Add a new classroom or facility"
        mode="create"
        size="lg"
        submit-text="Create Classroom"
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
        />
    </ModalForm>
</template>
