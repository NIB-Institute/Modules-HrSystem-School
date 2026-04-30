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
    { title: t('Classrooms'), href: '/dashboard/classrooms' },
    { title: t('Import'), href: '/dashboard/classrooms/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: t('Name'), minWidth: '200px' },
    { key: 'code', label: t('Code'), minWidth: '120px' },
    { key: 'department', label: t('Department'), minWidth: '150px' },
    { key: 'building', label: t('Building'), minWidth: '120px' },
    { key: 'capacity', label: t('Capacity'), minWidth: '80px' },
];

const availableColumns = [
    `${t('Name')} *`,
    `${t('Code')} *`,
    t('Department'),
    t('Building'),
    t('Floor'),
    t('Capacity'),
    t('Type'),
    t('Has Projector'),
    t('Has Whiteboard'),
    t('Has AC'),
    t('Description'),
    t('Status'),
];
</script>

<template>
    <ImportPage
        :title="t('Import Classrooms')"
        entity-name="classroom"
        :entity-name-plural="t('Classrooms')"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/classrooms/import/preview"
        import-url="/dashboard/classrooms/import"
        list-url="/dashboard/classrooms"
        template-url="/dashboard/classrooms/template"
        :required-fields-text="t('Name and Code are required. Department must match an existing department name exactly.')"
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
