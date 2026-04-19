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
  addOrder: (items: Order['items']) => void;
  
  // Auth Actions
  login: (pin: string) => boolean;
  logout: () => void;
  
  // UI State
  activeView: 'dashboard' | 'pos' | 'menu' | 'inventory' | 'reports' | 'shipping';
  setActiveView: (view: StoreState['activeView']) => void;
}

export const useStore = create<StoreState>()(
  persist(
    (set, get) => ({
      categories: [
        { id: '1', name: 'المأكولات', type: 'FOOD' },
        { id: '2', name: 'العصائر', type: 'JUICE' },
      ],
      products: [
        { id: 'p1', categoryId: '1', name: 'Margherita Pizza', nameAr: 'بيتزا مارجريتا', ingredients: ['طماطم', 'موتزاريلا', 'ريحان'], price: 120, inStock: 50 },
        { id: 'p2', categoryId: '1', name: 'Chicken Burger', nameAr: 'برجر دجاج', ingredients: ['دجاج', 'خس', 'صوص سري'], price: 85, inStock: 30 },
        { id: 'p3', categoryId: '2', name: 'Mango Juice', nameAr: 'عصير مانجو', ingredients: ['مانجو طازج', 'سكر'], price: 45, inStock: 100 },
        { id: 'p4', categoryId: '2', name: 'Lemon Mint', nameAr: 'ليمون نعناع', ingredients: ['ليمون', 'نعناع', 'ثلج'], price: 35, inStock: 80 },
      ],
      orders: [],
      users: [
        { id: 'u1', username: 'admin', name: 'مدير النظام', role: 'ADMIN', pin: '1234' },
        { id: 'u2', username: 'manager', name: 'مشرف الصالة', role: 'MANAGER', pin: '2222' },
        { id: 'u3', username: 'waiter', name: 'موظف مبيعات', role: 'WAITER', pin: '1111' },
      ],
      currentUser: null,
      activeView: 'pos',
      
      setActiveView: (view) => set({ activeView: view }),

      login: (pin) => {
        const user = get().users.find(u => u.pin === pin);
        if (user) {
          set({ currentUser: user });
          return true;
        }
        return false;
      },

      logout: () => set({ currentUser: null, activeView: 'pos' }),
      
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
      
      addOrder: (items) => {
        const total = items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
        const newOrder: Order = {
          id: `ORD-${Date.now()}`,
          items,
          total,
          timestamp: Date.now(),
          status: 'COMPLETED',
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
