<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { ImageUpload } from '@/components/shared';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type {
    InventoryFormData,
    InventoryStatus,
    InventoryCondition,
} from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const { __ } = useTranslation();

interface Props {
    mode?: 'create' | 'edit';
    statuses: Record<InventoryStatus, string>;
    conditions: Record<InventoryCondition, string>;
    equipment: { id: number; name: string }[];
    classrooms: { id: number; name: string }[];
    departments: { id: number; name: string }[];
}

withDefaults(defineProps<Props>(), { mode: 'create' });

const model = defineModel<InertiaForm<InventoryFormData>>({ required: true });

const isActive = computed({
    get: () => model.value.is_active,
    set: (val: boolean) => { model.value.is_active = val; },
});

// reka-ui's <SelectItem> forbids `value=""` (reserved for "clear").
// Use this sentinel for nullable selects (classroom, department).
const NONE = '__none__';

const setSelect = <K extends keyof InventoryFormData>(key: K) =>
    (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
        if (value === null || value === undefined || value === '' || value === NONE) {
            (model.value as InventoryFormData)[key] = null as InventoryFormData[K];
            return;
        }
        // numeric ids vs string status/condition
        (model.value as InventoryFormData)[key] = (
            typeof value === 'string' && /^\d+$/.test(value) ? Number(value) : value
        ) as InventoryFormData[K];
    };
</script>

<template>
    <div class="space-y-4">
        <!-- Asset Tag + Serial -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="space-y-2">
                <Label for="asset_tag">
                    {{ __('Asset Tag') }} <span class="text-destructive">*</span>
                </Label>
                <Input id="asset_tag" v-model="model.asset_tag" :placeholder="__('e.g. PRJ-001')" />
                <p v-if="model.errors.asset_tag" class="text-xs text-destructive">{{ model.errors.asset_tag }}</p>
            </div>

            <div class="space-y-2">
                <Label for="serial_number">{{ __('Serial Number') }}</Label>
                <Input id="serial_number" v-model="model.serial_number" :placeholder="__('Optional')" />
                <p v-if="model.errors.serial_number" class="text-xs text-destructive">{{ model.errors.serial_number }}</p>
            </div>
        </div>

        <!-- Name -->
        <div class="space-y-2">
            <Label for="name">{{ __('Display Name') }}</Label>
            <Input id="name" v-model="model.name" :placeholder="__('Optional, defaults to equipment name')" />
        </div>

        <!-- Equipment + Status + Condition -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="space-y-2">
                <Label>
                    {{ __('Equipment') }} <span class="text-destructive">*</span>
                </Label>
                <Select
                    :model-value="model.equipment_id ? String(model.equipment_id) : ''"
                    @update:model-value="setSelect('equipment_id')"
                >
                    <SelectTrigger><SelectValue :placeholder="__('Select equipment')" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="e in equipment" :key="e.id" :value="String(e.id)">
                            {{ e.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="model.errors.equipment_id" class="text-xs text-destructive">{{ model.errors.equipment_id }}</p>
            </div>

            <div class="space-y-2">
                <Label>
                    {{ __('Status') }} <span class="text-destructive">*</span>
                </Label>
                <Select
                    :model-value="model.status"
                    @update:model-value="setSelect('status')"
                >
                    <SelectTrigger><SelectValue :placeholder="__('Select status')" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="(label, value) in statuses" :key="value" :value="value">
                            {{ label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="space-y-2">
                <Label>
                    {{ __('Condition') }} <span class="text-destructive">*</span>
                </Label>
                <Select
                    :model-value="model.condition"
                    @update:model-value="setSelect('condition')"
                >
                    <SelectTrigger><SelectValue :placeholder="__('Select condition')" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="(label, value) in conditions" :key="value" :value="value">
                            {{ label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <!-- Classroom + Department -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="space-y-2">
                <Label>{{ __('Classroom') }}</Label>
                <Select
                    :model-value="model.classroom_id ? String(model.classroom_id) : NONE"
                    @update:model-value="setSelect('classroom_id')"
                >
                    <SelectTrigger><SelectValue :placeholder="__('None')" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="NONE">{{ __('None') }}</SelectItem>
                        <SelectItem v-for="c in classrooms" :key="c.id" :value="String(c.id)">
                            {{ c.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="space-y-2">
                <Label>{{ __('Department') }}</Label>
                <Select
                    :model-value="model.department_id ? String(model.department_id) : NONE"
                    @update:model-value="setSelect('department_id')"
                >
                    <SelectTrigger><SelectValue :placeholder="__('None')" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="NONE">{{ __('None') }}</SelectItem>
                        <SelectItem v-for="d in departments" :key="d.id" :value="String(d.id)">
                            {{ d.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <!-- Acquisition -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="space-y-2">
                <Label for="purchased_at">{{ __('Purchased At') }}</Label>
                <Input id="purchased_at" type="date" v-model="model.purchased_at" />
            </div>

            <div class="space-y-2">
                <Label for="cost">{{ __('Cost') }}</Label>
                <Input
                    id="cost"
                    type="number"
                    step="0.01"
                    min="0"
                    :model-value="model.cost ?? ''"
                    @update:model-value="(v) => model.cost = v === '' ? null : Number(v)"
                />
            </div>

            <div class="space-y-2">
                <Label for="vendor">{{ __('Vendor') }}</Label>
                <Input id="vendor" v-model="model.vendor" :placeholder="__('Supplier name')" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="space-y-2">
                <Label for="warranty_until">{{ __('Warranty Until') }}</Label>
                <Input id="warranty_until" type="date" v-model="model.warranty_until" />
                <p v-if="model.errors.warranty_until" class="text-xs text-destructive">{{ model.errors.warranty_until }}</p>
            </div>

            <div class="flex items-center gap-2 pt-7">
                <Switch id="is_active" v-model:checked="isActive" />
                <Label for="is_active" class="cursor-pointer">{{ __('Active') }}</Label>
            </div>
        </div>

        <!-- Notes -->
        <div class="space-y-2">
            <Label for="notes">{{ __('Notes') }}</Label>
            <Textarea id="notes" v-model="model.notes" :placeholder="__('Optional internal notes...')" rows="3" />
        </div>

        <!-- Images (photos / receipts / docs) -->
        <ImageUpload
            v-model="model.images"
            :label="__('Photos & Documents')"
            :max-files="10"
            :max-size="5"
            :error="model.errors['images'] as string | undefined"
        />
    </div>
</template>
