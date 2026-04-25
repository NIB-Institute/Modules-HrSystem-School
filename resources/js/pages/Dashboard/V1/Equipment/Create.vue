<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import EquipmentForm from '../../../../Components/Dashboard/V1/EquipmentForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import type { EquipmentFormData, EquipmentCreateProps, EquipmentCategory } from '@school/types';

const props = defineProps<EquipmentCreateProps>();

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
    name: '',
    slug: '',
    icon: '',
    description: '',
    category: 'other',
    qty: 0,
    price_total: null,
    status: true,
});

const isFormInvalid = computed(() => {
    return !form.name || !form.category;
});

const handleSubmit = () => {
    form.post('/dashboard/equipment', {
        onSuccess: () => {
            toast.success('Equipment created successfully.');
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
        title="Create Equipment"
        description="Add new equipment type for classrooms"
        mode="create"
        size="md"
        submit-text="Create Equipment"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <EquipmentForm v-model="form" :categories="props.categories" />
    </ModalForm>
</template>
