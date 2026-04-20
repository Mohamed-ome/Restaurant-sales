<aside class="sidebar p-2 shadow-lg">
    <div class="d-flex align-items-center justify-content-between mb-4 px-2 pt-2">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-amber-500 rounded-2 p-1.5 shadow-lg shadow-amber-500/10">
                <i data-lucide="store" class="text-dark" style="width: 18px; height: 18px;"></i>
            </div>
            <div>
                <h6 class="text-white fw-bold mb-0 text-[10px]">منتزه حاتم السياحي</h6>
                <p class="text-zinc-600 mb-0 uppercase tracking-widest" style="font-size: 6px;">المنطقة الوسطى</p>
            </div>
        </div>
        <button class="btn btn-link p-0 d-lg-none text-zinc-500" onclick="document.querySelector('.sidebar').classList.remove('show')">
            <i data-lucide="x" style="width: 20px; height: 20px;"></i>
        </button>
    </div>

    <div class="nav flex-column gap-1">
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        function isActive($page, $current) { return ($page == $current) ? 'active' : ''; }
        ?>
        <p class="text-zinc-600 uppercase tracking-wider mb-2 px-2" style="font-size: 7px; font-weight: 700;">الرئيسية</p>
        
        <a href="index.php" class="nav-link <?php echo isActive('index.php', $current_page); ?>">
            <i data-lucide="store" style="width: 14px;"></i>
            <span>نقطة البيع</span>
        </a>
        <a href="dashboard.php" class="nav-link <?php echo isActive('dashboard.php', $current_page); ?>">
            <i data-lucide="layout-dashboard" style="width: 14px;"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="menu.php" class="nav-link <?php echo isActive('menu.php', $current_page); ?>">
            <i data-lucide="menu" style="width: 14px;"></i>
            <span>قائمة الطعام</span>
        </a>
        <a href="inventory.php" class="nav-link <?php echo isActive('inventory.php', $current_page); ?>">
            <i data-lucide="package-search" style="width: 14px;"></i>
            <span>المخزون</span>
        </a>
        <a href="reports.php" class="nav-link <?php echo isActive('reports.php', $current_page); ?>">
            <i data-lucide="bar-chart-3" style="width: 14px;"></i>
            <span>التقارير</span>
        </a>
        <a href="shipping.php" class="nav-link <?php echo isActive('shipping.php', $current_page); ?>">
            <i data-lucide="truck" style="width: 14px;"></i>
            <span>التوصيل</span>
        </a>
    </div>

    <div class="mt-auto p-2" style="position: absolute; bottom: 1rem; left: 0.5rem; right: 0.5rem;">
        <hr class="border-zinc-800 mb-2">
        <a href="logout.php" class="nav-link text-danger-hover py-2">
            <i data-lucide="log-out" style="width: 14px;"></i>
            <span>تسجيل الخروج</span>
        </a>
    </div>
</aside>
