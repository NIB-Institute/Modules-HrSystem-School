<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { AlertTriangle, Building } from 'lucide-vue-next';
import type { DepartmentDeleteProps } from '@school/types';

const props = defineProps<DepartmentDeleteProps>();

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
    confirmed: false,
});

watch(confirmed, () => {
    form.confirmed = confirmed.value;
});

const canSubmit = computed(() => confirmed.value === true);

const handleSubmit = () => {
    form.delete(`/dashboard/departments/${props.department.uuid}`, {
        onSuccess: () => {
            toast.success('Department deleted successfully.');
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
        title="Delete Department"
        description="This action cannot be undone"
        mode="delete"
        size="md"
        submit-text="Delete Department"
        :loading="form.processing"
        :disabled="!canSubmit"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-6">
            <!-- Department Info -->
            <div class="flex items-center gap-4 p-4 rounded-lg border bg-muted/30">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <Building class="h-6 w-6 text-primary" />
                </div>
                <div>
                    <p class="font-medium">{{ department.name }}</p>
                    <p v-if="department.school_name" class="text-sm text-muted-foreground">
                        {{ department.school_name }}
                    </p>
                </div>
            </div>

            <!-- Warning Banner -->
            <div class="flex items-start gap-3 rounded-lg border border-destructive/50 bg-destructive/10 p-4">
                <AlertTriangle class="mt-0.5 h-5 w-5 text-destructive" />
                <div class="space-y-1">
                    <p class="text-sm font-medium text-destructive">
                        You are about to delete this department
                    </p>
                    <p class="text-sm text-muted-foreground">
                        <strong>{{ department.name }}</strong> will be permanently removed from the system.
                    </p>
                    <p v-if="department.programs_count && department.programs_count > 0" class="text-sm text-destructive">
                        Warning: This department has <strong>{{ department.programs_count }}</strong> program(s) assigned to it.
                    </p>
                    <p v-if="department.courses_count && department.courses_count > 0" class="text-sm text-destructive">
                        Warning: This department has <strong>{{ department.courses_count }}</strong> course(s) assigned to it.
                    </p>
                    <p v-if="department.employees_count && department.employees_count > 0" class="text-sm text-destructive">
                        Warning: This department has <strong>{{ department.employees_count }}</strong> employee(s) assigned to it.
                    </p>
                </div>
            </div>

            <!-- Confirmation Checkbox -->
            <div class="flex items-start space-x-3 rounded-lg border p-4">
                <Checkbox
                    id="confirmed"
                    v-model="confirmed"
                />
                <div class="space-y-1">
                    <Label for="confirmed" class="cursor-pointer font-medium">
                        I confirm this deletion
                    </Label>
                    <p class="text-sm text-muted-foreground">
                        I understand that this action cannot be undone.
                    </p>
                </div>
            </div>

            <p v-if="form.errors.confirmed" class="text-sm text-destructive">
                {{ form.errors.confirmed }}
            </p>
        </div>
    </ModalForm>
</template>
