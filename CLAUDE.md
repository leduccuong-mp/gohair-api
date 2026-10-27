# gohair-api — hướng dẫn cho Claude Code

Backend của GoHair (marketplace đặt thợ cắt tóc đến tận nhà): REST API cho app mobile, realtime, job nền, và web admin (Filament). App mobile nằm ở repo riêng `gohair-mobile` và chỉ giao tiếp với repo này qua API `/api/v1` + kênh Reverb.

Ngôn ngữ trao đổi: tiếng Việt. Code, tên biến, commit message: tiếng Anh.

Đọc trước khi làm bất kỳ task nào:
- `docs/spec.md` — nghiệp vụ, trạng thái booking, quy tắc tiền, bảng dữ liệu. **Repo này là nguồn chính của spec**; sửa ở đây rồi copy sang `gohair-mobile/docs/spec.md`.
- `docs/api-contract.md` — quy ước API mà app mobile dựa vào. Đổi contract = breaking change, phải tăng version hoặc giữ tương thích.
- `docs/tasks.md` — thứ tự triển khai; luôn làm task đầu tiên chưa tick.
- `docs/design-system.md` — màu dùng cho theme Filament.
- `docs/mockups/Admin*.dc.html` — mockup 11 màn hình admin. Đọc để lấy bố cục, cột bảng, bộ lọc, hành động; dựng lại bằng Filament Resource/Page/Widget, không copy HTML.

## Stack

- Laravel bản mới nhất, PHP 8.5+, PostgreSQL + PostGIS, Redis.
- Auth API: Sanctum (token cho mobile). Admin: Filament, guard `admin` riêng, bật 2FA.
- Realtime: Laravel Reverb. Queue: Redis + Horizon. Lịch chạy: Scheduler.
- OpenAPI: Scramble, phục vụ tại `/docs/api.json` (mobile sinh client từ đây).
- Test: Pest. Lỗi: Sentry.

## Cấu trúc

```
app/
  Modules/
    Auth/ Barber/ Booking/ Payment/ Wallet/ Voucher/ Review/ Complaint/ Report/
      Models/ Actions/ Http/{Controllers,Requests,Resources}/ Events/ Policies/ Jobs/
  Filament/   # Resources, Pages, Widgets cho admin
routes/api.php        # chỉ prefix /api/v1, mỗi module một file route con
routes/channels.php   # kênh Reverb: private-booking.{id}, private-barber.{id}
```

## Quy tắc code

- Controller mỏng: FormRequest validate → Action (`handle()`) → API Resource. Không đặt nghiệp vụ trong controller/model.
- Tiền là số nguyên VND (`bigint`). Không dùng float/decimal để tính.
- Mọi thay đổi trạng thái booking đi qua `BookingStateMachine`, ghi `booking_status_logs`, phát event Reverb.
- Mọi biến động ví thợ là một dòng `wallet_transactions` (sổ cái). Không sửa trực tiếp số dư.
- Giá dịch vụ và `fee_rate` được snapshot vào booking lúc tạo.
- Webhook/IPN cổng thanh toán: xác thực chữ ký, idempotent theo `gateway_txn_id`, lưu `raw_payload`.
- Mọi thao tác admin ghi `admin_audit_logs`.
- Không đưa secret vào code; dùng `.env`, cập nhật `.env.example`.

## Quy tắc làm việc

1. Đọc spec + mockup liên quan, đưa kế hoạch ngắn (file tạo/sửa). Chờ xác nhận nếu task chạm tới tiền, trạng thái booking, schema hoặc API contract.
2. Logic tiền (phí, voucher, hoàn tiền, ví, IPN): viết test Pest trước.
3. Khi thêm/sửa endpoint: cập nhật `docs/api-contract.md` và kiểm tra `/docs/api.json` sinh đúng.
4. Xong task: `php artisan test` xanh → commit Conventional Commits (`feat(booking): ...`) → tick `docs/tasks.md`.
5. Không thêm package ngoài danh sách stack mà không hỏi.

## Lệnh

```bash
php artisan serve           # API tại http://localhost:8000
php artisan reverb:start    # WebSocket tại :8080
php artisan horizon
php artisan test
```
