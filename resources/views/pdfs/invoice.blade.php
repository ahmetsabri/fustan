<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        


        body {
            font-family: 'Tajawal', 'DejaVu Sans', sans-serif;
            direction: rtl;
            color: #333;
            line-height: 1.6;
            padding: 40px;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #9333ea;
        }
        
        .header h1 {
            color: #9333ea;
            font-size: 32px;
            font-weight: bold;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #9333ea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9d5ff;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
        }
        
        .measurements-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        
        .measurement-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .measurement-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .measurement-value {
            font-size: 18px;
            font-weight: bold;
            color: #9333ea;
        }
        
        .total-section {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 16px;
        }
        
        .total-row.final {
            border-top: 2px solid #9333ea;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #9333ea;
        }
        
        .notes {
            background: #fff7ed;
            padding: 15px;
            border-radius: 8px;
            border-right: 4px solid #f59e0b;
            margin-top: 20px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #92400e;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        @media print {
            body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>فاتورة</h1>
            <div class="invoice-info">
              <bdi>
                  <p>{{ $order->order_number }} <strong>رقم الفاتورة:</strong> </p>
              </bdi>
              <bdi>
                <p>{{ $order->created_at->format('Y-m-d H:i:s') }} <strong>التاريخ:</strong></p>
              </bdi>
              @if($order->customerService)
              <bdi>
                <p>{{ $order->customerService->name }} <strong>تم الإنشاء بواسطة:</strong> </p>
              </bdi>
              @endif
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">معلومات العميل</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">الاسم</div>
                    <div class="info-value">{{ $order->customer->name ?? 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الهاتف</div>
                    <div class="info-value">{{ $order->customer->phone ?? 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">البريد الإلكتروني</div>
                    <div class="info-value">{{ $order->customer->email ?? 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">العنوان</div>
                    <div class="info-value">{{ $order->customer->address ?? 'غير محدد' }}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">معلومات الطلب</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">المنتج</div>
                    <div class="info-value">{{ $order->product->name ?? 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الحالة</div>
                    <div class="info-value">
                        @php
                            $statusLabels = [
                                'pending' => 'معلق',
                                'in_progress' => 'قيد التنفيذ',
                                'completed' => 'مكتمل',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغي',
                            ];
                        @endphp
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">الخياط</div>
                    <div class="info-value">{{ $order->tailor->name ?? 'غير معين' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">تاريخ التسليم المتوقع</div>
                    <div class="info-value">{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : 'غير محدد' }}</div>
                </div>
                @if($order->customerService)
                <div class="info-item">
                    <div class="info-label">تم الإنشاء بواسطة</div>
                    <div class="info-value">{{ $order->customerService->name }}</div>
                </div>
                @endif
            </div>
        </div>
        
        @if($order->length || $order->shoulder || $order->chest || $order->waist || $order->hip || $order->sleeve)
        <div class="section">
            <div class="section-title">المقاسات</div>
            <div class="measurements-grid">
                @if($order->length)
                <div class="measurement-item">
                    <div class="measurement-label">الطول</div>
                    <div class="measurement-value">{{ number_format($order->length, 2) }} cm</div>
                </div>
                @endif
                @if($order->shoulder)
                <div class="measurement-item">
                    <div class="measurement-label">الكتف</div>
                    <div class="measurement-value">{{ number_format($order->shoulder, 2) }} cm</div>
                </div>
                @endif
                @if($order->chest)
                <div class="measurement-item">
                    <div class="measurement-label">الصدر</div>
                    <div class="measurement-value">{{ number_format($order->chest, 2) }} cm</div>
                </div>
                @endif
                @if($order->waist)
                <div class="measurement-item">
                    <div class="measurement-label">الخصر</div>
                    <div class="measurement-value">{{ number_format($order->waist, 2) }} cm</div>
                </div>
                @endif
                @if($order->hip)
                <div class="measurement-item">
                    <div class="measurement-label">الورك</div>
                    <div class="measurement-value">{{ number_format($order->hip, 2) }} cm</div>
                </div>
                @endif
                @if($order->sleeve)
                <div class="measurement-item">
                    <div class="measurement-label">الكُم</div>
                    <div class="measurement-value">{{ number_format($order->sleeve, 2) }} cm</div>
                </div>
                @endif
            </div>
            @if($order->measurement_notes)
            <div class="notes" style="margin-top: 15px;">
                <div class="notes-title">ملاحظات المقاسات:</div>
                <div>{{ $order->measurement_notes }}</div>
            </div>
            @endif
        </div>
        @endif
        
        <div class="total-section">
            <div class="total-row">
                <span>السعر الإجمالي:</span>
                <br>
                <span>{{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'KWD' }}</span>
            </div>
            <div class="total-row final">
                <span>المبلغ المستحق:</span>
                <br>
                <span>{{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'KWD' }}</span>
            </div>
        </div>
        
        @if($order->notes)
        <div class="notes">
            <div class="notes-title"> ملاحظات الطلب:</div>
            <div>{{ $order->notes }}</div>
        </div>
        @endif
        
        <div class="footer">
            <p>شكراً لتعاملكم معنا</p>
            <p>تم إنشاء هذه الفاتورة تلقائياً في </p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

