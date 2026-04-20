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
                        <input type="text" id="productSearch" class="form-control bg-zinc-950 border-zinc-800 text-white rounded-end-pill shadow-none text-xs" placeholder="بحث عن صنف أو مكون...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="categoryFilter" class="form-select bg-zinc-950 border-zinc-800 text-zinc-400 rounded-3 text-xs shadow-none">
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

        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3" id="productGrid">
            <?php 
            $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
            foreach($products as $p): ?>
            <div class="col product-card-wrapper" data-category="<?php echo $p['category_id']; ?>" data-name="<?php echo $p['name_ar']; ?>" data-ingredients="<?php echo $p['ingredients']; ?>">
                <div class="glass-card p-3 h-100 card-product position-relative">
                    <div class="bg-zinc-950 rounded-3 mb-3 d-flex align-items-center justify-content-center border border-zinc-800 overflow-hidden" style="aspect-ratio: 1/1;">
                        <?php if(!empty($p['image'])): ?>
                             <img src="<?php echo $p['image']; ?>" class="w-100 h-100 object-fit-cover opacity-75">
                        <?php else: ?>
                            <svg width="100%" height="100%" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-20">
                                <rect width="100" height="100" fill="#09090b"/>
                                <circle cx="50" cy="50" r="30" stroke="#3f3f46" stroke-width="2"/>
                                <path d="M40 50H60M50 40V60" stroke="#3f3f46" stroke-width="2" stroke-linecap="round"/>
                            </svg>
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
                    <button class="btn btn-accent w-full py-1.5 rounded-2 text-[10px] uppercase add-to-cart" 
                            data-id="<?php echo $p['id']; ?>" 
                            data-name="<?php echo $p['name_ar']; ?>" 
                            data-image="<?php echo $p['image'] ?? ''; ?>"
                            data-price="<?php echo $p['price']; ?>">إضافة للفاتورة</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Sidebar: Cart -->
    <div class="col-lg-4 h-100">
        <div class="glass-card h-100 d-flex flex-column overflow-hidden">
            <div class="p-4 border-bottom border-zinc-800 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white fw-bold mb-0 italic">الفاتورة الحالية</h6>
                    <p class="text-zinc-600 mb-0" style="font-size: 9px;"><?php echo date('Y/m/d'); ?> | <span id="liveClock"><?php echo date('H:i'); ?></span></p>
                </div>
                <span class="badge bg-zinc-800 text-zinc-500 fw-normal" id="orderNumber">#INV-<?php echo time() % 10000; ?></span>
            </div>
            
            <div class="flex-grow-1 p-4 overflow-auto d-flex flex-column gap-3" id="cartContainer">
                <!-- Empty Cart State -->
                <div class="my-auto text-center opacity-25 py-5" id="emptyCart">
                    <i data-lucide="shopping-cart" class="text-zinc-500 mb-3" style="width: 40px; height: 40px;"></i>
                    <p class="text-xs fw-bold tracking-tight">السلة فارغة</p>
                </div>
            </div>

            <div class="p-4 bg-zinc-950 border-top border-zinc-800">
                <!-- قسم اختيار نوع الطلب وطريقة الدفع والملاحظات -->
                <div class="mb-4">
                    <!-- خيار سفري أو محلي -->
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">نوع الطلب</label>
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="dining_option" id="dine_local" value="DINEIN" autocomplete="off" checked>
                            <label class="btn btn-outline-zinc w-100 py-2 text-[11px] fw-bold d-flex align-items-center justify-content-center gap-2" for="dine_local">
                                <i data-lucide="utensils" style="width: 14px;"></i> محلي
                            </label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="dining_option" id="dine_away" value="TAKEAWAY" autocomplete="off">
                            <label class="btn btn-outline-zinc w-100 py-2 text-[11px] fw-bold d-flex align-items-center justify-content-center gap-2" for="dine_away">
                                <i data-lucide="package" style="width: 14px;"></i> سفري
                            </label>
                        </div>
                    </div>

                    <!-- اختيار وسيلة الدفع (كاش أو بنكك) -->
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">طريقة الدفع</label>
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="CASH" autocomplete="off" checked>
                            <label class="btn btn-outline-zinc w-100 py-2 text-[11px] fw-bold d-flex align-items-center justify-content-center gap-2" for="pay_cash">
                                <i data-lucide="banknote" style="width: 14px;"></i> كاش
                            </label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_bank" value="BANKAK" autocomplete="off">
                            <label class="btn btn-outline-zinc w-100 py-2 text-[11px] fw-bold d-flex align-items-center justify-content-center gap-2" for="pay_bank">
                                <i data-lucide="smartphone" style="width: 14px;"></i> بنكك
                            </label>
                        </div>
                    </div>
                    
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">ملاحظات الطلب</label>
                    <textarea id="orderNotes" class="form-control bg-zinc-950 border-zinc-800 text-zinc-300 text-[11px] rounded-3 focus:border-amber-500 shadow-none" rows="2" placeholder="أي طلبات خاصة (مثلاً: بدون بصل)..."></textarea>
                </div>

                <div class="mb-4 space-y-2">
                    <div class="d-flex justify-content-between text-zinc-500 text-[11px]">
                        <span>المجموع الفرعي:</span>
                        <span id="subTotal">0.00 ج.س</span>
                    </div>
                    <div class="d-flex justify-content-between text-zinc-500 text-[11px]">
                        <span>الضريبة (0%):</span>
                        <span id="tax">0.00 ج.س</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top border-zinc-800 mt-2">
                        <span class="text-zinc-400 fw-bold uppercase tracking-widest text-[10px]">الإجمالي:</span>
                        <span class="text-amber-500 fw-bold h4 mb-0" id="grandTotal">0.00 ج.س</span>
                    </div>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-full py-2.5 rounded-3 border-zinc-800 text-zinc-300 text-[10px] fw-bold" onclick="printCart()">
                            <i data-lucide="printer" style="width: 14px;" class="mb-1 d-block mx-auto"></i>
                            طباعة
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-accent w-full py-2.5 rounded-3 text-[10px] fw-bold shadow-lg shadow-amber-950/20" onclick="checkout()">
                            <span class="d-block" id="checkoutIcon">💳</span>
                            دفع وإنهاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    
    // Add to Cart
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', () => {
            const product = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                image: btn.dataset.image,
                price: parseFloat(btn.dataset.price),
                quantity: 1
            };
            
            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push(product);
            }
            updateCartUI();
        });
    });

    function updateCartUI() {
        const cartContainer = document.getElementById('cartContainer');
        const emptyCart = document.getElementById('emptyCart');
        
        if (emptyCart) {
            if (cart.length === 0) {
                cartContainer.innerHTML = '';
                cartContainer.appendChild(emptyCart);
                emptyCart.classList.remove('d-none');
            } else {
                emptyCart.classList.add('d-none');
                cartContainer.innerHTML = cart.map((item, index) => {
                    const imageHtml = item.image 
                        ? `<img src="${item.image}" class="w-100 h-100 object-fit-cover rounded-2 opacity-80">`
                        : `<svg width="100%" height="100%" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-20">
                                <rect width="100" height="100" fill="#18181b"/>
                                <circle cx="50" cy="50" r="30" stroke="#3f3f46" stroke-width="2"/>
                                <path d="M40 50H60M50 40V60" stroke="#3f3f46" stroke-width="2" stroke-linecap="round"/>
                           </svg>`;

                    return `
                    <div class="d-flex align-items-center gap-3 p-2 bg-zinc-950 rounded-3 border border-zinc-800">
                        <div class="bg-zinc-900 rounded-2 border border-zinc-800 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                            ${imageHtml}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="text-white text-[11px] fw-bold mb-0 text-truncate">${item.name}</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-amber-500 fw-bold text-[10px]">${item.price} ج.س</span>
                                <span class="text-zinc-600 text-[9px]">× ${item.quantity}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-zinc-800 p-1 text-zinc-500" onclick="updateQty(${index}, -1)">
                                <i data-lucide="minus" style="width: 10px; height: 10px;"></i>
                            </button>
                            <button class="btn btn-zinc-800 p-1 text-zinc-500" onclick="updateQty(${index}, 1)">
                                <i data-lucide="plus" style="width: 10px; height: 10px;"></i>
                            </button>
                        </div>
                    </div>
                    `;
                }).join('');
                lucide.createIcons();
            }
        }
        
        calculateTotals();
    }

    function updateQty(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        updateCartUI();
    }

    function calculateTotals() {
        const subTotal = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
        document.getElementById('subTotal').innerText = subTotal.toFixed(2) + ' ج.س';
        document.getElementById('grandTotal').innerText = subTotal.toFixed(2) + ' ج.س';
    }

    // Search and Filter logic
    const searchInput = document.getElementById('productSearch');
    const categorySelect = document.getElementById('categoryFilter');
    const products = document.querySelectorAll('.product-card-wrapper');

    function filterProducts() {
        const query = searchInput.value.toLowerCase();
        const categoryId = categorySelect.value;
        
        products.forEach(p => {
            const name = p.dataset.name.toLowerCase();
            const ingredients = p.dataset.ingredients.toLowerCase();
            const cat = p.dataset.category;
            
            const matchQuery = name.includes(query) || ingredients.includes(query);
            const matchCat = !categoryId || cat === categoryId;
            
            if (p) {
                if (matchQuery && matchCat) {
                    p.classList.remove('d-none');
                } else {
                    p.classList.add('d-none');
                }
            }
        });
    }

    searchInput.addEventListener('input', filterProducts);
    categorySelect.addEventListener('change', filterProducts);

    // Checkout Logic
    async function checkout() {
        if (cart.length === 0) {
            alert('السلة فارغة!');
            return;
        }

        const confirmBtn = document.querySelector('button[onclick="checkout()"]');
        const originalContent = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = 'جاري المعالجة...';

        // تجميع بيانات الطلب بما في ذلك النوع والوسيلة والملاحظات
        const orderData = {
            items: cart,
            total: cart.reduce((acc, item) => acc + (item.price * item.quantity), 0),
            payment_method: document.querySelector('input[name="payment_method"]:checked').value,
            dining_option: document.querySelector('input[name="dining_option"]:checked').value,
            notes: document.getElementById('orderNotes').value
        };

        try {
            const response = await fetch('api/confirm_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            const result = await response.json();
            
            if (result.success) {
                alert('تم إتمام الطلب بنجاح! رقم الفاتورة: ' + result.order_id);
                cart = [];
                updateCartUI();
                document.getElementById('orderNotes').value = '';
                location.reload(); // Refresh to update stock count in UI
            } else {
                alert('خطأ: ' + result.message);
            }
        } catch (error) {
            alert('حدث خطأ أثناء التواصل مع السيرفر');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalContent;
        }
    }

    function printCart() {
        if (cart.length === 0) return alert('السلة فارغة');
        window.print();
    }

    // Live Clock update
    setInterval(() => {
        const now = new Date();
        document.getElementById('liveClock').innerText = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    }, 60000);
</script>

<?php include 'includes/footer.php'; ?>
