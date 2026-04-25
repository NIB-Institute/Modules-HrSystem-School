<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Equipment', href: '/dashboard/equipment' },
    { title: 'Import', href: '/dashboard/equipment/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: 'Name', minWidth: '200px' },
    { key: 'category', label: 'Category', minWidth: '120px' },
    { key: 'icon', label: 'Icon', minWidth: '100px' },
    { key: 'description', label: 'Description', minWidth: '200px' },
];

const availableColumns = [
    'Name *',
    'Category',
    'Icon',
    'Description',
    'Status',
];
</script>

<template>
    <ImportPage
        title="Import Equipment"
        entity-name="equipment"
        entity-name-plural="Equipment"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/equipment/import/preview"
        import-url="/dashboard/equipment/import"
        list-url="/dashboard/equipment"
        template-url="/dashboard/equipment/template"
        required-fields-text="Name is required. Category must be one of: technology, furniture, safety, accessibility, other."
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
