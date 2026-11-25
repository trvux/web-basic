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
