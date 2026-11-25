<?php
// Bảng mẫu mọi component trong includes/ui.php. Mở http://localhost:8088/admin/components.php
// rồi copy đoạn code tương ứng trong file này sang trang của mình.
require_once __DIR__ . '/../includes/admin_header.php';
?>

<?= ui_breadcrumb(['Dashboard' => '/admin/index.php', 'Component' => null]) ?>
<h1 class="<?= UI_H1 ?>">Component giao diện</h1>

<div class="grid items-start gap-4 md:grid-cols-2">

  <section class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>"><div class="<?= UI_CARD_TITLE ?>">Nút, nhãn</div><div class="<?= UI_CARD_DESC ?>">ui_btn(), ui_badge(), UI_BUTTON_GROUP</div></div>
    <div class="<?= UI_CARD_CONTENT ?>">
      <div class="flex flex-wrap gap-2">
        <?php foreach (['default', 'outline', 'secondary', 'ghost', 'destructive', 'link'] as $v): ?>
          <button type="button" class="<?= ui_btn($v) ?>"><?= $v ?></button>
        <?php endforeach; ?>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php foreach (['default', 'secondary', 'destructive', 'outline'] as $v): ?>
          <span class="<?= ui_badge($v) ?>"><?= $v ?></span>
        <?php endforeach; ?>
      </div>
      <div class="<?= UI_BUTTON_GROUP ?>">
        <button type="button" class="<?= ui_btn('outline') ?>">Ngày</button>
        <button type="button" class="<?= ui_btn('outline') ?>">Tuần</button>
        <button type="button" class="<?= ui_btn('outline') ?>">Tháng</button>
      </div>
    </div>
  </section>

  <section class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>"><div class="<?= UI_CARD_TITLE ?>">Thông báo</div><div class="<?= UI_CARD_DESC ?>">UI_ALERT, UI_ALERT_ERROR, UI_ALERT_SUCCESS</div></div>
    <div class="<?= UI_CARD_CONTENT ?>">
      <div class="<?= UI_ALERT ?>"><div class="<?= UI_ALERT_TITLE ?>">Lưu ý</div><div class="<?= UI_ALERT_DESC ?>">Thông báo thường.</div></div>
      <div class="<?= UI_ALERT_ERROR ?>">Có lỗi xảy ra.</div>
      <div class="<?= UI_ALERT_SUCCESS ?>">Đã lưu thành công.</div>
    </div>
  </section>

  <section class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>"><div class="<?= UI_CARD_TITLE ?>">Form</div><div class="<?= UI_CARD_DESC ?>">UI_FIELD_GROUP, UI_INPUT, UI_SELECT, UI_TEXTAREA...</div></div>
    <form class="<?= UI_CARD_CONTENT ?>" onsubmit="return false">
      <div class="<?= UI_FIELD_GROUP ?>">
        <div class="<?= UI_FIELD ?>">
          <label class="<?= UI_LABEL ?>" for="c-name">Tên</label>
          <input class="<?= UI_INPUT ?>" id="c-name" placeholder="Nhập tên">
          <p class="<?= UI_FIELD_DESC ?>">Mô tả ngắn cho ô nhập.</p>
          <p class="<?= UI_FIELD_ERROR ?>">Vui lòng nhập tên.</p>
        </div>
        <div class="<?= UI_FIELD ?>">
          <label class="<?= UI_LABEL ?>" for="c-brand">Hãng</label>
          <select class="<?= UI_SELECT ?>" id="c-brand"><option>Asus</option><option>Dell</option></select>
        </div>
        <div class="<?= UI_FIELD ?>">
          <label class="<?= UI_LABEL ?>" for="c-note">Ghi chú</label>
          <textarea class="<?= UI_TEXTAREA ?>" id="c-note"></textarea>
        </div>
        <div class="<?= UI_FIELD ?>">
          <label class="<?= UI_LABEL ?>" for="c-file">Ảnh</label>
          <input class="<?= UI_INPUT_FILE ?>" id="c-file" type="file">
        </div>
        <div class="<?= UI_INPUT_GROUP ?>">
          <span class="<?= UI_INPUT_GROUP_ADDON ?>">Tìm</span>
          <input class="<?= UI_INPUT_GROUP_CONTROL ?>" placeholder="Tên laptop..." aria-label="Tìm kiếm">
          <span class="<?= UI_INPUT_GROUP_ADDON ?>"><kbd class="<?= UI_KBD ?>">Enter</kbd></span>
        </div>
        <label class="<?= UI_FIELD_ROW ?>"><input type="checkbox" class="<?= UI_CHECKBOX ?>" checked> <span class="<?= UI_LABEL ?>">Còn hàng</span></label>
        <label class="<?= UI_FIELD_ROW ?>"><input type="radio" name="r" class="<?= UI_RADIO ?>" checked> <span class="<?= UI_LABEL ?>">Giá tăng dần</span></label>
        <label class="<?= UI_FIELD_ROW ?>"><input type="checkbox" class="<?= UI_SWITCH ?>" checked> <span class="<?= UI_LABEL ?>">Hiển thị banner</span></label>
      </div>
    </form>
  </section>

  <section class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>"><div class="<?= UI_CARD_TITLE ?>">Tương tác</div><div class="<?= UI_CARD_DESC ?>">Tabs, accordion, dropdown, dialog</div></div>
    <div class="<?= UI_CARD_CONTENT ?>">
      <div class="<?= UI_TABS_LIST ?>"><a href="?tab=info" class="<?= ui_tab(true) ?>">Mô tả</a><a href="?tab=specs" class="<?= ui_tab() ?>">Thông số</a></div>
      <div>
        <details class="<?= UI_ACCORDION_ITEM ?>"><summary class="<?= UI_ACCORDION_TRIGGER ?>">Bảo hành bao lâu?</summary><div class="<?= UI_ACCORDION_CONTENT ?>">12 tháng.</div></details>
        <details class="<?= UI_ACCORDION_ITEM ?>"><summary class="<?= UI_ACCORDION_TRIGGER ?>">Giao hàng thế nào?</summary><div class="<?= UI_ACCORDION_CONTENT ?>">Giao tận nơi.</div></details>
      </div>
      <div class="flex gap-2">
        <details class="<?= UI_DROPDOWN ?>">
          <summary class="<?= ui_btn('outline') ?>">Menu</summary>
          <div class="<?= UI_DROPDOWN_CONTENT ?> left-0 right-auto">
            <div class="<?= UI_DROPDOWN_LABEL ?>">Hành động</div>
            <a href="#" class="<?= UI_DROPDOWN_ITEM ?>">Sửa</a>
            <div class="<?= UI_DROPDOWN_SEPARATOR ?>"></div>
            <button type="button" class="<?= UI_DROPDOWN_ITEM ?> text-destructive">Xoá</button>
          </div>
        </details>
        <button type="button" class="<?= ui_btn() ?>" onclick="document.getElementById('demo-dialog').showModal()">Mở dialog</button>
      </div>
      <dialog id="demo-dialog" class="<?= UI_DIALOG ?>">
        <div class="<?= UI_DIALOG_HEADER ?>"><div class="<?= UI_DIALOG_TITLE ?>">Xác nhận xoá</div><div class="<?= UI_DIALOG_DESC ?>">Thao tác này không hoàn tác được.</div></div>
        <form method="dialog" class="<?= UI_DIALOG_FOOTER ?>"><button class="<?= ui_btn('outline') ?>">Huỷ</button><button class="<?= ui_btn('destructive') ?>">Xoá</button></form>
      </dialog>
    </div>
  </section>

  <section class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>"><div class="<?= UI_CARD_TITLE ?>">Hiển thị</div><div class="<?= UI_CARD_DESC ?>">Avatar, separator, skeleton, progress, spinner, empty</div></div>
    <div class="<?= UI_CARD_CONTENT ?>">
      <div class="flex items-center gap-3"><span class="<?= UI_AVATAR ?>"><span class="<?= UI_AVATAR_FALLBACK ?>">HB</span></span><?= ui_spinner() ?></div>
      <hr class="<?= UI_SEPARATOR ?>">
      <div class="<?= UI_SKELETON ?> h-6 w-2/3"></div>
      <?= ui_progress(60) ?>
      <div class="<?= UI_EMPTY ?>"><div class="<?= UI_EMPTY_TITLE ?>">Chưa có dữ liệu</div><div class="<?= UI_EMPTY_DESC ?>">Thêm mục đầu tiên.</div></div>
    </div>
  </section>

  <section class="<?= UI_CARD ?>">
    <div class="<?= UI_CARD_HEADER ?>"><div class="<?= UI_CARD_TITLE ?>">Bảng, phân trang</div><div class="<?= UI_CARD_DESC ?>">UI_TABLE..., ui_pagination()</div></div>
    <div class="<?= UI_CARD_CONTENT ?>">
      <div class="<?= UI_TABLE_WRAP ?>">
        <table class="<?= UI_TABLE ?>">
          <thead><tr class="<?= UI_TR ?>"><th class="<?= UI_TH ?>">Tên</th><th class="<?= UI_TH ?>">Giá</th></tr></thead>
          <tbody><tr class="<?= UI_TR ?>"><td class="<?= UI_TD ?>">Laptop A</td><td class="<?= UI_TD ?>">15.000.000đ</td></tr></tbody>
          <tfoot class="<?= UI_TFOOT ?>"><tr><td class="<?= UI_TD ?>">Tổng</td><td class="<?= UI_TD ?>">15.000.000đ</td></tr></tfoot>
        </table>
      </div>
      <?= ui_pagination((int)($_GET['page'] ?? 1), 8) ?>
    </div>
  </section>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
