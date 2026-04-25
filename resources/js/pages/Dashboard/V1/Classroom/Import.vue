<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Classrooms', href: '/dashboard/classrooms' },
    { title: 'Import', href: '/dashboard/classrooms/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: 'Name', minWidth: '200px' },
    { key: 'code', label: 'Code', minWidth: '120px' },
    { key: 'department', label: 'Department', minWidth: '150px' },
    { key: 'building', label: 'Building', minWidth: '120px' },
    { key: 'capacity', label: 'Capacity', minWidth: '80px' },
];

const availableColumns = [
    'Name *',
    'Code *',
    'Department',
    'Building',
    'Floor',
    'Capacity',
    'Type',
    'Has Projector',
    'Has Whiteboard',
    'Has AC',
    'Description',
    'Status',
];
</script>

<template>
    <ImportPage
        title="Import Classrooms"
        entity-name="classroom"
        entity-name-plural="Classrooms"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/classrooms/import/preview"
        import-url="/dashboard/classrooms/import"
        list-url="/dashboard/classrooms"
        template-url="/dashboard/classrooms/template"
        required-fields-text="Name and Code are required. Department must match an existing department name exactly."
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
