import React from 'react';
import { useStore } from '../store/useStore';
import { Package, AlertTriangle, ArrowUpRight, ArrowDownRight, RefreshCw } from 'lucide-react';
import { cn, formatCurrency } from '../lib/utils';

export default function InventoryView() {
  const { products, updateProduct } = useStore();

  const inventoryStats = {
    totalItems: products.length,
    lowStock: products.filter(p => p.inStock <= 5).length,
    outOfStock: products.filter(p => p.inStock === 0).length,
    totalValue: products.reduce((acc, p) => acc + (p.price * p.inStock), 0)
  };

  const adjustStock = (id: string, amount: number) => {
    const product = products.find(p => p.id === id);
    if (product) {
      updateProduct(id, { inStock: Math.max(0, product.inStock + amount) });
    }
  };

  return (
    <div className="flex flex-col gap-6" dir="rtl">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {[
          { label: 'إجمالي الأصناف', value: inventoryStats.totalItems, icon: Package, color: 'blue' },
          { label: 'نواقص (منخفض)', value: inventoryStats.lowStock, icon: AlertTriangle, color: 'orange' },
          { label: 'نفذت الكمية', value: inventoryStats.outOfStock, icon: Package, color: 'red' },
          { label: 'قيمة المخزون', value: formatCurrency(inventoryStats.totalValue), icon: RefreshCw, color: 'emerald' },
        ].map((s, i) => (
          <div key={i} className="glass-card p-4 rounded-xl accent-border flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div className={cn(
              "w-12 h-12 rounded-xl flex items-center justify-center",
              s.color === 'blue' ? "bg-blue-500/10 text-blue-500" :
              s.color === 'orange' ? "bg-orange-500/10 text-orange-500" :
              s.color === 'red' ? "bg-red-500/10 text-red-500" :
              "bg-emerald-500/10 text-emerald-500"
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

      <div className="glass-card rounded-xl overflow-hidden">
        <div className="p-6 border-b border-zinc-800 flex justify-between items-center">
          <h3 className="font-bold text-zinc-100 italic">حالة المخزون الحالي</h3>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-right text-xs">
            <thead>
              <tr className="bg-zinc-950/50 text-zinc-500 border-b border-zinc-800">
                <th className="px-6 py-4 font-bold uppercase tracking-wider">المنتج</th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider">الكمية الحالية</th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider">الحالة</th>
                <th className="px-6 py-4 font-bold uppercase tracking-wider text-left">التعديل</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-zinc-800/50">
              {products.map((product) => (
                <tr key={product.id} className="hover:bg-zinc-800/30 transition-colors">
                  <td className="px-6 py-4">
                    <div className="flex flex-col">
                      <span className="font-bold text-zinc-200">{product.nameAr}</span>
                      <span className="text-[10px] text-zinc-600">{product.name}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span className={cn(
                      "font-bold text-lg",
                      product.inStock === 0 ? "text-red-500" :
                      product.inStock <= 5 ? "text-amber-500" :
                      "text-zinc-400"
                    )}>
                      {product.inStock}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    {product.inStock === 0 ? (
                      <span className="bg-red-500/10 text-red-500 border border-red-500/20 px-2 py-0.5 rounded text-[10px] font-bold">منتهي</span>
                    ) : product.inStock <= 5 ? (
                      <span className="bg-amber-500/10 text-amber-500 border border-amber-500/20 px-2 py-0.5 rounded text-[10px] font-bold">منخفض</span>
                    ) : (
                      <span className="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-2 py-0.5 rounded text-[10px] font-bold">متوفر</span>
                    )}
                  </td>
                  <td className="px-6 py-4 text-left">
                    <div className="flex gap-2 justify-end">
                      <button 
                        onClick={() => adjustStock(product.id, -1)}
                        className="p-1.5 bg-zinc-800 text-zinc-500 rounded hover:bg-zinc-700 transition-all font-bold"
                      >
                        <ArrowDownRight className="w-3.5 h-3.5" />
                      </button>
                      <button 
                        onClick={() => adjustStock(product.id, 1)}
                        className="p-1.5 bg-zinc-800 text-zinc-500 rounded hover:bg-zinc-700 transition-all font-bold"
                      >
                        <ArrowUpRight className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
