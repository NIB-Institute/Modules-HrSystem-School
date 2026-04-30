<script setup lang="ts">
import ImportPage, { type TableColumn } from '@/components/shared/ImportPage.vue';
import type { BreadcrumbItem } from '@/types';
import type { DuplicateOption } from '@/composables/useImport';
import { useTranslation } from '@/composables/useTranslation';

const { __ } = useTranslation();
defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: __('Dashboard'), href: '/dashboard' },
    { title: __('Programs'), href: '/dashboard/programs' },
    { title: __('Import'), href: '/dashboard/programs/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: __('Name'), minWidth: '200px' },
    { key: 'code', label: __('Code'), minWidth: '120px' },
    { key: 'school', label: __('School'), minWidth: '200px' },
    { key: 'department', label: __('Department'), minWidth: '150px' },
    { key: 'degree_level', label: __('Degree Level'), minWidth: '120px' },
];

const availableColumns = [
    `${__('Name')} *`,
    `${__('Code')} *`,
    __('School'),
    __('Department'),
    __('Degree Level'),
    __('Duration Years'),
    __('Credits Required'),
    __('Tuition Fee'),
    __('Max Students'),
    __('Admission Requirements'),
    __('Description'),
    __('Status'),
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <ImportPage
            :title="__('Import Programs')"
            entity-name="program"
            :entity-name-plural="__('Programs')"
            :breadcrumbs="breadcrumbs"
            :duplicate-options="duplicateOptions"
            preview-url="/dashboard/programs/import/preview"
            import-url="/dashboard/programs/import"
            list-url="/dashboard/programs"
            template-url="/dashboard/programs/template"
            :required-fields-text="__('Name and Code are required. School and Department must match existing names exactly.')"
            :available-columns="availableColumns"
            :preview-columns="previewColumns"
        />
    </AppLayout>
</template>
