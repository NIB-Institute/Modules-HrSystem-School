<script setup lang="ts">
import { ModalConfirm } from '@/components/shared';
import { router } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { useTranslation } from '@/composables/useTranslation';
import type { InventoryDeleteProps } from '@school/types';

const props = defineProps<InventoryDeleteProps>();
const { __ } = useTranslation();

const { show, close, redirect } = useModal();
const isDeleting = ref(false);

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const handleConfirm = () => {
    isDeleting.value = true;

    router.delete(`/dashboard/inventories/${props.inventory.uuid}`, {
        onSuccess: () => {
            toast.success(__('Inventory item deleted successfully.'));
            close();
            redirect();
        },
        onFinish: () => {
            isDeleting.value = false;
        },
    });
};

const handleCancel = () => {
    close();
    redirect();
};
</script>

<template>
    <ModalConfirm
        v-model:open="isOpen"
        :title="__('Delete Inventory Item')"
        :description="__('Are you sure you want to delete \':tag\'?', { tag: inventory.asset_tag })"
        :confirm-text="__('Delete')"
        :loading="isDeleting"
        variant="danger"
        @confirm="handleConfirm"
        @cancel="handleCancel"
    >
        <div class="space-y-4">
            <p class="text-sm text-muted-foreground">
                {{ __('This will move the inventory item to trash. Soft-deleted items can be restored later.') }}
            </p>
            <div class="rounded-lg border border-border bg-muted/30 p-3 text-sm">
                <div class="flex justify-between"><span class="text-muted-foreground">Asset Tag</span><span class="font-medium">{{ inventory.asset_tag }}</span></div>
                <div v-if="inventory.serial_number" class="flex justify-between mt-1"><span class="text-muted-foreground">Serial</span><span>{{ inventory.serial_number }}</span></div>
                <div class="flex justify-between mt-1"><span class="text-muted-foreground">Equipment</span><span>{{ inventory.equipment?.name || '—' }}</span></div>
                <div class="flex justify-between mt-1"><span class="text-muted-foreground">Status</span><span>{{ inventory.status_label }}</span></div>
            </div>
        </div>
    </ModalConfirm>
</template>
