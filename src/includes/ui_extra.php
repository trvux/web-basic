<?php
// Phần còn lại của registry shadcn/ui (style base-vega), chuyển sang HTML + CSS + JS thuần.
// File này được ui.php tự nạp. JS đi kèm nằm ở ui_js.php (footer.php tự nạp).
// Ví dụ chạy được: /admin/components.php
//
// Không cần hằng riêng:
//   direction  -> thuộc tính dir="rtl" của HTML
//   form       -> dùng UI_FIELD_GROUP, UI_FIELD, UI_FIELD_ERROR
//   label      -> UI_LABEL

function ui_attr($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ---------- Aspect ratio, Scroll area, Resizable ----------
const UI_ASPECT_VIDEO = 'relative aspect-video w-full overflow-hidden rounded-xl bg-muted';
const UI_ASPECT_SQUARE = 'relative aspect-square w-full overflow-hidden rounded-xl bg-muted';
const UI_SCROLL_AREA = 'relative overflow-auto overscroll-contain rounded-md border [scrollbar-width:thin]';
const UI_RESIZABLE = 'min-h-24 min-w-40 max-w-full resize overflow-auto rounded-lg border p-4';

// ---------- Collapsible (details > summary + div) ----------
const UI_COLLAPSIBLE = 'group flex flex-col gap-2';
const UI_COLLAPSIBLE_TRIGGER = 'flex w-fit cursor-pointer list-none items-center gap-2 text-sm font-medium';

// ---------- Popover (details > summary + div) ----------
const UI_POPOVER = 'relative inline-block [&>summary]:list-none';
const UI_POPOVER_CONTENT = 'absolute left-0 z-50 mt-1 flex w-72 flex-col gap-4 rounded-md bg-popover p-4 text-sm text-popover-foreground shadow-md ring-1 ring-foreground/10';

// ---------- Tooltip, Hover card (bọc ngoài bằng UI_HOVER_WRAP, hiện khi rê chuột hoặc focus) ----------
const UI_HOVER_WRAP = 'group/hover relative inline-flex';
const UI_TOOLTIP = 'pointer-events-none invisible absolute bottom-full left-1/2 z-50 mb-2 w-max max-w-xs -translate-x-1/2 rounded-md bg-foreground px-3 py-1.5 text-xs text-background opacity-0 transition-opacity group-hover/hover:visible group-hover/hover:opacity-100 group-focus-within/hover:visible group-focus-within/hover:opacity-100';
const UI_HOVER_CARD = 'invisible absolute top-full left-0 z-50 mt-2 w-64 rounded-lg bg-popover p-4 text-sm text-popover-foreground opacity-0 shadow-md ring-1 ring-foreground/10 transition-opacity group-hover/hover:visible group-hover/hover:opacity-100 group-focus-within/hover:visible group-focus-within/hover:opacity-100';

// ---------- Sheet (ngăn trượt cạnh), Drawer (ngăn trượt đáy), Alert dialog: đều là thẻ dialog ----------
const UI_SHEET_RIGHT = 'fixed inset-y-0 right-0 left-auto m-0 h-dvh max-h-dvh w-3/4 max-w-sm flex-col gap-4 overflow-y-auto border-l bg-popover p-4 text-sm text-popover-foreground shadow-lg backdrop:bg-black/40 open:flex';
const UI_SHEET_LEFT = 'fixed inset-y-0 left-0 right-auto m-0 h-dvh max-h-dvh w-3/4 max-w-sm flex-col gap-4 overflow-y-auto border-r bg-popover p-4 text-sm text-popover-foreground shadow-lg backdrop:bg-black/40 open:flex';
const UI_DRAWER = 'fixed inset-x-0 top-auto bottom-0 m-0 max-h-[80dvh] w-full max-w-none flex-col gap-4 overflow-y-auto rounded-t-xl border-t bg-popover p-4 text-sm text-popover-foreground backdrop:bg-black/40 open:flex';
const UI_SHEET_FOOTER = 'mt-auto flex flex-col gap-2';
// Alert dialog dùng chung UI_DIALOG, UI_DIALOG_TITLE, UI_DIALOG_FOOTER (thêm role="alertdialog")

// ---------- Toggle, Toggle group (input ẩn + nhãn) ----------
const UI_TOGGLE = 'inline-flex h-9 min-w-9 cursor-pointer items-center justify-center gap-1 rounded-md border border-input bg-background px-2.5 text-sm font-medium shadow-xs transition-colors select-none hover:bg-muted has-checked:bg-muted has-checked:text-foreground has-focus-visible:ring-3 has-focus-visible:ring-ring/50';
const UI_TOGGLE_INPUT = 'sr-only';
const UI_TOGGLE_GROUP = 'flex w-fit items-center [&>*:not(:first-child)]:rounded-l-none [&>*:not(:first-child)]:border-l-0 [&>*:not(:last-child)]:rounded-r-none';

// ---------- Slider (input type="range") ----------
const UI_SLIDER = 'h-1.5 w-full cursor-pointer accent-primary disabled:opacity-50';

// ---------- Item ----------
const UI_ITEM_GROUP = 'flex w-full flex-col gap-2';
const UI_ITEM = 'flex w-full flex-wrap items-center gap-3 rounded-md border px-4 py-3 text-sm transition-colors';
const UI_ITEM_MUTED = 'flex w-full flex-wrap items-center gap-3 rounded-md border border-transparent bg-muted/50 px-4 py-3 text-sm';
const UI_ITEM_CONTENT = 'flex min-w-0 flex-1 flex-col gap-1';
const UI_ITEM_TITLE = 'text-sm leading-snug font-medium';
const UI_ITEM_DESC = 'text-sm text-muted-foreground';
const UI_ITEM_ACTIONS = 'flex items-center gap-2';

// ---------- Menubar, Navigation menu (mỗi mục là một UI_DROPDOWN) ----------
const UI_MENUBAR = 'flex h-9 w-fit items-center gap-1 rounded-md border bg-background p-1 shadow-xs';
const UI_MENUBAR_TRIGGER = 'flex cursor-pointer list-none items-center rounded-sm px-2 py-1 text-sm font-medium select-none hover:bg-muted';
const UI_NAV_MENU = 'flex flex-wrap items-center gap-1';
const UI_NAV_LINK = 'inline-flex h-9 w-max cursor-pointer list-none items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors hover:bg-muted';

// ---------- Sidebar ----------
const UI_SIDEBAR_LAYOUT = 'flex flex-col gap-4 md:flex-row';
const UI_SIDEBAR = 'flex w-full shrink-0 flex-col gap-1 rounded-xl bg-card p-2 ring-1 ring-foreground/10 md:w-56';
const UI_SIDEBAR_LABEL = 'px-2 py-1.5 text-xs font-medium text-muted-foreground';
const UI_SIDEBAR_CONTENT = 'min-w-0 flex-1';
function ui_sidebar_link($active = false) {
    return 'flex h-8 w-full items-center gap-2 rounded-md px-2 text-sm hover:bg-accent ' . ($active ? 'bg-accent font-medium text-accent-foreground' : '');
}

// ---------- Chat: Message, Bubble, Marker, Attachment, Message scroller ----------
const UI_MESSAGE_SCROLLER = 'flex max-h-80 flex-col-reverse overflow-y-auto';
const UI_MESSAGE_LIST = 'flex flex-col gap-3 p-2';
const UI_MESSAGE = 'flex w-full min-w-0 gap-2 text-sm';
const UI_MESSAGE_END = 'flex w-full min-w-0 flex-row-reverse gap-2 text-sm';
const UI_BUBBLE = 'w-fit max-w-[80%] rounded-xl bg-muted px-3 py-2 break-words';
const UI_BUBBLE_PRIMARY = 'w-fit max-w-[80%] rounded-xl bg-primary px-3 py-2 break-words text-primary-foreground';
const UI_MARKER = 'flex w-full items-center gap-2 text-sm text-muted-foreground before:h-px before:flex-1 before:bg-border after:h-px after:flex-1 after:bg-border';
const UI_ATTACHMENT = 'relative flex w-fit max-w-full items-center gap-2.5 rounded-xl border bg-card px-3 py-2 text-sm text-card-foreground transition-colors hover:bg-muted/50';

// ---------- Carousel (JS: ui_js.php) ----------
// $slides = mảng các đoạn HTML đã escape sẵn. $auto = số giây tự chuyển (0 = tắt).
function ui_carousel($slides, $auto = 0) {
    if (!$slides) return '';
    $html = '<div class="relative w-full" data-carousel data-auto="' . (int)$auto . '">'
          . '<div class="flex snap-x snap-mandatory overflow-x-auto scroll-smooth rounded-xl [scrollbar-width:none]" data-track>';
    foreach ($slides as $s) $html .= '<div class="min-w-0 shrink-0 grow-0 basis-full snap-start">' . $s . '</div>';
    $html .= '</div>';
    if (count($slides) > 1) {
        $btn = ui_btn('outline', 'sm') . ' absolute! inset-y-0 my-auto rounded-full!';
        $html .= '<button type="button" class="' . $btn . ' left-2" data-prev aria-label="Trước">‹</button>'
               . '<button type="button" class="' . $btn . ' right-2" data-next aria-label="Sau">›</button>';
    }
    return $html . '</div>';
}

// ---------- Toast (shadcn sonner). In ra một lần ở trang, tự biến mất sau 4 giây ----------
function ui_toast($message, $type = 'default') {
    $color = $type === 'error' ? 'text-destructive' : 'text-popover-foreground';
    return '<div data-toast role="status" class="fixed right-4 bottom-4 z-50 max-w-sm rounded-lg border bg-popover px-4 py-3 text-sm shadow-lg transition-opacity duration-300 ' . $color . '">' . ui_attr($message) . '</div>';
}

// ---------- Combobox (ô nhập có gợi ý, dùng datalist của trình duyệt) ----------
function ui_combobox($name, $options, $value = '', $placeholder = '') {
    $id = 'dl-' . preg_replace('/[^a-z0-9_-]/i', '', $name);
    $html = '<input class="' . UI_INPUT . '" name="' . ui_attr($name) . '" list="' . $id . '" value="' . ui_attr($value) . '" placeholder="' . ui_attr($placeholder) . '" autocomplete="off">'
          . '<datalist id="' . $id . '">';
    foreach ($options as $o) $html .= '<option value="' . ui_attr($o) . '"></option>';
    return $html . '</datalist>';
}

// ---------- Command (danh sách lọc theo chữ gõ, JS: ui_js.php). $items = [nhãn => đường dẫn] ----------
function ui_command($items, $placeholder = 'Gõ để tìm...') {
    $html = '<div class="flex w-full flex-col overflow-hidden rounded-xl border bg-popover p-1 text-popover-foreground" data-command>'
          . '<input class="' . UI_INPUT . '" placeholder="' . ui_attr($placeholder) . '" aria-label="' . ui_attr($placeholder) . '" data-command-input>'
          . '<div class="mt-1 max-h-60 overflow-y-auto">';
    foreach ($items as $label => $href) $html .= '<a class="' . UI_DROPDOWN_ITEM . '" href="' . ui_attr($href) . '" data-command-item>' . ui_attr($label) . '</a>';
    return $html . '<div class="hidden py-6 text-center text-sm text-muted-foreground" data-command-empty>Không tìm thấy.</div></div></div>';
}

// ---------- Context menu (chuột phải). Đặt data-context="id-menu" lên vùng bấm ----------
const UI_CONTEXT_MENU = 'fixed z-50 hidden min-w-36 rounded-md bg-popover p-1 text-popover-foreground shadow-md ring-1 ring-foreground/10';

// ---------- Input OTP (JS tự nhảy sang ô kế) ----------
function ui_input_otp($name, $length = 6) {
    $html = '<div class="flex items-center" data-otp><input type="hidden" name="' . ui_attr($name) . '">';
    for ($i = 0; $i < $length; $i++) {
        $edge = ($i === 0 ? ' rounded-l-md border-l' : '') . ($i === $length - 1 ? ' rounded-r-md' : '');
        $html .= '<input class="size-9 border-y border-r border-input bg-background text-center text-sm shadow-xs outline-none focus:ring-3 focus:ring-ring/50' . $edge . '" type="text" inputmode="numeric" maxlength="1" aria-label="Số thứ ' . ($i + 1) . '">';
    }
    return $html . '</div>';
}

// ---------- Calendar (lịch một tháng). Chọn ngày trong form thì dùng input type="date" với UI_INPUT ----------
function ui_calendar($year = null, $month = null, $selected = null) {
    $year = (int)($year ?: date('Y'));
    $month = (int)($month ?: date('n'));
    $first = mktime(0, 0, 0, $month, 1, $year);
    $days = (int)date('t', $first);
    $offset = (int)date('N', $first) - 1; // tuần bắt đầu từ thứ Hai
    $html = '<div class="w-fit rounded-xl bg-card p-3 text-sm ring-1 ring-foreground/10"><div class="mb-2 text-center font-medium">Tháng ' . $month . '/' . $year . '</div><div class="grid grid-cols-7 gap-1 text-center">';
    foreach (['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'] as $d) $html .= '<div class="size-8 text-xs leading-8 text-muted-foreground">' . $d . '</div>';
    $html .= str_repeat('<div></div>', $offset);
    for ($d = 1; $d <= $days; $d++) {
        $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
        $cls = $date === $selected ? 'bg-primary text-primary-foreground' : ($date === date('Y-m-d') ? 'bg-accent text-accent-foreground' : '');
        $html .= '<div class="size-8 rounded-md leading-8 ' . $cls . '">' . $d . '</div>';
    }
    return $html . '</div></div>';
}

// ---------- Chart (biểu đồ cột). $data = [nhãn => số] ----------
function ui_bar_chart($data, $unit = '') {
    if (!$data) return '<div class="' . UI_EMPTY . '">Chưa có dữ liệu</div>';
    $max = max(1, max($data));
    $html = '<div class="flex h-48 w-full items-stretch gap-2">';
    foreach ($data as $label => $v) {
        $h = max(1, round($v / $max * 100));
        $text = number_format((float)$v) . $unit;
        $html .= '<div class="flex min-w-0 flex-1 flex-col items-center gap-1" title="' . ui_attr($label . ': ' . $text) . '">'
               . '<div class="flex w-full flex-1 items-end"><div class="w-full rounded-t-md bg-primary" style="height:' . $h . '%"></div></div>'
               . '<div class="w-full truncate text-center text-xs text-muted-foreground">' . ui_attr($label) . '</div></div>';
    }
    return $html . '</div>';
}
