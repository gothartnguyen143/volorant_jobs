<?php

set_time_limit(300); // Tăng thời gian chạy script lên 300 giây (5 phút) để tránh timeout khi tải/giải nén file lớn
ignore_user_abort(true); // Đảm bảo script tiếp tục chạy ngay cả khi người dùng đóng trình duyệt
ini_set('memory_limit', '256M');
$current_script_name = basename(__FILE__); // Lấy tên của file script hiện tại
$initial_script_dir = __DIR__; // Lưu lại đường dẫn thư mục gốc nơi script được chạy

echo "<h1>Bắt đầu quá trình tự động cập nhật</h1>";
echo "<h3>1. Xóa tất cả file và thư mục (ngoại trừ .htaccess gốc và script hiện tại)</h3>";

/**
 * Hàm đệ quy để xóa file và thư mục
 * @param string $dir Đường dẫn đến thư mục cần xóa
 * @param string $current_script_name Tên của script đang chạy để loại trừ
 * @param string $initial_script_dir Đường dẫn thư mục gốc nơi script được chạy
 */
function delete_recursive($dir, $current_script_name, $initial_script_dir) {
    if (!is_dir($dir)) {
        return false; // Đảm bảo đây là một thư mục
    }

    $items = scandir($dir); // Lấy tất cả các mục trong thư mục

    foreach ($items as $item) {
        // Bỏ qua . và ..
        if ($item == '.' || $item == '..') {
            continue;
        }

        $item_path = $dir . DIRECTORY_SEPARATOR . $item; // Xây dựng đường dẫn đầy đủ

        
        // === LOGIC LOẠI TRỪ ===
        // 1. Luôn bỏ qua chính file script đang chạy
        if ($item == $current_script_name && $dir == $initial_script_dir) {
            echo "Đã bỏ qua file script đang chạy: <b>" . $item_path . "</b><br>";
            continue;
        }

        // 2. Chỉ bỏ qua .htaccess NẾU nó nằm trong thư mục gốc ban đầu
        if ($item == '.htaccess' && $dir == $initial_script_dir) {
            echo "Đã bỏ qua .htaccess ở thư mục gốc: <b>" . $item_path . "</b><br>";
            continue;
        }
        // =======================
        
    

        if (is_dir($item_path)) {
            // Nếu là thư mục, gọi đệ quy để xóa nội dung bên trong
            echo "Đang xử lý thư mục: " . $item_path . "<br>";
            delete_recursive($item_path, $current_script_name, $initial_script_dir);
            // Sau khi xóa hết nội dung, xóa thư mục rỗng
            if (rmdir($item_path)) {
                echo "Đã xóa thư mục: <b>" . $item_path . "</b><br>";
            } else {
                echo "Không thể xóa thư mục: <b>" . $item_path . "</b> (kiểm tra quyền hoặc thư mục không rỗng)<br>";
            }
        } else {
            // Nếu là file, xóa file
            if (unlink($item_path)) {
                echo "Đã xóa file: <b>" . $item_path . "</b><br>";
            } else {
                echo "Không thể xóa file: <b>" . $item_path . "</b> (kiểm tra quyền)<br>";
            }
        }
    }
}

// Gọi hàm xóa, truyền vào thư mục hiện tại và thư mục gốc
delete_recursive(__DIR__, $current_script_name, $initial_script_dir);

echo "<p><b>Hoàn tất quá trình xóa file và thư mục.</b></p>";
echo "<hr>";

// --- Bắt đầu phần tải và giải nén ---
echo "<h3>2. Tải file ZIP từ GitHub</h3>";

$zip_url = "https://codeload.github.com/codevcn/web-thue-acc-valorant/zip/refs/heads/main";
$zip_file_name = "repository.zip"; // Tên file zip sẽ được lưu
$zip_file_path = $initial_script_dir . DIRECTORY_SEPARATOR . $zip_file_name;

echo "Đang tải file từ: " . $zip_url . " về " . $zip_file_path . "<br>";
function download_with_curl(string $url, string $savePath): void {
    if (!is_dir(dirname($savePath)) || !is_writable(dirname($savePath))) {
        throw new Exception("Thư mục đích không ghi được: " . dirname($savePath));
    }

    $fp = fopen($savePath, 'wb');
    if ($fp === false) {
        throw new Exception("Không thể mở file để ghi: $savePath");
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FILE            => $fp,          // ghi trực tiếp xuống file
        CURLOPT_FOLLOWLOCATION  => true,         // theo dõi redirect
        CURLOPT_USERAGENT       => 'Mozilla/5.0',// GitHub đôi khi cần UA
        CURLOPT_SSL_VERIFYPEER  => true,         // nên bật
        CURLOPT_CONNECTTIMEOUT  => 15,
        CURLOPT_TIMEOUT         => 120,
        CURLOPT_FAILONERROR     => false,        // tự xử lý HTTP code
        CURLOPT_VERBOSE         => true,         // bật log cURL (in ra stdout)
    ]);

    $ok = curl_exec($ch);
    $errNo  = curl_errno($ch);
    $errMsg = curl_error($ch);
    $http   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    fclose($fp);

    if ($ok === false || $errNo !== 0) {
        @unlink($savePath);
        throw new Exception("cURL error ($errNo): $errMsg");
    }
    if ($http !== 200) {
        @unlink($savePath);
        throw new Exception("HTTP $http khi tải $url");
    }

    if (!file_exists($savePath) || filesize($savePath) === 0) {
        @unlink($savePath);
        throw new Exception("File tải về rỗng hoặc không tồn tại: $savePath");
    }
}

try {
    // $zip_content = file_get_contents($zip_url);
    // if ($zip_content === false) {
    //     throw new Exception("Không thể tải file ZIP từ URL.");
    // }

    // if (file_put_contents($zip_file_path, $zip_content) === false) {
    //     throw new Exception("Không thể lưu file ZIP đã tải về.");
    // }
    // echo "Đã tải file ZIP thành công: <b>" . $zip_file_name . "</b><br>";
    
    try {
    download_with_curl($zip_url, $zip_file_path);
    echo "✅ Đã tải file ZIP thành công: <b>$zip_file_name</b>, size=" . filesize($zip_file_path) . " bytes<br>";
} catch (Exception $e) {
    echo "<p style='color:red'><b>LỖI tải ZIP:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    if (file_exists($zip_file_path)) { @unlink($zip_file_path); }
    return; // dừng sớm nếu tải thất bại
}
    
    
    
    
    

    echo "<hr>";
    echo "<h3>3. Giải nén file ZIP</h3>";

    $zip = new ZipArchive;
    if ($zip->open($zip_file_path) === TRUE) {
        // Giải nén vào thư mục hiện tại
        $zip->extractTo($initial_script_dir);
        $zip->close();
        echo "Đã giải nén file ZIP thành công.<br>";

        echo "<hr>";
        echo "<h3>4. Di chuyển nội dung ra thư mục gốc và dọn dẹp</h3>";

        // Tìm thư mục duy nhất vừa được giải nén (thường có dạng tên-repo-main)
        $extracted_folder = '';
        $items_after_extract = scandir($initial_script_dir);
        foreach ($items_after_extract as $item) {
            if ($item == '.' || $item == '..' || $item == $zip_file_name || $item == $current_script_name || $item == '.htaccess') {
                continue;
            }
            $item_path = $initial_script_dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($item_path)) {
                $extracted_folder = $item_path;
                break; // Tìm thấy thư mục đầu tiên (hy vọng là duy nhất)
            }
        }




        if ($extracted_folder && is_dir($extracted_folder)) {
            echo "Tìm thấy thư mục đã giải nén: <b>" . basename($extracted_folder) . "</b><br>";

            $extracted_contents = scandir($extracted_folder);
            foreach ($extracted_contents as $content) {
                if ($content == '.' || $content == '..') {
                    continue;
                }
                $source_path = $extracted_folder . DIRECTORY_SEPARATOR . $content;
                $destination_path = $initial_script_dir . DIRECTORY_SEPARATOR . $content;

                // Di chuyển file/thư mục
                if (rename($source_path, $destination_path)) {
                    echo "Đã di chuyển: <b>" . $content . "</b><br>";
                } else {
                    echo "Không thể di chuyển: <b>" . $content . "</b> (kiểm tra quyền hoặc file/thư mục đích đã tồn tại)<br>";
                }
            }

            // Xóa thư mục rỗng sau khi đã di chuyển nội dung
            if (rmdir($extracted_folder)) {
                echo "Đã xóa thư mục rỗng: <b>" . basename($extracted_folder) . "</b><br>";
            } else {
                echo "Không thể xóa thư mục rỗng: <b>" . basename($extracted_folder) . "</b> (kiểm tra quyền)<br>";
            }

        } else {
            echo "Không tìm thấy thư mục đã giải nén hoặc có lỗi.<br>";
        }

    } else {
        throw new Exception("Không thể mở file ZIP để giải nén. Mã lỗi: " . $zip->getStatusString());
    }

    echo "<hr>";
    echo "<h3>5. Xóa file ZIP đã tải về</h3>";
    // Xóa file ZIP
    if (unlink($zip_file_path)) {
        echo "Đã xóa file ZIP: <b>" . $zip_file_name . "</b><br>";
    } else {
        echo "Không thể xóa file ZIP: <b>" . $zip_file_name . "</b> (kiểm tra quyền)<br>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><b>LỖI: " . $e->getMessage() . "</b></p>";
    // Cố gắng xóa file zip nếu nó đã được tải về nhưng có lỗi sau đó
    if (file_exists($zip_file_path)) {
        unlink($zip_file_path);
        echo "Đã xóa file ZIP bị lỗi (nếu có).<br>";
    }
}

echo "<hr>";
echo "<hr>";
echo "<h2>6. Đổi tên thư mục (chữ thường thành chữ hoa đầu tiên)</h2>";

$directories_to_rename = [
    'src/controllers/apis',
    'src/controllers',
    'src/services',
    'src/core',
    'src/utils'
    // Thêm các thư mục khác nếu bạn muốn đổi tên
];

$base_dir = __DIR__; // Thư mục gốc nơi script đang chạy

foreach ($directories_to_rename as $old_relative_path) {
    $old_full_path = $base_dir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $old_relative_path);

    // Tách phần tên thư mục cuối cùng và chuyển đổi chữ hoa đầu tiên
    $path_parts = explode(DIRECTORY_SEPARATOR, $old_full_path);
    $last_part = array_pop($path_parts);
    $new_last_part = ucfirst($last_part); // Chuyển đổi chữ cái đầu tiên thành chữ hoa

    $new_full_path = implode(DIRECTORY_SEPARATOR, $path_parts) . DIRECTORY_SEPARATOR . $new_last_part;

    if (is_dir($old_full_path)) {


        if (rename($old_full_path, $new_full_path)) {
            echo "Đã đổi tên thư mục: <b>" . $old_relative_path . "</b> thành <b>" . str_replace($last_part, $new_last_part, $old_relative_path) . "</b><br>";
        } else {
            echo "Không thể đổi tên thư mục: <b>" . $old_relative_path . "</b> (kiểm tra quyền hoặc thư mục đích đã tồn tại)<br>";
        }
    } else {
        echo "Thư mục không tồn tại: <b>" . $old_relative_path . "</b> (bỏ qua)<br>";
    }
}

echo "<p><b>Hoàn tất quá trình đổi tên thư mục.</b></p>";
echo "<hr>";
echo "<h1>Quá trình cập nhật hoàn tất!</h1>";
echo "<h1>Hoàn tất quá trình.</h1>";

?>