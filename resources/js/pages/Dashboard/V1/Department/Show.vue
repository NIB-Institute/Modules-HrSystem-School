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
    Building,
    Mail,
    Phone,
    MapPin,
    Calendar,
    Users,
    BookOpen,
    GraduationCap,
    User,
    QrCode,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { DepartmentShowProps } from '@school/types';

const props = defineProps<DepartmentShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: '/dashboard/departments' },
    { title: props.department.name, href: `/dashboard/departments/${props.department.uuid}` },
];

const handleEdit = () => {
    router.visit(`/dashboard/departments/${props.department.uuid}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/departments/${props.department.uuid}/delete`);
};

const handleQrCode = () => {
    router.visit(`/dashboard/departments/${props.department.uuid}/qr-code`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="department.name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/departments" class="text-muted-foreground hover:text-foreground">
                        <ChevronLeft class="h-5 w-5" />
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10">
                            <Building class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-semibold">{{ department.name }}</h1>
                                <Badge :variant="department.status ? 'default' : 'secondary'">
                                    {{ department.status ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Badge v-if="department.school_name" variant="outline">
                                    {{ department.school_name }}
                                </Badge>
                                <span v-if="department.code">Code: {{ department.code }}</span>
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
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Programs"
                    :value="stats.programs_count"
                    :icon="BookOpen"
                />
                <StatsCard
                    title="Courses"
                    :value="stats.courses_count"
                    :icon="GraduationCap"
                />
                <StatsCard
                    title="Employees"
                    :value="stats.employees_count"
                    :icon="Users"
                />
            </div>

            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Description -->
                    <Card v-if="department.description">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">About</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ department.description }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Contact Information -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Contact Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-if="department.head_of_department" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <User class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Head of Department</p>
                                        <p class="text-sm font-medium">{{ department.head_of_department }}</p>
                                    </div>
                                </div>
                                <div v-if="department.email" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Mail class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Email</p>
                                        <a :href="`mailto:${department.email}`" class="text-sm hover:underline">
                                            {{ department.email }}
                                        </a>
                                    </div>
                                </div>
                                <div v-if="department.phone" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Phone class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Phone</p>
                                        <a :href="`tel:${department.phone}`" class="text-sm hover:underline">
                                            {{ department.phone }}
                                        </a>
                                    </div>
                                </div>
                                <div v-if="department.office_location" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <MapPin class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Office Location</p>
                                        <p class="text-sm">{{ department.office_location }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Programs List -->
                    <Card v-if="programs && programs.length > 0">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Programs ({{ programs.length }})</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div
                                    v-for="program in programs"
                                    :key="program.id"
                                    class="flex items-center justify-between rounded-lg border p-3"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10">
                                            <BookOpen class="h-4 w-4 text-primary" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">{{ program.name }}</p>
                                            <p v-if="program.code" class="text-xs text-muted-foreground">
                                                {{ program.code }}
                                            </p>
                                        </div>
                                    </div>
                                    <Badge variant="outline">
                                        {{ program.courses_count || 0 }} courses
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Details -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="department.established_year" class="flex items-center gap-3">
                                <Calendar class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Established</p>
                                    <p class="text-sm font-medium">{{ department.established_year }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Users class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Total Students</p>
                                    <p class="text-sm font-medium">{{ department.total_students || 0 }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Users class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Staff Count</p>
                                    <p class="text-sm font-medium">{{ department.staff_count || 0 }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Parent School -->
                    <Card v-if="department.school">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Parent School</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Link
                                :href="`/dashboard/schools/${department.school.uuid}`"
                                class="flex items-center gap-3 rounded-lg border p-3 hover:bg-muted/50 transition-colors"
                            >
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                    <Building class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ department.school.name }}</p>
                                    <p v-if="department.school.code" class="text-xs text-muted-foreground">
                                        {{ department.school.code }}
                                    </p>
                                </div>
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
