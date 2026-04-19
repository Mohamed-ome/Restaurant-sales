<aside class="sidebar p-4 shadow-lg">
    <div class="d-flex align-items-center gap-3 mb-5 px-2">
        <div class="bg-amber-500 rounded-3 p-2 shadow-lg shadow-amber-500/10">
            <i data-lucide="store" class="text-dark" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
            <h6 class="text-white fw-bold mb-0">مطعم المنطقة</h6>
            <p class="text-zinc-500 mb-0 uppercase tracking-widest" style="font-size: 8px;">المنطقة الوسطى</p>
        </div>
    </div>

    <div class="nav flex-column gap-1">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        function isActive($page, $current) { return ($page == $current) ? 'active' : ''; }
        ?>
        <p class="text-zinc-500 uppercase tracking-wider mb-2 px-3" style="font-size: 9px; font-weight: 700;">الرئيسية</p>
        
        <a href="index.php" class="nav-link <?php echo isActive('index.php', $current_page); ?>">
            <i data-lucide="store" style="width: 16px;"></i>
            <span>نقطة البيع</span>
        </a>
        <a href="dashboard.php" class="nav-link <?php echo isActive('dashboard.php', $current_page); ?>">
            <i data-lucide="layout-dashboard" style="width: 16px;"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="menu.php" class="nav-link <?php echo isActive('menu.php', $current_page); ?>">
            <i data-lucide="menu" style="width: 16px;"></i>
            <span>قائمة الطعام</span>
        </a>
        <a href="inventory.php" class="nav-link <?php echo isActive('inventory.php', $current_page); ?>">
            <i data-lucide="package-search" style="width: 16px;"></i>
            <span>المخزون</span>
        </a>
        <a href="reports.php" class="nav-link <?php echo isActive('reports.php', $current_page); ?>">
            <i data-lucide="bar-chart-3" style="width: 16px;"></i>
            <span>التقارير</span>
        </a>
        <a href="shipping.php" class="nav-link <?php echo isActive('shipping.php', $current_page); ?>">
            <i data-lucide="truck" style="width: 16px;"></i>
            <span>التوصيل</span>
        </a>
    </div>

    <div class="mt-auto p-2" style="position: absolute; bottom: 1.5rem; left: 1rem; right: 1rem;">
        <hr class="border-zinc-800">
        <a href="logout.php" class="nav-link text-danger-hover">
            <i data-lucide="log-out" style="width: 16px;"></i>
            <span>تسجيل الخروج</span>
        </a>
    </div>
</aside>
