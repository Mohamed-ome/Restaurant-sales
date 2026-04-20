<?php 
require_once 'db.php';
// Start session for login management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, if not redirect to login page (except for login.php)
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['user_id']) && $current_page !== 'login.php') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منتزه حاتم السياحي - POS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Custom Elegant Dark CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main-content">
    <header class="header mb-4">
        <div style="flex: 1;" class="d-flex align-items-center gap-2">
             <button class="btn btn-zinc-800 p-2 d-lg-none" onclick="document.querySelector('.sidebar').classList.toggle('show')">
                <i data-lucide="menu" style="width: 18px; height: 18px;"></i>
             </button>
             <!-- Left side empty for desktop alignment -->
        </div>
        
        <div style="flex: 2;" class="text-center">
            <h6 class="mb-0 text-white fw-bold italic tracking-tighter">منتزه حاتم السياحي</h6>
            <p class="text-zinc-600 mb-0" style="font-size: 7px; letter-spacing: 2px;">PREMIUM POS SYSTEM</p>
        </div>

        <div style="flex: 1;" class="d-flex justify-content-end align-items-center gap-2">
             <button class="btn btn-zinc-800 p-1.5 rounded-pill border-zinc-700 shadow-sm transition-all hover:scale-110" onclick="toggleTheme()" id="themeToggle">
                <i data-lucide="sun" class="text-amber-500" style="width: 14px; height: 14px;"></i>
             </button>

             <div class="glass-card px-3 py-1 d-flex align-items-center gap-2">
                <div class="bg-zinc-800 rounded p-1">
                    <i data-lucide="user" class="text-zinc-500" style="width: 14px; height: 14px;"></i>
                </div>
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 text-white fw-bold" style="font-size: 10px;">
                        <?php echo $_SESSION['user_name'] ?? 'مستخدم'; ?>
                    </p>
                    <p class="mb-0 text-zinc-500" style="font-size: 8px;">
                        <?php 
                        $role = $_SESSION['user_role'] ?? '';
                        if($role == 'ADMIN') echo 'مدير النظام';
                        else if($role == 'MANAGER') echo 'مشرف الصالة';
                        else echo 'موظف مبيعات';
                        ?>
                    </p>
                </div>
             </div>
        </div>
    </header>
