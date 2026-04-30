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
    Building2,
    Mail,
    Phone,
    Globe,
    MapPin,
    Calendar,
    Award,
    Users,
    BookOpen,
    GraduationCap,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { SchoolShowProps } from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps<SchoolShowProps>();
const { t } = useTranslation();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('Dashboard'), href: '/dashboard' },
    { title: t('Schools'), href: '/dashboard/schools' },
    { title: props.school.name, href: `/dashboard/schools/${props.school.uuid}` },
];

const handleEdit = () => {
    router.visit(`/dashboard/schools/${props.school.uuid}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/schools/${props.school.uuid}/delete`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="school.name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/dashboard/schools" class="text-muted-foreground hover:text-foreground">
                        <ChevronLeft class="h-5 w-5" />
                    </Link>
                    <div class="flex items-center gap-4">
                        <!-- Logo or fallback icon -->
                        <div v-if="school.logo" class="h-16 w-16 rounded-xl overflow-hidden border">
                            <img :src="school.logo" :alt="school.name" class="h-full w-full object-cover" />
                        </div>
                        <div v-else class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10">
                            <Building2 class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-semibold">{{ school.name }}</h1>
                                <Badge :variant="school.status ? 'default' : 'secondary'">
                                    {{ school.status ? t('Active') : t('Inactive') }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <Badge variant="outline">{{ school.type_label }}</Badge>
                                <span v-if="school.code">{{ t('Code') }}: {{ school.code }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="handleEdit">
                        <Pencil class="mr-2 h-4 w-4" />
                        {{ t('Edit') }}
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2 class="mr-2 h-4 w-4" />
                        {{ t('Delete') }}
                    </Button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <StatsCard
                    :title="t('Departments')"
                    :value="stats.departments_count"
                    :icon="Building2"
                />
                <StatsCard
                    :title="t('Programs')"
                    :value="stats.programs_count"
                    :icon="BookOpen"
                />
                <StatsCard
                    :title="t('Employees')"
                    :value="stats.employees_count"
                    :icon="Users"
                />
                <StatsCard
                    :title="t('Courses')"
                    :value="stats.courses_count"
                    :icon="GraduationCap"
                />
            </div>

            <!-- Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Description -->
                    <Card v-if="school.description">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">{{ t('About') }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground whitespace-pre-wrap">
                                {{ school.description }}
                            </p>
                        </CardContent>
                    </Card>

                    <!-- Contact Information -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">{{ t('Contact Information') }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div v-if="school.email" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Mail class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">{{ t('Email') }}</p>
                                        <a :href="`mailto:${school.email}`" class="text-sm hover:underline">
                                            {{ school.email }}
                                        </a>
                                    </div>
                                </div>
                                <div v-if="school.phone" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Phone class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">{{ t('Phone') }}</p>
                                        <a :href="`tel:${school.phone}`" class="text-sm hover:underline">
                                            {{ school.phone }}
                                        </a>
                                    </div>
                                </div>
                                <div v-if="school.website" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <Globe class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">{{ t('Website') }}</p>
                                        <a :href="school.website" target="_blank" class="text-sm hover:underline">
                                            {{ school.website }}
                                        </a>
                                    </div>
                                </div>
                                <div v-if="school.address || school.city" class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <MapPin class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-muted-foreground">{{ t('Location') }}</p>
                                        <p class="text-sm">
                                            {{ [school.city, school.country].filter(Boolean).join(', ') || '-' }}
                                        </p>
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
                            <CardTitle class="text-base">{{ t('Details') }}</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="school.established_year" class="flex items-center gap-3">
                                <Calendar class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">{{ t('Established') }}</p>
                                    <p class="text-sm font-medium">{{ school.established_year }}</p>
                                </div>
                            </div>
                            <div v-if="school.accreditation" class="flex items-center gap-3">
                                <Award class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">{{ t('Accreditation') }}</p>
                                    <p class="text-sm font-medium">{{ school.accreditation }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Users class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">{{ t('Total Students') }}</p>
                                    <p class="text-sm font-medium">{{ school.total_students }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Users class="h-4 w-4 text-muted-foreground" />
                                <div>
                                    <p class="text-xs text-muted-foreground">{{ t('Total Staff') }}</p>
                                    <p class="text-sm font-medium">{{ school.total_staff }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
