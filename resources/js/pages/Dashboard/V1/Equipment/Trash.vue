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
const { __ } = useTranslation();

const selectedUuids = ref<(string | number)[]>([]);

const breadcrumbs: BreadcrumbItem[] = [
    { title: __('Dashboard'), href: '/dashboard' },
    { title: __('Equipment'), href: '/dashboard/equipment' },
    { title: __('Trash'), href: '/dashboard/equipment/trash' },
];

const trashConfig: TrashConfigLocal = {
    entityLabel: __('Equipment'),
    entityLabelPlural: __('Equipment'),
    restoreRoute: (uuid: string) => `/dashboard/equipment/${uuid}/restore`,
    forceDeleteRoute: (uuid: string) => `/dashboard/equipment/${uuid}/force-delete`,
    listRoute: '/dashboard/equipment/trash',
};

const handleAll = () => {
    router.visit('/dashboard/equipment');
};

const handlePageChange = (page: number) => {
    router.get('/dashboard/equipment/trash', {
        page,
        per_page: props.trashItems.meta?.per_page || 10,
        search: props.filters.search,
    }, { preserveState: true });
};

const handleSearch = (query: string) => {
    router.get('/dashboard/equipment/trash', {
        search: query || undefined,
        per_page: props.trashItems.meta?.per_page || 10,
    }, { preserveState: true });
};

const handleBulkRestore = () => {
    router.put('/dashboard/equipment/trash/bulk-restore', {
        uuids: selectedUuids.value,
    }, {
        preserveState: false,
        onSuccess: () => {
            selectedUuids.value = [];
        },
    });
};

const handleBulkForceDelete = () => {
    if (confirm(__('Are you sure you want to permanently delete :count item(s)? This action cannot be undone.', { count: selectedUuids.value.length }))) {
        router.delete('/dashboard/equipment/trash/bulk-force-delete', {
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
        <Head :title="__('Equipment Trash')" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ __('Equipment Trash') }}</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ __('Manage deleted equipment - restore or permanently delete') }}
                    </p>
                </div>
                <ButtonGroup>
                    <Button variant="outline" @click="handleAll">
                        <Database class="mr-2 h-4 w-4" />
                        {{ __('All') }}
                    </Button>
                    <Button variant="default">
                        <Trash2 class="mr-2 h-4 w-4" />
                        {{ __('Trash') }}
                    </Button>
                </ButtonGroup>
            </div>

            <!-- Trash Table -->
            <TrashTable
                v-model:selected="selectedUuids"
                :items="trashItems.data"
                :config="trashConfig"
                :pagination="trashItems.meta"
                :show-type="false"
                :selectable="true"
                select-key="uuid"
                :empty-message="__('No deleted equipment found.')"
                empty-trash-route="/dashboard/equipment/trash/empty"
                @page-change="handlePageChange"
                @search="handleSearch"
            >
                <template #bulk-actions>
                    <Button variant="outline" size="sm" @click="handleBulkRestore">
                        <RotateCcw class="mr-2 h-4 w-4" />
                        {{ __('Restore Selected') }}
                    </Button>
                    <Button variant="destructive" size="sm" @click="handleBulkForceDelete">
                        <Trash2 class="mr-2 h-4 w-4" />
                        {{ __('Delete Permanently') }}
                    </Button>
                </template>
            </TrashTable>
        </div>
    </AppLayout>
</template>
