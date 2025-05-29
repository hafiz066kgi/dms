<?php
require_once __DIR__ . '/../includes/auth.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (login($username, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

include_once __DIR__ . '/../templates/header.php';
?>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card p-4 shadow" style="width: 370px;">
        <div class="text-center mb-4">
            <!-- Animated Logo -->
            <span id="animatedLogo" class="polyparts-logo"></span>
            <h6 class="mt-2 mb-3">Document Management System</h5>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger" id="errorMessage">
                <span id="errorText"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>
        <form id="loginForm" method="post" autocomplete="off" spellcheck="false">
            <div class="mb-4 position-relative">
                <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="username" id="username" class="form-control animated-field" required autofocus>
                <label for="username" class="animated-label">Username</label>
            </div>
            <div class="mb-4 position-relative">
                <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control animated-field" required>
                <label for="password" class="animated-label">Password</label>
                <button type="button" class="btn password-toggle" id="passwordToggle" tabindex="-1">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <button type="submit" class="btn btn-primary w-100 animated-btn">Login</button>
            <div class="text-end mt-2">
                <a href="#" id="forgotPasswordLink" style="font-size:0.8rem;">Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" style="display:none; position:fixed; z-index:1050; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
    <div style="background:#fff; padding:2rem; border-radius:10px; position:relative; width:340px; box-shadow:0 2px 16px rgba(0,0,0,0.2); animation: modalPop 0.4s cubic-bezier(.68,-0.55,.27,1.55);">
        <button type="button" id="closeModal" style="position:absolute;top:10px;right:10px; border:none; background:transparent; font-size:1.5rem;">&times;</button>
        <h5 class="mb-3">Forgot Password?</h5>
        <p class="mb-3">Contact admin or click below to send WhatsApp for reset assistance.</p>
        <input type="text" id="forgotUsername" class="form-control mb-3" placeholder="Enter your username">
        <a href="#" id="sendWhatsAppBtn" class="btn btn-success w-100 mb-2">
            <i class="fab fa-whatsapp"></i> Send WhatsApp
        </a>
        <small class="text-muted d-block text-center">or contact <b>QEHS - Hafiz</b> directly.</small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animated logo
    const animateLogo = () => {
        const logoElement = document.getElementById('animatedLogo');
        const logoText = "POLYPARTS";
        let index = 0;
        const interval = setInterval(() => {
            if (index < logoText.length) {
                logoElement.innerHTML = `<span class="app-logo">${logoText.substring(0, index + 1)}<span class="terminal-cursor"></span></span>`;
                index++;
            } else {
                clearInterval(interval);
                logoElement.innerHTML = `<span class="app-logo">${logoText}<span class="terminal-cursor"></span></span>`;
            }
        }, 130);
    };
    animateLogo();

    // Animated floating label for inputs
    document.querySelectorAll('.animated-field').forEach(input => {
        const wrapper = input.parentElement;
        input.addEventListener('focus', () => wrapper.classList.add('focused'));
        input.addEventListener('blur', () => {
            if (!input.value) wrapper.classList.remove('focused');
        });
        // On page load: if already filled (autocomplete)
        if (input.value) wrapper.classList.add('focused');
    });

    // Password show/hide toggle
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    passwordToggle.addEventListener('click', () => {
        const isHidden = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
        passwordToggle.querySelector('i').classList.toggle('fa-eye');
        passwordToggle.querySelector('i').classList.toggle('fa-eye-slash');
        passwordToggle.classList.add('pulse-toggle');
        setTimeout(() => passwordToggle.classList.remove('pulse-toggle'), 300);
    });

    // Forgot password modal
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    const closeModal = document.getElementById('closeModal');
    forgotPasswordLink.addEventListener('click', (e) => {
        e.preventDefault();
        forgotPasswordModal.style.display = 'flex';
    });
    closeModal.addEventListener('click', () => {
        forgotPasswordModal.style.display = 'none';
    });
    window.addEventListener('click', (e) => {
        if (e.target === forgotPasswordModal) {
            forgotPasswordModal.style.display = 'none';
        }
    });

    // WhatsApp Button Logic
    const sendWhatsAppBtn = document.getElementById('sendWhatsAppBtn');
    const forgotUsername = document.getElementById('forgotUsername');
    sendWhatsAppBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const username = forgotUsername.value.trim();
        if (!username) {
            forgotUsername.classList.add('is-invalid');
            forgotUsername.focus();
            return;
        }
        forgotUsername.classList.remove('is-invalid');
        const phone = '######'; // Nombor WhatsApp
        const text = encodeURIComponent(`Hello, I forgot my password for username: ${username}. Please assist. Thank you.`);
        window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
    });
});
</script>

<style>
.polyparts-logo, .app-logo {
    font-family: 'Cooper Black', 'Cooper Black W01', serif, sans-serif;
    color: #1a237e;
    font-size: 1.5rem;
    letter-spacing: 1px;
    font-weight: bold;
    transition: color .4s;
}
.terminal-cursor {
    display: inline-block;
    width: 8px;
    background: #1a237e;
    margin-left: 2px;
    height: 1.5em;
    animation: blink-cursor 0.7s steps(1) infinite;
    vertical-align: bottom;
}
@keyframes blink-cursor {
    0%, 60% { opacity: 1; }
    61%, 100% { opacity: 0; }
}

/* Animated field & floating label */
.position-relative { position: relative !important; }
.input-icon {
    position: absolute;
    top: 15px;
    left: 13px;
    color: #aaa;
    font-size: 1.08rem;
    z-index: 2;
    transition: color .3s;
}
.animated-field {
	font-size: 0.97rem; /* was default (1rem) */
    padding-left: 40px !important;
    border-radius: .65rem !important;
    height: 48px !important;
    transition: border .3s, box-shadow .3s;
    background: #f8fafc;
}
.animated-label {
    position: absolute;
    left: 41px;
    top: 18px;
    color: #999;
    background: transparent;
    font-size: 0.9rem;
    pointer-events: none;
    transition: all .24s cubic-bezier(.62,.07,.11,1.01);
    z-index: 3;
    padding: 0 2px;
}
.focused .input-icon,
.animated-field:focus ~ .animated-label,
.focused .animated-label {
    color: #1a237e;
}
.animated-field:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 2px 12px 0 rgba(59,130,246,0.08);

}
.focused .animated-label,
.animated-field:focus ~ .animated-label {
    top: -8px;
    left: 36px;
    font-size: 0.74rem;
    background: #fff;
    padding: 0 5px;
    color: #1976d2;
}

.password-toggle {
    position: absolute;
    top: 10px;
    right: 6px;
    background: none;
    border: none;
    outline: none;
    padding: 7px 9px 6px 9px;
    z-index: 4;
    font-size: 1.09rem;
    color: #888;
    cursor: pointer;
    transition: color .2s, background .2s, box-shadow .2s;
}
.password-toggle:focus, .password-toggle:hover, .pulse-toggle {
    color: #3b82f6 !important;
    background: #e3f0fc;
    box-shadow: 0 1px 5px 0 rgba(59,130,246,0.09);
    border-radius: 50%;
}
.animated-btn {
    transition: background .18s, box-shadow .18s, transform .13s;
	font-size: 0.98rem;
}
.animated-btn:hover, .animated-btn:focus {
    background: #1976d2;
    box-shadow: 0 3px 16px 0 rgba(59,130,246,0.16);
    transform: translateY(-2px) scale(1.02);
}
#forgotPasswordModal input.is-invalid {
    font-size: 1.08rem;	
    border-color: #dc3545;
    box-shadow: 0 0 0 0.1rem rgba(220,53,69,0.25);
}
@keyframes modalPop {
    0% {transform: scale(0.7);}
    100% {transform: scale(1);}
}
</style>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
