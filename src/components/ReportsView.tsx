import React, { useState, useMemo } from 'react';
import { useStore } from '../store/useStore';
import { Download, FileText, Calendar as CalendarIcon, Filter, Search, ChevronRight } from 'lucide-react';
import { formatCurrency } from '../lib/utils';
import { format, isWithinInterval, startOfDay, endOfDay, parseISO } from 'date-fns';
import { ar } from 'date-fns/locale';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

export default function ReportsView() {
  const { orders } = useStore();
  const [dateRange, setDateRange] = useState({ start: '', end: '' });
  const [searchProduct, setSearchProduct] = useState('');

  const filteredOrders = useMemo(() => {
    return orders.filter((order) => {
      const orderDate = new Date(order.timestamp);
      
      const isInDateRange = (!dateRange.start || orderDate >= startOfDay(parseISO(dateRange.start))) &&
                            (!dateRange.end || orderDate <= endOfDay(parseISO(dateRange.end)));
      
      const containsProduct = !searchProduct || order.items.some(item => 
        item.productName.toLowerCase().includes(searchProduct.toLowerCase())
      );

      return isInDateRange && containsProduct;
    });
  }, [orders, dateRange, searchProduct]);

  const totalSales = filteredOrders.reduce((acc, o) => acc + o.total, 0);

  const exportPDF = () => {
    const doc = new jsPDF('p', 'pt', 'a4');
    
    doc.setFontSize(22);
    doc.setTextColor(40);
    doc.text('Restaurant Sales Report - Al-Mantiqa', 40, 50);
    
    doc.setFontSize(12);
    doc.setTextColor(100);
    doc.text(`Generated on: ${new Date().toLocaleString()}`, 40, 75);
    doc.text(`Total Sales: ${totalSales.toFixed(2)} EGP`, 40, 95);
    doc.text(`Report Period: ${dateRange.start || 'Start'} to ${dateRange.end || 'Now'}`, 40, 115);

    const tableRows = filteredOrders.map(order => [
      order.id,
      format(order.timestamp, 'yyyy-MM-dd HH:mm'),
      order.items.map(i => `${i.productName} (x${i.quantity})`).join(', '),
      `${order.total.toFixed(2)}`
    ]);

    autoTable(doc, {
      startY: 140,
      head: [['Order ID', 'Date', 'Items', 'Total (EGP)']],
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
      <div className="flex flex-col lg:flex-row gap-4 glass-card p-6 rounded-xl">
        <div className="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div className="flex flex-col gap-2">
            <span className="text-[10px] font-bold text-zinc-500 uppercase pr-1">من تاريخ</span>
            <div className="relative">
              <CalendarIcon className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 w-3.5 h-3.5" />
              <input 
                type="date" 
                className="w-full pr-10 pl-4 py-2 bg-zinc-950 border border-zinc-800 rounded-full text-xs text-zinc-400 focus:border-amber-500 outline-none"
                value={dateRange.start}
                onChange={e => setDateRange({...dateRange, start: e.target.value})}
              />
            </div>
          </div>
          <div className="flex flex-col gap-2">
            <span className="text-[10px] font-bold text-zinc-500 uppercase pr-1">إلى تاريخ</span>
            <div className="relative">
              <CalendarIcon className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 w-3.5 h-3.5" />
              <input 
                type="date" 
                className="w-full pr-10 pl-4 py-2 bg-zinc-950 border border-zinc-800 rounded-full text-xs text-zinc-400 focus:border-amber-500 outline-none"
                value={dateRange.end}
                onChange={e => setDateRange({...dateRange, end: e.target.value})}
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
