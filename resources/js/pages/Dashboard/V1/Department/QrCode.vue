<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { ButtonGroup } from '@/components/shared';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    ArrowLeft,
    Printer,
    FileDown,
    FileImage,
    FileType,
    QrCode as QrCodeIcon,
    MapPin,
    Shield,
    AlertTriangle,
    CheckCircle,
    ExternalLink,
    Settings2,
    Palette,
    Maximize2,
    RefreshCw,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { onMounted, ref, computed, watch } from 'vue';
import QRCode from 'qrcode';

interface LocationData {
    id: number;
    uuid: string;
    name: string;
    code: string | null;
    type: string;
    geofence_type: string;
    geofence_radius: number;
    enforce_geofence: boolean;
    latitude: number;
    longitude: number;
    is_active: boolean;
    city: string | null;
}

interface Props {
    department: {
        id: number;
        uuid: string;
        name: string;
        code: string | null;
        school_name: string | null;
    };
    location: LocationData | null;
    qrData: string;
}

const props = defineProps<Props>();

// QR Code Customization Options
const qrSize = ref<string>('medium'); // small, medium, large, custom
const qrCustomSize = ref<number>(300);
const qrForegroundColor = ref<string>('#000000');
const qrBackgroundColor = ref<string>('#ffffff');
const qrErrorCorrection = ref<string>('M'); // L, M, Q, H
const qrIncludeTitle = ref<boolean>(true);
const qrIncludeInstructions = ref<boolean>(true);
const qrIncludeBorder = ref<boolean>(true);

// Size options mapping
const sizeOptions = {
    small: 150,
    medium: 250,
    large: 350,
    custom: qrCustomSize.value,
};

const actualQrSize = computed(() => {
    if (qrSize.value === 'custom') return qrCustomSize.value;
    return sizeOptions[qrSize.value as keyof typeof sizeOptions] || 250;
});

// Error correction level descriptions
const errorCorrectionLevels = [
    { value: 'L', label: 'Low (7%)', description: 'Smallest QR, less recovery' },
    { value: 'M', label: 'Medium (15%)', description: 'Balanced size and recovery' },
    { value: 'Q', label: 'Quartile (25%)', description: 'Good recovery, larger QR' },
    { value: 'H', label: 'High (30%)', description: 'Best recovery, largest QR' },
];

// Computed properties for location status
const hasLocation = computed(() => props.location !== null);
const isLocationActive = computed(() => props.location?.is_active ?? false);
const isGeofenceEnforced = computed(() => props.location?.enforce_geofence ?? false);

const formatGeofenceType = (type: string): string => {
    const types: Record<string, string> = {
        circle: 'Circle',
        polygon: 'Polygon',
        dynamic: 'Dynamic',
    };
    return types[type] || type;
};

const formatLocationType = (type: string): string => {
    const types: Record<string, string> = {
        office: 'Office',
        branch: 'Branch',
        site: 'Site',
        remote: 'Remote',
        other: 'Other',
    };
    return types[type] || type;
};

const openInMaps = () => {
    if (props.location) {
        window.open(`https://www.google.com/maps?q=${props.location.latitude},${props.location.longitude}`, '_blank');
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Departments', href: '/dashboard/departments' },
    { title: props.department.name, href: `/dashboard/departments/${props.department.uuid}` },
    { title: 'QR Code', href: `/dashboard/departments/${props.department.uuid}/qr-code` },
];

const qrCodeDataUrl = ref<string>('');
const qrCodeSvg = ref<string>('');
const isGenerating = ref<boolean>(false);

const generateQrCode = async () => {
    isGenerating.value = true;
    try {
        const options = {
            width: actualQrSize.value,
            margin: 2,
            errorCorrectionLevel: qrErrorCorrection.value as 'L' | 'M' | 'Q' | 'H',
            color: {
                dark: qrForegroundColor.value,
                light: qrBackgroundColor.value,
            },
        };

        // Generate QR code as data URL for display
        qrCodeDataUrl.value = await QRCode.toDataURL(props.qrData, options);

        // Generate QR code as SVG for better print quality
        qrCodeSvg.value = await QRCode.toString(props.qrData, {
            type: 'svg',
            width: actualQrSize.value,
            margin: 2,
            errorCorrectionLevel: qrErrorCorrection.value as 'L' | 'M' | 'Q' | 'H',
            color: {
                dark: qrForegroundColor.value,
                light: qrBackgroundColor.value,
            },
        });
    } catch (error) {
        console.error('Failed to generate QR code:', error);
    } finally {
        isGenerating.value = false;
    }
};

// Watch for option changes and regenerate
watch(
    [qrSize, qrCustomSize, qrForegroundColor, qrBackgroundColor, qrErrorCorrection],
    () => {
        generateQrCode();
    }
);

onMounted(() => {
    generateQrCode();
});

// Reset to defaults
const resetToDefaults = () => {
    qrSize.value = 'medium';
    qrCustomSize.value = 300;
    qrForegroundColor.value = '#000000';
    qrBackgroundColor.value = '#ffffff';
    qrErrorCorrection.value = 'M';
    qrIncludeTitle.value = true;
    qrIncludeInstructions.value = true;
    qrIncludeBorder.value = true;
};

const handleBack = () => {
    router.visit(`/dashboard/departments/${props.department.uuid}`);
};

const handlePrint = () => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        const scriptTag = '<' + 'script>window.onload = function() { window.print(); window.close(); }</' + 'script>';
        const borderStyle = qrIncludeBorder.value ? 'border: 2px solid #000; padding: 30px; border-radius: 10px;' : '';

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Code - ${props.department.name}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                        box-sizing: border-box;
                    }
                    .container {
                        text-align: center;
                        ${borderStyle}
                    }
                    .title {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                    .subtitle {
                        font-size: 16px;
                        color: #666;
                        margin-bottom: 20px;
                    }
                    .qr-code {
                        margin: 20px 0;
                    }
                    .qr-code svg {
                        width: ${actualQrSize.value}px;
                        height: ${actualQrSize.value}px;
                    }
                    .instruction {
                        font-size: 14px;
                        color: #333;
                        margin-top: 20px;
                        padding: 10px;
                        background: #f5f5f5;
                        border-radius: 5px;
                    }
                    .code {
                        font-size: 12px;
                        color: #999;
                        margin-top: 10px;
                    }
                    @media print {
                        body { margin: 0; padding: 0; }
                        ${qrIncludeBorder.value ? '.container { border: 2px solid #000; }' : ''}
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    ${qrIncludeTitle.value ? `
                        <div class="title">${props.department.name}</div>
                        <div class="subtitle">${props.department.school_name || ''}</div>
                    ` : ''}
                    <div class="qr-code">${qrCodeSvg.value}</div>
                    ${qrIncludeInstructions.value ? `
                        <div class="instruction">
                            Scan this QR code to record your attendance
                        </div>
                        <div class="code">Code: ${props.department.code || 'N/A'}</div>
                    ` : ''}
                </div>
                ${scriptTag}
            </body>
            </html>
        `);
        printWindow.document.close();
    }
};

const handleDownload = () => {
    if (!qrCodeDataUrl.value) return;

    const link = document.createElement('a');
    link.download = `qr-code-${props.department.code || props.department.uuid}.png`;
    link.href = qrCodeDataUrl.value;
    link.click();
};

const handleDownloadSvg = () => {
    if (!qrCodeSvg.value) return;

    const blob = new Blob([qrCodeSvg.value], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.download = `qr-code-${props.department.code || props.department.uuid}.svg`;
    link.href = url;
    link.click();
    URL.revokeObjectURL(url);
};

const handleExportPdf = () => {
    const pdfWindow = window.open('', '_blank');
    if (pdfWindow) {
        const borderStyle = qrIncludeBorder.value ? 'border: 3px solid #000; padding: 40px; border-radius: 15px;' : '';

        pdfWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Code - ${props.department.name}</title>
                <style>
                    @page {
                        size: A4;
                        margin: 20mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 40px;
                        box-sizing: border-box;
                    }
                    .container {
                        text-align: center;
                        ${borderStyle}
                        max-width: 400px;
                    }
                    .title {
                        font-size: 28px;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                    .subtitle {
                        font-size: 18px;
                        color: #666;
                        margin-bottom: 25px;
                    }
                    .qr-code {
                        margin: 25px 0;
                    }
                    .qr-code img {
                        width: ${actualQrSize.value}px;
                        height: ${actualQrSize.value}px;
                    }
                    .instruction {
                        font-size: 16px;
                        color: #333;
                        margin-top: 25px;
                        padding: 15px;
                        background: #f5f5f5;
                        border-radius: 8px;
                    }
                    .code {
                        font-size: 14px;
                        color: #666;
                        margin-top: 15px;
                    }
                    .footer {
                        margin-top: 20px;
                        font-size: 12px;
                        color: #999;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    ${qrIncludeTitle.value ? `
                        <div class="title">${props.department.name}</div>
                        <div class="subtitle">${props.department.school_name || ''}</div>
                    ` : ''}
                    <div class="qr-code">
                        <img src="${qrCodeDataUrl.value}" alt="QR Code" />
                    </div>
                    ${qrIncludeInstructions.value ? `
                        <div class="instruction">
                            Scan this QR code to record your attendance
                        </div>
                        <div class="code">Department Code: ${props.department.code || 'N/A'}</div>
                    ` : ''}
                    <div class="footer">Generated on ${new Date().toLocaleDateString()}</div>
                </div>
            </body>
            </html>
        `);
        pdfWindow.document.close();

        // Wait for image to load then trigger print (save as PDF)
        setTimeout(() => {
            pdfWindow.print();
        }, 500);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`QR Code - ${department.name}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="icon" @click="handleBack">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">Department QR Code</h1>
                        <p class="text-sm text-muted-foreground">
                            Print and place at department entrance for attendance tracking
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <!-- Download ButtonGroup -->
                    <ButtonGroup>
                        <Button variant="outline" size="sm" @click="handleDownload">
                            <FileImage class="mr-2 h-4 w-4" />
                            PNG
                        </Button>
                        <Button variant="outline" size="sm" @click="handleDownloadSvg">
                            <FileType class="mr-2 h-4 w-4" />
                            SVG
                        </Button>
                        <Button variant="outline" size="sm" @click="handleExportPdf">
                            <FileDown class="mr-2 h-4 w-4" />
                            PDF
                        </Button>
                    </ButtonGroup>
                    <Button size="sm" @click="handlePrint">
                        <Printer class="mr-2 h-4 w-4" />
                        Print
                    </Button>
                </div>
            </div>

            <!-- Location Status Alert -->
            <div v-if="!hasLocation" class="max-w-2xl mx-auto">
                <Alert variant="destructive">
                    <AlertTriangle class="h-4 w-4" />
                    <AlertTitle>No Location Linked</AlertTitle>
                    <AlertDescription class="mt-2">
                        <p>This department has no scan location linked. Geofence verification will not be performed.</p>
                        <Button variant="outline" size="sm" class="mt-3" as-child>
                            <Link :href="`/dashboard/departments/${department.uuid}/edit`">
                                Link a Location
                            </Link>
                        </Button>
                    </AlertDescription>
                </Alert>
            </div>

            <!-- QR Code, Options, and Location Info Grid -->
            <div class="grid gap-6 lg:grid-cols-3 max-w-6xl mx-auto">
                <!-- QR Code Card -->
                <Card>
                    <CardHeader class="text-center pb-2">
                        <CardTitle class="flex items-center justify-center gap-2 text-base">
                            <QrCodeIcon class="h-5 w-5" />
                            {{ department.name }}
                        </CardTitle>
                        <CardDescription v-if="department.school_name">
                            {{ department.school_name }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col items-center space-y-4">
                        <!-- QR Code Display -->
                        <div class="rounded-lg border bg-white p-4 relative">
                            <div v-if="isGenerating" class="absolute inset-0 flex items-center justify-center bg-white/80 rounded-lg">
                                <RefreshCw class="h-6 w-6 animate-spin text-primary" />
                            </div>
                            <img
                                v-if="qrCodeDataUrl"
                                :src="qrCodeDataUrl"
                                :alt="`QR Code for ${department.name}`"
                                :style="{ width: actualQrSize + 'px', height: actualQrSize + 'px', maxWidth: '100%' }"
                            />
                            <div v-else class="flex h-64 w-64 items-center justify-center">
                                <span class="text-muted-foreground">Generating QR Code...</span>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="rounded-lg bg-muted p-4 text-center w-full">
                            <p class="text-sm font-medium">How to use:</p>
                            <ol class="mt-2 text-sm text-muted-foreground text-left list-decimal list-inside space-y-1">
                                <li>Print this QR code</li>
                                <li>Place at department entrance</li>
                                <li>Employees scan to check-in/out</li>
                                <li>Attendance is automatically recorded</li>
                            </ol>
                        </div>

                        <!-- Department Info -->
                        <div class="text-center text-sm text-muted-foreground">
                            <p v-if="department.code">Code: {{ department.code }}</p>
                        </div>

                        <!-- Download Buttons -->
                        <div class="flex flex-col gap-2 w-full pt-2">
                            <div class="grid grid-cols-3 gap-2">
                                <Button variant="outline" size="sm" @click="handleDownload" class="w-full">
                                    <FileImage class="mr-1 h-4 w-4" />
                                    PNG
                                </Button>
                                <Button variant="outline" size="sm" @click="handleDownloadSvg" class="w-full">
                                    <FileType class="mr-1 h-4 w-4" />
                                    SVG
                                </Button>
                                <Button variant="outline" size="sm" @click="handleExportPdf" class="w-full">
                                    <FileDown class="mr-1 h-4 w-4" />
                                    PDF
                                </Button>
                            </div>
                            <Button @click="handlePrint" class="w-full">
                                <Printer class="mr-2 h-4 w-4" />
                                Print QR Code
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Customization Options Card -->
                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Settings2 class="h-4 w-4" />
                                Customize QR Code
                            </CardTitle>
                            <Button variant="ghost" size="sm" @click="resetToDefaults" class="h-8 text-xs">
                                Reset
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Size Options -->
                        <div class="space-y-2">
                            <Label class="flex items-center gap-2 text-xs text-muted-foreground">
                                <Maximize2 class="h-3 w-3" />
                                Size
                            </Label>
                            <Select v-model="qrSize">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select size" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="small">Small (150px)</SelectItem>
                                    <SelectItem value="medium">Medium (250px)</SelectItem>
                                    <SelectItem value="large">Large (350px)</SelectItem>
                                    <SelectItem value="custom">Custom</SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="qrSize === 'custom'" class="flex items-center gap-2">
                                <Input
                                    v-model.number="qrCustomSize"
                                    type="number"
                                    :min="100"
                                    :max="500"
                                    class="h-8"
                                />
                                <span class="text-xs text-muted-foreground">px</span>
                            </div>
                        </div>

                        <!-- Error Correction Level -->
                        <div class="space-y-2">
                            <Label class="flex items-center gap-2 text-xs text-muted-foreground">
                                <Shield class="h-3 w-3" />
                                Error Correction
                            </Label>
                            <Select v-model="qrErrorCorrection">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select level" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="level in errorCorrectionLevels"
                                        :key="level.value"
                                        :value="level.value"
                                    >
                                        <div class="flex flex-col">
                                            <span>{{ level.label }}</span>
                                            <span class="text-xs text-muted-foreground">{{ level.description }}</span>
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Color Options -->
                        <div class="space-y-2">
                            <Label class="flex items-center gap-2 text-xs text-muted-foreground">
                                <Palette class="h-3 w-3" />
                                Colors
                            </Label>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <Label class="text-xs">Foreground</Label>
                                    <div class="flex items-center gap-2">
                                        <Input
                                            v-model="qrForegroundColor"
                                            type="color"
                                            class="h-8 w-12 p-1 cursor-pointer"
                                        />
                                        <Input
                                            v-model="qrForegroundColor"
                                            type="text"
                                            class="h-8 flex-1 font-mono text-xs"
                                        />
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs">Background</Label>
                                    <div class="flex items-center gap-2">
                                        <Input
                                            v-model="qrBackgroundColor"
                                            type="color"
                                            class="h-8 w-12 p-1 cursor-pointer"
                                        />
                                        <Input
                                            v-model="qrBackgroundColor"
                                            type="text"
                                            class="h-8 flex-1 font-mono text-xs"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Print Options -->
                        <div class="space-y-3 pt-2 border-t">
                            <Label class="text-xs text-muted-foreground">Print/Export Options</Label>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm">Include Title</Label>
                                    <Switch v-model:checked="qrIncludeTitle" />
                                </div>
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm">Include Instructions</Label>
                                    <Switch v-model:checked="qrIncludeInstructions" />
                                </div>
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm">Include Border</Label>
                                    <Switch v-model:checked="qrIncludeBorder" />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Location Info Card -->
                <Card v-if="hasLocation && location">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2 text-base">
                                <MapPin class="h-4 w-4" />
                                Linked Location
                            </CardTitle>
                            <Badge :variant="isLocationActive ? 'default' : 'secondary'" class="gap-1">
                                <CheckCircle v-if="isLocationActive" class="h-3 w-3" />
                                {{ isLocationActive ? 'Active' : 'Inactive' }}
                            </Badge>
                        </div>
                        <CardDescription>
                            Geofence configuration for attendance verification
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Location Name -->
                        <div class="rounded-lg border p-4">
                            <h3 class="font-medium">{{ location.name }}</h3>
                            <p class="text-sm text-muted-foreground mt-1">
                                {{ formatLocationType(location.type) }}
                                <template v-if="location.city"> · {{ location.city }}</template>
                            </p>
                        </div>

                        <!-- Geofence Details -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="rounded-lg bg-muted/50 p-3">
                                <p class="text-xs text-muted-foreground mb-1">Geofence Type</p>
                                <p class="font-medium">{{ formatGeofenceType(location.geofence_type) }}</p>
                            </div>
                            <div class="rounded-lg bg-muted/50 p-3">
                                <p class="text-xs text-muted-foreground mb-1">Radius</p>
                                <p class="font-medium">{{ location.geofence_radius }}m</p>
                            </div>
                            <div class="rounded-lg bg-muted/50 p-3 col-span-2">
                                <p class="text-xs text-muted-foreground mb-1">Coordinates</p>
                                <p class="font-mono text-xs">
                                    {{ location.latitude.toFixed(6) }}, {{ location.longitude.toFixed(6) }}
                                </p>
                            </div>
                        </div>

                        <!-- Enforcement Status -->
                        <div :class="[
                            'flex items-center gap-3 rounded-lg border p-3',
                            isGeofenceEnforced ? 'border-green-200 bg-green-50 dark:border-green-900 dark:bg-green-950' : 'border-yellow-200 bg-yellow-50 dark:border-yellow-900 dark:bg-yellow-950'
                        ]">
                            <Shield :class="[
                                'h-5 w-5',
                                isGeofenceEnforced ? 'text-green-600' : 'text-yellow-600'
                            ]" />
                            <div>
                                <p :class="['text-sm font-medium', isGeofenceEnforced ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400']">
                                    {{ isGeofenceEnforced ? 'Geofence Enforced' : 'Geofence Optional' }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ isGeofenceEnforced
                                        ? 'Employees must be inside the geofence to scan'
                                        : 'Scanning allowed anywhere (location recorded only)'
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 pt-2">
                            <Button variant="outline" size="sm" @click="openInMaps" class="flex-1">
                                <ExternalLink class="h-4 w-4 mr-2" />
                                View on Map
                            </Button>
                            <Button variant="outline" size="sm" as-child class="flex-1">
                                <Link :href="`/dashboard/locations/${location.uuid}/edit`">
                                    Edit Location
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- No Location Card (when no location linked) -->
                <Card v-else>
                    <CardContent class="flex flex-col items-center justify-center py-12">
                        <MapPin class="h-12 w-12 text-muted-foreground mb-4" />
                        <h3 class="font-medium mb-1">No Location Linked</h3>
                        <p class="text-sm text-muted-foreground mb-4 text-center">
                            Link a scan location to enable geofence verification for this department.
                        </p>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/dashboard/departments/${department.uuid}/edit`">
                                    Edit Department
                                </Link>
                            </Button>
                            <Button size="sm" as-child>
                                <Link href="/dashboard/locations/create">
                                    Create Location
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
