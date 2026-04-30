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
    { title: t('Courses'), href: '/dashboard/courses' },
    { title: t('Import'), href: '/dashboard/courses/import' },
];

const previewColumns: TableColumn[] = [
    { key: 'name', label: t('Name'), minWidth: '200px' },
    { key: 'code', label: t('Code'), minWidth: '120px' },
    { key: 'department', label: t('Department'), minWidth: '150px' },
    { key: 'program', label: t('Program'), minWidth: '150px' },
    { key: 'credits', label: t('Credits'), minWidth: '80px' },
];

const availableColumns = [
    `${t('Name')} *`,
    `${t('Code')} *`,
    t('Department'),
    t('Program'),
    t('Credits'),
    t('Type'),
    t('Semester'),
    t('Year'),
    t('Max Students'),
    t('Schedule'),
    t('Room'),
    t('Description'),
    t('Status'),
];
</script>

<template>
    <ImportPage
        :title="t('Import Courses')"
        entity-name="course"
        :entity-name-plural="t('Courses')"
        :breadcrumbs="breadcrumbs"
        :duplicate-options="duplicateOptions"
        preview-url="/dashboard/courses/import/preview"
        import-url="/dashboard/courses/import"
        list-url="/dashboard/courses"
        template-url="/dashboard/courses/template"
        :required-fields-text="t('Name and Code are required. Department and Program must match existing names exactly.')"
        :available-columns="availableColumns"
        :preview-columns="previewColumns"
    />
</template>
