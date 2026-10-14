@extends('layouts.dashboard')

@section('title', 'Cetak Label - ' . $copy->copy_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 no-print-container">
    <!-- Header control panel (hidden during printing) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 no-print">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <a href="{{ route('book-copies.show', $copy->id) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke detail eksemplar
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Cetak Label Buku</h1>
                <p class="text-sm text-gray-500 mt-1">Konfigurasi dan cetak label fisik untuk ditempelkan pada buku.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Sekarang
                </button>
            </div>
        </div>

        <hr class="my-6 border-gray-200" />

        <!-- Control Panel Settings -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <label for="library_name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Perpustakaan</label>
                <input type="text" id="library_name" value="{{ app('settings')->get('library.name', 'PERPUSTAKAAN SIRKULIB') }}" oninput="updateLabels()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="label_qty" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Duplikasi Label</label>
                <select id="label_qty" onchange="changeQuantity(this.value)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="1">1 Label</option>
                    <option value="2">2 Label</option>
                    <option value="4">4 Label</option>
                    <option value="8">8 Label</option>
                    <option value="12" selected>12 Label (1 Lembar)</option>
                    <option value="24">24 Label (2 Lembar)</option>
                </select>
            </div>
            <div>
                <label for="label_type" class="block text-sm font-semibold text-gray-700 mb-1">Format Kode</label>
                <select id="label_type" onchange="toggleCodeType(this.value)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="both" selected>Barcode & QR Code</option>
                    <option value="barcode">Hanya Barcode</option>
                    <option value="qrcode">Hanya QR Code</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Print Canvas Preview Area -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 flex justify-center overflow-x-auto no-print-bg">
        <div class="bg-white shadow-md p-6 border border-gray-300 print-sheet" id="print-sheet" style="width: 210mm; min-height: 297mm; box-sizing: border-box;">
            <!-- Printable Grid (A4 sheet size representation) -->
            <div id="labels-container" class="grid grid-cols-2 gap-x-6 gap-y-8">
                <!-- Labels will be generated here dynamically by Javascript -->
            </div>
        </div>
    </div>
</div>

<!-- Raw Barcode Element Holder (Hidden) -->
<div class="hidden">
    <svg id="barcode-generator"></svg>
</div>

<!-- Print Styles & Script dependencies -->
<style>
/* High contrast styling for print sheet container */
.print-sheet {
    background: white;
    color: black;
    font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Individual label card styling */
.label-card {
    border: 1px solid #000;
    padding: 12px;
    width: 100%;
    max-width: 95mm;
    height: 65mm;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: white;
    page-break-inside: avoid;
}

@media print {
    /* Hide dashboard layout completely */
    body, html {
        background: white !important;
        color: black !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 210mm;
        height: 297mm;
    }

    .no-print,
    aside,
    nav,
    header,
    footer,
    .no-print-container > :not(.no-print-bg),
    .no-print-bg {
        display: none !important;
    }

    .no-print-bg {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
        overflow: visible !important;
    }

    .print-sheet {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        min-height: auto !important;
    }

    .label-card {
        page-break-inside: avoid !important;
    }
}
</style>

<!-- Load JsBarcode and QR libraries via CDN -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>

<script>
    // Copy Details passed from PHP
    const copyCode = @json($copy->copy_code);
    const barcodeVal = @json($copy->barcode ?? $copy->copy_code);
    const qrCodeVal = @json($copy->copy_code);
    const qrPayload = @json($qrPayload);
    const bookTitle = @json($copy->book->title);
    const bookAuthor = @json($copy->book->author);
    const rackLocation = @json($copy->book->rack_location ?? 'RAK-01');

    let currentQty = 12;
    let codeType = 'both';

    function changeQuantity(qty) {
        currentQty = parseInt(qty);
        renderLabels();
    }

    function toggleCodeType(type) {
        codeType = type;
        renderLabels();
    }

    function updateLabels() {
        const libName = document.getElementById('library_name').value || 'PERPUSTAKAAN';
        const els = document.querySelectorAll('.lib-name-label');
        els.forEach(el => el.innerText = libName.toUpperCase());
    }

    function renderLabels() {
        const container = document.getElementById('labels-container');
        const libName = document.getElementById('library_name').value || 'PERPUSTAKAAN';
        container.innerHTML = '';

        // Determine column layouts based on quantity
        if (currentQty <= 1) {
            container.className = "flex justify-center py-10";
        } else {
            container.className = "grid grid-cols-2 gap-x-6 gap-y-8";
        }

        function makeQrDataUrl(value, size = 150) {
            const qr = qrcode(0, 'M');
            qr.addData(value);
            qr.make();

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const modules = qr.getModuleCount();
            const cell = size / modules;
            canvas.width = size;
            canvas.height = size;
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, size, size);
            ctx.fillStyle = '#111827';

            for (let row = 0; row < modules; row++) {
                for (let col = 0; col < modules; col++) {
                    if (qr.isDark(row, col)) {
                        ctx.fillRect(Math.round(col * cell), Math.round(row * cell), Math.ceil(cell), Math.ceil(cell));
                    }
                }
            }

            return canvas.toDataURL('image/png');
        }

        // Generate QR code locally from standardized resolver payload
        const qrUrl = makeQrDataUrl(qrPayload);

        for (let i = 0; i < currentQty; i++) {
            const card = document.createElement('div');
            card.className = "label-card";

            // Limit title and author lengths safely
            const displayTitle = bookTitle.length > 40 ? bookTitle.substring(0, 37) + '...' : bookTitle;
            const displayAuthor = bookAuthor.length > 25 ? bookAuthor.substring(0, 22) + '...' : bookAuthor;

            // Define layouts
            let codesHTML = '';
            if (codeType === 'both') {
                codesHTML = `
                    <div class="flex items-center justify-between gap-2 mt-1">
                        <!-- Barcode Area -->
                        <div class="flex-1 flex flex-col items-center justify-center">
                            <img class="barcode-img max-h-12 w-full object-contain" src="" id="barcode-img-${i}" alt="Barcode">
                            <span class="text-[9px] font-mono font-bold mt-0.5 text-gray-700">${barcodeVal}</span>
                        </div>
                        <!-- QR Code Area -->
                        <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center border border-gray-200 p-0.5 bg-white">
                            <img class="w-full h-full object-contain" src="${qrUrl}" alt="QR">
                        </div>
                    </div>
                `;
            } else if (codeType === 'barcode') {
                codesHTML = `
                    <div class="flex flex-col items-center justify-center mt-2 w-full">
                        <img class="barcode-img max-h-16 w-4/5 object-contain" src="" id="barcode-img-${i}" alt="Barcode">
                        <span class="text-[10px] font-mono font-bold mt-1 text-gray-700">${barcodeVal}</span>
                    </div>
                `;
            } else {
                codesHTML = `
                    <div class="flex flex-col items-center justify-center mt-1">
                        <div class="w-20 h-20 flex items-center justify-center border border-gray-200 p-1 bg-white">
                            <img class="w-full h-full object-contain" src="${qrUrl}" alt="QR">
                        </div>
                        <span class="text-[10px] font-mono font-bold mt-1 text-gray-700">${qrCodeVal}</span>
                    </div>
                `;
            }

            card.innerHTML = `
                <!-- Library Header Label -->
                <div class="border-b border-black pb-1.5 text-center">
                    <span class="lib-name-label text-[11px] font-extrabold tracking-widest text-black block">${libName.toUpperCase()}</span>
                </div>

                <!-- Book Metadata Area -->
                <div class="my-2 flex-grow">
                    <h4 class="text-[11px] font-extrabold text-black leading-tight line-clamp-2 uppercase">${displayTitle}</h4>
                    <div class="flex items-center justify-between text-[9px] text-gray-700 mt-1 font-semibold">
                        <span>Oleh: ${displayAuthor}</span>
                        <span class="bg-gray-100 px-1 border border-black/20 font-mono">${rackLocation}</span>
                    </div>
                </div>

                <!-- Codes Generation Area -->
                ${codesHTML}

                <!-- Copy Code Footnote -->
                <div class="border-t border-black pt-1.5 flex justify-between items-center text-[9px] font-mono font-bold mt-1.5 text-black">
                    <span>KODE: ${copyCode}</span>
                    <span class="text-[8px] font-sans">SIRKULIB SYSTEM</span>
                </div>
            `;

            container.appendChild(card);

            // Generate barcode dataURI if barcode option is enabled
            if (codeType === 'both' || codeType === 'barcode') {
                setTimeout(() => {
                    const canvas = document.createElement('canvas');
                    try {
                        JsBarcode(canvas, barcodeVal, {
                            format: "CODE128",
                            width: 1.5,
                            height: 40,
                            displayValue: false,
                            margin: 0
                        });
                        const img = document.getElementById(`barcode-img-${i}`);
                        if (img) {
                            img.src = canvas.toDataURL("image/png");
                        }
                    } catch (e) {
                        console.error('JsBarcode failed', e);
                    }
                }, 50);
            }
        }
    }

    // Initial load
    document.addEventListener("DOMContentLoaded", () => {
        renderLabels();
    });
</script>
@endsection
