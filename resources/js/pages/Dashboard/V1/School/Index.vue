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
import { Plus, GraduationCap, CheckCircle, XCircle, Search, Eye, Pencil, Trash2, Building2, Database } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { SchoolIndexProps, School } from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps<SchoolIndexProps>();
const { t } = useTranslation();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('Dashboard'), href: '/dashboard' },
    { title: t('Schools'), href: '/dashboard/schools' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const typeFilter = ref(props.filters.type || 'all');

const columns: TableColumn<School>[] = [
    {
        key: 'name',
        label: t('Name'),
        render: (school) => school.name,
    },
    {
        key: 'code',
        label: t('Code'),
        render: (school) => school.code || '-',
    },
    {
        key: 'type',
        label: t('Type'),
        render: (school) => school.type_label,
    },
    {
        key: 'city',
        label: t('Location'),
        render: (school) => school.city || '-',
    },
    {
        key: 'status',
        label: t('Status'),
        render: (school) => school.status ? t('Active') : t('Inactive'),
    },
];

const actions: TableAction<School>[] = [
    {
        label: t('View'),
        icon: Eye,
        onClick: (school) => router.visit(`/dashboard/schools/${school.uuid}`),
    },
    {
        label: t('Edit'),
        icon: Pencil,
        onClick: (school) => router.visit(`/dashboard/schools/${school.uuid}/edit`),
    },
    {
        label: t('Delete'),
        icon: Trash2,
        onClick: (school) => router.visit(`/dashboard/schools/${school.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.schools.meta.current_page,
    last_page: props.schools.meta.last_page,
    per_page: props.schools.meta.per_page,
    total: props.schools.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    type: typeFilter.value !== 'all' ? typeFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/schools', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/schools', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/schools', getFilterParams(), { preserveState: true });
};

watch([statusFilter, typeFilter], () => {
    router.get('/dashboard/schools', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/schools/create');
};

const handleTrash = () => {
    router.visit('/dashboard/schools/trash');
};

const handleStatusToggle = (school: School, newStatus: boolean) => {
    router.put(`/dashboard/schools/${school.uuid}/toggle-status`, {
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
        <Head :title="t('Schools')" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    :title="t('Total Schools')"
                    :value="props.stats.total"
                    :icon="GraduationCap"
                />
                <StatsCard
                    :title="t('Active')"
                    :value="props.stats.active"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    :title="t('Inactive')"
                    :value="props.stats.inactive"
                    :icon="XCircle"
                    variant="warning"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">{{ t('Schools') }}</h2>
                        <p class="text-sm text-muted-foreground">{{ t('Manage schools and institutions') }}</p>
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
                        <Button @click="handleCreate">
                            <Plus class="mr-2 h-4 w-4" />
                            {{ t('Add School') }}
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            :placeholder="t('Search schools...')"
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Select v-model="typeFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue :placeholder="t('Type')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('All Types') }}</SelectItem>
                            <SelectItem v-for="type in typeOptions" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="statusFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue :placeholder="t('Status')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('All Status') }}</SelectItem>
                            <SelectItem value="1">{{ t('Active') }}</SelectItem>
                            <SelectItem value="0">{{ t('Inactive') }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Table -->
                <TableReusable
                    :data="props.schools.data"
                    :columns="columns"
                    :actions="actions"
                    :pagination="pagination"
                    :searchable="false"
                    @page-change="handlePageChange"
                    @per-page-change="handlePerPageChange"
                >
                    <template #cell-name="{ item }">
                        <div class="flex items-center gap-3">
                            <!-- Logo or fallback icon -->
                            <div v-if="item.logo" class="h-10 w-10 rounded-lg overflow-hidden border">
                                <img :src="item.logo" :alt="item.name" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <Building2 class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <div class="font-medium">{{ item.name }}</div>
                                <div v-if="item.email" class="text-xs text-muted-foreground">{{ item.email }}</div>
                            </div>
                        </div>
                    </template>
                    <template #cell-type="{ item }">
                        <Badge variant="outline">{{ item.type_label }}</Badge>
                    </template>
                    <template #cell-status="{ item }">
                        <div class="flex items-center gap-2" @click.stop>
                            <Switch
                                :model-value="item.status"
                                @update:model-value="handleStatusToggle(item, $event)"
                            />
                            <span class="text-sm text-muted-foreground">
                                {{ item.status ? t('Active') : t('Inactive') }}
                            </span>
                        </div>
                    </template>
                </TableReusable>
            </div>
        </div>
    </AppLayout>
</template>
