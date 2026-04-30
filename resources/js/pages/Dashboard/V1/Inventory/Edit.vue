<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import InventoryForm from '../../../../Components/Dashboard/V1/InventoryForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import type { InventoryFormData, InventoryEditProps } from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps<InventoryEditProps>();
const { t } = useTranslation();

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
    asset_tag: props.inventory.asset_tag,
    serial_number: props.inventory.serial_number ?? '',
    name: props.inventory.name ?? '',
    equipment_id: props.inventory.equipment_id,
    classroom_id: props.inventory.classroom_id,
    department_id: props.inventory.department_id,
    assigned_to_user_id: props.inventory.assigned_to_user_id,
    status: props.inventory.status,
    condition: props.inventory.condition,
    purchased_at: props.inventory.purchased_at ?? '',
    cost: props.inventory.cost === null || props.inventory.cost === undefined
        ? null
        : Number(props.inventory.cost),
    vendor: props.inventory.vendor ?? '',
    warranty_until: props.inventory.warranty_until ?? '',
    notes: props.inventory.notes ?? '',
    images: props.inventory.images ?? [],
    is_active: props.inventory.is_active,
});

const isFormInvalid = computed(() => !form.asset_tag || !form.equipment_id);

const handleSubmit = () => {
    form.put(`/dashboard/inventories/${props.inventory.uuid}`, {
        onSuccess: () => {
            toast.success(t('Item updated successfully.'));
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
        :title="t('Edit Inventory Item')"
        :description="t('Editing asset tag: :tag', { tag: inventory.asset_tag })"
        mode="edit"
        size="lg"
        :submit-text="t('Save Changes')"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <InventoryForm
            v-model="form"
            mode="edit"
            :statuses="props.statuses"
            :conditions="props.conditions"
            :equipment="props.equipment"
            :classrooms="props.classrooms"
            :departments="props.departments"
        />
    </ModalForm>
</template>
