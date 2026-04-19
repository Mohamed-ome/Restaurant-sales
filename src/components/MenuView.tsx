import React, { useState } from 'react';
import { useStore } from '../store/useStore';
import { Plus, Trash2, Edit2, X, PlusCircle, Search } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { formatCurrency } from '../lib/utils';
import { Product, CategoryType } from '../types';

export default function MenuView() {
  const { products, categories, addProduct, deleteProduct, updateProduct, addCategory, deleteCategory } = useStore();
  const [isProductModalOpen, setIsProductModalOpen] = useState(false);
  const [isCategoryModalOpen, setIsCategoryModalOpen] = useState(false);
  
  const [newProduct, setNewProduct] = useState<Omit<Product, 'id'>>({
    name: '',
    nameAr: '',
    categoryId: categories[0]?.id || '',
    ingredients: [],
    price: 0,
    inStock: 0,
  });

  const [newCategoryName, setNewCategoryName] = useState('');
  const [newCategoryType, setNewCategoryType] = useState<CategoryType>('FOOD');

  const handleAddProduct = () => {
    if (!newProduct.nameAr || newProduct.price <= 0) return;
    addProduct(newProduct);
    setIsProductModalOpen(false);
    setNewProduct({
      name: '',
      nameAr: '',
      categoryId: categories[0]?.id || '',
      ingredients: [],
      price: 0,
      inStock: 0,
    });
  };

  const handleAddCategory = () => {
    if (!newCategoryName) return;
    addCategory({ name: newCategoryName, type: newCategoryType });
    setIsCategoryModalOpen(false);
    setNewCategoryName('');
  };

  return (
    <div className="flex flex-col gap-6 h-full overflow-hidden" dir="rtl">
      <div className="flex justify-between items-center glass-card p-4 rounded-xl">
        <h2 className="font-bold text-zinc-100 text-lg italic">إدارة قائمة الطعام</h2>
        <div className="flex gap-2">
          <button 
            onClick={() => setIsCategoryModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2 bg-zinc-800 text-zinc-300 rounded-lg text-xs font-bold hover:bg-zinc-700 transition-colors"
          >
            <PlusCircle className="w-4 h-4" />
            إضافة قسم
          </button>
          <button 
            onClick={() => setIsProductModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2 bg-amber-600 text-zinc-950 rounded-lg text-xs font-bold hover:bg-amber-500 transition-colors shadow-lg shadow-amber-900/10"
          >
            <Plus className="w-4 h-4" />
            إضافة صنف جديد
          </button>
        </div>
      </div>

      <div className="flex-1 overflow-y-auto pr-1 pb-10">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          {products.map((product) => (
            <div key={product.id} className="glass-card rounded-xl p-4 group relative overflow-hidden border border-zinc-800 hover:border-amber-500/30 transition-all">
              <div className="absolute top-2 left-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button 
                  onClick={() => deleteProduct(product.id)}
                  className="p-1.5 bg-zinc-800 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm"
                >
                  <Trash2 className="w-3.5 h-3.5" />
                </button>
              </div>
              <div className="w-12 h-12 bg-zinc-950 border border-zinc-900 rounded-lg mb-4 flex items-center justify-center font-black text-xl text-zinc-700">
                {product.nameAr[0]}
              </div>
              <h3 className="font-bold text-zinc-100 mb-1">{product.nameAr}</h3>
              <p className="text-[10px] text-zinc-500 mb-3 truncate">{product.ingredients.join(' • ')}</p>
              <div className="flex justify-between items-center">
                <span className="text-amber-500 font-bold">{product.price} ر.س</span>
                <span className="text-[9px] bg-zinc-800 text-zinc-500 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter">
                  {categories.find(c => c.id === product.categoryId)?.name || 'عام'}
                </span>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Modals... */}
      <AnimatePresence>
        {isProductModalOpen && (
          <div className="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <motion.div 
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
              className="bg-zinc-900 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-zinc-800"
            >
              <div className="p-6 border-b border-zinc-800 flex justify-between items-center">
                <h3 className="font-bold text-zinc-100 text-lg italic uppercase tracking-tight">إضافة صنف جديد</h3>
                <button onClick={() => setIsProductModalOpen(false)} className="text-zinc-500 p-2 hover:bg-zinc-800 rounded-full transition-colors">
                  <X className="w-5 h-5" />
                </button>
              </div>
              <div className="p-6 flex flex-col gap-4">
                <div className="flex flex-col gap-1.5 text-right">
                  <label className="text-[10px] font-bold text-zinc-500 uppercase pr-1">اسم الصنف (بالعربية)</label>
                  <input 
                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 focus:border-amber-500 outline-none text-zinc-100 text-sm" 
                    placeholder="مثال: شاورما لحم"
                    value={newProduct.nameAr}
                    onChange={e => setNewProduct({...newProduct, nameAr: e.target.value, name: e.target.value})}
                  />
                </div>
                <div className="grid grid-cols-2 gap-4 text-right">
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase pr-1">السعر (ر.س)</label>
                    <input 
                      type="number"
                      className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 focus:border-amber-500 outline-none text-zinc-100 text-sm" 
                      value={newProduct.price}
                      onChange={e => setNewProduct({...newProduct, price: Number(e.target.value)})}
                    />
                  </div>
                  <div className="flex flex-col gap-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase pr-1">القسم الرئيسي</label>
                    <select 
                      className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 focus:border-amber-500 outline-none text-zinc-400 text-sm"
                      value={newProduct.categoryId}
                      onChange={e => setNewProduct({...newProduct, categoryId: e.target.value})}
                    >
                      {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                  </div>
                </div>
                <div className="flex flex-col gap-1.5 text-right">
                  <label className="text-[10px] font-bold text-zinc-500 uppercase pr-1">المكونات (بالعربية)</label>
                  <textarea 
                    className="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-2.5 focus:border-amber-500 outline-none text-zinc-100 text-sm min-h-[80px]" 
                    placeholder="لحم، بقدونس، طحينة..."
                    onChange={e => setNewProduct({...newProduct, ingredients: e.target.value.split('،').map(i => i.trim())})}
                  />
                </div>
              </div>
              <div className="p-6 bg-zinc-950/50 flex gap-2 border-t border-zinc-800">
                <button 
                  onClick={() => setIsProductModalOpen(false)}
                  className="flex-1 py-3 border border-zinc-800 rounded-lg font-bold text-xs text-zinc-500 hover:bg-zinc-800 transition-colors"
                >
                  إلغاء
                </button>
                <button 
                  onClick={handleAddProduct}
                  className="flex-1 py-3 bg-amber-600 text-zinc-950 rounded-lg font-bold text-xs hover:bg-amber-500 transition-colors shadow-lg shadow-amber-900/10"
                >
                  حفظ الصنف
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}

function cn(...inputs: any[]) {
  return inputs.filter(Boolean).join(' ');
}
