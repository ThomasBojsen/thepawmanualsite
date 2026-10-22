---
url: /tools/
page: Tools hub
seo-title: "Free Pet Calculators & Tools | ThePawManual"   # 43 chars
meta-description: "Free dog and cat tools, no signup: age, food and pet cost calculators plus breed selectors. Works in pounds or kilograms, dollars or euros."   # 139 chars
status: draft-v1
---

# Tools: Free Pet Calculators

ThePawManual's tools are free calculators that run entirely in your browser: no account, no email gate, just numbers you can trust matched to your dog or cat. Seven are live: age calculators for dogs and cats, food calculators for dogs and cats, a pet cost calculator in dollars or euros, and breed selectors for both species, each built on published veterinary formulas or researched data instead of rules of thumb.

## Live now

### [Dog Age Calculator](/tools/dog-age-calculator/)

Enter your dog's age and breed size to get their age in human years, using a size-aware model: small breeds age slower in their later years, giant breeds faster. [Open the calculator →](/tools/dog-age-calculator/)

### [Cat Age Calculator](/tools/cat-age-calculator/)

Enter your cat's age for an instant human-year equivalent, based on the standard feline aging model (year one counts as 15, year two brings the total to 24, and every year after adds about 4). [Open the calculator →](/tools/cat-age-calculator/)

### [Dog Food Calculator](/tools/dog-food-calculator/)

Enter your dog's weight, life stage and activity for their daily calories, worked out with the veterinary resting-energy formula, then turned into cups or grams for your exact food. Works in pounds or kilograms. [Open the calculator →](/tools/dog-food-calculator/)

### [Cat Food Calculator](/tools/cat-food-calculator/)

The same veterinary energy formula for cats, with an indoor-cat setting, converted into cans, cups or grams from your food's label. [Open the calculator →](/tools/cat-food-calculator/)

### [Pet Cost Calculator](/tools/pet-cost-calculator/)

Itemizes the first year, every year after, the monthly average and a lifetime estimate for a dog or cat, in US dollars or with separately researched Western European prices in euros. [Open the calculator →](/tools/pet-cost-calculator/)

### [Dog Breed Selector](/tools/dog-breed-selector/)

Ten questions about your home, schedule and family rank the five best-fitting breeds from 45 popular US and European breeds, with what each does well and what to keep in mind. [Open the selector →](/tools/dog-breed-selector/)

### [Cat Breed Selector](/tools/cat-breed-selector/)

The same approach for cats: ten questions, 25 breeds plus the everyday mixed-breed cat, and an honest caveat for every match. [Open the selector →](/tools/cat-breed-selector/)

## In development

A pet name generator is next. Like every tool here, it ships when it actually works, not before.

## Frequently asked questions

### What tools does ThePawManual offer?

Seven free tools are live: [dog](/tools/dog-age-calculator/) and [cat](/tools/cat-age-calculator/) age calculators, [dog](/tools/dog-food-calculator/) and [cat](/tools/cat-food-calculator/) food calculators, a [pet cost calculator](/tools/pet-cost-calculator/) in dollars or euros, and [dog](/tools/dog-breed-selector/) and [cat](/tools/cat-breed-selector/) breed selectors. A pet name generator is in development.

### Are the calculators free to use?

Yes. Every calculator on ThePawManual is free, runs in your browser, and doesn't require an account or email address.

### How accurate are the calculators?

They're built on published veterinary models: size-aware aging formulas, the resting-energy formula vets use for feeding, and researched price and breed data, never rules of thumb like "multiply by 7." They give a solid estimate, not a diagnosis; a veterinarian is still the right call for anything health-related.

### What tools are coming next?

A pet name generator is next. Each tool ships only once it works, so there are no placeholder tools on the site.

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
        "text": "Seven free tools are live: dog and cat age calculators, dog and cat food calculators, a pet cost calculator in dollars or euros, and dog and cat breed selectors. A pet name generator is in development."
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
        "text": "They're built on published veterinary models: size-aware aging formulas, the resting-energy formula vets use for feeding, and researched price and breed data, never rules of thumb like \"multiply by 7.\" They give a solid estimate, not a diagnosis; a veterinarian is still the right call for anything health-related."
      }
    },
    {
      "@type": "Question",
      "name": "What tools are coming next?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "A pet name generator is next. Each tool ships only once it works, so there are no placeholder tools on the site."
      }
    }
  ]
}
</script>
```

---

## Notes (not page copy)

- **Wave-1 page.** Per `SEO-STRATEGY.md` §4, this hub and the two live calculator pages publish in Wave 1 alongside the trust set and homepage.
- **2026-10-22: five tools shipped** (dog food, cat food, pet cost, dog breed selector, cat breed selector). Body, FAQ (visible + JSON-LD) and meta description rewritten; H1 and title unchanged so the registry entry still matches. **This page is live: paste the new body and FAQ block into WordPress and update the RankMath description by hand.**
- (Historical) The six "in development" tools were deliberately **not linked** — no `/tools/*` page exists for them yet, and linking would 404 at launch. If a calculator ships, add its live-calculator card here (matching the "Live now" section pattern) and move it out of "In development."
- The "In development" list links to the *content* pages that already cover the same math/decision by hand (feeding guides, cost guide, breed hubs) — this keeps the section useful today instead of just a promise.
- Order of "In development" list follows `SITEMAP.md`'s Tools table order, which itself follows the content-tool pairing priority in `content/README.md` §3 (feeding guides ↔ food calculators, cost guide ↔ cost calculator) — build calculators in that order per that doc.
- No card/grid markup specified here since the theme's `template-parts/tool-card.php` (per `CLAUDE.md` §4.1) renders the homepage tool grid from `inc/tools-data.php`; this page's job is the indexable SEO copy for `/tools/`, not to duplicate that component. Dev should decide whether this page also renders the same card component or stays copy-first with plain links — flagging for dev, not deciding here.
