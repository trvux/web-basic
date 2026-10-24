<?php
// Phần <head> dùng chung cho cả storefront và admin.
// Tailwind v4 chạy trực tiếp trên trình duyệt qua CDN (không cần build, cần có mạng).
// Theme lấy từ shadcn preset (font Inter, radius 0.625rem) + brand color ĐỎ (hue ~27).
// Muốn đổi màu thương hiệu: sửa các biến --primary, --link, --ring, --chart-*, --sidebar-primary bên dưới.
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
  @theme inline {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    --color-background: var(--background);
    --color-foreground: var(--foreground);
    --color-card: var(--card);
    --color-card-foreground: var(--card-foreground);
    --color-popover: var(--popover);
    --color-popover-foreground: var(--popover-foreground);
    --color-primary: var(--primary);
    --color-primary-foreground: var(--primary-foreground);
    --color-link: var(--link);
    --color-secondary: var(--secondary);
    --color-secondary-foreground: var(--secondary-foreground);
    --color-muted: var(--muted);
    --color-muted-foreground: var(--muted-foreground);
    --color-accent: var(--accent);
    --color-accent-foreground: var(--accent-foreground);
    --color-destructive: var(--destructive);
    --color-border: var(--border);
    --color-input: var(--input);
    --color-ring: var(--ring);
    --radius-sm: calc(var(--radius) * 0.6);
    --radius-md: calc(var(--radius) * 0.8);
    --radius-lg: var(--radius);
    --radius-xl: calc(var(--radius) * 1.4);
    --radius-2xl: calc(var(--radius) * 1.8);
    --radius-4xl: calc(var(--radius) * 2.6);
  }

  :root {
    --radius: 0.625rem;
    --background: oklch(1 0 0);
    --foreground: oklch(0.232 0.004 286.1);
    --card: oklch(1 0 0);
    --card-foreground: oklch(0.232 0.004 286.1);
    --popover: oklch(1 0 0);
    --popover-foreground: oklch(0.232 0.004 286.1);
    /* ---- Brand color: đỏ (kiểu CellphoneS / GearVN) ---- */
    --primary: oklch(0.53 0.225 27.3);
    --primary-foreground: oklch(1 0 0);
    --link: oklch(0.48 0.205 27.3);
    --ring: oklch(0.53 0.225 27.3);
    /* ---------------------------------------------------- */
    --secondary: oklch(0.971 0.003 286.4);
    --secondary-foreground: oklch(0.232 0.004 286.1);
    --muted: oklch(0.971 0.003 286.4);
    --muted-foreground: oklch(0.54 0.008 286.1);
    --accent: oklch(0.971 0.003 286.4);
    --accent-foreground: oklch(0.232 0.004 286.1);
    --destructive: oklch(0.577 0.245 27.325);
    --border: oklch(0.865 0.007 286.3);
    --input: oklch(0.865 0.007 286.3);
  }

  @layer base {
    * { @apply border-border outline-ring/50; }
    body { @apply bg-muted/40 text-foreground font-sans antialiased; }
  }
</style>
