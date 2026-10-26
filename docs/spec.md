# GoHair — Đặc tả nghiệp vụ (MVP)

Nguồn: tài liệu "Kế hoạch phát triển ứng dụng đặt thợ cắt tóc tại nhà" và bộ mockup GoHair. Nguồn chính của file này nằm ở repo `gohair-api` (docs/spec.md); repo `gohair-mobile` giữ bản sao. Phạm vi MVP cho 1 dev: một app mobile (chọn vai trò), đặt hẹn giờ, một cổng thanh toán (VNPay), admin Filament trong backend. Đặt live làm ngay sau pilot nhưng schema và máy trạng thái phải hỗ trợ từ đầu.

## 1. Vai trò

| Vai trò | Làm được gì |
| --- | --- |
| Khách hàng | Đăng nhập OTP/Google/Apple, quản lý địa chỉ, tìm thợ theo bán kính hoặc tên, xem hồ sơ thợ, đặt hẹn giờ, đặt live, thanh toán trước, theo dõi thợ, đánh giá, xem lịch hẹn, khiếu nại |
| Thợ | Đăng ký + eKYC (chờ admin duyệt), hồ sơ, dịch vụ & giá, portfolio ảnh/video, lịch làm việc theo tuần + ngày nghỉ, bật/tắt live, nhận/từ chối đơn, cập nhật trạng thái đơn, xem thu nhập, yêu cầu rút tiền |
| Admin | Tổng quan doanh thu, booking (danh sách, chi tiết, hoàn tiền), thợ (danh sách, duyệt eKYC, khóa), khách hàng, phí & voucher, duyệt rút tiền, khiếu nại, báo cáo tháng/quý |

## 2. Máy trạng thái booking

```
pending_payment → awaiting_barber → accepted → on_the_way → in_service → completed → settled
        │                │              │            │
        └────────────────┴──────────────┴────────────┴──→ cancelled (+ refund theo chính sách)
completed → disputed (khiếu nại) → completed | refunded
```

- `pending_payment`: slot bị khóa tạm 10 phút (Redis lock) trong lúc thanh toán; hết hạn thì giải phóng.
- `awaiting_barber`: hẹn giờ — gửi cho đúng thợ đã chọn, hạn nhận 30 phút. Live — gửi theo đợt cho 3 thợ đang live gần nhất trong 30 giây, sau 2 phút không ai nhận thì mở rộng bán kính lên 5 km; ai nhận trước được đơn.
- `completed → settled`: job chạy sau 24 giờ giữ tiền, cộng `barber_earning` vào ví (dòng `wallet_transactions` loại `earning`). Đơn đang `disputed` không được quyết toán.
- Mọi chuyển trạng thái: ghi `booking_status_logs (booking_id, from, to, actor_id, meta, created_at)` và phát event realtime cho khách/thợ.

## 3. Quy tắc tiền

- Tiền là số nguyên VND.
- `subtotal` = tổng giá dịch vụ (snapshot từ `services` lúc đặt).
- `discount` = giá trị voucher hợp lệ. `funded_by` của voucher: `app` | `barber` | `split`.
- `total` = `subtotal − discount` (số khách trả).
- `fee_rate` = quy tắc phí khớp cụ thể nhất (xem mục 4), snapshot vào booking.
- `fee_amount` = `round(subtotal × fee_rate)` — tính trên giá gốc trước voucher.
- `barber_earning` = `subtotal − fee_amount − phần voucher thợ chịu`.
- App thực thu một đơn = `fee_amount − phần voucher app chịu − phí cổng thanh toán`.
- Ví dụ (mockup): subtotal 210.000, voucher GOHAIR30 −30.000 (app chịu), total 180.000, phí 10% = 21.000, thợ nhận 189.000, phí cổng ~3.600 → app thực thu −12.600.

### Chính sách hủy

| Trường hợp | Hoàn cho khách |
| --- | --- |
| Khách hủy trước giờ hẹn > 2 giờ | 100% |
| Khách hủy trong vòng 2 giờ | 50–70% (cấu hình), phần còn lại bù cho thợ |
| Thợ hủy hoặc không đến | 100% + điểm phạt cho thợ |
| Không có thợ nhận (hết hạn) | 100%, tự động |

## 4. Phí linh động

Bảng `fee_rules (scope: global|barber|service|area, scope_id, booking_type: all|scheduled|live, rate, valid_from, valid_to, priority)`. Chọn quy tắc còn hiệu lực có độ cụ thể cao nhất: barber > service > area > global; trùng thì theo `priority`. Mặc định toàn hệ thống 10%.

## 5. Bảng dữ liệu chính

users, customer_addresses, barber_profiles (rating_avg, rating_count, is_live, current_location geography, service_radius_km, kyc_status), barber_kyc_documents, services, portfolio_media, availability_rules, availability_exceptions, bookings, booking_items, booking_status_logs, payments (gateway, gateway_txn_id, amount, status, raw_payload), refunds, fee_rules, vouchers, voucher_usages, wallet_transactions (type: earning|withdraw|adjust|penalty, amount, balance_after), withdrawal_requests, reviews, complaints, complaint_messages, admin_audit_logs.

Tìm thợ theo bán kính: `ST_DWithin(barber_profiles.current_location, :point, :radius_m)` với index GiST. Thợ live lưu vị trí tạm trong Redis GEO, ghi xuống DB định kỳ.

## 6. Tính slot trống (đặt hẹn giờ)

slot trống = khung giờ trong `availability_rules` của ngày đó − `availability_exceptions` − các booking chưa hủy (cộng thời lượng) − thời gian đệm di chuyển (mặc định 30 phút, thợ tự cấu hình). Bước slot 30 phút; slot phải chứa đủ tổng thời lượng các dịch vụ đã chọn. Chỉ cho đặt trước tối đa N ngày (thợ cấu hình, mặc định 14).

## 7. Thanh toán (VNPay trước)

- Tạo `payments` trạng thái `pending` → redirect/deeplink sang VNPay → nhận IPN (webhook) → kiểm tra chữ ký, số tiền, idempotent theo `gateway_txn_id` → chuyển booking sang `awaiting_barber`.
- Lưu nguyên `raw_payload`. Không tin kết quả trả về từ app; chỉ tin IPN.
- Bọc sau interface `PaymentGateway` để thêm MoMo/ZaloPay sau.

## 8. Rút tiền

Thợ gửi yêu cầu ≤ số dư khả dụng (không gồm tiền đang giữ). Hệ thống tự kiểm tra: đủ số dư, tên tài khoản khớp eKYC, không có khiếu nại đang mở. Admin chuyển khoản thủ công rồi bấm "Đã chuyển" → ghi `wallet_transactions` loại `withdraw`.

## 9. Bảo mật & dữ liệu cá nhân

Ẩn SĐT giữa khách và thợ (chat/gọi trong app — giai đoạn 2). Ảnh CCCD lưu bucket riêng, chỉ admin xem qua URL ký hạn ngắn. Xin đồng ý khi thu thập vị trí. Mọi thao tác admin ghi `admin_audit_logs`.
