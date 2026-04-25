<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Courses', href: '/dashboard/courses' },
    { title: 'Import', href: '/dashboard/courses/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: 'Name', minWidth: '200px' },
    { key: 'code', label: 'Code', minWidth: '120px' },
    { key: 'department', label: 'Department', minWidth: '150px' },
    { key: 'program', label: 'Program', minWidth: '150px' },
    { key: 'credits', label: 'Credits', minWidth: '80px' },
];

const availableColumns = [
    'Name *',
    'Code *',
    'Department',
    'Program',
    'Credits',
    'Type',
    'Semester',
    'Year',
    'Max Students',
    'Schedule',
    'Room',
    'Description',
    'Status',
];
</script>

<template>
    <ImportPage
        title="Import Courses"
        entity-name="course"
        entity-name-plural="Courses"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/courses/import/preview"
        import-url="/dashboard/courses/import"
        list-url="/dashboard/courses"
        template-url="/dashboard/courses/template"
        required-fields-text="Name and Code are required. Department and Program must match existing names exactly."
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
