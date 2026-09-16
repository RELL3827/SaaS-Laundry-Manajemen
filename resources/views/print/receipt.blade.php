<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota - {{ $order->order_number }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            width: 58mm; /* Thermal printer width */
            margin: 0 auto;
            padding: 10px;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 2px 0; vertical-align: top; }
        
        @media print {
            body { width: 100%; padding: 0; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="center">
        <h2 style="margin:0;">{{ $order->tenant->name }}</h2>
        @if($order->tenant->address)<p style="margin:2px 0;">{{ $order->tenant->address }}</p>@endif
        @if($order->tenant->phone)<p style="margin:2px 0;">WA: {{ $order->tenant->phone }}</p>@endif
    </div>
    
    <div class="divider"></div>
    
    <table class="table">
        <tr>
            <td>No</td>
            <td>: {{ $order->order_number }}</td>
        </tr>
        <tr>
            <td>Tgl</td>
            <td>: {{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Plg</td>
            <td>: {{ $order->customer->name }}</td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <table class="table">
        @foreach($order->items as $item)
        <tr>
            <td colspan="3">{{ $item->service->name }}</td>
        </tr>
        <tr>
            <td>{{ $item->qty }} x</td>
            <td class="right">{{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    
    <div class="divider"></div>
    
    <table class="table">
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount > 0)
        <tr>
            <td>Diskon</td>
            <td class="right">-{{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td class="bold">TOTAL</td>
            <td class="right bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Status Byr</td>
            <td class="right">{{ strtoupper($order->payment_status) }}</td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <div class="center" style="margin-top: 10px;">
        <p style="margin: 0 0 5px 0;">Scan untuk Cek Status:</p>
        <div>
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate(url('/track/'.$order->order_number)) !!}
        </div>
    </div>
    
    <div class="center" style="margin-top: 10px;">
        @if($order->tenant->isPro())
            <p style="margin: 0; font-weight: bold;">-- Terima Kasih Atas Kunjungan Anda --</p>
            <p style="margin: 3px 0 0 0; font-size: 10px;">Pakaian bersih, wangi & rapi untuk Anda</p>
        @else
            <p style="margin: 0;">Terima Kasih</p>
            <div style="margin-top: 10px; padding-top: 6px; border-top: 1px dashed #000; font-size: 9px; line-height: 1.3;">
                <p style="margin: 0; font-weight: bold;">*** CETAK DENGAN VERSI GRATIS ***</p>
                <p style="margin: 2px 0 0 0;">LaundryPro SaaS: Kelola Kasir Laundry Mudah</p>
                <p style="margin: 2px 0 0 0;">Upgrade ke Paket PRO untuk nota bersih</p>
            </div>
        @endif
    </div>

</body>
</html>
