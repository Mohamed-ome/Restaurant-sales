<?php include 'includes/header.php'; ?>

<div class="row g-4">
    <!-- Stat Cards -->
    <div class="col-md-3">
        <div class="glass-card p-4">
            <p class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2">مبيعات اليوم</p>
            <div class="d-flex justify-content-between align-items-end">
                <?php 
                $today_sales = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
                ?>
                <h3 class="text-white fw-bold mb-0"><?php echo number_format($today_sales ?? 0, 0); ?> <small class="fs-6 fw-normal">ر.س</small></h3>
                <span class="text-amber-500 text-[10px] fw-bold">+0%</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4 border-start border-4 border-amber-500">
            <p class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2">إجمالي الطلبات</p>
            <div class="d-flex justify-content-between align-items-end">
                <?php 
                $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
                ?>
                <h3 class="text-white fw-bold mb-0"><?php echo $total_orders; ?></h3>
                <i data-lucide="shopping-bag" class="text-zinc-800" style="width: 24px;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4">
            <p class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2">متوسط الفاتورة</p>
            <div class="d-flex justify-content-between align-items-end">
                <?php 
                $avg_order = $pdo->query("SELECT AVG(total_amount) FROM orders")->fetchColumn();
                ?>
                <h3 class="text-white fw-bold mb-0"><?php echo number_format($avg_order ?? 0, 0); ?> <small class="fs-6 fw-normal">ر.س</small></h3>
                <i data-lucide="trending-up" class="text-zinc-800" style="width: 24px;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="glass-card p-4">
            <p class="text-zinc-500 text-[10px] fw-bold uppercase tracking-widest mb-2">المخزون المنخفض</p>
            <div class="d-flex justify-content-between align-items-end">
                <?php 
                $low_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE in_stock <= min_threshold")->fetchColumn();
                ?>
                <h3 class="text-red-500 fw-bold mb-0"><?php echo $low_stock_count; ?></h3>
                <i data-lucide="alert-triangle" class="text-red-500/20" style="width: 24px;"></i>
            </div>
        </div>
    </div>

    <!-- Charts Mock -->
    <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
            <h6 class="text-white fw-bold mb-4 italic">الإحصائيات التحليلية</h6>
            <div class="bg-zinc-950/50 rounded-4 d-flex items-center justify-center border border-zinc-800" style="height: 300px;">
                <p class="text-zinc-700 text-xs italic tracking-widest">مساحة الرسوم البيانية (Recharts -> PHP Alternative)</p>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100">
            <h6 class="text-white fw-bold mb-4 italic">أحدث العمليات</h6>
            <div class="space-y-4">
                <?php 
                $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                $recent = $stmt->fetchAll();
                foreach($recent as $order): ?>
                <div class="d-flex align-items-center gap-3 p-2 bg-zinc-950/20 rounded-3 border border-transparent hover:border-zinc-800 transition-all">
                    <div class="bg-zinc-800 rounded p-2">
                        <i data-lucide="check-circle-2" class="text-amber-500" style="width: 14px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-xs text-white fw-bold mb-0">طلب #ORD-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></p>
                        <p class="text-zinc-600 mb-0" style="font-size: 9px;">منذ قليل</p>
                    </div>
                    <span class="text-amber-500 fw-bold text-xs"><?php echo formatCurrency($order['total_amount']); ?></span>
                </div>
                <?php endforeach; 
                if(empty($recent)): ?>
                <div class="text-center py-5 opacity-20"><p class="text-xs italic">لا توجد عمليات مبيعات مسجلة</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
