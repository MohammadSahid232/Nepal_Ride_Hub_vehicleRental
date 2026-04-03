<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php'; 
?>
<style>
/* Hide the default navbar and footer */
.top-bar, .navbar-redesigned, .footer { display: none !important; }

body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: url('uploads/premium_bg.png') no-repeat center center/cover;
    position: relative;
}

/* Dark overlay for contrast if needed */
body::before {
    content: '';
    position: absolute;
    top:0; left:0; width:100%; height:100%;
    background: linear-gradient(90deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.4) 100%);
    z-index: 1;
}

/* Floating overlay layout */
.auth-premium-wrapper {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 100vh;
    padding: 0 8%;
}

/* Left Content */
.auth-premium-left {
    flex: 1;
    color: #fff;
    max-width: 500px;
}
.auth-top-logo {
    position: absolute;
    top: 30px;
    left: 5%;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    z-index: 10;
}
.auth-top-logo .icon {
    width: 40px;
    height: 40px;
    background-color: #3561ff;
    color: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.auth-top-logo .titles { display: flex; flex-direction: column; }
.auth-top-logo .titles .main { font-weight: 700; color: #fff; font-size: 1.1rem; line-height: 1.1; font-family: 'Inter', sans-serif; }
.auth-top-logo .titles .sub { color: #ccc; font-size: 0.6rem; font-weight: 600; letter-spacing: 1.5px; }

.auth-premium-left h1 {
    font-size: 3.8rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    line-height: 1.1;
    margin: 0;
    color: #ffffff;
    text-shadow: 0 4px 15px rgba(0,0,0,0.4);
}

/* Right Card */
.auth-premium-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    width: 100%;
    max-width: 440px;
    border-radius: 20px;
    padding: 3rem;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
}

/* Card Logo */
.card-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
}
.card-logo .icon {
    width: 32px;
    height: 32px;
    background-color: #3561ff;
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.card-logo .titles { display: flex; flex-direction: column; }
.card-logo .titles .main { font-weight: 800; color: #111; font-size: 0.95rem; line-height: 1.1; font-family: 'Inter', sans-serif; }
.card-logo .titles .sub { color: #777; font-size: 0.5rem; font-weight: 600; letter-spacing: 1px; }

.auth-premium-card h2 {
    font-family: 'Outfit', sans-serif;
    font-size: 2.2rem;
    font-weight: 800;
    color: #111;
    text-align: center;
    margin-bottom: 2rem;
    margin-top: 0;
}

.auth-form .form-group { margin-bottom: 1.5rem; }
.auth-form label {
    display: block;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.6rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}
.input-with-icon i {
    position: absolute;
    left: 1.2rem;
    color: #888;
    font-size: 1.1rem;
}
.input-with-icon input {
    width: 100%;
    padding: 1rem 1rem 1rem 3.2rem;
    border: 1.5px solid #eee;
    border-radius: 12px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    background: #fcfcfc;
    transition: all 0.3s ease;
}
.input-with-icon input:focus { 
    outline: none; 
    border-color: #3561ff;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(53,97,255,0.1); 
}

.btn-auth-submit {
    background-color: #3561ff;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 1rem;
    width: 100%;
    margin: 2rem 0 1.5rem;
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: transform 0.2s, background-color 0.2s;
}
.btn-auth-submit:hover { 
    background-color: #264ac9;
    transform: translateY(-2px);
}
.btn-auth-submit:active { transform: translateY(0); }

.auth-links-group {
    text-align: center;
    font-size: 0.9rem;
    color: #666;
}
.auth-links-group a {
    color: #3561ff;
    text-decoration: none;
    font-weight: 700;
}
.auth-links-group a:hover { text-decoration: underline; }
.auth-links-group p { margin: 0.5rem 0; }

.auth-social {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.btn-social {
    background-color: #fff;
    color: #444;
    border: 1.5px solid #eee;
    border-radius: 12px;
    padding: 1rem;
    width: 100%;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    font-family: 'Inter', sans-serif;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-social:hover { 
    background-color: #f9f9f9;
    border-color: #ddd;
    transform: translateY(-1px);
}
.btn-social img { width: 20px; height: 20px; }

@media (max-width: 900px) {
    .auth-premium-wrapper {
        flex-direction: column;
        justify-content: center;
        padding: 6rem 2rem 2rem;
        gap: 3rem;
    }
    .auth-premium-left h1 { margin-bottom: 0; font-size: 3rem; text-align: center; }
    .auth-top-logo { 
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
    }
}
</style>

<div class="auth-premium-wrapper">
    <div class="auth-premium-left">
        <a href="index.php" class="auth-top-logo">
            <div class="icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="titles">
                <span class="main">Nepal Ride Hub</span>
                <span class="sub" style="color:#e0e0e0;">PREMIUM MOBILITY</span>
            </div>
        </a>
        <h1>Premium rides<br>across Nepal</h1>
    </div>

    <div class="auth-premium-card">
        <div class="card-logo">
            <div class="icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="titles">
                <span class="main">Nepal Ride Hub</span>
                <span class="sub">PREMIUM MOBILITY</span>
            </div>
        </div>
        
        <h2>Login</h2>
        <div id="loginAlert" class="alert" style="display: none; font-size: 0.85rem; margin-bottom: 1rem; padding: 0.8rem;"></div>
        
        <form id="loginForm" class="auth-form">
            <div class="form-group">
                <label for="email">Username / Email:</label>
                <div class="input-with-icon">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" id="email" name="email" placeholder="Enter username or email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>
            
            <div style="text-align: right; margin-bottom: 1.5rem;">
                <a href="forgot_password.php" style="color: #3561ff; font-weight: 700; text-decoration: none; font-size: 0.85rem;">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-auth-submit" id="loginBtn">Sign in</button>
        </form>
        
        <div class="divider"><span>OR CONTINUE WITH</span></div>

        <div class="auth-social">
            <a href="api/oauth_google.php?action=login" class="btn-social" style="background: #fdfdfd; border-color: #eee;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"> 
                <span style="font-weight: 700;">Google Account</span>
            </a>
            <a href="api/oauth_facebook.php?action=login" class="btn-social" style="background: #fdfdfd; border-color: #eee;">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook"> 
                <span style="font-weight: 700;">Facebook Account</span>
            </a>
        </div>

        <div class="auth-links-group" style="margin-top: 2.5rem;">
            <p>Don't have an account? <a href="register.php">Create Account</a></p>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('api/auth.php?action=login', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const loginAlert = document.getElementById('loginAlert');
        loginAlert.style.display = 'block';
        if (data.success) {
            loginAlert.className = 'alert alert-success';
            loginAlert.textContent = data.message;
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            loginAlert.className = 'alert alert-danger';
            loginAlert.textContent = data.message;
        }
    });
});
</script>
</body>
</html>
