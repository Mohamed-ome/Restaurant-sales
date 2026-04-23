import React, { useState, useMemo } from 'react';
import { useStore } from '../store/useStore';
import { Search, ShoppingCart, Trash2, Printer, Plus, Minus, X, Utensils, User as UserIcon, ChevronDown } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { formatCurrency, cn } from '../lib/utils';
import { Product } from '../types';
import Fuse from 'fuse.js';

export default function PosView() {
  const { products, categories, addOrder } = useStore();
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string | 'all'>('all');
  const [cart, setCart] = useState<{ product: Product; quantity: number }[]>([]);
  const [isCheckoutOpen, setIsCheckoutOpen] = useState(false);
  const [showClearConfirm, setShowClearConfirm] = useState(false);
  const [showPrintMenu, setShowPrintMenu] = useState(false);
  const [paymentMethod, setPaymentMethod] = useState<'CASH' | 'BANKAK'>('CASH');
  const [transactionId, setTransactionId] = useState('');

  const filteredProducts = useMemo(() => {
    let results = products;

    // Filter by category first
    if (selectedCategory !== 'all') {
      results = results.filter(p => p.categoryId === selectedCategory);
    }

    // Apply fuzzy search if search term exists
    if (search.trim()) {
      const fuse = new Fuse(results, {
        keys: [
          { name: 'nameAr', weight: 0.7 },
          { name: 'name', weight: 0.5 },
          { name: 'ingredients', weight: 0.3 }
        ],
        threshold: 0.4,
        distance: 100,
        useExtendedSearch: true,
      });

      results = fuse.search(search).map(r => r.item);
    }

    return results;
  }, [products, search, selectedCategory]);

  const addToCart = (product: Product) => {
    setCart((prev) => {
      const existing = prev.find((item) => item.product.id === product.id);
      if (existing) {
        return prev.map((item) =>
          item.product.id === product.id ? { ...item, quantity: item.quantity + 1 } : item
        );
      }
      return [...prev, { product, quantity: 1 }];
    });
  };

  const removeFromCart = (id: string) => {
    setCart((prev) => prev.filter((item) => item.product.id !== id));
  };

  const updateQuantity = (id: string, delta: number) => {
    setCart((prev) =>
      prev.map((item) =>
        item.product.id === id ? { ...item, quantity: Math.max(0, item.quantity + delta) } : item
      ).filter(item => item.quantity > 0)
    );
  };

  const setQuantity = (id: string, value: number) => {
    if (isNaN(value)) return;
    setCart((prev) =>
      prev.map((item) =>
        item.product.id === id ? { ...item, quantity: Math.max(0, value) } : item
      ).filter(item => item.quantity > 0)
    );
  };

  const cartTotal = cart.reduce((acc, item) => acc + item.product.price * item.quantity, 0);

  const handleCheckout = () => {
    if (cart.length === 0) return;
    setIsCheckoutOpen(true);
  };

  const confirmOrder = () => {
    if (paymentMethod === 'BANKAK' && transactionId.length !== 4) {
      console.warn('يرجى إدخال رقم العملية (4 أرقام)');
      return;
    }

    const orderItems = cart.map(item => ({
      productId: item.product.id,
      productName: item.product.nameAr,
      quantity: item.quantity,
      price: item.product.price,
    }));
    
    addOrder(orderItems, paymentMethod, transactionId);
    setCart([]);
    setIsCheckoutOpen(false);
    setPaymentMethod('CASH');
    setTransactionId('');
  };

  const printInvoice = (type: 'customer' | 'kitchen') => {
    // We set the print type in the store or as a global variable that App.tsx can read
    // or we can use a class on the body to control visibility in CSS
    document.body.classList.remove('print-customer', 'print-kitchen');
    document.body.classList.add(`print-${type}`);
    window.print();
    setShowPrintMenu(false);
  };

  return (
    <>
    <div className="flex h-full gap-4 overflow-hidden" dir="rtl">
       {/* Products Column */}
       <div className="flex-1 flex flex-col gap-4 overflow-hidden">
        <div className="flex gap-4 glass-card p-4 rounded-xl">
          <div className="relative flex-1">
            <Search className="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 w-3.5 h-3.5" />
            <input
              type="text"
              placeholder="بحث عن صنف أو مكون..."
              className="w-full pr-10 pl-4 py-2 bg-zinc-950 border border-zinc-800 rounded-full focus:border-amber-500 outline-none transition-all text-xs text-zinc-100 placeholder:text-zinc-600"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
          <select
            className="bg-zinc-950 border border-zinc-800 text-zinc-400 rounded-lg px-4 py-2 text-xs focus:border-amber-500 outline-none"
            value={selectedCategory}
            onChange={(e) => setSelectedCategory(e.target.value)}
          >
            <option value="all">كل الأقسام</option>
            {categories.map((c) => (
              <option key={c.id} value={c.id}>{c.name}</option>
            ))}
          </select>
        </div>

        <div className="flex gap-4 border-b border-zinc-800 pb-1 overflow-x-auto">
          <button 
             onClick={() => setSelectedCategory('all')}
             className={cn(
               "pb-2 px-3 text-xs font-bold transition-all",
               selectedCategory === 'all' ? "text-amber-500 border-b-2 border-amber-500" : "text-zinc-500 hover:text-zinc-300"
             )}
          >
            الكل
          </button>
          {categories.map(cat => (
            <button 
              key={cat.id}
              onClick={() => setSelectedCategory(cat.id)}
              className={cn(
                "pb-2 px-3 text-xs font-bold transition-all whitespace-nowrap",
                selectedCategory === cat.id ? "text-amber-500 border-b-2 border-amber-500" : "text-zinc-500 hover:text-zinc-300"
              )}
            >
              {cat.name}
            </button>
          ))}
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 overflow-y-auto pb-4 pr-1">
          {filteredProducts.map((product) => (
            <motion.button
              key={product.id}
              layout
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              whileTap={{ scale: 0.98 }}
              onClick={() => addToCart(product)}
              className="group flex flex-col glass-card rounded-xl p-3 hover:border-amber-500/50 transition-all text-right cursor-pointer"
            >
              <div className="w-full aspect-square bg-zinc-950 rounded-lg mb-3 overflow-hidden flex items-center justify-center relative border border-zinc-800 group-hover:border-amber-500/30 transition-colors">
                {product.image ? (
                  <img src={product.image} alt={product.nameAr} className="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" referrerPolicy="no-referrer" />
                ) : (
                  <span className="text-zinc-800 text-3xl font-bold group-hover:text-zinc-700 transition-colors">{product.nameAr[0]}</span>
                )}
                {product.inStock <= 5 && (
                  <div className="absolute top-2 left-2 bg-red-500/20 text-red-500 border border-red-500/30 text-[9px] px-2 py-0.5 rounded-full font-bold">
                    منخفض
                  </div>
                )}
              </div>
              <div className="flex justify-between items-start mb-1 overflow-hidden">
                <h3 className="font-bold text-zinc-100 text-xs truncate flex-1">{product.nameAr}</h3>
                <span className="text-amber-500 font-bold text-xs mr-2 shrink-0">{product.price} ج.س</span>
              </div>
              <p className="text-zinc-500 text-[10px] truncate leading-tight">{product.ingredients.join('، ')}</p>
              
              <div className="mt-4 w-full bg-zinc-800 group-hover:bg-amber-600 text-zinc-300 group-hover:text-zinc-950 py-1.5 rounded-lg text-[10px] font-bold transition-colors">
                إضافة للفاتورة
              </div>
            </motion.button>
          ))}
          {filteredProducts.length === 0 && (
            <div className="col-span-full py-24 text-center text-zinc-600">
              لا توجد نتائج مطابقة للبحث
            </div>
          )}
        </div>
      </div>

      {/* Cart Column */}
      <div className="w-80 flex flex-col bg-zinc-900 border-r border-zinc-800 overflow-hidden rounded-xl">
        <div className="p-4 border-b border-zinc-800 flex items-center justify-between">
          <h2 className="font-bold text-zinc-100 flex items-center gap-2 text-sm italic">
            الفاتورة الحالية
          </h2>
          <div className="flex items-center gap-2">
            {cart.length > 0 && (
              <button 
                onClick={() => setShowClearConfirm(true)}
                className="p-1.5 text-zinc-600 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all"
                title="إلغاء الطلب"
              >
                <Trash2 className="w-4 h-4" />
              </button>
            )}
            <span className="bg-zinc-800 text-zinc-400 px-2 py-0.5 rounded text-[10px] font-medium uppercase tracking-wider">
              #INV-{Date.now().toString().slice(-4)}
            </span>
          </div>
        </div>

        <div className="flex-1 overflow-y-auto p-4 flex flex-col gap-3 relative">
          <AnimatePresence initial={false}>
            {cart.map((item) => (
              <motion.div 
                key={item.product.id}
                layout
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -100 }}
                drag="x"
                dragConstraints={{ left: -100, right: 0 }}
                onDragEnd={(_, info) => {
                  if (info.offset.x < -60) {
                    removeFromCart(item.product.id);
                  }
                }}
                className="relative bg-zinc-950/40 rounded-xl border border-transparent hover:border-zinc-800 transition-all group overflow-hidden"
              >
                {/* Swipe Background */}
                <div className="absolute inset-0 bg-red-600 flex items-center justify-end px-6 -z-10">
                  <Trash2 className="w-5 h-5 text-white" />
                </div>

                <div className="bg-zinc-950/40 p-3 rounded-xl">
                  <div className="flex justify-between items-start gap-4 mb-2">
                    <div className="flex-1 min-w-0">
                      <h4 className="text-[13px] font-bold text-zinc-100 truncate mb-0.5">{item.product.nameAr}</h4>
                      <div className="flex items-center gap-2">
                        <span className="text-[11px] font-bold text-amber-500">{(item.product.price * item.quantity).toFixed(2)} ج.س</span>
                        <span className="text-[9px] text-zinc-600">({item.product.price} للواحد)</span>
                      </div>
                    </div>
                    <button 
                      onClick={() => removeFromCart(item.product.id)}
                      className="text-zinc-700 hover:text-red-500 transition-colors p-1"
                    >
                      <X className="w-4 h-4" />
                    </button>
                  </div>
                  
                  <div className="flex items-center justify-between mt-2 pt-2 border-t border-zinc-900/50">
                    <div className="flex items-center gap-1">
                      <div className="flex items-center bg-zinc-900 rounded-xl p-1 border border-zinc-800">
                        <button 
                          onClick={() => updateQuantity(item.product.id, -1)}
                          className="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center hover:bg-zinc-700 text-zinc-400 transition-colors active:scale-90"
                        >
                          <Minus className="w-3.5 h-3.5" />
                        </button>
                        <div className="w-10 text-center text-xs font-black text-zinc-100">
                          {item.quantity}
                        </div>
                        <button 
                          onClick={() => updateQuantity(item.product.id, 1)}
                          className="w-8 h-8 rounded-lg bg-amber-600 flex items-center justify-center hover:bg-amber-500 text-zinc-950 transition-colors active:scale-90"
                        >
                          <Plus className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </div>
                    <div className="flex items-center gap-1 text-[10px] text-zinc-500 font-bold">
                      <Utensils className="w-3 h-3" />
                      <span>جاهز للتجهيز</span>
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </AnimatePresence>
          {cart.length === 0 && (
            <motion.div 
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="flex-1 flex flex-col items-center justify-center gap-3 opacity-20 py-20"
            >
              <ShoppingCart className="w-10 h-10 text-zinc-500" />
              <p className="text-xs font-medium uppercase tracking-tight">السلة فارغة</p>
            </motion.div>
          )}
        </div>

        <div className="p-5 bg-zinc-950/50 border-t border-zinc-800 space-y-3">
          <div className="space-y-1.5">
            <div className="flex justify-between items-center text-xs text-zinc-500">
              <span>المجموع الفرعي:</span>
              <span>{(cartTotal * 0.86).toFixed(2)} ج.س</span>
            </div>
            <div className="flex justify-between items-center text-xs text-zinc-500">
              <span>الضريبة (14%):</span>
              <span>{(cartTotal * 0.14).toFixed(2)} ج.س</span>
            </div>
            <div className="flex justify-between items-center pt-2 border-t border-zinc-800">
              <span className="text-xs font-bold text-zinc-400 uppercase tracking-widest">الإجمالي:</span>
              <span className="text-xl font-bold text-amber-500">{(cartTotal).toFixed(2)} ج.س</span>
            </div>
          </div>
          
          <div className="grid grid-cols-2 gap-2 pt-2">
            <div className="relative">
              <button
                disabled={cart.length === 0}
                onClick={() => setShowPrintMenu(!showPrintMenu)}
                className="w-full flex flex-col items-center justify-center py-2.5 bg-zinc-800 text-zinc-300 rounded-lg text-[10px] font-bold hover:bg-zinc-700 transition-colors disabled:opacity-30"
              >
                <div className="flex items-center gap-1 mb-1">
                  <Printer className="w-3.5 h-3.5 text-zinc-500" />
                  <ChevronDown className={cn("w-3 h-3 transition-transform", showPrintMenu && "rotate-180")} />
                </div>
                طباعة
              </button>

              <AnimatePresence>
                {showPrintMenu && (
                  <motion.div
                    initial={{ opacity: 0, y: 10, scale: 0.95 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    exit={{ opacity: 0, y: 10, scale: 0.95 }}
                    className="absolute bottom-full mb-2 left-0 w-48 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl z-50 overflow-hidden"
                  >
                    <button
                      onClick={() => printInvoice('customer')}
                      className="w-full flex items-center gap-3 px-4 py-3 hover:bg-zinc-800 text-zinc-100 transition-colors border-b border-zinc-800"
                    >
                      <UserIcon className="w-3.5 h-3.5 text-blue-400" />
                      <div className="text-right">
                        <p className="text-[11px] font-bold">فاتورة العميل</p>
                        <p className="text-[9px] text-zinc-500">تفصيلية مع السعر والضريبة</p>
                      </div>
                    </button>
                    <button
                      onClick={() => printInvoice('kitchen')}
                      className="w-full flex items-center gap-3 px-4 py-3 hover:bg-zinc-800 text-zinc-100 transition-colors"
                    >
                      <Utensils className="w-3.5 h-3.5 text-amber-500" />
                      <div className="text-right">
                        <p className="text-[11px] font-bold">بون المطبخ</p>
                        <p className="text-[9px] text-zinc-500">أصناف وكميات فقط للتجهيز</p>
                      </div>
                    </button>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
            <button
              disabled={cart.length === 0}
              onClick={handleCheckout}
              className="flex flex-col items-center justify-center py-2.5 bg-amber-600 text-zinc-950 rounded-lg text-[10px] font-bold hover:bg-amber-500 transition-colors disabled:opacity-30 shadow-lg shadow-amber-900/20"
            >
              <span>💳</span>
              دفع وإنهاء
            </button>
          </div>
        </div>
      </div>
    </div>

    {/* Checkout Modal */}
    <AnimatePresence>
      {isCheckoutOpen && (
        <div className="fixed inset-0 flex items-center justify-center z-[200] p-4">
          <motion.div 
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={() => setIsCheckoutOpen(false)}
            className="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm"
          />
          <motion.div 
            initial={{ opacity: 0, scale: 0.95, y: 10 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 10 }}
            className="relative w-full max-w-sm glass-card p-6 flex flex-col gap-6 rounded-2xl"
          >
            <div className="flex items-center justify-between">
              <h3 className="text-lg font-bold text-zinc-100 italic">إتمام الدفع</h3>
              <button 
                onClick={() => setIsCheckoutOpen(false)}
                className="p-1.5 text-zinc-600 hover:text-zinc-100 bg-zinc-800 rounded-lg"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="space-y-4">
              <div className="bg-zinc-950/50 p-4 rounded-xl border border-zinc-800 flex justify-between items-center">
                <span className="text-sm font-bold text-zinc-400">الإجمالي المطلوب:</span>
                <span className="text-2xl font-black text-amber-500">{cartTotal.toFixed(2)} ج.س</span>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <button
                  onClick={() => setPaymentMethod('CASH')}
                  className={cn(
                    "flex flex-col items-center gap-2 p-4 rounded-xl border transition-all",
                    paymentMethod === 'CASH' 
                      ? "bg-amber-600/10 border-amber-500 text-amber-500" 
                      : "bg-zinc-900 border-zinc-800 text-zinc-500 hover:border-zinc-700"
                  )}
                >
                  <span className="text-xl">💵</span>
                  <span className="text-xs font-bold">دفع نقدي</span>
                </button>
                <button
                  onClick={() => setPaymentMethod('BANKAK')}
                  className={cn(
                    "flex flex-col items-center gap-2 p-4 rounded-xl border transition-all",
                    paymentMethod === 'BANKAK' 
                      ? "bg-blue-600/10 border-blue-500 text-blue-500" 
                      : "bg-zinc-900 border-zinc-800 text-zinc-500 hover:border-zinc-700"
                  )}
                >
                  <span className="text-xl">📱</span>
                  <span className="text-xs font-bold">تطبيق بنكك</span>
                </button>
              </div>

              <AnimatePresence mode="wait">
                {paymentMethod === 'BANKAK' && (
                  <motion.div
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: 'auto' }}
                    exit={{ opacity: 0, height: 0 }}
                    className="overflow-hidden"
                  >
                    <div className="flex flex-col gap-2 mt-2">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase pr-1">رقم العملية (آخر 4 أرقام)</label>
                      <input 
                        type="text"
                        maxLength={4}
                        placeholder="0000"
                        className="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-center text-xl font-mono font-bold tracking-[0.5em] text-blue-400 focus:border-blue-500 outline-none"
                        value={transactionId}
                        onChange={(e) => setTransactionId(e.target.value.replace(/\D/g, ''))}
                      />
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            <button 
              onClick={confirmOrder}
              className="w-full py-4 bg-amber-600 text-zinc-950 rounded-xl text-sm font-black hover:bg-amber-500 transition-all shadow-lg shadow-amber-900/20 active:scale-[0.98]"
            >
              تأكيد العملية وحفظ الطلب
            </button>
          </motion.div>
        </div>
      )}
    </AnimatePresence>

    {/* Confirmation Modal */}
    <AnimatePresence>
      {showClearConfirm && (
        <div className="fixed inset-0 flex items-center justify-center z-[200] p-4">
          <motion.div 
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={() => setShowClearConfirm(false)}
            className="absolute inset-0 bg-zinc-950/80 backdrop-blur-sm"
          />
          <motion.div 
            initial={{ opacity: 0, scale: 0.95, y: 10 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 10 }}
            className="relative w-full max-w-xs glass-card p-6 flex flex-col items-center text-center gap-6 rounded-2xl"
          >
            <div className="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center">
              <Trash2 className="w-8 h-8 text-red-500" />
            </div>
            <div>
              <h3 className="text-lg font-bold text-zinc-100">إلغاء الطلب بالكامل؟</h3>
              <p className="text-xs text-zinc-500 mt-1">سيتم مسح كافة الأصناف المضافة للسلة بشكل نهائي.</p>
            </div>
            <div className="grid grid-cols-2 gap-3 w-full">
              <button 
                onClick={() => setShowClearConfirm(false)}
                className="py-2.5 bg-zinc-800 text-zinc-400 rounded-xl text-xs font-bold hover:bg-zinc-700 transition-colors"
              >
                تراجع
              </button>
              <button 
                onClick={() => {
                  setCart([]);
                  setShowClearConfirm(false);
                }}
                className="py-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-500 transition-colors shadow-lg shadow-red-900/20"
              >
                نعم، مسح السلة
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
    </>
  );
}
