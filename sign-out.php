<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>Sign Out | EIP MAA Group</title>
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

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Inter', sans-serif;
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

        .logout-card {
            width: 100%;
            max-width: 420px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            z-index: 1;
            text-align: center;
            color: #fff;
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        /* Efek hover untuk button */
        .login-btn:hover {
            background: linear-gradient(to right, #750606, #b30f0f, #d71f1f) !important;
            box-shadow: 0 5px 20px rgba(137, 7, 7, 0.5);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
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

        /* Remove the shape from the original theme */
        .shape {
            display: none;
        }

        .logout-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Auth background with particles -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
        </div>

        <!-- Logout Card -->
        <div class="logout-card">
            <!-- Logo -->
            <div class="login-logo">
                <img src="assets/images/logo_MAAA.png" alt="Mineral Alam Abadi Logo">
            </div>

            <!-- Logout Icon -->
            <lord-icon src="https://cdn.lordicon.com/hzomhqxz.json" trigger="loop" colors="primary:#ffffff,secondary:#e52222" class="logout-icon"></lord-icon>

            <h3 class="mt-4 text-white">You are Logged Out</h3>
            <p class="text-white-50">Thank you for using Employee Information Portal</p>

            <div class="mt-4">
                <a href="login.php" class="login-btn">Sign In Again</a>
            </div>
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
</body>

</html>