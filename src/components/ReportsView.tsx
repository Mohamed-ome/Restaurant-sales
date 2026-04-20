import React, { useState, useMemo } from 'react';
import { useStore } from '../store/useStore';
import { Download, FileText, Calendar as CalendarIcon, Filter, Search, ChevronRight, X } from 'lucide-react';
import { formatCurrency } from '../lib/utils';
import { format, isWithinInterval, startOfDay, endOfDay, parseISO, isValid } from 'date-fns';
import { ar } from 'date-fns/locale';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import DatePicker, { registerLocale } from 'react-datepicker';
import { arSA } from "date-fns/locale";

registerLocale('ar', arSA);

export default function ReportsView() {
  const { orders } = useStore();
  const [startDate, setStartDate] = useState<Date | null>(null);
  const [endDate, setEndDate] = useState<Date | null>(null);
  const [searchProduct, setSearchProduct] = useState('');

  const filteredOrders = useMemo(() => {
    return orders.filter((order) => {
      const orderDate = new Date(order.timestamp);
      
      const isInDateRange = (!startDate || orderDate >= startOfDay(startDate)) &&
                            (!endDate || orderDate <= endOfDay(endDate));
      
      const containsProduct = !searchProduct || order.items.some(item => 
        item.productName.toLowerCase().includes(searchProduct.toLowerCase())
      );

      return isInDateRange && containsProduct;
    });
  }, [orders, startDate, endDate, searchProduct]);

  const totalSales = filteredOrders.reduce((acc, o) => acc + o.total, 0);

  const exportPDF = () => {
    const doc = new jsPDF('p', 'pt', 'a4');
    
    // Add Arabic font support if possible, or use standard with better labeling
    doc.setFontSize(22);
    doc.setTextColor(40);
    doc.text('Hatem Tourist Park - تقرير المبيعات', 40, 50);
    
    doc.setFontSize(12);
    doc.setTextColor(100);
    doc.text(`تاريخ الاستخراج: ${format(new Date(), 'yyyy/MM/dd | HH:mm:ss', { locale: ar })}`, 40, 75);
    doc.text(`إجمالي المبيعات: ${totalSales.toFixed(2)} EGP`, 40, 95);
    doc.text(`الفترة: ${startDate ? format(startDate, 'yyyy/MM/dd') : 'من البداية'} إلى ${endDate ? format(endDate, 'yyyy/MM/dd') : 'الآن'}`, 40, 115);

    const tableRows = filteredOrders.map(order => [
      `#${(order.id.split('-')[1] || '').slice(-6)}`,
      format(order.timestamp, 'yyyy-MM-dd HH:mm'),
      order.items.map(i => `${i.productName} (x${i.quantity})`).join(', '),
      `${order.total.toFixed(2)}`
    ]);

    autoTable(doc, {
      startY: 140,
      head: [['رقم العملية', 'التاريخ والوقت', 'الأصناف', 'المجموع (جنيه)']],
      body: tableRows,
      theme: 'striped',
      headStyles: { fillColor: [245, 158, 11] }, // Amber 500
    });

    doc.save(`sales_report_${format(new Date(), 'yyyyMMdd_HHmm')}.pdf`);
  };

  const exportCSV = () => {
    const headers = ['Order ID', 'Date', 'Total', 'Items'];
    const rows = filteredOrders.map(o => [
      o.id,
      format(o.timestamp, 'yyyy-MM-dd HH:mm'),
      o.total,
      o.items.map(i => `${i.productName} (x${i.quantity})`).join('; ')
    ]);
    
    // Use BOM for Excel Arabic support
    const csvContent = "\uFEFF" + [headers, ...rows].map(e => e.join(",")).join("\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", `sales_report_${format(new Date(), 'yyyyMMdd_HHmm')}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="flex flex-col gap-6" dir="rtl">
      <style>{`
        .react-datepicker-wrapper { width: 100%; }
        .react-datepicker {
          background-color: #09090b;
          border: 1px solid #27272a;
          font-family: inherit;
          color: #e4e4e7;
          border-radius: 0.75rem;
          padding: 0.5rem;
          box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        .react-datepicker__header {
          background-color: #09090b;
          border-bottom: 1px solid #27272a;
          padding-top: 0.5rem;
        }
        .react-datepicker__current-month, .react-datepicker-time__header, .react-datepicker-year-header {
          color: #f4f4f5;
          font-weight: 600;
          font-size: 0.875rem;
        }
        .react-datepicker__day-name, .react-datepicker__day, .react-datepicker__time-name {
          color: #a1a1aa;
          width: 2rem;
          line-height: 2rem;
        }
        .react-datepicker__day:hover {
          background-color: #27272a;
          color: #fafafa;
          border-radius: 0.375rem;
        }
        .react-datepicker__day--selected, .react-datepicker__day--in-selecting-range, .react-datepicker__day--in-range {
          background-color: #f59e0b !important;
          color: #09090b !important;
          border-radius: 0.375rem;
        }
        .react-datepicker__day--keyboard-selected {
          background-color: rgba(245, 158, 11, 0.1);
          color: #f59e0b;
        }
        .react-datepicker__navigation-icon::before {
          border-color: #71717a;
        }
        .react-datepicker__triangle { display: none; }
      `}</style>

      <div className="flex flex-col lg:flex-row gap-4 glass-card p-6 rounded-xl">
        <div className="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div className="flex flex-col gap-2">
            <span className="text-[10px] font-bold text-zinc-500 uppercase pr-1">الفترة الزمنية</span>
            <div className="relative">
              <CalendarIcon className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 w-3.5 h-3.5 z-10" />
              <DatePicker
                selectsRange={true}
                startDate={startDate}
                endDate={endDate}
                onChange={(update) => {
                  const [start, end] = update;
                  setStartDate(start);
                  setEndDate(end);
                }}
                isClearable={true}
                placeholderText="اختر الفترة..."
                className="w-full pr-10 pl-4 py-2 bg-zinc-950 border border-zinc-800 rounded-full text-xs text-zinc-400 focus:border-amber-500 outline-none cursor-pointer"
                locale="ar"
                dateFormat="yyyy/MM/dd"
              />
            </div>
          </div>
          <div className="flex flex-col gap-2">
            <span className="text-[10px] font-bold text-zinc-500 uppercase pr-1">البحث عن صنف</span>
            <div className="relative">
              <Search className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 w-3.5 h-3.5" />
              <input 
                type="text" 
                placeholder="مثال: شاورما..."
                className="w-full pr-10 pl-4 py-2 bg-zinc-950 border border-zinc-800 rounded-full text-xs text-zinc-400 focus:border-amber-500 outline-none"
                value={searchProduct}
                onChange={e => setSearchProduct(e.target.value)}
              />
            </div>
          </div>
        </div>
        <div className="flex gap-2 items-end">
          <button 
            onClick={exportCSV}
            className="flex items-center gap-2 px-6 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors"
          >
            تصدير CSV
          </button>
          <button 
            onClick={exportPDF}
            className="flex items-center gap-2 px-6 py-2 bg-amber-600 text-zinc-950 rounded-lg text-xs font-bold hover:bg-amber-500 transition-colors shadow-lg shadow-amber-900/10"
          >
            تصدير PDF
          </button>
        </div>
      </div>

      <div className="glass-card rounded-xl overflow-hidden">
        <div className="p-6 border-b border-zinc-800 flex justify-between items-center">
          <h3 className="font-bold text-zinc-100 italic">سجل المبيعات والعمليات</h3>
          <div className="text-right">
            <span className="text-[10px] text-zinc-500 uppercase tracking-widest ml-4">المبيعات الإجمالية</span>
            <span className="font-bold text-amber-500 text-lg">{formatCurrency(totalSales)}</span>
          </div>
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead>
              <tr className="bg-zinc-950/50 text-zinc-500 border-b border-zinc-800">
                <th className="px-6 py-4 font-bold uppercase tracking-wider">العملية</th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider">التاريخ</th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider">الأصناف</th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider text-left">المبلغ</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-800/50">
              {filteredOrders.map((order) => (
                <tr key={order.id} className="hover:bg-zinc-800/30 transition-colors">
                  <td className="px-6 py-4 font-bold text-zinc-300">#{(order.id.split('-')[1] || '').slice(-6)}</td>
                  <td className="px-6 py-4 text-zinc-500">{format(order.timestamp, 'yyyy/MM/dd | HH:mm', { locale: ar })}</td>
                  <td className="px-6 py-4">
                    <div className="flex flex-wrap gap-1">
                      {order.items.map((item, idx) => (
                        <span key={idx} className="bg-zinc-800 text-zinc-400 px-2 py-0.5 rounded text-[9px] font-bold">
                          {item.productName} (×{item.quantity})
                        </span>
                      ))}
                    </div>
                  </td>
                  <td className="px-6 py-4 text-left font-bold text-amber-500">{formatCurrency(order.total)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
