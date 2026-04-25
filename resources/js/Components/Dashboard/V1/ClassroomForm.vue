<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Switch } from '@/components/ui/switch';
import { Checkbox } from '@/components/ui/checkbox';
import { SearchableSelect } from '@/components/shared';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type { ClassroomFormData, ClassroomType } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    types: Record<ClassroomType, string>;
    departments: { id: number | string; name: string }[];
    equipmentOptions: { value: number; label: string }[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    departments: () => [],
    equipmentOptions: () => [],
});

const model = defineModel<InertiaForm<ClassroomFormData>>({ required: true });

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value: value as ClassroomType,
        label,
    }));
});

const departmentOptions = computed(() => {
    return props.departments.map(dept => ({
        value: String(dept.id),
        label: dept.name,
    }));
});

// String wrapper for department id
const departmentIdString = computed({
    get: () => model.value.department_id ? String(model.value.department_id) : '',
    set: (val: string) => {
        model.value.department_id = val;
    }
});

// String wrapper for type
const typeString = computed({
    get: () => model.value.type,
    set: (val: string) => {
        model.value.type = val as ClassroomType;
    }
});

// Number wrappers
const floorValue = computed({
    get: () => model.value.floor ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.floor = value !== undefined && value !== null && value !== '' ? Number(value) : null;
    },
});

const capacityValue = computed({
    get: () => model.value.capacity ?? undefined,
    set: (value: string | number | undefined | null) => {
        model.value.capacity = value !== undefined && value !== null && value !== '' ? Number(value) : 30;
    },
});

// Boolean wrappers
const isActive = computed({
    get: () => model.value.status,
    set: (value: boolean) => {
        model.value.status = value;
    },
});

const isAvailable = computed({
    get: () => model.value.is_available,
    set: (value: boolean) => {
        model.value.is_available = value;
    },
});

const handleEquipmentChange = (equipmentId: number, checked: boolean | 'indeterminate') => {
    if (checked === true) {
        if (!model.value.equipment_ids.includes(equipmentId)) {
            model.value.equipment_ids = [...model.value.equipment_ids, equipmentId];
        }
    } else {
        model.value.equipment_ids = model.value.equipment_ids.filter(id => id !== equipmentId);
    }
};

const handleProjectorChange = (value: boolean | 'indeterminate') => {
    model.value.has_projector = value === true;
};

const handleWhiteboardChange = (value: boolean | 'indeterminate') => {
    model.value.has_whiteboard = value === true;
};

const handleAcChange = (value: boolean | 'indeterminate') => {
    model.value.has_ac = value === true;
};
</script>

<template>
    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
        <!-- Department -->
        <div class="space-y-2">
            <Label for="department_id">
                Department <span class="text-destructive">*</span>
            </Label>
            <SearchableSelect
                v-model="departmentIdString"
                :options="departmentOptions"
                placeholder="Select department"
                search-placeholder="Search departments..."
                empty-message="No departments found."
            />
            <p v-if="model.errors.department_id" class="text-xs text-destructive">
                {{ model.errors.department_id }}
            </p>
        </div>

        <!-- Name & Code -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="name">
                    Name <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="name"
                    v-model="model.name"
                    placeholder="Enter classroom name"
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
                    placeholder="e.g., RM101"
                    :class="{ 'border-destructive': model.errors.code }"
                />
                <p v-if="model.errors.code" class="text-xs text-destructive">
                    {{ model.errors.code }}
                </p>
            </div>
        </div>

        <!-- Type & Capacity -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="type">
                    Type <span class="text-destructive">*</span>
                </Label>
                <Select v-model="typeString">
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
                <Label for="capacity">
                    Capacity <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="capacity"
                    type="number"
                    v-model.number="capacityValue"
                    placeholder="e.g., 30"
                    :class="{ 'border-destructive': model.errors.capacity }"
                />
                <p v-if="model.errors.capacity" class="text-xs text-destructive">
                    {{ model.errors.capacity }}
                </p>
            </div>
        </div>

        <!-- Building & Floor -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="building">Building</Label>
                <Input
                    id="building"
                    v-model="model.building"
                    placeholder="e.g., Main Building"
                />
                <p v-if="model.errors.building" class="text-xs text-destructive">
                    {{ model.errors.building }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="floor">Floor</Label>
                <Input
                    id="floor"
                    type="number"
                    v-model.number="floorValue"
                    placeholder="e.g., 1"
                />
                <p v-if="model.errors.floor" class="text-xs text-destructive">
                    {{ model.errors.floor }}
                </p>
            </div>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <TiptapEditor
                v-model="model.description"
                placeholder="Enter classroom description..."
                min-height="120px"
                max-height="250px"
            />
            <p v-if="model.errors.description" class="text-xs text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <!-- Amenities -->
        <div class="space-y-3">
            <Label>Amenities</Label>
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="flex items-center space-x-2">
                    <Checkbox
                        id="has_projector"
                        :checked="model.has_projector"
                        @update:checked="handleProjectorChange"
                    />
                    <Label for="has_projector" class="cursor-pointer font-normal">
                        Projector
                    </Label>
                </div>
                <div class="flex items-center space-x-2">
                    <Checkbox
                        id="has_whiteboard"
                        :checked="model.has_whiteboard"
                        @update:checked="handleWhiteboardChange"
                    />
                    <Label for="has_whiteboard" class="cursor-pointer font-normal">
                        Whiteboard
                    </Label>
                </div>
                <div class="flex items-center space-x-2">
                    <Checkbox
                        id="has_ac"
                        :checked="model.has_ac"
                        @update:checked="handleAcChange"
                    />
                    <Label for="has_ac" class="cursor-pointer font-normal">
                        Air Conditioning
                    </Label>
                </div>
            </div>
        </div>

        <!-- Equipment -->
        <div class="space-y-3">
            <Label>Additional Equipment</Label>
            <div class="grid gap-3 sm:grid-cols-3">
                <div v-for="equipment in props.equipmentOptions" :key="equipment.value" class="flex items-center space-x-2">
                    <Checkbox
                        :id="`equipment-${equipment.value}`"
                        :checked="model.equipment_ids.includes(equipment.value)"
                        @update:checked="(checked: boolean | 'indeterminate') => handleEquipmentChange(equipment.value, checked)"
                    />
                    <Label :for="`equipment-${equipment.value}`" class="cursor-pointer font-normal">
                        {{ equipment.label }}
                    </Label>
                </div>
            </div>
        </div>

        <!-- Availability & Status -->
        <div class="space-y-4">
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Available for Booking</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isAvailable ? 'Classroom can be booked' : 'Classroom is not available for booking' }}
                    </p>
                </div>
                <Switch v-model="isAvailable" />
            </div>
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Active Status</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isActive ? 'Classroom is active' : 'Classroom is inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </div>
</template>