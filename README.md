# ThePawManual

A hub of free pet calculators and tools for dog and cat owners
(production: [thepawmanual.com](https://thepawmanual.com)).

This repository holds the two custom components of the WordPress site:

- **`wp-content/themes/thepawmanual/`** — the custom theme: site chrome,
  homepage, and page templates, styled with Tailwind CSS.
- **`wp-content/plugins/pawmanual-calculators/`** — the plugin that provides
  the interactive calculators.

Everything else in the repo is development tooling and is **not** deployed to
the server.

## Documentation

**[`CLAUDE.md`](CLAUDE.md) is the source of truth** for the architecture,
conventions, build plan, and current status. Read it first.

## Local development

The site runs locally in [LocalWP](https://localwp.com/). The repo's theme and
plugin folders are symlinked into LocalWP's `wp-content/`, so edits made here
are live immediately. See `CLAUDE.md` §8 for the full setup guide.

### Building the theme CSS

The theme's stylesheet is compiled from Tailwind. The compiled output
(`wp-content/themes/thepawmanual/assets/css/theme.css`) is committed to git, so
the production server needs no build step.

```sh
npm install         # one-time: install the build tooling
npm run build:css   # compile once, minified
npm run watch:css   # recompile automatically on every save
```

The plugin does **not** use Tailwind — its CSS is hand-written and
self-contained so the calculators look correct under any theme.
