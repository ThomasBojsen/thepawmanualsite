# Deploy files

The files that get uploaded to thepawmanual.com, kept in git so everyone on the team has the exact versions that went live.

**Current release: 2026-10-22, the five new tools** (Dog Food, Cat Food, Pet Cost, Dog Breed Selector, Cat Breed Selector, plus the homepage filter). Built from `main` at commit `55ecfb7`.

**Theme update, 2026-10-22 (tool headers):** the five new tool pages render their bare tool name as the H1 with no deck, so their header matches the Dog/Cat Age Calculator pages (`tpm_get_title_part_overrides()` in `inc/article-meta.php`). Compared with the previous theme zip, only `inc/article-meta.php` differs. Built with `git archive` so line endings stay LF on Windows.

| File | What it is | Where it goes |
|---|---|---|
| `pawmanual-calculators-2026-10-22-tools.zip` | The calculators plugin, version 0.2.0 | WP admin → Plugins → Add New Plugin → Upload Plugin → "Replace current with uploaded" |
| `thepawmanual-theme-2026-10-22-tool-headers.zip` | The theme | WP admin → Appearance → Themes → Add New Theme → Upload Theme → "Replace active with uploaded" |
| `thepawmanual-wave-tools.xml` | The five new tool pages (they import as drafts) | WP admin → Tools → Import → WordPress. Then publish the five drafts. The message "Tools: Free Pet Calculators already exists" is expected |
| `tools-siden-ny-tekst.txt` | New body for the live `/tools/` page | Paste over the page's whole content in the Code editor. Also set the RankMath description (it is in `content/tools/index.md`) |
| `pet-costs-ny-tekst.txt` | Full new body for the live `/pet-guides/pet-costs/` page (the live text plus seven corrected price lines; checked sentence by sentence against live on 2026-10-22) | Paste over the page's whole content in the Code editor |

`gamle-til-fortryd/` ("old, to roll back") holds the theme and plugin zips that were live before this release. Upload them the same way to undo.

**Rules** (see CLAUDE.md §11 and §12):
- Build zips from `main` only, and run `npm run build:css` first.
- Before uploading, compare the new zip with the previous one by file name and size (`unzip -l`). Only the files you changed on purpose may differ.
- Give every new zip a new name. Move the previously live zip into `gamle-til-fortryd/` and delete older ones from there, since git history keeps them anyway.
