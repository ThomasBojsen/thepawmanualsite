# Page images

A page's image is its WordPress **Featured image**. The theme prints it under
the title and deck and above the Key takeaway box
(`template-parts/content/page-image.php`, built by `tpm_get_page_image()` in
`inc/page-image.php`). A page with no Featured image prints nothing and looks
exactly as before.

The assignment lives **only in the WordPress database**. It is not in
`content/*.md`, not in the WXR file, and not in the theme, so it is lost when a
page is deleted and re-imported (the `docs/LAUNCH-RUNBOOK.md` path). The table
below is the record. After any delete-and-reimport, set each image again.

This file lives in `docs/`, not `content/`: `tools/wxr/generate.js` parses every
Markdown file under `content/` that is not on its exclusion list as a page, and
fails the whole import on one without frontmatter.

## Who has which image

Uploaded to the Media Library in October 2026 (`/wp-content/uploads/2026/10/`).

| Page | Media Library file | Shape | Alternative Text (replace the short one now in the library) |
|---|---|---|---|
| `/pet-guides/` | `Pet-Guides.webp` | 1024 square | A golden retriever and a gray tabby cat on a woven rug beside a notebook and a bowl of food. |
| `/pet-guides/pet-costs/` | `Pet-Costs.webp` | 1050x1008 | A ginger cat and a Yorkshire terrier on a garden path with a terracotta piggy bank between them. |
| `/pet-guides/new-pet-checklist/` | `New-Pet-Checklist.webp` | 1024 square | A golden retriever puppy and an orange-and-white cat in a home set up for pets, with a baby gate and a play pen. |
| `/pet-guides/pet-safety/` | `Pet-Safety.webp` | 1024 square | A golden retriever and a ginger-and-white cat beside a table of foods toxic to pets, such as chocolate, grapes, onion and garlic. |
| `/pet-guides/pet-travel/` | `Pet-Travel.webp` | 1024 square | A smiling man crouching beside a dog in a harness at an airport, next to a green suitcase. |
| `/pet-guides/pet-insurance/` | `Gemini_Generated_Image_albj56albj56albj-scaled.webp` | 2560x1429 (16:9) | A person in a knitted jumper holding the paw of a golden retriever on a wooden living-room floor. |
| `/tools/dog-age-calculator/` | `Dog-Age-Calculator.webp` | 1024 square | A corgi-type dog, a husky and three puppies sitting together on a woven rug in a sunny garden. |
| `/tools/cat-age-calculator/` | `Cat-Age-Calculator.webp` | 1024 square | Adult cats and kittens of different ages around a tiered stone fountain in a sunny flower garden. |
| `/dogs/` | `Dogs.webp` | 1024 square | An adult dog and five puppies on a knitted rug by a sunny window. |
| `/cats/` | `cats.webp` | 1024 square | Five kittens of different colors cuddled together on a knitted rug by a window. |

`pet-tools.webp` is also in the Media Library but is not assigned to any page.
It shows a cat and a dog looking at a calculator, so it probably belongs to
`/tools/`; nothing here uses it yet.

Alternative Text matters: WordPress prints exactly what that field holds. An
empty field renders `alt=""`, which marks the image as decorative and gives
search engines nothing.

**Decision before you set `Pet-Safety.webp` and `New-Pet-Checklist.webp` (both
pages are already live):** each image has the same cork board with an
"EMERGENCY CLINICS" poster, and the phone numbers on it ((888) 233-5535 and two
more) are invented by the image generator. They are too small to read at the
size the page shows, but they can be read in the larger file, when zooming, and
by image search or OCR. The real number on the Pet Safety page is the ASPCA
Animal Poison Control line, (888) 426-4435, so a reader could dial a number
this site never vetted. Either regenerate those two images without legible text
on the poster (or retouch that area) and use the new files, or accept the
poster knowingly. Until you decide, leave those two pages' Featured image
unset: the other eight do not depend on them.

The pet-insurance image keeps its generator file name. The name is an image-search
signal and WordPress cannot rename a file, so if that matters: upload it again
as `pet-insurance.webp` (no more than 2560px in width or height, so
WordPress adds no `-scaled` suffix), set it as the Featured image, fill in its Alternative Text, update
this table, then delete the old attachment (which also frees the 4.3 MB
original WordPress keeps next to it).

## Shipping this change

1. Upload the new theme zip: **Appearance → Themes → Add New Theme → Upload
   Theme**, choose the zip, **Install Now**. When WordPress says the theme is
   already installed, click **Replace installed with uploaded** (older
   WordPress: "Replace current with uploaded"). ThePawManual stays active.
2. Set the Featured images (next section): eight straight away, and Pet
   Safety and New Pet Checklist once you have decided about their poster (see
   the flag above).
3. Open each page in a private window and hard-refresh. If the image is not
   there, purge any cache plugin or host cache first (none is documented, so
   this is unverified), then check you edited the page itself and not its
   parent hub.

Building the zip (the zip itself is not in git): from the repo root, with the
branch checked out, `cd wp-content/themes && zip -qr -X
~/Desktop/thepawmanual-deploy/thepawmanual-theme-<date>-<what>.zip thepawmanual
-x '*.DS_Store'`. Use a new file name each time, then compare it with the
previous zip as CLAUDE.md section 11 describes (file names and sizes, and the
`depth` check on `header.php`).

Do **not** run the delete-and-reimport path in `docs/LAUNCH-RUNBOOK.md` for
this: it would wipe the assignments. Rollback is per page: **Remove featured
image**, then **Update**. The feature does nothing without a Featured image, so
the theme zip only needs reverting if the theme code itself misbehaves (the
previous zip is `~/Desktop/thepawmanual-deploy/thepawmanual-theme.zip`).

## Attaching an image (WP admin, block editor)

1. **Pages → All Pages**, open the page.
2. In the right sidebar (open it with the Settings icon if it is hidden), pick
   the **Page** tab, expand **Featured image**, click **Set featured image**.
3. In the **Media Library** tab, click the file.
4. In **Attachment Details**, fill in **Alt Text** (it saves on its own; wait
   for "Saved."), then click **Set featured image**.
5. Click **Update**. The page must be Published.

With the Classic Editor plugin the same **Featured image** box sits in the
right-hand column of the edit screen.

RankMath, which owns the social meta, will probably pick the Featured image as
the page's default share picture too. The Media Library slugs (`pet-safety-2`,
`dogs-2` and so on) also mean WordPress has an attachment page for each image;
RankMath's "Redirect attachments" setting should be on so those thin pages are
not indexed. Worth a look by the SEO specialist; neither was checked here.

## Where else the image shows

Since 2026-10-21 the same Featured image is also the picture on that page's
card wherever it is linked as a card: the Related Reading block at the foot of
every page, and the child-page grid on hub pages
(`template-parts/content/card-image.php`, `tpm_get_card_image()`). It is shown
square, lazy-loaded and small, so it costs nothing above the fold. A page
without a Featured image gets a pale paw placeholder in its cards, and the
placeholder disappears the moment an image is set: one field fills both the
page and every card that points to it.

## Preparing new images

- Any shape works, and the image is shown whole, never cropped. On desktop
  (1024px and up) it is 400px wide beside the title, so a square is 400px
  tall and a 16:9 picture about 225px. Below that it is drawn at most 448px
  tall: a square comes out 448px wide, a 16:9 picture
  about 800px wide, and nothing is ever wider than the 960px column. On a
  phone it fills the width. Square and 16:9 suit the slot. A very narrow
  portrait (taller than about 1:1.9) is held to 240px wide and so ends up
  taller than 448px: avoid those.
- Name the file for what it shows before uploading (`pet-insurance.webp`, not
  `Gemini_Generated_Image_...`).
- Upload WebP at least 800px on the long side; smaller than that is shown at
  its own size, not stretched (down to 240px, the narrowest it is ever drawn).
  A very large upload is fine: WordPress scales anything over 2560px in width
  or height down (keeping the original on disk) and makes the smaller
  versions.
- Keep WordPress's 768px size switched on. The page serves a file no wider than
  twice the slot (about 900px for a square, 1600px for a 16:9 picture), which
  keeps the original and the scaled copy off phones, but that only holds while
  the 768px version exists. A plugin that removes it brings the heavy file back.
- No legible text, signs or phone numbers in generated images: the generator
  invents them. Check the picture at full size before uploading.
- Do not put an image at the top of the page body to fill this slot. Only the
  Featured image is sized for the slot, loaded with high priority, and placed
  above the Key takeaway box. If a page body ever does hold an image, give it
  `loading="lazy"` so it does not compete with the page image for priority.

## What it costs on the page

The image sits above the Key takeaway box, so the answer starts lower. On a
375px phone a square image fills about half the screen and the box starts
below the first screen; a 16:9 image costs about 215px instead of 370px. On a
1280px desktop the image sits beside the title, so the box starts just below
a 400px square instead of under title plus image. Below 1024px the
knob is `TPM_PAGE_IMAGE_MAX_HEIGHT` in `inc/page-image.php` (448 now; about 360
moves the box up some 90px). No CSS rebuild is needed to change it.

## Open items for whoever continues

State on 2026-10-21: the theme with this feature is live, and the owner has set
Featured images on at least Dogs, Cats, Pet Costs and Pet Insurance. Nothing
about the images themselves is decided on the other pages.

1. **Image size: decided 2026-10-21 (option B).** The owner found the old
   layout (a 448px square left-aligned beside a 960px Key takeaway box) did
   not fit, ruled out cropping again (a 16:9 crop zooms in and cuts the
   animals), and chose B: from 1024px up the title and deck sit on the left
   and the whole image stands in a 400px column on the right
   (`TPM_PAGE_IMAGE_DESKTOP_WIDTH`, written literally into
   `article-header.php`'s grid class as well). Below 1024px nothing changed:
   the image sits under the title, up to 448px tall, as before. A page without
   a Featured image gets no grid.
2. **Two images carry an invented emergency poster** (`Pet-Safety.webp`,
   `New-Pet-Checklist.webp`): see the decision above. Their pages have no
   Featured image yet.
3. **`pet-tools.webp` is unassigned**: it probably belongs to `/tools/`.
4. Nothing was checked against RankMath (social image, attachment-page
   redirects) or a cache plugin.
