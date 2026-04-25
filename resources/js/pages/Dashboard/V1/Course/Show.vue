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
    BookOpen,
    Building2,
    GraduationCap,
    Users,
    Calendar,
    Clock,
    MapPin,
    User,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { CourseShowProps } from '@school/types';

const props = defineProps<CourseShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Courses', href: '/dashboard/courses' },
    { title: props.course.name, href: `/dashboard/courses/${props.course.uuid}` },
];

const handleEdit = () => {
    router.visit(`/dashboard/courses/${props.course.uuid}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/courses/${props.course.uuid}/delete`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="course.name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/courses" class="text-muted-foreground hover:text-foreground">
                        <ChevronLeft class="h-5 w-5" />
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10">
                            <BookOpen class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-semibold">{{ course.name }}</h1>
                                <Badge :variant="course.status ? 'default' : 'secondary'">
                                    {{ course.status ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Badge variant="outline">{{ course.type_label }}</Badge>
                                <span v-if="course.code">Code: {{ course.code }}</span>
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
                    title="Credits"
                    :value="stats.credits"
                    :icon="BookOpen"
                />
                <StatsCard
                    title="Max Students"
                    :value="stats.max_students"
                    :icon="Users"
                />
                <StatsCard
                    title="Current Enrollment"
                    :value="stats.current_enrollment"
                    :icon="GraduationCap"
                />
                <StatsCard
                    title="Available Seats"
                    :value="stats.available_seats"
                    :icon="Users"
                    :variant="stats.available_seats > 0 ? 'success' : 'warning'"
                />
            </div>

            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Description -->
                    <Card v-if="course.description">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">About</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ course.description }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Syllabus -->
                    <Card v-if="course.syllabus">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Syllabus</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ course.syllabus }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Prerequisites -->
                    <Card v-if="course.prerequisites && course.prerequisites.length > 0">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Prerequisites</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-wrap gap-2">
                                <Badge v-for="prereq in course.prerequisites" :key="prereq" variant="secondary">
                                    {{ prereq }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Details -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="course.department_name" class="flex items-center gap-3">
                                <Building2 class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Department</p>
                                    <p class="text-sm font-medium">{{ course.department_name }}</p>
                                </div>
                            </div>
                            <div v-if="course.program_name" class="flex items-center gap-3">
                                <GraduationCap class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Program</p>
                                    <p class="text-sm font-medium">{{ course.program_name }}</p>
                                </div>
                            </div>
                            <div v-if="course.instructor_name" class="flex items-center gap-3">
                                <User class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Instructor</p>
                                    <p class="text-sm font-medium">{{ course.instructor_name }}</p>
                                </div>
                            </div>
                            <div v-if="course.semester || course.year" class="flex items-center gap-3">
                                <Calendar class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Semester / Year</p>
                                    <p class="text-sm font-medium">
                                        {{ course.semester ? `Semester ${course.semester}` : '' }}
                                        {{ course.semester && course.year ? ' / ' : '' }}
                                        {{ course.year ? `Year ${course.year}` : '' }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="course.schedule" class="flex items-center gap-3">
                                <Clock class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Schedule</p>
                                    <p class="text-sm font-medium">{{ course.schedule }}</p>
                                </div>
                            </div>
                            <div v-if="course.room" class="flex items-center gap-3">
                                <MapPin class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Room</p>
                                    <p class="text-sm font-medium">{{ course.room }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
