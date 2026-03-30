<?php

// Định nghĩa các hằng số toàn cục dùng trong dự án (chỉ define nếu chưa được định nghĩa)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__)); // Đường dẫn tuyệt đối tới thư mục gốc của dự án
}
if (!defined('BASE_URL')) {
<<<<<<< HEAD
    define('BASE_URL', '/Agile_Scrum_PMA101/base_ws2/'); // URL cơ bản của dự án(Lưu ý cấp độ trong htdocs hoặc www)
=======
    define('BASE_URL', '/base_ws2/base_ws2/'); // URL cơ bản của dự án(Lưu ý cấp độ trong htdocs hoặc www)
>>>>>>> 8f18587a95ae52f595be5d3d1dea905717c60629
}

// Cấu hình cơ bản cho kết nối CSDL
return [
    'db' => [
        'host' => 'localhost', // Host cơ sở dữ liệu
<<<<<<< HEAD
        'name' => 'shop_db_final', // Tên cơ sở dữ liệu
=======
        'name' => 'fuit_shop_agile', // Tên cơ sở dữ liệu
>>>>>>> 8f18587a95ae52f595be5d3d1dea905717c60629
        'user' => 'root', // Tên người dùng
        'pass' => '', // Mật khẩu
        'charset' => 'utf8mb4', // Mã hóa
    ],
];
