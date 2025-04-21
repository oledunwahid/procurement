<?php session_start();
if (isset($_SESSION['username']) && $_SESSION['username']) {
    // Jika sudah login, arahkan ke halaman akses (atau halaman home)
    header("Location: location:index.php?Page=Dashboard");
    exit();
}

// Store session messages in temporary variables
$message = isset($_SESSION['Messages']) ? $_SESSION['Messages'] : '';
$icon = isset($_SESSION['Icon']) ? $_SESSION['Icon'] : '';

// Clear session variables after storing
if (isset($_SESSION['Messages'])) unset($_SESSION['Messages']);
if (isset($_SESSION['Icon'])) unset($_SESSION['Icon']);
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Sign In with Token | EIP MAA Group</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Employee Information Portal" name="description" />
    <meta content="Mineral Alam Abadi" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/logo.svg">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

    <!-- Add SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .text-field {
            color: #fff !important;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Using existing auth-one-bg classes from your current theme */
        .auth-one-bg-position {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .login-logo {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .login-logo img {
            height: 30px;
            width: auto;
            max-width: 100%;
        }

        .login-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #fff;
        }

        .login-title h2 {
            font-size: 1.35rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-title p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Form control styling */
        .form-control {
            background-color: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            color: #fff !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Additional style for field icons to ensure visibility */
        .field-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7) !important;
            z-index: 1;
        }

        /* Ensure text inside input field is visible */
        input.form-control {
            color: #fff !important;
        }

        /* Glossy gradient login button */
        .login-btn {
            background: linear-gradient(to right, #890707, #c61111, #e52222) !important;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(137, 7, 7, 0.3);
        }

        /* Efek hover untuk button */
        .login-btn:hover {
            background: linear-gradient(to right, #750606, #b30f0f, #d71f1f) !important;
            box-shadow: 0 5px 20px rgba(137, 7, 7, 0.5);
            transform: translateY(-2px);
        }

        /* Efek aktif saat diklik */
        .login-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(137, 7, 7, 0.4);
        }

        /* Tambahkan efek glossy dengan pseudo-element */
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    rgba(255, 255, 255, 0) 100%);
            transform: skewX(-25deg);
            transition: all 0.75s;
        }

        /* Animasi glossy saat hover */
        .login-btn:hover::before {
            left: 100%;
        }

        .login-footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 1.5rem;
        }

        .heart-icon {
            color: #e74c3c;
            display: inline-block;
        }

        /* Force override any bg-overlay styles */
        .bg-overlay {
            position: absolute !important;
            height: 100% !important;
            width: 100% !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            top: 0 !important;
            opacity: 0.7 !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
            background-image: none !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            z-index: -1 !important;
        }

        /* Additional style to ensure nothing else overrides */
        html,
        body,
        .login-container,
        .auth-one-bg-position {
            background-color: transparent !important;
        }

        /* Add an inline style directly to the element */
        #auth-particles::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            z-index: -1;
            pointer-events: none;
        }

        .form-control.ps-4 {
            padding-left: 35px !important;
        }

        /* Fix for autofill coloring */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px rgba(35, 35, 40, 0.8) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
            caret-color: white;
        }

        /* Memastikan warna teks tetap putih */
        input:-webkit-autofill::first-line {
            color: white !important;
        }

        /* Tambahan untuk browser Firefox dan Edge */
        @-moz-document url-prefix() {
            input.form-control {
                background-color: rgba(35, 35, 40, 0.8) !important;
            }
        }

        /* Remove the shape from the original theme */
        .shape {
            display: none;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Auth background with particles -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <!-- Logo -->
            <div class="login-logo">
                <img src="assets/images/logo_MAAA.png" alt="Mineral Alam Abadi Logo">
            </div>

            <!-- Title -->
            <div class="login-title">
                <h2 class="text-white">Token Authentication</h2>
                <p>Sign in with your token to access the portal</p>
            </div>

            <!-- Login Form -->
            <form action="systemlogintoken.php" method="POST">
                <!-- Token Field -->
                <div class="mb-3">
                    <label for="token" class="form-label text-field">Token</label>
                    <div class="position-relative">
                        <span class="field-icon">
                            <i class="ri-key-line"></i>
                        </span>
                        <input type="password" class="form-control ps-4" id="token" name="token"
                            placeholder="Enter your token" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn">Sign In</button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            &copy; <script>
                document.write(new Date().getFullYear())
            </script>
            Employee Information Portal <span class="heart-icon">❤</span> by Mineral Alam Abadi
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="assets/libs/particles.js/particles.js"></script>
    <script src="assets/js/pages/particles.app.js"></script>

    <!-- SweetAlert for messages -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($message != '' && $icon != '') { ?>
                Swal.fire({
                    icon: '<?php echo $icon; ?>',
                    title: '<?php echo ($icon == 'success') ? 'Success' : 'Error'; ?>',
                    text: '<?php echo $message; ?>',
                    showConfirmButton: true,
                    timer: 3000
                });
            <?php } ?>
        });
    </script>
</body>

</html>