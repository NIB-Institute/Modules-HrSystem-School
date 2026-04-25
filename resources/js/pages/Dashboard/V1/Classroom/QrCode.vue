<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { ArrowLeft, Printer, Download, FileDown, QrCode as QrCodeIcon, DoorOpen } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { onMounted, ref } from 'vue';
import QRCode from 'qrcode';

interface Props {
    classroom: {
        id: number;
        uuid: string;
        name: string;
        code: string | null;
        building: string | null;
        floor: number | null;
        full_location: string | null;
        department_name: string | null;
        school_name: string | null;
    };
    qrData: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Classrooms', href: '/dashboard/classrooms' },
    { title: props.classroom.name, href: `/dashboard/classrooms/${props.classroom.uuid}` },
    { title: 'QR Code', href: `/dashboard/classrooms/${props.classroom.uuid}/qr-code` },
];

const qrCodeDataUrl = ref<string>('');
const qrCodeSvg = ref<string>('');

onMounted(async () => {
    try {
        // Generate QR code as data URL for display
        qrCodeDataUrl.value = await QRCode.toDataURL(props.qrData, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff',
            },
        });

        // Generate QR code as SVG for better print quality
        qrCodeSvg.value = await QRCode.toString(props.qrData, {
            type: 'svg',
            width: 300,
            margin: 2,
        });
    } catch (error) {
        console.error('Failed to generate QR code:', error);
    }
});

const handleBack = () => {
    router.visit(`/dashboard/classrooms/${props.classroom.uuid}`);
};

const handlePrint = () => {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
        const scriptTag = '<' + 'script>window.onload = function() { window.print(); window.close(); }</' + 'script>';
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Code - ${props.classroom.name}</title>
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
                        border: 2px solid #000;
                        padding: 30px;
                        border-radius: 10px;
                    }
                    .title {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .location {
                        font-size: 16px;
                        color: #333;
                        margin-bottom: 5px;
                    }
                    .subtitle {
                        font-size: 14px;
                        color: #666;
                        margin-bottom: 20px;
                    }
                    .qr-code {
                        margin: 20px 0;
                    }
                    .qr-code svg {
                        width: 250px;
                        height: 250px;
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
                        .container { border: 2px solid #000; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="title">${props.classroom.name}</div>
                    <div class="location">${props.classroom.full_location || ''}</div>
                    <div class="subtitle">${props.classroom.department_name || ''} - ${props.classroom.school_name || ''}</div>
                    <div class="qr-code">${qrCodeSvg.value}</div>
                    <div class="instruction">
                        Scan this QR code to record your attendance
                    </div>
                    <div class="code">Room Code: ${props.classroom.code || 'N/A'}</div>
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
    link.download = `qr-code-classroom-${props.classroom.code || props.classroom.uuid}.png`;
    link.href = qrCodeDataUrl.value;
    link.click();
};

const handleExportPdf = () => {
    const pdfWindow = window.open('', '_blank');
    if (pdfWindow) {
        pdfWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>QR Code - ${props.classroom.name}</title>
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
                        border: 3px solid #000;
                        padding: 40px;
                        border-radius: 15px;
                        max-width: 400px;
                    }
                    .title {
                        font-size: 28px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .location {
                        font-size: 18px;
                        color: #333;
                        margin-bottom: 5px;
                    }
                    .subtitle {
                        font-size: 16px;
                        color: #666;
                        margin-bottom: 25px;
                    }
                    .qr-code {
                        margin: 25px 0;
                    }
                    .qr-code img {
                        width: 280px;
                        height: 280px;
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
                    <div class="title">${props.classroom.name}</div>
                    <div class="location">${props.classroom.full_location || ''}</div>
                    <div class="subtitle">${props.classroom.department_name || ''} - ${props.classroom.school_name || ''}</div>
                    <div class="qr-code">
                        <img src="${qrCodeDataUrl.value}" alt="QR Code" />
                    </div>
                    <div class="instruction">
                        Scan this QR code to record your attendance
                    </div>
                    <div class="code">Room Code: ${props.classroom.code || 'N/A'}</div>
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
        <Head :title="`QR Code - ${classroom.name}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="icon" @click="handleBack">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">Classroom QR Code</h1>
                        <p class="text-sm text-muted-foreground">
                            Print and place inside classroom for attendance tracking
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="handleDownload">
                        <Download class="mr-2 h-4 w-4" />
                        PNG
                    </Button>
                    <Button variant="outline" @click="handleExportPdf">
                        <FileDown class="mr-2 h-4 w-4" />
                        PDF
                    </Button>
                    <Button @click="handlePrint">
                        <Printer class="mr-2 h-4 w-4" />
                        Print
                    </Button>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="flex justify-center">
                <Card class="w-full max-w-md">
                    <CardHeader class="text-center">
                        <CardTitle class="flex items-center justify-center gap-2">
                            <DoorOpen class="h-5 w-5" />
                            {{ classroom.name }}
                        </CardTitle>
                        <CardDescription v-if="classroom.full_location">
                            {{ classroom.full_location }}
                        </CardDescription>
                        <CardDescription v-if="classroom.department_name || classroom.school_name">
                            {{ classroom.department_name }} - {{ classroom.school_name }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col items-center space-y-4">
                        <!-- QR Code Display -->
                        <div class="rounded-lg border bg-white p-4">
                            <img
                                v-if="qrCodeDataUrl"
                                :src="qrCodeDataUrl"
                                :alt="`QR Code for ${classroom.name}`"
                                class="h-64 w-64"
                            />
                            <div v-else class="flex h-64 w-64 items-center justify-center">
                                <span class="text-muted-foreground">Generating QR Code...</span>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="rounded-lg bg-muted p-4 text-center">
                            <p class="text-sm font-medium">How to use:</p>
                            <ol class="mt-2 text-sm text-muted-foreground text-left list-decimal list-inside space-y-1">
                                <li>Print this QR code</li>
                                <li>Place inside the classroom (near entrance)</li>
                                <li>Students/employees scan when entering</li>
                                <li>Attendance is automatically recorded</li>
                            </ol>
                        </div>

                        <!-- Classroom Info -->
                        <div class="text-center text-sm text-muted-foreground">
                            <p v-if="classroom.code">Room Code: {{ classroom.code }}</p>
                            <p v-if="classroom.building">Building: {{ classroom.building }}</p>
                            <p v-if="classroom.floor">Floor: {{ classroom.floor }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
