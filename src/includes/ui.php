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
