<?php include 'includes/header.php'; ?>

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="row g-3 items-end">
                <div class="col-md-3">
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">من تاريخ</label>
                    <input type="date" class="form-control bg-zinc-950 border-zinc-800 text-zinc-400 rounded-pill text-xs focus:border-amber-500 shadow-none">
                </div>
                <div class="col-md-3">
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">إلى تاريخ</label>
                    <input type="date" class="form-control bg-zinc-950 border-zinc-800 text-zinc-400 rounded-pill text-xs focus:border-amber-500 shadow-none">
                </div>
                <div class="col-md-4">
                    <label class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2 d-block">بحث في الأصناف</label>
                    <div class="input-group">
                        <span class="input-group-text bg-zinc-950 border-zinc-800 text-zinc-500 rounded-start-pill b-l-0">
                            <i data-lucide="search" style="width: 14px;"></i>
                        </span>
                        <input type="text" class="form-control bg-zinc-950 border-zinc-800 text-white rounded-end-pill shadow-none text-xs" placeholder="مثال: شاورما...">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-accent flex-grow-1 py-2 text-[10px] fw-bold">تصدير PDF</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="glass-card overflow-hidden">
            <div class="p-4 border-bottom border-zinc-800 d-flex justify-content-between align-items-center bg-zinc-950/20">
                <h6 class="text-white fw-bold mb-0 italic">سجل المبيعات المالي</h6>
                <div class="text-end">
                    <span class="text-zinc-500 text-[10px] uppercase tracking-widest ml-3">إجمالي الفترة المحددة</span>
                    <span class="text-amber-500 h5 fw-bold mb-0">14,230.50 ر.س</span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 text-end align-middle" style="--bs-table-bg: transparent; --bs-table-border-color: #18181b;">
                    <thead>
                        <tr class="text-zinc-500 text-[10px] tracking-widest uppercase border-0">
                            <th class="px-4 py-3">رقم العملية</th>
                            <th class="px-4 py-3">التاريخ والوقت</th>
                            <th class="px-4 py-3">الأصناف الملحقة</th>
                            <th class="px-4 py-3 text-start">القيمة الإجمالية</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        <?php 
                        $stmt = $pdo->query("SELECT o.*, u.name as cashier_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 50");
                        $orders = $stmt->fetchAll();
                        foreach($orders as $row): ?>
                        <tr>
                            <td class="px-4 py-3 text-zinc-400">#INV-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-4 py-3 text-zinc-600"><?php echo date('Y/m/d | H:i', strtotime($row['created_at'])); ?></td>
                            <td class="px-4 py-3">
                                <?php 
                                // Fetch items for this order
                                $items_stmt = $pdo->prepare("SELECT p.name_ar, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                $items_stmt->execute([$row['id']]);
                                $order_items = $items_stmt->fetchAll();
                                foreach($order_items as $oi): ?>
                                    <span class="bg-zinc-800 text-zinc-500 px-2 py-1 rounded text-[9px] fw-bold"><?php echo $oi['name_ar']; ?> (×<?php echo $oi['quantity']; ?>)</span>
                                <?php endforeach; ?>
                            </td>
                            <td class="px-4 py-3 text-start fw-bold text-amber-500"><?php echo formatCurrency($row['total_amount']); ?></td>
                        </tr>
                        <?php endforeach; 
                        if(empty($orders)): ?>
                        <tr><td colspan="4" class="text-center py-5 text-zinc-600 italic">لا توجد عمليات مبيعات مسجلة حتى الآن</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
