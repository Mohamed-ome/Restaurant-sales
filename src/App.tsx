import React from 'react';
import { useStore } from './store/useStore';
import { 
  LayoutDashboard, 
  Store, 
  Menu as MenuIcon, 
  PackageSearch, 
  BarChart3, 
  LogOut,
  Settings,
  Bell,
  Truck
} from 'lucide-react';
import DashboardView from './components/DashboardView';
import PosView from './components/PosView';
import MenuView from './components/MenuView';
import InventoryView from './components/InventoryView';
import ReportsView from './components/ReportsView';
import ShippingView from './components/ShippingView';
import SalesInsightsView from './components/SalesInsightsView';
import LoginView from './components/LoginView';
import { cn } from './lib/utils';
import { motion, AnimatePresence } from 'motion/react';

export default function App() {
  const { activeView, setActiveView, currentUser, logout } = useStore();

  const navItems = [
    { id: 'pos', label: 'نقاط البيع', icon: Store, roles: ['ADMIN', 'MANAGER'] },
    { id: 'dashboard', label: 'لوحة التحكم', icon: LayoutDashboard, roles: ['ADMIN', 'MANAGER'] },
    { id: 'sales_insights', label: 'تحليلات المبيعات', icon: BarChart3, roles: ['ADMIN', 'MANAGER'] },
    { id: 'menu', label: 'قائمة الطعام', icon: MenuIcon, roles: ['ADMIN', 'MANAGER'] },
    { id: 'inventory', label: 'المخزون', icon: PackageSearch, roles: ['ADMIN', 'MANAGER'] },
    { id: 'reports', label: 'السجلات', icon: BarChart3, roles: ['ADMIN'] },
    { id: 'shipping', label: 'الشحن والتوصيل', icon: Truck, roles: ['ADMIN', 'MANAGER'] },
  ];

  const filteredNavItems = navItems.filter(item => 
    currentUser && item.roles.includes(currentUser.role)
  );

  if (!currentUser) {
    return <LoginView />;
  }

  return (
    <div className="flex h-screen bg-zinc-950 font-sans selection:bg-amber-900/30 selection:text-amber-500" dir="rtl">
      {/* Sidebar */}
      <aside className="w-64 bg-zinc-900 border-l border-zinc-800 flex flex-col group transition-all duration-300">
        <div className="p-6 mb-6">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/10">
              <Store className="text-zinc-900 w-6 h-6" />
            </div>
            <div>
              <h1 className="text-zinc-100 font-bold text-lg leading-tight">منتزه حاتم السياحي</h1>
              <p className="text-zinc-500 text-[10px] font-bold tracking-widest uppercase">المنطقة الوسطى</p>
            </div>
          </div>
        </div>

        <nav className="flex-1 px-4 space-y-1">
          <div className="text-[10px] uppercase tracking-wider text-zinc-500 px-3 mb-2">الرئيسية</div>
          {filteredNavItems.map((item) => (
            <button
              key={item.id}
              onClick={() => setActiveView(item.id as any)}
              className={cn(
                "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group/btn text-right",
                activeView === item.id 
                  ? "bg-amber-500/10 text-amber-500" 
                  : "text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100"
              )}
            >
              <item.icon className={cn(
                "w-4 h-4 transition-transform group-hover/btn:scale-110",
                activeView === item.id ? "text-amber-500" : "text-zinc-500 group-hover:text-zinc-300"
              )} />
              <span className="font-medium text-sm flex-1">{item.label}</span>
              {activeView === item.id && (
                <div className="mr-auto w-1 h-4 bg-amber-500 rounded-full" />
              )}
            </button>
          ))}
        </nav>

        <div className="p-4 border-t border-zinc-800">
          <button 
            onClick={logout}
            className="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-zinc-500 hover:bg-red-500/10 hover:text-red-500 transition-all text-right translate-x-0"
          >
            <LogOut className="w-4 h-4" />
            <span className="font-medium text-sm">تسجيل الخروج</span>
          </button>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 flex flex-col overflow-hidden">
        {/* Header */}
        <header className="h-16 bg-zinc-900/50 backdrop-blur-md border-b border-zinc-800 px-8 flex items-center justify-between">
          <div>
            <h2 className="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-0.5">
              {navItems.find(i => i.id === activeView)?.label}
            </h2>
            <p className="text-zinc-100 font-bold text-lg">
              {activeView === 'pos' ? 'نقطة البيع' : 'لوحة التحكم'}
            </p>
          </div>

          <div className="flex items-center gap-4">
            <div className="flex bg-zinc-950 border border-zinc-800 rounded-xl p-1">
              <button className="p-2 text-zinc-500 hover:text-zinc-100 transition-colors">
                <Bell className="w-5 h-5" />
              </button>
              <button className="p-2 text-zinc-500 hover:text-zinc-100 transition-colors">
                <Settings className="w-5 h-5" />
              </button>
            </div>
            <div className="h-8 w-px bg-zinc-800" />
            <div className="flex items-center gap-3 bg-zinc-950 border border-zinc-800 pl-2 pr-3 py-1.5 rounded-xl">
              <div className="w-7 h-7 bg-zinc-800 rounded-lg flex items-center justify-center font-bold text-zinc-400 text-xs">
                {currentUser.name[0]}
              </div>
              <div className="text-right">
                <p className="text-xs font-bold text-zinc-100 leading-none mb-1">{currentUser.name}</p>
                <p className="text-[10px] text-zinc-500 font-medium leading-none">
                  {currentUser.role === 'ADMIN' ? 'مدير النظام' : 'مشرف الصالة'}
                </p>
              </div>
            </div>
          </div>
        </header>

        {/* Content Area */}
        <div className="flex-1 overflow-y-auto p-6 relative">
          <AnimatePresence mode="wait">
            <motion.div
              key={activeView}
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.2 }}
              className="h-full"
            >
              {activeView === 'dashboard' && <DashboardView />}
              {activeView === 'pos' && <PosView />}
              {activeView === 'sales_insights' && <SalesInsightsView />}
              {activeView === 'menu' && <MenuView />}
              {activeView === 'inventory' && <InventoryView />}
              {activeView === 'reports' && <ReportsView />}
              {activeView === 'shipping' && <ShippingView />}
            </motion.div>
          </AnimatePresence>
        </div>
      </main>

      {/* Print-only Invoice Templates */}
      <div id="invoice-print-container" className="hidden print:block bg-white text-slate-900 min-h-screen font-sans" dir="rtl">
        
        {/* Customer Receipt Template (Detailed) */}
        <div className="customer-receipt hidden p-8">
          <div className="text-center mb-6 border-b-2 border-slate-900 pb-4">
            <h1 className="text-2xl font-black mb-1">منتزه حاتم السياحي</h1>
            <p className="text-sm">المنطقة الصناعية - الفرع الرئيسي</p>
            <p className="text-sm">هاتف: 0123456789</p>
          </div>
          
          <div className="flex justify-between mb-6 text-[10px]">
            <div>
              <p className="font-bold">التاريخ: {new Date().toLocaleDateString('ar-EG')}</p>
              <p>الوقت: {new Date().toLocaleTimeString('ar-EG')}</p>
            </div>
            <div className="text-left">
              <p className="font-bold">رقم الفاتورة: #INV-{Date.now().toString().slice(-6)}</p>
              <p>البائع: {currentUser?.name}</p>
            </div>
          </div>

          <table className="w-full text-right mb-6 text-sm">
            <thead>
              <tr className="border-y border-slate-900">
                <th className="py-2">الصنف</th>
                <th className="py-2 text-center">الكمية</th>
                <th className="py-2 text-left">السعر</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {/* In a real app, we'd pass the actual cart items here or use a shared state */}
              <tr className="border-b border-slate-100">
                <td className="py-3 font-bold">يرجى تأكيد الطلب قبل الطباعة</td>
                <td className="py-3 text-center">1</td>
                <td className="py-3 text-left">---</td>
              </tr>
            </tbody>
          </table>

          <div className="flex justify-end border-t-2 border-slate-900 pt-4">
            <div className="w-full max-w-[200px] space-y-1 text-sm">
              <div className="flex justify-between font-bold text-lg">
                <span>طريقة الدفع:</span>
                <span>---</span>
              </div>
              <div className="flex justify-between font-bold text-lg">
                <span>الإجمالي:</span>
                <span>--- ج.س</span>
              </div>
            </div>
          </div>

          <div className="mt-12 text-center text-[10px] text-slate-500">
            <p>شكرًا لزيارتكم منتزه حاتم السياحي!</p>
            <p className="mt-1">نتمنى لكم وجبة شهية • الرقم الضريبي: 123-456-789</p>
          </div>
        </div>

        {/* Kitchen/Order Ticket Template (Simplified) */}
        <div className="kitchen-ticket hidden p-8">
          <div className="text-center mb-6 border-b-2 border-slate-300 pb-4">
            <h1 className="text-3xl font-black mb-1">طلب تجهيز مطبخ</h1>
            <p className="text-xl font-bold">#{Date.now().toString().slice(-4)}</p>
          </div>
          
          <div className="flex justify-between mb-6 text-sm">
            <div>
              <p className="font-bold">التاريخ: {new Date().toLocaleDateString('ar-EG')}</p>
              <p>الوقت: {new Date().toLocaleTimeString('ar-EG')}</p>
            </div>
            <div className="text-left font-bold text-lg">
              محلي / Local
            </div>
          </div>

          <table className="w-full text-right mb-8">
            <thead>
              <tr className="border-y-2 border-black">
                <th className="py-3 text-xl">الصنف</th>
                <th className="py-3 text-center text-xl font-black">العدد</th>
              </tr>
            </thead>
            <tbody>
              <tr className="border-b border-slate-300">
                <td className="py-6 text-2xl font-black">جاري تجهيز الطلب...</td>
                <td className="py-6 text-center text-3xl font-black">1</td>
              </tr>
            </tbody>
          </table>

          <div className="mt-12 border-t border-slate-300 pt-4 text-center">
            <p className="text-sm font-bold uppercase tracking-widest italic opacity-50">KITCHEN COPY • نسخة المطبخ</p>
          </div>
        </div>
      </div>

      {/* Print styles */}
      <style>{`
        @media print {
          aside, header, main {
            display: none !important;
          }
          #invoice-print-container {
            display: block !important;
          }
          body.print-customer .customer-receipt {
            display: block !important;
          }
          body.print-kitchen .kitchen-ticket {
            display: block !important;
          }
          body {
            background-color: white !important;
            margin: 0;
            padding: 0;
          }
        }
      `}</style>
    </div>
  );
}
