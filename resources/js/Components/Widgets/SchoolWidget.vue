<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ChartContainer } from '@/components/ui/chart';
import {
    VisXYContainer,
    VisStackedBar,
    VisAxis,
    VisArea,
    VisLine,
} from '@unovis/vue';
import {
    GraduationCap,
    Building2,
    BookOpen,
    Users,
    Calendar,
    RefreshCw,
    ArrowUpRight,
    TrendingUp,
    Eye,
    Store,
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import type { ChartConfig } from '@/components/ui/chart';
import { useTranslation } from '@/composables/useTranslation';

const { __ } = useTranslation();

// Types
export interface SchoolMetrics {
    totalSchools: number;
    activeSchools: number;
    inactiveSchools: number;
    totalDepartments: number;
    totalPrograms: number;
    totalClassrooms: number;
    growthPercent: number;
}

export interface DepartmentBySchool {
    id: number;
    name: string;
    departments: number;
}

export interface GrowthTrendPoint {
    label: string;
    schools: number;
    departments: number;
    classrooms: number;
}

export interface RecentSchool {
    id: number;
    name: string;
    status: string;
    departments: number;
    programs: number;
    created_at: string;
}

export interface DashboardWidgetItem {
    id: number;
    name: string;
    module: string;
    chart_type: string | null;
    sort_order: number;
    status: boolean;
}

export interface SchoolWidgetProps {
    metrics: SchoolMetrics;
    departmentsBySchool: DepartmentBySchool[];
    growthTrend: GrowthTrendPoint[];
    recentSchools?: RecentSchool[];
    widgets?: DashboardWidgetItem[];
    dateRange?: string;
    loading?: boolean;
    showStats?: boolean;
    showDistribution?: boolean;
    showGrowth?: boolean;
    showRecent?: boolean;
}

const props = withDefaults(defineProps<SchoolWidgetProps>(), {
    widgets: () => [],
    dateRange: '30d',
    loading: false,
    showStats: true,
    showDistribution: true,
    showGrowth: true,
    showRecent: true,
});

const widgetByChartType = computed(() => {
    const map: Record<string, DashboardWidgetItem> = {};
    for (const w of props.widgets ?? []) {
        if (w.chart_type) map[w.chart_type] = w;
    }
    return map;
});

const orderOf = (chartType: string, fallback: number) => {
    return widgetByChartType.value[chartType]?.sort_order ?? fallback;
};

const enabled = (chartType: string, showProp: boolean) => {
    const widget = widgetByChartType.value[chartType];
    if (widget) return widget.status && showProp;
    return showProp;
};

const emit = defineEmits<{
    (e: 'dateRangeChange', value: string): void;
    (e: 'refresh'): void;
}>();

const selectedDateRange = ref(props.dateRange);

const dateRangeOptions = [
    { value: 'today', label: __('Today') },
    { value: '7d', label: __('Last 7 Days') },
    { value: '30d', label: __('Last 30 Days') },
    { value: '90d', label: __('Last 90 Days') },
    { value: 'year', label: __('This Year') },
];

// Chart configs
const growthChartConfig: ChartConfig = {
    schools: { label: __('Schools'), color: 'var(--chart-1)' },
    departments: { label: __('Departments'), color: 'var(--chart-2)' },
    classrooms: { label: __('Classrooms'), color: 'var(--chart-3)' },
};

// Computed
const growthIndicator = computed(() => ({
    isPositive: props.metrics.growthPercent >= 0,
    value: Math.abs(props.metrics.growthPercent),
}));

const maxDepartments = computed(() => {
    return Math.max(...props.departmentsBySchool.map(s => s.departments), 1);
});

watch(selectedDateRange, (newValue) => {
    emit('dateRangeChange', newValue);
});

const handleRefresh = () => {
    emit('refresh');
};

const formatNumber = (num: number) => {
    return new Intl.NumberFormat().format(num);
};

const formatPercent = (num: number) => {
    return `${num.toFixed(1)}%`;
};

const getStatusBadgeVariant = (status: string | boolean): 'default' | 'secondary' | 'destructive' | 'outline' => {
    // Handle boolean status (true = active, false = inactive)
    if (typeof status === 'boolean') {
        return status ? 'default' : 'secondary';
    }
    // Handle string status
    switch (status?.toLowerCase?.() ?? '') {
        case 'active':
            return 'default';
        case 'inactive':
            return 'secondary';
        default:
            return 'outline';
    }
};

const formatStatus = (status: string | boolean): string => {
    if (typeof status === 'boolean') {
        return status ? __('Active') : __('Inactive');
    }
    return status || __('Unknown');
};
</script>

<template>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Header with Date Filter -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between lg:col-span-2" :style="{ order: -1 }">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">{{ __('School Overview') }}</h2>
                <p class="text-sm text-muted-foreground">{{ __('Track schools, departments, and programs') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Select v-model="selectedDateRange">
                    <SelectTrigger class="w-[160px]">
                        <Calendar class="mr-2 h-4 w-4" />
                        <SelectValue :placeholder="__('Select period')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in dateRangeOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Button variant="outline" size="icon" @click="handleRefresh" :disabled="loading">
                    <RefreshCw class="h-4 w-4" :class="{ 'animate-spin': loading }" />
                </Button>
            </div>
        </div>

        <!-- Key Metrics Grid -->
        <div v-if="enabled('stats', showStats)" :style="{ order: orderOf('stats', 1) }" class="grid gap-4 md:grid-cols-2 lg:col-span-2 lg:grid-cols-3 xl:grid-cols-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ __('Total Schools') }}</CardTitle>
                    <GraduationCap class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ formatNumber(metrics.totalSchools) }}</div>
                    <div class="flex items-center text-xs">
                        <ArrowUpRight
                            v-if="growthIndicator.isPositive"
                            class="mr-1 h-3 w-3 text-green-500"
                        />
                        <span :class="growthIndicator.isPositive ? 'text-green-500' : 'text-red-500'">
                            {{ growthIndicator.isPositive ? '+' : '-' }}{{ formatPercent(growthIndicator.value) }}
                        </span>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ __('Active Schools') }}</CardTitle>
                    <TrendingUp class="h-4 w-4 text-green-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-green-600">{{ formatNumber(metrics.activeSchools) }}</div>
                    <p class="text-xs text-muted-foreground">
                        {{ formatNumber(metrics.inactiveSchools) }} {{ __('inactive') }}
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ __('Departments') }}</CardTitle>
                    <Building2 class="h-4 w-4 text-blue-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-blue-600">{{ formatNumber(metrics.totalDepartments) }}</div>
                    <p class="text-xs text-muted-foreground">{{ __('Across all schools') }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ __('Programs') }}</CardTitle>
                    <BookOpen class="h-4 w-4 text-purple-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-purple-600">{{ formatNumber(metrics.totalPrograms) }}</div>
                    <p class="text-xs text-muted-foreground">{{ __('Available programs') }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ __('Classrooms') }}</CardTitle>
                    <Store class="h-4 w-4 text-amber-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-amber-600">{{ formatNumber(metrics.totalClassrooms) }}</div>
                    <p class="text-xs text-muted-foreground">{{ __('Total classrooms') }}</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ __('Inactive') }}</CardTitle>
                    <GraduationCap class="h-4 w-4 text-gray-400" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-gray-600">{{ formatNumber(metrics.inactiveSchools) }}</div>
                    <p class="text-xs text-muted-foreground">{{ __('Needs attention') }}</p>
                </CardContent>
            </Card>
        </div>

        <!-- Departments by School -->
        <Card v-if="enabled('bar', showDistribution)" :style="{ order: orderOf('bar', 2) }">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Building2 class="h-5 w-5" />
                        {{ __('Departments by School') }}
                    </CardTitle>
                    <CardDescription>{{ __('Top schools by department count') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="school in departmentsBySchool" :key="school.id" class="flex items-center justify-between">
                            <span class="font-medium text-sm truncate max-w-[200px]">{{ school.name }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-32 h-2 bg-muted rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-primary rounded-full transition-all"
                                        :style="{ width: `${(school.departments / maxDepartments) * 100}%` }"
                                    ></div>
                                </div>
                                <span class="text-sm text-muted-foreground w-8 text-right">{{ school.departments }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

        <!-- Growth Trend Chart -->
        <Card v-if="enabled('area', showGrowth)" :style="{ order: orderOf('area', 3) }">
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <TrendingUp class="h-5 w-5" />
                    {{ __('Growth Trend (Last 6 Months)') }}
                </CardTitle>
                    <CardDescription>{{ __('New additions over time') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <ChartContainer :config="growthChartConfig" class="h-[280px]">
                        <VisXYContainer :data="growthTrend" :margin="{ top: 10, bottom: 30, left: 40, right: 10 }">
                            <VisStackedBar
                                :x="(_: GrowthTrendPoint, i: number) => i"
                                :y="[(d: GrowthTrendPoint) => d.schools, (d: GrowthTrendPoint) => d.departments, (d: GrowthTrendPoint) => d.classrooms]"
                                :color="[growthChartConfig.schools.color, growthChartConfig.departments.color, growthChartConfig.classrooms.color]"
                                :bar-padding="0.2"
                                :rounded-corners="4"
                            />
                            <VisAxis
                                type="x"
                                :tick-format="(i: number) => growthTrend[i]?.label || ''"
                            />
                            <VisAxis type="y" :num-ticks="5" />
                        </VisXYContainer>
                    </ChartContainer>
                    <div class="flex justify-center gap-4 mt-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-chart-1"></div>
                            <span class="text-sm">{{ __('Schools') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-chart-2"></div>
                            <span class="text-sm">{{ __('Departments') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-chart-3"></div>
                            <span class="text-sm">{{ __('Classrooms') }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

        <!-- Recent Schools -->
        <Card v-if="showRecent && recentSchools && recentSchools.length > 0" class="lg:col-span-2" :style="{ order: 999 }">
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            <GraduationCap class="h-5 w-5 text-primary" />
                            {{ __('Recent Schools') }}
                        </CardTitle>
                        <CardDescription>{{ __('Latest schools added to the system') }}</CardDescription>
                    </div>
                    <Link href="/dashboard/schools" class="text-sm text-primary hover:underline">
                        {{ __('View All') }}
                    </Link>
                </div>
            </CardHeader>
            <CardContent>
                <div class="space-y-3">
                    <div
                        v-for="school in recentSchools"
                        :key="school.id"
                        class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/50"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary font-medium">
                                {{ school.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium truncate">{{ school.name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ school.departments }} {{ __('departments') }}, {{ school.programs }} {{ __('programs') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <Badge :variant="getStatusBadgeVariant(school.status)">
                                {{ formatStatus(school.status) }}
                            </Badge>
                            <Link
                                :href="`/dashboard/schools/${school.id}`"
                                class="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                            >
                                <Eye class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
