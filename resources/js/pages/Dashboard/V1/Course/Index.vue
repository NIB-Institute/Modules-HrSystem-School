<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Plus, BookOpen, CheckCircle, XCircle, Search, Eye, Pencil, Trash2, Download, Upload, FileSpreadsheet, Database } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { CourseIndexProps, Course } from '@school/types';

const props = defineProps<CourseIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Courses', href: '/dashboard/courses' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const typeFilter = ref(props.filters.type || 'all');
const departmentFilter = ref(props.filters.department_id?.toString() || 'all');
const programFilter = ref(props.filters.program_id?.toString() || 'all');

// Selection state
const selectedUuids = ref<(string | number)[]>([]);

// Bulk delete handler
const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/courses/bulk-delete?${params.toString()}`);
};

const columns: TableColumn<Course>[] = [
    {
        key: 'name',
        label: 'Name',
        render: (course) => course.name,
    },
    {
        key: 'code',
        label: 'Code',
        render: (course) => course.code || '-',
    },
    {
        key: 'type',
        label: 'Type',
        render: (course) => course.type_label,
    },
    {
        key: 'credits',
        label: 'Credits',
        render: (course) => course.credits?.toString() || '-',
    },
    {
        key: 'department',
        label: 'Department',
        render: (course) => course.department_name || '-',
    },
    {
        key: 'status',
        label: 'Status',
        render: (course) => course.status ? 'Active' : 'Inactive',
    },
];

const actions: TableAction<Course>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (course) => router.visit(`/dashboard/courses/${course.uuid}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (course) => router.visit(`/dashboard/courses/${course.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (course) => router.visit(`/dashboard/courses/${course.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.courses.meta.current_page,
    last_page: props.courses.meta.last_page,
    per_page: props.courses.meta.per_page,
    total: props.courses.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    type: typeFilter.value !== 'all' ? typeFilter.value : undefined,
    department_id: departmentFilter.value !== 'all' ? departmentFilter.value : undefined,
    program_id: programFilter.value !== 'all' ? programFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/courses', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/courses', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/courses', getFilterParams(), { preserveState: true });
};

watch([statusFilter, typeFilter, departmentFilter, programFilter], () => {
    router.get('/dashboard/courses', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/courses/create');
};

const handleExport = () => {
    const filterParams = getFilterParams();
    const params = new URLSearchParams();
    Object.entries(filterParams).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            params.append(key, String(value));
        }
    });
    window.location.href = `/dashboard/courses/export?${params.toString()}`;
};

const handleImport = () => {
    router.visit('/dashboard/courses/import');
};

const handleDownloadTemplate = () => {
    window.location.href = '/dashboard/courses/template';
};

const handleTrash = () => {
    router.visit('/dashboard/courses/trash');
};

const handleStatusToggle = (course: Course, newStatus: boolean) => {
    router.put(`/dashboard/courses/${course.uuid}/toggle-status`, {
        status: newStatus,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const typeOptions = computed(() => {
    return Object.entries(props.types).map(([value, label]) => ({
        value,
        label,
    }));
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Courses" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Total Courses"
                    :value="props.stats.total"
                    :icon="BookOpen"
                />
                <StatsCard
                    title="Active"
                    :value="props.stats.active"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    title="Inactive"
                    :value="props.stats.inactive"
                    :icon="XCircle"
                    variant="warning"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Courses</h2>
                        <p class="text-sm text-muted-foreground">Manage courses and curricula</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <ButtonGroup>
                            <Button variant="default">
                                <Database class="mr-2 h-4 w-4" />
                                All
                            </Button>
                            <Button variant="outline" @click="handleTrash">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Trash
                            </Button>
                        </ButtonGroup>
                        <ButtonGroup>
                            <Button variant="outline" @click="handleExport">
                                <Download class="mr-2 h-4 w-4" />
                                Export
                            </Button>
                            <Button variant="outline" @click="handleImport">
                                <Upload class="mr-2 h-4 w-4" />
                                Import
                            </Button>
                            <Button variant="outline" @click="handleDownloadTemplate">
                                <FileSpreadsheet class="mr-2 h-4 w-4" />
                                Template
                            </Button>
                        </ButtonGroup>
                        <Button @click="handleCreate">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Course
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search courses..."
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Select v-model="typeFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Types</SelectItem>
                            <SelectItem v-for="type in typeOptions" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="departmentFilter">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Department" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Departments</SelectItem>
                            <SelectItem v-for="dept in props.departments" :key="dept.id" :value="dept.id.toString()">
                                {{ dept.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="statusFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            <SelectItem value="1">Active</SelectItem>
                            <SelectItem value="0">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Table -->
                <TableReusable
                    v-model:selected="selectedUuids"
                    :data="props.courses.data"
                    :columns="columns"
                    :actions="actions"
                    :pagination="pagination"
                    :searchable="false"
                    :selectable="true"
                    select-key="uuid"
                    @page-change="handlePageChange"
                    @per-page-change="handlePerPageChange"
                >
                    <template #bulk-actions>
                        <Button variant="destructive" size="sm" @click="openBulkDeleteDialog">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete Selected
                        </Button>
                    </template>
                    <template #cell-name="{ item }">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <BookOpen class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.program_name" class="text-xs text-muted-foreground">{{ item.program_name }}</div>
                            </div>
                        </div>
                    </template>
                    <template #cell-type="{ item }">
                        <Badge variant="outline">{{ item.type_label }}</Badge>
                    </template>
                    <template #cell-credits="{ item }">
                        <Badge variant="secondary">{{ item.credits }} credits</Badge>
                    </template>
                    <template #cell-status="{ item }">
                        <div class="flex items-center gap-2" @click.stop>
                            <Switch
                                :model-value="item.status"
                                @update:model-value="handleStatusToggle(item, $event)"
                            />
                            <span class="text-sm text-muted-foreground">
                                {{ item.status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </template>
                </TableReusable>
            </div>
        </div>
    </AppLayout>
</template>
