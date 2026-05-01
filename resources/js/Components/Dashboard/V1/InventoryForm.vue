<script setup lang="ts">
import { computed, watch } from 'vue';
import axios from 'axios';
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
import {
    Package,
    Tag,
    Info,
    MapPin,
    Calendar,
    DollarSign,
    Store,
    ShieldCheck,
    FileImage,
    LayoutDashboard,
    RefreshCw
} from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { useTranslation } from '@/composables/useTranslation';
import TiptapEditor from '@/components/TiptapEditor.vue';

const { __ } = useTranslation();

interface Props {
    mode?: 'create' | 'edit';
    statuses: Record<InventoryStatus, string>;
    conditions: Record<InventoryCondition, string>;
    equipment: { id: number; name: string }[];
    classrooms: { id: number; name: string; department_id: number | null }[];
    departments: { id: number; name: string }[];
}

const props = withDefaults(defineProps<Props>(), { mode: 'create' });

const model = defineModel<InertiaForm<InventoryFormData>>({ required: true });

// Classrooms belong to a department. When a department is picked, only show
// its classrooms; if no department is picked, show none (force the cascade).
// Laravel may serialize unsigned bigint as either number or string depending on
// driver/config — coerce both sides before comparing to be safe.
const filteredClassrooms = computed(() => {
    const deptId = model.value.department_id;
    if (!deptId) return [];
    const target = Number(deptId);
    return props.classrooms.filter(c => Number(c.department_id) === target);
});

// If the user changes department and the current classroom no longer belongs to it,
// clear the classroom selection.
watch(() => model.value.department_id, (newDeptId) => {
    if (!model.value.classroom_id) return;
    const target = Number(newDeptId);
    const stillValid = props.classrooms.some(
        c => c.id === model.value.classroom_id && Number(c.department_id) === target,
    );
    if (!stillValid) {
        model.value.classroom_id = null;
    }
});

const isActive = computed({
    get: () => model.value.is_active,
    set: (val: boolean) => { model.value.is_active = val; },
});

// reka-ui's <SelectItem> forbids `value=""` (reserved for "clear").
// Use this sentinel for nullable selects (classroom, department).
const NONE = '__none__';

const createSelectModel = <K extends keyof InventoryFormData>(key: K) =>
    computed({
        get: () => {
            const val = model.value[key];
            if (val === null || val === undefined) return NONE;
            return String(val);
        },
        set: (value: string) => {
            if (value === null || value === undefined || value === '' || value === NONE) {
                (model.value as InventoryFormData)[key] = null as InventoryFormData[K];
                return;
            }
            // numeric ids vs string status/condition
            (model.value as InventoryFormData)[key] = (
                typeof value === 'string' && /^\d+$/.test(value) ? Number(value) : value
            ) as InventoryFormData[K];
        },
    });

const equipmentModel = createSelectModel('equipment_id');
const statusModel = createSelectModel('status');
const conditionModel = createSelectModel('condition');
const classroomModel = createSelectModel('classroom_id');
const departmentModel = createSelectModel('department_id');

// Asset tag is generated server-side (see InventoryTagGenerator) so the prefix
// rules and uniqueness check live in one place. The form fetches a fresh tag
// when the equipment changes or the user clicks the refresh button. Display
// Name wins as the prefix source when set; falls back to the equipment name
// (resolved by the backend from equipment_id).
const regenerateAssetTag = async () => {
    const source = (model.value.name ?? '').trim();
    try {
        const { data } = await axios.get('/dashboard/inventories/generate-tag', {
            params: {
                source: source || undefined,
                equipment_id: model.value.equipment_id ?? undefined,
            },
        });
        if (data?.asset_tag) {
            model.value.asset_tag = data.asset_tag;
        }
    } catch {
        // Silently ignore — user can retry via the refresh button.
    }
};

if (props.mode === 'create') {
    watch(() => model.value.equipment_id, () => {
        regenerateAssetTag();
    });
}
</script>

<template>
    <div class="space-y-10 py-2">
        <!-- Display Name (top, full width) -->
        <div class="space-y-2 px-1">
            <Label for="name" class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Display Name') }}</Label>
            <Input id="name" v-model="model.name" :placeholder="__('Leave blank to use equipment name')" class="text-base focus-visible:ring-primary/40 border-muted-foreground/20 bg-background" />
        </div>

        <!-- Section 1: Identification -->
        <div class="space-y-6">
            <div class="flex items-center gap-2 px-1 pb-2 border-b border-dashed border-muted-foreground/20">
                <Package class="h-5 w-5 text-primary" />
                <h4 class="text-xs font-bold uppercase tracking-widest text-foreground/90">{{ __('Asset Identification') }}</h4>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 px-1">
                <div class="space-y-2">
                    <Label for="asset_tag" class="text-[11px] font-bold uppercase text-muted-foreground/70">
                        {{ __('Asset Tag') }} <span class="text-destructive">*</span>
                    </Label>
                    <div class="relative">
                        <Tag class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/30" />
                        <Input
                            id="asset_tag"
                            v-model="model.asset_tag"
                            :placeholder="mode === 'create' ? __('Auto-generated') : __('e.g. PRJ-001')"
                            :readonly="mode === 'create'"
                            :class="['focus-visible:ring-primary/40 border-muted-foreground/20 bg-background', mode === 'create' ? 'pl-9 pr-10 cursor-default' : 'pl-9']"
                        />
                        <button
                            v-if="mode === 'create'"
                            type="button"
                            @click="regenerateAssetTag"
                            :title="__('Regenerate')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded text-muted-foreground/60 hover:text-foreground hover:bg-muted transition-colors"
                        >
                            <RefreshCw class="h-4 w-4" />
                        </button>
                    </div>
                    <p v-if="model.errors.asset_tag" class="text-[11px] font-medium text-destructive">{{ model.errors.asset_tag }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="serial_number" class="text-[11px] font-bold uppercase text-muted-foreground/70">
                        {{ __('Serial Number') }}
                    </Label>
                    <div class="relative">
                        <Info class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/30" />
                        <Input id="serial_number" v-model="model.serial_number" :placeholder="__('Serial/Part number')" class="pl-9 focus-visible:ring-primary/40 border-muted-foreground/20 bg-background" />
                    </div>
                </div>


                <div class="space-y-2">
                    <Label class="text-[11px] font-bold uppercase text-muted-foreground/70">
                        {{ __('Equipment Type') }} <span class="text-destructive">*</span>
                    </Label>
                    <Select v-model="equipmentModel">
                        <SelectTrigger class="focus:ring-primary/40 border-muted-foreground/20 bg-background">
                            <SelectValue :placeholder="__('Select equipment')" />
                        </SelectTrigger>
                        <SelectContent class="z-[9999]">
                            <SelectItem v-for="e in equipment" :key="e.id" :value="String(e.id)">
                                {{ e.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="model.errors.equipment_id" class="text-[11px] font-medium text-destructive">{{ model.errors.equipment_id }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Status') }} <span class="text-destructive">*</span></Label>
                        <Select v-model="statusModel">
                            <SelectTrigger class="focus:ring-primary/40 border-muted-foreground/20 bg-background">
                                <SelectValue :placeholder="__('Status')" />
                            </SelectTrigger>
                            <SelectContent class="z-[9999]">
                                <SelectItem v-for="(label, value) in statuses" :key="value" :value="value">
                                    {{ __(label) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Condition') }} <span class="text-destructive">*</span></Label>
                        <Select v-model="conditionModel">
                            <SelectTrigger class="focus:ring-primary/40 border-muted-foreground/20 bg-background">
                                <SelectValue :placeholder="__('Condition')" />
                            </SelectTrigger>
                            <SelectContent class="z-[9999]">
                                <SelectItem v-for="(label, value) in conditions" :key="value" :value="value">
                                    {{ __(label) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Location -->
        <div class="space-y-6">
            <div class="flex items-center gap-2 px-1 pb-2 border-b border-dashed border-muted-foreground/20">
                <MapPin class="h-5 w-5 text-primary" />
                <h4 class="text-xs font-bold uppercase tracking-widest text-foreground/90">{{ __('Deployment & Assignment') }}</h4>
            </div>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 px-1">
                <div class="space-y-2">
                    <Label class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Department') }}</Label>
                    <Select v-model="departmentModel">
                        <SelectTrigger class="focus:ring-primary/40 border-muted-foreground/20 bg-background">
                            <SelectValue :placeholder="__('Select department')" />
                        </SelectTrigger>
                        <SelectContent class="z-[9999]">
                            <SelectItem :value="NONE">{{ __('None') }}</SelectItem>
                            <SelectItem v-for="d in departments" :key="d.id" :value="String(d.id)">
                                {{ d.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="space-y-2">
                    <Label class="flex items-center gap-2 text-[11px] font-bold uppercase text-muted-foreground/70">
                        <span>{{ __('Classroom') }}</span>
                        <span v-if="model.department_id" class="font-normal normal-case text-[10px] text-muted-foreground/60">
                            ({{ filteredClassrooms.length }} {{ __('available') }})
                        </span>
                    </Label>
                    <Select v-model="classroomModel" :disabled="!model.department_id">
                        <SelectTrigger class="focus:ring-primary/40 border-muted-foreground/20 bg-background">
                            <SelectValue :placeholder="model.department_id ? __('Select classroom') : __('Pick a department first')" />
                        </SelectTrigger>
                        <SelectContent class="z-[9999]">
                            <SelectItem v-for="c in filteredClassrooms" :key="c.id" :value="String(c.id)">
                                {{ c.name }}
                            </SelectItem>
                            <div v-if="model.department_id && filteredClassrooms.length === 0" class="px-2 py-1.5 text-xs text-muted-foreground">
                                {{ __('No classrooms in this department') }}
                            </div>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>

        <!-- Section 3: Acquisition -->
        <div class="space-y-6">
            <div class="flex items-center gap-2 px-1 pb-2 border-b border-dashed border-muted-foreground/20">
                <Calendar class="h-5 w-5 text-primary" />
                <h4 class="text-xs font-bold uppercase tracking-widest text-foreground/90">{{ __('Acquisition & Warranty') }}</h4>
            </div>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3 px-1">
                <div class="space-y-2">
                    <Label for="purchased_at" class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Purchase Date') }}</Label>
                    <div class="relative">
                        <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/20" />
                        <Input id="purchased_at" type="date" v-model="model.purchased_at" class="pl-9 focus-visible:ring-primary/40 border-muted-foreground/20 bg-background" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="cost" class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Unit Cost') }}</Label>
                    <div class="relative">
                        <DollarSign class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/20" />
                        <Input
                            id="cost"
                            type="number"
                            step="0.01"
                            min="0"
                            :model-value="model.cost ?? ''"
                            @update:model-value="(v) => model.cost = v === '' ? null : Number(v)"
                            class="pl-9 focus-visible:ring-primary/40 border-muted-foreground/20 bg-background"
                        />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="vendor" class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Supplier / Vendor') }}</Label>
                    <div class="relative">
                        <Store class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/20" />
                        <Input id="vendor" v-model="model.vendor" :placeholder="__('Supplier name')" class="pl-9 focus-visible:ring-primary/40 border-muted-foreground/20 bg-background" />
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="warranty_until" class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Warranty Expiry') }}</Label>
                    <div class="relative">
                        <ShieldCheck class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/20" />
                        <Input id="warranty_until" type="date" v-model="model.warranty_until" class="pl-9 focus-visible:ring-primary/40 border-muted-foreground/20 bg-background" />
                    </div>
                    <p v-if="model.errors.warranty_until" class="text-[11px] font-medium text-destructive">{{ model.errors.warranty_until }}</p>
                </div>

                <div class="flex items-center gap-3 pt-6 md:pt-8">
                    <Switch id="is_active" v-model:checked="isActive" />
                    <Label for="is_active" class="text-[10px] font-bold uppercase tracking-widest cursor-pointer">{{ __('Active') }}</Label>
                </div>
            </div>
        </div>

        <!-- Section 4: Documentation -->
        <div class="space-y-6">
            <div class="flex items-center gap-2 px-1 pb-2 border-b border-dashed border-muted-foreground/20">
                <FileImage class="h-5 w-5 text-primary" />
                <h4 class="text-xs font-bold uppercase tracking-widest text-foreground/90">{{ __('Notes & Attachments') }}</h4>
            </div>
            
            <div class="space-y-6 px-1">
                <div class="space-y-2">
                    <Label for="notes" class="text-[11px] font-bold uppercase text-muted-foreground/70">{{ __('Internal Remarks') }}</Label>
                    <TiptapEditor id="notes" v-model="model.notes" :placeholder="__('Add any specific details...')" class="min-h-[100px] border border-muted-foreground/20 rounded-lg bg-background p-1" />
                </div>

                <div class="space-y-3 pt-2">
                    <ImageUpload
                        v-model="model.images"
                        :label="__('Asset Gallery & Documents')"
                        :max-files="10"
                        :max-size="5"
                        :error="model.errors['images'] as string | undefined"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
