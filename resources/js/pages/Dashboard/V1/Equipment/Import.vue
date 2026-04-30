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
    { title: __('Equipment'), href: '/dashboard/equipment' },
    { title: __('Import'), href: '/dashboard/equipment/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: __('Name'), minWidth: '200px' },
    { key: 'category', label: __('Category'), minWidth: '120px' },
    { key: 'icon', label: __('Icon'), minWidth: '100px' },
    { key: 'description', label: __('Description'), minWidth: '200px' },
];

const availableColumns = [
    `${__('Name')} *`,
    __('Category'),
    __('Icon'),
    __('Description'),
    __('Status'),
];
</script>

<template>
    <ImportPage
        :title="__('Import Equipment')"
        entity-name="equipment"
        :entity-name-plural="__('Equipment')"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/equipment/import/preview"
        import-url="/dashboard/equipment/import"
        list-url="/dashboard/equipment"
        template-url="/dashboard/equipment/template"
        :required-fields-text="__('Name is required. Category must be one of: technology, furniture, safety, accessibility, other.')"
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
