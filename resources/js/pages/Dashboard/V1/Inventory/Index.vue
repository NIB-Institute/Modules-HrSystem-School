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
import {
    Plus, Package, Search, Eye, Pencil, Trash2,
    Download, Upload, FileSpreadsheet, CheckCircle, AlertTriangle,
    Wrench, Ban, Database,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { InventoryIndexProps, Inventory, InventoryStatus } from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps<InventoryIndexProps>();
const { t } = useTranslation();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('Dashboard'), href: '/dashboard' },
    { title: t('Inventory'), href: '/dashboard/inventories' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref<string>(props.filters.status || 'all');
const conditionFilter = ref<string>(props.filters.condition || 'all');
const equipmentFilter = ref<string>(props.filters.equipment_id?.toString() || 'all');

// Selection state
const selectedUuids = ref<(string | number)[]>([]);

const openBulkDeleteDialog = () => {
    const params = new URLSearchParams();
    selectedUuids.value.forEach((uuid) => params.append('uuids[]', String(uuid)));
    router.visit(`/dashboard/inventories/bulk-delete?${params.toString()}`);
};

const statusVariant = (status: InventoryStatus): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'in_use': return 'default';
        case 'in_stock': return 'secondary';
        case 'maintenance': return 'outline';
        case 'retired':
        case 'lost':
        case 'disposed': return 'destructive';
        default: return 'outline';
    }
};

const columns: TableColumn<Inventory>[] = [
    { key: 'asset_tag', label: t('Asset Tag'), render: (i) => i.asset_tag },
    { key: 'name', label: t('Name'), render: (i) => i.name || i.equipment?.name || '—' },
    { key: 'equipment', label: t('Equipment'), render: (i) => i.equipment?.name ?? '—' },
    {
        key: 'location',
        label: t('Location'),
        render: (i) => i.classroom?.name || i.department?.name || (i.assigned_to ? `${t('Assigned')}: ${i.assigned_to.name}` : '—'),
    },
    { key: 'status', label: t('Status'), render: (i) => i.status_label },
    { key: 'condition', label: t('Condition'), render: (i) => i.condition_label },
];

const actions: TableAction<Inventory>[] = [
    {
        label: t('View'),
        icon: Eye,
        onClick: (i) => router.visit(`/dashboard/inventories/${i.uuid}`),
    },
    {
        label: t('Edit'),
        icon: Pencil,
        onClick: (i) => router.visit(`/dashboard/inventories/${i.uuid}/edit`),
    },
    {
        label: t('Delete'),
        icon: Trash2,
        onClick: (i) => router.visit(`/dashboard/inventories/${i.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.inventories.meta.current_page,
    last_page: props.inventories.meta.last_page,
    per_page: props.inventories.meta.per_page,
    total: props.inventories.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    condition: conditionFilter.value !== 'all' ? conditionFilter.value : undefined,
    equipment_id: equipmentFilter.value !== 'all' ? equipmentFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/inventories', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/inventories', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/inventories', getFilterParams(), { preserveState: true });
};

watch([statusFilter, conditionFilter, equipmentFilter], () => {
    router.get('/dashboard/inventories', getFilterParams(), { preserveState: true });
});

const handleCreate = () => router.visit('/dashboard/inventories/create');
const handleExport = () => router.visit('/dashboard/inventories/export-options');
const handleDownloadTemplate = () => router.visit('/dashboard/inventories/export-options');
const handleImport = () => router.visit('/dashboard/inventories/import');
const handleTrash = () => router.visit('/dashboard/inventories/trash');

const statusOptions = computed(() =>
    Object.entries(props.statuses).map(([value, label]) => ({ value, label })),
);

const conditionOptions = computed(() =>
    Object.entries(props.conditions).map(([value, label]) => ({ value, label })),
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="t('Inventory')" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-6">
                <StatsCard :title="t('Total')" :value="props.stats.total" :icon="Package" />
                <StatsCard :title="t('In Stock')" :value="props.stats.in_stock" :icon="Package" variant="success" />
                <StatsCard :title="t('In Use')" :value="props.stats.in_use" :icon="CheckCircle" variant="success" />
                <StatsCard :title="t('Maintenance')" :value="props.stats.maintenance" :icon="Wrench" />
                <StatsCard :title="t('Retired')" :value="props.stats.retired" :icon="Ban" />
                <StatsCard :title="t('Lost')" :value="props.stats.lost" :icon="AlertTriangle" variant="destructive" />
            </div>

            <!-- Main -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ t('Inventory') }}</h2>
                        <p class="text-sm text-muted-foreground">{{ t('Track individual physical assets across the school.') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <ButtonGroup>
                            <Button variant="default">
                                <Database class="mr-2 h-4 w-4" />
                                {{ t('All') }}
                            </Button>
                            <Button variant="outline" @click="handleTrash">
                                <Trash2 class="mr-2 h-4 w-4" />
                                {{ t('Trash') }}
                            </Button>
                        </ButtonGroup>
                        <ButtonGroup>
                            <Button variant="outline" @click="handleExport">
                                <Download class="mr-2 h-4 w-4" /> {{ t('Export') }}
                            </Button>
                            <Button variant="outline" @click="handleImport">
                                <Upload class="mr-2 h-4 w-4" /> {{ t('Import') }}
                            </Button>
                            <Button variant="outline" @click="handleDownloadTemplate">
                                <FileSpreadsheet class="mr-2 h-4 w-4" /> {{ t('Template') }}
                            </Button>
                        </ButtonGroup>
                        <Button @click="handleCreate">
                            <Plus class="mr-2 h-4 w-4" /> {{ t('Add Item') }}
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            :placeholder="t('Search by tag / serial / name...')"
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>

                    <Select v-model="statusFilter">
                        <SelectTrigger class="w-[160px]"><SelectValue :placeholder="t('Status')" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('All statuses') }}</SelectItem>
                            <SelectItem v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="conditionFilter">
                        <SelectTrigger class="w-[160px]"><SelectValue :placeholder="t('Condition')" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('All conditions') }}</SelectItem>
                            <SelectItem v-for="opt in conditionOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select v-model="equipmentFilter">
                        <SelectTrigger class="w-[180px]"><SelectValue :placeholder="t('Equipment')" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('All equipment') }}</SelectItem>
                            <SelectItem v-for="e in props.equipment" :key="e.id" :value="String(e.id)">
                                {{ e.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Table -->
                <TableReusable
                    :data="props.inventories.data"
                    :columns="columns"
                    :actions="actions"
                    :pagination="pagination"
                    v-model:selected-rows="selectedUuids"
                    row-key="uuid"
                    :searchable="false"
                    @page-change="handlePageChange"
                    @per-page-change="handlePerPageChange"
                >
                    <template #cell-status="{ item }">
                        <Badge :variant="statusVariant(item.status)">{{ item.status_label }}</Badge>
                    </template>

                    <template #bulk-actions>
                        <Button
                            v-if="selectedUuids.length"
                            variant="destructive"
                            size="sm"
                            @click="openBulkDeleteDialog"
                        >
                            <Trash2 class="mr-2 h-4 w-4" />
                            {{ t('Delete :count', { count: selectedUuids.length }) }}
                        </Button>
                    </template>
                </TableReusable>
            </div>
        </div>
    </AppLayout>
</template>
