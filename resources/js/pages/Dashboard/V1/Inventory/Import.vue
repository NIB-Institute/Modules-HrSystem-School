<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Inventory', href: '/dashboard/inventories' },
    { title: 'Import', href: '/dashboard/inventories/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'asset_tag', label: 'Asset Tag', minWidth: '140px' },
    { key: 'serial_number', label: 'Serial Number', minWidth: '140px' },
    { key: 'equipment', label: 'Equipment', minWidth: '180px' },
    { key: 'classroom', label: 'Classroom', minWidth: '140px' },
    { key: 'department', label: 'Department', minWidth: '160px' },
];

const availableColumns = [
    'Asset Tag *',
    'Serial Number',
    'Name',
    'Equipment *',
    'Classroom',
    'Department',
    'Status',
    'Condition',
    'Cost',
    'Vendor',
    'Purchased At',
    'Warranty Until',
    'Notes',
    'Is Active',
];
</script>

<template>
    <ImportPage
        title="Import Inventory"
        entity-name="inventory item"
        entity-name-plural="Inventory"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/inventories/import/preview"
        import-url="/dashboard/inventories/import"
        list-url="/dashboard/inventories"
        template-url="/dashboard/inventories/template"
        required-fields-text="Asset Tag and Equipment are required. Equipment must match an existing equipment name exactly."
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
