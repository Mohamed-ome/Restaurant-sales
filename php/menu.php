<?php include 'includes/header.php'; ?>

<div class="row g-4">
    <!-- Categories Section -->
    <div class="col-lg-4">
        <div class="glass-card p-4">
            <h6 class="text-white fw-bold mb-4 italic">إدارة الأقسام</h6>
            <div class="space-y-2">
                <?php 
                $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
                foreach($categories as $cat): ?>
                <div class="d-flex justify-content-between align-items-center p-3 bg-zinc-950 rounded-3 border border-zinc-800 hover:border-amber-500/20 transition-all group">
                    <span class="text-xs text-white fw-bold"><?php echo $cat['name']; ?></span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-link p-0 text-zinc-600 hover:text-amber-500" 
                                onclick="editCategory(<?php echo $cat['id']; ?>, '<?php echo $cat['name']; ?>', '<?php echo $cat['type']; ?>')">
                            <i data-lucide="edit-3" style="width: 12px;"></i>
                        </button>
                        <button class="btn btn-link p-0 text-zinc-600 hover:text-red-500" 
                                onclick="deleteData('category', <?php echo $cat['id']; ?>)">
                            <i data-lucide="trash-2" style="width: 12px;"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="btn btn-outline-secondary w-full mt-4 border-dashed border-zinc-800 text-zinc-500 text-[10px] fw-bold py-2" data-bs-toggle="modal" data-bs-target="#categoryModal">
                إضافة قسم جديد +
            </button>
        </div>
    </div>

    <!-- Products Section -->
    <div class="col-lg-8">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="text-white fw-bold mb-0 italic">قائمة الطعام</h6>
                <button class="btn btn-accent text-[10px] fw-bold px-4" data-bs-toggle="modal" data-bs-target="#productModal">إضافة صنف +</button>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-3">
                <?php 
                $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY c.name, p.name_ar");
                $products = $stmt->fetchAll();
                foreach($products as $item): ?>
                <div class="col">
                    <div class="p-3 bg-zinc-950/30 rounded-4 border border-zinc-900 d-flex gap-3 align-items-center hover:bg-zinc-950/50 transition-all">
                        <div class="bg-zinc-900 rounded-3 text-center d-flex align-items-center justify-content-center border border-zinc-800" style="width: 60px; height: 60px; flex-shrink: 0;">
                            <?php if(!empty($item['image'])): ?>
                                <img src="<?php echo $item['image']; ?>" class="w-100 h-100 object-fit-cover rounded-3 opacity-80">
                            <?php else: ?>
                                <span class="text-zinc-700 fw-bold fs-4"><?php echo mb_substr($item['name_ar'], 0, 1); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-zinc-200 fw-bold mb-0 text-xs text-truncate"><?php echo $item['name_ar']; ?></h6>
                                    <p class="text-zinc-700 m-0" style="font-size: 8px;"><?php echo $item['category_name']; ?></p>
                                </div>
                                <span class="text-amber-500 fw-bold text-xs"><?php echo formatCurrency($item['price']); ?></span>
                            </div>
                            <p class="text-zinc-600 mb-0 italic text-truncate mt-1" style="font-size: 9px;"><?php echo $item['ingredients']; ?></p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-zinc-700 p-0 shadow-none" data-bs-toggle="dropdown">
                                <i data-lucide="more-vertical" style="width: 14px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark bg-zinc-900 border-zinc-800 shadow-xl">
                                <li><a class="dropdown-item text-xs" href="javascript:void(0)" onclick='editProduct(<?php echo json_encode($item); ?>)'>تعديل</a></li>
                                <li><a class="dropdown-item text-xs text-danger" href="javascript:void(0)" onclick="deleteData('product', <?php echo $item['id']; ?>)">حذف</a></li>
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
                            <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">السعر (ر.س)</label>
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

<script>
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
                alert(res.message);
            }
        } catch (err) {
            alert('حدث خطأ أثناء الحفظ');
        }
    });
}

async function deleteData(type, id) {
    if(!confirm('هل أنت متأكد من الحذف؟')) return;
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
            alert(res.message);
        }
    } catch (err) {
        alert('حدث خطأ أثناء الحذف');
    }
}

handleSubmit('categoryForm', 'save_category.php');
handleSubmit('productForm', 'save_product.php');

// Reset forms on close
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('hidden.bs.modal', () => {
        m.querySelector('form').reset();
        m.querySelector('input[type="hidden"]').value = '';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
