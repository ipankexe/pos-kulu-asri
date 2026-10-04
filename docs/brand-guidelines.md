# Brand Guidelines v1.0 — Rumah Makan Kulu Asri

> **Official Visual Identity & Design System Standards**  
> *Tagline:* "Kulu Asri - Jagonya Ikan Bakar!"  
> *Last updated:* 2026-10-04  
> *Status:* Active / Approved  

---

## Executive Summary

**Rumah Makan Kulu Asri** is a premier Indonesian culinary dining brand renowned for its authentic grilled seafood (*Ikan Bakar khas pesisir & rempah Nusantara*), serene garden saung atmosphere (*Suasana Asri*), and genuine Indonesian warm hospitality.

The POS & Digital Ordering ecosystem unites:
1. **Admin Management & Executive Analytics** (Clarity, data precision, clean operational controls)
2. **Cashier / POS Touchscreen System** (High-speed, error-free cashier workflow, tactile touch ergonomics)
3. **Customer QR Self-Order Experience** (Appetizing visual showcase, seamless ordering, modern frictionless dining)

---

## Quick Reference

| Element | Value | Meaning / Rationale |
|---------|-------|---------------------|
| Primary Color | `#047857` | Heritage Botanical Emerald ("Asri" — nature, freshness, sanctuary) |
| Secondary Color | `#d97706` | Ember Amber / Honey Glaze ("Ikan Bakar" — grill fire, glaze, warmth) |
| Accent Color | `#ea580c` | Sambal Rempah Orange (Culinary appetite stimulant, high-priority CTAs) |
| Background Canvas | `#f8faf7` | Natural linen-sand neutral (Gentle on cashier eyes over long shifts) |
| Primary Font | Plus Jakarta Sans | Modern Indonesian geometric sans with supreme numeric legibility |
| Serif Display Accent | Playfair Display | Elegant hospitality serif for brand tagline and culinary prestige |
| Voice & Tone | Warm, Hospitable, Authentic, Swift & Reliable |

---

## 1. Color Palette

### Primary Colors

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| Primary Botanical | #047857 | rgb(4,120,87) | Primary brand identity, active tabs, main action CTAs |
| Primary Dark | #064e3b | rgb(6,78,59) | Sidebar backgrounds, dark contrast panels, active states |
| Primary Light | #10b981 | rgb(16,185,129) | Success badges, focus borders, active indicators |
| Primary Subtle | #ecfdf5 | rgb(236,253,245) | Soft chip fills, selection background, highlight alerts |

### Secondary Colors

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| Secondary Amber | #d97706 | rgb(217,119,6) | Price figures, key callouts, warm promotional banners |
| Secondary Dark | #b45309 | rgb(180,83,9) | Hover states on amber actions |
| Secondary Light | #f59e0b | rgb(245,158,11) | Warning badges, stars, ratings |
| Secondary Subtle | #fffbeb | rgb(255,251,235) | Subtle highlight backgrounds |

### Accent Colors

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| Accent Flame | #ea580c | rgb(234,88,12) | Urgent notifications, instant pay buttons, cart counter |
| Accent Dark | #c2410c | rgb(194,65,12) | Hover states for urgent buttons |
| Accent Light | #fb923c | rgb(251,146,60) | Secondary badges |
| Accent Subtle | #fff7ed | rgb(255,247,237) | Highlight containers |

### Neutral Palette

| Name | Hex | RGB | Usage |
|------|-----|-----|-------|
| Background | #f8faf7 | rgb(248,250,247) | App background, dashboard canvas |
| Surface Card | #ffffff | rgb(255,255,255) | Product cards, modal bodies, table containers |
| Text Primary | #0f172a | rgb(15,23,42) | Main titles, price figures, primary data tables (14:1 contrast) |
| Text Secondary | #475569 | rgb(71,85,105) | Subheadings, secondary descriptions (7.5:1 contrast) |
| Text Muted | #94a3b8 | rgb(148,163,184) | Form placeholders, subtle timestamps |
| Border | #e2e8f0 | rgb(226,232,240) | Cards, inputs, table divider lines |
| Border Subtle | #f1f5f9 | rgb(241,245,249) | Internal row dividers |

### Semantic Colors

| State | Hex | Background Hex | Usage |
|-------|-----|----------------|-------|
| Success | #059669 | #ecfdf5 | Completed orders, paid status, surplus indicators |
| Warning | #d97706 | #fffbeb | Low stock warning, pending table bills |
| Destructive | #dc2626 | #fef2f2 | Void items, cancel order, delete user |
| Info | #0284c7 | #f0f9ff | Order QR notifications, system updates |

---

## 2. Typography

### Font Families

```css
/* Display & UI Sans */
--font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

/* Hospitality Serif Accent */
--font-serif: 'Playfair Display', Georgia, serif;

/* Monospace for Receipts & Tokens */
--font-mono: 'JetBrains Mono', 'Fira Code', monospace;
```

### Type Scale

| Role | Font Family | Size | Weight | Line Height | Usage |
|------|-------------|------|--------|-------------|-------|
| Hero Display | Plus Jakarta Sans | 32px - 36px | 800 | 1.15 | Dashboard main greeting, POS welcome |
| Tagline Accent | Playfair Display | 15px - 18px | 700 italic | 1.3 | "Jagonya Ikan Bakar!" brand signoff |
| Page Heading (H1) | Plus Jakarta Sans | 24px - 28px | 700 | 1.25 | View headers, modal titles |
| Section Title (H2) | Plus Jakarta Sans | 18px - 20px | 700 | 1.3 | Card group titles, category headers |
| Subheading (H3) | Plus Jakarta Sans | 15px - 16px | 600 | 1.4 | Product names, widget labels |
| Body Main | Plus Jakarta Sans | 14px | 400 / 500 | 1.5 | General descriptions, form inputs |
| Metric / Monetary | Plus Jakarta Sans | 20px - 26px | 800 (tabular-nums) | 1.2 | Financial metrics, cart total, prices |
| Caption / Meta | Plus Jakarta Sans | 12px | 500 | 1.4 | Timestamps, table badges, SKUs |

---

## 3. Visual Identity & Sensory Cues

1. **Clean Architectural Spacing:** Generous whitespace, 16px–24px card padding, 12px gaps.
2. **Tactile Touch Targets:** All POS buttons and mobile action triggers have a minimum height of `48px`, providing immediate confidence to cashiers and customers.
3. **No Low-Quality Emojis:** System controls and navigation rely solely on crisp SVG vector iconography (Bootstrap Icons / Lucide) styled in brand tokens.
4. **Appetizing Food Presentation:** Product cards feature smooth curved corners (`16px`), subtle warm hover lift (`transform: translateY(-4px)`), and crisp image ratios.
5. **Glassmorphic App Bars:** Sticky headers utilize `backdrop-filter: blur(16px)` with `rgba(255,255,255,0.85)` for depth and elegance.
