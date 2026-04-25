<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import TiptapEditor from '@/components/TiptapEditor.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type { ProgramFormData, DegreeLevel } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    degreeLevels: Record<DegreeLevel, string>;
    schools: { id: number; name: string }[];
    departments: { id: number; school_id: number; name: string }[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    schools: () => [],
    departments: () => [],
});

const model = defineModel<InertiaForm<ProgramFormData>>({ required: true });

const degreeLevelOptions = computed(() => {
    return Object.entries(props.degreeLevels).map(([value, label]) => ({
        value: value as DegreeLevel,
        label,
    }));
});

const schoolOptions = computed(() => {
    return props.schools.map((school) => ({
        value: school.id,
        label: school.name,
    }));
});

const filteredDepartments = computed(() => {
    if (!model.value.school_id) return [];
    return props.departments.filter((dept) => dept.school_id === model.value.school_id);
});

const handleDegreeLevelChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        model.value.degree_level = value as DegreeLevel;
    }
};

const handleSchoolChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'number') {
        model.value.school_id = value;
        model.value.department_id = null;
    } else if (typeof value === 'string') {
        model.value.school_id = parseInt(value, 10);
        model.value.department_id = null;
    }
};

const handleDepartmentChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'number') {
        model.value.department_id = value;
    } else if (typeof value === 'string') {
        model.value.department_id = parseInt(value, 10);
    }
};

const isActive = computed({
    get: () => model.value.status,
    set: (value: boolean) => {
        model.value.status = value;
    },
});

// Wrappers for numbers
const durationYearsValue = computed({
    get: () => model.value.duration_years ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.duration_years = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const creditsRequiredValue = computed({
    get: () => model.value.credits_required ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.credits_required = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const tuitionFeeValue = computed({
    get: () => model.value.tuition_fee ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.tuition_fee = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const maxStudentsValue = computed({
    get: () => model.value.max_students ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.max_students = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const currentEnrollmentValue = computed({
    get: () => model.value.current_enrollment ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.current_enrollment = value !== undefined && value !== null && value !== '' ? Number(value) : null;
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
                    placeholder="Enter program name"
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
                    placeholder="e.g., CS-BSC-001"
                    :class="{ 'border-destructive': model.errors.code }"
                />
                <p v-if="model.errors.code" class="text-xs text-destructive">
                    {{ model.errors.code }}
                </p>
            </div>
        </div>

        <!-- School & Department -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="school_id">
                    School <span class="text-destructive">*</span>
                </Label>
                <Select :model-value="model.school_id?.toString()" @update:model-value="handleSchoolChange">
                    <SelectTrigger :class="{ 'border-destructive': model.errors.school_id }">
                        <SelectValue placeholder="Select school" />
                    </SelectTrigger>
                    <SelectContent class="z-200">
                        <SelectItem v-for="school in schoolOptions" :key="school.value" :value="school.value.toString()">
                            {{ school.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="model.errors.school_id" class="text-xs text-destructive">
                    {{ model.errors.school_id }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="department_id">Department</Label>
                <Select :model-value="model.department_id?.toString()" @update:model-value="handleDepartmentChange" :disabled="!model.school_id || filteredDepartments.length === 0">
                    <SelectTrigger>
                        <SelectValue :placeholder="!model.school_id ? 'Select school first' : 'Select department'" />
                    </SelectTrigger>
                    <SelectContent class="z-200">
                        <SelectItem v-for="dept in filteredDepartments" :key="dept.id" :value="dept.id.toString()">
                            {{ dept.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <!-- Degree Level -->
        <div class="space-y-2">
            <Label for="degree_level">
                Degree Level <span class="text-destructive">*</span>
            </Label>
            <Select :model-value="model.degree_level" @update:model-value="handleDegreeLevelChange">
                <SelectTrigger :class="{ 'border-destructive': model.errors.degree_level }">
                    <SelectValue placeholder="Select degree level" />
                </SelectTrigger>
                <SelectContent class="z-200">
                    <SelectItem v-for="level in degreeLevelOptions" :key="level.value" :value="level.value">
                        {{ level.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="model.errors.degree_level" class="text-xs text-destructive">
                {{ model.errors.degree_level }}
            </p>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <TiptapEditor
                v-model="model.description"
                placeholder="Enter program description..."
                min-height="200px"
                max-height="400px"
            />
        </div>

        <!-- Duration & Credits -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="duration_years">Duration (Years)</Label>
                <Input
                    id="duration_years"
                    type="number"
                    v-model.number="durationYearsValue"
                    placeholder="e.g., 4"
                    min="1"
                    max="10"
                />
            </div>
            <div class="space-y-2">
                <Label for="credits_required">Credits Required</Label>
                <Input
                    id="credits_required"
                    type="number"
                    v-model.number="creditsRequiredValue"
                    placeholder="e.g., 120"
                    min="0"
                />
            </div>
        </div>

        <!-- Tuition & Coordinator -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="tuition_fee">Tuition Fee</Label>
                <Input
                    id="tuition_fee"
                    type="number"
                    v-model.number="tuitionFeeValue"
                    placeholder="e.g., 15000.00"
                    min="0"
                    step="0.01"
                />
            </div>
            <div class="space-y-2">
                <Label for="program_coordinator">Program Coordinator</Label>
                <Input
                    id="program_coordinator"
                    v-model="model.program_coordinator"
                    placeholder="Coordinator name"
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
                    placeholder="e.g., 100"
                    min="0"
                />
            </div>
            <div class="space-y-2">
                <Label for="current_enrollment">Current Enrollment</Label>
                <Input
                    id="current_enrollment"
                    type="number"
                    v-model.number="currentEnrollmentValue"
                    placeholder="e.g., 75"
                    min="0"
                />
            </div>
        </div>

        <!-- Admission Requirements -->
        <div class="space-y-2">
            <Label for="admission_requirements">Admission Requirements</Label>
            <TiptapEditor
                v-model="model.admission_requirements"
                placeholder="Enter admission requirements..."
                min-height="150px"
                max-height="300px"
            />
        </div>

        <!-- Accreditation Status -->
        <div class="space-y-2">
            <Label for="accreditation_status">Accreditation Status</Label>
            <Input
                id="accreditation_status"
                v-model="model.accreditation_status"
                placeholder="e.g., Fully Accredited"
            />
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between rounded-lg border p-4">
            <div>
                <p class="text-sm font-medium">Active Status</p>
                <p class="text-xs text-muted-foreground">
                    {{ isActive ? 'Program will be active' : 'Program will be inactive' }}
                </p>
            </div>
            <Switch v-model="isActive" />
        </div>
    </div>
</template>
