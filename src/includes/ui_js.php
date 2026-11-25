<script>
// JS thuần cho các component cần tương tác. footer.php tự nạp, không cần làm gì thêm.
(() => {
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

  // Mở / đóng dialog, sheet, drawer: data-open="id" mở, bấm ra nền ngoài thì đóng
  $$('[data-open]').forEach(b => b.addEventListener('click', () => document.getElementById(b.dataset.open)?.showModal()));
  $$('dialog:not([role="alertdialog"])').forEach(d => d.addEventListener('click', e => { if (e.target === d) d.close(); }));

  // Dropdown, popover, menubar: bấm ra ngoài hoặc nhấn Esc thì đóng
  const menus = () => $$('details[data-menu][open]');
  document.addEventListener('click', e => menus().forEach(d => { if (!d.contains(e.target)) d.open = false; }));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') menus().forEach(d => d.open = false); });

  // Carousel
  $$('[data-carousel]').forEach(c => {
    const track = c.querySelector('[data-track]');
    const go = step => {
      const w = track.clientWidth, n = track.children.length;
      const i = (Math.round(track.scrollLeft / w) + step + n) % n;
      track.scrollTo({ left: i * w, behavior: 'smooth' });
    };
    c.querySelector('[data-prev]')?.addEventListener('click', () => go(-1));
    c.querySelector('[data-next]')?.addEventListener('click', () => go(1));
    const auto = Number(c.dataset.auto);
    if (auto && track.children.length > 1) setInterval(() => { if (!c.matches(':hover') && !document.hidden) go(1); }, auto * 1000);
  });

  // Toast
  $$('[data-toast]').forEach(t => setTimeout(() => { t.style.opacity = 0; setTimeout(() => t.remove(), 300); }, 4000));

  // Command: lọc danh sách theo chữ gõ
  $$('[data-command]').forEach(box => box.querySelector('[data-command-input]').addEventListener('input', e => {
    const q = e.target.value.trim().toLowerCase();
    let shown = 0;
    $$('[data-command-item]', box).forEach(a => { const ok = a.textContent.toLowerCase().includes(q); a.classList.toggle('hidden', !ok); shown += ok; });
    box.querySelector('[data-command-empty]').classList.toggle('hidden', shown > 0);
  }));

  // Context menu
  const hideContext = () => $$('[data-context-menu]').forEach(m => m.classList.add('hidden'));
  document.addEventListener('click', hideContext);
  $$('[data-context]').forEach(area => area.addEventListener('contextmenu', e => {
    const m = document.getElementById(area.dataset.context);
    if (!m) return;
    e.preventDefault(); hideContext(); m.classList.remove('hidden');
    m.style.left = Math.min(e.clientX, innerWidth - m.offsetWidth - 8) + 'px';
    m.style.top = Math.min(e.clientY, innerHeight - m.offsetHeight - 8) + 'px';
  }));

  // Input OTP
  $$('[data-otp]').forEach(box => {
    const hidden = box.querySelector('input[type="hidden"]'), cells = $$('input[type="text"]', box);
    cells.forEach((cell, i) => {
      cell.addEventListener('input', () => { cell.value = cell.value.replace(/\D/g, ''); if (cell.value) cells[i + 1]?.focus(); hidden.value = cells.map(c => c.value).join(''); });
      cell.addEventListener('keydown', e => { if (e.key === 'Backspace' && !cell.value) cells[i - 1]?.focus(); });
    });
  });
})();
</script>
