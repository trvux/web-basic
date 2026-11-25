## Làm gì
<!-- 1-2 câu. Một PR = một việc nhỏ. Liên kết issue: Closes #12 -->

## Đã làm trước khi tạo PR
- [ ] Đã chạy `./scripts/sync-branch.sh` (kéo main mới nhất vào nhánh), không còn conflict. **Làm lại bước này nếu có PR khác vừa được merge**
- [ ] Chạy thử trên http://localhost:8088 và bấm thật các chức năng đã làm
- [ ] Chỉ sửa file trong thư mục của mình (CI sẽ báo đỏ nếu sai). Cần sửa file chung (`header.php`, `ui.php`, `index.php`, `db/init.sql`) thì đã báo leader
- [ ] Giao diện dùng bộ shadcn trong `ui.php` (`ui_btn()`, `UI_INPUT`, `UI_CARD`...), không màu Tailwind viết cứng
- [ ] Nếu đổi cấu trúc DB: đã tạo file mới trong `db/migrations/` (không sửa `init.sql`)
- [ ] Không commit mật khẩu, `.env`, mã số sinh viên
