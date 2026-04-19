<?php include 'includes/header.php'; ?>

<div class="row g-4 h-100">
    <!-- Products Section -->
    <div class="col-lg-8 h-100 overflow-auto">
        <div class="glass-card p-3 mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-zinc-950 border-zinc-800 text-zinc-500 rounded-start-pill b-l-0">
                            <i data-lucide="search" style="width: 14px;"></i>
                        </span>
                        <input type="text" class="form-control bg-zinc-950 border-zinc-800 text-white rounded-end-pill shadow-none text-xs" placeholder="بحث عن صنف أو مكون...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select bg-zinc-950 border-zinc-800 text-zinc-400 rounded-3 text-xs shadow-none">
                        <option value="">كل الأقسام</option>
                        <?php 
                        $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
                        foreach($cats as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
            <?php 
            $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
            foreach($products as $p): ?>
            <div class="col">
                <div class="glass-card p-3 h-100 card-product position-relative">
                    <div class="bg-zinc-950 rounded-3 mb-3 d-flex align-items-center justify-content-center border border-zinc-800 overflow-hidden" style="aspect-ratio: 1/1;">
                        <?php if(!empty($p['image'])): ?>
                             <img src="<?php echo $p['image']; ?>" class="w-100 h-100 object-fit-cover opacity-75">
                        <?php else: ?>
                            <span class="text-zinc-800 fs-3 fw-bold"><?php echo mb_substr($p['name_ar'], 0, 1); ?></span>
                        <?php endif; ?>
                        
                        <?php if($p['in_stock'] <= $p['min_threshold']): ?>
                            <div class="position-absolute top-0 start-0 m-2 badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 text-[8px] rounded-pill">منخفض</div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="text-white fw-bold mb-0 text-xs text-truncate"><?php echo $p['name_ar']; ?></h6>
                        <span class="text-amber-500 fw-bold text-xs"><?php echo formatCurrency($p['price']); ?></span>
                    </div>
                    <p class="text-zinc-500 mb-3 text-truncate" style="font-size: 9px;"><?php echo $p['ingredients']; ?></p>
                    <button class="btn btn-accent w-full py-1.5 rounded-2 text-[10px] uppercase">إضافة للفاتورة</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Sidebar: Cart -->
    <div class="col-lg-4 h-100">
        <div class="glass-card h-100 d-flex flex-column overflow-hidden">
            <div class="p-4 border-bottom border-zinc-800 d-flex justify-content-between align-items-center">
                <h6 class="text-white fw-bold mb-0 italic">الفاتورة الحالية</h6>
                <span class="badge bg-zinc-800 text-zinc-500 fw-normal">#INV-8832</span>
            </div>
            
            <div class="flex-grow-1 p-4 overflow-auto d-flex flex-column gap-3">
                <!-- Empty Cart State -->
                <div class="my-auto text-center opacity-25 py-5">
                    <i data-lucide="shopping-cart" class="text-zinc-500 mb-3" style="width: 40px; height: 40px;"></i>
                    <p class="text-xs fw-bold tracking-tight">السلة فارغة</p>
                </div>
            </div>

            <div class="p-4 bg-zinc-950 border-top border-zinc-800">
                <!-- Payment Method and Notes -->
                <div class="mb-4">
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">طريقة الدفع</label>
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_cash" autocomplete="off" checked>
                            <label class="btn btn-outline-zinc w-100 py-2 text-[11px] fw-bold d-flex align-items-center justify-content-center gap-2" for="pay_cash">
                                <i data-lucide="banknote" style="width: 14px;"></i> كاش
                            </label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_bank" autocomplete="off">
                            <label class="btn btn-outline-zinc w-100 py-2 text-[11px] fw-bold d-flex align-items-center justify-content-center gap-2" for="pay_bank">
                                <i data-lucide="smartphone" style="width: 14px;"></i> بنكك
                            </label>
                        </div>
                    </div>
                    
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">ملاحظات الطلب</label>
                    <textarea class="form-control bg-zinc-950 border-zinc-800 text-zinc-300 text-[11px] rounded-3 focus:border-amber-500 shadow-none" rows="2" placeholder="أي طلبات خاصة (مثلاً: بدون بصل)..."></textarea>
                </div>

                <div class="mb-4 space-y-2">
                    <div class="d-flex justify-content-between text-zinc-500 text-[11px]">
                        <span>المجموع الفرعي:</span>
                        <span>0.00 ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between text-zinc-500 text-[11px]">
                        <span>الضريبة (14%):</span>
                        <span>0.00 ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top border-zinc-800 mt-2">
                        <span class="text-zinc-400 fw-bold uppercase tracking-widest text-[10px]">الإجمالي:</span>
                        <span class="text-amber-500 fw-bold h4 mb-0">0.00 ر.س</span>
                    </div>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-full py-2.5 rounded-3 border-zinc-800 text-zinc-300 text-[10px] fw-bold">
                            <i data-lucide="printer" style="width: 14px;" class="mb-1 d-block mx-auto"></i>
                            طباعة
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-accent w-full py-2.5 rounded-3 text-[10px] fw-bold shadow-lg shadow-amber-950/20">
                            <span class="d-block">💳</span>
                            دفع وإنهاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
