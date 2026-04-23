import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { Category, Product, Order, CategoryType, User, UserRole } from '../types';

interface StoreState {
  categories: Category[];
  products: Product[];
  orders: Order[];
  users: User[];
  currentUser: User | null;
  
  // Actions
  addCategory: (category: Omit<Category, 'id'>) => void;
  deleteCategory: (id: string) => void;
  addProduct: (product: Omit<Product, 'id'>) => void;
  updateProduct: (id: string, product: Partial<Product>) => void;
  deleteProduct: (id: string) => void;
  addOrder: (items: Order['items'], paymentMethod: Order['paymentMethod'], transactionId?: string) => void;
  
  // Auth Actions
  login: (pin: string) => boolean;
  logout: () => void;
  
  // UI State
  activeView: 'dashboard' | 'pos' | 'menu' | 'inventory' | 'reports' | 'shipping' | 'sales_insights';
  setActiveView: (view: StoreState['activeView']) => void;
}

export const useStore = create<StoreState>()(
  persist(
    (set, get) => ({
      categories: [
        { id: '1', name: 'المأكولات', type: 'FOOD' },
        { id: '2', name: 'العصائر', type: 'JUICE' },
        { id: '3', name: 'الأسماك', type: 'FOOD' },
        { id: '4', name: 'المشويات', type: 'FOOD' },
      ],
      products: [
        { id: 'p1', categoryId: '1', name: 'Margherita Pizza', nameAr: 'بيتزا مارجريتا', ingredients: ['طماطم', 'موتزاريلا', 'ريحان'], price: 120, inStock: 50, image: 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?q=80&w=200&auto=format&fit=crop' },
        { id: 'p2', categoryId: '1', name: 'Chicken Burger', nameAr: 'برجر دجاج', ingredients: ['دجاج', 'خس', 'صوص سري'], price: 85, inStock: 30, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=200&auto=format&fit=crop' },
        { id: 'p3', categoryId: '2', name: 'Mango Juice', nameAr: 'عصير مانجو', ingredients: ['مانجو طازج', 'سكر'], price: 45, inStock: 100, image: 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?q=80&w=200&auto=format&fit=crop' },
        { id: 'p4', categoryId: '2', name: 'Lemon Mint', nameAr: 'ليمون نعناع', ingredients: ['ليمون', 'نعناع', 'ثلج'], price: 35, inStock: 80, image: 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?q=80&w=200&auto=format&fit=crop' },
        { id: 'p5', categoryId: '3', name: 'Fried Tilapia', nameAr: 'سمك بلطي مقلي', ingredients: ['سمك بلطي', 'تتبيلة خاصة', 'ليمون'], price: 150, inStock: 20, image: 'https://images.unsplash.com/photo-1599084993091-1cb5c0721cc6?q=80&w=200&auto=format&fit=crop' },
        { id: 'p6', categoryId: '3', name: 'Grilled Sea Bream', nameAr: 'سمك قاروص مشوي', ingredients: ['سمك قاروص', 'أعشاب', 'زيت زيتون'], price: 250, inStock: 15, image: 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=200&auto=format&fit=crop' },
        { id: 'p7', categoryId: '4', name: 'Mixed Grill', nameAr: 'مشاوي مشكلة', ingredients: ['كفتة', 'شيش طاووق', 'كباب'], price: 320, inStock: 25, image: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=200&auto=format&fit=crop' },
        { id: 'p8', categoryId: '4', name: 'Grilled Chicken', nameAr: 'دجاج مشوي على الفحم', ingredients: ['نصف دجاجة', 'ثومية', 'خبز'], price: 180, inStock: 30, image: 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?q=80&w=200&auto=format&fit=crop' },
      ],
      orders: [],
      users: [
        { id: 'u1', username: 'admin', name: 'مدير النظام', role: 'ADMIN', pin: '1234' },
        { id: 'u2', username: 'manager', name: 'مشرف الصالة', role: 'MANAGER', pin: '2222' },
      ],
      currentUser: null,
      activeView: 'dashboard',
      
      setActiveView: (view) => set({ activeView: view }),

      login: (pin) => {
        const user = get().users.find(u => u.pin === pin);
        if (user) {
          set({ currentUser: user });
          return true;
        }
        return false;
      },

      logout: () => set({ currentUser: null, activeView: 'dashboard' }),
      
      addCategory: (category) => set((state) => ({
        categories: [...state.categories, { ...category, id: Math.random().toString(36).substr(2, 9) }]
      })),
      
      deleteCategory: (id) => set((state) => ({
        categories: state.categories.filter((c) => c.id !== id)
      })),
      
      addProduct: (product) => set((state) => ({
        products: [...state.products, { ...product, id: Math.random().toString(36).substr(2, 9) }]
      })),
      
      updateProduct: (id, updatedProduct) => set((state) => ({
        products: state.products.map((p) => p.id === id ? { ...p, ...updatedProduct } : p)
      })),
      
      deleteProduct: (id) => set((state) => ({
        products: state.products.filter((p) => p.id !== id)
      })),
      
      addOrder: (items, paymentMethod, transactionId) => {
        const total = items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
        const newOrder: Order = {
          id: `ORD-${Date.now()}`,
          items,
          total,
          timestamp: Date.now(),
          status: 'COMPLETED',
          paymentMethod,
          transactionId
        };
        
        // Update stock
        set((state) => {
          const newProducts = state.products.map(p => {
            const orderItem = items.find(item => item.productId === p.id);
            if (orderItem) {
              return { ...p, inStock: Math.max(0, p.inStock - orderItem.quantity) };
            }
            return p;
          });
          
          return {
            orders: [newOrder, ...state.orders],
            products: newProducts
          };
        });
      },
    }),
    {
      name: 'al-mantiqa-storage',
    }
  )
);
