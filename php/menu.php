<?php include 'includes/header.php'; ?>

<div class="row g-2">
    <!-- Categories Section -->
    <div class="col-lg-4">
        <div class="glass-card p-3">
            <h6 class="text-white fw-bold mb-3 italic text-xs">إدارة الأقسام</h6>
            <div class="space-y-1">
                <!-- زر عرض الكل -->
                <div class="d-flex justify-content-between align-items-center p-2 bg-amber-500/10 rounded-2 border border-amber-500/20 hover:bg-amber-500/20 transition-all cursor-pointer category-filter-btn active" 
                     onclick="filterProducts('all', this)">
                    <span class="text-[10px] text-amber-500 fw-bold">عرض الكل</span>
                    <i data-lucide="layout-grid" class="text-amber-500" style="width: 12px;"></i>
                </div>

                <?php 
                $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
                foreach($categories as $cat): ?>
                <div class="d-flex justify-content-between align-items-center p-2 bg-zinc-950 rounded-2 border border-zinc-800 hover:border-zinc-700 transition-all group cursor-pointer category-filter-btn"
                     onclick="filterProducts(<?php echo $cat['id']; ?>, this)">
                    <span class="text-[10px] text-white fw-bold"><?php echo $cat['name']; ?></span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-link p-0 text-zinc-700 hover:text-amber-500" 
                                onclick="event.stopPropagation(); editCategory(<?php echo $cat['id']; ?>, '<?php echo $cat['name']; ?>', '<?php echo $cat['type']; ?>')">
                            <i data-lucide="edit-3" style="width: 10px;"></i>
                        </button>
                        <button class="btn btn-link p-0 text-zinc-700 hover:text-red-500" 
                                onclick="event.stopPropagation(); deleteData('category', <?php echo $cat['id']; ?>)">
                            <i data-lucide="trash-2" style="width: 10px;"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-outline-secondary w-full mt-3 border-dashed border-zinc-800 text-zinc-600 text-[9px] fw-bold py-1.5" data-bs-toggle="modal" data-bs-target="#categoryModal">
                إضافة قسم جديد +
            </button>
        </div>
    </div>

    <!-- Products Section -->
    <div class="col-lg-8">
        <div class="glass-card p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="text-white fw-bold mb-0 italic text-xs">قائمة الطعام</h6>
                    <div class="input-group" style="max-width: 200px;">
                        <span class="input-group-text bg-zinc-900 border-zinc-800 text-zinc-700 rounded-start-pill b-l-0">
                            <i data-lucide="search" style="width: 10px;"></i>
                        </span>
                        <input type="text" id="menuSearch" class="form-control bg-zinc-900 border-zinc-800 text-white rounded-end-pill shadow-none text-[9px]" placeholder="بحث في المنيو...">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-zinc text-[9px] fw-bold px-3 py-1.5" onclick="location.reload()">
                        <i data-lucide="save" style="width: 12px;" class="me-1"></i> حفظ التغييرات 
                    </button>
                    <button class="btn btn-accent text-[9px] fw-bold px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#productModal">إضافة صنف +</button>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-2" id="productsContainer">
                <?php 
                $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.name, p.name_ar");
                $products = $stmt->fetchAll();
                foreach($products as $item): ?>
                <div class="col product-item" data-category="<?php echo $item['category_id']; ?>" data-search="<?php echo $item['name_ar']; ?>">
                    <div class="p-2 bg-zinc-950/30 rounded-3 border border-zinc-900 d-flex gap-2 align-items-center hover:bg-zinc-950/50 transition-all">
                        <div class="bg-zinc-900 rounded-2 text-center d-flex align-items-center justify-content-center border border-zinc-800 overflow-hidden" style="width: 44px; height: 44px; flex-shrink: 0;">
                            <?php if(!empty($item['image'])): ?>
                                <img src="<?php echo $item['image']; ?>" class="w-100 h-100 object-fit-cover rounded-2 opacity-80" loading="lazy" referrerpolicy="no-referrer">
                            <?php else: ?>
                                <svg width="44" height="44" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="opacity-10">
                                    <rect width="100" height="100" fill="#18181b"/>
                                    <circle cx="50" cy="50" r="25" stroke="#3f3f46" stroke-width="2"/>
                                    <path d="M42 50H58M50 42V58" stroke="#3f3f46" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-zinc-200 fw-bold mb-0 text-[10px] text-truncate"><?php echo $item['name_ar']; ?></h6>
                                    <p class="text-zinc-700 m-0" style="font-size: 7px;"><?php echo $item['category_name']; ?></p>
                                </div>
                                <div class="text-end">
                                    <div class="d-flex align-items-center bg-zinc-900 rounded-pill px-1.5 py-0.5 border border-zinc-800 focus-within:border-amber-500/30 transition-all">
                                        <input type="number" step="0.01" 
                                               class="bg-transparent border-0 text-amber-500 fw-bold text-[10px] w-10 text-center p-0 shadow-none outline-none price-quick-edit" 
                                               value="<?php echo $item['price']; ?>"
                                               onchange="quickUpdatePrice(<?php echo $item['id']; ?>, this)">
                                        <span class="text-zinc-700 text-[8px] fw-bold me-0.5">ج.س</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-zinc-700 mb-0 italic text-truncate mt-0.5" style="font-size: 8px;"><?php echo $item['ingredients']; ?></p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-zinc-800 p-0 shadow-none" data-bs-toggle="dropdown">
                                <i data-lucide="more-vertical" style="width: 12px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark bg-zinc-900 border-zinc-800 shadow-xl">
                                <li><a class="dropdown-item text-[10px]" href="javascript:void(0)" onclick='editProduct(<?php echo json_encode($item); ?>)'>تعديل</a></li>
                                <li><a class="dropdown-item text-[10px] text-danger" href="javascript:void(0)" onclick="deleteData('product', <?php echo $item['id']; ?>)">حذف</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- نافذة إدارة الأقسام (إضافة وتعديل) -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-zinc-950 border-zinc-800">
            <div class="modal-header border-zinc-800">
                <h6 class="modal-title text-white fw-bold">إضافة / تعديل قسم</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="cat_id">
                    <div class="mb-3">
                        <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">اسم القسم</label>
                        <input type="text" name="name" id="cat_name" class="form-control bg-zinc-900 border-zinc-800 text-white rounded-3 shadow-none text-xs" required>
                    </div>
                    <div class="mb-0">
                        <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">النوع</label>
                        <select name="type" id="cat_type" class="form-select bg-zinc-900 border-zinc-800 text-zinc-400 rounded-3 shadow-none text-xs">
                            <option value="FOOD">مأكولات</option>
                            <option value="JUICE">مشروبات</option>
                            <option value="OTHER">أخرى</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-zinc-800">
                    <button type="button" class="btn btn-zinc-800 text-zinc-400 text-xs py-2 px-4 rounded-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-accent text-xs py-2 px-4 rounded-3 fw-bold">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نافذة إدارة الأصناف (إضافة وتعديل) -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-zinc-950 border-zinc-800">
            <div class="modal-header border-zinc-800">
                <h6 class="modal-title text-white fw-bold">إضافة / تعديل صنف</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="prod_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">اسم الصنف (بالعربي)</label>
                            <input type="text" name="name_ar" id="prod_name_ar" class="form-control bg-zinc-900 border-zinc-800 text-white rounded-3 shadow-none text-xs" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">القسم</label>
                            <select name="category_id" id="prod_category" class="form-select bg-zinc-900 border-zinc-800 text-zinc-400 rounded-3 shadow-none text-xs" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">السعر (ج.س)</label>
                            <input type="number" step="0.01" name="price" id="prod_price" class="form-control bg-zinc-900 border-zinc-800 text-white rounded-3 shadow-none text-xs" required>
                        </div>
                        <div class="col-md-6">
                            <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">الاسم (بالإنجليزي)</label>
                            <input type="text" name="name" id="prod_name" class="form-control bg-zinc-900 border-zinc-800 text-white rounded-3 shadow-none text-xs">
                        </div>
                        <div class="col-12">
                            <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">المكونات / الوصف</label>
                            <textarea name="ingredients" id="prod_ingredients" class="form-control bg-zinc-900 border-zinc-800 text-white rounded-3 shadow-none text-xs" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-zinc-800">
                    <button type="button" class="btn btn-zinc-800 text-zinc-400 text-xs py-2 px-4 rounded-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-accent text-xs py-2 px-4 rounded-3 fw-bold">حفظ الصنف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- نافذة تأكيد الحذف -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-zinc-950 border-zinc-800">
            <div class="modal-body p-4 text-center">
                <div class="bg-red-500/10 w-12 h-12 rounded-full d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i data-lucide="alert-triangle" class="text-red-500"></i>
                </div>
                <h6 class="text-white fw-bold mb-2">هل أنت متأكد؟</h6>
                <p class="text-zinc-500 text-[10px] mb-4">لا يمكن التراجع عن هذه العملية بعد إتمامها.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-zinc-900 text-zinc-400 text-[10px] flex-grow-1 py-2" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger text-[10px] flex-grow-1 py-2 fw-bold">حذف نهائي</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let deleteContext = { type: null, id: null };
const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));

function editCategory(id, name, type) {
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_name').value = name;
    document.getElementById('cat_type').value = type;
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function editProduct(prod) {
    document.getElementById('prod_id').value = prod.id;
    document.getElementById('prod_name_ar').value = prod.name_ar;
    document.getElementById('prod_name').value = prod.name;
    document.getElementById('prod_price').value = prod.price;
    document.getElementById('prod_category').value = prod.category_id;
    document.getElementById('prod_ingredients').value = prod.ingredients;
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

async function handleSubmit(formId, apiEndpoint) {
    const form = document.getElementById(formId);
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch(`api/${apiEndpoint}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const res = await response.json();
            if(res.success) {
                location.reload();
            } else {
                console.warn(res.message);
            }
        } catch (err) {
            console.error('حدث خطأ أثناء الحفظ');
        }
    });
}

function deleteData(type, id) {
    deleteContext = { type, id };
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    const { type, id } = deleteContext;
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerText = 'جاري الحذف...';

    try {
        const response = await fetch('api/delete_item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, id })
        });
        const res = await response.json();
        if(res.success) {
            location.reload();
        } else {
            console.warn(res.message);
            btn.disabled = false;
            btn.innerText = 'حذف نهائي';
        }
    } catch (err) {
        console.error('حدث خطأ أثناء الحذف');
        btn.disabled = false;
        btn.innerText = 'حذف نهائي';
    }
});

async function quickUpdatePrice(id, input) {
    const originalBorder = input.parentElement.style.borderColor;
    input.parentElement.style.borderColor = '#f59e0b';
    
    try {
        const response = await fetch('api/update_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, price: input.value })
        });
        const res = await response.json();
        if(res.success) {
            input.parentElement.style.borderColor = '#10b981';
            setTimeout(() => {
                input.parentElement.style.borderColor = '';
            }, 2000);
        } else {
            console.error('خطأ في التحديث: ' + res.message);
            input.parentElement.style.borderColor = '#ef4444';
        }
    } catch (err) {
        console.error('حدث خطأ أثناء الاتصال بالخادم');
        input.parentElement.style.borderColor = '#ef4444';
    }
}

let activeCategoryId = 'all';

function filterProducts(categoryId, btn) {
    activeCategoryId = categoryId;
    // تحديث حالة الأزرار
    document.querySelectorAll('.category-filter-btn').forEach(el => {
        if (el) {
            el.classList.remove('active', 'bg-amber-500/10', 'border-amber-500/20');
            el.classList.add('bg-zinc-950', 'border-zinc-800');
            const span = el.querySelector('span');
            if (span) {
                span.classList.remove('text-amber-500');
                span.classList.add('text-white');
            }
        }
    });
    
    if (btn) {
        btn.classList.add('active', 'bg-amber-500/10', 'border-amber-500/20');
        btn.classList.remove('bg-zinc-950', 'border-zinc-800');
        const span = btn.querySelector('span');
        if (span) {
            span.classList.remove('text-white');
            span.classList.add('text-amber-500');
        }
    }

    applyFilters();
}

function applyFilters() {
    const searchInputEl = document.getElementById('menuSearch');
    if (!searchInputEl) return;
    const query = searchInputEl.value.toLowerCase();
    const items = document.querySelectorAll('.product-item');
    
    items.forEach(item => {
        if (item) {
            const matchCategory = (activeCategoryId === 'all' || item.getAttribute('data-category') == activeCategoryId);
            const dataSearch = item.getAttribute('data-search');
            const matchSearch = dataSearch ? dataSearch.toLowerCase().includes(query) : true;
            
            if (matchCategory && matchSearch) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        }
    });
}

document.getElementById('menuSearch').addEventListener('input', applyFilters);

handleSubmit('categoryForm', 'save_category.php');
handleSubmit('productForm', 'save_product.php');

// Reset forms on close
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('hidden.bs.modal', () => {
        const form = m.querySelector('form');
        if (form) {
            form.reset();
            const hiddenInput = form.querySelector('input[type="hidden"]');
            if (hiddenInput) {
                hiddenInput.value = '';
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
