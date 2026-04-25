<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { AlertTriangle, Building } from 'lucide-vue-next';
import type { Department } from '@school/types';

interface BulkDeleteProps {
    departments: Department[];
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
    uuids: props.departments.map((d) => d.uuid),
});

watch(confirmed, () => {
    form.clearErrors();
});

const canSubmit = computed(() => confirmed.value === true);

const handleSubmit = () => {
    form.delete('/dashboard/departments/bulk-delete', {
        onSuccess: () => {
            toast.success(`${props.departments.length} department(s) deleted successfully.`);
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
        :title="`Delete ${departments.length} Department${departments.length > 1 ? 's' : ''}`"
        description="This action will move the selected departments to trash"
        mode="delete"
        size="md"
        :submit-text="`Delete ${departments.length} Department${departments.length > 1 ? 's' : ''}`"
        :loading="form.processing"
        :disabled="!canSubmit"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Departments List -->
            <div class="space-y-2">
                <p class="text-sm font-medium text-muted-foreground">
                    The following departments will be deleted:
                </p>
                <div class="max-h-48 space-y-2 overflow-y-auto rounded-lg border p-3">
                    <div
                        v-for="department in departments"
                        :key="department.uuid"
                        class="flex items-center gap-3 rounded-md bg-muted/30 p-2"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                            <Building class="h-4 w-4 text-primary" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ department.name }}</p>
                            <p v-if="department.school_name" class="truncate text-xs text-muted-foreground">
                                {{ department.school_name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Banner -->
            <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <AlertTriangle class="mt-0.5 h-5 w-5 shrink-0 text-destructive" />
                <div class="space-y-1">
                    <p class="text-sm font-medium text-destructive">
                        You are about to delete {{ departments.length }} department{{ departments.length > 1 ? 's' : '' }}
                    </p>
                    <p class="text-sm text-muted-foreground">
                        These departments will be moved to trash. They can be restored within 30 days.
                    </p>
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
                        I confirm this bulk deletion
                    </Label>
                    <p class="text-sm text-muted-foreground">
                        I understand that {{ departments.length }} department{{ departments.length > 1 ? 's' : '' }} will be deleted.
                    </p>
                </div>
            </div>

            <p v-if="form.errors.uuids" class="text-sm text-destructive">
                {{ form.errors.uuids }}
            </p>
        </div>
    </ModalForm>
</template>
