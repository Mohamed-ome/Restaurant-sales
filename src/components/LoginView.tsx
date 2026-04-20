import React, { useState } from 'react';
import { useStore } from '../store/useStore';
import { Lock, Delete, ChevronRight, User as UserIcon } from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { cn } from '../lib/utils';

export default function LoginView() {
  const { login, users } = useStore();
  const [pin, setPin] = useState('');
  const [error, setError] = useState(false);

  const handleKeyPress = (num: string) => {
    if (pin.length < 4) {
      const newPin = pin + num;
      setPin(newPin);
      if (newPin.length === 4) {
        const success = login(newPin);
        if (!success) {
          setError(true);
          setTimeout(() => {
            setError(false);
            setPin('');
          }, 600);
        }
      }
    }
  };

  const handleBackspace = () => {
    setPin(pin.slice(0, -1));
  };

  return (
    <div className="fixed inset-0 bg-zinc-950 flex flex-col items-center justify-center p-4 z-[100]" dir="rtl">
      <div className="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none overflow-hidden">
        <div className="absolute -top-20 -left-20 w-96 h-96 bg-amber-500 rounded-full blur-[120px]" />
        <div className="absolute top-1/2 -right-20 w-80 h-80 bg-orange-600 rounded-full blur-[100px]" />
      </div>

      <motion.div 
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="w-full max-w-sm flex flex-col items-center gap-8 relative z-10"
      >
        <div className="text-center space-y-2">
          <div className="w-16 h-16 bg-zinc-900 rounded-2xl flex items-center justify-center mx-auto mb-6 accent-border">
            <Lock className="w-8 h-8 text-amber-500" />
          </div>
          <h1 className="text-2xl font-bold text-zinc-100 italic">نظام منتزه حاتم السياحي</h1>
          <p className="text-zinc-500 text-xs font-medium uppercase tracking-widest">أدخل رمز الدخول للاستمرار</p>
        </div>

        <div className="flex gap-4 justify-center py-4">
          {[0, 1, 2, 3].map((i) => (
            <motion.div
              key={i}
              animate={error ? { x: [0, -10, 10, -10, 10, 0] } : {}}
              className={cn(
                "w-4 h-4 rounded-full border-2 transition-all duration-300",
                pin.length > i 
                  ? "bg-amber-500 border-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.5)]" 
                  : "border-zinc-800 bg-transparent"
              )}
            />
          ))}
        </div>

        <div className="grid grid-cols-3 gap-4 w-full px-4">
          {['1', '2', '3', '4', '5', '6', '7', '8', '9'].map((num) => (
            <button
              key={num}
              onClick={() => handleKeyPress(num)}
              className="h-16 glass-card rounded-xl text-xl font-bold text-zinc-300 hover:text-white hover:border-amber-500/50 transition-all active:scale-95"
            >
              {num}
            </button>
          ))}
          <button
            onClick={handleBackspace}
            className="h-16 flex items-center justify-center text-zinc-500 hover:text-red-400 grayscale hover:grayscale-0 transition-all active:scale-95"
          >
            <Delete className="w-6 h-6" />
          </button>
          <button
            onClick={() => handleKeyPress('0')}
            className="h-16 glass-card rounded-xl text-xl font-bold text-zinc-300 hover:text-white hover:border-amber-500/50 transition-all active:scale-95"
          >
            0
          </button>
          <div className="h-16 flex items-center justify-center opacity-20">
             <ChevronRight className="w-6 h-6 text-zinc-500" />
          </div>
        </div>

        <div className="mt-8 pt-8 border-t border-zinc-900 w-full">
           <p className="text-[9px] text-zinc-700 text-center uppercase tracking-widest font-bold mb-4">الموظفين المتاحين (لأغراض العرض)</p>
           <div className="flex flex-wrap justify-center gap-2">
              {users.map(u => (
                <div key={u.id} className="group flex items-center gap-2 px-3 py-1.5 glass-card rounded-lg border-zinc-900">
                  <UserIcon className="w-3 h-3 text-zinc-600" />
                  <div className="text-right">
                    <p className="text-[10px] font-bold text-zinc-400 leading-none">{u.name}</p>
                    <p className="text-[8px] text-amber-600/50 uppercase tracking-tighter">{u.pin}</p>
                  </div>
                </div>
              ))}
           </div>
        </div>
      </motion.div>
    </div>
  );
}
