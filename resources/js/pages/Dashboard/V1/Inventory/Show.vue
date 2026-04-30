<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Pencil, Trash2, Package } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { InventoryShowProps } from '@school/types';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps<InventoryShowProps>();
const { __ } = useTranslation();

const breadcrumbs: BreadcrumbItem[] = [
    { title: __('Dashboard'), href: '/dashboard' },
    { title: __('Inventory'), href: '/dashboard/inventories' },
    { title: props.inventory.asset_tag, href: `/dashboard/inventories/${props.inventory.uuid}` },
];

const handleEdit = () => router.visit(`/dashboard/inventories/${props.inventory.uuid}/edit`);
const handleDelete = () => router.visit(`/dashboard/inventories/${props.inventory.uuid}/delete`);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="__('Inventory - :tag', { tag: inventory.asset_tag })" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="ghost" size="icon" as-child>
                        <Link href="/dashboard/inventories"><ArrowLeft class="h-4 w-4" /></Link>
                    </Button>
                    <div>
                        <h1 class="flex items-center gap-2 text-2xl font-bold tracking-tight">
                            <Package class="h-6 w-6 text-muted-foreground" />
                            {{ inventory.asset_tag }}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            {{ inventory.name || inventory.equipment?.name || __('Inventory item') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="handleEdit">
                        <Pencil class="mr-2 h-4 w-4" /> {{ __('Edit') }}
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2 class="mr-2 h-4 w-4" /> {{ __('Delete') }}
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader><CardTitle>{{ __('Identification') }}</CardTitle></CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Asset Tag') }}</span><span class="font-medium">{{ inventory.asset_tag }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Serial Number') }}</span><span>{{ inventory.serial_number || '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Equipment') }}</span><span>{{ inventory.equipment?.name || '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Status') }}</span><Badge>{{ inventory.status_label }}</Badge></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Condition') }}</span><Badge variant="outline">{{ inventory.condition_label }}</Badge></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Active') }}</span><span>{{ inventory.is_active ? __('Yes') : __('No') }}</span></div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>{{ __('Location') }}</CardTitle></CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Classroom') }}</span><span>{{ inventory.classroom?.name || '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Department') }}</span><span>{{ inventory.department?.name || '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Assigned To') }}</span><span>{{ inventory.assigned_to?.name || '—' }}</span></div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>{{ __('Acquisition') }}</CardTitle></CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Purchased At') }}</span><span>{{ inventory.purchased_at || '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Cost') }}</span><span>{{ inventory.cost ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-muted-foreground">{{ __('Vendor') }}</span><span>{{ inventory.vendor || '—' }}</span></div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">{{ __('Warranty Until') }}</span>
                            <span :class="inventory.warranty_expired ? 'text-destructive' : ''">
                                {{ inventory.warranty_until || '—' }}
                                <span v-if="inventory.warranty_expired" class="ml-1 text-xs">({{ __('expired') }})</span>
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="inventory.notes">
                    <CardHeader><CardTitle>{{ __('Notes') }}</CardTitle></CardHeader>
                    <CardContent class="text-sm text-muted-foreground whitespace-pre-line">
                        {{ inventory.notes }}
                    </CardContent>
                </Card>

                <Card v-if="inventory.images && inventory.images.length > 0" class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>{{ __('Photos & Documents (:count)', { count: inventory.images.length }) }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                            <a
                                v-for="(url, i) in inventory.images"
                                :key="i"
                                :href="url"
                                target="_blank"
                                rel="noopener"
                                class="block aspect-square overflow-hidden rounded-md border bg-muted hover:opacity-90"
                            >
                                <img :src="url" :alt="`Image ${i + 1}`" class="h-full w-full object-cover" />
                            </a>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
