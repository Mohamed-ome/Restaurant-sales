import React, { useMemo } from 'react';
import { useStore } from '../store/useStore';
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer, 
  Cell,
  AreaChart,
  Area
} from 'recharts';
import { formatCurrency } from '../lib/utils';
import { format, subDays, isWithinInterval, startOfDay, endOfDay } from 'date-fns';
import { ar } from 'date-fns/locale';
import { Utensils, CupSoda, TrendingUp, Calendar, Award } from 'lucide-react';

export default function SalesInsightsView() {
  const { orders, products, categories } = useStore();

  // 1. Calculate Top Selling Products
  const topProducts = useMemo(() => {
    const counts: Record<string, number> = {};
    orders.forEach(order => {
      order.items.forEach(item => {
        counts[item.productId] = (counts[item.productId] || 0) + item.quantity;
      });
    });

    const sorted = Object.entries(counts)
      .map(([id, count]) => {
        const product = products.find(p => p.id === id);
        const category = categories.find(c => c?.id === product?.categoryId);
        return {
          id,
          count,
          name: product?.nameAr || 'منتج محذوف',
          categoryType: category?.type || 'OTHER',
          price: product?.price || 0
        };
      })
      .sort((a, b) => b.count - a.count);

    return {
      dishes: sorted.filter(p => p.categoryType === 'FOOD').slice(0, 5),
      juices: sorted.filter(p => p.categoryType === 'JUICE').slice(0, 5)
    };
  }, [orders, products, categories]);

  // 2. Prepare Financial Graph Data (Last 7 Days)
  const chartData = useMemo(() => {
    const last7Days = Array.from({ length: 7 }, (_, i) => {
      const date = subDays(new Date(), i);
      return {
        dateStr: format(date, 'yyyy-MM-dd'),
        label: format(date, 'EEEE', { locale: ar }),
        total: 0
      };
    }).reverse();

    orders.forEach(order => {
      const orderDate = format(new Date(order.timestamp), 'yyyy-MM-dd');
      const dayData = last7Days.find(d => d.dateStr === orderDate);
      if (dayData) {
        dayData.total += order.total;
      }
    });

    return last7Days;
  }, [orders]);

  return (
    <div className="flex flex-col gap-6 pb-10" dir="rtl">
      {/* Header Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div className="glass-card p-6 rounded-2xl border-r-4 border-amber-500">
          <div className="flex justify-between items-start">
            <div>
              <p className="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">إجمالي الطلبات</p>
              <h3 className="text-2xl font-black text-zinc-100">{orders.length}</h3>
            </div>
            <div className="p-2 bg-amber-500/10 rounded-xl">
              <TrendingUp className="w-5 h-5 text-amber-500" />
            </div>
          </div>
        </div>
        <div className="glass-card p-6 rounded-2xl border-r-4 border-blue-500">
          <div className="flex justify-between items-start">
            <div>
              <p className="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">أفضل يوم مبيعاً</p>
              <h3 className="text-2xl font-black text-zinc-100">
                {chartData.reduce((prev, current) => (prev.total > current.total) ? prev : current).label}
              </h3>
            </div>
            <div className="p-2 bg-blue-500/10 rounded-xl">
              <Calendar className="w-5 h-5 text-blue-500" />
            </div>
          </div>
        </div>
        <div className="glass-card p-6 rounded-2xl border-r-4 border-emerald-500">
          <div className="flex justify-between items-start">
            <div>
              <p className="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">المنتج الأكثر طلباً</p>
              <h3 className="text-lg font-bold text-zinc-100 truncate max-w-[150px]">
                {topProducts.dishes[0]?.name || 'لا يوجد'}
              </h3>
            </div>
            <div className="p-2 bg-emerald-500/10 rounded-xl">
              <Award className="w-5 h-5 text-emerald-500" />
            </div>
          </div>
        </div>
      </div>

      {/* Main Financial Chart */}
      <div className="glass-card p-6 rounded-2xl overflow-hidden">
        <div className="flex justify-between items-center mb-8">
          <div>
            <h3 className="text-lg font-bold text-zinc-100 italic">الأداء المالي الأسبوعي</h3>
            <p className="text-xs text-zinc-500 mt-1">تتبع نمو المبيعات خلال الـ 7 أيام الماضية</p>
          </div>
        </div>
        
        <div className="h-[300px] w-full mt-4">
          <ResponsiveContainer width="100%" height="100%">
            <AreaChart data={chartData}>
              <defs>
                <linearGradient id="colorTotal" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#f59e0b" stopOpacity={0.3}/>
                  <stop offset="95%" stopColor="#f59e0b" stopOpacity={0}/>
                </linearGradient>
              </defs>
              <CartesianGrid strokeDasharray="3 3" stroke="#27272a" vertical={false} />
              <XAxis 
                dataKey="label" 
                stroke="#71717a" 
                fontSize={10} 
                tickLine={false} 
                axisLine={false}
                dy={10}
              />
              <YAxis 
                stroke="#71717a" 
                fontSize={10} 
                tickLine={false} 
                axisLine={false}
                tickFormatter={(value) => `${value}`}
                dx={-10}
              />
              <Tooltip 
                contentStyle={{ 
                  backgroundColor: '#18181b', 
                  border: '1px solid #27272a', 
                  borderRadius: '12px',
                  textAlign: 'right'
                }}
                itemStyle={{ color: '#f59e0b', fontWeight: 'bold' }}
                labelStyle={{ color: '#71717a', fontSize: '10px', marginBottom: '4px' }}
                formatter={(value: number) => [`${value} ج.س`, 'المبيعات']}
              />
              <Area 
                type="monotone" 
                dataKey="total" 
                stroke="#f59e0b" 
                strokeWidth={3}
                fillOpacity={1} 
                fill="url(#colorTotal)" 
                animationDuration={1500}
              />
            </AreaChart>
          </ResponsiveContainer>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Top Dishes */}
        <div className="glass-card rounded-2xl overflow-hidden">
          <div className="p-5 border-b border-zinc-800 bg-zinc-900/30 flex items-center gap-3">
            <div className="p-2 bg-amber-500/10 rounded-lg">
              <Utensils className="w-4 h-4 text-amber-500" />
            </div>
            <h3 className="font-bold text-zinc-100 flex-1">أكثر 5 أطباق مبيعاً</h3>
          </div>
          <div className="p-4 space-y-3">
            {topProducts.dishes.map((p, idx) => (
              <div key={p.id} className="flex items-center gap-4 p-3 bg-zinc-950/40 rounded-xl border border-zinc-900 hover:border-zinc-800 transition-colors group">
                <div className="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-500">
                  {idx + 1}
                </div>
                <div className="flex-1 min-w-0">
                  <h4 className="text-xs font-bold text-zinc-200 truncate">{p.name}</h4>
                  <p className="text-[10px] text-zinc-500">السعر: {p.price} ج.س</p>
                </div>
                <div className="text-right">
                  <div className="text-xs font-black text-amber-500">{p.count}</div>
                  <div className="text-[9px] text-zinc-600 uppercase font-bold tracking-tighter">طلب</div>
                </div>
                <div className="w-24 h-1 bg-zinc-900 rounded-full overflow-hidden shrink-0">
                  <div 
                    className="h-full bg-amber-500 transition-all duration-1000" 
                    style={{ width: `${(p.count / topProducts.dishes[0].count) * 100}%` }}
                  />
                </div>
              </div>
            ))}
            {topProducts.dishes.length === 0 && (
              <div className="py-10 text-center text-zinc-600 text-xs italic">لا توجد بيانات مبيعات كافية</div>
            )}
          </div>
        </div>

        {/* Top Juices */}
        <div className="glass-card rounded-2xl overflow-hidden">
          <div className="p-5 border-b border-zinc-800 bg-zinc-900/30 flex items-center gap-3">
            <div className="p-2 bg-blue-500/10 rounded-lg">
              <CupSoda className="w-4 h-4 text-blue-400" />
            </div>
            <h3 className="font-bold text-zinc-100 flex-1">أكثر 5 عصائر مبيعاً</h3>
          </div>
          <div className="p-4 space-y-3">
            {topProducts.juices.map((p, idx) => (
              <div key={p.id} className="flex items-center gap-4 p-3 bg-zinc-950/40 rounded-xl border border-zinc-900 hover:border-zinc-800 transition-colors group">
                <div className="w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-500">
                  {idx + 1}
                </div>
                <div className="flex-1 min-w-0">
                  <h4 className="text-xs font-bold text-zinc-200 truncate">{p.name}</h4>
                  <p className="text-[10px] text-zinc-500">السعر: {p.price} ج.س</p>
                </div>
                <div className="text-right">
                  <div className="text-xs font-black text-blue-400">{p.count}</div>
                  <div className="text-[9px] text-zinc-600 uppercase font-bold tracking-tighter">طلب</div>
                </div>
                <div className="w-24 h-1 bg-zinc-900 rounded-full overflow-hidden shrink-0">
                  <div 
                    className="h-full bg-blue-400 transition-all duration-1000" 
                    style={{ width: `${(p.count / topProducts.juices[0].count) * 100}%` }}
                  />
                </div>
              </div>
            ))}
            {topProducts.juices.length === 0 && (
              <div className="py-10 text-center text-zinc-600 text-xs italic">لا توجد بيانات مبيعات كافية</div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
