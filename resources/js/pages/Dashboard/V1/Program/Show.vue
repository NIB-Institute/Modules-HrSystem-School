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
    GraduationCap,
    Building2,
    BookOpen,
    Users,
    Clock,
    DollarSign,
    Award,
    User,
    FileText,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { ProgramShowProps } from '@school/types';

const props = defineProps<ProgramShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Programs', href: '/dashboard/programs' },
    { title: props.program.name, href: `/dashboard/programs/${props.program.uuid}` },
];

const handleEdit = () => {
    router.visit(`/dashboard/programs/${props.program.uuid}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/programs/${props.program.uuid}/delete`);
};

const formatCurrency = (value: number | string | null) => {
    if (!value) return '-';
    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(numValue);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="program.name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/programs" class="text-muted-foreground hover:text-foreground">
                        <ChevronLeft class="h-5 w-5" />
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10">
                            <GraduationCap class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-semibold">{{ program.name }}</h1>
                                <Badge :variant="program.status ? 'default' : 'secondary'">
                                    {{ program.status ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Badge variant="outline">{{ program.degree_level_label }}</Badge>
                                <span v-if="program.code">Code: {{ program.code }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
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
            <div class="grid gap-4 md:grid-cols-4">
                <StatsCard
                    title="Courses"
                    :value="stats.courses_count"
                    :icon="BookOpen"
                />
                <StatsCard
                    title="Current Enrollment"
                    :value="stats.current_enrollment"
                    :icon="Users"
                />
                <StatsCard
                    title="Max Students"
                    :value="stats.max_students"
                    :icon="Users"
                />
                <StatsCard
                    title="Credits Required"
                    :value="stats.credits_required"
                    :icon="Award"
                />
            </div>

            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Description -->
                    <Card v-if="program.description">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">About</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ program.description }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Admission Requirements -->
                    <Card v-if="program.admission_requirements">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Admission Requirements</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ program.admission_requirements }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Program Information -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Program Information</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-if="program.school_name" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Building2 class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">School</p>
                                        <p class="text-sm font-medium">{{ program.school_name }}</p>
                                    </div>
                                </div>
                                <div v-if="program.department_name" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <FileText class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Department</p>
                                        <p class="text-sm font-medium">{{ program.department_name }}</p>
                                    </div>
                                </div>
                                <div v-if="program.program_coordinator" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <User class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Program Coordinator</p>
                                        <p class="text-sm font-medium">{{ program.program_coordinator }}</p>
                                    </div>
                                </div>
                                <div v-if="program.tuition_fee" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <DollarSign class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">Tuition Fee</p>
                                        <p class="text-sm font-medium">{{ formatCurrency(program.tuition_fee) }}</p>
                                    </div>
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
                            <div class="flex items-center gap-3">
                                <GraduationCap class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Degree Level</p>
                                    <p class="text-sm font-medium">{{ program.degree_level_label }}</p>
                                </div>
                            </div>
                            <div v-if="program.duration_years" class="flex items-center gap-3">
                                <Clock class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Duration</p>
                                    <p class="text-sm font-medium">{{ program.duration_years }} {{ program.duration_years === 1 ? 'Year' : 'Years' }}</p>
                                </div>
                            </div>
                            <div v-if="program.credits_required" class="flex items-center gap-3">
                                <Award class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Credits Required</p>
                                    <p class="text-sm font-medium">{{ program.credits_required }}</p>
                                </div>
                            </div>
                            <div v-if="program.accreditation_status" class="flex items-center gap-3">
                                <Award class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Accreditation Status</p>
                                    <p class="text-sm font-medium">{{ program.accreditation_status }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Users class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Enrollment</p>
                                    <p class="text-sm font-medium">
                                        {{ program.current_enrollment ?? 0 }} / {{ program.max_students ?? 'Unlimited' }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
