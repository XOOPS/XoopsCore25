![XOOPS CMS](https://xoops.org/images/logoXoops4GithubRepository.png)
# Modern Theme — Customization Guide

A practical guide for **site owners and theme designers** who want to customize the Modern admin theme's appearance without touching the theme's core files.

> **Developers** extending the theme's PHP or JavaScript behaviour should refer to [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) instead.

## Table of Contents

- [The Golden Rule](#the-golden-rule)
- [CSS File Overview](#css-file-overview)
- [Where to Make Changes](#where-to-make-changes)
- [Using custom.css](#using-customcss)
- [Common Recipes](#common-recipes)
  - [Change the accent color](#1-change-the-accent-color)
  - [Change the sidebar width](#2-change-the-sidebar-width)
  - [Restyle admin tables](#3-restyle-admin-tables)
  - [Change form button appearance](#4-change-form-button-appearance)
  - [Adjust border radius](#5-adjust-border-radius-globally)
  - [Hide or restyle the breadcrumb](#6-hide-or-restyle-the-breadcrumb)
  - [Custom dark mode colors](#7-custom-dark-mode-colors)
  - [Style a specific module's admin page](#8-style-a-specific-modules-admin-page)
- [Surviving Theme Updates](#surviving-theme-updates)
- [Using the Built-in Customizer](#using-the-built-in-customizer)
- [Troubleshooting](#troubleshooting)

---

## The Golden Rule

> **Put your customizations in `css/custom.css`. Never edit `modern.css`, `xoops.css`, `dark.css`, or `fixes.css` directly.**

`custom.css` is:
- Loaded **last** — it wins over every other CSS rule without needing `!important`
- **Never shipped** in theme update packages — your changes are safe
- **Empty by default** — a clean slate with usage instructions

---

## CSS File Overview

The theme loads five CSS files in this exact order:

```text
1. modern.css   ← Theme layout (sidebar, header, cards) — replace wholesale on updates
2. xoops.css    ← XOOPS admin elements (tables, forms, tabs) — maintained by XOOPS team
3. dark.css     ← Dark mode color palette
4. fixes.css    ← !important rules to beat XOOPS core inline styles
5. custom.css   ← YOUR customizations ← loaded last, wins over everything
```

Because `custom.css` is last in the cascade, most of your rules will work **without** `!important`.

---

## Where to Make Changes

| I want to change... | Use |
|---|---|
| Accent/brand color | `custom.css` → override `--primary` |
| Any other visual tweak | `custom.css` |
| Dark mode colors | `css/dark.css` (this file IS part of the theme — see [Dark Mode](#7-custom-dark-mode-colors)) |
| Something XOOPS core keeps overriding | `custom.css` with `!important` on that one rule |
| Major structural changes | Fork the theme (outside scope of this guide) |

---

## Using custom.css

### File location

```text
modules/system/themes/modern/css/custom.css
```

### Basic structure

```css
/* ============================================================
   Site: My XOOPS Site
   Last changed: 2026-02-23
   ============================================================ */

/* --- Brand color ------------------------------------------ */
:root {
    --primary:       #e11d48;   /* Rose 600 */
    --primary-dark:  #be123c;   /* Rose 700 */
    --primary-light: #fb7185;   /* Rose 400 */
}

/* --- Table headers ---------------------------------------- */
table.outer thead th {
    background: #1e3a5f;
    color: #ffffff;
}
```

### How cascade works in your favour

Because `custom.css` is loaded after everything else, a simple rule like:

```css
table.outer thead th { background: #1e3a5f; }
```

will override the identical selector in `xoops.css` — **no `!important` needed**.

You only need `!important` when XOOPS core itself injects an inline `style=""` attribute on an element. That is rare. If you find you need it for everything, check that `custom.css` is actually loading (see [Troubleshooting](#troubleshooting)).

---

## Common Recipes

### 1. Change the accent color

The entire theme uses `--primary` as its accent. Change it once, everything updates.

```css
/* custom.css */
:root {
    --primary:       #7c3aed;   /* Violet 700 */
    --primary-dark:  #6d28d9;   /* Violet 800 — used for hover states */
    --primary-light: #8b5cf6;   /* Violet 500 — used in dark mode */
}
```

> **Tip:** The built-in Customizer panel (gear icon) offers 8 ready-made presets and applies them instantly as a live preview. Once you find a color you like, copy its hex values here to make the choice permanent and update-proof.

---

### 2. Change the sidebar width

```css
/* custom.css */
:root {
    --sidebar-width: 300px;   /* default: 260px */
}
```

No other rules needed — `modern-main`, `modern-footer`, and the logger all reference `--sidebar-width` automatically.

---

### 3. Restyle admin tables

`table.outer` is the XOOPS admin table class used on virtually every module admin page.

**Flat header style:**

```css
/* custom.css */
table.outer thead th {
    background: #1e40af;
    color: #ffffff;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
```

**Remove row striping (solid rows instead):**

```css
/* custom.css */
table.outer tbody tr.even,
table.outer tbody tr:nth-child(even),
table.outer tbody tr.odd,
table.outer tbody tr:nth-child(odd) {
    background: var(--bg-secondary);
}
```

**Add a left accent border on hover:**

```css
/* custom.css */
table.outer tbody tr:hover {
    background: var(--bg-tertiary);
    box-shadow: inset 3px 0 0 var(--primary);
}
```

**Tighter row padding:**

```css
/* custom.css */
table.outer tbody td {
    padding: 8px 12px;
}
```

---

### 4. Change form button appearance

```css
/* custom.css */

/* Rounded pill buttons */
input[type="submit"],
input[type="button"],
button.formButton {
    border-radius: 20px;
    padding: 6px 20px;
}

/* Outlined style instead of filled */
input[type="submit"],
input[type="button"] {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
}

input[type="submit"]:hover,
input[type="button"]:hover {
    background: var(--primary);
    color: white;
}
```

---

### 5. Adjust border radius globally

Three tokens control all rounding in the theme:

```css
/* custom.css */
:root {
    --radius-sm: 2px;    /* Small elements: badges, small buttons — default 4px */
    --radius:    6px;    /* Standard: cards, inputs, buttons — default 8px */
    --radius-lg: 8px;    /* Large containers, panels — default 12px */
}
```

Set all three to `0` for a sharp, flat look. Set `--radius-lg` to `16px` or more for a softer feel.

---

### 6. Hide or restyle the breadcrumb

**Hide it entirely:**

```css
/* custom.css */
#xo-breadcrumb { display: none; }
```

**Flat style without border:**

```css
/* custom.css */
#xo-breadcrumb {
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--border);
    border-radius: 0;
    padding: 8px 0;
    margin-bottom: 16px;
}
```

---

### 7. Custom dark mode colors

Dark mode overrides live in `dark.css` (part of the theme, not `custom.css`). However, you can safely override dark mode colors in `custom.css` too — it is loaded after `dark.css`, so it wins:

```css
/* custom.css */

/* Darker page background */
body.dark-mode {
    --bg-primary: #050a14;
    --bg-secondary: #0f1a2e;
}

/* Custom dark table headers */
body.dark-mode table.outer thead th {
    background: #1a2744;
}
```

---

### 8. Style a specific module's admin page

XOOPS sets the `<body id="">` to the module's `dirname`. Use it to scope rules to a single module:

```css
/* custom.css */

/* Only affects the Publisher module admin */
#publisher table.outer thead th {
    background: linear-gradient(to right, #1e3a5f, #2563eb);
    color: white;
}

/* Only affects the News module admin */
#news .CPbigTitle {
    color: var(--primary);
    font-size: 22px;
}
```

This is the safest way to customize one module without affecting others.

---

## Surviving Theme Updates

When a new version of the Modern theme is released:

1. **Replace** `modern.css`, `xoops.css`, `dark.css`, `fixes.css` with the new versions.
2. **Do not touch** `custom.css` — it is excluded from the update package.
3. **Clear** the XOOPS Smarty cache (System Admin → Maintenance → Clear Cache).

Your customizations in `custom.css` are unaffected.

> **First install note:** `custom.css` is shipped as an empty placeholder on first installation. If you accidentally delete it, create a new empty file at `css/custom.css` — the theme handles a missing file gracefully (it only loads the file if it exists) but you will lose the placeholder with its usage instructions.

---

## Using the Built-in Customizer

The gear/settings icon in the admin panel opens the **Customizer panel** — a live preview tool that lets you:

- Switch between 8 color presets instantly
- Adjust font size (12–18px)
- Toggle compact sidebar, compact view, animations
- Show/hide dashboard sections (KPIs, charts, widgets, system info)
- Choose which modules appear in the content chart

**Customizer settings are saved in browser cookies** — they apply only to the current browser and are not shared across devices or users.

To make a Customizer choice permanent and shared with all admins, copy its value into `custom.css`:

| Customizer action | Equivalent `custom.css` rule |
|---|---|
| Select "Green" preset | `:root { --primary: #10b981; --primary-dark: #059669; --primary-light: #34d399; }` |
| Enable compact view | `body { /* handled by JS body class, not directly overridable via CSS alone */ }` |
| Wider sidebar | `:root { --sidebar-width: 300px; }` |

---

## Troubleshooting

### My `custom.css` rule is not working

1. **Check the file exists** at `modules/system/themes/modern/css/custom.css`
2. **Check it loaded** — open browser DevTools → Network tab → look for `custom.css` in the request list
3. **Check for typos** — a CSS parse error earlier in the file silently stops all subsequent rules
4. **Inspect the element** — in DevTools → Elements/Inspector → find the element → Styles pane → see which rule is winning and where it comes from
5. **Is it an inline style?** — If DevTools shows `style="..."` on the element itself (not from a stylesheet), it is a XOOPS core inline style. Add `!important` to your rule in `custom.css`

### My rule needs `!important` for everything

`custom.css` is loaded last in the normal cascade — you should rarely need `!important`. If you find you need it constantly, the file may not be loading. Check the Network tab in DevTools to confirm it appears as a loaded resource.

### Changes take effect only in some browsers

You are likely seeing a cached version. Hard-refresh (`Ctrl+Shift+R` / `Cmd+Shift+R`) or clear browser cache. The theme does not add cache-busting query strings to CSS URLs.

### I want to revert all my customizations

Empty the content of `custom.css` (leave the file in place — don't delete it). Everything reverts to the theme defaults.

---

## Reference: Design Tokens

All of these are defined in `modern.css :root` and can be overridden in `custom.css`:

| Token | Default | Controls |
|---|---|---|
| `--primary` | `#2563eb` | Accent color (tabs, links, buttons, active states) |
| `--primary-dark` | `#1e40af` | Hover/active states |
| `--primary-light` | `#3b82f6` | Dark mode accent |
| `--success` | `#10b981` | Success messages, KPI positive badges |
| `--warning` | `#f59e0b` | Warning messages, KPI warning badges |
| `--danger` | `#ef4444` | Error messages, required field markers |
| `--info` | `#06b6d4` | Info messages |
| `--bg-primary` | `#f8fafc` | Page background |
| `--bg-secondary` | `#ffffff` | Card / sidebar / header background |
| `--bg-tertiary` | `#f1f5f9` | Hover states, alternate table rows |
| `--text-primary` | `#0f172a` | Main text |
| `--text-secondary` | `#475569` | Subdued text, labels |
| `--text-tertiary` | `#94a3b8` | Placeholders, nav section titles |
| `--border` | `#e2e8f0` | Standard borders |
| `--border-light` | `#f1f5f9` | Subtle separators |
| `--radius-sm` | `4px` | Small elements (badges, small buttons) |
| `--radius` | `8px` | Cards, inputs, standard buttons |
| `--radius-lg` | `12px` | Large panels, containers |
| `--sidebar-width` | `260px` | Sidebar width |
| `--header-height` | `64px` | Fixed header height |
| `--shadow-sm` | `0 1px 2px …` | Subtle shadow (header) |
| `--shadow` | `0 1px 3px …` | Card shadow |
| `--shadow-md` | `0 4px 6px …` | Medium shadow |
| `--shadow-lg` | `0 10px 15px …` | Hover card shadow |
