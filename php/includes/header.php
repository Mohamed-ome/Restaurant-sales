<?php 
require_once 'db.php';
// Start session for login management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مطعم المنطقة - POS</title>
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
        <div>
            <h5 class="mb-0 text-white fw-bold">نظام المنطقة</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="glass-card px-3 py-1 d-flex align-items-center gap-2">
                <div class="bg-zinc-800 rounded p-1">
                    <i data-lucide="user" class="text-zinc-500" style="width: 14px; height: 14px;"></i>
                </div>
                <div class="text-end">
                    <p class="mb-0 text-white fw-bold" style="font-size: 11px;">محمد خالد</p>
                    <p class="mb-0 text-zinc-500" style="font-size: 9px;">مدير النظام</p>
                </div>
             </div>
        </div>
    </header>
