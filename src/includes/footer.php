</main>
<?php if (!empty($isAdminArea)): ?>
  </div><!-- hết vùng nội dung admin -->
</div><!-- hết khung sidebar -->
<?php else: ?>
<footer class="mt-8 border-t bg-card">
  <div class="mx-auto grid w-full max-w-6xl gap-6 px-4 py-8 text-sm sm:grid-cols-3">
    <div class="grid content-start gap-2">
      <div class="text-base font-bold text-primary">LaptopShop</div>
      <p class="text-muted-foreground">Cửa hàng laptop văn phòng và gaming.</p>
    </div>
    <div class="grid content-start gap-2">
      <div class="font-medium">Liên kết</div>
      <a href="/index.php" class="text-muted-foreground hover:text-foreground">Trang chủ</a>
      <a href="/products/index.php" class="text-muted-foreground hover:text-foreground">Sản phẩm</a>
      <a href="/posts/index.php" class="text-muted-foreground hover:text-foreground">Tin tức</a>
      <a href="/cart/index.php" class="text-muted-foreground hover:text-foreground">Giỏ hàng</a>
    </div>
    <div class="grid content-start gap-2">
      <div class="font-medium">Về website</div>
      <p class="text-muted-foreground">Đồ án môn Lập trình ứng dụng web, Khoa Công nghệ Thông tin, Trường Đại học Văn Lang. Không bán hàng thật.</p>
      <p class="text-muted-foreground">Dữ liệu sản phẩm tham khảo từ gearvn.com.</p>
    </div>
  </div>
  <div class="border-t py-3 text-center text-xs text-muted-foreground">© <?= date('Y') ?> LaptopShop</div>
</footer>
<?php endif; ?>
<?php require __DIR__ . '/ui_js.php'; ?>
</body>
</html>
