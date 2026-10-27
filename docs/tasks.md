# gohair-api — Danh sách task

Làm theo thứ tự. Tick `[x]` khi test xanh và đã commit. Cột "Mở khóa cho mobile" cho biết task mobile nào có thể bắt đầu sau khi xong.

## M0 — Khởi tạo

- [x] Tạo Laravel (PostgreSQL, Redis), cài Sanctum, Pest, Scramble, Reverb, Horizon, Filament; `.env.example`; migration bật PostGIS
- [x] Docker Compose cho PostgreSQL+PostGIS và Redis để chạy local
- [x] Cấu trúc `app/Modules/*` + module mẫu `Health` (`GET /api/v1/health`) có test
- [x] Chuẩn hóa lỗi JSON, phân trang, CORS; Scramble phục vụ `/docs/api.json` → *mở khóa: mobile sinh API client*
- [x] GitHub Actions: chạy Pest với PostgreSQL+Redis service

## M1 — Tài khoản & hồ sơ

- [ ] OTP qua SMS (driver `log` ở local), Google/Apple, `GET/PATCH /me`, role customer/barber → *mobile: Đăng nhập*
- [ ] Sổ địa chỉ khách (geography point) → *mobile: Địa chỉ*
- [ ] Hồ sơ thợ, dịch vụ & giá, presigned upload S3/R2 cho portfolio → *mobile: Hồ sơ thợ*
- [ ] eKYC: upload CCCD + chân dung vào bucket riêng, `kyc_status`

## M2 — Tìm thợ

- [ ] `GET /barbers` theo bán kính (ST_DWithin + GiST), tên, giá, sao, dịch vụ, sắp xếp; seeder dữ liệu mẫu ở TP.HCM → *mobile: Trang chủ, Bộ lọc*
- [ ] `GET /barbers/{id}`, reviews → *mobile: Hồ sơ thợ (khách xem)*

## M3 — Lịch & đặt hẹn giờ

- [ ] Lịch làm việc tuần + ngày nghỉ + thời gian đệm
- [ ] `SlotCalculator` (spec mục 6) + test biên
- [ ] `BookingStateMachine` + `booking_status_logs` + test mọi chuyển trạng thái
- [ ] `POST /bookings`: snapshot giá, Redis lock slot 10 phút, 409 khi trùng → *mobile: Đặt lịch*

## M4 — Tiền

- [ ] `FeeRuleResolver` + test
- [ ] Voucher: điều kiện, giới hạn lượt, `funded_by` + test
- [ ] `PaymentGateway` + VNPay sandbox: tạo URL, IPN idempotent + test → *mobile: Thanh toán*
- [ ] Hủy & hoàn tiền theo chính sách + test
- [ ] Ví thợ (sổ cái), job quyết toán sau 24 giờ + test
- [ ] Rút tiền: yêu cầu + kiểm tra tự động → *mobile: Thu nhập*

## M5 — Thực hiện đơn

- [ ] Endpoint thợ accept/decline/start-trip/arrive/complete → *mobile: Hôm nay (thợ)*
- [ ] Reverb kênh `private-booking.{id}`, `private-barber.{id}`; `POST /barber/location` → *mobile: Theo dõi thợ*
- [ ] FCM push theo mốc trạng thái; `POST /devices`
- [ ] Đánh giá + cập nhật rating cache → *mobile: Đánh giá*
- [ ] Khiếu nại: tạo, trao đổi, tạm giữ tiền

## M6 — Admin (Filament) — theo `docs/mockups/Admin*`

- [ ] Đăng nhập admin + 2FA + audit log (AdminLogin)
- [ ] Tổng quan doanh thu (AdminDashboard)
- [ ] Booking: danh sách, chi tiết, hoàn tiền thủ công (AdminBookings, AdminBookingDetail)
- [ ] Thợ: danh sách, duyệt eKYC, khóa/mở khóa (AdminBarbers, AdminBarberReview)
- [ ] Khách hàng (AdminCustomers)
- [ ] Phí & voucher (AdminFees)
- [ ] Duyệt rút tiền (AdminWithdrawals)
- [ ] Khiếu nại (AdminComplaints)
- [ ] Báo cáo tháng/quý + xuất Excel (AdminReports)

## M7 — Triển khai

- [ ] Deploy VPS (Forge hoặc Coolify), HTTPS, Reverb sau Nginx, Horizon bằng Supervisor
- [ ] Backup DB hằng ngày, Sentry, uptime monitor
- [ ] Môi trường staging cho mobile test

## Sau pilot

- [ ] Matching đơn live theo đợt + mở rộng bán kính
- [ ] MoMo, ZaloPay
- [ ] Chat trong app, ẩn SĐT
