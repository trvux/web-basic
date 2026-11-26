<?php
if (!function_exists('ui_btn')) exit; // chỉ dùng để include
// Form dùng chung cho create.php và edit.php. Cần sẵn: $post (mảng), $error.
?>
<div class="<?= UI_CARD ?> max-w-2xl">
  <form method="post" enctype="multipart/form-data" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="title">Tiêu đề</label>
      <input class="<?= UI_INPUT ?>" id="title" name="title" maxlength="255" value="<?= e($post['title']) ?>" required>
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="image">Ảnh (không bắt buộc, JPG, PNG, WEBP, tối đa 2 MB)</label>
      <?php if ($post['image']): ?>
        <img src="<?= e(upload_url($post['image'], 'posts')) ?>" alt="Ảnh hiện tại" class="aspect-video w-64 rounded-md bg-muted object-cover">
      <?php endif; ?>
      <input class="<?= UI_INPUT_FILE ?>" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="content">Nội dung</label>
      <textarea class="<?= UI_TEXTAREA ?> min-h-48" id="content" name="content" required><?= e($post['content']) ?></textarea>
    </div>
    <div class="flex gap-2">
      <button class="<?= ui_btn() ?>" type="submit">Lưu bài viết</button>
      <a href="index.php" class="<?= ui_btn('outline') ?>">Huỷ</a>
    </div>
  </form>
</div>
