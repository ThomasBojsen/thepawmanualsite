# ThePawManual: Project Guide

> **This file is the living source of truth for the project.** Every Claude Code
> session should read it first and keep it updated. When a phase completes, when
> a decision is made, or when conventions change: edit this file.

---

## 1. Project Overview

**ThePawManual** (production domain: `thepawmanual.com`) is a hub of **free pet
calculators and tools** for dog and cat owners, later expanding to birds and
small pets. It is **monetized via display ads**, so it is a content + tools site,
not a store.

- The **calculators** are interactive tools whose math runs in the browser
  (vanilla JavaScript).
- The **site structure** (pages, templates, navigation, homepage) is WordPress.
- A WordPress-literate **SEO specialist** co-maintains the site, so everything
  must be standard, clean WordPress that a non-Claude developer can work with.
- The site must **scale**: many more calculators and programmatic SEO
  (auto-generated pages from a data source) are planned, but **not built yet**.

---

## 2. Firm Tech Decisions (do not revisit)

| Decision | Detail |
|---|---|
| Platform | **WordPress** (PHP + MySQL). **Not** Next.js, **not** a static site. |
| Production hosting | Simply.com shared hosting. |
| Local development | **LocalWP** (Local by Flywheel). |
| The design | The attached Aura export becomes a **custom WordPress theme**. |
| Calculator logic | **Vanilla JavaScript**, math runs in the browser. **Never** ported to PHP. |
| Calculator delivery | A **custom plugin** (`pawmanual-calculators`), not baked into the theme. See §5. |
| Calculator architecture | A **reusable pattern**: one shared engine + one small config file per calculator. See §6. |
| SEO meta & schema | Managed by an SEO plugin (**RankMath**). The theme must **not** hardcode what the plugin should own. |
| Styling | **Tailwind CSS** for the theme, **compiled** to a static file. The plugin uses its own plain CSS. |

---

## 3. The Aura Design: Structure Summary  *(Step 1)*

The attached export (`design/aura-export.html`) is a **single homepage**.

### 3.1 What's in the export
- **Tailwind CSS via CDN** (`cdn.tailwindcss.com`): fine for a mockup, not for production.
- **Fonts:** Inter (base sans), Playfair Display (display headings), Geist (UI text).
- **Lucide icons**: SVGs are already inline; a CDN script is also loaded.
- A custom `fadeSlideIn` animation + an IntersectionObserver scroll-reveal effect.
- An **"UnicornStudio" animated background** widget loaded from an external CDN.

### 3.2 Page sections (top → bottom)
1. **Animated background**: decorative full-width widget behind the top ~800px.
2. **Header** (sticky, blurred): left nav (*Dog Tools, Cat Tools, Bird Tools,
   Small Pet Tools*), centered wordmark "ThePawManual" (Playfair), right side a
   *Search* button and a *Cart* button with a "2" badge.
3. **Hero**: giant "ThePawManual" headline; a large dog photo card with a
   *Find a Tool* button and category pills (*Age, Nutrition, Health, Puppies*);
   a "Featured Tool" card promoting the **Dog Age Calculator** with an
   *Open Calculator* button.
4. **Filter bar**: "Filter by" with *Category / Pet Size / Type: Dogs* buttons
   and a search input.
5. **Tools grid**, 6 cards: **Dog Age Calculator**, **Cat Age Calculator**,
   Puppy Weight Predictor, Food Portion Calculator, Pregnancy Due Date, Water
   Intake Calculator. Each card: icon, title, description, *Open Tool* button.
6. **Ad banner placeholder**: a dashed box (this is the ad monetization slot).
7. **Pet Life Gallery**: one large image + two small images, species filter
   pills, carousel arrows. Decorative/editorial.
8. **Statement band** (dark): "Happy Pets, Happy Life", 4 colored image tiles,
   and a "Use code FUREVER" promo line.
9. **Secondary promo**: full-width image, "Nature's Best" organic wellness
   line, *Shop Now* / *Learn More* buttons.
10. **Footer**: 4 link columns (ThePawManual/contact, Social, Company, Terms &
    Policy) and a copyright line.
11. **Scripts**: Lucide init, a JS year-setter, a favourite-toggle demo.

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
`~/Local Sites/<site-name>/app/public/`; that is the WordPress root, and
`wp-content/` lives inside it.

We connect the repo to LocalWP with **two symlinks**: the theme folder and the
plugin folder. A symlink makes the LocalWP location *point at* the repo folder,
so **anything edited in the repo is instantly live in LocalWP, no copying.**

| Repo folder | Symlinked to (inside LocalWP) |
|---|---|
| `wp-content/themes/thepawmanual/` | `~/Local Sites/thepawmanual/app/public/wp-content/themes/thepawmanual/` |
| `wp-content/plugins/pawmanual-calculators/` | `~/Local Sites/thepawmanual/app/public/wp-content/plugins/pawmanual-calculators/` |

Only those two folders enter WordPress. The repo's `package.json`,
`node_modules/`, `design/`, `CLAUDE.md`, etc. stay **outside** WordPress entirely:
they are development tooling, not site files. The exact symlink commands are in
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

## 5. Calculators: Plugin vs Theme (Recommendation)  *(Step 3)*

**Recommendation: build the calculators as a plugin** (`pawmanual-calculators`).
You leaned toward a plugin; I agree, and here is the reasoning so it's on record.

**Why a plugin:**
- **Survives a theme change / redesign.** The calculators are the *product*; the
  theme is the *wrapper*. If the site is re-themed later (very likely for an SEO
  project), the calculators keep working untouched.
- **Clean separation of concerns.** "Site chrome" (theme) and "tools" (plugin)
  can be reasoned about, debugged, and updated independently, helpful for the
  SEO specialist and future devs.
- **Scales to programmatic SEO.** The plugin's registry + shortcode pattern is
  exactly the mechanism we'll later use to mass-produce calculator pages.
- **Isolated activation.** A plugin can be deactivated to debug an issue without
  taking down the whole site's look.

**The tradeoff (cost of a plugin):**
- Two components to install and keep in sync instead of one.
- The plugin can't rely on the theme's Tailwind classes, so it ships its own
  CSS. We do this **deliberately**: it's what makes the plugin
  theme-independent, so it's a feature, not a cost.
- A small one-time indirection layer (registry + shortcode).

**The coupling rule (both directions stay loose):**
- The **theme works without the plugin**: homepage tool cards simply become
  links to pages.
- The **plugin works under any theme**: its CSS and JS are self-contained.

**What lives where:**
- **Theme:** header, footer, homepage, the homepage tool-grid *cards* (static
  promo cards that link to tool pages), and ad slots.
- **Plugin:** the actual interactive calculators: the markup shell, the engine,
  each calculator's config/formula, and the `[pawmanual_calculator]` shortcode.
- **WordPress Pages:** each calculator gets a real Page (e.g.
  `/dog-age-calculator/`) whose content holds the shortcode plus SEO intro copy
  the SEO specialist can edit.

---

## 6. The Reusable Calculator Pattern

This is the core architecture. We **establish** it with the Dog Age Calculator
and **prove** it with the Cat Age Calculator.

**Three shared pieces, written once, never duplicated:**
1. `templates/calculator-shell.php`, the identical markup wrapper for every
   calculator: a title slot, an empty `<form>` (fields are injected by JS),
   Calculate / Reset actions, an `aria-live` result region, a disclaimer, and a
   `<noscript>` fallback. It carries `data-calculator="<id>"`.
2. `assets/js/calculator-core.js`: **the engine.** It exposes
   `window.PawManual.register(config)`. On page load it finds every
   `.pm-calc[data-calculator]` element, looks up the registered config by id,
   renders the input fields from that config, wires the buttons, validates
   input, runs `config.calculate()`, and renders the result.
3. `assets/css/calculators.css`: styles the shell; plain CSS, visually matched
   to the theme's neutral/rounded look.

**One small piece per calculator:**
- `calculators/<id>/<id>.js`: a config object describing the calculator, ending
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
the CSS with **zero changes**: only a new `cat-age.js`. If adding Cat Age
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

### 7.1 POC: definition of done

The proof of concept is complete when, in LocalWP:
- The `thepawmanual` **theme** is active and renders the **homepage from the Aura
  design** (header, hero, filter bar, tools grid, ad slot, gallery, statement,
  promo, footer), responsive on mobile and desktop.
- The `pawmanual-calculators` **plugin** is active.
- A `/dog-age-calculator/` Page and a `/cat-age-calculator/` Page each render a
  **fully working calculator** end to end: enter values → Calculate → correct
  result; input validation; Reset.
- The **Cat Age Calculator was built purely as a new config file**, proving the
  reusable pattern from §6.
- The homepage tool cards for Dog Age and Cat Age link to those pages.
- Tailwind is **compiled to a static file**, no CDN, and there are no console errors.

**The POC explicitly excludes:** the other 4 calculators, programmatic SEO,
RankMath configuration, real ad code, written content, and production deploy.

### 7.2 Phases

- [x] **Phase 0: Scaffolding.** Create the repo file tree, `.gitignore`,
      `package.json`, `tailwind.config.js`, the Tailwind input CSS, `README.md`;
      copy the Aura export to `design/aura-export.html`; run `npm install`.
- [x] **Phase 1: Theme skeleton.** `style.css`, `functions.php`, `inc/*`,
      `header.php`, `footer.php`, `index.php`, `page.php`, `404.php`; theme
      supports (`title-tag`, `html5`, `post-thumbnails`, `custom-logo`, menus);
      asset enqueuing; menu registration; first Tailwind compile; placeholder
      `screenshot.png`.
- [x] **Phase 2: Homepage.** Build `front-page.php` + `template-parts/home/*`
      from the Aura design; reusable `tool-card.php` and `ad-slot.php`; homepage
      tool list in `inc/tools-data.php`; apply the §10 design adaptations.
- [x] **Phase 3: Plugin framework.** Plugin bootstrap + header;
      `class-registry.php`; `calculator-shell.php`; the `calculator-core.js`
      engine; `calculators.css`; the `[pawmanual_calculator]` shortcode;
      conditional asset enqueuing.
- [x] **Phase 4: Dog Age Calculator.** `dog-age.js` config + formula; register
      it; verify end to end.
- [x] **Phase 5: Cat Age Calculator.** `cat-age.js` config + formula **only**
      (no changes to any shared file); verify the pattern held.
- [x] **Phase 6: Wire-up & test.** Create the two WP Pages with shortcodes,
      link the homepage cards, full LocalWP test pass, responsive check.

**After the POC (NOT now):** the remaining calculators, programmatic-SEO page
generation from a data source, RankMath setup, ad integration, font self-hosting
finalization, and production deploy to Simply.com.

---

## 8. LocalWP Setup & Testing Guide  *(Step 5)*

**Legend:** **[You]** = you click in LocalWP / the WordPress admin.
**[Claude]** = I do it through tools: you don't write any code or run commands.

**Step 1: [You] Create the LocalWP site.**
LocalWP is already installed (you have a `hampenlocal` site). In LocalWP, click
**"+"** (Create a new site) → name it **`thepawmanual`** → choose the
**"Preferred"** environment → set an admin **username and password** you'll
remember → **Create site**. This creates the site at
`~/Local Sites/thepawmanual/` with a local address `http://thepawmanual.local`.

**Step 2: [You] Confirm the site folder.**
In LocalWP, right-click the site → **"Reveal in Finder"**. Confirm the path is
`~/Local Sites/thepawmanual/app/public/` (that is the WordPress root; `wp-content`
is inside it). **Tell me the path**, especially if you named the site something
other than `thepawmanual`, since that changes the symlink commands.

**Step 3: [Claude] Link the theme & plugin into LocalWP.**
Once you confirm the path, I create the two symlinks so the repo's theme and
plugin appear inside LocalWP's `wp-content`:
```
ln -s "/Users/thomashjaltebojsen/Desktop/Git/thepawmanual/wp-content/themes/thepawmanual" \
      "/Users/thomashjaltebojsen/Local Sites/thepawmanual/app/public/wp-content/themes/thepawmanual"

ln -s "/Users/thomashjaltebojsen/Desktop/Git/thepawmanual/wp-content/plugins/pawmanual-calculators" \
      "/Users/thomashjaltebojsen/Local Sites/thepawmanual/app/public/wp-content/plugins/pawmanual-calculators"
```
After this, every change I make in the repo is instantly live in LocalWP.

**Step 4: [Claude] Build the CSS.**
I run `npm install` then `npm run build:css` so the compiled `theme.css` exists.

**Step 5: [You] Activate the theme.**
In LocalWP, select the site → **"WP Admin"**. In WordPress:
**Appearance → Themes → "ThePawManual" → Activate**.

**Step 6: [You] Activate the plugin.**
WordPress admin → **Plugins → "PawManual Calculators" → Activate**.

**Step 7: [You] Set clean URLs.**
WordPress admin → **Settings → Permalinks → select "Post name" → Save**.
(Clean URLs matter for SEO.)

**Step 8: [You] Create the calculator pages.**
> **Superseded by the content workstream (§13).** The calculator pages now live
> under a `/tools/` parent (`/tools/dog-age-calculator/`, not the flat
> `/dog-age-calculator/` this step originally created) and they arrive via the
> WXR import (§13) with their SEO copy already written, rather than being typed
> by hand. If you have the old flat pages in LocalWP from the POC, move them
> under the `Tools` parent (Page Attributes → Parent) or delete them before
> importing, so you don't end up with two copies.

The shortcodes are unchanged: **`[pawmanual_calculator id="dog-age"]`** and
**`[pawmanual_calculator id="cat-age"]`**, and they ship inside the imported
page content.

**Step 9: [You] Set the homepage.**
**Pages → Add New** → title **"Home"** → **Publish** (leave the content empty;
`front-page.php` supplies the homepage). Then **Settings → Reading → "Your
homepage displays" → "A static page" → Homepage = "Home" → Save**.

**Step 10: Testing.**
- **[You]** Open `http://thepawmanual.local` and check the homepage renders.
- **[You]** Open a calculator page, enter values, click Calculate, confirm the
  result; try empty/invalid input; try Reset.
- After I change files, **refresh the browser**: use a hard refresh
  (**Cmd+Shift+R**) after CSS/JS changes.
- I can verify code correctness, **but I cannot see your browser.** Tell me what
  you see, or paste a screenshot, and I'll fix from there.

**Optional / later:** install **RankMath** (Plugins → Add New) when SEO work
begins; enable `WP_DEBUG` via LocalWP for development.

---

## 9. Conventions for Future Sessions

- The tech decisions in **§2 are firm**: don't reopen them.
- **WordPress standards:** escape on output (`esc_html`, `esc_attr`, `esc_url`),
  sanitize on input, use text domains (`thepawmanual` for the theme,
  `pawmanual-calculators` for the plugin), prefix functions (`tpm_` theme /
  `pmc_` plugin), and omit the closing `?>` in PHP-only files.
- **Calculators are vanilla JS**: never port the math to PHP.
- **The reusable pattern is law:** a new calculator must not require editing
  `calculator-shell.php`, `calculator-core.js`, or `calculators.css`. If it
  seems to, fix the shared layer rather than duplicating it.
- **Styling:** the theme uses Tailwind: rebuild `theme.css` after changing
  classes (`npm run build:css`). The plugin uses plain CSS in `calculators.css`.
- **Assets:** enqueue everything via `wp_enqueue_*`; load calculator assets only
  on pages that actually use the shortcode.
- **SEO ownership:** RankMath owns `<title>`, meta description, Open Graph,
  canonical URLs, and breadcrumb/Article schema. The theme uses
  `add_theme_support('title-tag')` and calls `wp_head()`, `wp_body_open()`, and
  `wp_footer()`; it must **never** hardcode meta tags. The plugin *may* output
  calculator-specific JSON-LD (`WebApplication` / `HowTo` / `FAQ`), since that
  schema describes the tool, not the page.
- **Ad slots:** `ad-slot.php` stays an empty, labelled container: never hardcode
  an ad network's script; that gets added later or managed by a plugin.
- Keep `functions.php` thin: it just `require`s the `inc/` files. One concern
  per file.
- Write clean code with comments explaining the **why**, not the **what**: a
  WordPress-literate SEO specialist and future devs must be able to maintain it.
- **Accessibility:** real `<label>`s, `aria-live` on result regions, fully
  keyboard-operable controls.
- **Mobile-first:** most pet-owner traffic is mobile: always verify small screens.
- Keep the Aura reference in `design/`.
- **No em dashes (U+2014) anywhere in published copy or user-facing UI strings.**
  Use a colon, a comma, parentheses, or a full stop, whichever the sentence
  actually calls for, never a mechanical find-and-replace. En dashes (U+2013)
  are kept and required in numeric and date ranges: `$1,000–$2,000`,
  `10–16 years`, `4–6 weeks`; removing one corrupts a price, a dose, or a
  timeline. Why: this is the published house style, not a preference. The
  live site contains zero em dashes; whoever published it replaced every one
  by hand at launch. The repo had drifted to roughly 2,779 of them across 130
  files before the 2026-09-15 sweep, so every future wave would have
  re-introduced them if the rule weren't written down. The boundary: this
  rule governs published page copy and user-facing strings (theme and plugin
  translatable/echoed strings included): of a page's frontmatter, that means
  only `seo-title` and `meta-description` (they publish verbatim as
  `rank_math_title`/`rank_math_description`, per `tools/wxr/lib/wxr-xml.js`).
  Every other frontmatter field (`page`, `status`, and any inline comment)
  never reaches a reader and may carry one freely, and several already do,
  as internal shorthand like "(YMYL: AAHA-sourced)". PHP/JS code comments and
  internal planning docs are exempt too, since nobody reads them on the site
  and churning the roughly 386 that live there would touch every file in the
  theme for no reader benefit. How to check before shipping a wave: `grep`
  `content/` for the character and expect zero hits everywhere except inside
  a `(not page copy)` section (`## Notes`, `## Expansion suggestions`,
  `## Implementation notes`: internal fact-check flags and citation lists that
  never publish, per §13) or a non-publishing frontmatter field; a hit in
  either is expected and correct. All H1s are now clean too (a separate
  worker corrected the eight that weren't, in lockstep with
  `tools/wxr/publish-registry.json`, per §13), so a hit on an H1 line is a
  defect like any other, not an exemption. A check that respects all of this,
  including the frontmatter boundary the first version of this rule missed:
  `grep -rlP '\x{2014}' content/ | while read -r f; do awk '/^## .*\(not page copy\)/{skip=1;next} /^## /{skip=0} /^---$/{fm=!fm;next} fm && !/^(seo-title|meta-description):/{next} !skip' "$f" | grep -qP '\x{2014}' && echo "$f"; done`
  lists only files with a genuine, unexempted hit.
- **Update this file**, §7.2 phase checkboxes and §11 status, as work completes.

---

## 10. Design Adaptations: Decisions Needed

The Aura export is a generic e-commerce template with pet content dropped in.
Several pieces don't fit a free-tools, ad-monetized site. Here is my recommended
action for each: **please confirm or adjust when you approve this plan.**

| Element in the Aura design | Issue | Recommended action |
|---|---|---|
| Header cart icon + "2" badge | E-commerce; there's no store | **Remove** |
| Header "Search" button | Fine in principle | **Keep**: wire to WP search later (visual only for the POC). **Done 2026-09-14**: real `search.php`/`searchform.php`/`inc/search.php`; see §13. |
| "Statement" band: "Use code FUREVER" line | E-commerce promo | **Drop the promo line**, keep the visual band, rewrite the copy |
| "Secondary Promo": "Nature's Best", "Shop Now" | E-commerce product push | **Repurpose** into a content CTA (e.g. "Browse all tools") or remove |
| "Pet Life Gallery" section | Decorative, slightly off-purpose | **Keep** for POC visual fidelity; candidate to become "Popular tools" later |
| Footer: duplicate copyright; one says "Pawsome" | Template leftover bug | **Fix**: a single correct ThePawManual copyright |
| Duplicate `id="y"` + JS year-setter | Invalid HTML | **Render the year in PHP** (`date('Y')`); drop the JS |
| UnicornStudio animated background | External CDN dependency, decorative | **Drop** (optionally replace with a lightweight CSS gradient) |
| Lucide icons CDN script | The SVGs are already inline | **Keep SVGs inline**, drop the CDN script |
| Tailwind via CDN | Not production-grade; hurts Core Web Vitals | **Replace** with a compiled static `theme.css` (already planned) |
| "Find a Tool" / category pills / filter bar | Good UI, but no logic in the export | **Keep visually**; real filtering is post-POC |
| Ad Banner Placeholder | This is the monetization | **Keep**: becomes the reusable `ad-slot.php` |

**Open questions, all resolved on approval (2026-05-21); the recommendations below were accepted:**
1. The e-commerce-flavoured sections (Statement band, Secondary Promo): remove
   them entirely, or keep them visually and rewrite the copy to be
   tool/content-oriented? *(My recommendation: keep visually, rewrite copy.)*
2. Keep the Pet Life Gallery on the homepage for the POC? *(Recommendation: keep.)*
3. Confirm the **calculators-as-a-plugin** recommendation in §5. *(You leaned
   plugin; I agree.)*
4. Confirm the LocalWP site will be named **`thepawmanual`**: it determines the
   symlink paths in §8.

---

## 11. Current Status

- **Last updated:** 2026-10-06.
- **`main` is the live theme (since 2026-10-21). Read before zipping
  anything.** Until then it was not: the theme on `thepawmanual.com` was the
  branch `claude/seo-optimization-design-4519ad` (Terracotta redesign,
  key-takeaway box, search, tools strip) plus three files never committed there
  (`header.php` menu `depth` 2, `src/tailwind.css` with the nav dropdown CSS and
  the `blur` removed from the `fadeSlideIn` keyframes, and the compiled
  `assets/css/theme.css`), and `main` was 171 files behind it. A zip built from
  that old `main` would have replaced the live theme through WordPress's
  "Replace installed with uploaded" (which deletes the old theme folder first)
  and silently rolled the site back to the old design. On 2026-10-21 `main` was
  fast-forwarded to include the redesign, the three live-only files and the
  page-image feature, so a zip built from `main` now matches live. The standing
  check stays: before uploading, compare the new zip with the previous
  `~/Desktop/thepawmanual-deploy/thepawmanual-theme.zip` by file name **and
  size** (`unzip -l`) and expect only the differences you made on purpose;
  `unzip -p NEW.zip thepawmanual/header.php | grep "'depth'"` must print `2`
  twice. Keep the previous zip as the rollback copy and write new zips under a
  new name. Any other branch cut from before 2026-10-21 is based on the old
  design: rebase it onto `main` before using it.
- **Page images (2026-10-06).** A page's Featured image now prints under the
  title and deck and above the key-takeaway box (`inc/page-image.php`,
  `template-parts/content/page-image.php`, called from `article-header.php`).
  No CSS change was needed. Which image belongs to which page is stored only in
  the database: see `docs/PAGE-IMAGES.md`, which also holds the owner's
  shipping steps and a content flag on `Pet-Safety.webp` and `New-Pet-Checklist.webp`.
- **Phase:** **Redeploy-ready.** Every workstream this file and §13 track is
  now closed: the SEO/design render pipeline (three review rounds,
  2026-09-14), the Terracotta rebrand, the em-dash purge (2026-09-15), the
  fact-check pass and its application across ~35 pages, and the GEO rewrite
  of the remaining 109 pages (two full review-fix-gate rounds plus a final
  direct cleanup, 2026-09-16). All 126 content pages have now been through
  the same fact-check + GEO treatment, not just wave 1. A final,
  whole-diff gate (2026-09-16, below) then checked theme + content +
  registry together for the first time, the deploy artifacts are built and
  verified, and `docs/LAUNCH-RUNBOOK.md` is rewritten end to end for the
  actual next operation: **the owner permanently deletes the 18 live pages
  and reimports fresh**, since they aren't indexed yet. See
  `docs/LAUNCH-RUNBOOK.md` for the step-by-step; nothing in this repo blocks
  it any longer. *(Superseded 2026-10-06: that redeploy has since happened. The
  theme zip of 2026-09-18 is what is live, see the first note above. Do not
  repeat the delete-and-reimport for a theme change.)*

**Final whole-diff gate, 2026-09-16: what it checked, run directly rather
than delegated, since every prior round had reviewed theme, content, or the
registry in isolation but never all three together against the actual
cumulative diff.** All of the following passed or was fixed on the spot:
`php -l` clean across all 43 theme+plugin PHP files; `php tools/tests/run.php`
94/94 passing, after fixing 2 tests that had gone stale, not wrong: they
asserted an em dash would survive in the deck of the "French Bulldog" and
"Can Cats Eat Tuna?" titles, written before the em-dash purge correctly
removed it from both, now asserting the actual post-purge wording instead;
`node tools/wxr/generate.js --all` (126 pages) and `--wave 1` (17 pages) both
clean, no title-drift errors; all 126 registry titles independently verified
to match their files' current H1 exactly, not just the 8 corrected during
the em-dash purge; all 121 pages carrying FAQ JSON-LD independently verified
to still parse as valid JSON; the em-dash grep check (§9) returns zero hits;
no `[PLACEHOLDER]`, debug statement (`var_dump`/`print_r`/`console.log`), or
stale `clay-` class reference anywhere in `content/` or the theme/plugin;
`theme.css` was found **stale** (hadn't been rebuilt since the last content
and template edits): rebuilt via `npm run build:css`, and a second,
independent build to a scratch path confirmed byte-identical, closing
exactly the silent-failure mode §13's "Deploy reminder, standing" note
warns about.

**Pre-deploy review, 2026-08-14: what it caught, so nobody re-introduces it.**
The content workstream's output was reviewed before anything was uploaded. All
six findings were confirmed by reproduction, not inspection:
1. The calculator shortcode was HTML-escaped into `id=&quot;dog-age&quot;` by the
   markdown converter: both tool pages would have imported with no calculator.
2. 201 of 262 internal links on the wave-1 pages pointed at unpublished pages.
3. Five trust pages carry `[PLACEHOLDER]` copy that publishes verbatim.
4. The generator stamped a fresh date per run, so wave 2 would have duplicated
   every ancestor hub and orphaned its children.
5. Page titles were re-read from the markdown H1 on every run, reproducing (4)
   whenever an H1 was edited.
6. The `DOMDocument` heading pass could silently drop everything after a
   JSON-LD block containing a literal `<`.
Structure fidelity was verified against `design/seo-site-structure.html`: 107 of
its 113 URLs are written and correctly nested; the 6 absent ones are tools pages
for calculators that do not exist yet.

**Known, still open (not defects, decisions):** a 2026-09-15 re-measurement
(parsing every FAQPage block in `content/` and diffing visible answer text
against its JSON-LD twin word for word; see `content/README.md`'s "What
remains" item 2) found 118 of 121 pages carrying FAQ schema have text that
differs between the visible question or answer and its JSON-LD, not the 25
pages previously quoted. Google requires the two to match, and which wording
wins is an SEO call. Fix before generating wave 2.

**Done**
- **Phase 0: Scaffolding** ✓: `.gitignore`, `package.json`, `tailwind.config.js`,
  theme `src/tailwind.css`, `README.md`; Aura export copied to
  `design/aura-export.html`; `npm install` run.
- **Phase 1: Theme skeleton** ✓: `style.css`, thin `functions.php`,
  `inc/{setup,enqueue,nav-menus}.php`, `header.php`, `footer.php`, `index.php`,
  `page.php`, `404.php`, `assets/js/theme.js`; theme supports + asset enqueuing;
  `primary` nav menu registered; Tailwind compiled to `assets/css/theme.css`.
  (`screenshot.png`, the WP-admin thumbnail, is intentionally skipped, cosmetic.)
- **Phase 2: Homepage** ✓: `front-page.php` plus
  `template-parts/home/{hero,filter-bar,tools-grid,gallery,statement,promo}.php`,
  reusable `template-parts/{tool-card,ad-slot}.php`, `inc/tools-data.php`, and the
  `inc/icons.php` inline-SVG helper. Aura images downloaded, optimised to JPEG,
  and stored in `assets/images/`. The §10 design adaptations are applied.
- **Phase 3: Plugin framework** ✓: the `pawmanual-calculators` plugin:
  bootstrap + header, `PMC_Registry`, the `[pawmanual_calculator]` shortcode,
  conditional asset enqueuing, the shared `templates/calculator-shell.php`, the
  self-contained `assets/css/calculators.css`, and the `calculator-core.js`
  engine. The two calculator config files (`calculators/*/`) come in Phases 4–5.
- **Phase 4: Dog Age Calculator** ✓: `calculators/dog-age/dog-age.js`: a
  size-aware veterinary formula plus its fields, registered with the shared
  engine. Verified end-to-end on a live page.
- **Phase 5: Cat Age Calculator** ✓: `calculators/cat-age/cat-age.js`. Added
  as a single new config file with zero changes to the shared engine, shell,
  CSS, or registry: the reusable pattern (§6) is proven. Verified end-to-end.
- **Phase 6: Wire-up & test** ✓: both calculator Pages created with their
  shortcodes; the homepage cards link through to them; a static "Home" page is
  the front page. Responsive pass done: the header collapses to a hamburger
  menu below 1024px and the hero headline scales fluidly with `clamp()`. The
  POC is verified end-to-end in LocalWP.

**Environment**
- Build tooling: Node v24 / npm 11; **Tailwind CSS v3**: `npm run build:css`
  (one-off) / `npm run watch:css`. The compiled `theme.css` is committed.
- **LocalWP site:** created: display name "ThePawManual", folder
  `~/Local Sites/thepawmanual/`, served at **`http://localhost:10008`** (LocalWP
  *localhost* routing mode; no HTTPS; note this differs from the
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
preview for the team, without exposing a half-built site to Google.

**The SEO rule (non-negotiable):** the preview must never be indexable. Google
indexing a thin, unfinished site weakens the domain before the real launch. The
preview is locked behind a password **and** has search-engine indexing
discouraged. Indexing is switched on only at the real launch.

**Approach: upload the code, reproduce the setup.** The theme and plugin are
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
   natively; simply.com's File Manager cannot extract zips, so it is not used.
3. **[You]** simply.com WP admin → set the Site Title; set permalinks to
   "Post name".
4. **[You]** Create the Dog Age Calculator, Cat Age Calculator and Home pages
   (the calculator pages hold `[pawmanual_calculator id="…"]`); set Home as the
   static front page.
5. **[You]** Lock it down: install the "Password Protected" plugin and set a
   password; Settings → Reading → tick "Discourage search engines".
6. **[You]** Verify in a private browser window: the password gate appears and
   every page works once past it. Hand the URL + password to the team.

**Ongoing updates:** rebuild the CSS, re-zip, re-upload the theme/plugin folders
(overwriting), and add any new calculator pages on simply.com. All development
stays in LocalWP: never edit the live preview directly, or the next upload
overwrites it.

**At the real launch (later):** remove the password, untick "Discourage search
engines", confirm HTTPS/SSL is active, and submit the site to Google Search
Console. The step-by-step version of that day now lives in
`docs/LAUNCH-RUNBOOK.md`: follow it rather than this paragraph.

---

## 13. The Content Workstream *(added 2026-07-28)*

The site has a second half that §1–§12 predate: **the content**. It has its own
source of truth, deliberately separate from this file.

| Where | What it owns |
|---|---|
| `content/README.md` | The **editorial constitution**: the rules every page follows. Read before writing or editing any page. |
| `content/SITEMAP.md` | Page-by-page tracker: every URL, its status, its file, plus the backlog of unwritten pages. |
| `content/SEO-STRATEGY.md` | Launch checklist, GSC setup, the **wave publishing plan**, link building, ops calendar. |
| `docs/LAUNCH-RUNBOOK.md` | The executable version of the above: what to do, in order, on launch day. |
| `docs/PAGE-IMAGES.md` | Which Media Library image is each page's Featured image, with alt text, plus the shipping steps. That assignment lives only in the database, so this table is the record to re-apply after a delete-and-reimport. (Not in `content/`: the WXR generator would try to import it as a page.) |

**`content/` mirrors the URL structure.** `content/dogs/food/puppy-food.md` →
`/dogs/food/puppy-food/`; a folder's `index.md` is that folder's hub page.
Frontmatter carries the RankMath fields. **127 of 133 pages are drafted** (126 of
them importable, the homepage is a theme template, not a WP page). The six
holdouts are Tools pages for calculators that don't exist yet, deliberately on
hold: a tool page without a working tool is thin content a fresh domain can't
afford.

### The import path: do not hand-paste pages

`tools/wxr/` generates a WordPress import file from the markdown:

```bash
node tools/wxr/generate.js --wave 1
```

Everything imports as **drafts**, with parent/child hierarchy derived from the
URL and RankMath title/description as postmeta. `tools/wxr/README.md` has the
WP-admin walkthrough. Three rules the tooling enforces, all of them load-bearing:

> **`tools/wxr/publish-registry.json` is state, not a build artifact: never
> delete it.** WordPress's importer recognises an already-imported page by
> **title + date** (`post_id` is only a transient parent-mapping key). Every
> wave re-includes its ancestor hubs so `post_parent` resolves, so if `/dogs/`
> arrives on wave 2 with a different title or date than wave 1 gave it,
> WordPress inserts a *second* `/dogs/` at `dogs-2` and parents that wave's
> children to the duplicate. The registry pins both values per URL on first
> generation. **But the pinned date is only trustworthy until the page is
> actually published.** This tool always imports as
> `<wp:status>draft</wp:status>`, and a WordPress draft does not keep the
> imported `post_date` as its real "went live" value: the moment a human
> clicks Publish, WordPress stamps that literal moment as the post's real
> `post_date`/`post_date_gmt`, silently overwriting whatever the registry had
> recorded. That drift hit all 17 wave-1 URLs: the registry's original bare
> dates (recorded at generation time) no longer matched the live pages' real
> `post_date` once they were published (e.g. `/dogs/` is actually
> `2026-09-10T20:03:42+00:00`, not the generation-time date the registry
> first pinned), so title+date dedup against the live database would have
> failed and a wave-2 run that re-includes `/dogs/` as an ancestor hub would
> have inserted a duplicate rather than resolving to the existing page: the
> opposite of what this file exists to prevent.
>
> **Repaired 2026-09-14.** The registry's `date` field now holds one of two
> shapes, and the shape itself is the signal for which: a bare calendar date
> is a *reservation* for a URL that has never been published, still safe to
> emit at midnight UTC because no live timestamp exists yet to disagree with
> it; a full ISO-8601 datetime with a UTC offset is a *harvested* value,
> copied verbatim (offset included) from that exact page's live RankMath
> JSON-LD `datePublished` field once it has actually gone live. Every
> wave-1 registry entry was corrected to the harvested shape. `generate.js`
> now also warns automatically the moment a run is about to re-emit a URL
> whose registry date is still bare, so a published page can't drift back
> into this state unnoticed: see `tools/wxr/README.md`'s
> "Re-harvesting a drifted date" for the manual fix if it ever does. Editing
> a page's H1 in `content/` after it has been imported still fails the build
> loudly rather than silently duplicating weeks later: that error is the
> tool working, not breaking.

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
helper that enforces it: use it for any new hardcoded internal link in a
template, including in the homepage copy (`template-parts/home/seo-content.php`).

**Page *content* is covered too, since 2026-08-14.** The templates always
honoured the rule above; the imported page bodies did not, and they are where
most links live: after wave 1, 201 of 262 internal links on the launch pages
pointed at pages that had not shipped yet. `inc/internal-links.php` adds a
`the_content` filter that degrades any link to a not-yet-published page into
plain text, and lights it back up automatically when that page's wave ships:
no re-import, no template edit. It costs one memoized `get_pages()` query per
request. Two limits are documented at the call site: targets are matched by
**current path**, so moving a live page permanently strands links written
against its old path; and the path map loads every page row per request, which
needs a persistent cache well before the programmatic-SEO scale in §1.

`inc/script-protection.php` exists because the heading/TOC pass runs
`DOMDocument` over content that contains the FAQ JSON-LD block, and libxml has
no HTML5 script-data state: a literal `<` followed by a letter inside that JSON
used to break out of the script and silently drop the rest of the page. Script
blocks are swapped for inert placeholders and restored verbatim; anything
unbalanced fails closed. **If you add a pass that parses post content, route it
through this helper**: losing a table of contents is a non-event, losing half a
page is not. `inc/content-blocks.php` (below) is the **first** of the two
DOMDocument passes over post content that now run on every article request:
see that subsection for why its own fail-closed guard exists, and why its
ordering relative to `tpm_inject_heading_ids()` is load-bearing, not just
conventional.

### The SEO/design render pipeline *(added 2026-09-14)*

A second pass, on top of the content workstream above, reworked how every one
of the 127 pages is DISPLAYED, five requirements from the client: an answer
box + FAQ + table of contents at the top of every article; a related-links
block at the bottom of every article AND every landing/hub page; every
landing page linking to Tools, articles getting a contextual tool CTA; a
working header search (it used to be a dead `<button>`); and splitting an
over-long H1 ("Hook: Subtitle clause") into a short `<h1>` plus a separate
deck line.

**Architectural decision, do not revisit:** all five are implemented
THEME-SIDE: PHP and Tailwind, never `content/*.md` or
`tools/wxr/publish-registry.json`. Two hard constraints force this (§13
above, C1/C2): WordPress's importer recognises an already-imported page by
title + date, so the 18 pages already live at the time of this pass would
never re-import even if the markdown changed; and `tools/wxr/generate.js`
deliberately THROWS if a page's H1 changes after import, specifically to stop
someone "fixing" a long H1 in markdown and silently orphaning that page's
registry entry. A theme-side fix applies to all 127 pages at once, the 18
already live and the 109 still to come, with zero import risk and no
markdown edits at all.

**The new `inc/` files, one concern each:**
- `inc/article-meta.php`, `tpm_split_title()` (requirement 5: the pure
  H1/deck splitter, floored at a 5-character H1 side, verified against the
  full 127-title corpus, not a guessed number, see the function's own
  docblock for why 5 and not the original 12), `tpm_get_read_time()`, and
  `tpm_get_eyebrow()` (the category chip, itself running the parent hub's
  title through the same splitter).
- `inc/content-blocks.php`, `tpm_prepare_article_content()` (requirements 1
  and 3): ONE `DOMDocument` parse that lifts the lead paragraph into a "key
  takeaway" box, restructures the FAQ's H3/P pairs into a `<details>`
  accordion, and inserts a contextual tool CTA card. **MUST run before**
  `tpm_inject_heading_ids()` (`inc/content-nav.php`) in `page.php`: this
  pass changes which elements are even present (the lead paragraph gone, the
  FAQ's H3 "questions" moved inside `<summary>`) before the TOC pass walks
  the tree, and the inline-CTA placement (before the second top-level H2) is
  only well-defined against the original flat section structure. That makes
  this file's parse the FIRST of two full `DOMDocument` parses now run over
  every article's content on every uncached request, worth remembering
  alongside `inc/internal-links.php`'s `get_pages()` caching warning already
  in this section: at 127 pages neither costs anything; both are exactly the
  kind of per-request cost the programmatic-SEO scale in §1 will need to
  revisit.
- `inc/tools-data.php`, extended (requirement 3) with
  `tpm_get_tools_for_context()` and `tpm_get_inline_tool_for_post()`, ranking
  the six-tool catalogue against a page's URL hierarchy. A tool never
  promotes itself (a calculator's own Page is excluded from its own
  strip/CTA by resolved path), and the inline CTA specifically requires a
  genuine TOPIC match, not a bare species match: living under `/dogs/` alone
  does not justify an age-calculator CTA on an unrelated dog article.
- `inc/search.php` + `search.php` + `searchform.php` (requirement 4), a
  real site search: query tuning (Pages only, capped), a results template,
  and the search form partial. The header's search button
  (`[data-tpm-search-toggle]`) now actually opens a working panel at every
  viewport width, not just `sm:` and up.
- `template-parts/content/related-reading.php` (requirement 2, rewritten) +
  `tpm_get_top_level_hub_siblings()` (`inc/content-nav.php`): the bottom
  links block on every article AND every landing/hub page, including the ten
  pages with no parent at all (the four content hubs themselves plus the six
  trust pages), which fall back to the OTHER top-level content hubs rather
  than rendering nothing.
- `template-parts/content/{article-header,key-takeaway,tools-strip}.php`,
  the corresponding display partials for the pieces above.
- `inc/page-image.php` + `template-parts/content/page-image.php` (added
  2026-10-06): a Page's Featured image, rendered by `article-header.php`
  between the deck and the key-takeaway box. The helper builds the markup with
  a capped srcset so a phone does not fetch the multi-megabyte original (as
  long as WordPress's 768px size exists), and a width that follows the image's
  shape (at most 448px tall; a square is 448px wide, a 16:9 picture about
  800px).

**The shared class/ID contract** (used across the templates above, treat as
fixed, several partials depend on it): table of contents `nav.tpm-toc` /
`a.tpm-toc__link` / `ol.tpm-toc__list`; FAQ `div.tpm-faq` /
`details.tpm-faq__item` / `summary.tpm-faq__q` / `div.tpm-faq__a`; key
takeaway `aside.tpm-key-takeaway` / `div.tpm-key-takeaway__body`; page image
`figure.tpm-page-image`; search
`button[data-tpm-search-toggle]` / `#tpm-search-panel` (ships `hidden`) /
`#tpm-search-field` / `form.tpm-search-form`; tools strip
`section.tpm-tools-strip`, inline CTA `aside.tpm-tool-cta`; related reading
`section.tpm-related-reading`, cards `a.tpm-card`.

**Why the pieces above degrade instead of fatal:** several of these files are
independent of each other by design: `related-reading.php` and
`child-grid.php` call `tpm_split_title()` through a `function_exists()` guard
rather than a hard dependency, and `inc/content-blocks.php`'s
`tpm_dom_content_already_links_url()` reimplements its own path comparison
rather than calling into `inc/internal-links.php`. That's deliberate: a
missing or reordered file degrades one card's title or one CTA's dedup check,
never a fatal error taking down a page.

**2026-09-14 review round 1:** a four-evaluator review of the above (correctness,
SEO, accessibility/responsive, requirements-completeness lenses) found and
fixed: the 12-character H1 floor silently cancelling requirement 5 on the
shortest, most keyword-focused hooks including the live `/tools/` and
`/pet-guides/` pages (now 5, verified against the full corpus); the header
search trigger being `display:none` below Tailwind's `sm` breakpoint, making
requirement 4 desktop-only on a mobile-majority site; both calculator pages
carrying a self-linking tool CTA/strip card: **fixed**, `tpm_rank_tools_for_post()`
(`inc/tools-data.php`) now excludes a page's own tool from its own candidate
list by resolved path; the bottom related-links block rendering nothing on
all ten parentless top-level pages; a "coming soon" tools-strip card failing
WCAG AA text contrast; and `tpm_prepare_article_content()` not failing closed
on a stray unbalanced closing tag in hand-edited content: the same
"silently drops everything after the break" failure mode
`inc/script-protection.php` exists to prevent for `<script>` blocks,
reproduced here for a plain `</div>` and now guarded the same way in both
`inc/content-blocks.php` and `inc/content-nav.php`.

> **Correction, 2026-09-14 (review round 2):** this note originally ALSO
> claimed "the cat page's CTA resolving to the dog calculator on a scoring
> tie" was fixed in round 1. **It was not.** At the time, the fail-closed
> truncation guard above (the last item in this list) was false-positiving on
> real page content: an entity-decode asymmetry between the imported
> `&#39;`/`&quot;` markup and DOMDocument's re-serialized output, not a
> genuine truncation, which silently disabled the whole takeaway/FAQ/CTA
> pass on affected pages. That MASKED the cross-species bug rather than
> fixing it: the inline CTA wasn't rendering AT ALL on either calculator
> page, for a reason that had nothing to do with species, so nobody watching
> only the rendered output could have told the two defects apart. A status
> note asserting a fix that hadn't happened is worse than one saying
> nothing, because the next session trusts it: see review round 2 below for
> what was actually true and what actually fixed it.

**2026-09-14 review round 2 (independent Opus gate, "changes-required"):**
found six further issues in round 1's own output, one of them a correction
of round 1's status note above rather than a code defect:

1. **The cross-species inline CTA: the real fix.** Once the entity-decode
   fix below let `tpm_prepare_article_content()` actually run on affected
   pages, the masked bug reproduced exactly as the correction above
   describes: `/tools/dog-age-calculator/` inserted a mid-article CTA
   promoting the CAT Age Calculator, and `/tools/cat-age-calculator/`
   promoted the DOG one, both are live, indexed pages. Root cause:
   `tpm_rank_tools_for_post()`'s self-exclusion correctly drops a page's own
   tool, but on a calculator page that leaves the OTHER age calculator as
   the only remaining 'available' candidate, and it topic-matches on the
   'age' stem both calculators' `topics` arrays share. Fix:
   `tpm_get_inline_tool_for_post()` (`inc/tools-data.php`) now ALSO requires
   species compatibility: it rejects a candidate whose `species` set is
   disjoint from the page's own species context
   (`tpm_get_page_species_context()`: the top-level ancestor slug when it's
   a species hub, or a 'dog'/'cat' stem in the page's own slug), but only
   when the page actually HAS a species context; a species-general page is
   never blocked by this gate (see item 3). The gate deliberately does NOT
   loosen to a bare species match in place of the existing topic-match
   requirement: that would resurrect the "any page in the dog cluster
   qualifies" failure mode the topic-match gate exists to prevent, just
   moved to a different axis. `template-parts/content/tools-strip.php` is
   unchanged: it still cross-links the other calculator, correctly, in a
   "here are the other tools" context that isn't pretending to be
   topic-specific.
2. **Topic stemming.** A URL segment is often plural ("pet-costs") where a
   tool's `topics` entry is singular ("cost"); the comparison now runs both
   sides through a deliberately crude trailing-'s' trim
   (`tpm_stem_topic_word()`, `inc/tools-data.php`), sufficient for this
   closed, hand-written vocabulary, not a general stemmer.
3. **The wave-1 inline-CTA gap: open by design, not a bug.** Simulated
   across all 18 live URLs: every real article
   (`/pet-guides/{new-pet-checklist,pet-costs,pet-insurance,pet-safety,pet-travel}/`)
   returns null from `tpm_get_inline_tool_for_post()`, and correctly so:
   with only the two age calculators built, no wave-1 article is actually
   about dog/cat age, and `/pet-guides/pet-insurance/` DOES topic-match
   'insurance' but that tool is 'coming-soon', so the top-ranked AVAILABLE
   tool still has no genuine topic match. This is the "no CTA beats an
   irrelevant CTA" rule (see that function's own docblock) working exactly
   as intended, not a regression to chase. It requires no further code
   change: the moment a topically-relevant tool ships, the Pet Cost
   Calculator (`species => ['dogs','cats']`, so item 1's species gate never
   blocks it either) is the documented example: `/pet-guides/pet-costs/`
   starts getting a genuinely relevant CTA automatically, no template edit,
   same mechanism as every other "lights up once published" piece of this
   pipeline (§13 above).
4. **The `/tools/` H1 regression.** `tpm_split_title()`'s 5-character floor
   (correct, per round 1) still splits "Tools: Free Pet Calculators" into H1
   "Tools" + a demoted deck, legitimately clearing the floor, but stripping
   the ranking phrase "Free Pet Calculators" out of the page's one `<h1>`.
   Raising the floor again was rejected, for the same reason round 1's
   12-character floor was rejected: it would also stop genuinely-should-split
   hooks like "Why Does My Cat Knead? Biscuits, Explained" from splitting.
   Fix: a small, filterable per-path override map
   (`tpm_get_title_part_overrides()`, `apply_filters( 'tpm_title_part_overrides', … )`)
   now sits in front of `tpm_split_title()` via a new wrapper,
   `tpm_get_title_parts( $post_id )` (both `inc/article-meta.php`):
   `tpm_split_title()` itself is untouched, still pure, still
   unit-tested directly. Seeded with exactly one entry, `'tools' => true`
   (keep the whole title, matching what the live page shows today, the
   conservative, zero-risk default). **Open SEO question, not resolved by
   this change:** whether `/tools/` should instead read "Free Pet
   Calculators" as its H1. `page.php` resolves `$title_parts` and passes it
   to `template-parts/content/article-header.php`, the template that
   actually renders the `<h1>`, and (round 3, Finding 1, below) that
   template now reads `$args['title_parts']` instead of calling
   `tpm_split_title()` directly, so the override takes visible effect on
   `/tools/` end to end, not just in `inc/article-meta.php`'s own return
   value.
5. **A stale docblock.** `page.php` claimed calculator pages "have no H2s, so
   the TOC simply doesn't trigger for them." False:
   `content/tools/dog-age-calculator.md` has 4 PUBLISHED top-level H2s and
   `cat-age-calculator.md` has 3 (5 and 4 in the markdown; one H2 in each is
   `## Notes (not page copy)`, which never publishes, corrected again
   2026-09-14, round 4: this item's own original "5 and 4" was literally
   true of the markdown but not of what ships), so the TOC renders on both
   like any other article; only the calculator widget's own
   `<h2 class="pm-calc__title">` is excluded from it, by
   `tpm_inject_heading_ids()` (`inc/content-nav.php`). The cat page's 3
   published H2s sit exactly on `page.php`'s `>= 3` TOC threshold, worth
   recording, since removing one H2 there silently drops its TOC.
   Corrected.
6. **The entity-normalized truncation guard**, the fix that made item 1
   reproducible instead of masked: `tpm_content_blocks_lost_too_much_text()`
   and its twin `tpm_content_nav_lost_too_much_text()` (`inc/content-blocks.php`,
   `inc/content-nav.php`) compare a "before" and "after" character count to
   catch libxml silently truncating content on a stray unbalanced tag (round
   1, last item above). Both counts used a plain `wp_strip_all_tags()`,
   which strips tags but does NOT decode entities, and imported
   `post_content` is full of `&#39;`/`&quot;` (the markdown→WXR conversion
   escapes every apostrophe and quote), while DOMDocument's re-serialized
   output decodes those to literal `'`/`"`. That asymmetry alone shrank the
   "after" count by 4–5 characters per entity with ZERO content actually
   lost. (Round 3 replayed all 17 wave-1 page bodies through the real
   `the_content` pipeline and confirmed this precisely: the pre-fix
   comparison false-fires on 7 of the 17 pages on `content-blocks.php`'s
   guard, including `/tools/` outright, entirely explained by entity
   counts, never by dropped text; this item's original "7 of 18" claim was
   in the right ballpark but unreproduced when first written: see round
   3's item 4 below, and `inc/content-blocks.php`'s own docblock, for the
   methodology that corrected it. **Correction, round 4:** round 3's own
   `/tools/` delta/tolerance pair (61 vs 54, below) was measured on a
   replay that stopped at wpautop(): under the FULL chain, through
   `shortcode_unautop()` -> `do_shortcode()` (the real
   `[pawmanual_calculator]` shortcode) -> `tpm_filter_internal_links()` at
   priority 30, `/tools/` measures delta 60 vs tolerance 45 instead. A
   per-page delta is pipeline-dependent and neither pair is repeated here
   as a standalone fact for that reason; the 7-of-17 / 0-of-17 page counts
   are what's load-bearing, and both hold under the full chain,
   independently reproduced twice.) Both guards now run BOTH sides
   through `html_entity_decode()` before comparing (plus whitespace
   stripping, to absorb wpautop's incidental formatting differences);
   normalizing is load-bearing here, not cosmetic: without it, this
   fail-closed guard was a false alarm strictly worse than the bug it exists
   to catch, silently disabling the takeaway/FAQ/CTA/TOC pass on pages that
   had parsed perfectly fine.

**2026-09-14 review round 3 (independent Opus gate, "changes-required"):**
found and fixed two further code defects in round 2's own output, plus
corrected two documentation-accuracy issues in round 2's own notes (the two
items above already folded in).

1. **The `/tools/` H1 override was dead code.** Round 2 built the override
   machinery (`tpm_get_title_part_overrides()`, `tpm_get_title_parts()`,
   `inc/article-meta.php`) and had `page.php` resolve and pass down
   `$title_parts`, but flagged, correctly, that
   `template-parts/content/article-header.php` (the template that actually
   renders the `<h1>`) still called `tpm_split_title( get_the_title( $post_id ) )`
   directly, never reading the passed-down value. That gap meant `/tools/`,
   one of the 18 live, indexed URLs, was still rendering `<h1>Tools</h1>`
   with "Free Pet Calculators" demoted to the deck, the exact regression the
   override exists to fix. Closed: `article-header.php` now reads
   `$args['title_parts']` (falling back to calling `tpm_get_title_parts()`
   itself, then to bare `tpm_split_title()`, if that arg is ever absent).
   **Deliberately left alone:** the nav-menu filter
   (`tpm_filter_nav_menu_item_title()`) still calls `tpm_split_title()`
   directly, bypassing the override map on purpose: the header's "Tools"
   nav label should stay short even though the page's own `<h1>` should not;
   a comment at that filter now says so, so a future pass doesn't "fix" it
   into false consistency.
2. **The FAQ JSON-LD placeholder guard was a no-op against real content.**
   `tpm_dom_is_script_placeholder_node()` (`inc/content-blocks.php`) tested
   only `XML_TEXT_NODE`, but wpautop() (part of the real `the_content`
   pipeline, already run before this pass ever sees the content) wraps any
   bare placeholder line in a `<p>`, so the node this predicate actually
   receives is a `<p>` ELEMENT, not the bare text node it assumed. Reproduced
   on all 11 wave-1 pages carrying a FAQPage block: the answer-collection
   loop in `tpm_dom_build_faq_accordion()` never broke on the wrapped
   placeholder, so it absorbed that `<p>` into the last FAQ answer, and the
   restored `<script>` ended up nested inside
   `details.tpm-faq__item > div.tpm-faq__a`, not sitting at the top level
   search engines expect. The JSON-LD itself was always restored
   byte-identical either way: this was a DOM-position defect, not a data
   defect. Fixed: the predicate now unwraps a single-text-child element
   before the text-node check, recognising both the wpautop'd shape it
   actually receives and the bare-text-node shape as a fallback.
3. **The "no CTA beats an irrelevant CTA" behaviour, confirmed as correct,
   not a bug.** Re-verified against all 17 wave-1 pages: the in-article tool
   CTA (item 3, round 2, above) renders on none of them, and that remains
   exactly right: see that item for why, and for the Pet Cost Calculator as
   the documented tool that unlocks it. No code change.
4. **The "7 of 18" false-fire figure was overstated and unreproduced.**
   Round 2's item 6 above cited "7 of 18 live pages" for how often the
   pre-fix, non-entity-decoded truncation guard false-fired, a number
   carried forward without being independently reproduced, which is exactly
   what this project's own review culture (CLAUDE.md's repeated "reproduced
   by replaying," not "reproduced by inspection") warns against. Round 3
   replayed all 17 wave-1 page bodies through the real `the_content`
   pipeline (`wptexturize()`/`wpautop()`, loaded directly from WordPress
   core, ahead of this theme's own passes) and fed the pre-fix comparison
   the exact protected/serialized string pairs each guard actually receives:
   `inc/content-blocks.php`'s guard false-fires on 7 of the 17 pages,
   including `/tools/` outright, close to the original figure in
   magnitude, off in its exact basis (18 live pages vs. 17 wave-1 bodies,
   and not distinguishing which of the two guards); `inc/content-nav.php`'s
   twin guard false-fires on 0 of 17, because by the time that pass runs
   the content has already been through one DOMDocument round-trip
   (content-blocks.php's own), which already decoded every entity that
   would otherwise trip it. Both docblocks now state the reproduced numbers
   and the methodology instead of an unqualified count. **The fix itself
   was already correct**: this is a documentation correction, not a code
   change; both guards fire 0/17 today. **Correction, round 4:** this
   item's own `/tools/` delta/tolerance pair (delta 61 vs tolerance 54) was
   itself measured on a replay that stopped at wpautop(): a REAL, but
   PARTIAL, pipeline. Under the FULL production chain: adding
   `shortcode_unautop()` -> `do_shortcode()` (the real
   `[pawmanual_calculator]` shortcode) -> `tpm_filter_internal_links()` at
   priority 30: `/tools/` measures delta 60 vs tolerance 45 instead. The
   guard still does not false-fire either way (both numbers clear their
   own tolerance), so nothing here changes the guard's correctness; it is
   the exact "unqualified number carried forward" failure mode this round
   exists to stamp out, so it is flagged rather than silently swapped:
   the 7-of-17 / 0-of-17 page counts above are the numbers that matter and
   both hold, independently reproduced twice, under the full chain.

**Client design decisions, 2026-09-14 (palette + `/tools/` H1):** after seeing
the design draft, the client made two decisions, both theme-side, both
scoped to the files this section already names.

- **Palette: terracotta becomes primary, navy becomes the accent.** `brand`
  (the primary scale, `tailwind.config.js`) is now terracotta: `50 #FDF4F0,
  100 #F8E1D6, 200 #EFC4B1, 500 #C4643D, 600 #B4522F, 700 #8F3F23,
  900 #5C2715`, chosen by the client from four candidates after a contrast
  review. Measured: white text on `brand-600` (#B4522F) is **5.01:1**, which
  clears WCAG AA's 4.5:1 floor but with little headroom: **do not lighten
  #B4522F**, the next shade up already fails. The old green scale is gone
  entirely, not just relabelled.
- **`clay` (the old terracotta *accent*) is renamed to `accent` and
  repurposed as navy**: `50 #EEF2F7, 100 #D9E2EE, 200 #BBCADF, 600 #2A3F5C,
  700 #1F3047`, rather than leaving navy values inside a scale still named
  "clay": a WordPress-literate SEO specialist reading `bg-clay-50` in a
  template and finding it renders navy is exactly the kind of trap a rename
  avoids. The only two consumers, both two occurrences of `bg-clay-50` /
  `text-clay-700` (the category-chip pill), live in
  `template-parts/content/related-reading.php` and
  `template-parts/content/child-grid.php`, updated to `bg-accent-50` /
  `text-accent-700`. Confirmed zero `clay-` references survive anywhere in
  the theme's `.php`/`.css` source after the rename.
- **`/tools/` H1 becomes "Free Pet Calculators."** Round 2's open question
  above (item 4): whether `/tools/` should render "Tools" (the WP post
  title, "Tools: Free Pet Calculators," kept whole) or the shorter search
  phrase, is now resolved: the client wants the **rendered `<h1>`** to be
  exactly "Free Pet Calculators," no deck. `tpm_get_title_part_overrides()`
  (`inc/article-meta.php`) now seeds `'tools' => array( 'h1' => 'Free Pet
  Calculators', 'deck' => '' )` instead of `true`. Why now, and why this
  wording: "Tools" is a navigation word, not a phrase anyone searches;
  "Free Pet Calculators" is. Changing a live H1 is normally a ranking risk.
  Whether `/tools/`, or any of the 18 live wave-1 URLs, is actually
  indexed isn't something this repo or its tooling can verify: "published"
  and "indexed" are different states, and only Google Search Console's
  Pages / URL Inspection report can settle the second (see
  `content/SITEMAP.md`'s indexing note). That verification hasn't been done,
  so this change accepts the ranking risk as unresolved rather than treating
  it as waived. The nav-menu label stays "Tools," unchanged, per round 3 item 1 above:
  that decision was already right and this doesn't touch it.
  `tools/tests/run.php`'s ROUND 3, FINDING 1 case now asserts the new
  behaviour (h1 exactly "Free Pet Calculators", no deck paragraph) against
  the real `article-header.php` template, and was proven to still be a
  regression test by mutation: reverting the override to `true` fails it
  (3 of 94 assertions fail); restoring it returns to green (94/94).
- **Typography is unchanged, deliberately.** Playfair Display / Inter /
  Geist stay exactly as they are (§3.1, §3.3), this pass touches color only.
- **Breadcrumb H1 mismatch: RESOLVED, see the deploy-gate review below.**
  This note originally flagged `inc/breadcrumbs.php`'s fallback trail as
  printing the raw post title instead of the split H1; it no longer does.

**Deploy-gate review, 2026-09-14 (post-terracotta-rebrand), findings closed:**
the Opus gate that approved the palette swap above flagged what the swap
itself hadn't closed. All three are now closed, not just noted.

1. **A third colour scale, `caution` (`tailwind.config.js`), for the "Pet
   emergency?" callout and any future safety warning.** `brand` becoming a
   warm red-orange terracotta broke reusing Tailwind's stock `amber-*` for
   warnings the way the pre-rebrand green palette safely could: every warm
   hue Tailwind ships sits close to `brand-600` (#B4522F, 16°) in hue
   alone, amber and red-600 both ~16° away, rose/crimson only ~29° away,
   so swapping one stock warm colour for another buys no real separation.
   The pre-rebrand regression this replaces measured dE76 (CIE Lab)
   separations of 30.5 (icon fill) and just **8.4** (heading text) against
   the new brand colours: 8.4 reads as "the same brown" to the eye, and
   that was the actual bug; hue rotation can't fix it. The fix is a scale
   that's DARKER, not just differently-hued: `caution-700`/`caution-900`
   (`#9F1239`/`#5E0A22`, pulled far past `brand`'s own lightness range) now
   measure dE76 ≈ 31.2 and 30.0 against the brand colours they sit beside:
   separation restored by weight, not hue.
   `template-parts/content/emergency-callout.php` is the only file that may
   reach for `caution-*` (its own docblock carries the full reasoning and
   the measurements); the compiled `theme.css` confirms only
   `caution-700`/`caution-900` are actually in use, matching the one
   component this scale exists for.
2. **The homepage's own palette pass.** The `brand`/`accent` swap in
   `tailwind.config.js` only reaches a template once someone edits its
   classes: `template-parts/home/*.php` and `template-parts/tool-card.php`
   still had pre-rebrand `bg-neutral-900`/`hover:bg-neutral-800` black CTAs
   and a leftover `border-amber-600 bg-amber-500` decorative dot, plus a
   genuinely dead search `<input>` in the filter bar (no wrapping `<form>`,
   no action, the same "looks live, does nothing" bug requirement 4 fixed
   for the header, reproduced here). Closed: every homepage CTA/pill/badge
   now uses `brand-600`/`brand-700` in place of `neutral-900`/`neutral-800`
   and `amber-*`; the dead filter-bar search input is removed outright
   rather than fixed, since the header's `#tpm-search-panel` is already the
   site's one real search entry point; the hero's featured-tool card and the
   tools-grid cards now share `.tpm-card`/`.tpm-tool-cta` with every other
   card grid on the site instead of hand-rolled borders, so the next
   palette change only has to touch those two shared classes.
3. **The breadcrumb H1 mismatch flagged as open above: resolved.**
   `inc/breadcrumbs.php`'s fallback trail (used when RankMath is inactive)
   now resolves its final crumb through
   `tpm_get_title_parts( $post->ID )['h1']` behind a `function_exists()`
   guard: falling back to raw `get_the_title()` only if
   `inc/article-meta.php` hasn't loaded, so this can never fatal on a
   load-order change. `/tools/` breadcrumb and `<h1>` now both read "Free
   Pet Calculators."

**Palette coherence, verified 2026-09-14:** `npm run build:css` rebuilt clean
after all three findings above were closed; a second build to a scratch path
outside the repo is byte-for-byte identical to the committed `theme.css`. All
four old green RGB triples (`45 106 79`, `35 86 64`, `241 247 244`,
`220 235 227`) are absent from the compiled CSS; the terracotta triple
`180 82 47` (`brand-600`) and both live `caution` shades are present. A
repo-wide grep of every `.php`/`.css` source file for `amber-` turns up
exactly two hits, both inside `emergency-callout.php`'s own docblock (the
historical dE76 comparison in finding 1 above), no live class anywhere.
Every class in the shared contract (`tpm-faq`, `tpm-card`, the
`tpm-key-takeaway` family, `tpm-toc__link`, `tpm-tools-strip`, `tpm-chip`,
`tpm-search-form`, `sm:inline`) still compiles. `php -l` is clean across all
43 theme+plugin PHP files; `php tools/tests/run.php` is 94 passed, 0
failed, unchanged from before this pass, confirming the palette work
touched no logic the suite covers.

**Deploy reminder, standing (§12):** the compiled `assets/css/theme.css` MUST
be rebuilt (`npm run build:css`) and the result committed BEFORE zipping the
theme for upload: every new class introduced by this render pipeline (the
`brand`/`accent` color scales, `.tpm-toc`, `.tpm-faq`, `.tpm-key-takeaway`,
`.tpm-tools-strip`, `.tpm-tool-cta`, `.tpm-card`, `.tpm-search-form`, …) only
reaches Simply.com if Tailwind's content scan has actually run against the
`.php` files that use them since they were added. §12 step 1 already says
"compile the CSS" first for exactly this reason: this is a reminder to
re-verify it, not a new step, because a deploy built from a stale
`theme.css` fails silently: the page still renders, just unstyled or
half-styled, with no error anywhere to catch it.

**Em-dash purge, 2026-09-15: done, with one build-breaking gap caught and closed
after the fact.** Content-wide, `content/` went from roughly 2,779 em dashes to
zero outside `(not page copy)` sections and non-publishing frontmatter fields
(§9's rule above); every en dash in a numeric range survived untouched
(verified by an independent count before/after). Two review rounds on the
purge itself: the first confirmed the dangerous failure modes never
happened (no range corrupted, no hedge dropped, FAQ-schema parity held,
only the eight pre-authorized H1s changed) but found the sweep's directory
inventory had silently skipped `content/dogs/grooming/index.md` entirely,
and that frontmatter (`seo-title`/`meta-description`) had been wrongly
treated as exempt even though it publishes verbatim as the RankMath
title/description. A second round closed both, plus the five live-rendering
calculator plugin strings the first sweep also missed.

That second round's own gate then caught the one that actually mattered:
the eight-H1 correction (`### The two things...` below is unrelated; this is
a separate, now-resolved item) had rewritten the eight unpublished pages'
H1s but had **not** updated their matching entries in
`tools/wxr/publish-registry.json`, despite this file's own §13 and the
rule above both asserting, incorrectly at the time, that the two were
changed "in lockstep." `node tools/wxr/generate.js --all` was failing
outright with eight title-drift errors as a result, which would have
blocked every future wave, not just these eight pages. Fixed directly
(the registry's eight `title` values now match the files' current H1s
exactly; `date`/`filePath`/every other URL untouched) and re-verified:
both `--wave 1` and `--all` complete cleanly. The lesson worth keeping: a
worker or a gate asserting two files were "changed together" is not the
same as verifying it by actually running the build that depends on both
agreeing: do that run, don't just read the diff.

### The two things that block launch and cannot be fixed in code

1. **RESOLVED (2026-09-14): do not revert.** All six trust pages
   (`content/about.md`, `content/contact.md`, `content/affiliate-disclosure.md`,
   `content/terms.md`, `content/privacy-policy.md`, and
   `content/editorial-process.md`) now carry real details (named publishers,
   a real contact email, a real affiliate/program list, a dated terms
   document, a genuine "not currently vet-reviewed" statement) and are
   **published and live** as of 2026-09-10. None of them hold a `[PLACEHOLDER]`
   any more. **Do not set any of these six pages back to Draft**: that would
   404 six live, indexed, YMYL-adjacent trust URLs and remove exactly the
   publisher-identity surface this rule used to protect. The one remaining
   open item is editorial, not a placeholder: `content/terms.md` governs the
   site under Danish law on a site aimed at US readers, worth a legal review,
   but not a publish-blocker in the way an unfilled `[STATE]` was.

   **Why this item used to say the opposite.** The placeholders were filled
   directly in WordPress admin, not in `content/`: the repo's own copies of
   these six files still read the old `[PLACEHOLDER]` text for days after the
   live pages went out clean, and this section's previous wording ("keep
   those five as Drafts when publishing wave 1") was written against that
   stale repo state. Read literally, it told a future session to un-publish
   pages that were already live and correct. `content/` has now been
   re-synced to match what is actually live. **Standing rule going
   forward: when WordPress is edited directly** (a placeholder fill, a
   style pass, anything done in WP admin rather than through the
   `tools/wxr/` import), **bring the same edit back into the matching
   `content/*.md` file before the session ends.** `content/` is not
   guaranteed newer than live for any already-published page (see
   `content/SITEMAP.md`'s "Live-vs-repo drift" note for a second, live
   instance of this same gap); skip the sync and the next person who copies
   a page's body out of `content/` and back into WordPress, hand-pasting a
   GEO rewrite, for instance, silently overwrites the live fix with the
   stale repo version it was meant to replace.
2. **The fact-check pass hasn't run.** Each page's Notes section lists its own
   flags. Publishing unverified veterinary claims is the one mistake here that is
   genuinely expensive to undo.
