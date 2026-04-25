<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: '/dashboard/departments' },
    { title: 'Import', href: '/dashboard/departments/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: 'Name', minWidth: '200px' },
    { key: 'code', label: 'Code', minWidth: '120px' },
    { key: 'school', label: 'School', minWidth: '200px' },
    { key: 'email', label: 'Email', minWidth: '180px' },
    { key: 'phone', label: 'Phone', minWidth: '140px' },
];

const availableColumns = [
    'Name *',
    'Code *',
    'School',
    'Description',
    'Email',
    'Phone',
    'Office Location',
    'Established Year',
    'Status',
];
</script>

<template>
    <ImportPage
        title="Import Departments"
        entity-name="department"
        entity-name-plural="Departments"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/departments/import/preview"
        import-url="/dashboard/departments/import"
        list-url="/dashboard/departments"
        template-url="/dashboard/departments/template"
        required-fields-text="Name and Code are required. School must match an existing school name exactly."
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
