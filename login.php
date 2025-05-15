<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBridge - Login/Register</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #f6fafd;
        }

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            color: #222;
            width: 100vw;
            height: 100vh;
            position: relative;
        }

        .blob-bg {
            position: fixed;
            left: 50%;
            bottom: -150px;
            z-index: 0;
            width: 800px;
            height: 800px;
            pointer-events: none;
            transform: translateX(-50%);
            animation: blobMove 7s cubic-bezier(.77, 0, .18, 1) infinite;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: 0.22;
            filter: blur(2px);
            animation: blobFade 7s cubic-bezier(.77, 0, .18, 1) infinite;
        }

        .blob1 {
            width: 500px;
            height: 500px;
            background: #1877f2;
            left: 0;
            bottom: 0;
            animation-delay: 0s;
        }

        .blob2 {
            width: 320px;
            height: 320px;
            background: #3ab7ff;
            left: 300px;
            bottom: 120px;
            animation-delay: 1.2s;
        }

        .blob3 {
            width: 200px;
            height: 200px;
            background: #1877f2;
            left: 200px;
            bottom: 350px;
            animation-delay: 2.4s;
        }

        @keyframes blobMove {
            0% {
                transform: translateX(-50%) translateY(0);
            }

            70% {
                transform: translateX(-50%) translateY(-70vh) scale(0.4);
                opacity: 1;
            }

            100% {
                transform: translateX(-50%) translateY(-80vh) scale(0.2);
                opacity: 0;
            }
        }

        @keyframes blobFade {
            0% {
                opacity: 0.22;
            }

            70% {
                opacity: 0.12;
            }

            100% {
                opacity: 0;
            }
        }

        .center-container {
            min-height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(24, 119, 242, 0.11);
            padding: 40px 34px 32px 34px;
            min-width: 340px;
            max-width: 98vw;
            width: 370px;
            position: relative;
            overflow: visible;
            animation: fadeUp 0.7s cubic-bezier(.77, 0, .18, 1);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .toggle-row {
            display: flex;
            width: 100%;
            margin-bottom: 26px;
        }

        .toggle-btn {
            flex: 1;
            padding: 12px 0;
            font-weight: 700;
            font-size: 1.17rem;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            color: #1877f2;
            cursor: pointer;
            transition: border 0.18s, color 0.18s;
        }

        .toggle-btn.active {
            border-bottom: 3px solid #1877f2;
            color: #165ecb;
        }

        .sub-toggle-row {
            display: flex;
            width: 100%;
            margin-bottom: 18px;
            gap: 8px;
        }

        .sub-toggle-btn {
            flex: 1;
            padding: 10px 0;
            font-weight: 600;
            font-size: 1rem;
            background: #e7f1ff;
            border: none;
            border-radius: 12px;
            color: #1877f2;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .sub-toggle-btn.active {
            background: #1877f2;
            color: #fff;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        label {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1877f2;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: max-content;
            padding: 11px 42px 11px 14px;
            border: 1.5px solid #d3e4fc;
            border-radius: 9px;
            font-size: 1rem;
            background: #f6fafd;
            color: #222;
            font-family: inherit;
            outline: none;
            transition: border 0.18s;
        }

        input:focus {
            border-color: #1877f2;
        }

        input.error {
            border-color: #ff3a3a;
        }

        .error-message {
            color: #ff3a3a;
            font-size: 0.95rem;
            margin-top: 4px;
            margin-left: 3px;
        }

        .show-hide-btn {
            position: absolute;
            right: 13px;
            top: 36px;
            background: none;
            border: none;
            color: #1877f2;
            font-size: 1.05rem;
            cursor: pointer;
            z-index: 2;
        }

        .submit-btn {
            width: 100%;
            padding: 13px 0;
            background: linear-gradient(90deg, #1877f2 70%, #3ab7ff 100%);
            color: #fff;
            font-size: 1.13rem;
            font-weight: 700;
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(24, 119, 242, 0.11);
            cursor: pointer;
            transition: background 0.18s, transform 0.18s;
            margin-top: 8px;
        }

        .submit-btn:hover {
            background: linear-gradient(90deg, #165ecb 70%, #1f9fff 100%);
            transform: translateY(-2px) scale(1.03);
        }

        .fade {
            animation: fadeSwap 0.45s cubic-bezier(.77, 0, .18, 1);
        }

        @keyframes fadeSwap {
            from {
                opacity: 0;
                transform: scale(0.96);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 700px) {
            .auth-card {
                min-width: 0;
                width: 98vw;
                padding: 22px 4vw 18px 4vw;
            }
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 12px 1vw 12px 1vw;
                border-radius: 13px;
            }

            .toggle-btn {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="blob-bg">
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>
        <div class="blob blob3"></div>
    </div>
    <div class="center-container">
        <div class="auth-card" id="authCard">
            <div class="toggle-row">
                <button class="toggle-btn active" id="loginTab">Login</button>
                <button class="toggle-btn" id="registerTab">Register</button>
            </div>
            <form id="loginForm" autocomplete="off">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="loginEmail" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="loginPassword" required autocomplete="current-password">
                    <button type="button" class="show-hide-btn" onclick="togglePassword('loginPassword', this)">Show</button>
                </div>
                <button type="submit" class="submit-btn">Login</button>
            </form>
            <form id="registerForm" style="display:none;" autocomplete="off">
                <div class="sub-toggle-row">
                    <button class="sub-toggle-btn active" id="teacherTab">I'm a Teacher</button>
                    <button class="sub-toggle-btn" id="parentTab">I'm a Parent</button>
                </div>
                <div id="teacherForm" class="fade">
                    <div class="form-group">
                        <label for="teacherName">Name</label>
                        <input type="text" id="teacherName" name="teacherName" required>
                    </div>
                    <div class="form-group">
                        <label for="teacherEmail">Email</label>
                        <input type="email" id="teacherEmail" name="teacherEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="teacherPassword">Password</label>
                        <input type="password" id="teacherPassword" name="teacherPassword" required>
                        <button type="button" class="show-hide-btn" onclick="togglePassword('teacherPassword', this)">Show</button>
                    </div>
                    <div class="form-group">
                        <label for="teacherConfirmPassword">Confirm Password</label>
                        <input type="password" id="teacherConfirmPassword" name="teacherConfirmPassword" required>
                        <button type="button" class="show-hide-btn" onclick="togglePassword('teacherConfirmPassword', this)">Show</button>
                        <div class="error-message" id="teacherPasswordError"></div>
                    </div>
                    <button type="submit" class="submit-btn">Register as Teacher</button>
                </div>
                <div id="parentForm" class="fade" style="display:none;">
                    <div class="form-group">
                        <label for="parentName">Student Name</label>
                        <input type="text" id="parentName" name="parentName" required>
                    </div>
                    <div class="form-group">
                        <label for="parentEmail">Email</label>
                        <input type="email" id="parentEmail" name="parentEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="parentPassword">Password</label>
                        <input type="password" id="parentPassword" name="parentPassword" required>
                        <button type="button" class="show-hide-btn" onclick="togglePassword('parentPassword', this)">Show</button>
                    </div>
                    <div class="form-group">
                        <label for="parentConfirmPassword">Confirm Password</label>
                        <input type="password" id="parentConfirmPassword" name="parentConfirmPassword" required>
                        <button type="button" class="show-hide-btn" onclick="togglePassword('parentConfirmPassword', this)">Show</button>
                        <div class="error-message" id="parentPasswordError"></div>
                    </div>
                    <div class="form-group">
                        <label for="parentPin">Parental PIN</label>
                        <input type="number" id="parentPin" name="parentPin" required maxlength="6" pattern="\d{6}" inputmode="numeric" oninput="limitPinLength(this)">
                        <div class="error-message" id="parentPinError"></div>
                    </div>
                    <button type="submit" class="submit-btn">Register as Parent</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Tab switching logic
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const teacherTab = document.getElementById('teacherTab');
        const parentTab = document.getElementById('parentTab');
        const teacherForm = document.getElementById('teacherForm');
        const parentForm = document.getElementById('parentForm');

        loginTab.addEventListener('click', function() {
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            loginForm.style.display = '';
            registerForm.style.display = 'none';
        });
        registerTab.addEventListener('click', function() {
            loginTab.classList.remove('active');
            registerTab.classList.add('active');
            loginForm.style.display = 'none';
            registerForm.style.display = '';
        });
        teacherTab.addEventListener('click', function(e) {
            e.preventDefault();
            teacherTab.classList.add('active');
            parentTab.classList.remove('active');
            teacherForm.style.display = '';
            parentForm.style.display = 'none';
            teacherForm.classList.add('fade');
            parentForm.classList.remove('fade');
        });
        parentTab.addEventListener('click', function(e) {
            e.preventDefault();
            parentTab.classList.add('active');
            teacherTab.classList.remove('active');
            teacherForm.style.display = 'none';
            parentForm.style.display = '';
            parentForm.classList.add('fade');
            teacherForm.classList.remove('fade');
        });

        // Password show/hide toggle
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                btn.textContent = 'Hide';
            } else {
                field.type = 'password';
                btn.textContent = 'Show';
            }
        }

        // Limit PIN length
        function limitPinLength(input) {
            if (input.value.length > 6) {
                input.value = input.value.slice(0, 6);
            }
            if (!/^\d*$/.test(input.value)) {
                input.value = input.value.replace(/\D/g, '');
            }
        }

        // Password validation
        function validatePasswordMatch(type) {
            let pw, cpw, errorDiv;
            if (type === 'teacher') {
                pw = document.getElementById('teacherPassword');
                cpw = document.getElementById('teacherConfirmPassword');
                errorDiv = document.getElementById('teacherPasswordError');
            } else {
                pw = document.getElementById('parentPassword');
                cpw = document.getElementById('parentConfirmPassword');
                errorDiv = document.getElementById('parentPasswordError');
            }
            if (cpw.value && pw.value !== cpw.value) {
                cpw.classList.add('error');
                errorDiv.textContent = 'Passwords do not match';
            } else {
                cpw.classList.remove('error');
                errorDiv.textContent = '';
            }
        }
        document.getElementById('teacherPassword').addEventListener('input', function() {
            validatePasswordMatch('teacher');
        });
        document.getElementById('teacherConfirmPassword').addEventListener('input', function() {
            validatePasswordMatch('teacher');
        });
        document.getElementById('parentPassword').addEventListener('input', function() {
            validatePasswordMatch('parent');
        });
        document.getElementById('parentConfirmPassword').addEventListener('input', function() {
            validatePasswordMatch('parent');
        });

        // PIN validation for parent
        document.getElementById('parentPin').addEventListener('input', function() {
            const pinInput = this;
            const errorDiv = document.getElementById('parentPinError');
            if (!/^\d{6}$/.test(pinInput.value)) {
                pinInput.classList.add('error');
                errorDiv.textContent = 'PIN must be exactly 6 digits';
            } else {
                pinInput.classList.remove('error');
                errorDiv.textContent = '';
            }
        });

        // Password hashing (for demonstration only, use backend for real security!)
        function hashPassword(pw) {
            // Simple SHA-256 hash using Web Crypto API
            return crypto.subtle.digest('SHA-256', new TextEncoder().encode(pw)).then(buf => {
                return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2, '0')).join('');
            });
        }

        // Login form submit
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const pw = document.getElementById('loginPassword').value;
            hashPassword(pw).then(hash => {
                // Connect to backend here
                alert('Login submitted! Email: ' + email + '\nPassword Hash: ' + hash);
            });
        });
        // Register form submit
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let formType = teacherTab.classList.contains('active') ? 'teacher' : 'parent';
            if (formType === 'teacher') {
                const name = document.getElementById('teacherName').value;
                const email = document.getElementById('teacherEmail').value;
                const pw = document.getElementById('teacherPassword').value;
                const cpw = document.getElementById('teacherConfirmPassword').value;
                if (pw !== cpw) return;
                hashPassword(pw).then(hash => {
                    // Connect to backend here
                    alert('Register Teacher! Name: ' + name + '\nEmail: ' + email + '\nPassword Hash: ' + hash);
                });
            } else {
                const name = document.getElementById('parentName').value;
                const email = document.getElementById('parentEmail').value;
                const pw = document.getElementById('parentPassword').value;
                const cpw = document.getElementById('parentConfirmPassword').value;
                const pin = document.getElementById('parentPin').value;
                if (pw !== cpw || !/^\d{6}$/.test(pin)) return;
                hashPassword(pw).then(hash => {
                    // Connect to backend here
                    alert('Register Parent! Student Name: ' + name + '\nEmail: ' + email + '\nPassword Hash: ' + hash + '\nPIN: ' + pin);
                });
            }
        });
    </script>
</body>

</html>