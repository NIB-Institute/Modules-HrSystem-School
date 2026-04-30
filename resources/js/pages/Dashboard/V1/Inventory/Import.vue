<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';
import { useTranslation } from '@/composables/useTranslation';

defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const { t } = useTranslation();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('Dashboard'), href: '/dashboard' },
    { title: t('Inventory'), href: '/dashboard/inventories' },
    { title: t('Import'), href: '/dashboard/inventories/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'asset_tag', label: t('Asset Tag'), minWidth: '150px' },
    { key: 'equipment', label: t('Equipment'), minWidth: '150px' },
    { key: 'status', label: t('Status'), minWidth: '120px' },
    { key: 'condition', label: t('Condition'), minWidth: '120px' },
];

const availableColumns = [
    `${t('Asset Tag')} *`,
    `${t('Equipment ID')} *`,
    t('Name'),
    t('Serial Number'),
    `${t('Status')} *`,
    `${t('Condition')} *`,
    t('Classroom ID'),
    t('Department ID'),
    t('User ID'),
    t('Acquisition Date'),
    t('Acquisition Cost'),
    t('Warranty Expiry'),
    t('Notes'),
];
</script>

<template>
    <ImportPage
        :title="t('Import Inventory Items')"
        entity-name="inventory"
        :entity-name-plural="t('Inventory Items')"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/inventories/import/preview"
        import-url="/dashboard/inventories/import"
        list-url="/dashboard/inventories"
        template-url="/dashboard/inventories/template"
        :required-fields-text="t('Asset Tag, Equipment ID, Status, and Condition are required.')"
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
