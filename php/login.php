<?php 
require_once 'includes/db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = $_POST['pin'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE pin = ?");
    $stmt->execute([$pin]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'رمز PIN غير صحيح، حاول مرة أخرى';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - مجمع المنطقة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, rgba(245, 158, 11, 0.05), transparent 40%),
                        radial-gradient(circle at bottom left, rgba(217, 119, 6, 0.03), transparent 40%);
        }
        .pin-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #27272a;
            transition: all 0.2s;
        }
        .pin-dot.active {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
        }
        .numpad-btn {
            height: 60px;
            font-size: 1.25rem;
            font-weight: 700;
            background: rgba(24, 24, 27, 0.4);
            border: 1px solid #18181b;
            color: #d4d4d8;
            border-radius: 12px;
            transition: all 0.2s;
        }
        .numpad-btn:hover {
            background: rgba(39, 39, 42, 0.6);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fff;
        }
        .error-msg {
            font-size: 11px;
            color: #ef4444;
            margin-top: 10px;
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body class="bg-zinc-950">
    <div class="login-container px-4">
        <div class="w-100" style="max-width: 360px;">
            <div class="text-center mb-5">
                <div class="bg-zinc-900 rounded-4 p-3 d-inline-flex mb-4 border border-zinc-800 shadow-xl">
                    <i data-lucide="lock" class="text-amber-500" style="width: 32px; height: 32px;"></i>
                </div>
                <h4 class="text-white fw-bold italic mb-2">منظومة منتزه حاتم السياحي</h4>
                <p class="text-zinc-600 text-[10px] uppercase tracking-widest fw-bold">أدخل رمز المرور للمتابعة</p>
                <?php if($error): ?>
                    <div class="error-msg"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-center gap-4 mb-5">
                <div class="pin-dot" id="dot-0"></div>
                <div class="pin-dot" id="dot-1"></div>
                <div class="pin-dot" id="dot-2"></div>
                <div class="pin-dot" id="dot-3"></div>
            </div>

            <form id="loginForm" method="POST">
                <input type="hidden" name="pin" id="pinInput">
                <div class="row g-3 px-2">
                    <?php for($i=1; $i<=9; $i++): ?>
                    <div class="col-4">
                        <button type="button" class="numpad-btn w-100" onclick="addNumber(<?php echo $i; ?>)"><?php echo $i; ?></button>
                    </div>
                    <?php endfor; ?>
                    <div class="col-4">
                        <button type="button" class="btn btn-link w-100 text-zinc-700 p-0 shadow-none" onclick="clearPin()">
                            <i data-lucide="delete" style="width: 20px;"></i>
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="numpad-btn w-100" onclick="addNumber(0)">0</button>
                    </div>
                </div>
            </form>
            
            <div class="mt-5 pt-4 border-top border-zinc-900 text-center">
                 <p class="text-zinc-800 text-[8px] uppercase tracking-tighter fw-black mb-0 italic">Secure POS Terminal Hardware Environment</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        let currentPin = "";
        const dots = [
            document.getElementById('dot-0'),
            document.getElementById('dot-1'),
            document.getElementById('dot-2'),
            document.getElementById('dot-3')
        ];
        const pinInput = document.getElementById('pinInput');
        const loginForm = document.getElementById('loginForm');

        function addNumber(num) {
            if (currentPin.length < 4) {
                currentPin += num;
                updateDots();
                
                if (currentPin.length === 4) {
                    pinInput.value = currentPin;
                    setTimeout(() => {
                        loginForm.submit();
                    }, 200);
                }
            }
        }

        function clearPin() {
            currentPin = "";
            updateDots();
        }

        function updateDots() {
            dots.forEach((dot, index) => {
                if (dot) {
                    if (index < currentPin.length) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                }
            });
        }
    </script>
</body>
</html>
