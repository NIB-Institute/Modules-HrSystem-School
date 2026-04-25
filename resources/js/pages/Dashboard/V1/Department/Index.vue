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
import { Plus, Building, CheckCircle, XCircle, Search, Eye, Pencil, Trash2, Download, Upload, FileSpreadsheet, Database, Users, QrCode } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { DepartmentIndexProps, Department } from '@school/types';

const props = defineProps<DepartmentIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: '/dashboard/departments' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const schoolFilter = ref(props.filters.school_id || 'all');

// Selection state - now using v-model with TableReusable
const selectedUuids = ref<(string | number)[]>([]);

const columns: TableColumn<Department>[] = [
    {
        key: 'name',
        label: 'Name',
        render: (department) => department.name,
    },
    {
        key: 'code',
        label: 'Code',
        render: (department) => department.code || '-',
    },
    {
        key: 'school',
        label: 'School',
        render: (department) => department.school_name || '-',
    },
    {
        key: 'head_of_department',
        label: 'Head',
        render: (department) => department.head_of_department || '-',
    },
    {
        key: 'staff_count',
        label: 'Staff',
        align: 'center',
        render: (department) => department.staff_count ?? 0,
    },
    {
        key: 'status',
        label: 'Status',
        render: (department) => department.status ? 'Active' : 'Inactive',
    },
];

const actions: TableAction<Department>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (department) => router.visit(`/dashboard/departments/${department.uuid}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (department) => router.visit(`/dashboard/departments/${department.uuid}/edit`),
    },
    {
        label: 'QR Code',
        icon: QrCode,
        onClick: (department) => router.visit(`/dashboard/departments/${department.uuid}/qr-code`),
        separator: true,
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (department) => router.visit(`/dashboard/departments/${department.uuid}/delete`),
        variant: 'destructive',
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.departments.meta.current_page,
    last_page: props.departments.meta.last_page,
    per_page: props.departments.meta.per_page,
    total: props.departments.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    school_id: schoolFilter.value !== 'all' ? schoolFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/departments', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/departments', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/departments', getFilterParams(), { preserveState: true });
};

watch([statusFilter, schoolFilter], () => {
    router.get('/dashboard/departments', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/departments/create');
};

const handleExport = () => {
    const filterParams = getFilterParams();
    const params = new URLSearchParams();
    Object.entries(filterParams).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            params.append(key, String(value));
        }
    });
    window.location.href = `/dashboard/departments/export?${params.toString()}`;
};

const handleImport = () => {
    router.visit('/dashboard/departments/import');
};

const handleDownloadTemplate = () => {
    window.location.href = '/dashboard/departments/template';
};

const handleTrash = () => {
    router.visit('/dashboard/departments/trash');
};

const handleStatusToggle = (department: Department, newStatus: boolean) => {
    router.put(`/dashboard/departments/${department.uuid}/toggle-status`, {
        status: newStatus,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleSchoolFilterChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        schoolFilter.value = value;
    }
};

const handleStatusFilterChange = (value: string | number | boolean | bigint | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        statusFilter.value = value;
    }
};

// Bulk delete handler - navigate to confirmation modal
const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/departments/bulk-delete?${params.toString()}`);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Departments" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Total Departments"
                    :value="props.stats.total"
                    :icon="Building"
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
                        <h2 class="text-lg font-semibold">Departments</h2>
                        <p class="text-sm text-muted-foreground">Manage departments within schools</p>
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
                            Add Department
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search departments..."
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Select :model-value="schoolFilter" @update:model-value="handleSchoolFilterChange">
                        <SelectTrigger class="w-[200px]">
                            <SelectValue placeholder="School" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Schools</SelectItem>
                            <SelectItem v-for="school in props.schools" :key="school.value" :value="school.value">
                                {{ school.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select :model-value="statusFilter" @update:model-value="handleStatusFilterChange">
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

                <!-- Table with built-in selection -->
                <TableReusable
                    v-model:selected="selectedUuids"
                    :data="props.departments.data"
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
                                <Building class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.email" class="text-xs text-muted-foreground">{{ item.email }}</div>
                            </div>
                        </div>
                    </template>
                    <template #cell-school="{ item }">
                        <Badge variant="outline">{{ item.school_name || '-' }}</Badge>
                    </template>
                    <template #cell-staff_count="{ item }">
                        <div class="flex items-center justify-center gap-1">
                            <Users class="h-4 w-4 text-muted-foreground" />
                            <span>{{ item.staff_count ?? 0 }}</span>
                        </div>
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
