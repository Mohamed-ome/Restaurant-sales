import React from 'react';
import { Truck, MapPin, Phone, Package, CheckCircle2, Clock } from 'lucide-react';
import { cn } from '../lib/utils';

export default function ShippingView() {
  const deliveries = [
    { id: 'DEL-01', customer: 'أحمد علي', address: 'شارع 10، المقطم', status: 'IN_TRANSIT', time: '10 mins' },
    { id: 'DEL-02', customer: 'سارة حسن', address: 'مدينتي، طلعت مصطفى', status: 'PENDING', time: '25 mins' },
    { id: 'DEL-03', customer: 'محمود كمال', address: 'المعادي، دجلة', status: 'DELIVERED', time: 'Completed' },
  ];

  return (
    <div className="flex flex-col gap-6" dir="rtl">
      <div className="glass-card p-6 rounded-xl">
        <div className="flex justify-between items-center mb-8">
          <h3 className="font-bold text-zinc-100 text-lg italic">طلبات التوصيل النشطة</h3>
          <button className="bg-amber-600 text-zinc-950 px-4 py-2 rounded-lg text-[10px] font-bold hover:bg-amber-500 transition-colors shadow-lg shadow-amber-900/10">
            ربط شركة شحن +
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {deliveries.map((d) => (
            <div key={d.id} className="bg-zinc-950/50 p-4 rounded-xl border border-zinc-800 flex flex-col gap-4">
              <div className="flex justify-between items-start">
                <div className="flex items-center gap-2">
                  <div className="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-500">
                    <Truck className="w-4 h-4" />
                  </div>
                  <div>
                    <p className="text-[9px] text-zinc-600 font-bold uppercase">{d.id}</p>
                    <p className="text-xs font-bold text-zinc-300">{d.customer}</p>
                  </div>
                </div>
                <span className={cn(
                  "text-[9px] font-bold px-2 py-0.5 rounded-lg uppercase tracking-tight",
                  d.status === 'IN_TRANSIT' ? "bg-blue-500/10 text-blue-500 border border-blue-500/20" :
                  d.status === 'PENDING' ? "bg-orange-500/10 text-orange-500 border border-orange-500/20" :
                  "bg-emerald-500/10 text-emerald-500 border border-emerald-500/20"
                )}>
                  {d.status === 'IN_TRANSIT' ? 'جاري التوصيل' : d.status === 'PENDING' ? 'انتظار' : 'تم'}
                </span>
              </div>

              <div className="space-y-1.5 font-medium">
                <div className="flex items-center gap-2 text-[11px] text-zinc-500">
                  <MapPin className="w-3 h-3" />
                  <span>{d.address}</span>
                </div>
                <div className="flex items-center gap-2 text-[11px] text-zinc-500">
                  <Clock className="w-3 h-3" />
                  <span>{d.time}</span>
                </div>
              </div>

              <div className="pt-3 border-t border-zinc-800 mt-1 flex gap-2">
                <button className="flex-1 py-1.5 bg-zinc-800 text-[9px] font-bold text-zinc-400 rounded hover:bg-zinc-700 transition-colors">
                  التفاصيل
                </button>
                <button className="flex-1 py-1.5 bg-amber-600/10 text-amber-500 text-[9px] font-bold rounded border border-amber-500/20 hover:bg-amber-600 hover:text-zinc-950 transition-all">
                  تتبع الموقع
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
      
      <div className="p-20 border-2 border-dashed border-zinc-900 rounded-3xl flex flex-col items-center gap-4 text-center opacity-30">
        <Package className="w-12 h-12 text-zinc-700" />
        <div>
          <h4 className="font-bold text-zinc-400 italic">تكامل مزودي الشحن</h4>
          <p className="text-[10px] text-zinc-600 max-w-xs mt-1">تكامل مباشر مع خدمات مرسول، طلبات، وهنقرستيشن لإدارة شاملة.</p>
        </div>
      </div>
    </div>
  );
}
