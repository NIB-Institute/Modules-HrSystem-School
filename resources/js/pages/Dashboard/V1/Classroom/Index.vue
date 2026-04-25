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
import { Plus, DoorOpen, CheckCircle, XCircle, Search, Eye, Pencil, Trash2, Users, Building, Download, Upload, FileSpreadsheet, Database } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { ClassroomIndexProps, Classroom } from '@school/types';

const props = defineProps<ClassroomIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Classrooms', href: '/dashboard/classrooms' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const typeFilter = ref(props.filters.type || 'all');
const departmentFilter = ref(props.filters.department_id || 'all');

// Selection state
const selectedUuids = ref<(string | number)[]>([]);

// Bulk delete handler
const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/classrooms/bulk-delete?${params.toString()}`);
};

const columns: TableColumn<Classroom>[] = [
    {
        key: 'name',
        label: 'Name',
        render: (classroom) => classroom.name,
    },
    {
        key: 'code',
        label: 'Code',
        render: (classroom) => classroom.code || '-',
    },
    {
        key: 'type',
        label: 'Type',
        render: (classroom) => classroom.type_label,
    },
    {
        key: 'department_name',
        label: 'Department',
        render: (classroom) => classroom.department_name || '-',
    },
    {
        key: 'capacity',
        label: 'Capacity',
        render: (classroom) => classroom.capacity?.toString() || '-',
    },
    {
        key: 'status',
        label: 'Status',
        render: (classroom) => classroom.status ? 'Active' : 'Inactive',
    },
];

const actions: TableAction<Classroom>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (classroom) => router.visit(`/dashboard/classrooms/${classroom.uuid}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (classroom) => router.visit(`/dashboard/classrooms/${classroom.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (classroom) => router.visit(`/dashboard/classrooms/${classroom.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.classrooms.meta.current_page,
    last_page: props.classrooms.meta.last_page,
    per_page: props.classrooms.meta.per_page,
    total: props.classrooms.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    type: typeFilter.value !== 'all' ? typeFilter.value : undefined,
    department_id: departmentFilter.value !== 'all' ? departmentFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/classrooms', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/classrooms', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/classrooms', getFilterParams(), { preserveState: true });
};

watch([statusFilter, typeFilter, departmentFilter], () => {
    router.get('/dashboard/classrooms', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/classrooms/create');
};

const handleExport = () => {
    const filterParams = getFilterParams();
    const params = new URLSearchParams();
    Object.entries(filterParams).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            params.append(key, String(value));
        }
    });
    window.location.href = `/dashboard/classrooms/export?${params.toString()}`;
};

const handleImport = () => {
    router.visit('/dashboard/classrooms/import');
};

const handleDownloadTemplate = () => {
    window.location.href = '/dashboard/classrooms/template';
};

const handleTrash = () => {
    router.visit('/dashboard/classrooms/trash');
};

const handleStatusToggle = (classroom: Classroom, newStatus: boolean) => {
    router.put(`/dashboard/classrooms/${classroom.uuid}/toggle-status`, {
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
        <Head title="Classrooms" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <StatsCard
                    title="Total Classrooms"
                    :value="props.stats.total"
                    :icon="DoorOpen"
                />
                <StatsCard
                    title="Active"
                    :value="props.stats.active"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    title="Available"
                    :value="props.stats.available"
                    :icon="Users"
                    variant="info"
                />
                <StatsCard
                    title="Labs"
                    :value="props.stats.by_type?.lab || 0"
                    :icon="Building"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Classrooms</h2>
                        <p class="text-sm text-muted-foreground">Manage classrooms and facilities</p>
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
                            Add Classroom
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search classrooms..."
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Select v-model="departmentFilter">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Department" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Departments</SelectItem>
                            <SelectItem v-for="dept in props.departments" :key="dept.id" :value="String(dept.id)">
                                {{ dept.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
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
                    :data="props.classrooms.data"
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
                                <DoorOpen class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.full_location" class="text-xs text-muted-foreground">{{ item.full_location }}</div>
                            </div>
                        </div>
                    </template>
                    <template #cell-type="{ item }">
                        <Badge variant="outline">{{ item.type_label }}</Badge>
                    </template>
                    <template #cell-capacity="{ item }">
                        <div class="flex items-center gap-2">
                            <Users class="h-4 w-4 text-muted-foreground" />
                            <span>{{ item.capacity }}</span>
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
