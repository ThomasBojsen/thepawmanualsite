---
url: /tools/
page: Tools hub
seo-title: "Free Pet Calculators & Tools | ThePawManual"   # 43 chars
meta-description: "Free dog and cat calculators that run right in your browser — no signup. Age calculators live now; food, cost, and breed tools are in development."   # 146 chars
status: draft-v1
---

# Tools: Free Pet Calculators

ThePawManual's tools are free calculators that run entirely in your browser — no account, no email gate, just numbers you can trust matched to your dog or cat. Two are live today: the [dog age calculator](/tools/dog-age-calculator/) and the [cat age calculator](/tools/cat-age-calculator/), both built on veterinary-informed aging models instead of the flat "multiply by 7" myth. Six more are in development and paired to guides you can already read in full.

## Live now

### [Dog Age Calculator](/tools/dog-age-calculator/)

Enter your dog's age and breed size to get their age in human years, using a size-aware model — small breeds age slower in their later years, giant breeds faster. [Open the calculator →](/tools/dog-age-calculator/)

### [Cat Age Calculator](/tools/cat-age-calculator/)

Enter your cat's age for an instant human-year equivalent, based on the standard feline aging model (year one counts as 15, year two brings the total to 24, and every year after adds about 4). [Open the calculator →](/tools/cat-age-calculator/)

## In development

We're building the rest of the toolkit deliberately, one working calculator at a time, rather than shipping placeholder tools. Here's what's next, paired with the guides that already cover the same ground by hand:

- **Dog Food Calculator** — until it ships, work the math yourself with [how much should I feed my dog?](/dogs/food/how-much-should-i-feed-my-dog/)
- **Cat Food Calculator** — see [how much should I feed my cat?](/cats/food/how-much-should-i-feed-my-cat/) in the meantime
- **Pet Cost Calculator** — the full itemized numbers are already in [how much does a pet cost?](/pet-guides/pet-costs/)
- **Dog Breed Selector** — browse by lifestyle now in the [dog breed guides](/dogs/breeds/)
- **Cat Breed Selector** — browse by lifestyle now in the [cat breed guides](/cats/breeds/)
- **Pet Name Generator**

No timeline promises here — each tool ships when it's actually built, matching how the two live calculators were done.

## Frequently asked questions

### What tools does ThePawManual offer?

Two free calculators are live: a [dog age calculator](/tools/dog-age-calculator/) and a [cat age calculator](/tools/cat-age-calculator/), both converting your pet's age into human years using veterinary-informed models. Six more tools — covering food portions, pet costs, breed selection, and pet names — are in development.

### Are the calculators free to use?

Yes. Every calculator on ThePawManual is free, runs in your browser, and doesn't require an account or email address.

### How accurate are the calculators?

They're built on established aging models — a size-aware formula for dogs and the standard feline model for cats — not the outdated "multiply by 7" rule. They give a solid estimate, not a diagnosis; a veterinarian is still the right call for anything health-related.

### What tools are coming next?

Food portion calculators for dogs and cats, a pet cost calculator, breed selectors for both species, and a pet name generator are all in development, in roughly that order.

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "What tools does ThePawManual offer?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Two free calculators are live: a dog age calculator and a cat age calculator, both converting your pet's age into human years using veterinary-informed models. Six more tools — covering food portions, pet costs, breed selection, and pet names — are in development."
      }
    },
    {
      "@type": "Question",
      "name": "Are the calculators free to use?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Yes. Every calculator on ThePawManual is free, runs in your browser, and doesn't require an account or email address."
      }
    },
    {
      "@type": "Question",
      "name": "How accurate are the calculators?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "They're built on established aging models — a size-aware formula for dogs and the standard feline model for cats — not the outdated \"multiply by 7\" rule. They give a solid estimate, not a diagnosis; a veterinarian is still the right call for anything health-related."
      }
    },
    {
      "@type": "Question",
      "name": "What tools are coming next?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Food portion calculators for dogs and cats, a pet cost calculator, breed selectors for both species, and a pet name generator are all in development, in roughly that order."
      }
    }
  ]
}
</script>
```

---

## Notes (not page copy)

- **Wave-1 page.** Per `SEO-STRATEGY.md` §4, this hub and the two live calculator pages publish in Wave 1 alongside the trust set and homepage.
- The six "in development" tools are deliberately **not linked** — no `/tools/*` page exists for them yet, and linking would 404 at launch. If a calculator ships, add its live-calculator card here (matching the "Live now" section pattern) and move it out of "In development."
- The "In development" list links to the *content* pages that already cover the same math/decision by hand (feeding guides, cost guide, breed hubs) — this keeps the section useful today instead of just a promise.
- Order of "In development" list follows `SITEMAP.md`'s Tools table order, which itself follows the content-tool pairing priority in `content/README.md` §3 (feeding guides ↔ food calculators, cost guide ↔ cost calculator) — build calculators in that order per that doc.
- No card/grid markup specified here since the theme's `template-parts/tool-card.php` (per `CLAUDE.md` §4.1) renders the homepage tool grid from `inc/tools-data.php`; this page's job is the indexable SEO copy for `/tools/`, not to duplicate that component. Dev should decide whether this page also renders the same card component or stays copy-first with plain links — flagging for dev, not deciding here.
