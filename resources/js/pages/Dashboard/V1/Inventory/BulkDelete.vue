<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { AlertTriangle, Package } from 'lucide-vue-next';
import type { Inventory } from '@school/types';

interface BulkDeleteProps {
    inventories: Inventory[];
}

const props = defineProps<BulkDeleteProps>();

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

const confirmed = ref(false);

const form = useForm({
    uuids: props.inventories.map((i) => i.uuid),
});

watch(confirmed, () => form.clearErrors());

const canSubmit = computed(() => confirmed.value === true);

const handleSubmit = () => {
    form.delete('/dashboard/inventories/bulk-delete', {
        onSuccess: () => {
            toast.success(`${props.inventories.length} inventory item(s) deleted successfully.`);
            setTimeout(() => {
                close();
                redirect();
            }, 100);
        },
        onError: (errors) => {
            if (errors.uuids) {
                toast.error(errors.uuids);
            }
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
        :title="`Delete ${inventories.length} Inventory Item${inventories.length > 1 ? 's' : ''}`"
        description="This will move the selected items to trash"
        mode="delete"
        size="md"
        :submit-text="`Delete ${inventories.length} Item${inventories.length > 1 ? 's' : ''}`"
        :loading="form.processing"
        :disabled="!canSubmit"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <div class="flex items-start gap-2 rounded-lg border border-destructive/30 bg-destructive/5 p-3 text-sm">
                <AlertTriangle class="mt-0.5 h-4 w-4 text-destructive" />
                <p>
                    You are about to delete <strong>{{ inventories.length }}</strong> inventory item{{ inventories.length > 1 ? 's' : '' }}.
                    This action moves them to trash and can be restored later.
                </p>
            </div>

            <div class="max-h-64 space-y-1.5 overflow-y-auto rounded-md border p-3">
                <div
                    v-for="item in inventories"
                    :key="item.uuid"
                    class="flex items-center justify-between rounded px-2 py-1.5 text-sm hover:bg-muted"
                >
                    <div class="flex items-center gap-2">
                        <Package class="h-4 w-4 text-muted-foreground" />
                        <span class="font-medium">{{ item.asset_tag }}</span>
                        <span class="text-muted-foreground">— {{ item.equipment?.name || item.name || '—' }}</span>
                    </div>
                    <span class="text-xs text-muted-foreground">{{ item.status_label }}</span>
                </div>
            </div>

            <label class="flex cursor-pointer items-start gap-2">
                <Checkbox v-model="confirmed" />
                <Label class="cursor-pointer text-sm">
                    I understand this will delete {{ inventories.length }} item{{ inventories.length > 1 ? 's' : '' }}.
                </Label>
            </label>
        </div>
    </ModalForm>
</template>
