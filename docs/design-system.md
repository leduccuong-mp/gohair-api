# GoHair — Design system (rút từ mockup)

Dùng bảng màu và font này cho theme Filament (admin). Mockup admin nằm trong `docs/mockups/Admin*.dc.html`.

## Màu

| Token | Hex | Dùng cho |
| --- | --- | --- |
| primary | #0E7C66 | Nút chính, trạng thái chọn, link |
| primaryDark | #0B5E4E | Nền card nổi bật (live, số dư), chữ trên nền tint |
| primaryTint | #E3F2EE | Nền chip/thẻ đang chọn, badge "Thợ đã nhận" |
| primarySoft | #9CCFC2 | Cột biểu đồ phụ |
| ink | #14161A | Chữ chính, nền nav admin |
| text2 | #3A404B | Chữ thân phụ |
| muted | #5B6270 | Chữ phụ, nhãn |
| subtle | #6B7280 | Chữ rất phụ, placeholder |
| border | #E4E6EA | Viền mảnh, đường kẻ |
| borderStrong | #D5D8DD | Viền ô nhập, nút viền |
| surface | #F4F5F7 | Nền màn hình, nền ô xám |
| white | #FFFFFF | Card |
| danger | #C62828 | Chấm live, SOS, badge đếm |
| dangerText | #B71C1C | Chữ lỗi/hủy |
| dangerTint | #FDECEC | Nền badge hủy/live |
| warningText | #8A4B00 | Chữ cảnh báo, "Chờ thợ nhận" |
| warningTint | #FFF4DB | Nền cảnh báo |
| infoText | #1F4FA8 | "Đang đến" |
| infoTint | #E1EAFB | Nền badge info |
| disputeText | #5B3FA0 | Khiếu nại |
| disputeTint | #EDE7F6 | Nền khiếu nại |
| star | #E8A317 | Sao đánh giá |
| liveAccent | #FFD166 | Icon tia sét trên nền tối |

## Chữ

Font: **Be Vietnam Pro** (Google Fonts, hỗ trợ đầy đủ tiếng Việt) — weight 400, 500, 600, 700. Mobile dùng `@expo-google-fonts/be-vietnam-pro`.

| Kiểu | Cỡ / weight |
| --- | --- |
| Tiêu đề màn hình | 22–24 / 700 |
| Tiêu đề section | 15–17 / 700 |
| Thân | 14–15 / 400–500 |
| Nhãn, phụ | 12–13 / 400–600 |
| Số tiền nổi bật | 18–30 / 700 |

## Khoảng cách & bo góc

- Lưới 4px; padding màn hình 16–20; khoảng giữa section 12–22.
- Bo góc: 8 (ô nhỏ), 10–12 (nút, ô nhập), 14–16 (card), 18 (card nổi bật), 24 (bottom sheet).
- Chiều cao: nút chính 52, nút phụ 44–48, ô nhập 46–52, tab bar 78.

## Component dùng chung (mobile)

Button (primary / outline / danger / ghost), Chip (toggle, aria-pressed), Card, ListRow, Avatar (chữ cái đầu + nền pastel), RatingStars, PriceText (định dạng `120.000đ`), StatusBadge (map từ trạng thái booking sang cặp màu ở trên), Switch, SegmentedControl, BottomSheet, MapPlaceholder → MapView, TabBar khách (Trang chủ, Lịch hẹn, Tin nhắn, Tài khoản), TabBar thợ (Hôm nay, Lịch làm việc, Thu nhập, Hồ sơ).

## Map trạng thái → badge

| Trạng thái | Nhãn | Màu |
| --- | --- | --- |
| awaiting_barber | Chờ thợ nhận | warning |
| accepted | Thợ đã nhận | primaryTint / primaryDark |
| on_the_way | Đang đến | info |
| in_service | Đang cắt | info |
| completed, settled | Hoàn thành | surface / text2 |
| cancelled | Đã hủy | danger |
| disputed | Khiếu nại | dispute |

## Mockup → màn hình

| File mockup | Màn hình / route gợi ý |
| --- | --- |
| Main | `(auth)/login` — đăng nhập, chọn vai trò |
| Home | `(customer)/index` |
| Filter | `(customer)/filter` (modal) |
| BarberProfile | `(customer)/barbers/[id]` |
| Booking | `(customer)/book/[barberId]` |
| Address | `(customer)/address` |
| Payment | `(customer)/checkout/[bookingId]` |
| LiveSearch | `(customer)/live` |
| Tracking | `(customer)/bookings/[id]/track` |
| Review | `(customer)/bookings/[id]/review` |
| History | `(customer)/bookings` |
| BarberHome | `(barber)/index` |
| LiveRequest | `(barber)/live-request` (modal) |
| Schedule | `(barber)/schedule` |
| Services | `(barber)/profile` |
| Income | `(barber)/income` |
| Admin* | Trang Filament tương ứng (Dashboard, Booking, Thợ, Duyệt eKYC, Khách hàng, Phí & voucher, Rút tiền, Khiếu nại, Báo cáo, Đăng nhập) |
