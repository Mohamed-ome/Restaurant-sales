<?php include 'includes/header.php'; 

// Filtering logic
$filter = $_GET['filter'] ?? 'daily';
$from_date = $_GET['from'] ?? '';
$to_date = $_GET['to'] ?? '';
$product_id = $_GET['product_id'] ?? '';

$where_clause = "WHERE 1=1";
$params = [];

if ($product_id) {
    $where_clause .= " AND o.id IN (SELECT order_id FROM order_items WHERE product_id = ?)";
    $params[] = $product_id;
}

if ($filter === 'daily') {
    $where_clause .= " AND DATE(o.created_at) = CURDATE()";
} elseif ($filter === 'weekly') {
    $where_clause .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($filter === 'monthly') {
    $where_clause .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
} elseif ($filter === 'custom' && !empty($from_date) && !empty($to_date)) {
    $where_clause .= " AND DATE(o.created_at) BETWEEN ? AND ?";
    $params[] = $from_date;
    $params[] = $to_date;
} else if ($filter === 'all') {
    // No extra where
} else {
    // Default to daily if something is weird
    $where_clause .= " AND DATE(o.created_at) = CURDATE()";
}

// Calculate Stats for current filter
$dinein_total_q = $pdo->prepare("SELECT SUM(total_amount) FROM orders o $where_clause AND dining_option = 'DINEIN'");
$dinein_total_q->execute($params);
$dinein_total = $dinein_total_q->fetchColumn();

$takeaway_total_q = $pdo->prepare("SELECT SUM(total_amount) FROM orders o $where_clause AND dining_option = 'TAKEAWAY'");
$takeaway_total_q->execute($params);
$takeaway_total = $takeaway_total_q->fetchColumn();

$period_total_q = $pdo->prepare("SELECT SUM(total_amount) FROM orders o $where_clause");
$period_total_q->execute($params);
$period_total = $period_total_q->fetchColumn();

?>

<div class="row g-2">
    <div class="col-12">
        <div class="glass-card p-2 mb-2">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-1">
                <div class="d-flex gap-1 bg-zinc-900 p-0.5 rounded-pill border border-zinc-800">
                    <a href="?filter=daily&product_id=<?php echo $product_id; ?>" class="btn <?php echo $filter == 'daily' ? 'btn-accent' : 'btn-link text-zinc-500'; ?> px-3 py-1 text-[9px] fw-bold rounded-pill text-decoration-none">يومي</a>
                    <a href="?filter=weekly&product_id=<?php echo $product_id; ?>" class="btn <?php echo $filter == 'weekly' ? 'btn-accent' : 'btn-link text-zinc-500'; ?> px-3 py-1 text-[9px] fw-bold rounded-pill text-decoration-none">أسبوعي</a>
                    <a href="?filter=monthly&product_id=<?php echo $product_id; ?>" class="btn <?php echo $filter == 'monthly' ? 'btn-accent' : 'btn-link text-zinc-500'; ?> px-3 py-1 text-[9px] fw-bold rounded-pill text-decoration-none">شهري</a>
                    <a href="?filter=all&product_id=<?php echo $product_id; ?>" class="btn <?php echo $filter == 'all' ? 'btn-accent' : 'btn-link text-zinc-500'; ?> px-3 py-1 text-[9px] fw-bold rounded-pill text-decoration-none">الكل</a>
                </div>

                <form method="GET" class="d-flex align-items-center gap-2">
                    <select name="product_id" class="form-select bg-zinc-950 border-zinc-800 text-zinc-500 rounded-pill text-[9px] py-1 shadow-none" style="width: 140px;" onchange="this.form.submit()">
                        <option value="">جميع الأصناف</option>
                        <?php 
                        $all_products = $pdo->query("SELECT id, name_ar FROM products ORDER BY name_ar ASC")->fetchAll();
                        foreach($all_products as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $product_id == $p['id'] ? 'selected' : ''; ?>><?php echo $p['name_ar']; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                    <?php if($filter == 'custom'): ?>
                        <input type="date" name="from" value="<?php echo $from_date; ?>" class="form-control bg-zinc-950 border-zinc-800 text-zinc-500 rounded-pill text-[9px] py-1 shadow-none" style="width: 110px;">
                        <span class="text-zinc-700 text-[9px]">إلى</span>
                        <input type="date" name="to" value="<?php echo $to_date; ?>" class="form-control bg-zinc-950 border-zinc-800 text-zinc-500 rounded-pill text-[9px] py-1 shadow-none" style="width: 110px;">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-zinc-800 p-1 rounded-circle border-zinc-700">
                        <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                    </button>
                </form>

                <div class="d-flex gap-2">
                    <button class="btn btn-outline-zinc px-3 py-1 text-[9px] fw-bold rounded-pill" onclick="window.print()">
                        <i data-lucide="printer" style="width: 12px;" class="me-1"></i> طباعة السجل
                    </button>
                    <button class="btn btn-accent px-3 py-1 text-[9px] fw-bold rounded-pill">تصدير EXCEL</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="glass-card overflow-hidden">
            <div class="p-3 border-bottom border-zinc-800 d-flex justify-content-between align-items-center bg-zinc-950/20">
                <div>
                    <h6 class="text-white fw-bold mb-0 italic text-xs">سجل المبيعات المالي</h6>
                    <p class="text-zinc-600 mb-0" style="font-size: 8px;">نطاق التقرير: <?php 
                        if($filter == 'daily') echo 'مبيعات اليوم ' . date('Y/m/d');
                        elseif($filter == 'weekly') echo 'آخر 7 أيام';
                        elseif($filter == 'monthly') echo 'آخر 30 يوم';
                        elseif($filter == 'all') echo 'جميع السجلات';
                        else echo "من $from_date إلى $to_date";
                    ?></p>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-end">
                        <span class="text-zinc-600 text-[8px] uppercase tracking-widest d-block">إجمالي المحلي</span>
                        <span class="text-white fw-bold text-[11px]"><?php echo formatCurrency($dinein_total ?? 0); ?></span>
                    </div>
                    <div class="text-end">
                        <span class="text-zinc-600 text-[8px] uppercase tracking-widest d-block">إجمالي السفري</span>
                        <span class="text-white fw-bold text-[11px]"><?php echo formatCurrency($takeaway_total ?? 0); ?></span>
                    </div>
                    <div class="text-end border-start border-zinc-800 ps-3">
                        <span class="text-zinc-600 text-[8px] uppercase tracking-widest d-block">إجمالي الدخل</span>
                        <span class="text-amber-500 fw-bold mb-0 text-xs"><?php echo formatCurrency($period_total ?? 0); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 text-end align-middle" style="--bs-table-bg: transparent; --bs-table-border-color: #18181b;">
                    <thead>
                        <tr class="text-zinc-700 text-[8px] tracking-widest uppercase border-0">
                            <th class="px-3 py-2">رقم العملية</th>
                            <th class="px-3 py-2">التاريخ والوقت</th>
                            <th class="px-3 py-2">نوع الطلب</th>
                            <th class="px-3 py-2">وسيلة الدفع</th>
                            <th class="px-3 py-2 text-center">الأصناف الملحقة</th>
                            <th class="px-3 py-2 text-start">القيمة</th>
                        </tr>
                    </thead>
                    <tbody class="text-[9px]">
                        <?php 
                        $stmt = $pdo->prepare("SELECT o.*, u.name as cashier_name FROM orders o LEFT JOIN users u ON o.user_id = u.id $where_clause ORDER BY o.created_at DESC LIMIT 100");
                        $stmt->execute($params);
                        $orders = $stmt->fetchAll();
                        foreach($orders as $row): ?>
                        <tr>
                            <td class="px-3 py-2 text-zinc-500">#INV-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-3 py-2 text-zinc-700"><?php echo date('Y/m/d | H:i', strtotime($row['created_at'])); ?></td>
                            <td class="px-3 py-2">
                                <span class="badge border-zinc-900 border bg-zinc-950 text-zinc-500 fw-bold px-1.5 py-0.5" style="font-size: 6px;">
                                    <?php echo $row['dining_option'] == 'DINEIN' ? '🍽️ محلي' : '🥡 سفري'; ?>
                                </span>
                            </td>
                            <td class="px-3 py-2 text-zinc-600">
                                <i data-lucide="<?php echo $row['payment_method'] == 'CASH' ? 'banknote' : 'smartphone'; ?>" style="width: 10px;" class="me-1"></i>
                                <?php echo $row['payment_method'] == 'CASH' ? 'كاش' : 'بنكك (' . $row['transaction_id'] . ')'; ?>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <?php 
                                $items_stmt = $pdo->prepare("SELECT p.name_ar, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                $items_stmt->execute([$row['id']]);
                                $order_items = $items_stmt->fetchAll();
                                foreach($order_items as $oi): ?>
                                    <span class="bg-zinc-900 text-zinc-600 px-1.5 py-0.5 rounded text-[7px] fw-bold"><?php echo $oi['name_ar']; ?> (×<?php echo $oi['quantity']; ?>)</span>
                                <?php endforeach; ?>
                            </td>
                            <td class="px-3 py-2 text-start fw-bold text-amber-500"><?php echo formatCurrency($row['total_amount']); ?></td>
                        </tr>
                        <?php endforeach; 
                        if(empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-zinc-700 italic">لا توجد سجلات مطابقة لهذا البحث</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
