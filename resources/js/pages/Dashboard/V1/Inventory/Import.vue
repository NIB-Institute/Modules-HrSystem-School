<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';
import { useTranslation } from '@/composables/useTranslation';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const { __ } = useTranslation();

const breadcrumbs: BreadcrumbItem[] = [
    { title: __('Dashboard'), href: '/dashboard' },
    { title: __('Inventory'), href: '/dashboard/inventories' },
    { title: __('Import'), href: '/dashboard/inventories/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'asset_tag', label: __('Asset Tag'), minWidth: '150px' },
    { key: 'equipment', label: __('Equipment'), minWidth: '150px' },
    { key: 'status', label: __('Status'), minWidth: '120px' },
    { key: 'condition', label: __('Condition'), minWidth: '120px' },
];

const availableColumns = [
    `${ __('Asset Tag')} *`,
    `${ __('Equipment ID')} *`,
    __('Name'),
    __('Serial Number'),
    `${ __('Status')} *`,
    `${ __('Condition')} *`,
    __('Classroom ID'),
    __('Department ID'),
    __('User ID'),
    __('Acquisition Date'),
    __('Acquisition Cost'),
    __('Warranty Expiry'),
    __('Notes'),
];
</script>

<template>
    <ImportPage
        :title="__('Import Inventory Items')"
        entity-name="inventory"
        :entity-name-plural="__('Inventory Items')"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/inventories/import/preview"
        import-url="/dashboard/inventories/import"
        list-url="/dashboard/inventories"
        template-url="/dashboard/inventories/template"
        :required-fields-text="__('Asset Tag, Equipment ID, Status, and Condition are required.')"
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
