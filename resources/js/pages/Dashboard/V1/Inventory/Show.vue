<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    ArrowLeft,
    Pencil,
    Trash2,
    Package,
    Tag,
    Info,
    MapPin,
    Calendar,
    DollarSign,
    Store,
    ShieldCheck,
    Clock,
    User,
    Building2,
    Phone,
    Mail,
    LayoutDashboard
} from 'lucide-vue-next';
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

const formatDate = (date: string | null) => {
    if (!date) return '—';
    try {
        const d = new Date(date);
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }).format(d);
    } catch (e) {
        return date;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="__('Inventory - :tag', { tag: inventory.asset_tag })" />

        <div class="flex h-full flex-1 flex-col gap-12 p-8 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="icon" as-child class="rounded-full hover:bg-muted">
                        <Link href="/dashboard/inventories"><ArrowLeft class="h-5 w-5" /></Link>
                    </Button>
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex p-3 bg-primary/5 rounded-2xl text-primary/60">
                            <Package class="h-8 w-8" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <h1 class="text-3xl font-extrabold tracking-tight">{{ inventory.asset_tag }}</h1>
                                <Badge :variant="inventory.is_active ? 'default' : 'secondary'" class="uppercase tracking-wider px-2 py-0.5 text-[10px]">
                                    {{ inventory.is_active ? __('Active') : __('Inactive') }}
                                </Badge>
                            </div>
                            <p class="text-muted-foreground font-medium flex items-center gap-1.5">
                                {{ inventory.name || inventory.equipment?.name || __('Inventory unit') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <Button variant="outline" @click="handleEdit" class="rounded-xl px-5 border-muted-foreground/20">
                        <Pencil class="mr-2 h-4 w-4" /> {{ __('Edit') }}
                    </Button>
                    <Button variant="destructive" @click="handleDelete" class="rounded-xl px-5 shadow-none">
                        <Trash2 class="mr-2 h-4 w-4" /> {{ __('Delete') }}
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                <!-- Main Information -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- Identification Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 px-1">
                            <Tag class="h-4 w-4 text-primary/60" />
                            <h4 class="text-xs font-bold uppercase tracking-widest text-muted-foreground">{{ __('Asset Identification') }}</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 px-1">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Asset Tag') }}</p>
                                <p class="font-bold text-xl">{{ inventory.asset_tag }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Serial Number') }}</p>
                                <p class="font-medium text-lg">{{ inventory.serial_number || '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Equipment Type') }}</p>
                                <p class="font-medium flex items-center gap-2">
                                    {{ inventory.equipment?.name || '—' }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Category') }}</p>
                                <p class="font-medium">{{ inventory.equipment?.category_label || '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Status') }}</p>
                                <Badge variant="secondary" class="font-bold">{{ __(inventory.status_label) }}</Badge>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Condition') }}</p>
                                <Badge variant="outline" class="font-bold border-muted-foreground/20">{{ __(inventory.condition_label) }}</Badge>
                            </div>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 px-1">
                            <MapPin class="h-4 w-4 text-primary/60" />
                            <h4 class="text-xs font-bold uppercase tracking-widest text-muted-foreground">{{ __('Deployment Details') }}</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 px-1">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('School / Site') }}</p>
                                <p class="font-medium text-lg">{{ inventory.department?.school_name || '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Department') }}</p>
                                <p class="font-medium text-lg">{{ inventory.department?.name || '—' }}</p>
                                <div v-if="inventory.department" class="flex items-center gap-4 mt-1 opacity-60">
                                    <p v-if="inventory.department.phone" class="text-xs flex items-center gap-1">
                                        <Phone class="h-3 w-3" /> {{ inventory.department.phone }}
                                    </p>
                                    <p v-if="inventory.department.email" class="text-xs flex items-center gap-1">
                                        <Mail class="h-3 w-3" /> {{ inventory.department.email }}
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Classroom / Office') }}</p>
                                <p class="font-medium text-lg">{{ inventory.classroom?.name || '—' }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Assigned Personnel') }}</p>
                                <p class="font-medium text-lg">{{ inventory.assigned_to?.name || '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div v-if="inventory.notes" class="space-y-6">
                        <div class="flex items-center gap-2 px-1">
                            <Info class="h-4 w-4 text-primary/60" />
                            <h4 class="text-xs font-bold uppercase tracking-widest text-muted-foreground">{{ __('Internal Notes') }}</h4>
                        </div>
                        <div class="text-base text-foreground/80 leading-relaxed whitespace-pre-line px-1">
                            {{ inventory.notes }}
                        </div>
                    </div>

                    <!-- Photos Section -->
                    <div v-if="inventory.images && inventory.images.length > 0" class="space-y-6">
                        <div class="flex items-center gap-2 px-1">
                            <Package class="h-4 w-4 text-primary/60" />
                            <h4 class="text-xs font-bold uppercase tracking-widest text-muted-foreground">{{ __('Photos & Documents') }}</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 px-1">
                            <a
                                v-for="(url, i) in inventory.images"
                                :key="i"
                                :href="url"
                                target="_blank"
                                rel="noopener"
                                class="group relative aspect-square overflow-hidden rounded-xl border border-muted-foreground/10 bg-muted/30"
                            >
                                <img :src="url" :alt="`Image ${i + 1}`" class="h-full w-full object-cover grayscale-[0.5] group-hover:grayscale-0 transition-all duration-500" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Side Information -->
                <div class="space-y-12">
                    <!-- Acquisition Details -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 px-1">
                            <Calendar class="h-4 w-4 text-primary/60" />
                            <h4 class="text-xs font-bold uppercase tracking-widest text-muted-foreground">{{ __('Financials') }}</h4>
                        </div>
                        <div class="space-y-6 px-1">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Purchased At') }}</span>
                                <span class="font-bold text-lg">{{ formatDate(inventory.purchased_at) }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Unit Cost') }}</span>
                                <span class="font-bold text-2xl text-primary">{{ inventory.cost ? `$${inventory.cost}` : '—' }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Vendor') }}</span>
                                <span class="font-medium text-lg">{{ inventory.vendor || '—' }}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-muted-foreground/60 uppercase tracking-widest">{{ __('Warranty Until') }}</span>
                                <span :class="['font-bold text-lg', inventory.warranty_expired ? 'text-destructive' : 'text-foreground']">
                                    {{ formatDate(inventory.warranty_until) }}
                                    <span v-if="inventory.warranty_expired" class="ml-1 text-[10px] uppercase font-black">({{ __('expired') }})</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- System Metadata -->
                    <div class="space-y-4 px-1 pt-6 border-t border-muted-foreground/10">
                        <div class="flex items-center justify-between text-[11px] opacity-40">
                            <span class="font-bold uppercase tracking-tighter">{{ __('Created') }}</span>
                            <span class="font-medium">{{ formatDate(inventory.created_at) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] opacity-40">
                            <span class="font-bold uppercase tracking-tighter">{{ __('Last Updated') }}</span>
                            <span class="font-medium">{{ formatDate(inventory.updated_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
