<?php include 'includes/header.php'; ?>

<div class="row g-4 h-100">
    <!-- Products Section -->
    <div class="col-lg-9 h-100 overflow-auto">
        <div class="glass-card p-2 mb-3">
            <div class="row g-2">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text bg-zinc-950 border-zinc-800 text-zinc-500 rounded-start-pill b-l-0">
                            <i data-lucide="search" style="width: 12px;"></i>
                        </span>
                        <input type="text" id="productSearch" class="form-control bg-zinc-950 border-zinc-800 text-white rounded-end-pill shadow-none text-[9px]" placeholder="بحث عن صنف أو مكون...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="categoryFilter" class="form-select bg-zinc-950 border-zinc-800 text-zinc-400 rounded-3 text-[9px] shadow-none">
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

        <div class="row row-cols-2 row-cols-md-4 row-cols-xl-5 row-cols-xxl-6 g-2" id="productGrid">
            <?php 
            $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
            foreach($products as $p): ?>
            <div class="col product-card-wrapper" data-category="<?php echo $p['category_id']; ?>" data-name="<?php echo $p['name_ar']; ?>" data-ingredients="<?php echo $p['ingredients']; ?>">
                <div class="glass-card p-2 h-100 card-product position-relative border-zinc-800">
                    <div class="bg-zinc-950 rounded-3 mb-2 d-flex align-items-center justify-content-center border border-zinc-800 overflow-hidden" style="aspect-ratio: 1/1;">
                        <?php if(!empty($p['image'])): ?>
                             <img src="<?php echo $p['image']; ?>" class="w-100 h-100 object-fit-cover opacity-75" loading="lazy" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <svg width="100%" height="100%" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-10">
                                <rect width="100" height="100" fill="#09090b"/>
                                <circle cx="50" cy="50" r="25" stroke="#3f3f46" stroke-width="2"/>
                                <path d="M42 50H58M50 42V58" stroke="#3f3f46" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        <?php endif; ?>
                        
                        <?php if($p['in_stock'] <= $p['min_threshold']): ?>
                            <div class="position-absolute top-0 start-0 m-1 badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 text-[7px] rounded-pill">منخفض</div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-1 gap-1">
                        <h6 class="text-white fw-bold mb-0 text-[9px] text-truncate"><?php echo $p['name_ar']; ?></h6>
                        <span class="text-amber-500 fw-bold text-[9px] flex-shrink-0"><?php echo formatCurrency($p['price']); ?></span>
                    </div>
                    <button class="btn btn-accent w-full py-1 rounded-2 text-[8px] uppercase add-to-cart" 
                            data-id="<?php echo $p['id']; ?>" 
                            data-name="<?php echo $p['name_ar']; ?>" 
                            data-image="<?php echo $p['image'] ?? ''; ?>"
                            data-price="<?php echo $p['price']; ?>">إضافة</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Sidebar: Cart -->
    <div class="col-lg-3 h-100">
        <div class="glass-card h-100 d-flex flex-column overflow-hidden border-zinc-800">
            <div class="p-3 border-bottom border-zinc-800 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white fw-bold mb-0 italic text-[11px]">الفاتورة الحالية</h6>
                    <p class="text-zinc-600 mb-0" style="font-size: 7px;"><?php echo date('Y/m/d'); ?> | <span id="liveClock"><?php echo date('H:i'); ?></span></p>
                </div>
                <span class="badge bg-zinc-800 text-zinc-500 fw-normal text-[7px]" id="orderNumber">#<?php echo time() % 10000; ?></span>
            </div>
            
            <div class="flex-grow-1 p-2 overflow-auto d-flex flex-column gap-2" id="cartContainer">
                <!-- Empty Cart State -->
                <div class="my-auto text-center opacity-25 py-4" id="emptyCart">
                    <i data-lucide="shopping-cart" class="text-zinc-500 mb-2" style="width: 24px; height: 24px;"></i>
                    <p class="text-[9px] fw-bold tracking-tight">السلة فارغة</p>
                </div>
            </div>

            <div class="p-3 bg-zinc-950 border-top border-zinc-800">
                <!-- قسم اختيار نوع الطلب وطريقة الدفع والملاحظات -->
                <div class="mb-4">
                    <!-- خيار سفري أو محلي -->
                    <label class="text-zinc-500 text-[9px] fw-bold uppercase tracking-widest mb-2 d-block">نوع الطلب</label>
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="dining_option" id="dine_local" value="DINEIN" autocomplete="off" checked>
                            <label class="btn btn-outline-zinc w-100 py-2 text-[10px] fw-bold d-flex align-items-center justify-content-center gap-2" for="dine_local">
                                <i data-lucide="utensils" style="width: 14px;"></i> محلي
                            </label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="dining_option" id="dine_away" value="TAKEAWAY" autocomplete="off">
                            <label class="btn btn-outline-zinc w-100 py-2 text-[10px] fw-bold d-flex align-items-center justify-content-center gap-2" for="dine_away">
                                <i data-lucide="package" style="width: 14px;"></i> سفري
                            </label>
                        </div>
                    </div>

                    <!-- اختيار وسيلة الدفع (كاش أو بنكك) -->
                    <label class="text-zinc-500 text-[9px] fw-bold uppercase tracking-widest mb-2 d-block">طريقة الدفع</label>
                    <div class="d-flex gap-2 mb-3">
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_cash" value="CASH" autocomplete="off" checked onchange="toggleBankFields()">
                            <label class="btn btn-outline-zinc w-100 py-2 text-[10px] fw-bold d-flex align-items-center justify-content-center gap-2" for="pay_cash">
                                <i data-lucide="banknote" style="width: 14px;"></i> كاش
                            </label>
                        </div>
                        <div class="flex-grow-1">
                            <input type="radio" class="btn-check" name="payment_method" id="pay_bank" value="BANKAK" autocomplete="off" onchange="toggleBankFields()">
                            <label class="btn btn-outline-zinc w-100 py-2 text-[10px] fw-bold d-flex align-items-center justify-content-center gap-2" for="pay_bank">
                                <i data-lucide="smartphone" style="width: 14px;"></i> بنكك
                            </label>
                        </div>
                    </div>

                    <div id="cashFields" class="mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="text-zinc-500 text-[9px] fw-bold uppercase tracking-widest mb-2 d-block">المبلغ المستلم</label>
                                <input type="number" id="amountReceived" class="form-control bg-zinc-950 border-zinc-800 text-amber-500 text-[11px] rounded-3 shadow-none fw-bold" placeholder="0.00" oninput="calculateChange()">
                            </div>
                            <div class="col-6">
                                <label class="text-zinc-500 text-[9px] fw-bold uppercase tracking-widest mb-2 d-block">المتبقي</label>
                                <div id="changeAmount" class="form-control bg-zinc-900 border-zinc-800 text-white text-[11px] rounded-3 fw-bold">0.00 ج.س</div>
                            </div>
                        </div>
                    </div>

                    <div id="bankFields" class="d-none mb-3">
                        <label class="text-zinc-500 text-[9px] fw-bold uppercase tracking-widest mb-2 d-block">رقم العملية (آخر 4 أرقام)</label>
                        <input type="text" id="transactionId" class="form-control bg-zinc-950 border-zinc-800 text-amber-500 text-[11px] rounded-3 shadow-none fw-bold" placeholder="1234" maxlength="4">
                    </div>

                    <div class="mb-3">
                        <label class="text-zinc-500 text-[9px] fw-bold uppercase tracking-widest mb-2 d-block">ملاحظات الطلب</label>
                        <textarea id="orderNotes" class="form-control bg-zinc-950 border-zinc-800 text-white text-[10px] rounded-3 shadow-none w-100" rows="2" placeholder="مثال: بدون بصل، زيادة شطة..."></textarea>
                    </div>
                </div>

                <div class="mb-4 space-y-2">
                    <div class="d-flex justify-content-between text-zinc-500 text-[10px]">
                        <span>المجموع:</span>
                        <span id="subTotal">0.00 ج.س</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top border-zinc-800 mt-2">
                        <span class="text-zinc-400 fw-bold uppercase tracking-widest text-[9px]">الإجمالي النهائي:</span>
                        <span class="text-amber-500 fw-bold h5 mb-0" id="grandTotal">0.00 ج.س</span>
                    </div>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-full py-2.5 rounded-3 border-zinc-800 text-zinc-300 text-[9px] fw-bold" onclick="printCart()">
                            <i data-lucide="printer" style="width: 14px;" class="mb-1 d-block mx-auto"></i>
                            طباعة
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-accent w-full py-2.5 rounded-3 text-[9px] fw-bold shadow-lg shadow-amber-950/20" onclick="checkout()">
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
    let cart = JSON.parse(localStorage.getItem('hatem_pos_cart')) || [];
    
    function saveCart() {
        localStorage.setItem('hatem_pos_cart', JSON.stringify(cart));
    }
    
    // Initial UI Update
    window.addEventListener('DOMContentLoaded', () => {
        updateCartUI();
        calculateTotals();
    });

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
            saveCart();
            updateCartUI();
        });
    });

    function updateCartUI() {
        const cartContainer = document.getElementById('cartContainer');
        const emptyCart = document.getElementById('emptyCart');
        const scrollPos = cartContainer.scrollTop;
        
        if (cart.length === 0) {
            cartContainer.innerHTML = `
                <div class="my-auto text-center opacity-25 py-4" id="emptyCart">
                    <i data-lucide="shopping-cart" class="text-zinc-500 mb-2" style="width: 24px; height: 24px;"></i>
                    <p class="text-[9px] fw-bold tracking-tight">السلة فارغة</p>
                </div>
            `;
            cartContainer.classList.remove('overflow-y-auto');
            lucide.createIcons();
        } else {
            cartContainer.classList.add('overflow-y-auto');
            cartContainer.innerHTML = cart.map((item, index) => {
                return `
                <div class="d-flex align-items-center justify-content-between p-2 bg-zinc-950 rounded-3 border border-zinc-800 transition-all hover:bg-zinc-900 group animate-in fade-in slide-in-from-right-2 duration-200">
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="text-white text-[12px] fw-bold mb-0 text-truncate">${item.name}</h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-zinc-500 text-[10px]">${item.price} ج.س</span>
                            <span class="text-amber-500 fw-bold text-[11px] bg-amber-500/10 px-1.5 rounded">إجمالي: ${(item.price * item.quantity).toFixed(2)} ج.س</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center bg-zinc-900 border border-zinc-800 rounded-pill p-1 gap-1">
                        <button class="btn btn-zinc-800 p-1 text-zinc-400 hover:text-white rounded-circle shadow-sm" onclick="updateQty(${index}, -1)">
                            <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                        </button>
                        <span class="text-white text-[11px] fw-bold px-1 min-w-[24px] text-center">${item.quantity}</span>
                        <button class="btn btn-zinc-800 p-1 text-zinc-400 hover:text-white rounded-circle shadow-sm" onclick="updateQty(${index}, 1)">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </div>
                `;
            }).join('');
            
            cartContainer.scrollTop = scrollPos;
            lucide.createIcons();
        }
        
        calculateTotals();
    }

    function updateQty(index, delta) {
        if (!cart[index]) return;
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        saveCart();
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
    function toggleBankFields() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const bankFields = document.getElementById('bankFields');
        const cashFields = document.getElementById('cashFields');
        
        if (method === 'BANKAK') {
            bankFields.classList.remove('d-none');
            cashFields.classList.add('d-none');
        } else {
            bankFields.classList.add('d-none');
            cashFields.classList.remove('d-none');
        }
    }

    function calculateChange() {
        const total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
        const received = parseFloat(document.getElementById('amountReceived').value) || 0;
        const change = received - total;
        document.getElementById('changeAmount').innerText = (change > 0 ? change.toLocaleString() : '0.00') + ' ج.س';
    }

    async function checkout() {
        if (cart.length === 0) {
            alert('السلة فارغة!');
            return;
        }

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const transactionId = document.getElementById('transactionId').value;
        const orderNotes = document.getElementById('orderNotes').value;

        if (paymentMethod === 'BANKAK' && transactionId.length < 4) {
            alert('يرجى إدخال آخر 4 أرقام من عملية "بنكك"');
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
            payment_method: paymentMethod,
            transaction_id: paymentMethod === 'BANKAK' ? transactionId : null,
            dining_option: document.querySelector('input[name="dining_option"]:checked').value,
            notes: orderNotes
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
        
        const itemsList = document.getElementById('receipt-items');
        const now = new Date();
        const dateStr = now.toLocaleDateString('ar-EG');
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        
        const receivedAmount = parseFloat(document.getElementById('amountReceived').value) || 0;
        const changeAmount = document.getElementById('changeAmount').innerText;
        
        document.getElementById('receipt-date').innerText = dateStr;
        document.getElementById('receipt-time').innerText = timeStr;
        document.getElementById('receipt-order-no').innerText = document.getElementById('orderNumber').innerText.replace('#', '');
        
        let totalQty = 0;
        let grandTotal = 0;
        
        itemsList.innerHTML = cart.map((item, index) => {
            const itemTotal = item.price * item.quantity;
            totalQty += item.quantity;
            grandTotal += itemTotal;
            return `
                <tr>
                    <td>${index + 1}</td>
                    <td style="text-align: right;">${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price.toLocaleString()}</td>
                    <td>${itemTotal.toLocaleString()}</td>
                </tr>
            `;
        }).join('');
        
        document.getElementById('receipt-total-qty').innerText = totalQty;
        document.getElementById('receipt-grand-total').innerText = grandTotal.toLocaleString();
        document.getElementById('receipt-net-pay').innerText = grandTotal.toLocaleString();
        
        // Add Notes and Spelling
        const notes = document.getElementById('orderNotes').value;
        const notesDiv = document.getElementById('receipt-notes-section');
        if (notes) {
            notesDiv.style.display = 'block';
            document.getElementById('receipt-notes').innerText = notes;
        } else {
            notesDiv.style.display = 'none';
        }
        
        // Basic Arabic Spelling Placeholder (You can expand this with a library if needed)
        document.getElementById('receipt-spelling').innerText = "فقط " + grandTotal.toLocaleString() + " جنيهاً سودانياً لا غير";
        
        // Add received and change to receipt if CASH
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const receivedDiv = document.getElementById('receipt-received-row');
        const changeDiv = document.getElementById('receipt-change-row');
        
        if (method === 'CASH') {
            receivedDiv.style.display = 'flex';
            changeDiv.style.display = 'flex';
            document.getElementById('receipt-received').innerText = receivedAmount.toLocaleString();
            document.getElementById('receipt-change').innerText = changeAmount.replace(' ج.س', '');
        } else {
            receivedDiv.style.display = 'none';
            changeDiv.style.display = 'none';
        }
        
        window.print();
    }

    // Live Clock update
    setInterval(() => {
        const now = new Date();
        document.getElementById('liveClock').innerText = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    }, 60000);
</script>

<!-- Hidden Receipt Template for Printing -->
<div class="receipt-print" style="display: none;">
    <div class="receipt-header" style="text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">
        <h2 style="margin: 0; font-size: 20px;">منتزه حاتم السياحي</h2>
        <p style="margin: 2px 0; font-size: 12px;">هاتف: 0912345678</p>
    </div>
    
    <div class="receipt-meta" style="margin-bottom: 10px; font-size: 11px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <div>رقم الطلب: <span id="receipt-order-no" style="font-weight: bold;">000</span></div>
            <div>زبون نقدي</div>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <div id="receipt-date">2024/05/20</div>
            <div id="receipt-time">12:00</div>
            <div>بائع: <?php echo $_SESSION['user_name'] ?? 'موظف'; ?></div>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; text-align: center;">
        <thead style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
            <tr>
                <th style="padding: 5px 2px;">رقم</th>
                <th style="padding: 5px 2px; text-align: right;">الصنف</th>
                <th style="padding: 5px 2px;">الكمية</th>
                <th style="padding: 5px 2px;">السعر</th>
                <th style="padding: 5px 2px;">الاجمالي</th>
            </tr>
        </thead>
        <tbody id="receipt-items">
            <!-- Items injected by JS -->
        </tbody>
        <tfoot style="border-top: 1px solid #000;">
            <tr style="font-weight: bold;">
                <td></td>
                <td style="text-align: center;">المجموع</td>
                <td id="receipt-total-qty">0</td>
                <td></td>
                <td id="receipt-grand-total">0.00</td>
            </tr>
        </tfoot>
    </table>

    <div id="receipt-notes-section" style="margin-bottom: 15px; background: #f9f9f9; padding: 5px; border: 1px dashed #ccc; display: none; text-align: right;">
        <div style="font-size: 9px; font-weight: bold; margin-bottom: 2px; border-bottom: 1px solid #ddd;">ملاحظات العميل:</div>
        <p id="receipt-notes" style="margin: 0; font-size: 10px;"></p>
    </div>

    <div class="receipt-footer" style="text-align: center; border-top: 2px dashed #000; padding-top: 10px;">
        <div class="receipt-totals" style="margin-bottom: 10px; font-size: 11px;">
            <div id="receipt-received-row" style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                <span>المقبوض :</span>
                <span id="receipt-received">0.00</span>
            </div>
            <div id="receipt-change-row" style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                <span>الباقي :</span>
                <span id="receipt-change">0.00</span>
            </div>
        </div>
        <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">
            الصافي للدفع : <span id="receipt-net-pay">0.00</span>
        </div>
        <p id="receipt-spelling" style="margin: 5px 0; font-size: 9px;"></p>
        <div style="font-size: 8px; color: #666; margin-top: 10px;">برنامج البيان للمحاسبة والمستودعات</div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
