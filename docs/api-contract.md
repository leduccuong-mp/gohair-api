# GoHair API contract (v1)

Thỏa thuận giữa `gohair-api` và `gohair-mobile`. Chi tiết từng endpoint lấy từ OpenAPI tự sinh tại `GET /docs/api.json`; file này ghi các quy ước chung và danh sách endpoint dự kiến.

## Quy ước

- Base URL: `{API_URL}/api/v1`. Local: `http://localhost:8000/api/v1` (Android emulator: `http://10.0.2.2:8000/api/v1`).
- Auth: header `Authorization: Bearer <sanctum_token>`. Token trả về sau khi xác thực OTP/Google/Apple.
- JSON snake_case. Thời gian ISO 8601 có múi giờ (`2026-10-27T14:00:00+07:00`). Tiền là số nguyên VND.
- Phân trang: `?page=&per_page=` → `{ data: [], meta: { current_page, last_page, total } }`.
- Lỗi: HTTP status + `{ "message": "...", "errors": { "field": ["..."] } }`. 401 = token hết hạn, 403 = không đủ quyền, 422 = validate, 409 = xung đột (slot đã bị đặt).
- Breaking change: thêm `/api/v2` hoặc giữ tương thích ngược; không đổi nghĩa field cũ.
- CORS: mở cho mọi origin trên `api/*`, `docs/api`, `docs/api.json` (app mobile dùng Bearer token, không dùng cookie nên không cần `supports_credentials`).

## Realtime (Reverb, giao thức Pusher)

- Host/port/key qua env: `REVERB_HOST`, `REVERB_PORT`, `REVERB_APP_KEY`. Auth kênh private: `POST /broadcasting/auth` với Bearer token.
- `private-booking.{id}`: `BookingStatusChanged { booking_id, from, to, at }`, `BarberLocationUpdated { lat, lng, eta_minutes }`.
- `private-barber.{id}`: `BookingRequested { booking_id, type, expires_at }` (đơn mới / đơn live).

## Endpoint dự kiến

| Nhóm | Method & path | Ai dùng |
| --- | --- | --- |
| Hạ tầng | `GET /health` — kiểm tra kết nối DB + Redis, trả `200` (`status: ok`) hoặc `503` (`status: down`) | Hạ tầng / uptime monitor |
| Auth | `POST /auth/otp/request`, `POST /auth/otp/verify`, `POST /auth/social`, `POST /auth/logout`, `GET /me`, `PATCH /me` | Cả hai |
| Địa chỉ | `GET/POST/PATCH/DELETE /addresses` | Khách |
| Tìm thợ | `GET /barbers?lat=&lng=&radius_km=&q=&min_rating=&price_min=&price_max=&service=&live_only=&sort=` | Khách |
| Hồ sơ thợ | `GET /barbers/{id}`, `GET /barbers/{id}/reviews`, `GET /barbers/{id}/slots?date=&service_ids[]=` | Khách |
| Đặt lịch | `POST /bookings`, `GET /bookings`, `GET /bookings/{id}`, `POST /bookings/{id}/cancel` | Khách |
| Thanh toán | `POST /bookings/{id}/payments` → `{ payment_url }`; `POST /payments/vnpay/ipn` (server-to-server) | Khách / cổng |
| Voucher | `POST /vouchers/validate` | Khách |
| Live | `POST /live-requests`, `DELETE /live-requests/{id}` | Khách |
| Đánh giá | `POST /bookings/{id}/review` | Khách |
| Khiếu nại | `POST /bookings/{id}/complaints`, `GET /complaints/{id}` | Khách / Thợ |
| Thợ — hồ sơ | `GET/PATCH /barber/profile`, `GET/POST/PATCH/DELETE /barber/services`, `POST /barber/media/presign`, `POST/DELETE /barber/media`, `POST /barber/kyc` | Thợ |
| Thợ — lịch | `GET/PUT /barber/availability`, `GET/POST/DELETE /barber/availability/exceptions` | Thợ |
| Thợ — live | `POST /barber/live/on`, `POST /barber/live/off`, `POST /barber/location` | Thợ |
| Thợ — đơn | `GET /barber/bookings?date=`, `POST /barber/bookings/{id}/{accept|decline|start-trip|arrive|complete}` | Thợ |
| Thợ — ví | `GET /barber/wallet`, `GET /barber/wallet/transactions`, `POST /barber/withdrawals` | Thợ |
| Thiết bị | `POST /devices` (FCM token) | Cả hai |

Admin không dùng API này; Filament truy cập trực tiếp model.
