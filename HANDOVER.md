# Handover checklist — content needed from Lucky Udaan Foundation

Everything below can be entered by the client from the dashboard (`/admin`) — no code changes needed.
Where something is missing, the website already shows a clean fallback (e.g. "Details will be published soon").

## Must have before launch

| Item | Where to enter it | Currently |
|---|---|---|
| **Logo** (high-resolution PNG/SVG, ideally transparent) | Settings → General → Logo | Cropped from the supplied poster (low resolution) |
| **Bank details** (bank, account name, account no., IFSC, branch) | Settings → Donation & legal | Empty — Donate page asks visitors to call |
| **UPI ID and UPI QR code** | Settings → Donation & legal | Empty |
| **CIN, PAN, 12A, 80G, CSR-1, NGO Darpan numbers** | Settings → Donation & legal, and Content → Certifications | Empty ("to be updated") |
| **Certificate scans / PDFs** (incorporation, 12A, 80G) | Content → Certifications | Not uploaded |
| **Impact counter figures** (homepage) | Settings → Homepage | ⚠ Placeholder numbers (5,000+ / 120+ / 300+ / 14) — replace with real, verifiable figures |
| **Team members** — names, designations, photos, short bios | Website → Team | Only "Pawan Kumar — Director" (designation to confirm) |
| **Website domain** | `.env` → `APP_URL` | The posters show both `luckyudaanfoundation.com` and `luckyudaanfoundatin.com` (typo). The site uses **luckyudaanfoundation.com** — please confirm |
| **Admin passwords** | My profile | Default `LuckyUdaan@2026` — change for all three accounts |
| **SMTP email settings** | `.env` (`MAIL_*`) | Logs only — needed for admin password resets and form notifications |

## Replace sample content

These were written as realistic examples so the site looks complete. Edit or delete them:

- **Testimonials** (3) — Content → Testimonials. Replace with real quotes (with permission).
- **Success stories** (2) — Content → Success Stories.
- **Campaigns** (4) — goals, dates and copy. Totals update automatically when donations are confirmed.
- **Events** (5) — dates and venues are illustrative.
- **Blog / news / announcements / press release** (8 posts).
- **Job & internship openings** (3) — Content → Jobs & Internships.
- **Homepage banners** (3) — upload large landscape photos (1600px+ wide) for best quality.
- **Gallery** (8 photos) — currently crops from the supplied posters; real event photos will look sharper.
- **Policies** (Privacy, Terms, Refund) — generic drafts; have them reviewed by the organisation's legal adviser.

## Nice to have

- **Founder / leadership photos.** Two portrait photos were supplied without names; they are saved in `client-assets/portrait-1.webp` and `portrait-2.webp`. Tell us who they are, or upload them under Website → Team.
- **Social media links** (Facebook, Instagram, YouTube, X, LinkedIn) — Settings → Social links. Icons appear automatically.
- **Year founded** — Settings → General.
- **Exact map location / coordinates** — Settings → Contact. The map uses an address search for Ambedkar Chauraha, Civil Lines, Gonda; the latitude/longitude used for local SEO are approximate.
- **Partner / supporter logos** — Content → Partners (section hidden until at least one is added).
- **Annual reports / audited financials (PDF)** — Content → Annual Reports.
- **Brand colours** — The posters use navy blue; the site uses the requested green + gold. Change instantly in Settings → Brand colours (e.g. primary `#1a3f8f`, dark `#0d2257`, accent `#f2b400`).
- **Google Search Console code & Google Analytics ID** — Settings → SEO & analytics.
- **Default social share image** (1200×630) — Settings → SEO & analytics (currently made from a poster).
