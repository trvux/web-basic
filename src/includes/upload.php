<?php
// Tải ảnh lên an toàn, dùng chung cho banner và bài viết.
// Ảnh lưu ở src/uploads/<thư mục>/, trong DB chỉ lưu TÊN FILE.

const UPLOAD_MAX_BYTES = 2 * 1024 * 1024; // 2 MB

// $field = tên ô <input type="file">, $folder = 'banners' hoặc 'posts'.
// Trả về [tên file, null] nếu thành công, [null, null] nếu không chọn file, [null, 'câu báo lỗi'] nếu lỗi.
function save_image_upload($field, $folder) {
    $file = $_FILES[$field] ?? null;
    if (!$file || !is_int($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > UPLOAD_MAX_BYTES) {
        return [null, 'Ảnh quá lớn, tối đa 2 MB.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        return [null, 'Tải ảnh lên thất bại, hãy thử lại.'];
    }

    // Đọc nội dung file để biết loại thật, không tin vào đuôi file người dùng đặt
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    $info = @getimagesize($file['tmp_name']);
    if (!$info || !isset($types[$info[2]])) {
        return [null, 'Chỉ nhận ảnh JPG, PNG hoặc WEBP.'];
    }

    // Tên file do server đặt ngẫu nhiên
    $name = bin2hex(random_bytes(8)) . '.' . $types[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . "/../uploads/$folder/$name")) {
        return [null, 'Không lưu được ảnh trên máy chủ.'];
    }
    return [$name, null];
}

function delete_image_upload($name, $folder) {
    $path = __DIR__ . "/../uploads/$folder/" . basename((string)$name);
    if ($name && is_file($path)) unlink($path);
}

// Đường dẫn để hiển thị ảnh đã tải lên
function upload_url($name, $folder) {
    return "/uploads/$folder/" . rawurlencode(basename((string)$name));
}
