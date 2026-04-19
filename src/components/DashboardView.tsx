import React, { useMemo } from 'react';
import { useStore } from '../store/useStore';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, AreaChart, Area } from 'recharts';
import { formatCurrency } from '../lib/utils';
import { TrendingUp, Users, ShoppingBag, DollarSign } from 'lucide-react';
import { format, subDays, startOfDay, endOfDay, isSameDay } from 'date-fns';
import { ar } from 'date-fns/locale';

export default function DashboardView() {
  const { orders } = useStore();

  const stats = useMemo(() => {
    const today = new Date();
    const todayOrders = orders.filter(o => isSameDay(new Date(o.timestamp), today));
    const totalSales = orders.reduce((sum, o) => sum + o.total, 0);
    const todaySales = todayOrders.reduce((sum, o) => sum + o.total, 0);
    
    return [
      { label: 'مبيعات اليوم', value: formatCurrency(todaySales), icon: DollarSign, color: 'amber' },
      { label: 'إجمالي المبيعات', value: formatCurrency(totalSales), icon: TrendingUp, color: 'emerald' },
      { label: 'طلبات اليوم', value: todayOrders.length, icon: ShoppingBag, color: 'blue' },
      { label: 'إجمالي الطلبات', value: orders.length, icon: Users, color: 'purple' },
    ];
  }, [orders]);

  const chartData = useMemo(() => {
    return Array.from({ length: 7 }).map((_, i) => {
      const date = subDays(new Date(), 6 - i);
      const dayOrders = orders.filter(o => isSameDay(new Date(o.timestamp), date));
      return {
        name: format(date, 'eee', { locale: ar }),
        sales: dayOrders.reduce((sum, o) => sum + o.total, 0),
      };
    });
  }, [orders]);

  return (
    <div className="flex flex-col gap-6" dir="rtl">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((s, i) => (
          <div key={i} className="glass-card p-5 rounded-xl accent-border flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div className={cn(
              "w-12 h-12 rounded-xl flex items-center justify-center",
              s.color === 'amber' ? "bg-amber-500/10 text-amber-500" :
              s.color === 'emerald' ? "bg-emerald-500/10 text-emerald-500" :
              s.color === 'blue' ? "bg-blue-500/10 text-blue-500" :
              "bg-purple-500/10 text-purple-500"
            )}>
              <s.icon className="w-5 h-5" />
            </div>
            <div>
              <p className="text-[10px] text-zinc-500 uppercase font-bold tracking-wider mb-0.5">{s.label}</p>
              <p className="text-xl font-bold text-zinc-100">{s.value}</p>
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="glass-card p-6 rounded-xl overflow-hidden">
          <div className="flex justify-between items-center mb-6">
            <h3 className="font-bold text-zinc-100 italic">مخطط المبيعات الأسبوعي</h3>
            <div className="flex items-center gap-2 text-[10px] text-zinc-500">
              <div className="w-2 h-2 rounded-full bg-amber-500" />
              <span>الأسبوع الحالي</span>
            </div>
          </div>
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={chartData}>
                <defs>
                  <linearGradient id="colorSales" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#f59e0b" stopOpacity={0.2}/>
                    <stop offset="95%" stopColor="#f59e0b" stopOpacity={0}/>
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#27272a" />
                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 10, fill: '#71717a' }} />
                <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 10, fill: '#71717a' }} />
                <Tooltip 
                  contentStyle={{ backgroundColor: '#18181b', borderRadius: '12px', border: '1px solid #3f3f46', color: '#f4f4f5' }}
                  itemStyle={{ color: '#f59e0b' }}
                />
                <Area type="monotone" dataKey="sales" stroke="#f59e0b" fillOpacity={1} fill="url(#colorSales)" strokeWidth={3} />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="glass-card p-6 rounded-xl overflow-hidden flex flex-col">
          <h3 className="font-bold text-zinc-100 mb-6 italic">أحدث العمليات</h3>
          <div className="flex-1 overflow-y-auto pr-1">
            <table className="w-full text-right text-xs">
              <thead>
                <tr className="text-zinc-500 border-b border-zinc-800">
                  <th className="pb-3 px-2 font-medium">رقم العملية</th>
                  <th className="pb-3 px-2 font-medium">تاريخ العملية</th>
                  <th className="pb-3 px-2 font-medium text-left">المبلغ</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-zinc-800/50">
                {orders.slice(0, 8).map((o) => (
                  <tr key={o.id} className="hover:bg-zinc-800/30 transition-colors group">
                    <td className="py-4 px-2 font-bold text-zinc-300">#{(o.id.split('-')[1] || '').slice(-6)}</td>
                    <td className="py-4 px-2 text-zinc-500">{format(o.timestamp, 'HH:mm - MM/dd')}</td>
                    <td className="py-4 px-2 font-bold text-amber-500 text-left">{formatCurrency(o.total)}</td>
                  </tr>
                ))}
                {orders.length === 0 && (
                  <tr>
                    <td colSpan={3} className="py-12 text-center text-zinc-600">لا توجد عمليات مسجلة</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}

function cn(...inputs: any[]) {
  return inputs.filter(Boolean).join(' ');
}
