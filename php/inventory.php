<?php include 'includes/header.php'; ?>

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="text-white fw-bold mb-0 italic">إدارة المخزون والمستودع</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-zinc-800 text-zinc-400 text-[10px] fw-bold border-zinc-700">تحديث الكميات</button>
                    <button class="btn btn-accent text-[10px] fw-bold">طلب توريد +</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 text-end align-middle" style="--bs-table-bg: transparent; --bs-table-border-color: #27272a;">
                    <thead>
                        <tr class="text-zinc-500 text-[10px] tracking-widest uppercase border-0">
                            <th class="px-4 py-3">الصنف</th>
                            <th class="px-4 py-3">القسم</th>
                            <th class="px-4 py-3">الرصيد الحالي</th>
                            <th class="px-4 py-3">الحد الأدنى</th>
                            <th class="px-4 py-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        <?php 
                        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.name_ar ASC");
                        $items = $stmt->fetchAll();
                        foreach($items as $item): ?>
                        <tr>
                            <td class="px-4 py-3 fw-bold text-white"><?php echo $item['name_ar']; ?></td>
                            <td class="px-4 py-3 text-zinc-500"><?php echo $item['category_name']; ?></td>
                            <td class="px-4 py-3 fw-bold text-amber-500"><?php echo $item['in_stock']; ?></td>
                            <td class="px-4 py-3 text-zinc-500"><?php echo $item['min_threshold']; ?></td>
                            <td class="px-4 py-3">
                                <?php 
                                $is_low = $item['in_stock'] <= $item['min_threshold'];
                                $status_text = $is_low ? 'منخفض' : 'متوفر';
                                $status_class = $is_low ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle';
                                ?>
                                <span class="badge py-1.5 px-3 rounded-pill <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-danger-subtle { background-color: rgba(239, 68, 68, 0.1) !important; }
.bg-success-subtle { background-color: rgba(16, 185, 129, 0.1) !important; }
</style>

<?php include 'includes/footer.php'; ?>
