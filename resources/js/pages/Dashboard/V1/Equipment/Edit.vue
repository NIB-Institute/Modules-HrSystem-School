<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import EquipmentForm from '../../../../Components/Dashboard/V1/EquipmentForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import type { EquipmentFormData, EquipmentEditProps, EquipmentCategory } from '@school/types';

const props = defineProps<EquipmentEditProps>();

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

const form = useForm<EquipmentFormData>({
    name: props.equipment.name,
    slug: props.equipment.slug || '',
    icon: props.equipment.icon || '',
    description: props.equipment.description || '',
    category: props.equipment.category,
    qty: props.equipment.qty ?? 0,
    price_total: props.equipment.price_total,
    status: props.equipment.status,
});

const isFormInvalid = computed(() => {
    return !form.name || !form.category;
});

const handleSubmit = () => {
    form.put(`/dashboard/equipment/${props.equipment.uuid}`, {
        onSuccess: () => {
            toast.success('Equipment updated successfully.');
            setTimeout(() => {
                close();
                redirect();
            }, 100);
        },
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
        title="Edit Equipment"
        :description="`Editing: ${equipment.name}`"
        mode="edit"
        size="md"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <EquipmentForm v-model="form" :categories="props.categories" mode="edit" />
    </ModalForm>
</template>
