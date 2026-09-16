/** @type {import('tailwindcss').Config} */
module.exports = {
  // Tailwind scans these files for class names and emits only the CSS that is
  // actually used. The plugin (pawmanual-calculators) is deliberately NOT
  // scanned: it ships its own hand-written CSS so it stays theme-independent.
  content: [
    './wp-content/themes/thepawmanual/**/*.php',
    './wp-content/themes/thepawmanual/assets/js/**/*.js',
  ],
  theme: {
    extend: {
      // The Aura design's three typefaces, exposed as utility classes:
      // `font-sans` (Inter, the base), `font-playfair` (display headings)
      // and `font-geist` (UI text).
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        playfair: ['"Playfair Display"', 'ui-serif', 'Georgia', 'serif'],
        geist: ['Geist', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      // The two brand colour scales. `brand` is the primary terracotta,
      // `accent` a navy used sparingly for contrast against it. The client
      // picked this pairing from four candidates after a contrast review
      // (2026-09-14): white text on `brand-600` (#B4522F) measures 5.01:1,
      // which clears WCAG AA (4.5:1) but with little headroom — do not
      // lighten #B4522F, that headroom is gone at the next shade up.
      // Everything else (backgrounds, borders, body text) stays Tailwind's
      // stock `neutral` scale — these two are additive, not a palette
      // replacement.
      colors: {
        brand: {
          50: '#FDF4F0',
          100: '#F8E1D6',
          200: '#EFC4B1',
          500: '#C4643D',
          600: '#B4522F',
          700: '#8F3F23',
          900: '#5C2715',
        },
        accent: {
          50: '#EEF2F7',
          100: '#D9E2EE',
          200: '#BBCADF',
          600: '#2A3F5C',
          700: '#1F3047',
        },
        // `caution` is a THIRD, deliberately separate scale — not a shade of
        // `brand`, not Tailwind's stock amber/red. It exists so "this is a
        // safety warning" can be named in the config, not left to whichever
        // template author reaches for `amber-*` next. Against `brand-600`
        // (#B4522F, a warm red-orange terracotta) every warm caution hue
        // sits close in hue alone — amber 16° away, red-600 16° away,
        // rose/crimson 347° only ~29° away (measured 2026-09-14, pre-deploy
        // review). Hue rotation cannot buy separation here; rose was picked
        // because at equal saturation it is the warm hue with the most
        // headroom, and separation is then made real with LIGHTNESS/WEIGHT
        // instead: `caution-700`/`caution-900` sit far darker than
        // `brand-600`/`brand-700` ever go, so the callout that uses them
        // reads as a solid, saturated alert rather than a pale tint that
        // photographs as "one more terracotta box." See
        // template-parts/content/emergency-callout.php AND footer.php's
        // "Pet Emergency?" column heading (approved 2026-09-14 as the site's
        // second emergency affordance: same wording, same targets, rendered
        // sitewide including on pages that carry no inline callout) for the
        // two places this scale is used — nothing else may reach for it, or
        // "caution" stops meaning caution.
        caution: {
          50: '#FEF2F4',
          100: '#FDE3E7',
          200: '#F9C6CE',
          600: '#C2183C',
          700: '#9F1239',
          900: '#5E0A22',
        },
      },
    },
  },
  plugins: [],
};
