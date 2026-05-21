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
    },
  },
  plugins: [],
};
