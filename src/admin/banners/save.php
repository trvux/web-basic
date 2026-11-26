<?php
// Kiểm tra dữ liệu form banner và lưu. $id = null là thêm mới.
// Trả về câu báo lỗi, hoặc null nếu đã lưu. $banner được cập nhật theo dữ liệu vừa nhập.
function save_banner($pdo, &$banner, $id = null) {
    $banner['title'] = trim((string)($_POST['title'] ?? ''));
    $banner['link'] = trim((string)($_POST['link'] ?? ''));
    $banner['sort_order'] = (int)($_POST['sort_order'] ?? 0);
    $banner['is_active'] = isset($_POST['is_active']) ? 1 : 0;

    if ($banner['title'] === '') return 'Tiêu đề không được trống (nếu ảnh quá lớn cũng sẽ báo lỗi này).';
    // Chỉ nhận link trong trang (bắt đầu bằng /) hoặc http(s), chặn kiểu javascript:
    if ($banner['link'] !== '' && !preg_match('#^(https?://|/)#i', $banner['link'])) {
        return 'Link phải bắt đầu bằng / hoặc http://, https://';
    }

    [$newImage, $uploadError] = save_image_upload('image', 'banners');
    if ($uploadError) return $uploadError;
    if (!$newImage && !$banner['image']) return 'Vui lòng chọn ảnh banner.';

    $oldImage = $banner['image'];
    if ($newImage) $banner['image'] = $newImage;
    $values = [$banner['title'], $banner['image'], $banner['link'] ?: null, $banner['sort_order'], $banner['is_active']];

    if ($id === null) {
        $pdo->prepare('INSERT INTO banners (title, image, link, sort_order, is_active) VALUES (?, ?, ?, ?, ?)')->execute($values);
    } else {
        $pdo->prepare('UPDATE banners SET title = ?, image = ?, link = ?, sort_order = ?, is_active = ? WHERE id = ?')
            ->execute([...$values, $id]);
        if ($newImage) delete_image_upload($oldImage, 'banners'); // xoá ảnh cũ sau khi lưu xong
    }
    return null;
}
