<?php
include "roleseason.php";
include "koneksi.php";
?>
<?php $tgl = date('Y-m-d'); ?>

<?php
$idnik = $_SESSION['idnik'];
$sqllogin = mysqli_query($koneksi, "SELECT * FROM  user WHERE idnik='$idnik'");
$rowlogin = mysqli_fetch_assoc($sqllogin);

$niklogin = $idnik;
$namalogin = $rowlogin['nama'];
$foto_profile = $rowlogin['file_foto'];
$divisilogin = $rowlogin['divisi'];


$role = isset($_SESSION['role']) ? $_SESSION['role'] : [];

date_default_timezone_set('Asia/Jakarta');
?>

<?php
function rupiah($angka)
{
    $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
    return $hasil_rupiah;
}

function fsize($file)
{
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    $size = filesize($file);
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, 2) . " " . $a[$pos];
}
?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
    <meta charset="utf-8" />
    <title>EIP | Mineral Alam Abadi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <link rel="shortcut icon" href="assets/images/logo.svg">
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/layout.js"></script>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Floating Chat Button */
        .chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #8B0000;
            /* Dark Red */
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Chat Window */
        .chat-window {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 300px;
            height: 400px;
            border: 1px solid #ccc;
            background-color: white;
            display: none;
            flex-direction: column;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .chat-header {
            background-color: #8B0000;
            /* Dark Red */
            color: white;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }

        .chat-footer {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #ccc;
        }

        .chat-input {
            width: 80%;
            padding: 5px;
        }

        .chat-send {
            background-color: #8B0000;
            /* Dark Red */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include "layout/page-topbar.php" ?>
        <?php include "layout/menu-procurement.php" ?>
        <div class="vertical-overlay"></div>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0"><?php echo $page = $_GET['page']; ?></h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php?page=Dashboard">Dashboards</a></li>
                                        <li class="breadcrumb-item active"><?php echo $page = $_GET['page']; ?></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include "akses_menu_procurement.php" ?>
                </div>
            </div>
            <?php include "layout/footer.php" ?>
        </div>
    </div>

    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    <!-- Floating Chat Button -->
    <button class="chat-button" id="chat-button">💬</button>

    <!-- Chat Window -->
    <div class="chat-window" id="chat-window">
        <div class="chat-header">
            Chat
            <button onclick="toggleChatWindow()">✖</button>
        </div>
        <div class="chat-body" id="chat-body">
            <!-- Chat messages will appear here -->
        </div>
        <div class="chat-footer">
            <input type="text" class="chat-input" id="chat-input" placeholder="Type a message">
            <button class="chat-send" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world-merc.js"></script>
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/libs/prismjs/prism.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        document.getElementById('chat-button').addEventListener('click', toggleChatWindow);

        function toggleChatWindow() {
            var chatWindow = document.getElementById('chat-window');
            if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                chatWindow.style.display = 'flex';
            } else {
                chatWindow.style.display = 'none';
            }
        }

        function sendMessage() {
            var input = document.getElementById('chat-input');
            var message = input.value;
            if (message.trim() !== '') {
                var chatBody = document.getElementById('chat-body');
                var messageElement = document.createElement('div');
                messageElement.textContent = message;
                chatBody.appendChild(messageElement);
                input.value = '';
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        }
    </script>
</body>

</html>