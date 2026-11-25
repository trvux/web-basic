<?php
// Icon kiểu lucide (shadcn dùng bộ này). Dùng: echo ui_icon('users');
function ui_icon($name, $class = 'size-4 shrink-0') {
    static $paths = [
        'dashboard'  => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
        'package'    => '<path d="M21 8 12 3 3 8v8l9 5 9-5Z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/>',
        'folder'     => '<path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>',
        'cart'       => '<circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M2 3h3l2.7 12h11.5L22 7H6"/>',
        'users'      => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0"/><path d="M16 4a4 4 0 0 1 0 8"/><path d="M18 14a7 7 0 0 1 4 7"/>',
        'image'      => '<rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>',
        'file'       => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h8"/>',
        'components' => '<path d="m12 3 4 4-4 4-4-4Z"/><path d="m12 13 4 4-4 4-4-4Z"/><path d="m7 8 4 4-4 4-4-4Z"/><path d="m17 8 4 4-4 4-4-4Z"/>',
        'store'      => '<path d="M3 9 5 3h14l2 6"/><path d="M4 9v12h16V9"/><path d="M3 9h18"/><path d="M10 21v-6h4v6"/>',
        'logout'     => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
        'panel'      => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/>',
    ];
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $class . '" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}
