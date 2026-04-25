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
import type { EquipmentFormData, EquipmentCategory } from '@school/types';

interface Props {
    mode?: 'create' | 'edit';
    categories: Record<EquipmentCategory, string>;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const model = defineModel<InertiaForm<EquipmentFormData>>({ required: true });

const categoryOptions = computed(() => {
    return Object.entries(props.categories).map(([value, label]) => ({
        value: value as EquipmentCategory,
        label,
    }));
});

const handleCategoryChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        model.value.category = value as EquipmentCategory;
    }
};

const isActive = computed({
    get: () => model.value.status,
    set: (value: boolean) => {
        model.value.status = value;
    },
});

const qtyValue = computed({
    get: () => model.value.qty ?? 0,
    set: (val: string | number | undefined | null) => {
        model.value.qty = val ? Number(val) : 0;
    }
});

const priceTotalValue = computed({
    get: () => model.value.price_total ?? undefined,
    set: (val: string | number | undefined | null) => {
        model.value.price_total = val ? Number(val) : null;
    }
});
</script>

<template>
    <div class="space-y-4">
        <!-- Name -->
        <div class="space-y-2">
            <Label for="name">
                Name <span class="text-destructive">*</span>
            </Label>
            <Input
                id="name"
                v-model="model.name"
                placeholder="e.g., Projector"
                :class="{ 'border-destructive': model.errors.name }"
            />
            <p v-if="model.errors.name" class="text-xs text-destructive">
                {{ model.errors.name }}
            </p>
        </div>

        <!-- Slug -->
        <div class="space-y-2">
            <Label for="slug">Slug</Label>
            <Input
                id="slug"
                v-model="model.slug"
                placeholder="e.g., projector (auto-generated if empty)"
                :class="{ 'border-destructive': model.errors.slug }"
            />
            <p v-if="model.errors.slug" class="text-xs text-destructive">
                {{ model.errors.slug }}
            </p>
        </div>

        <!-- Category -->
        <div class="space-y-2">
            <Label for="category">
                Category <span class="text-destructive">*</span>
            </Label>
            <Select :model-value="model.category" @update:model-value="handleCategoryChange">
                <SelectTrigger :class="{ 'border-destructive': model.errors.category }">
                    <SelectValue placeholder="Select category" />
                </SelectTrigger>
                <SelectContent class="z-200">
                    <SelectItem v-for="cat in categoryOptions" :key="cat.value" :value="cat.value">
                        {{ cat.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <p v-if="model.errors.category" class="text-xs text-destructive">
                {{ model.errors.category }}
            </p>
        </div>

        <!-- Icon -->
        <div class="space-y-2">
            <Label for="icon">Icon (Lucide icon name)</Label>
            <Input
                id="icon"
                v-model="model.icon"
                placeholder="e.g., monitor, projector"
            />
            <p v-if="model.errors.icon" class="text-xs text-destructive">
                {{ model.errors.icon }}
            </p>
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <Label for="description">Description</Label>
            <TiptapEditor
                v-model="model.description"
                placeholder="Enter equipment description..."
                min-height="120px"
                max-height="250px"
            />
            <p v-if="model.errors.description" class="text-xs text-destructive">
                {{ model.errors.description }}
            </p>
        </div>

        <!-- Quantity & Price -->
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
                <Label for="qty">Quantity</Label>
                <Input
                    id="qty"
                    type="number"
                    v-model.number="qtyValue"
                    placeholder="0"
                    min="0"
                />
                <p v-if="model.errors.qty" class="text-xs text-destructive">
                    {{ model.errors.qty }}
                </p>
            </div>
            <div class="space-y-2">
                <Label for="price_total">Total Price</Label>
                <Input
                    id="price_total"
                    type="number"
                    step="0.01"
                    v-model.number="priceTotalValue"
                    placeholder="0.00"
                    min="0"
                />
                <p v-if="model.errors.price_total" class="text-xs text-destructive">
                    {{ model.errors.price_total }}
                </p>
            </div>
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between rounded-lg border p-4">
            <div>
                <p class="text-sm font-medium">Active Status</p>
                <p class="text-xs text-muted-foreground">
                    {{ isActive ? 'Equipment is active and available' : 'Equipment is inactive' }}
                </p>
            </div>
            <Switch v-model="isActive" />
        </div>
    </div>
</template>
