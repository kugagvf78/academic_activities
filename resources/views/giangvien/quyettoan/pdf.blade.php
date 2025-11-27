<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quyết toán #{{ $quyettoan->maquyettoan }}</title>
    <style>
        @page {
            margin: 15mm 15mm 20mm 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1a1a1a;
        }
        
        .container {
            width: 100%;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px double #003d82;
        }
        
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .header-left, .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-left {
            text-align: left;
            padding-right: 20px;
        }
        
        .header-right {
            text-align: right;
            padding-left: 20px;
        }
        
        .header-org {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #003d82;
            margin-bottom: 3px;
        }
        
        .header-dept {
            font-size: 9pt;
            font-weight: 600;
            color: #333;
            text-decoration: underline;
        }
        
        .header-code {
            font-size: 9pt;
            color: #666;
            font-style: italic;
        }
        
        .title {
            font-size: 18pt;
            font-weight: bold;
            color: #003d82;
            text-transform: uppercase;
            margin: 15px 0 8px;
            letter-spacing: 0.5px;
        }
        
        .subtitle {
            font-size: 11pt;
            color: #444;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-draft {
            background-color: #f0f0f0;
            color: #555;
            border: 1px solid #ccc;
        }
        
        .status-pending {
            background-color: #fff8e1;
            color: #f57c00;
            border: 1px solid #ffb74d;
        }
        
        .status-approved {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }
        
        .status-rejected {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #e57373;
        }
        
        /* Section Styles */
        .section {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #003d82;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: linear-gradient(to right, #e3f2fd, transparent);
            border-left: 4px solid #003d82;
        }
        
        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-table tr:last-child {
            border-bottom: none;
        }
        
        .info-table td {
            padding: 10px 12px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 38%;
            font-weight: 600;
            color: #424242;
            background-color: #fafafa;
        }
        
        .info-table td:last-child {
            width: 62%;
            color: #212121;
        }
        
        /* Summary Boxes */
        .summary-grid {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        
        .summary-box {
            width: 33.33%;
            padding: 18px 12px;
            text-align: center;
            border: 2px solid #e0e0e0;
            vertical-align: top;
        }
        
        .summary-box.budget {
            background-color: #e3f2fd;
            border-color: #1976d2;
        }
        
        .summary-box.actual {
            background-color: #e8f5e9;
            border-color: #388e3c;
        }
        
        .summary-box.difference {
            background-color: #fff3e0;
            border-color: #f57c00;
        }
        
        .summary-label {
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #616161;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .summary-amount {
            font-size: 20pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .summary-box.budget .summary-amount {
            color: #0d47a1;
        }
        
        .summary-box.actual .summary-amount {
            color: #1b5e20;
        }
        
        .summary-box.difference .summary-amount {
            color: #e65100;
        }
        
        .summary-note {
            font-size: 8pt;
            color: #757575;
            margin-top: 4px;
            font-style: italic;
        }
        
        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 10pt;
        }
        
        .data-table thead {
            background: linear-gradient(to bottom, #1565c0, #0d47a1);
            color: white;
        }
        
        .data-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .data-table th:last-child {
            border-right: none;
        }
        
        .data-table th.text-center {
            text-align: center;
        }
        
        .data-table th.text-right {
            text-align: right;
        }
        
        .data-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .data-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .data-table td {
            padding: 12px 10px;
            vertical-align: top;
        }
        
        .data-table td.text-center {
            text-align: center;
        }
        
        .data-table td.text-right {
            text-align: right;
        }
        
        .expense-name {
            font-weight: 600;
            color: #212121;
            margin-bottom: 4px;
        }
        
        .expense-note {
            font-size: 9pt;
            color: #757575;
            font-style: italic;
            line-height: 1.4;
        }
        
        .amount {
            font-weight: 600;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        
        .amount.positive {
            color: #2e7d32;
        }
        
        .amount.negative {
            color: #c62828;
        }
        
        .amount.neutral {
            color: #424242;
        }
        
        .data-table tfoot {
            background: linear-gradient(to bottom, #f5f5f5, #e0e0e0);
            font-weight: bold;
            border-top: 3px double #003d82;
        }
        
        .data-table tfoot td {
            padding: 14px 10px;
            font-size: 11pt;
            color: #003d82;
        }
        
        /* Statistics Section */
        .statistics {
            width: 100%;
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .stat-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            padding: 8px 0;
            border-bottom: 1px dashed #ccc;
        }
        
        .stat-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .stat-label {
            display: table-cell;
            width: 60%;
            font-weight: 600;
            color: #495057;
        }
        
        .stat-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-weight: bold;
            color: #212529;
        }
        
        /* Notes Section */
        .notes-box {
            margin: 20px 0;
            padding: 15px;
            background-color: #fffbf0;
            border-left: 4px solid #ff9800;
            border-radius: 4px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #e65100;
            margin-bottom: 10px;
            font-size: 11pt;
        }
        
        .notes-content {
            color: #5d4037;
            white-space: pre-wrap;
            line-height: 1.6;
        }
        
        /* Signatures */
        .signatures {
            margin-top: 35px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-cell {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }
        
        .signature-date {
            font-style: italic;
            color: #666;
            margin-bottom: 8px;
            font-size: 10pt;
        }
        
        .signature-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 60px;
            font-size: 11pt;
            color: #003d82;
        }
        
        .signature-name {
            font-weight: bold;
            color: #1565c0;
            font-size: 11pt;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #757575;
            font-size: 9pt;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        /* Page Break Control */
        .no-break {
            page-break-inside: avoid;
        }
        
        /* Watermark for Draft */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if($quyettoan->trangthai == 'Draft')
        <div class="watermark">BẢN NHÁP</div>
    @endif

    <div class="container">
        {{-- Header --}}
        <div class="header no-break">
            <div class="header-top">
                <div class="header-left">
                    <div class="header-org">TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP.HCM</div>
                    <div class="header-dept">{{ $quyettoan->tenbomon }}</div>
                </div>
                <div class="header-right">
                    <div class="header-code">Mã số: {{ $quyettoan->maquyettoan }}</div>
                    <div class="header-code">Ngày lập: {{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('d/m/Y') }}</div>
                </div>
            </div>
            
            <div class="title">HỒ SƠ QUYẾT TOÁN TÀI CHÍNH</div>
            <div class="subtitle">Cuộc thi: <strong>{{ $quyettoan->tencuocthi }}</strong></div>
            <div class="subtitle">
                @if($quyettoan->trangthai == 'Draft')
                    <span class="status-badge status-draft">Bản nháp</span>
                @elseif($quyettoan->trangthai == 'Pending')
                    <span class="status-badge status-pending">Chờ duyệt</span>
                @elseif($quyettoan->trangthai == 'Approved')
                    <span class="status-badge status-approved">Đã duyệt</span>
                @elseif($quyettoan->trangthai == 'Rejected')
                    <span class="status-badge status-rejected">Từ chối</span>
                @endif
            </div>
        </div>

        {{-- Thông tin cuộc thi --}}
        <div class="section no-break">
            <div class="section-title">I. THÔNG TIN CUỘC THI</div>
            <table class="info-table">
                <tr>
                    <td>Tên cuộc thi:</td>
                    <td><strong>{{ $quyettoan->tencuocthi }}</strong></td>
                </tr>
                <tr>
                    <td>Loại cuộc thi:</td>
                    <td>{{ $quyettoan->loaicuocthi }}</td>
                </tr>
                <tr>
                    <td>Đơn vị tổ chức:</td>
                    <td>{{ $quyettoan->tenbomon }}</td>
                </tr>
                <tr>
                    <td>Thời gian tổ chức:</td>
                    <td>
                        <strong>Từ:</strong> {{ \Carbon\Carbon::parse($quyettoan->thoigianbatdau)->format('H:i - d/m/Y') }}
                        <br>
                        <strong>Đến:</strong> {{ \Carbon\Carbon::parse($quyettoan->thoigianketthuc)->format('H:i - d/m/Y') }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Tổng quan tài chính --}}
        <div class="section no-break">
            <div class="section-title">II. TỔNG QUAN TÀI CHÍNH</div>
            <table class="summary-grid">
                <tr>
                    <td class="summary-box budget">
                        <div class="summary-label">Tổng Dự Trù</div>
                        <div class="summary-amount">{{ number_format($quyettoan->tongdutru, 0, ',', '.') }}</div>
                        <div class="summary-note">VNĐ</div>
                    </td>
                    <td class="summary-box actual">
                        <div class="summary-label">Tổng Thực Tế</div>
                        <div class="summary-amount">{{ number_format($quyettoan->tongthucte, 0, ',', '.') }}</div>
                        <div class="summary-note">VNĐ</div>
                    </td>
                    <td class="summary-box difference">
                        <div class="summary-label">Chênh Lệch</div>
                        <div class="summary-amount">{{ number_format($quyettoan->chenhlech, 0, ',', '.') }}</div>
                        <div class="summary-note">VNĐ</div>
                    </td>
                </tr>
            </table>
            
            @if($chiphis->isNotEmpty())
            <div class="statistics">
                @php
                    $totalBudget = $chiphis->sum('dutruchiphi');
                    $totalActual = $chiphis->sum('thuctechi');
                    $totalDiff = $totalBudget - $totalActual;
                    $percentUsed = $totalBudget > 0 ? ($totalActual / $totalBudget * 100) : 0;
                @endphp
                <div class="stat-row">
                    <div class="stat-label">📊 Số khoản chi:</div>
                    <div class="stat-value">{{ $chiphis->count() }} khoản</div>
                </div>
                <div class="stat-row">
                    <div class="stat-label">💰 Tỷ lệ sử dụng ngân sách:</div>
                    <div class="stat-value">{{ number_format($percentUsed, 1) }}%</div>
                </div>
                <div class="stat-row">
                    <div class="stat-label">💵 Số tiền tiết kiệm:</div>
                    <div class="stat-value {{ $totalDiff >= 0 ? 'positive' : 'negative' }}">
                        {{ number_format(abs($totalDiff), 0, ',', '.') }} VNĐ
                        @if($totalDiff >= 0)
                            (Tiết kiệm)
                        @else
                            (Vượt chi)
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Chi tiết chi phí --}}
        @if($chiphis->isNotEmpty())
            <div class="section">
                <div class="section-title">III. CHI TIẾT CÁC KHOẢN CHI</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">STT</th>
                            <th style="width: 35%;">Tên Khoản Chi</th>
                            <th class="text-right" style="width: 18%;">Dự Trù (VNĐ)</th>
                            <th class="text-right" style="width: 18%;">Thực Tế (VNĐ)</th>
                            <th class="text-right" style="width: 16%;">Chênh Lệch (VNĐ)</th>
                            <th class="text-center" style="width: 8%;">Tỷ Lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $stt = 1; @endphp
                        @foreach($chiphis as $cp)
                            @php
                                $diff = $cp->dutruchiphi - $cp->thuctechi;
                                $percent = $cp->dutruchiphi > 0 ? ($cp->thuctechi / $cp->dutruchiphi * 100) : 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $stt++ }}</td>
                                <td>
                                    <div class="expense-name">{{ $cp->tenkhoanchi }}</div>
                                    @if($cp->ghichu)
                                        <div class="expense-note">{{ $cp->ghichu }}</div>
                                    @endif
                                    @if($cp->ngaychi)
                                        <div class="expense-note">
                                            Ngày chi: {{ \Carbon\Carbon::parse($cp->ngaychi)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-right amount neutral">
                                    {{ number_format($cp->dutruchiphi, 0, ',', '.') }}
                                </td>
                                <td class="text-right amount neutral">
                                    {{ number_format($cp->thuctechi, 0, ',', '.') }}
                                </td>
                                <td class="text-right amount {{ $diff >= 0 ? 'positive' : 'negative' }}">
                                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="amount {{ $percent <= 100 ? 'positive' : 'negative' }}">
                                        {{ number_format($percent, 0) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-center">TỔNG CỘNG</td>
                            <td class="text-right">
                                {{ number_format($chiphis->sum('dutruchiphi'), 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($chiphis->sum('thuctechi'), 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                @php
                                    $totalDiff = $chiphis->sum('dutruchiphi') - $chiphis->sum('thuctechi');
                                @endphp
                                <span class="amount {{ $totalDiff >= 0 ? 'positive' : 'negative' }}">
                                    {{ $totalDiff >= 0 ? '+' : '' }}{{ number_format($totalDiff, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $totalPercent = $chiphis->sum('dutruchiphi') > 0 
                                        ? ($chiphis->sum('thuctechi') / $chiphis->sum('dutruchiphi') * 100) 
                                        : 0;
                                @endphp
                                <span class="amount {{ $totalPercent <= 100 ? 'positive' : 'negative' }}">
                                    {{ number_format($totalPercent, 0) }}%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- Ghi chú --}}
        @if($quyettoan->ghichu)
            <div class="notes-box no-break">
                <div class="notes-title">📝 GHI CHÚ:</div>
                <div class="notes-content">{{ $quyettoan->ghichu }}</div>
            </div>
        @endif

        {{-- Thông tin quyết toán --}}
        <div class="section no-break">
            <div class="section-title">IV. THÔNG TIN QUYẾT TOÁN</div>
            <table class="info-table">
                <tr>
                    <td>Mã quyết toán:</td>
                    <td><strong>{{ $quyettoan->maquyettoan }}</strong></td>
                </tr>
                <tr>
                    <td>Ngày lập:</td>
                    <td>{{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Người lập:</td>
                    <td><strong>{{ $quyettoan->tennguoilap ?? 'Không xác định' }}</strong></td>
                </tr>
                <tr>
                    <td>Trạng thái:</td>
                    <td>
                        @if($quyettoan->trangthai == 'Draft')
                            <strong>Bản nháp</strong> - Chưa gửi duyệt
                        @elseif($quyettoan->trangthai == 'Pending')
                            <strong>Chờ duyệt</strong> - Đang chờ phê duyệt
                        @elseif($quyettoan->trangthai == 'Approved')
                            <strong>Đã duyệt</strong> - Người duyệt: {{ $quyettoan->tennguoiduyet ?? 'N/A' }}
                        @elseif($quyettoan->trangthai == 'Rejected')
                            <strong>Từ chối</strong> - Không được phê duyệt
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Chữ ký --}}
        <div class="signatures no-break">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-date">
                            TP.HCM, ngày {{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('d') }} 
                            tháng {{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('m') }} 
                            năm {{ \Carbon\Carbon::parse($quyettoan->ngayquyettoan)->format('Y') }}
                        </div>
                        <div class="signature-title">NGƯỜI LẬP BIỂU</div>
                        <div class="signature-name">{{ $quyettoan->tennguoilap ?? '................................' }}</div>
                    </td>
                    <td class="signature-cell">
                        @if($quyettoan->trangthai == 'Approved')
                            <div class="signature-date">
                                TP.HCM, ngày ..... tháng ..... năm {{ \Carbon\Carbon::now()->format('Y') }}
                            </div>
                        @endif
                        <div class="signature-title">TRƯỞNG BỘ MÔN</div>
                        <div class="signature-name">
                            @if($quyettoan->trangthai == 'Approved' && $quyettoan->tennguoiduyet)
                                {{ $quyettoan->tennguoiduyet }}
                            @else
                                ................................
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p><strong>Tài liệu này được tạo tự động từ Hệ thống Quản lý Cuộc thi</strong></p>
            <p>Trường Đại học Công Thương TP.HCM - {{ $quyettoan->tenbomon }}</p>
            <p>Thời gian in: {{ \Carbon\Carbon::now()->format('H:i - d/m/Y') }}</p>
            <p style="font-size: 8pt; margin-top: 5px;">
                Mọi thông tin trong tài liệu này là tài sản của Trường và cần được bảo mật
            </p>
        </div>
    </div>
</body>
</html>