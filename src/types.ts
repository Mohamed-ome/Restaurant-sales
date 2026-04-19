export type CategoryType = 'FOOD' | 'JUICE' | 'OTHER';

export interface Category {
  id: string;
  name: string;
  type: CategoryType;
}

export interface Product {
  id: string;
  categoryId: string;
  name: string;
  nameAr: string;
  ingredients: string[];
  price: number;
  image?: string;
  inStock: number;
}

export interface OrderItem {
  productId: string;
  productName: string;
  quantity: number;
  price: number;
}

export interface Order {
  id: string;
  items: OrderItem[];
  total: number;
  timestamp: number;
  status: 'COMPLETED' | 'CANCELLED';
}

export type UserRole = 'ADMIN' | 'MANAGER' | 'WAITER';

export interface User {
  id: string;
  username: string;
  name: string;
  role: UserRole;
  pin: string; // 4-digit PIN for quick POS access
}

export interface InventoryItem {
  id: string;
  productId: string;
  quantity: number;
  minThreshold: number;
}
