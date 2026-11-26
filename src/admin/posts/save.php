<?php
// Kiểm tra dữ liệu form bài viết và lưu. $id = null là thêm mới.
// Trả về câu báo lỗi, hoặc null nếu đã lưu. $post được cập nhật theo dữ liệu vừa nhập.
function save_post($pdo, &$post, $id = null) {
    $post['title'] = trim((string)($_POST['title'] ?? ''));
    $post['content'] = trim((string)($_POST['content'] ?? ''));

    if ($post['title'] === '' || $post['content'] === '') {
        return 'Tiêu đề và nội dung không được trống (nếu ảnh quá lớn cũng sẽ báo lỗi này).';
    }

    [$newImage, $uploadError] = save_image_upload('image', 'posts');
    if ($uploadError) return $uploadError;

    $oldImage = $post['image'];
    if ($newImage) $post['image'] = $newImage;
    $values = [$post['title'], $post['content'], $post['image'] ?: null];

    if ($id === null) {
        $pdo->prepare('INSERT INTO posts (title, content, image) VALUES (?, ?, ?)')->execute($values);
    } else {
        $pdo->prepare('UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?')->execute([...$values, $id]);
        if ($newImage) delete_image_upload($oldImage, 'posts');
    }
    return null;
}
