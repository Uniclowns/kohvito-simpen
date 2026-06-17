# Konsumen & Kasir UI Fixes — Design

**Date:** 2026-06-04
**Branch:** feature/task2-cleanup
**Scope:** View/CSS polish + one one-line controller change. No DB, route, or query changes.

## 1. Cart badge → distinct-menu count

The cart badge currently sums portions (`array_sum(array_column($keranjang, 'jumlah'))`),
so 1 menu × 3 shows "3". Change it to count distinct menus (`count($keranjang)`)
everywhere the badge is rendered/updated. Line items keep showing their own quantity.

Touched:
- `resources/views/components/layouts/konsumen.blade.php` (desktop top-nav badge — Gambar 2)
- `resources/views/konsumen/beranda.blade.php`
- `resources/views/konsumen/keranjang.blade.php`
- `resources/views/konsumen/pesanan.blade.php`
- `resources/views/konsumen/lacak.blade.php`
- `app/Http/Controllers/KeranjangKonsumenController.php` (AJAX JSON `cartCount`)

`pembayaran.blade.php` uses `$orderItemCount` (placed-order items, not the cart) — left as-is.

## 2. Detail popup polish (Gambar 1)

Root cause: desktop modal panel is capped at `max-width: 580px` (`app.css` @media ≥768px),
too narrow for the partial's 2-column desktop layout → squished.

- Widen desktop modal to ~`880px` / `width: 92%`, refine radius/shadow.
- Add a real close (✕) button for desktop; keep the drag handle for the mobile sheet.
- Polish `detail-menu-content.blade.php`: image card, spacing, typography, chip rhythm.
- Mobile bottom-sheet behavior unchanged.

## 3. Cart "Edit" opens popup, not a page (Gambar 3)

- Extract `#menu-sheet` markup + `openMenuSheet/closeMenuSheet` JS from `beranda.blade.php`
  into a reusable `<x-menu-detail-sheet />` component (no behavior change for beranda).
- Include the component on the cart page; change the Edit `<a href=…detail>` to
  `<button onclick="openMenuSheet(id)">`. Saving keeps current add-to-cart behavior.

## 4. Kasir dashboard visual polish (Gambar 4)

Restyle only — every `$variable`, query, `data-count-up`, and Chart.js block stays intact.
Improve stat cards (depth + accent + icon chips), best-seller cards, chart card headers,
and the header greeting. Brand palette only (`#460001` / `#681F1F` / `#E52E2D`).

## Verification

`npm run build`, then dogfood `localhost:8000` (menu sheet on desktop + mobile, cart badge
count, cart Edit popup, kasir dashboard).
