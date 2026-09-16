---
url: /tools/cat-age-calculator/
page: Cat Age Calculator
seo-title: "Cat Age Calculator: Cat Years to Human Years"   # 44 chars
meta-description: "Free cat age calculator based on the standard feline aging model. Enter your cat's age for a real human-year estimate: no 'multiply by 7' myths."   # 144 chars
status: draft-v1
---

# Cat Age Calculator: How Old Is Your Cat in Human Years?

**A cat's first year of life equals about 15 human years, the second year brings the total to 24, and every year after that adds roughly 4 more.** That means a 5-year-old cat is closer to 36 in human terms, not 35 (the "multiply by 7" number): close by coincidence at that particular age, but the two methods diverge fast on either side of it. Unlike dogs, breed size doesn't meaningfully change a cat's aging pace, so this calculator needs just one input. Enter your cat's age below for an instant estimate.

[pawmanual_calculator id="cat-age"]

## How the math works

A cat's age converts to human years in three stages: year one is worth about 15 human years, year two brings the total to 24, and every year after that adds roughly 4 more.

| Cat's age | Human years |
|---|---|
| 1 year | 15 |
| 2 years | 24 |
| 3 years | 28 |
| 5 years | 36 |
| 10 years | 56 |
| 15 years | 76 |

The jump is steepest in the first two years (a kitten reaches the rough equivalent of human adolescence and early adulthood by its first birthday), then settles into a steady +4 human years for every additional cat-year. Because cats don't have the dramatic size range dogs do, one formula covers a 6 lb cat and a 15 lb cat about equally well; this is also why the [cat breed guides](/cats/breeds/) don't organize by size the way our [dog breed guides](/dogs/breeds/) do.

## What happens once a cat turns 11?

By around age 11, a cat is generally considered a senior. The main practical change is increased vigilance: cats are notorious for hiding illness until it's advanced, so routine veterinary monitoring matters more, not less, as the numbers climb.

Two places to go next: the [senior cat food guide](/cats/food/senior-cat-food/) for how feeding needs shift with age, and [signs your cat needs a vet](/cats/health/signs-your-cat-needs-a-vet/) for the warning signs worth acting on rather than waiting out. If you're budgeting ahead for the senior years (more frequent bloodwork, possible chronic-condition management), our [pet cost guide](/pet-guides/pet-costs/) breaks down what that tends to run.

> **Veterinary disclaimer:** This calculator gives a general estimate for a healthy cat, not a medical assessment. Individual aging varies with genetics, health history, and indoor/outdoor lifestyle: a veterinarian is the only reliable source for how your specific cat is aging.

## Frequently asked questions

### How do I calculate my cat's age in human years?

The first year of a cat's life equals about 15 human years, the second year brings the total to 24, and every year after that adds roughly 4 more. A 5-year-old cat comes out to about 36 in human years.

### Is the "multiply by 7" rule accurate for cats?

Not really: it happens to land close for a handful of ages in the middle of a cat's life, but it overstates how old kittens and young cats are and understates how the aging pace actually changes over a cat's life, since real feline aging isn't a flat rate.

### Does breed affect how fast a cat ages?

Not meaningfully, unlike dogs. Cats don't have the extreme size range dog breeds do, so this calculator uses a single formula for every cat rather than adjusting by breed or size.

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How do I calculate my cat's age in human years?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "The first year of a cat's life equals about 15 human years, the second year brings the total to 24, and every year after that adds roughly 4 more. A 5-year-old cat comes out to about 36 in human years."
      }
    },
    {
      "@type": "Question",
      "name": "Is the \"multiply by 7\" rule accurate for cats?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Not really: it happens to land close for a handful of ages in the middle of a cat's life, but it overstates how old kittens and young cats are and understates how the aging pace actually changes over a cat's life, since real feline aging isn't a flat rate."
      }
    },
    {
      "@type": "Question",
      "name": "Does breed affect how fast a cat ages?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Not meaningfully, unlike dogs. Cats don't have the extreme size range dog breeds do, so this calculator uses a single formula for every cat rather than adjusting by breed or size."
      }
    },
    {
      "@type": "Question",
      "name": "What happens once a cat turns 11?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "By around age 11, a cat is generally considered a senior. The main practical change is increased vigilance: cats are notorious for hiding illness until it's advanced, so routine veterinary monitoring matters more, not less, as the numbers climb."
      }
    }
  ]
}
</script>
```

---

## Notes (not page copy)

- **Math source:** formula and figures transcribed directly from `wp-content/plugins/pawmanual-calculators/calculators/cat-age/cat-age.js` (`YEARS_AFTER_TWO = 4`, `SENIOR_AGE = 11`, `toHumanYears()`). Matches CLAUDE.md §6's summary ("year 1 = 15, year 2 = 24, +4/yr after") exactly — no discrepancy to flag.
- **Table values** (ages 1/2/3/5/10/15 → 15/24/28/36/56/76) computed by hand from `24 + (age-2) * 4` for age > 2, matching the calculator's rounding. Re-verify if the formula constants change.
- **Fact-check flag:** the "multiply by 7 lands close around age 5" observation in the BLUF and FAQ is arithmetically true for this specific formula (5 years → 35 vs. 36) but is a coincidence of this one data point, not a general defense of the 7-year rule — phrasing is deliberately hedged to avoid implying the rule is broadly accurate.
- **Fact-check flag:** senior age of 11 is the calculator's internal default (`SENIOR_AGE = 11` in the JS), not cited to a specific guideline in this copy. AAFP/ISFM feline life-stage guidelines are the standard source (they define "senior" as roughly 11–14 and "geriatric" as 15+) if a citation gets added later — not cited here since I didn't independently verify an exact match.
- Internal links verified against `content/SITEMAP.md`: `/cats/breeds/`, `/dogs/breeds/`, `/cats/food/senior-cat-food/`, `/cats/health/signs-your-cat-needs-a-vet/`, `/pet-guides/pet-costs/` all exist.
