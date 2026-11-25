<?php
// Bộ class Tailwind lấy từ shadcn/ui (style base-vega), dùng như "component" cho PHP.
// Cách dùng: in hàm ui_btn() hoặc hằng UI_INPUT vào thuộc tính class của thẻ HTML
// (dùng cú pháp in ra của PHP, xem các trang trong admin/products để thấy ví dụ).
// Muốn biết class nào làm gì -> xem docs Tailwind. Muốn thêm component -> chép class từ ui.shadcn.com.

// ---------- Button (giống cva trong button.tsx) ----------
function ui_btn($variant = 'default', $size = 'default') {
    $base = 'inline-flex shrink-0 items-center justify-center rounded-md border border-transparent text-sm font-medium whitespace-nowrap transition-all outline-none select-none cursor-pointer focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 active:translate-y-px disabled:pointer-events-none disabled:opacity-50';
    $variants = [
        'default'     => 'bg-primary text-primary-foreground hover:bg-primary/85',
        'outline'     => 'border-border bg-background shadow-xs hover:bg-muted hover:text-foreground',
        'secondary'   => 'bg-secondary text-secondary-foreground hover:bg-secondary/70',
        'ghost'       => 'hover:bg-muted hover:text-foreground',
        'destructive' => 'bg-destructive/10 text-destructive hover:bg-destructive/20',
        'link'        => 'text-primary underline-offset-4 hover:underline',
    ];
    $sizes = [
        'default' => 'h-9 gap-1.5 px-3',
        'sm'      => 'h-8 gap-1 px-2.5',
        'lg'      => 'h-10 gap-1.5 px-4',
    ];
    return "$base {$variants[$variant]} {$sizes[$size]}";
}

// ---------- Badge ----------
function ui_badge($variant = 'default') {
    $base = 'inline-flex h-5 w-fit shrink-0 items-center justify-center gap-1 rounded-4xl border border-transparent px-2 py-0.5 text-xs font-medium whitespace-nowrap';
    $variants = [
        'default'     => 'bg-primary text-primary-foreground',
        'secondary'   => 'bg-secondary text-secondary-foreground',
        'destructive' => 'bg-destructive/10 text-destructive',
        'outline'     => 'border-border text-foreground',
    ];
    return "$base {$variants[$variant]}";
}

// ---------- Form ----------
const UI_INPUT = 'h-9 w-full min-w-0 rounded-md border border-input bg-background px-2.5 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';
const UI_TEXTAREA = 'flex min-h-20 w-full rounded-md border border-input bg-background px-2.5 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 md:text-sm';
const UI_LABEL = 'flex items-center gap-2 text-sm leading-none font-medium select-none';
const UI_FIELD = 'grid gap-2';

// ---------- Card ----------
const UI_CARD = 'flex flex-col gap-4 overflow-hidden rounded-xl bg-card py-6 text-sm text-card-foreground shadow-xs ring-1 ring-foreground/10';
const UI_CARD_HEADER = 'grid auto-rows-min items-start gap-1 px-6';
const UI_CARD_TITLE = 'text-base leading-normal font-medium';
const UI_CARD_DESC = 'text-sm text-muted-foreground';
const UI_CARD_CONTENT = 'flex flex-col gap-3 px-6';

// ---------- Table ----------
const UI_TABLE_WRAP = 'overflow-x-auto rounded-xl bg-card ring-1 ring-foreground/10';
const UI_TABLE = 'w-full caption-bottom text-sm';
const UI_TR = 'border-b transition-colors last:border-0 hover:bg-muted/50';
const UI_TH = 'h-10 px-3 text-left align-middle font-medium whitespace-nowrap text-foreground';
const UI_TD = 'px-3 py-2.5 align-middle';

// ---------- Alert ----------
const UI_ALERT_ERROR = 'relative grid w-full gap-0.5 rounded-lg border border-destructive/30 bg-destructive/5 px-4 py-3 text-left text-sm text-destructive';

// ---------- Khác ----------
const UI_H1 = 'mb-5 text-2xl font-semibold tracking-tight';
const UI_PAGE = 'mx-auto w-full max-w-6xl px-4 py-6';

// ---------- Bổ sung cho các trang mới ----------
const UI_SELECT = 'h-9 w-full min-w-0 rounded-md border border-input bg-background px-2.5 py-1 text-base shadow-xs outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm';
const UI_ALERT_SUCCESS = 'relative grid w-full gap-0.5 rounded-lg border border-primary/30 bg-primary/5 px-4 py-3 text-left text-sm text-primary';
const UI_EMPTY = 'rounded-xl bg-card px-6 py-10 text-center text-sm text-muted-foreground ring-1 ring-foreground/10';

// Phân trang. $page = trang hiện tại (từ 1), $pages = tổng số trang,
// $params = các tham số lọc cần giữ trên URL, vd ['q' => 'asus', 'brand' => 'Dell'] (không cần truyền 'page').
// Dùng: echo ui_pagination($page, $pages, ["q" => $q]);
function ui_pagination($page, $pages, $params = []) {
    if ($pages <= 1) return '';
    $page = max(1, min($page, $pages));
    $link = function ($p, $label, $active = false, $disabled = false) use ($params) {
        $cls = $active ? ui_btn('default', 'sm') : ui_btn('outline', 'sm');
        if ($disabled) {
            return '<span class="' . $cls . ' pointer-events-none opacity-50">' . $label . '</span>';
        }
        $href = '?' . http_build_query($params + ['page' => $p]);
        return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" class="' . $cls . '">' . $label . '</a>';
    };
    $html = '<nav class="mt-6 flex flex-wrap items-center justify-center gap-1">';
    $html .= $link($page - 1, '‹ Trước', false, $page <= 1);
    for ($i = 1; $i <= $pages; $i++) {
        // luôn hiện trang đầu, cuối và 2 trang quanh trang hiện tại
        if ($i === 1 || $i === $pages || abs($i - $page) <= 2) {
            $html .= $link($i, (string)$i, $i === $page);
        } elseif (abs($i - $page) === 3) {
            $html .= '<span class="px-1 text-muted-foreground">…</span>';
        }
    }
    $html .= $link($page + 1, 'Sau ›', false, $page >= $pages);
    return $html . '</nav>';
}

// =====================================================================
// Các component còn lại, chuyển từ shadcn/ui style base-vega.
// React được thay bằng HTML thuần: dialog = thẻ dialog, dropdown và accordion = thẻ details.
// Xem ví dụ chạy được của TẤT CẢ component ở /admin/components.php (mở file đó để copy).
// =====================================================================

// ---------- Alert thường (lỗi: UI_ALERT_ERROR, thành công: UI_ALERT_SUCCESS) ----------
const UI_ALERT = 'relative grid w-full gap-0.5 rounded-lg border bg-card px-4 py-3 text-left text-sm text-card-foreground';
const UI_ALERT_TITLE = 'font-medium';
const UI_ALERT_DESC = 'text-sm text-balance text-muted-foreground';

// ---------- Phần phụ của Card, Table, Empty ----------
const UI_CARD_FOOTER = 'flex items-center gap-2 px-6';
const UI_TFOOT = 'border-t bg-muted/50 font-medium';
const UI_CAPTION = 'mt-4 text-sm text-muted-foreground';
const UI_EMPTY_TITLE = 'mb-1 text-lg font-medium tracking-tight text-foreground';
const UI_EMPTY_DESC = 'text-sm text-muted-foreground';

// ---------- Form nâng cao ----------
const UI_FIELD_SET = 'flex flex-col gap-6';
const UI_FIELD_LEGEND = 'mb-3 font-medium';
const UI_FIELD_GROUP = 'flex w-full flex-col gap-7';
const UI_FIELD_ROW = 'flex w-full flex-row items-center gap-3'; // checkbox/radio/switch + nhãn
const UI_FIELD_DESC = 'text-left text-sm leading-normal font-normal text-muted-foreground';
const UI_FIELD_ERROR = 'text-sm font-normal text-destructive';
const UI_INPUT_FILE = UI_INPUT . ' file:mr-2 file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground';

const UI_CHECKBOX = 'size-4 shrink-0 cursor-pointer rounded-[4px] border border-input accent-primary shadow-xs outline-none focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50';
const UI_RADIO = 'size-4 shrink-0 cursor-pointer accent-primary outline-none focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50';
// Switch: đặt class này trên input type="checkbox"
const UI_SWITCH = 'relative inline-flex h-[18px] w-8 shrink-0 cursor-pointer appearance-none items-center rounded-full bg-input shadow-xs transition-all outline-none checked:bg-primary focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 after:absolute after:left-px after:size-4 after:rounded-full after:bg-background after:transition-transform checked:after:translate-x-[14px]';

// Input group: khung bao input + chữ/nút phụ (vd ô tìm kiếm)
const UI_INPUT_GROUP = 'relative flex h-9 w-full min-w-0 items-center rounded-md border border-input bg-background shadow-xs has-[input:focus-visible]:border-ring has-[input:focus-visible]:ring-3 has-[input:focus-visible]:ring-ring/50';
const UI_INPUT_GROUP_CONTROL = 'h-full min-w-0 flex-1 border-0 bg-transparent px-2.5 text-sm outline-none placeholder:text-muted-foreground';
const UI_INPUT_GROUP_ADDON = 'flex items-center gap-2 px-2.5 text-sm font-medium text-muted-foreground select-none';

// Nhóm nút dính liền nhau
const UI_BUTTON_GROUP = 'inline-flex w-fit items-stretch [&>*:not(:first-child)]:rounded-l-none [&>*:not(:first-child)]:border-l-0 [&>*:not(:last-child)]:rounded-r-none';

// ---------- Avatar ----------
const UI_AVATAR = 'relative flex size-8 shrink-0 overflow-hidden rounded-full select-none';
const UI_AVATAR_IMG = 'aspect-square size-full rounded-full object-cover';
const UI_AVATAR_FALLBACK = 'flex size-full items-center justify-center rounded-full bg-muted text-sm text-muted-foreground';

// ---------- Breadcrumb ----------
// Dùng: echo ui_breadcrumb(['Trang chủ' => '/index.php', 'Sản phẩm' => '/products/index.php'], 'Tên trang hiện tại');
// Trang hiện tại truyền riêng để tiêu đề trùng nhãn cố định không đè mất link.
function ui_breadcrumb($items, $current = null) {
    $html = '<nav class="mb-4"><ol class="flex flex-wrap items-center gap-1.5 text-sm break-words text-muted-foreground sm:gap-2.5">';
    $list = [];
    foreach ($items as $label => $href) $list[] = [$label, $href];
    if ($current !== null) $list[] = [$current, null];
    $i = 0;
    foreach ($list as [$label, $href]) {
        if ($i++ > 0) $html .= '<li aria-hidden="true">›</li>';
        $text = htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8');
        $html .= '<li class="inline-flex items-center gap-1.5">' . ($href === null
            ? '<span class="font-normal text-foreground" aria-current="page">' . $text . '</span>'
            : '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" class="transition-colors hover:text-foreground">' . $text . '</a>') . '</li>';
    }
    return $html . '</ol></nav>';
}

// ---------- Tabs (mỗi tab là một link, vd ?tab=specs) ----------
const UI_TABS_LIST = 'inline-flex h-9 w-fit items-center justify-center rounded-lg bg-muted p-[3px] text-muted-foreground';
function ui_tab($active = false) {
    $base = 'inline-flex h-full flex-1 items-center justify-center gap-1.5 rounded-md border border-transparent px-3 py-1 text-sm font-medium whitespace-nowrap transition-all outline-none focus-visible:ring-3 focus-visible:ring-ring/50';
    return $base . ($active ? ' bg-background text-foreground shadow-sm' : ' text-foreground/60 hover:text-foreground');
}

// ---------- Accordion (details > summary + div) ----------
const UI_ACCORDION_ITEM = 'group border-b last:border-b-0';
const UI_ACCORDION_TRIGGER = 'flex cursor-pointer list-none items-center justify-between rounded-md py-4 text-left text-sm font-medium outline-none hover:underline focus-visible:ring-3 focus-visible:ring-ring/50 after:text-muted-foreground after:content-["+"] group-open:after:content-["−"]';
const UI_ACCORDION_CONTENT = 'pb-4 text-sm';

// ---------- Dropdown menu (details > summary + div) ----------
const UI_DROPDOWN = 'relative inline-block [&>summary]:list-none';
const UI_DROPDOWN_CONTENT = 'absolute right-0 z-50 mt-1 min-w-32 rounded-md bg-popover p-1 text-popover-foreground shadow-md ring-1 ring-foreground/10';
const UI_DROPDOWN_LABEL = 'px-2 py-1.5 text-xs font-medium text-muted-foreground';
const UI_DROPDOWN_ITEM = 'relative flex w-full cursor-pointer items-center gap-2 rounded-sm px-2 py-1.5 text-left text-sm whitespace-nowrap outline-none select-none hover:bg-accent hover:text-accent-foreground focus-visible:bg-accent';
const UI_DROPDOWN_SEPARATOR = '-mx-1 my-1 h-px bg-border';

// ---------- Dialog (thẻ dialog) ----------
// Mở: onclick="document.getElementById('ten-dialog').showModal()"   Đóng: nút trong form method="dialog"
const UI_DIALOG = 'fixed top-1/2 left-1/2 m-0 w-full max-w-[calc(100%-2rem)] -translate-x-1/2 -translate-y-1/2 rounded-xl bg-popover p-6 text-sm text-popover-foreground ring-1 ring-foreground/10 outline-none backdrop:bg-black/40 open:grid open:gap-6 sm:max-w-md';
const UI_DIALOG_HEADER = 'flex flex-col gap-2';
const UI_DIALOG_TITLE = 'text-base leading-none font-medium';
const UI_DIALOG_DESC = 'text-sm text-muted-foreground';
const UI_DIALOG_FOOTER = 'flex flex-col-reverse gap-2 sm:flex-row sm:justify-end';

// ---------- Linh tinh ----------
const UI_SEPARATOR = 'h-px w-full shrink-0 border-0 bg-border';
const UI_SKELETON = 'animate-pulse rounded-md bg-muted';
const UI_KBD = 'inline-flex h-5 w-fit min-w-5 items-center justify-center gap-1 rounded-sm bg-muted px-1 font-sans text-xs font-medium text-muted-foreground select-none';

function ui_progress($percent) {
    $p = max(0, min(100, (int)$percent));
    return '<div class="relative flex h-1.5 w-full overflow-hidden rounded-full bg-muted" role="progressbar" aria-valuenow="' . $p . '" aria-valuemin="0" aria-valuemax="100"><div class="h-full bg-primary transition-all" style="width:' . $p . '%"></div></div>';
}

function ui_spinner() {
    return '<span class="inline-block size-4 animate-spin rounded-full border-2 border-current border-t-transparent" role="status" aria-label="Đang tải"></span>';
}

require_once __DIR__ . '/ui_extra.php';
