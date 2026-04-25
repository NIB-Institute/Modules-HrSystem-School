<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Programs', href: '/dashboard/programs' },
    { title: 'Import', href: '/dashboard/programs/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: 'Name', minWidth: '200px' },
    { key: 'code', label: 'Code', minWidth: '120px' },
    { key: 'school', label: 'School', minWidth: '200px' },
    { key: 'department', label: 'Department', minWidth: '150px' },
    { key: 'degree_level', label: 'Degree Level', minWidth: '120px' },
];

const availableColumns = [
    'Name *',
    'Code *',
    'School',
    'Department',
    'Degree Level',
    'Duration Years',
    'Credits Required',
    'Tuition Fee',
    'Max Students',
    'Admission Requirements',
    'Description',
    'Status',
];
</script>

<template>
    <ImportPage
        title="Import Programs"
        entity-name="program"
        entity-name-plural="Programs"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/programs/import/preview"
        import-url="/dashboard/programs/import"
        list-url="/dashboard/programs"
        template-url="/dashboard/programs/template"
        required-fields-text="Name and Code are required. School and Department must match existing names exactly."
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
