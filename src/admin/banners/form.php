<?php
if (!function_exists('ui_btn')) exit; // chỉ dùng để include
// Form dùng chung cho create.php và edit.php. Cần sẵn: $banner (mảng), $error.
?>
<div class="<?= UI_CARD ?> max-w-xl">
  <form method="post" enctype="multipart/form-data" class="<?= UI_CARD_CONTENT ?> gap-4!">
    <?php if ($error): ?><div class="<?= UI_ALERT_ERROR ?>"><?= e($error) ?></div><?php endif; ?>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="title">Tiêu đề</label>
      <input class="<?= UI_INPUT ?>" id="title" name="title" maxlength="255" value="<?= e($banner['title']) ?>" required>
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="image">Ảnh (JPG, PNG, WEBP, tối đa 2 MB, nên dùng ảnh ngang tỉ lệ 3:1)</label>
      <?php if ($banner['image']): ?>
        <img src="<?= e(upload_url($banner['image'], 'banners')) ?>" alt="Ảnh hiện tại" class="aspect-[3/1] w-full rounded-md bg-muted object-cover">
      <?php endif; ?>
      <input class="<?= UI_INPUT_FILE ?>" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" <?= $banner['image'] ? '' : 'required' ?>>
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="link">Link khi bấm vào (không bắt buộc)</label>
      <input class="<?= UI_INPUT ?>" id="link" name="link" maxlength="255" value="<?= e($banner['link']) ?>" placeholder="/index.php?cat=2">
    </div>
    <div class="<?= UI_FIELD ?>">
      <label class="<?= UI_LABEL ?>" for="sort_order">Thứ tự hiển thị (số nhỏ hiện trước)</label>
      <input class="<?= UI_INPUT ?>" id="sort_order" name="sort_order" type="number" value="<?= (int)$banner['sort_order'] ?>">
    </div>
    <label class="<?= UI_FIELD_ROW ?>">
      <input type="checkbox" name="is_active" value="1" class="<?= UI_SWITCH ?>" <?= $banner['is_active'] ? 'checked' : '' ?>>
      <span class="<?= UI_LABEL ?>">Hiện ở trang chủ</span>
    </label>
    <div class="flex gap-2">
      <button class="<?= ui_btn() ?>" type="submit">Lưu banner</button>
      <a href="index.php" class="<?= ui_btn('outline') ?>">Huỷ</a>
    </div>
  </form>
</div>
