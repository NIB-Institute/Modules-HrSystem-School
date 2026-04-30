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
import { useTranslation } from '@/composables/useTranslation';

interface BulkDeleteProps {
    inventories: Inventory[];
}

const props = defineProps<BulkDeleteProps>();
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

const confirmed = ref(false);

const form = useForm({
    uuids: props.inventories.map((i) => i.uuid),
});

watch(confirmed, () => form.clearErrors());

const canSubmit = computed(() => confirmed.value === true);

const handleSubmit = () => {
    form.delete('/dashboard/inventories/bulk-delete', {
        onSuccess: () => {
            toast.success(__(':count item(s) deleted successfully.', { count: props.inventories.length }));
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
        :title="__('Delete :count Item(s)', { count: inventories.length })"
        :description="__('This action will move the selected items to trash')"
        mode="delete"
        size="md"
        :submit-text="__('Delete :count Item(s)', { count: inventories.length })"
        :loading="form.processing"
        :disabled="!canSubmit"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Warning Banner -->
            <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <AlertTriangle class="mt-0.5 h-5 w-5 shrink-0 text-destructive" />
                <div class="space-y-1">
                    <p class="text-sm font-medium text-destructive">
                        {{ __('You are about to delete :count item(s)', { count: inventories.length }) }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        {{ __('These items will be moved to trash. They can be restored within 30 days.') }}
                    </p>
                </div>
            </div>

            <!-- Items List -->
            <div class="space-y-2">
                <p class="text-sm font-medium text-muted-foreground">
                    {{ __('The following items will be deleted:') }}
                </p>
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
            </div>

            <!-- Confirmation Checkbox -->
            <div class="flex items-start space-x-3 rounded-lg border p-4">
                <Checkbox
                    id="bulk-confirmed"
                    :model-value="confirmed"
                    @update:model-value="(val: boolean | 'indeterminate') => confirmed = val === true"
                />
                <div class="space-y-1">
                    <Label for="bulk-confirmed" class="cursor-pointer font-medium">
                        {{ __('I confirm this bulk deletion') }}
                    </Label>
                    <p class="text-sm text-muted-foreground">
                        {{ __('I understand that :count item(s) will be deleted.', { count: inventories.length }) }}
                    </p>
                </div>
            </div>
        </div>
    </ModalForm>
</template>
