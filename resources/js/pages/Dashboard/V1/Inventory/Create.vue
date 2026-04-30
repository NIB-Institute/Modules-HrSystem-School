<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import InventoryForm from '../../../../Components/Dashboard/V1/InventoryForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import type { InventoryCreateProps, InventoryFormData } from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps<InventoryCreateProps>();
const { __ } = useTranslation();

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

const form = useForm<InventoryFormData>({
    asset_tag: '',
    serial_number: '',
    name: '',
    equipment_id: null,
    classroom_id: null,
    department_id: null,
    assigned_to_user_id: null,
    status: 'in_stock',
    condition: 'new',
    purchased_at: '',
    cost: null,
    vendor: '',
    warranty_until: '',
    notes: '',
    images: [],
    is_active: true,
});

const isFormInvalid = computed(() => !form.asset_tag || !form.equipment_id);

const handleSubmit = () => {
    form.post('/dashboard/inventories', {
        onSuccess: () => {
            toast.success(__('Item created successfully.'));
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
        :title="__('Add Inventory Item')"
        :description="__('Add a new physical asset to inventory')"
        mode="create"
        size="lg"
        :submit-text="__('Add Item')"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <InventoryForm
            v-model="form"
            :statuses="props.statuses"
            :conditions="props.conditions"
            :equipment="props.equipment"
            :classrooms="props.classrooms"
            :departments="props.departments"
        />
    </ModalForm>
</template>
