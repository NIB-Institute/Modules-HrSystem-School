<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { StatsCard } from '@/components/shared';
import {
    ChevronLeft,
    Pencil,
    Trash2,
    DoorOpen,
    Building,
    Users,
    BookOpen,
    MapPin,
    Monitor,
    Snowflake,
    ClipboardList,
    CheckCircle,
    XCircle,
    Package,
    QrCode,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { ClassroomShowProps } from '@school/types';

const props = defineProps<ClassroomShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Classrooms', href: '/dashboard/classrooms' },
    { title: props.classroom.name, href: `/dashboard/classrooms/${props.classroom.uuid}` },
];

const handleEdit = () => {
    router.visit(`/dashboard/classrooms/${props.classroom.uuid}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/classrooms/${props.classroom.uuid}/delete`);
};

const handleQrCode = () => {
    router.visit(`/dashboard/classrooms/${props.classroom.uuid}/qr-code`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="classroom.name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/classrooms" class="text-muted-foreground hover:text-foreground">
                        <ChevronLeft class="h-5 w-5" />
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10">
                            <DoorOpen class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-semibold">{{ classroom.name }}</h1>
                                <Badge :variant="classroom.status ? 'default' : 'secondary'">
                                    {{ classroom.status ? 'Active' : 'Inactive' }}
                                </Badge>
                                <Badge v-if="classroom.is_available" variant="outline" class="border-green-500 text-green-500">
                                    Available
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Badge variant="outline">{{ classroom.type_label }}</Badge>
                                <span v-if="classroom.code">Code: {{ classroom.code }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="handleQrCode">
                        <QrCode class="mr-2 h-4 w-4" />
                        Generate QR
                    </Button>
                    <Button variant="outline" @click="handleEdit">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-5">
                <StatsCard
                    title="Capacity"
                    :value="stats.capacity"
                    :icon="Users"
                />
                <StatsCard
                    title="Courses"
                    :value="stats.courses_count"
                    :icon="BookOpen"
                />
                <StatsCard
                    title="Equipment"
                    :value="stats.equipment_count"
                    :icon="Package"
                />
                <StatsCard
                    title="Floor"
                    :value="classroom.floor || 'N/A'"
                    :icon="Building"
                />
                <StatsCard
                    title="Building"
                    :value="classroom.building || 'N/A'"
                    :icon="MapPin"
                />
            </div>

            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Description -->
                    <Card v-if="classroom.description">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">About</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ classroom.description }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Location Information -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Location Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-if="classroom.school_name" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Building class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">School</p>
                                        <p class="text-sm font-medium">{{ classroom.school_name }}</p>
                                    </div>
                                </div>
                                <div v-if="classroom.building" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <MapPin class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Building</p>
                                        <p class="text-sm font-medium">{{ classroom.building }}</p>
                                    </div>
                                </div>
                                <div v-if="classroom.floor !== null" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Building class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Floor</p>
                                        <p class="text-sm font-medium">Floor {{ classroom.floor }}</p>
                                    </div>
                                </div>
                                <div v-if="classroom.full_location" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <MapPin class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Full Location</p>
                                        <p class="text-sm font-medium">{{ classroom.full_location }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Equipment Items -->
                    <Card v-if="classroom.equipment_items && classroom.equipment_items.length > 0">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base flex items-center gap-2">
                                <Package class="h-4 w-4" />
                                Equipment ({{ classroom.equipment_items.length }})
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div
                                    v-for="item in classroom.equipment_items"
                                    :key="item.id"
                                    class="flex items-center justify-between rounded-lg border p-3"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-muted">
                                            <Package class="h-4 w-4 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">{{ item.name }}</p>
                                            <p v-if="item.notes" class="text-xs text-muted-foreground">{{ item.notes }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <Badge variant="secondary">Qty: {{ item.quantity }}</Badge>
                                        <p v-if="item.value" class="text-xs text-muted-foreground mt-1">{{ item.value }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Legacy Equipment (string array) -->
                    <Card v-if="classroom.equipment && classroom.equipment.length > 0">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Additional Equipment</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-wrap gap-2">
                                <Badge v-for="item in classroom.equipment" :key="item" variant="secondary">
                                    {{ item.replace('_', ' ').replace(/\b\w/g, (l: string) => l.toUpperCase()) }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Amenities -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Amenities</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center gap-3">
                                <Monitor class="h-4 w-4 text-muted-foreground" />
                                <div class="flex-1">
                                    <p class="text-sm">Projector</p>
                                </div>
                                <CheckCircle v-if="classroom.has_projector" class="h-4 w-4 text-green-500" />
                                <XCircle v-else class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="flex items-center gap-3">
                                <ClipboardList class="h-4 w-4 text-muted-foreground" />
                                <div class="flex-1">
                                    <p class="text-sm">Whiteboard</p>
                                </div>
                                <CheckCircle v-if="classroom.has_whiteboard" class="h-4 w-4 text-green-500" />
                                <XCircle v-else class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="flex items-center gap-3">
                                <Snowflake class="h-4 w-4 text-muted-foreground" />
                                <div class="flex-1">
                                    <p class="text-sm">Air Conditioning</p>
                                </div>
                                <CheckCircle v-if="classroom.has_ac" class="h-4 w-4 text-green-500" />
                                <XCircle v-else class="h-4 w-4 text-muted-foreground" />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Details -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center gap-3">
                                <Users class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Capacity</p>
                                    <p class="text-sm font-medium">{{ classroom.capacity }} seats</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <DoorOpen class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Type</p>
                                    <p class="text-sm font-medium">{{ classroom.type_label }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <BookOpen class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Courses</p>
                                    <p class="text-sm font-medium">{{ stats.courses_count }} course(s)</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
