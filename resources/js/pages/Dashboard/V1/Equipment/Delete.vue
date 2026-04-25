<script setup lang="ts">
import { ModalConfirm } from '@/components/shared';
import { router } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import type { EquipmentDeleteProps } from '@school/types';

const props = defineProps<EquipmentDeleteProps>();

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

    router.delete(`/dashboard/equipment/${props.equipment.uuid}`, {
        onSuccess: () => {
            toast.success('Equipment deleted successfully.');
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
        title="Delete Equipment"
        :description="`Are you sure you want to delete '${equipment.name}'?`"
        confirm-text="Delete"
        :loading="isDeleting"
        variant="danger"
        @confirm="handleConfirm"
        @cancel="handleCancel"
    >
        <div class="space-y-4">
            <p class="text-sm text-muted-foreground">
                This action cannot be undone. This will permanently delete the equipment
                and remove it from any classrooms that have it assigned.
            </p>

            <div v-if="equipment.classrooms_count && equipment.classrooms_count > 0" class="rounded-md bg-destructive/10 p-3">
                <p class="text-sm text-destructive">
                    Warning: This equipment is currently assigned to {{ equipment.classrooms_count }} classroom(s).
                </p>
            </div>
        </div>
    </ModalConfirm>
</template>
