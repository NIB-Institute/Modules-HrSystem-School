<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ImageUpload } from '@/components/shared';
import type { InertiaForm } from '@inertiajs/vue3';
import type { SchoolFormData, SchoolType } from '@school/types';

interface Props {
    form: InertiaForm<SchoolFormData>;
    mode?: 'create' | 'edit';
    types: Record<SchoolType, string>;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value: value as SchoolType,
        label,
    }));
});

// Convert type to string for Select component
const selectedType = computed({
    get: () => props.form.type,
    set: (val: string | undefined) => {
        props.form.type = (val as SchoolType) || 'school';
    },
});

// Convert status to boolean
const isActive = computed({
    get: () => props.form.status,
    set: (val: boolean) => {
        props.form.status = val;
    },
});

// Nullable numbers workaround
const establishedYearValue = computed({
    get: () => props.form.established_year ?? undefined,
    set: (val: number | undefined) => {
        props.form.established_year = val ?? null;
    }
});

const currencyValue = computed({
    get: () => props.form.currency ?? undefined,
    set: (val: number | undefined) => {
        props.form.currency = val ?? null;
    }
});

const tuitionFeeBaseValue = computed({
    get: () => props.form.tuition_fee_base ?? undefined,
    set: (val: number | undefined) => {
        props.form.tuition_fee_base = val ?? null;
    }
});

const totalStudentsValue = computed({
    get: () => props.form.total_students ?? 0,
    set: (val: number | undefined) => {
        props.form.total_students = val ?? 0;
    }
});

const totalStaffValue = computed({
    get: () => props.form.total_staff ?? 0,
    set: (val: number | undefined) => {
        props.form.total_staff = val ?? 0;
    }
});

// Logo - convert string to array for ImageUpload component
const logoImages = computed({
    get: () => props.form.logo ? [props.form.logo] : [],
    set: (val: string[]) => {
        props.form.logo = val.length > 0 ? val[0] : '';
    }
});
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column: Form Fields -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Basic Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Name -->
                        <div class="space-y-2 sm:col-span-2">
                            <Label for="name">Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="name"
                                v-model="props.form.name"
                                placeholder="Enter school name"
                                :class="{ 'border-destructive': props.form.errors.name }"
                            />
                            <p v-if="props.form.errors.name" class="text-xs text-destructive">
                                {{ props.form.errors.name }}
                            </p>
                        </div>

                        <!-- Code -->
                        <div class="space-y-2">
                            <Label for="code">Code</Label>
                            <Input
                                id="code"
                                v-model="props.form.code"
                                placeholder="e.g., SCH001"
                                :class="{ 'border-destructive': props.form.errors.code }"
                            />
                            <p v-if="props.form.errors.code" class="text-xs text-destructive">
                                {{ props.form.errors.code }}
                            </p>
                        </div>

                        <!-- Type -->
                        <div class="space-y-2">
                            <Label for="type">Type <span class="text-destructive">*</span></Label>
                            <Select v-model="selectedType">
                                <SelectTrigger :class="{ 'border-destructive': props.form.errors.type }">
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="type in typeOptions" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="props.form.errors.type" class="text-xs text-destructive">
                                {{ props.form.errors.type }}
                            </p>
                        </div>

                        <!-- School Level -->
                        <div class="space-y-2">
                            <Label for="school_lavel">School Level</Label>
                            <Input
                                id="school_lavel"
                                v-model="props.form.school_lavel"
                                placeholder="e.g., Primary, Secondary"
                            />
                            <p v-if="props.form.errors.school_lavel" class="text-xs text-destructive">
                                {{ props.form.errors.school_lavel }}
                            </p>
                        </div>

                        <!-- Education System -->
                        <div class="space-y-2">
                            <Label for="education_system">Education System</Label>
                            <Input
                                id="education_system"
                                v-model="props.form.education_system"
                                placeholder="e.g., National, International"
                            />
                            <p v-if="props.form.errors.education_system" class="text-xs text-destructive">
                                {{ props.form.errors.education_system }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <TiptapEditor
                            v-model="props.form.description"
                            placeholder="Enter school description..."
                            min-height="120px"
                            max-height="250px"
                        />
                        <p v-if="props.form.errors.description" class="text-xs text-destructive">
                            {{ props.form.errors.description }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Contact Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Contact Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="props.form.email"
                                placeholder="school@example.com"
                            />
                            <p v-if="props.form.errors.email" class="text-xs text-destructive">
                                {{ props.form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div class="space-y-2">
                            <Label for="phone">Phone</Label>
                            <Input
                                id="phone"
                                v-model="props.form.phone"
                                placeholder="+1 234 567 8900"
                            />
                            <p v-if="props.form.errors.phone" class="text-xs text-destructive">
                                {{ props.form.errors.phone }}
                            </p>
                        </div>

                        <!-- Website -->
                        <div class="space-y-2">
                            <Label for="website">Website</Label>
                            <Input
                                id="website"
                                v-model="props.form.website"
                                placeholder="https://example.com"
                            />
                            <p v-if="props.form.errors.website" class="text-xs text-destructive">
                                {{ props.form.errors.website }}
                            </p>
                        </div>

                        <!-- City -->
                        <div class="space-y-2">
                            <Label for="city">City</Label>
                            <Input
                                id="city"
                                v-model="props.form.city"
                                placeholder="City name"
                            />
                            <p v-if="props.form.errors.city" class="text-xs text-destructive">
                                {{ props.form.errors.city }}
                            </p>
                        </div>

                        <!-- Country -->
                        <div class="space-y-2">
                            <Label for="country">Country</Label>
                            <Input
                                id="country"
                                v-model="props.form.country"
                                placeholder="Country name"
                            />
                            <p v-if="props.form.errors.country" class="text-xs text-destructive">
                                {{ props.form.errors.country }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div class="space-y-2 lg:col-span-1">
                            <Label for="address">Address</Label>
                            <Input
                                id="address"
                                v-model="props.form.address"
                                placeholder="Street address"
                            />
                            <p v-if="props.form.errors.address" class="text-xs text-destructive">
                                {{ props.form.errors.address }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Financial & Statistics Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Financial & Statistics</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Currency -->
                        <div class="space-y-2">
                            <Label for="currency">Currency</Label>
                            <Input
                                id="currency"
                                type="number"
                                v-model.number="currencyValue"
                                placeholder="Currency ID"
                            />
                            <p v-if="props.form.errors.currency" class="text-xs text-destructive">
                                {{ props.form.errors.currency }}
                            </p>
                        </div>

                        <!-- Tuition Fee Base -->
                        <div class="space-y-2">
                            <Label for="tuition_fee_base">Tuition Fee Base</Label>
                            <Input
                                id="tuition_fee_base"
                                type="number"
                                step="0.01"
                                v-model.number="tuitionFeeBaseValue"
                                placeholder="0.00"
                            />
                            <p v-if="props.form.errors.tuition_fee_base" class="text-xs text-destructive">
                                {{ props.form.errors.tuition_fee_base }}
                            </p>
                        </div>

                        <!-- Total Students -->
                        <div class="space-y-2">
                            <Label for="total_students">Total Students</Label>
                            <Input
                                id="total_students"
                                type="number"
                                v-model.number="totalStudentsValue"
                                placeholder="0"
                            />
                            <p v-if="props.form.errors.total_students" class="text-xs text-destructive">
                                {{ props.form.errors.total_students }}
                            </p>
                        </div>

                        <!-- Total Staff -->
                        <div class="space-y-2">
                            <Label for="total_staff">Total Staff</Label>
                            <Input
                                id="total_staff"
                                type="number"
                                v-model.number="totalStaffValue"
                                placeholder="0"
                            />
                            <p v-if="props.form.errors.total_staff" class="text-xs text-destructive">
                                {{ props.form.errors.total_staff }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Right Column: Logo, Status & Accreditation -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Logo Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">School Logo</CardTitle>
                </CardHeader>
                <CardContent>
                    <ImageUpload
                        v-model="logoImages"
                        label=""
                        :multiple="false"
                        :max-files="1"
                        :max-size="5"
                        :error="props.form.errors.logo"
                    />
                </CardContent>
            </Card>

            <!-- Status Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Status</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">School Status</p>
                            <p class="text-xs text-muted-foreground">
                                {{ isActive ? 'School is active' : 'School is inactive' }}
                            </p>
                        </div>
                        <Switch v-model="isActive" />
                    </div>
                </CardContent>
            </Card>

            <!-- Accreditation Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Accreditation</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Established Year -->
                    <div class="space-y-2">
                        <Label for="established_year">Established Year</Label>
                        <Input
                            id="established_year"
                            type="number"
                            v-model.number="establishedYearValue"
                            placeholder="e.g., 1990"
                        />
                        <p v-if="props.form.errors.established_year" class="text-xs text-destructive">
                            {{ props.form.errors.established_year }}
                        </p>
                    </div>

                    <!-- Accreditation -->
                    <div class="space-y-2">
                        <Label for="accreditation">Accreditation</Label>
                        <Input
                            id="accreditation"
                            v-model="props.form.accreditation"
                            placeholder="e.g., WASC, AdvancED"
                        />
                        <p v-if="props.form.errors.accreditation" class="text-xs text-destructive">
                            {{ props.form.errors.accreditation }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
