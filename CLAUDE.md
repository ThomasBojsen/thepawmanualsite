# ThePawManual — Project Guide

> **This file is the living source of truth for the project.** Every Claude Code
> session should read it first and keep it updated. When a phase completes, when
> a decision is made, or when conventions change — edit this file.

---

## 1. Project Overview

**ThePawManual** (production domain: `thepawmanual.com`) is a hub of **free pet
calculators and tools** for dog and cat owners — later expanding to birds and
small pets. It is **monetized via display ads**, so it is a content + tools site,
not a store.

- The **calculators** are interactive tools whose math runs in the browser
  (vanilla JavaScript).
- The **site structure** (pages, templates, navigation, homepage) is WordPress.
- A WordPress-literate **SEO specialist** co-maintains the site, so everything
  must be standard, clean WordPress that a non-Claude developer can work with.
- The site must **scale**: many more calculators and programmatic SEO
  (auto-generated pages from a data source) are planned — but **not built yet**.

---

## 2. Firm Tech Decisions (do not revisit)

| Decision | Detail |
|---|---|
| Platform | **WordPress** (PHP + MySQL). **Not** Next.js, **not** a static site. |
| Production hosting | Simply.com shared hosting. |
| Local development | **LocalWP** (Local by Flywheel). |
| The design | The attached Aura export becomes a **custom WordPress theme**. |
| Calculator logic | **Vanilla JavaScript**, math runs in the browser. **Never** ported to PHP. |
| Calculator delivery | A **custom plugin** (`pawmanual-calculators`) — not baked into the theme. See §5. |
| Calculator architecture | A **reusable pattern**: one shared engine + one small config file per calculator. See §6. |
| SEO meta & schema | Managed by an SEO plugin (**RankMath**). The theme must **not** hardcode what the plugin should own. |
| Styling | **Tailwind CSS** for the theme, **compiled** to a static file. The plugin uses its own plain CSS. |

---

## 3. The Aura Design — Structure Summary  *(Step 1)*

The attached export (`design/aura-export.html`) is a **single homepage**.

### 3.1 What's in the export
- **Tailwind CSS via CDN** (`cdn.tailwindcss.com`) — fine for a mockup, not for production.
- **Fonts:** Inter (base sans), Playfair Display (display headings), Geist (UI text).
- **Lucide icons** — SVGs are already inline; a CDN script is also loaded.
- A custom `fadeSlideIn` animation + an IntersectionObserver scroll-reveal effect.
- An **"UnicornStudio" animated background** widget loaded from an external CDN.

### 3.2 Page sections (top → bottom)
1. **Animated background** — decorative full-width widget behind the top ~800px.
2. **Header** (sticky, blurred) — left nav (*Dog Tools, Cat Tools, Bird Tools,
   Small Pet Tools*), centered wordmark "ThePawManual" (Playfair), right side a
   *Search* button and a *Cart* button with a "2" badge.
3. **Hero** — giant "ThePawManual" headline; a large dog photo card with a
   *Find a Tool* button and category pills (*Age, Nutrition, Health, Puppies*);
   a "Featured Tool" card promoting the **Dog Age Calculator** with an
   *Open Calculator* button.
4. **Filter bar** — "Filter by" with *Category / Pet Size / Type: Dogs* buttons
   and a search input.
5. **Tools grid** — 6 cards: **Dog Age Calculator**, **Cat Age Calculator**,
   Puppy Weight Predictor, Food Portion Calculator, Pregnancy Due Date, Water
   Intake Calculator. Each card: icon, title, description, *Open Tool* button.
6. **Ad banner placeholder** — a dashed box (this is the ad monetization slot).
7. **Pet Life Gallery** — one large image + two small images, species filter
   pills, carousel arrows. Decorative/editorial.
8. **Statement band** (dark) — "Happy Pets, Happy Life", 4 colored image tiles,
   and a "Use code FUREVER" promo line.
9. **Secondary promo** — full-width image, "Nature's Best" organic wellness
   line, *Shop Now* / *Learn More* buttons.
10. **Footer** — 4 link columns (ThePawManual/contact, Social, Company, Terms &
    Policy) and a copyright line.
11. **Scripts** — Lucide init, a JS year-setter, a favourite-toggle demo.

### 3.3 Design system / visual language
- **Colors:** `neutral-50` background, `neutral-900` text, white cards,
  `neutral-200` borders; amber as an accent.
- **Shape:** large rounded corners (`rounded-2xl` / `rounded-3xl`), generous spacing.
- **Type:** Playfair Display for display headings, Geist for UI text, Inter as base.
- **Motion:** `fadeSlideIn` on load + on scroll into view.

### 3.4 Template leftovers to clean up
The export is a **generic e-commerce template** with pet content dropped in. It
carries store artifacts that don't fit a free-tools / ad site: the cart icon,
"Shop Now", the "FUREVER" discount code, the "Nature's Best" product line, a
duplicated footer copyright (one reads "Pawsome"), and a duplicated `id="y"`
(invalid HTML). See **§10** for the recommended fix for each.

---

## 4. Repository Structure & LocalWP Mapping  *(Step 2)*

### 4.1 Repository layout

The repo **mirrors WordPress's `wp-content` folder** so the mapping to LocalWP is
one-to-one and obvious.

```
thepawmanual/                              ← repo root (git root)
│
├── CLAUDE.md                              ← this file (source of truth)
├── README.md                             ← short human-facing readme
├── .gitignore
├── .gitattributes                        ← already present (LF normalization)
├── package.json                          ← Tailwind build tooling (dev only)
├── tailwind.config.js                    ← Tailwind config
│
├── design/
│   └── aura-export.html                  ← original Aura design, kept for reference
│
└── wp-content/                           ← mirrors WordPress's wp-content
    ├── themes/
    │   └── thepawmanual/                 ← THE THEME (symlinked into LocalWP)
    │       ├── style.css                 ← theme header (real CSS is compiled, see assets/)
    │       ├── functions.php             ← thin; just require()s the inc/ files
    │       ├── index.php                 ← generic fallback template
    │       ├── front-page.php            ← the homepage (Aura design)
    │       ├── page.php                  ← standard page template (used by calculator pages)
    │       ├── 404.php
    │       ├── header.php
    │       ├── footer.php
    │       ├── screenshot.png            ← theme thumbnail shown in WP admin
    │       │
    │       ├── inc/                      ← PHP partials, one concern per file
    │       │   ├── setup.php             ← add_theme_support(), image sizes
    │       │   ├── enqueue.php           ← register/enqueue CSS, JS, fonts
    │       │   ├── nav-menus.php         ← register menu locations
    │       │   └── tools-data.php        ← homepage tool-card list (simple PHP array)
    │       │
    │       ├── template-parts/
    │       │   ├── home/                 ← one file per homepage section
    │       │   │   ├── hero.php
    │       │   │   ├── filter-bar.php
    │       │   │   ├── tools-grid.php
    │       │   │   ├── gallery.php
    │       │   │   ├── statement.php
    │       │   │   └── promo.php
    │       │   ├── tool-card.php          ← one reusable card in the tools grid
    │       │   └── ad-slot.php            ← reusable ad container (empty by default)
    │       │
    │       ├── src/
    │       │   └── tailwind.css           ← Tailwind INPUT (directives + custom layers)
    │       │
    │       └── assets/
    │           ├── css/
    │           │   └── theme.css          ← Tailwind OUTPUT (compiled, committed)
    │           ├── js/
    │           │   └── theme.js           ← nav, scroll-reveal, small UI bits
    │           ├── fonts/                 ← self-hosted webfonts (see §10)
    │           └── images/
    │
    └── plugins/
        └── pawmanual-calculators/         ← THE PLUGIN (symlinked into LocalWP)
            ├── pawmanual-calculators.php  ← plugin header + bootstrap
            │
            ├── includes/
            │   ├── class-registry.php     ← list of available calculators
            │   ├── shortcode.php          ← [pawmanual_calculator id="…"]
            │   └── enqueue.php            ← conditional asset loading
            │
            ├── templates/
            │   └── calculator-shell.php   ← shared calculator markup (all calculators)
            │
            ├── assets/
            │   ├── css/
            │   │   └── calculators.css    ← shared, self-contained calculator styles
            │   └── js/
            │       └── calculator-core.js ← the shared engine (the reusable pattern)
            │
            └── calculators/
                ├── dog-age/
                │   └── dog-age.js         ← config + formula only
                └── cat-age/
                    └── cat-age.js         ← config + formula only
```

### 4.2 How the repo maps onto a LocalWP site

A LocalWP site stores its files at
`~/Local Sites/<site-name>/app/public/` — that is the WordPress root, and
`wp-content/` lives inside it.

We connect the repo to LocalWP with **two symlinks** — the theme folder and the
plugin folder. A symlink makes the LocalWP location *point at* the repo folder,
so **anything edited in the repo is instantly live in LocalWP — no copying.**

| Repo folder | Symlinked to (inside LocalWP) |
|---|---|
| `wp-content/themes/thepawmanual/` | `~/Local Sites/thepawmanual/app/public/wp-content/themes/thepawmanual/` |
| `wp-content/plugins/pawmanual-calculators/` | `~/Local Sites/thepawmanual/app/public/wp-content/plugins/pawmanual-calculators/` |

Only those two folders enter WordPress. The repo's `package.json`,
`node_modules/`, `design/`, `CLAUDE.md`, etc. stay **outside** WordPress entirely
— they are development tooling, not site files. The exact symlink commands are in
**§8, Step 3** (I run them for you).

### 4.3 Tailwind build

- **Input:** `wp-content/themes/thepawmanual/src/tailwind.css`
- **Output:** `wp-content/themes/thepawmanual/assets/css/theme.css` (minified)
- `tailwind.config.js` scans every theme `.php` file and `theme.js` for classes.
- npm scripts: `build:css` (one-off, minified) and `watch:css` (auto-rebuild on save).
- **I (Claude) run these for you.** During active work I can run the watcher.
- The compiled `theme.css` **is committed to git**, so the theme works on
  Simply.com shared hosting with **no build step** there (deploy = upload files).
- The **plugin does not use Tailwind.** Its `calculators.css` is hand-written and
  self-contained, so calculators look correct under *any* theme.

---

## 5. Calculators: Plugin vs Theme — Recommendation  *(Step 3)*

**Recommendation: build the calculators as a plugin** (`pawmanual-calculators`).
You leaned toward a plugin — I agree, and here is the reasoning so it's on record.

**Why a plugin:**
- **Survives a theme change / redesign.** The calculators are the *product*; the
  theme is the *wrapper*. If the site is re-themed later (very likely for an SEO
  project), the calculators keep working untouched.
- **Clean separation of concerns.** "Site chrome" (theme) and "tools" (plugin)
  can be reasoned about, debugged, and updated independently — helpful for the
  SEO specialist and future devs.
- **Scales to programmatic SEO.** The plugin's registry + shortcode pattern is
  exactly the mechanism we'll later use to mass-produce calculator pages.
- **Isolated activation.** A plugin can be deactivated to debug an issue without
  taking down the whole site's look.

**The tradeoff (cost of a plugin):**
- Two components to install and keep in sync instead of one.
- The plugin can't rely on the theme's Tailwind classes, so it ships its own
  CSS. We do this **deliberately** — it's what makes the plugin
  theme-independent, so it's a feature, not a cost.
- A small one-time indirection layer (registry + shortcode).

**The coupling rule (both directions stay loose):**
- The **theme works without the plugin** — homepage tool cards simply become
  links to pages.
- The **plugin works under any theme** — its CSS and JS are self-contained.

**What lives where:**
- **Theme:** header, footer, homepage, the homepage tool-grid *cards* (static
  promo cards that link to tool pages), and ad slots.
- **Plugin:** the actual interactive calculators — the markup shell, the engine,
  each calculator's config/formula, and the `[pawmanual_calculator]` shortcode.
- **WordPress Pages:** each calculator gets a real Page (e.g.
  `/dog-age-calculator/`) whose content holds the shortcode plus SEO intro copy
  the SEO specialist can edit.

---

## 6. The Reusable Calculator Pattern

This is the core architecture. We **establish** it with the Dog Age Calculator
and **prove** it with the Cat Age Calculator.

**Three shared pieces — written once, never duplicated:**
1. `templates/calculator-shell.php` — the identical markup wrapper for every
   calculator: a title slot, an empty `<form>` (fields are injected by JS),
   Calculate / Reset actions, an `aria-live` result region, a disclaimer, and a
   `<noscript>` fallback. It carries `data-calculator="<id>"`.
2. `assets/js/calculator-core.js` — **the engine.** It exposes
   `window.PawManual.register(config)`. On page load it finds every
   `.pm-calc[data-calculator]` element, looks up the registered config by id,
   renders the input fields from that config, wires the buttons, validates
   input, runs `config.calculate()`, and renders the result.
3. `assets/css/calculators.css` — styles the shell; plain CSS, visually matched
   to the theme's neutral/rounded look.

**One small piece per calculator:**
- `calculators/<id>/<id>.js` — a config object describing the calculator, ending
  with `PawManual.register(config)`. Illustrative shape:

```js
PawManual.register({
  id: 'dog-age',
  title: 'Dog Age Calculator',
  fields: [
    { name: 'age',  label: "Dog's age (years)", type: 'number',
      min: 0, max: 30, step: 0.5, required: true },
    { name: 'size', label: 'Breed size', type: 'select', required: true,
      options: [
        { value: 'small',  label: 'Small (0–9 kg)'   },
        { value: 'medium', label: 'Medium (10–22 kg)' },
        { value: 'large',  label: 'Large (23–40 kg)'  },
        { value: 'giant',  label: 'Giant (40+ kg)'    },
      ] },
  ],
  calculate(values) {
    // size-based veterinary lookup → returns a result object
    return { humanAge: /* … */, summary: /* … */ };
  },
});
```

**Why the Cat Age Calculator proves the pattern:** it needs only **one** field
(the cat's age) and a simpler formula. It must reuse the shell, the engine, and
the CSS with **zero changes** — only a new `cat-age.js`. If adding Cat Age
requires editing anything shared, the pattern has failed and must be fixed
*before* we build more calculators.

**Adding any future calculator** = (1) new `calculators/<id>/<id>.js`,
(2) register the id in `class-registry.php`, (3) create a WP Page with the
shortcode. Nothing else.

**Calculator math (finalized during the build):**
- **Dog Age:** a size-aware veterinary model (AKC-style). Roughly: year 1 ≈ 15
  human years, year 2 ≈ +9, then +4…+5 per year depending on size (small breeds
  age slower late in life, giant breeds faster). Inputs: age + size category.
- **Cat Age:** the standard feline model (year 1 = 15, year 2 = 24, +4 per year
  after). Cats have negligible size variance → a single input (age). This is
  why Cat Age is the ideal second calculator to prove the reusable pattern.
- Both show a short "general guidance, not veterinary advice" disclaimer.

---

## 7. Build Plan & Proof-of-Concept Scope  *(Step 4)*

### 7.1 POC — definition of done

The proof of concept is complete when, in LocalWP:
- The `thepawmanual` **theme** is active and renders the **homepage from the Aura
  design** (header, hero, filter bar, tools grid, ad slot, gallery, statement,
  promo, footer), responsive on mobile and desktop.
- The `pawmanual-calculators` **plugin** is active.
- A `/dog-age-calculator/` Page and a `/cat-age-calculator/` Page each render a
  **fully working calculator** end to end — enter values → Calculate → correct
  result; input validation; Reset.
- The **Cat Age Calculator was built purely as a new config file**, proving the
  reusable pattern from §6.
- The homepage tool cards for Dog Age and Cat Age link to those pages.
- Tailwind is **compiled to a static file** — no CDN — and there are no console errors.

**The POC explicitly excludes:** the other 4 calculators, programmatic SEO,
RankMath configuration, real ad code, written content, and production deploy.

### 7.2 Phases

- [x] **Phase 0 — Scaffolding.** Create the repo file tree, `.gitignore`,
      `package.json`, `tailwind.config.js`, the Tailwind input CSS, `README.md`;
      copy the Aura export to `design/aura-export.html`; run `npm install`.
- [x] **Phase 1 — Theme skeleton.** `style.css`, `functions.php`, `inc/*`,
      `header.php`, `footer.php`, `index.php`, `page.php`, `404.php`; theme
      supports (`title-tag`, `html5`, `post-thumbnails`, `custom-logo`, menus);
      asset enqueuing; menu registration; first Tailwind compile; placeholder
      `screenshot.png`.
- [x] **Phase 2 — Homepage.** Build `front-page.php` + `template-parts/home/*`
      from the Aura design; reusable `tool-card.php` and `ad-slot.php`; homepage
      tool list in `inc/tools-data.php`; apply the §10 design adaptations.
- [x] **Phase 3 — Plugin framework.** Plugin bootstrap + header;
      `class-registry.php`; `calculator-shell.php`; the `calculator-core.js`
      engine; `calculators.css`; the `[pawmanual_calculator]` shortcode;
      conditional asset enqueuing.
- [x] **Phase 4 — Dog Age Calculator.** `dog-age.js` config + formula; register
      it; verify end to end.
- [x] **Phase 5 — Cat Age Calculator.** `cat-age.js` config + formula **only**
      (no changes to any shared file); verify the pattern held.
- [x] **Phase 6 — Wire-up & test.** Create the two WP Pages with shortcodes,
      link the homepage cards, full LocalWP test pass, responsive check.

**After the POC (NOT now):** the remaining calculators, programmatic-SEO page
generation from a data source, RankMath setup, ad integration, font self-hosting
finalization, and production deploy to Simply.com.

---

## 8. LocalWP Setup & Testing Guide  *(Step 5)*

**Legend:** **[You]** = you click in LocalWP / the WordPress admin.
**[Claude]** = I do it through tools — you don't write any code or run commands.

**Step 1 — [You] Create the LocalWP site.**
LocalWP is already installed (you have a `hampenlocal` site). In LocalWP, click
**"+"** (Create a new site) → name it **`thepawmanual`** → choose the
**"Preferred"** environment → set an admin **username and password** you'll
remember → **Create site**. This creates the site at
`~/Local Sites/thepawmanual/` with a local address `http://thepawmanual.local`.

**Step 2 — [You] Confirm the site folder.**
In LocalWP, right-click the site → **"Reveal in Finder"**. Confirm the path is
`~/Local Sites/thepawmanual/app/public/` (that is the WordPress root; `wp-content`
is inside it). **Tell me the path** — especially if you named the site something
other than `thepawmanual`, since that changes the symlink commands.

**Step 3 — [Claude] Link the theme & plugin into LocalWP.**
Once you confirm the path, I create the two symlinks so the repo's theme and
plugin appear inside LocalWP's `wp-content`:
```
ln -s "/Users/thomashjaltebojsen/Desktop/Git/thepawmanual/wp-content/themes/thepawmanual" \
      "/Users/thomashjaltebojsen/Local Sites/thepawmanual/app/public/wp-content/themes/thepawmanual"

ln -s "/Users/thomashjaltebojsen/Desktop/Git/thepawmanual/wp-content/plugins/pawmanual-calculators" \
      "/Users/thomashjaltebojsen/Local Sites/thepawmanual/app/public/wp-content/plugins/pawmanual-calculators"
```
After this, every change I make in the repo is instantly live in LocalWP.

**Step 4 — [Claude] Build the CSS.**
I run `npm install` then `npm run build:css` so the compiled `theme.css` exists.

**Step 5 — [You] Activate the theme.**
In LocalWP, select the site → **"WP Admin"**. In WordPress:
**Appearance → Themes → "ThePawManual" → Activate**.

**Step 6 — [You] Activate the plugin.**
WordPress admin → **Plugins → "PawManual Calculators" → Activate**.

**Step 7 — [You] Set clean URLs.**
WordPress admin → **Settings → Permalinks → select "Post name" → Save**.
(Clean URLs matter for SEO.)

**Step 8 — [You] Create the calculator pages.**
> **Superseded by the content workstream (§13).** The calculator pages now live
> under a `/tools/` parent — `/tools/dog-age-calculator/`, not the flat
> `/dog-age-calculator/` this step originally created — and they arrive via the
> WXR import (§13) with their SEO copy already written, rather than being typed
> by hand. If you have the old flat pages in LocalWP from the POC, move them
> under the `Tools` parent (Page Attributes → Parent) or delete them before
> importing, so you don't end up with two copies.

The shortcodes are unchanged: **`[pawmanual_calculator id="dog-age"]`** and
**`[pawmanual_calculator id="cat-age"]`**, and they ship inside the imported
page content.

**Step 9 — [You] Set the homepage.**
**Pages → Add New** → title **"Home"** → **Publish** (leave the content empty;
`front-page.php` supplies the homepage). Then **Settings → Reading → "Your
homepage displays" → "A static page" → Homepage = "Home" → Save**.

**Step 10 — Testing.**
- **[You]** Open `http://thepawmanual.local` and check the homepage renders.
- **[You]** Open a calculator page, enter values, click Calculate, confirm the
  result; try empty/invalid input; try Reset.
- After I change files, **refresh the browser** — use a hard refresh
  (**Cmd+Shift+R**) after CSS/JS changes.
- I can verify code correctness, **but I cannot see your browser.** Tell me what
  you see, or paste a screenshot, and I'll fix from there.

**Optional / later:** install **RankMath** (Plugins → Add New) when SEO work
begins; enable `WP_DEBUG` via LocalWP for development.

---

## 9. Conventions for Future Sessions

- The tech decisions in **§2 are firm** — don't reopen them.
- **WordPress standards:** escape on output (`esc_html`, `esc_attr`, `esc_url`),
  sanitize on input, use text domains (`thepawmanual` for the theme,
  `pawmanual-calculators` for the plugin), prefix functions (`tpm_` theme /
  `pmc_` plugin), and omit the closing `?>` in PHP-only files.
- **Calculators are vanilla JS** — never port the math to PHP.
- **The reusable pattern is law:** a new calculator must not require editing
  `calculator-shell.php`, `calculator-core.js`, or `calculators.css`. If it
  seems to, fix the shared layer rather than duplicating it.
- **Styling:** the theme uses Tailwind — rebuild `theme.css` after changing
  classes (`npm run build:css`). The plugin uses plain CSS in `calculators.css`.
- **Assets:** enqueue everything via `wp_enqueue_*`; load calculator assets only
  on pages that actually use the shortcode.
- **SEO ownership:** RankMath owns `<title>`, meta description, Open Graph,
  canonical URLs, and breadcrumb/Article schema. The theme uses
  `add_theme_support('title-tag')` and calls `wp_head()`, `wp_body_open()`, and
  `wp_footer()` — it must **never** hardcode meta tags. The plugin *may* output
  calculator-specific JSON-LD (`WebApplication` / `HowTo` / `FAQ`), since that
  schema describes the tool, not the page.
- **Ad slots:** `ad-slot.php` stays an empty, labelled container — never hardcode
  an ad network's script; that gets added later or managed by a plugin.
- Keep `functions.php` thin — it just `require`s the `inc/` files. One concern
  per file.
- Write clean code with comments explaining the **why**, not the **what** — a
  WordPress-literate SEO specialist and future devs must be able to maintain it.
- **Accessibility:** real `<label>`s, `aria-live` on result regions, fully
  keyboard-operable controls.
- **Mobile-first:** most pet-owner traffic is mobile — always verify small screens.
- Keep the Aura reference in `design/`.
- **Update this file** — §7.2 phase checkboxes and §11 status — as work completes.

---

## 10. Design Adaptations — Decisions Needed

The Aura export is a generic e-commerce template with pet content dropped in.
Several pieces don't fit a free-tools, ad-monetized site. Here is my recommended
action for each — **please confirm or adjust when you approve this plan.**

| Element in the Aura design | Issue | Recommended action |
|---|---|---|
| Header cart icon + "2" badge | E-commerce; there's no store | **Remove** |
| Header "Search" button | Fine in principle | **Keep** — wire to WP search later (visual only for the POC) |
| "Statement" band — "Use code FUREVER" line | E-commerce promo | **Drop the promo line**, keep the visual band, rewrite the copy |
| "Secondary Promo" — "Nature's Best", "Shop Now" | E-commerce product push | **Repurpose** into a content CTA (e.g. "Browse all tools") or remove |
| "Pet Life Gallery" section | Decorative, slightly off-purpose | **Keep** for POC visual fidelity; candidate to become "Popular tools" later |
| Footer: duplicate copyright; one says "Pawsome" | Template leftover bug | **Fix** — a single correct ThePawManual copyright |
| Duplicate `id="y"` + JS year-setter | Invalid HTML | **Render the year in PHP** (`date('Y')`); drop the JS |
| UnicornStudio animated background | External CDN dependency, decorative | **Drop** (optionally replace with a lightweight CSS gradient) |
| Lucide icons CDN script | The SVGs are already inline | **Keep SVGs inline**, drop the CDN script |
| Tailwind via CDN | Not production-grade; hurts Core Web Vitals | **Replace** with a compiled static `theme.css` (already planned) |
| "Find a Tool" / category pills / filter bar | Good UI, but no logic in the export | **Keep visually**; real filtering is post-POC |
| Ad Banner Placeholder | This is the monetization | **Keep** — becomes the reusable `ad-slot.php` |

**Open questions — all resolved on approval (2026-05-21); the recommendations below were accepted:**
1. The e-commerce-flavoured sections (Statement band, Secondary Promo) — remove
   them entirely, or keep them visually and rewrite the copy to be
   tool/content-oriented? *(My recommendation: keep visually, rewrite copy.)*
2. Keep the Pet Life Gallery on the homepage for the POC? *(Recommendation: keep.)*
3. Confirm the **calculators-as-a-plugin** recommendation in §5. *(You leaned
   plugin; I agree.)*
4. Confirm the LocalWP site will be named **`thepawmanual`** — it determines the
   symlink paths in §8.

---

## 11. Current Status

- **Last updated:** 2026-07-28.
- **Phase:** **POC complete + content workstream landed.** The site is no longer a
  two-calculator proof of concept: 130 content pages are drafted, the import
  tooling exists, and the theme has a real internal-linking layer. The next
  milestone is **wave-1 launch** — see §13 and `docs/LAUNCH-RUNBOOK.md`.

**Done**
- **Phase 0 — Scaffolding** ✓ — `.gitignore`, `package.json`, `tailwind.config.js`,
  theme `src/tailwind.css`, `README.md`; Aura export copied to
  `design/aura-export.html`; `npm install` run.
- **Phase 1 — Theme skeleton** ✓ — `style.css`, thin `functions.php`,
  `inc/{setup,enqueue,nav-menus}.php`, `header.php`, `footer.php`, `index.php`,
  `page.php`, `404.php`, `assets/js/theme.js`; theme supports + asset enqueuing;
  `primary` nav menu registered; Tailwind compiled to `assets/css/theme.css`.
  (`screenshot.png`, the WP-admin thumbnail, is intentionally skipped — cosmetic.)
- **Phase 2 — Homepage** ✓ — `front-page.php` plus
  `template-parts/home/{hero,filter-bar,tools-grid,gallery,statement,promo}.php`,
  reusable `template-parts/{tool-card,ad-slot}.php`, `inc/tools-data.php`, and the
  `inc/icons.php` inline-SVG helper. Aura images downloaded, optimised to JPEG,
  and stored in `assets/images/`. The §10 design adaptations are applied.
- **Phase 3 — Plugin framework** ✓ — the `pawmanual-calculators` plugin:
  bootstrap + header, `PMC_Registry`, the `[pawmanual_calculator]` shortcode,
  conditional asset enqueuing, the shared `templates/calculator-shell.php`, the
  self-contained `assets/css/calculators.css`, and the `calculator-core.js`
  engine. The two calculator config files (`calculators/*/`) come in Phases 4–5.
- **Phase 4 — Dog Age Calculator** ✓ — `calculators/dog-age/dog-age.js`: a
  size-aware veterinary formula plus its fields, registered with the shared
  engine. Verified end-to-end on a live page.
- **Phase 5 — Cat Age Calculator** ✓ — `calculators/cat-age/cat-age.js`. Added
  as a single new config file with zero changes to the shared engine, shell,
  CSS, or registry — the reusable pattern (§6) is proven. Verified end-to-end.
- **Phase 6 — Wire-up & test** ✓ — both calculator Pages created with their
  shortcodes; the homepage cards link through to them; a static "Home" page is
  the front page. Responsive pass done — the header collapses to a hamburger
  menu below 1024px and the hero headline scales fluidly with `clamp()`. The
  POC is verified end-to-end in LocalWP.

**Environment**
- Build tooling: Node v24 / npm 11; **Tailwind CSS v3** — `npm run build:css`
  (one-off) / `npm run watch:css`. The compiled `theme.css` is committed.
- **LocalWP site:** created — display name "ThePawManual", folder
  `~/Local Sites/thepawmanual/`, served at **`http://localhost:10008`** (LocalWP
  *localhost* routing mode; no HTTPS — note this differs from the
  `thepawmanual.local` address §8 assumed).
- **Symlinks:** both the theme and the `pawmanual-calculators` plugin are
  symlinked into LocalWP's `wp-content/` → the repo.

**Repo**
- Single folder at `/Users/thomashjaltebojsen/Desktop/Git/thepawmanual/`
  (remote `github.com/ThomasBojsen/thepawmanualsite.git`, branch `main`).

Update §7.2 checkboxes and this section as each phase completes.

---

## 12. Deploying to simply.com (team preview)

**Goal:** put the site on **`thepawmanual.com`** (simply.com hosting, where a
WordPress install already exists) as a **password-protected, non-indexed**
preview for the team — without exposing a half-built site to Google.

**The SEO rule (non-negotiable):** the preview must never be indexable. Google
indexing a thin, unfinished site weakens the domain before the real launch. The
preview is locked behind a password **and** has search-engine indexing
discouraged. Indexing is switched on only at the real launch.

**Approach — upload the code, reproduce the setup.** The theme and plugin are
git-managed and symlinked into LocalWP, and the site content is tiny (three
pages). Deployment is therefore: (1) upload the theme + plugin folders from the
repo, (2) reproduce the WordPress configuration on simply.com. This avoids the
fragility of a database-migration tool against symlinked folders, and it
doubles as the repeatable update process.

**Procedure:**
1. **[Claude]** Compile the CSS; zip the theme and plugin from the repo
   (output: `~/Desktop/thepawmanual-deploy/`).
2. **[You]** simply.com WP admin → install the theme and plugin directly from
   their zips (Appearance → Themes → Add New → Upload Theme; Plugins → Add New
   → Upload Plugin) and activate both. WordPress unpacks theme/plugin zips
   natively — simply.com's File Manager cannot extract zips, so it is not used.
3. **[You]** simply.com WP admin → set the Site Title; set permalinks to
   "Post name".
4. **[You]** Create the Dog Age Calculator, Cat Age Calculator and Home pages
   (the calculator pages hold `[pawmanual_calculator id="…"]`); set Home as the
   static front page.
5. **[You]** Lock it down: install the "Password Protected" plugin and set a
   password; Settings → Reading → tick "Discourage search engines".
6. **[You]** Verify in a private browser window — the password gate appears and
   every page works once past it. Hand the URL + password to the team.

**Ongoing updates:** rebuild the CSS, re-zip, re-upload the theme/plugin folders
(overwriting), and add any new calculator pages on simply.com. All development
stays in LocalWP — never edit the live preview directly, or the next upload
overwrites it.

**At the real launch (later):** remove the password, untick "Discourage search
engines", confirm HTTPS/SSL is active, and submit the site to Google Search
Console. The step-by-step version of that day now lives in
`docs/LAUNCH-RUNBOOK.md` — follow it rather than this paragraph.

---

## 13. The Content Workstream *(added 2026-07-28)*

The site has a second half that §1–§12 predate: **the content**. It has its own
source of truth, deliberately separate from this file.

| Where | What it owns |
|---|---|
| `content/README.md` | The **editorial constitution** — the rules every page follows. Read before writing or editing any page. |
| `content/SITEMAP.md` | Page-by-page tracker: every URL, its status, its file, plus the backlog of unwritten pages. |
| `content/SEO-STRATEGY.md` | Launch checklist, GSC setup, the **wave publishing plan**, link building, ops calendar. |
| `docs/LAUNCH-RUNBOOK.md` | The executable version of the above — what to do, in order, on launch day. |

**`content/` mirrors the URL structure.** `content/dogs/food/puppy-food.md` →
`/dogs/food/puppy-food/`; a folder's `index.md` is that folder's hub page.
Frontmatter carries the RankMath fields. **127 of 133 pages are drafted** (126 of
them importable — the homepage is a theme template, not a WP page). The six
holdouts are Tools pages for calculators that don't exist yet, deliberately on
hold: a tool page without a working tool is thin content a fresh domain can't
afford.

### The import path — do not hand-paste pages

`tools/wxr/` generates a WordPress import file from the markdown:

```bash
node tools/wxr/generate.js --wave 1
```

Everything imports as **drafts**, with parent/child hierarchy derived from the
URL and RankMath title/description as postmeta. `tools/wxr/README.md` has the
WP-admin walkthrough. Three rules the tooling enforces, all of them load-bearing:

- **Waves, never a bulk publish.** Google's scaled-content policies flag
  single-shot dumps of templated pages on a fresh domain. `--wave N` exists so
  nobody is tempted.
- **`## Notes (not page copy)` sections never publish.** They hold fact-check
  flags and citation lists meant for the SEO specialist.
- **Visible FAQ text and its JSON-LD must stay identical.** Both ship; editing
  one without the other breaks the schema.

### What the theme now does for internal linking

`page.php` branches on whether a page has children. Hubs render an automatic
child-page grid; articles render a table of contents, sibling links, and a link
back up the hub. Breadcrumbs defer entirely to RankMath when it is active.

The important property: **all of it queries live WordPress state, not hardcoded
lists.** A page links only once its target is actually published, so partial
wave publishing never produces a dead link, and no template needs editing when
a wave ships. `tpm_get_published_page_url()` in `inc/content-nav.php` is the
helper that enforces it — use it for any new hardcoded internal link in a
template, including in the homepage copy (`template-parts/home/seo-content.php`).

### The two things that block launch and cannot be fixed in code

1. **The trust pages carry `[PLACEHOLDER]`s** — `content/about.md`,
   `content/contact.md`, `content/editorial-process.md` need real human names and
   a real domain email. Pet health is YMYL-adjacent; an anonymous site publishing
   it caps its own rankings. Never invent a name or imply veterinary review that
   doesn't happen.
2. **The fact-check pass hasn't run.** Each page's Notes section lists its own
   flags. Publishing unverified veterinary claims is the one mistake here that is
   genuinely expensive to undo.
