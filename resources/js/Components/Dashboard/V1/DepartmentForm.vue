<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Slider } from '@/components/ui/slider';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import TiptapEditor from '@/components/TiptapEditor.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Building2, MapPin, Shield, Plus, Unlink, Navigation, Globe } from 'lucide-vue-next';
import type { InertiaForm } from '@inertiajs/vue3';
import type { DepartmentFormData, SchoolOption } from '@school/types';

interface LocationOption {
    value: string;
    label: string;
    type: string;
    city: string | null;
    geofence_type: string;
    geofence_radius: number;
}

interface Props {
    form: InertiaForm<DepartmentFormData>;
    mode?: 'create' | 'edit';
    schools: SchoolOption[];
    availableLocations?: LocationOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    schools: () => [],
    availableLocations: () => [],
});

// Convert school_id to string for Select component
const schoolIdString = computed({
    get: () => props.form.school_id ? String(props.form.school_id) : '',
    set: (val: string) => {
        props.form.school_id = val;
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
    set: (val: string | number | undefined | null) => {
        props.form.established_year = val ? Number(val) : null;
    }
});

const totalStudentsValue = computed({
    get: () => props.form.total_students ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.total_students = val ? Number(val) : null;
    }
});

const totalStaffValue = computed({
    get: () => props.form.total_staff ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.total_staff = val ? Number(val) : null;
    }
});

// Computed property for TiptapEditor v-model
const editorContent = computed({
    get: () => props.form.description ?? '',
    set: (val: string) => {
        props.form.description = val;
    },
});

// Location selection computed property
const locationIdString = computed({
    get: () => props.form.location_id ? String(props.form.location_id) : '',
    set: (val: string) => {
        props.form.location_id = val ? Number(val) : null;
    },
});

// Get selected location details
const selectedLocation = computed(() => {
    if (!props.form.location_id) return null;
    return props.availableLocations.find(loc => loc.value === String(props.form.location_id));
});

// Check if location is linked
const hasLinkedLocation = computed(() => {
    return props.form.location_id !== null && props.form.location_id !== undefined;
});

// Unlink location
const unlinkLocation = () => {
    props.form.location_id = null;
};

// Geofence mode: 'location' (linked) or 'manual' (direct coordinates)
const geofenceMode = ref<'location' | 'manual'>(
    props.form.location_id ? 'location' : (props.form.latitude ? 'manual' : 'location')
);

// Latitude computed property
const latitudeValue = computed({
    get: () => props.form.latitude ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.latitude = val ? Number(val) : null;
    }
});

// Longitude computed property
const longitudeValue = computed({
    get: () => props.form.longitude ?? undefined,
    set: (val: string | number | undefined | null) => {
        props.form.longitude = val ? Number(val) : null;
    }
});

// Geofence radius as array for Slider component
const geofenceRadiusArray = computed({
    get: () => [props.form.geofence_radius ?? 100],
    set: (val: number[]) => {
        props.form.geofence_radius = val[0];
    }
});

// Enforce geofence computed
const enforceGeofenceValue = computed({
    get: () => props.form.enforce_geofence ?? false,
    set: (val: boolean) => {
        props.form.enforce_geofence = val;
    }
});

// Handle mode change
const handleModeChange = (mode: string) => {
    geofenceMode.value = mode as 'location' | 'manual';
    if (mode === 'manual') {
        // Clear location_id when switching to manual
        props.form.location_id = null;
    } else {
        // Clear manual coordinates when switching to location
        props.form.latitude = null;
        props.form.longitude = null;
    }
};

// Get current location
const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                props.form.latitude = position.coords.latitude;
                props.form.longitude = position.coords.longitude;
            },
            (error) => {
                console.error('Error getting location:', error);
            }
        );
    }
};

// Check if manual geofence is configured
const hasManualGeofence = computed(() => {
    return props.form.latitude !== null && props.form.longitude !== null;
});

// Format geofence type for display
const formatGeofenceType = (type: string): string => {
    const types: Record<string, string> = {
        circle: 'Circle (Radius)',
        polygon: 'Polygon (Custom Shape)',
        dynamic: 'Dynamic (Moving)',
    };
    return types[type] || type;
};

// Format location type for display
const formatLocationType = (type: string): string => {
    const types: Record<string, string> = {
        office: 'Office',
        branch: 'Branch',
        site: 'Site',
        remote: 'Remote',
        other: 'Other',
    };
    return types[type] || type;
};
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column: Main Form Fields -->
        <div class="space-y-6 lg:col-span-2">
            <!-- School Selection Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Assign to School</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <Label for="school_id" class="sr-only">School</Label>
                        <Select v-model="schoolIdString">
                            <SelectTrigger
                                class="h-12 w-full text-base"
                                :class="{ 'border-destructive': props.form.errors.school_id }"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-primary/10">
                                        <Building2 class="h-4 w-4 text-primary" />
                                    </div>
                                    <SelectValue placeholder="Select a school to assign this department" />
                                </div>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="school in props.schools"
                                    :key="school.value"
                                    :value="String(school.value)"
                                >
                                    <div class="flex items-center gap-2">
                                        <Building2 class="h-4 w-4 text-muted-foreground" />
                                        {{ school.label }}
                                    </div>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="props.form.errors.school_id" class="text-xs text-destructive">
                            {{ props.form.errors.school_id }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Basic Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Basic Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Name -->
                        <div class="space-y-2 sm:col-span-2">
                            <Label for="name">Department Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="name"
                                v-model="props.form.name"
                                placeholder="e.g., Computer Science"
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
                                placeholder="e.g., CS-001"
                                :class="{ 'border-destructive': props.form.errors.code }"
                            />
                            <p v-if="props.form.errors.code" class="text-xs text-destructive">
                                {{ props.form.errors.code }}
                            </p>
                        </div>

                        <!-- Head of Department -->
                        <div class="space-y-2">
                            <Label for="head_of_department">Head of Department</Label>
                            <Input
                                id="head_of_department"
                                v-model="props.form.head_of_department"
                                placeholder="e.g., Dr. John Smith"
                            />
                            <p v-if="props.form.errors.head_of_department" class="text-xs text-destructive">
                                {{ props.form.errors.head_of_department }}
                            </p>
                        </div>

                        <!-- Office Location -->
                        <div class="space-y-2">
                            <Label for="office_location">Office Location</Label>
                            <Input
                                id="office_location"
                                v-model="props.form.office_location"
                                placeholder="e.g., Building A, Room 101"
                            />
                            <p v-if="props.form.errors.office_location" class="text-xs text-destructive">
                                {{ props.form.errors.office_location }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <TiptapEditor
                            v-model="editorContent"
                            placeholder="Enter department description..."
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
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="props.form.email"
                                placeholder="department@university.edu"
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
                    </div>
                </CardContent>
            </Card>

            <!-- Geofence Configuration Card -->
            <Card>
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Shield class="h-4 w-4" />
                                Geofence Configuration
                            </CardTitle>
                            <CardDescription class="mt-1">
                                Set up location-based attendance verification
                            </CardDescription>
                        </div>
                        <Badge v-if="hasLinkedLocation || hasManualGeofence" variant="default" class="gap-1">
                            <Shield class="h-3 w-3" />
                            Configured
                        </Badge>
                        <Badge v-else variant="secondary">Not Configured</Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Tabs :default-value="geofenceMode" @update:model-value="handleModeChange">
                        <TabsList class="grid w-full grid-cols-2">
                            <TabsTrigger value="location">
                                <MapPin class="h-4 w-4 mr-2" />
                                Link Location
                            </TabsTrigger>
                            <TabsTrigger value="manual">
                                <Navigation class="h-4 w-4 mr-2" />
                                Manual Setup
                            </TabsTrigger>
                        </TabsList>

                        <!-- Location Linking Tab -->
                        <TabsContent value="location" class="space-y-4 mt-4">
                            <div class="space-y-2">
                                <Label>Select Location</Label>
                                <Select v-model="locationIdString">
                                    <SelectTrigger :class="{ 'border-primary': hasLinkedLocation }">
                                        <div class="flex items-center gap-2">
                                            <MapPin :class="['h-4 w-4', hasLinkedLocation ? 'text-primary' : 'text-muted-foreground']" />
                                            <SelectValue placeholder="Choose a scan location..." />
                                        </div>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="location in props.availableLocations"
                                            :key="location.value"
                                            :value="location.value"
                                        >
                                            <div class="flex flex-col">
                                                <span>{{ location.label }}</span>
                                                <span class="text-xs text-muted-foreground">
                                                    {{ formatLocationType(location.type) }}
                                                    <template v-if="location.city"> · {{ location.city }}</template>
                                                    · {{ formatGeofenceType(location.geofence_type) }}
                                                    <template v-if="location.geofence_type === 'circle'">
                                                        ({{ location.geofence_radius }}m)
                                                    </template>
                                                </span>
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p class="text-xs text-muted-foreground">
                                    Locations are managed at <a href="/dashboard/locations" target="_blank" class="text-primary hover:underline">Dashboard → Locations</a>
                                </p>
                            </div>

                            <!-- Selected Location Details -->
                            <div v-if="selectedLocation" class="rounded-lg border bg-muted/30 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-sm">{{ selectedLocation.label }}</h4>
                                    <Button variant="ghost" size="sm" @click="unlinkLocation" class="h-8 text-xs text-muted-foreground hover:text-destructive">
                                        <Unlink class="h-3 w-3 mr-1" />
                                        Unlink
                                    </Button>
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-xs text-muted-foreground">Type</p>
                                        <p>{{ formatLocationType(selectedLocation.type) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Geofence</p>
                                        <p>{{ formatGeofenceType(selectedLocation.geofence_type) }}</p>
                                    </div>
                                    <div v-if="selectedLocation.geofence_type === 'circle'">
                                        <p class="text-xs text-muted-foreground">Radius</p>
                                        <p>{{ selectedLocation.geofence_radius }}m</p>
                                    </div>
                                    <div v-if="selectedLocation.city">
                                        <p class="text-xs text-muted-foreground">City</p>
                                        <p>{{ selectedLocation.city }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- No Location State -->
                            <div v-else class="rounded-lg border border-dashed bg-muted/20 p-6 text-center">
                                <MapPin class="h-8 w-8 mx-auto text-muted-foreground mb-2" />
                                <p class="text-sm text-muted-foreground mb-3">
                                    No location linked. Select an existing location above or create a new one.
                                </p>
                                <Button variant="outline" size="sm" as-child>
                                    <Link href="/dashboard/locations/create" target="_blank">
                                        <Plus class="h-4 w-4 mr-1" />
                                        Create New Location
                                    </Link>
                                </Button>
                            </div>
                        </TabsContent>

                        <!-- Manual Setup Tab -->
                        <TabsContent value="manual" class="space-y-4 mt-4">
                            <!-- Coordinates -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="latitude">Latitude</Label>
                                    <Input
                                        id="latitude"
                                        type="number"
                                        step="0.00000001"
                                        v-model.number="latitudeValue"
                                        placeholder="e.g., 11.5564"
                                        :class="{ 'border-destructive': props.form.errors.latitude }"
                                    />
                                    <p v-if="props.form.errors.latitude" class="text-xs text-destructive">
                                        {{ props.form.errors.latitude }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <Label for="longitude">Longitude</Label>
                                    <Input
                                        id="longitude"
                                        type="number"
                                        step="0.00000001"
                                        v-model.number="longitudeValue"
                                        placeholder="e.g., 104.9282"
                                        :class="{ 'border-destructive': props.form.errors.longitude }"
                                    />
                                    <p v-if="props.form.errors.longitude" class="text-xs text-destructive">
                                        {{ props.form.errors.longitude }}
                                    </p>
                                </div>
                            </div>

                            <!-- Get Current Location Button -->
                            <Button type="button" variant="outline" size="sm" @click="getCurrentLocation" class="w-full">
                                <Globe class="h-4 w-4 mr-2" />
                                Use My Current Location
                            </Button>

                            <!-- Geofence Radius -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Geofence Radius</Label>
                                    <span class="text-sm font-medium">{{ props.form.geofence_radius ?? 100 }}m</span>
                                </div>
                                <Slider
                                    v-model="geofenceRadiusArray"
                                    :min="10"
                                    :max="1000"
                                    :step="10"
                                    class="w-full"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Employees must be within this radius to check in/out
                                </p>
                            </div>

                            <!-- Enforce Geofence Toggle -->
                            <div class="flex items-center justify-between rounded-lg border p-4">
                                <div class="space-y-0.5">
                                    <Label class="text-sm font-medium">Enforce Geofence</Label>
                                    <p class="text-xs text-muted-foreground">
                                        Block attendance if employee is outside the geofence
                                    </p>
                                </div>
                                <Switch v-model="enforceGeofenceValue" />
                            </div>

                            <!-- Timezone -->
                            <div class="space-y-2">
                                <Label for="timezone">Timezone</Label>
                                <Select v-model="props.form.timezone">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select timezone..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Asia/Phnom_Penh">Asia/Phnom_Penh (UTC+7)</SelectItem>
                                        <SelectItem value="Asia/Bangkok">Asia/Bangkok (UTC+7)</SelectItem>
                                        <SelectItem value="Asia/Ho_Chi_Minh">Asia/Ho_Chi_Minh (UTC+7)</SelectItem>
                                        <SelectItem value="Asia/Singapore">Asia/Singapore (UTC+8)</SelectItem>
                                        <SelectItem value="Asia/Tokyo">Asia/Tokyo (UTC+9)</SelectItem>
                                        <SelectItem value="UTC">UTC (UTC+0)</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Manual Geofence Status -->
                            <div v-if="hasManualGeofence" class="rounded-lg border bg-green-50 dark:bg-green-950 p-4">
                                <div class="flex items-center gap-2 text-green-700 dark:text-green-400">
                                    <Shield class="h-4 w-4" />
                                    <span class="text-sm font-medium">Geofence Configured</span>
                                </div>
                                <p class="text-xs text-green-600 dark:text-green-500 mt-1">
                                    Location: {{ props.form.latitude?.toFixed(6) }}, {{ props.form.longitude?.toFixed(6) }}
                                    · Radius: {{ props.form.geofence_radius }}m
                                    · Enforce: {{ props.form.enforce_geofence ? 'Yes' : 'No' }}
                                </p>
                            </div>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>
        </div>

        <!-- Right Column: Statistics & Status -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Statistics Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Statistics</CardTitle>
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
                            <p class="text-sm font-medium">Department Status</p>
                            <p class="text-xs text-muted-foreground">
                                {{ isActive ? 'Department is active' : 'Department is inactive' }}
                            </p>
                        </div>
                        <Switch v-model="isActive" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
