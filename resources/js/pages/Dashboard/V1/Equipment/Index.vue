<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard, ButtonGroup } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Plus, Wrench, CheckCircle, Search, Eye, Pencil, Trash2, Monitor, Armchair, ShieldCheck, Accessibility, Download, Upload, FileSpreadsheet, Database } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EquipmentIndexProps, Equipment } from '@school/types';

const props = defineProps<EquipmentIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Equipment', href: '/dashboard/equipment' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const categoryFilter = ref(props.filters.category || 'all');

// Selection state
const selectedUuids = ref<(string | number)[]>([]);

// Bulk delete handler
const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => {
        params.append('uuids[]', String(uuid));
    });
    router.visit(`/dashboard/equipment/bulk-delete?${params.toString()}`);
};

const columns: TableColumn<Equipment>[] = [
    {
        key: 'name',
        label: 'Name',
        render: (equipment) => equipment.name,
    },
    {
        key: 'category',
        label: 'Category',
        render: (equipment) => equipment.category_label,
    },
    {
        key: 'classrooms_count',
        label: 'Used In',
        render: (equipment) => `${equipment.classrooms_count || 0} classrooms`,
    },
    {
        key: 'status',
        label: 'Status',
        render: (equipment) => equipment.status ? 'Active' : 'Inactive',
    },
];

const actions: TableAction<Equipment>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (equipment) => router.visit(`/dashboard/equipment/${equipment.uuid}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (equipment) => router.visit(`/dashboard/equipment/${equipment.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (equipment) => router.visit(`/dashboard/equipment/${equipment.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.equipment.meta.current_page,
    last_page: props.equipment.meta.last_page,
    per_page: props.equipment.meta.per_page,
    total: props.equipment.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    category: categoryFilter.value !== 'all' ? categoryFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/equipment', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/equipment', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/equipment', getFilterParams(), { preserveState: true });
};

watch([statusFilter, categoryFilter], () => {
    router.get('/dashboard/equipment', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/equipment/create');
};

const handleExport = () => {
    const filterParams = getFilterParams();
    const params = new URLSearchParams();
    Object.entries(filterParams).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            params.append(key, String(value));
        }
    });
    window.location.href = `/dashboard/equipment/export?${params.toString()}`;
};

const handleImport = () => {
    router.visit('/dashboard/equipment/import');
};

const handleDownloadTemplate = () => {
    window.location.href = '/dashboard/equipment/template';
};

const handleTrash = () => {
    router.visit('/dashboard/equipment/trash');
};

const categoryOptions = computed(() => {
    return Object.entries(props.categories).map(([value, label]) => ({
        value,
        label,
    }));
});

const getCategoryIcon = (category: string) => {
    switch (category) {
        case 'technology':
            return Monitor;
        case 'furniture':
            return Armchair;
        case 'safety':
            return ShieldCheck;
        case 'accessibility':
            return Accessibility;
        default:
            return Wrench;
    }
};

const getCategoryVariant = (category: string) => {
    switch (category) {
        case 'technology':
            return 'default';
        case 'furniture':
            return 'secondary';
        case 'safety':
            return 'destructive';
        case 'accessibility':
            return 'outline';
        default:
            return 'outline';
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Equipment" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Total Equipment"
                    :value="props.stats.total"
                    :icon="Wrench"
                />
                <StatsCard
                    title="Active"
                    :value="props.stats.active"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    title="Technology"
                    :value="props.stats.by_category.technology"
                    :icon="Monitor"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Equipment</h2>
                        <p class="text-sm text-muted-foreground">Manage classroom equipment and amenities</p>
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
                            Add Equipment
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search equipment..."
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Select v-model="categoryFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Categories</SelectItem>
                            <SelectItem v-for="cat in categoryOptions" :key="cat.value" :value="cat.value">
                                {{ cat.label }}
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
                    :data="props.equipment.data"
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
                                <component :is="getCategoryIcon(item.category)" class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.slug" class="text-xs text-muted-foreground">{{ item.slug }}</div>
                            </div>
                        </div>
                    </template>
                    <template #cell-category="{ item }">
                        <Badge :variant="getCategoryVariant(item.category)">
                            {{ item.category_label }}
                        </Badge>
                    </template>
                    <template #cell-status="{ item }">
                        <Badge :variant="item.status ? 'default' : 'secondary'">
                            {{ item.status ? 'Active' : 'Inactive' }}
                        </Badge>
                    </template>
                </TableReusable>
            </div>
        </div>
    </AppLayout>
</template>
