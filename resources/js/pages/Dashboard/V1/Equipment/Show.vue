<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Pencil, Trash2, Monitor, Armchair, ShieldCheck, Accessibility, Wrench } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EquipmentShowProps } from '@school/types';

const props = defineProps<EquipmentShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Equipment', href: '/dashboard/equipment' },
    { title: props.equipment.name, href: `/dashboard/equipment/${props.equipment.uuid}` },
];

const handleBack = () => {
    router.visit('/dashboard/equipment');
};

const handleEdit = () => {
    router.visit(`/dashboard/equipment/${props.equipment.uuid}/edit`);
};

const handleDelete = () => {
    router.visit(`/dashboard/equipment/${props.equipment.uuid}/delete`);
};

const getCategoryIcon = (category: string) => {
    switch (category) {
        case 'technology':
            return Monitor;
        case 'furniture':
            return Armchair;
        case 'safety':
            return ShieldCheck;
        case 'accessibility':
            return Accessibility;
        default:
            return Wrench;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="equipment.name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="icon" @click="handleBack">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                            <component :is="getCategoryIcon(equipment.category)" class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">{{ equipment.name }}</h1>
                            <p class="text-sm text-muted-foreground">{{ equipment.slug }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
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

            <!-- Content -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Details -->
                <Card>
                    <CardHeader>
                        <CardTitle>Details</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Name</span>
                            <span class="font-medium">{{ equipment.name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Slug</span>
                            <span class="font-medium">{{ equipment.slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Category</span>
                            <Badge>{{ equipment.category_label }}</Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="equipment.status ? 'default' : 'secondary'">
                                {{ equipment.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </div>
                        <div v-if="equipment.icon" class="flex justify-between">
                            <span class="text-muted-foreground">Icon</span>
                            <span class="font-medium">{{ equipment.icon }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Usage Stats -->
                <Card>
                    <CardHeader>
                        <CardTitle>Usage</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Used in Classrooms</span>
                            <span class="font-medium">{{ equipment.classrooms_count || 0 }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Description -->
            <Card v-if="equipment.description">
                <CardHeader>
                    <CardTitle>Description</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground">{{ equipment.description }}</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
