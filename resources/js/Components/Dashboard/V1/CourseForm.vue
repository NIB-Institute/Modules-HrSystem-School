<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type { CourseFormData, CourseType } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    types: Record<CourseType, string>;
    departments: { id: number | string; name: string }[];
    programs: { id: number | string; name: string }[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    departments: () => [],
    programs: () => [],
});

const model = defineModel<InertiaForm<CourseFormData>>({ required: true });

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value: value as CourseType,
        label,
    }));
});

const handleTypeChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        model.value.type = value as CourseType;
    }
};

const handleDepartmentChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (value === 'none') {
        model.value.department_id = null;
    } else if (typeof value === 'string') {
        model.value.department_id = parseInt(value);
    }
};

const handleProgramChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (value === 'none') {
        model.value.program_id = null;
    } else if (typeof value === 'string') {
        model.value.program_id = parseInt(value);
    }
};

const isActive = computed({
    get: () => model.value.status,
    set: (value: boolean) => {
        model.value.status = value;
    },
});

// Wrappers for numbers
const creditsValue = computed({
    get: () => model.value.credits ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.credits = value !== undefined && value !== null && value !== '' ? Number(value) : 3;
    },
});

const semesterValue = computed({
    get: () => model.value.semester ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.semester = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const yearValue = computed({
    get: () => model.value.year ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.year = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const maxStudentsValue = computed({
    get: () => model.value.max_students ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.max_students = value !== undefined && value !== null && value !== '' ? Number(value) : 30;
    },
});

const currentEnrollmentValue = computed({
    get: () => model.value.current_enrollment ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.current_enrollment = value !== undefined && value !== null && value !== '' ? Number(value) : 0;
    },
});
</script>

<template>
    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
        <!-- Name & Code -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="name">
                    Name <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="name"
                    v-model="model.name"
                    placeholder="Enter course name"
                    :class="{ 'border-destructive': model.errors.name }"
                />
                <p v-if="model.errors.name" class="text-xs text-destructive">
                    {{ model.errors.name }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="code">Code</Label>
                <Input
                    id="code"
                    v-model="model.code"
                    placeholder="e.g., CS101"
                    :class="{ 'border-destructive': model.errors.code }"
                />
                <p v-if="model.errors.code" class="text-xs text-destructive">
                    {{ model.errors.code }}
                </p>
            </div>
        </div>

        <!-- Type & Credits -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="type">
                    Type <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="model.type" @update:model-value="handleTypeChange">
                    <SelectTrigger :class="{ 'border-destructive': model.errors.type }">
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent class="z-200">
                        <SelectItem v-for="type in typeOptions" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="model.errors.type" class="text-xs text-destructive">
                    {{ model.errors.type }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="credits">
                    Credits <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="credits"
                    type="number"
                    v-model.number="creditsValue"
                    min="1"
                    max="20"
                    :class="{ 'border-destructive': model.errors.credits }"
                />
                <p v-if="model.errors.credits" class="text-xs text-destructive">
                    {{ model.errors.credits }}
                </p>
            </div>
        </div>

        <!-- Department & Program -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="department_id">Department</Label>
                <Select :model-value="model.department_id?.toString() || 'none'" @update:model-value="handleDepartmentChange">
                    <SelectTrigger>
                        <SelectValue placeholder="Select department" />
                    </SelectTrigger>
                    <SelectContent class="z-200">
                        <SelectItem value="none">No Department</SelectItem>
                        <SelectItem v-for="dept in props.departments" :key="dept.id" :value="dept.id.toString()">
                            {{ dept.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="space-y-2">
                <Label for="program_id">Program</Label>
                <Select :model-value="model.program_id?.toString() || 'none'" @update:model-value="handleProgramChange">
                    <SelectTrigger>
                        <SelectValue placeholder="Select program" />
                    </SelectTrigger>
                    <SelectContent class="z-200">
                        <SelectItem value="none">No Program</SelectItem>
                        <SelectItem v-for="prog in props.programs" :key="prog.id" :value="prog.id.toString()">
                            {{ prog.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <TiptapEditor
                v-model="model.description"
                placeholder="Enter course description..."
                min-height="150px"
                max-height="300px"
            />
        </div>

        <!-- Semester & Year -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="semester">Semester</Label>
                <Input
                    id="semester"
                    type="number"
                    v-model.number="semesterValue"
                    min="1"
                    max="8"
                    placeholder="e.g., 1"
                />
            </div>
            <div class="space-y-2">
                <Label for="year">Year</Label>
                <Input
                    id="year"
                    type="number"
                    v-model.number="yearValue"
                    min="1"
                    max="10"
                    placeholder="e.g., 1"
                />
            </div>
        </div>

        <!-- Max Students & Current Enrollment -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="max_students">Max Students</Label>
                <Input
                    id="max_students"
                    type="number"
                    v-model.number="maxStudentsValue"
                    min="1"
                    placeholder="e.g., 30"
                />
            </div>
            <div class="space-y-2">
                <Label for="current_enrollment">Current Enrollment</Label>
                <Input
                    id="current_enrollment"
                    type="number"
                    v-model.number="currentEnrollmentValue"
                    min="0"
                    placeholder="e.g., 0"
                />
            </div>
        </div>

        <!-- Schedule & Room (if mode === 'create') / Max Students & Schedule (if mode !== 'edit') depending on original logic => we unify the form fields -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="schedule">Schedule</Label>
                <Input
                    id="schedule"
                    v-model="model.schedule"
                    placeholder="e.g., Mon/Wed 9:00-10:30"
                />
            </div>
            <div class="space-y-2">
                <Label for="room">Room</Label>
                <Input
                    id="room"
                    v-model="model.room"
                    placeholder="e.g., Room 101"
                />
            </div>
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between rounded-lg border p-4">
            <div>
                <p class="text-sm font-medium">Active Status</p>
                <p class="text-xs text-muted-foreground">
                    {{ isActive ? 'Course will be active' : 'Course will be inactive' }}
                </p>
            </div>
            <Switch v-model="isActive" />
        </div>
    </div>
</template>
