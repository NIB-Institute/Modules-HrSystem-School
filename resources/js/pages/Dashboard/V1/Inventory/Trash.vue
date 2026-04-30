<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TrashTable, ButtonGroup } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Database, Trash2, RotateCcw } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { TrashPaginationData, TrashConfigLocal, TrashConfig } from '@/types/trash';
import { useTranslation } from '@/composables/useTranslation';

interface Props {
    trashItems: TrashPaginationData;
    config: TrashConfig;
    filters: {
        search?: string;
        per_page?: number;
    };
}

const props = defineProps<Props>();
const { t } = useTranslation();

const selectedUuids = ref<(string | number)[]>([]);

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('Dashboard'), href: '/dashboard' },
    { title: t('Inventory'), href: '/dashboard/inventories' },
    { title: t('Trash'), href: '/dashboard/inventories/trash' },
];

const trashConfig: TrashConfigLocal = {
    entityLabel: t('Item'),
    entityLabelPlural: t('Items'),
    restoreRoute: (uuid: string) => `/dashboard/inventories/${uuid}/restore`,
    forceDeleteRoute: (uuid: string) => `/dashboard/inventories/${uuid}/force-delete`,
    listRoute: '/dashboard/inventories/trash',
};

const handleAll = () => {
    router.visit('/dashboard/inventories');
};

const handlePageChange = (page: number) => {
    router.get('/dashboard/inventories/trash', {
        page,
        per_page: props.trashItems.meta?.per_page || 10,
        search: props.filters.search,
    }, { preserveState: true });
};

const handleSearch = (query: string) => {
    router.get('/dashboard/inventories/trash', {
        search: query || undefined,
        per_page: props.trashItems.meta?.per_page || 10,
    }, { preserveState: true });
};

const handleBulkRestore = () => {
    router.put('/dashboard/inventories/trash/bulk-restore', {
        uuids: selectedUuids.value,
    }, {
        preserveState: false,
        onSuccess: () => {
            selectedUuids.value = [];
        },
    });
};

const handleBulkForceDelete = () => {
    if (confirm(t('Are you sure you want to permanently delete :count item(s)? This action cannot be undone.', { count: selectedUuids.value.length }))) {
        router.delete('/dashboard/inventories/trash/bulk-force-delete', {
            data: { uuids: selectedUuids.value },
            preserveState: false,
            onSuccess: () => {
                selectedUuids.value = [];
            },
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="t('Inventory Trash')" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ t('Inventory Trash') }}</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ t('Manage deleted inventory items - restore or permanently delete') }}
                    </p>
                </div>
                <ButtonGroup>
                    <Button variant="outline" @click="handleAll">
                        <Database class="mr-2 h-4 w-4" />
                        {{ t('All') }}
                    </Button>
                    <Button variant="default">
                        <Trash2 class="mr-2 h-4 w-4" />
                        {{ t('Trash') }}
                    </Button>
                </ButtonGroup>
            </div>

            <TrashTable
                v-model:selected="selectedUuids"
                :items="trashItems.data"
                :config="trashConfig"
                :pagination="trashItems.meta"
                :show-type="false"
                :selectable="true"
                select-key="uuid"
                :empty-message="t('No deleted items found.')"
                empty-trash-route="/dashboard/inventories/trash/empty"
                @page-change="handlePageChange"
                @search="handleSearch"
            >
                <template #bulk-actions>
                    <Button variant="outline" size="sm" @click="handleBulkRestore">
                        <RotateCcw class="mr-2 h-4 w-4" />
                        {{ t('Restore Selected') }}
                    </Button>
                    <Button variant="destructive" size="sm" @click="handleBulkForceDelete">
                        <Trash2 class="mr-2 h-4 w-4" />
                        {{ t('Delete Permanently') }}
                    </Button>
                </template>
            </TrashTable>
        </div>
    </AppLayout>
</template>
