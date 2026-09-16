---
status: working document — not published to the site
last updated: 2026-09-14
---

# ThePawManual — Content Fact-Check

This is a **documentary fact-check against published sources** — ten claim-cluster
passes over the site's veterinary/behavior/nutrition content, followed by an
independent audit that re-fetched a sample of the citations. It is **not a
veterinary review**, and **no veterinarian has reviewed this content**, consistent
with what `content/editorial-process.md` already tells readers: *"researched
against the veterinary sources above, but it is not currently reviewed by a
veterinarian."* Nothing below changes that disclosure — if anything, the findings
here are the reason it needs to stay accurate.

## At a glance

| | Count | Where |
|---|---|---|
| Must-fix (high-risk, contradicted/imprecise, sourced) | 16 rows + 1 top-priority item | §3 |
| Should-fix (medium/low-risk corrections) | ~19 rows | §4 |
| Unverifiable (needs a human decision) | 13 rows | §5 |
| Audit-rejected (citation failed re-fetch) | 6 findings, ~11 individual claims | §6 |
| Standing-maintenance items (things that expire) | 12 | §7 |

---

## 1. Scope and coverage

**Date:** 2026-09-14. Ten cluster fact-checks (`emergency`, `toxicity`, `vaccines`,
`health-cats`, `health-dogs`, `nutrition-cats`, `nutrition-dogs`,
`behavior-training`, `breeds`, `practical`), each run against the pages listed
below, followed by an independent Opus audit that re-fetched a sample of the
citations to catch fabrication and misuse.

| Cluster | Pages in scope | Coverage |
|---|---|---|
| emergency | 5 | Full pass. ~12–15 low-risk, non-numeric claims (vomit-color table rows, unattributed site heuristics) not individually sourced — disclosed in the cluster's own report. |
| toxicity | 6 | Full pass — all "can dogs/cats eat X" pages that exist plus pet-safety.md. |
| vaccines | 2 explicit + grep sweep | Full pass on the two vaccination-schedule pages; every other page on the site was grepped for independent vaccine-interval claims and none was found. |
| health-cats | 7 | Full pass, excluding the emergency page (covered by the `emergency` cluster). |
| health-dogs | 4 | Full pass, **explicitly excluding** dental-care, ear-infections, allergies, skin-and-coat, why-is-my-dog-itching, and fleas-ticks-and-worms — see the gap below. |
| nutrition-cats | 8 | Full pass. |
| nutrition-dogs | 6 | Full pass. |
| behavior-training | 21 | Partial — roughly 16 of 90+ distinct checkable claims verified; the rest listed as not-reached in the cluster's own report. |
| breeds | 23 | Partial — 15 of ~45–50 checkable claims verified; WebSearch budget was exhausted mid-cluster. |
| practical | 16 assigned | **Partial**, self-declared "Status: partial" by the worker. Most of best-dog-beds.md, best-dog-harnesses.md, best-gps-dog-trackers.md, best-cat-carriers.md, best-cat-trees.md, and new-pet-checklist.md's general advice were never reached. |

### What was never checked at all

These pages fall outside every cluster's assigned scope — nobody read them for
this fact-check:

- `content/dogs/health/fleas-ticks-and-worms.md`
- `content/dogs/health/why-is-my-dog-itching.md` — **gives Benadryl (human-medication) dosing guidance**
- `content/dogs/health/ear-infections.md`
- `content/dogs/health/dental-care.md` (dog)
- `content/dogs/health/allergies.md` (dog)
- `content/dogs/health/skin-and-coat.md`
- `content/dogs/food/index.md`
- Five of the six `content/dogs/puppy-care/` pages (only puppy-vaccination-schedule.md was checked) — including `kitten-feeding-schedule.md`-style pages with orphan-animal milk-replacer/weaning instructions
- Five of the six `content/cats/kitten-care/` pages (only kitten-vaccination-schedule.md was checked)

A grep across the dogs/health and cats trees during this pass found **zero
permethrin/pyrethroid warnings anywhere on the site** — the classic, potentially
lethal error of applying a dog flea product to a cat is not covered at all. No
fact-check pass would catch this, because nobody has read the page it would
live on. Treat this as a content gap, not just a verification gap.

### High-risk claims that went unverified

At least **7** high-risk claims (by the cluster workers' own risk-coding) could
not be settled against a retrievable source in this pass:

1. Vaccine adverse-reaction / anaphylaxis triage thresholds (dog page)
2. Feline injection-site-sarcoma incidence (cat page)
3. FVR/calicivirus latency mechanism (cat page)
4. Cat-bite infection rate and same-day-care urgency
5. AVMA's raw-diet position
6. Feline rabies vaccination age/interval
7. The dog "not eating 24+ hours" vet-call threshold

Four of these were returned as "unverifiable" with an **empty source field and
no record of what was tried** — see §5 for the full list and recommended
actions.

---

## 2. How to read this document

- **SUPPORTED** — a cluster worker retrieved a URL this session and pulled a
  verbatim quote that settles the claim. It does **not** mean "true forever" —
  see §7 for what expires and when.
- **IMPRECISE** — directionally right, but a specific number, attribution, or
  qualifier doesn't match what the retrieved source actually says.
- **CONTRADICTED** — the retrieved source states something different from the
  claim.
- **UNVERIFIABLE** — no authoritative source could be retrieved in the time
  available. **This does not mean the claim is false.** Most unverifiable rows
  here are standard veterinary knowledge that simply couldn't be pinned to a
  fetchable, top-tier URL this pass (paywalls, bot-blocking, 403s, a shared
  search-budget running out). Treat "unverifiable" as "still needs a source,"
  not "wrong."

### The audit, stated plainly

After the ten clusters finished, an independent Opus pass re-fetched **45** of
the returned claim/citation pairs, deliberately weighted toward high-risk rows
and rows marked "supported" — the two categories where a mistake matters most
and an editor is most likely to trust the row without checking it.

**Result: 11 of 45 re-fetched pairs (24%) carried a citation that did not do
the job it was presented as doing, and one was an outright fabricated
citation** — a PubMed *search-results* page for an unrelated topic, with a
quoted sentence that does not appear anywhere on it. Among high-risk rows
specifically marked "supported," the defect rate was roughly **1 in 4**.

The audit's verdict was **changes-required**. Roughly three-quarters of the
fact-check is sound, and it caught several real, valuable errors — leptospirosis
reclassified as a core vaccine, the German Shepherd lifespan error, the
cat-and-xylitol myth, the chocolate-dose unit error, the stale CFA breed
ranking — but it cannot be handed to an editor as-is. §6 lists every row the
audit could not stand behind, regardless of what the original cluster report
said. Nothing in that section should be treated as verified just because a
cluster worker called it "supported."

---

## 3. MUST FIX before these pages get traffic

Every row is a **HIGH-RISK** claim that came back CONTRADICTED or IMPRECISE
against a source that was actually retrieved and survived the audit (rows the
audit rejected outright are in §6, not here, even if the risk was high).
**[SCHEMA]** rows are mirrored into FAQPage JSON-LD — these get quoted
verbatim by Google's rich results and by AI answer engines, so they carry
disproportionate downstream risk and are listed first.

### Top-priority item (not in schema, but the single largest under-statement of risk found in the whole corpus)

**Leptospirosis is core, not non-core.** `content/dogs/puppy-care/puppy-vaccination-schedule.md`
states this in four places: the "what core shots protect against" list, the
10–12 week schedule row, the 14–16 week schedule row, and the "Non-core, worth
the conversation" paragraph.

- **Claim as written:** "Core shots (per AAHA guidelines) protect against
  distemper, adenovirus, parvovirus, and rabies," with leptospirosis grouped
  under "non-core by lifestyle."
- **What the source actually says:** "All dogs should have the following core
  vaccines: Distemper, Adenovirus, Parvovirus, +/- Parainfluenza, Leptospira,
  Rabies." / "NOTE: These guidelines were updated in 2024 to include
  leptospirosis as a recommended core vaccine for all dogs."
- **Source:** https://www.aaha.org/resources/2022-aaha-canine-vaccination-guidelines/
  (2022 AAHA Canine Vaccination Guidelines, Table 2, amended 2024)
- **Replacement wording:** Move leptospirosis into the core-vaccine list
  ("distemper, adenovirus, parvovirus, leptospirosis, and rabies"). In the
  schedule table, give leptospirosis its own core row starting at 12 weeks (2
  doses, 2–4 weeks apart, annual boosters after) — do not group it with
  Bordetella, canine influenza, or Lyme, which are correctly non-core.
- **Why this matters:** leptospirosis is bacterial, environmental
  (wildlife-urine/standing-water spread), zoonotic, and can be fatal
  (acute kidney/liver failure). Telling readers it's an optional
  "lifestyle" add-on when current AAHA guidance is "give it to every dog" is a
  meaningful under-statement of risk, not a cosmetic error.

### Schema-marked claims

| # | Page | Claim as written | What the source actually says | Source | Replacement wording |
|---|---|---|---|---|---|
| M1 | `content/dogs/food/can-dogs-eat-grapes.md`, `content/pet-guides/pet-safety.md` | "No safe dose has ever been established" | Merck's own page now states a threshold: "more than one grape or raisin per 4.5 kg (10 lb) of body weight **may** contain enough tartaric acid to pose a risk for renal effects in dogs." The quote undercuts the claim rather than supporting it. | https://www.merckvetmanual.com/toxicology/food-hazards/grape-raisin-and-tamarind-vitis-spp-tamarindus-spp-toxicosis-in-dogs | "There is no dose considered safe to feed. Current veterinary guidance treats more than roughly one grape or raisin per 10 lb of body weight as a poisoning risk, and any ingestion warrants a call regardless of amount." |
| M2 | `content/dogs/food/can-dogs-eat-grapes.md` | Vomiting/diarrhea "within 6–24 hours" | "Most dogs...develop vomiting or diarrhea **within 6–12 hours** after ingestion." (The 24–72h kidney-failure phase is correctly stated.) | same as M1 | "Within 6–12 hours: vomiting (often first)... Within 24–72 hours: kidney failure signs..." |
| M3 | `content/pet-guides/pet-safety.md` (FAQ #1, #5) | "Human medications outrank all foods... top the list" (ibuprofen, acetaminophen, antidepressants, ADHD meds as one group) | ASPCA's 2025 ranking: #1 is "OTC medications and supplements" (16.9%), #2 "Food and drink" (16.4%), #3 "Human prescription medications" — a separate category. The page merges the #1 (OTC) and #3 (prescription) categories as one. | https://www.aspca.org/news/top-10-toxins-2025 | "Over-the-counter human medications and supplements are the #1 reason people call animal poison control (ASPCA's 2025 Top Toxins report), with human prescription medications — heart drugs, antidepressants, ADHD medications — a separate top-3 category." |
| M4 | `content/cats/health/signs-your-cat-needs-a-vet.md`, `content/cats/health/index.md`, `content/cats/health/symptoms.md` | Urethral obstruction "fatal within roughly 24–48 hours untreated" | "Complete UO causes uremia within 36–48 hours...death within approximately 72 hours." (Two clusters gave this different verdicts from different-tier sources — MSPCA "supported," Merck "imprecise." Resolved to Merck, the higher-tier source.) | https://www.merckvetmanual.com/urinary-system/urolithiasis-in-small-animals/urethral-obstruction-in-small-animals | "...a blocked cat becomes uremic (a life-threatening crisis) within 36–48 hours and can die within about 3 days untreated — treat any straining male cat as an emergency, hours matter." |
| M5 | `content/dogs/health/signs-your-dog-needs-a-vet.md` (vital-signs FAQ) | Heart rate "about 60–140 beats per minute" | "Dog: 70–120" bpm; small dogs run faster (no numeric ceiling given). | https://www.merckvetmanual.com/multimedia/table/resting-heart-rates | "Heart rate about 70–120 beats per minute at rest (small dogs run toward the higher end)." |
| M6 | `content/dogs/puppy-care/puppy-vaccination-schedule.md` (FAQ #2) | Rabies "required by law in nearly every U.S. state, typically by 12–16 weeks with a booster a year later" | CDC confirms only a 12-week minimum for its dog-*import* program; AAHA's own table gives no age or interval at all, deferring entirely to state law. | https://www.cdc.gov/importation/hcp/dog-importation/instructions-us-issued-rabies-vaccination-form.html ; https://www.aaha.org/resources/2022-aaha-canine-vaccination-guidelines/ | "Rabies — required by law in nearly every U.S. state; timing and booster interval are set by your state, typically starting no earlier than 12 weeks. Check your state's schedule; AAHA defers entirely to local law here." |
| M7 | `content/cats/health/hairballs.md` (FAQ #1) | "One or two hairballs a month is normal; weekly or more is a symptom" | "It's not uncommon...for a cat to 'upchuck' a hairball once every week or two, and that's nothing to worry about." Cornell's real trigger is behavioral (lethargy, anorexia >1 day, repeated unproductive retching), not raw frequency. | https://www.vet.cornell.edu/departments-centers-and-institutes/cornell-feline-health-center/health-information/feline-health-topics/hairy-dilemma | "Occasional hairballs (roughly weekly or less) are normal. See a vet if your cat retches repeatedly without producing one, goes off food, seems lethargic, or the frequency climbs." |
| M8 | `content/cats/food/senior-cat-food.md` (FAQ: "Why is my old cat losing weight but eating well?") | "Classic hyperthyroidism presentation" | The page's cited source (a catvets.com senior-care-guidelines announcement) never mentions hyperthyroidism — it only defines "senior" as 10+. The claim itself is real; the citation doesn't support it. | Swap to: https://www.vet.cornell.edu/departments-centers-and-institutes/cornell-feline-health-center/health-information/feline-health-topics/hyperthyroidism-cats — "The most common clinical signs of hyperthyroidism are weight loss, increased appetite, and increased thirst and urination." | Keep the claim, replace the citation with the Cornell URL above (already independently retrieved for the same fact on a different page in this fact-check). |
| M9 | `content/cats/food/how-much-should-i-feed-my-cat.md` (FAQ: "How do I put my cat on a diet safely?") | "Safe feline dieting is gradual (roughly 1–2% of body weight per month)" | "about 0.5% to 2% of current body weight **per week**." A ~4x unit error. | https://www.petobesityprevention.org/weight-loss-cats | "Safe feline dieting is gradual — roughly 0.5–2% of body weight per week. For a 15 lb cat, that's about 0.08–0.3 lb per week." |
| M10 | `content/dogs/health/why-is-my-dog-throwing-up.md` (FAQ) | "Rest the stomach: no food for 6–12 hours" | Two clusters cited two mismatched, unreliable numbers (2–4h via a manufacturer-affiliated portal that 403'd; 24–48h via a teaching-hospital page). Resolved to Merck, the highest-tier source actually reachable: "withholding food...for 24 hours. If the vomiting has stopped after 24 hours, the dog may be offered small amounts...of easily digested food." | https://www.merckvetmanual.com/dog-owners/digestive-disorders-of-dogs/vomiting-in-dogs | "Rest the stomach: no food for about 24 hours if vomiting has stopped (adults only — never fast puppies, small breeds, or diabetics without vet guidance), then reintroduce small amounts of a bland diet." |
| M11 | `content/dogs/breeds/german-shepherd.md` (quick-facts table + FAQ) | Lifespan "7–10 years" | "LIFE EXPECTANCY — 12-14 years" (confirmed same breed page via matching weight figures). | https://www.akc.org/dog-breeds/german-shepherd-dog/ | "Lifespan | 12–14 years" — update the table and the FAQ answer. *(No Merck/Cornell/AAHA-specific corroborating source was reachable in this pass — AKC alone was checked — but AKC directly contradicts the current number, so "7–10" cannot stand as-is either way. Get a second source before wave 2 if possible.)* |

### High-risk, not in schema

| # | Page | Claim as written | What the source actually says | Source | Replacement wording |
|---|---|---|---|---|---|
| M12 | `content/dogs/health/signs-your-dog-needs-a-vet.md` (emergency list) | Seizure "lasting over 2–3 minutes" is the threshold | "If your dog has been seizing for more than **five minutes**, go to the veterinary clinic immediately." >5 min is the clinical definition of status epilepticus. | https://www.vet.cornell.edu/departments-centers-and-institutes/riney-canine-health-center/canine-health-information/managing-seizures | "...one lasting more than 5 minutes (status epilepticus — a genuine emergency), or several in a row." Or: keep 2–3 minutes as the site's own earlier-caution point, but label it that way rather than implying it's the clinical threshold. |
| M13 | `content/dogs/puppy-care/puppy-vaccination-schedule.md` | Parvovirus "frequently fatal in unvaccinated puppies even with intensive care" | "With appropriate supportive care, **70–90%** of dogs with parvoviral enteritis will survive." | https://www.merckvetmanual.com/digestive-system/infectious-diseases-of-the-gastrointestinal-tract-in-small-animals/canine-parvovirus-infection-parvoviral-enteritis-in-dogs | "...often fatal without treatment, but 70–90% of dogs survive with prompt, intensive veterinary care." |
| M14 | `content/dogs/food/can-dogs-eat-chocolate.md` (body copy only — the page's own FAQ/JSON-LD already has this right) | "~44 g (1.5 oz) of milk chocolate **per kg** of body weight is trouble territory" | Merck's own table gives milk chocolate = 2.3 mg/g theobromine. The page's own 20 mg/kg mild-signs threshold works out to ~8.7 g/kg — not 44 g/kg. Off by ~4.5×, understating the real risk. The page's own FAQ gets it right ("~1.5 oz per **10 lb**"). | https://www.merckvetmanual.com/toxicology/food-hazards/chocolate-toxicosis-in-animals | Fix the body sentence to match the page's own correct FAQ: "~1.5 oz of milk chocolate per **10 lb** of body weight is trouble territory" — not "per kg." This is the single highest-value fix in the toxicity cluster: a live toxic-dose figure that currently understates real risk by 4.5× in the body copy of a live page. |
| M15 | `content/cats/health/index.md`, `content/cats/health/symptoms.md`, `content/cats/food/best-cat-food.md`, `content/cats/food/senior-cat-food.md`, `content/cats/food/cat-food-for-sensitive-stomachs.md`, `content/cats/food/index.md` (FAQ, in schema on the food hub), `content/cats/food/can-cats-eat-tuna.md` | "24+ hours without eating risks hepatic lipidosis" | Cornell confirms 24h is a genuine action threshold for feline anorexia generally, but hepatic lipidosis specifically typically takes **2–4 days** (VCA: "three to four consecutive days") to develop, faster in overweight cats. | https://www.vet.cornell.edu/departments-centers-and-institutes/cornell-feline-health-center/health-information/feline-health-topics/anorexia ; https://vcahospitals.com/know-your-pet/liver-disease-fatty-liver-syndrome-in-cats | "Call your vet if your cat goes 24+ hours without eating — cats (especially overweight ones) are uniquely prone to hepatic lipidosis, a life-threatening liver condition, which can develop within just a few days of not eating." |

### No action needed (a dedup note, so nobody "fixes" something that's already correct)

Two clusters disagreed on the **GDV high-risk breed list** (German Shepherds,
Standard Poodles) in `content/dogs/health/signs-your-dog-needs-a-vet.md` and
breed pages: the ACVS/VCA/Glickman-cohort sources one worker checked don't
list German Shepherds, but AAHA — the guideline the page itself already cites
elsewhere — explicitly does: *"Breeds that are considered high-risk
include...Great Danes, Standard Poodles, German Shepherds, Weimaraners..."*
(https://www.aaha.org/resources/understanding-canine-bloat-gdv-a-medical-emergency/).
**No change needed** — the current breed list is correct against the
site's own preferred authority.

---

## 4. SHOULD FIX

Medium/low-risk corrections, or high-risk claims where only the citation
(not the fact) needs an upgrade. Same columns as §3, condensed.

| # | Page(s) | Claim | Source says | Source | Fix |
|---|---|---|---|---|---|
| S1 | `content/pet-guides/pet-safety.md` | ASPCA fee: "(fee applies)" / "consultation fee may apply" | Live ASPCA wording: "A one-time consultation fee can help you avoid thousands in potential emergency vet costs." | https://www.aspca.org/pet-care/aspca-poison-control | Minor wording drift — align to current ASPCA phrasing or leave as neutral "(a consultation fee applies)." |
| S2 | `pet-guides/pet-safety.md`, `cats/health/*`, `cats/food/index.md` | Lilies "lethal to cats in any amount" | ASPCA: "small amounts can cause severe injury to the kidneys," good outcomes if treated within 18h. | https://www.aspca.org/news/which-lilies-are-toxic-pets | "Small amounts can cause fatal kidney injury; treat any exposure — including pollen groomed off fur or vase water — as an emergency." |
| S3 | `pet-guides/pet-safety.md` + dog health pages | Dog resp. rate "15–30/min" | Cornell confirms 15–30 (fine); Merck's own reference table (used elsewhere on the same pages) gives 18–34. | https://www.vet.cornell.edu/pets-get-heart-disease-too ; https://www.merckvetmanual.com/multimedia/table/resting-respiratory-rates | No urgent change — note the source conflict rather than silently citing only the agreeing one. |
| S4 | `content/dogs/training/how-to-stop-dog-barking.md` | Devocalization "condemned by mainstream veterinary organizations, full stop" | AVMA (adopted by AAHA), CVMA, and ASPCA all *permit* it as a licensed-vet last resort after failed behavior modification, to prevent euthanasia/relinquishment. None condemn it outright. | Wikipedia mirrors AVMA policy language — wrong tier, see R5 | "...is discouraged by mainstream veterinary organizations except as a licensed veterinarian's last resort after behavior modification has failed." |
| S5 | `content/cats/breeds/ragdoll.md`, `content/cats/breeds/index.md` (FAQ, in schema) | "The Ragdoll has topped CFA registrations in recent years, with Maine Coons close behind" | CFA's most recent ranking (2024 registrations, published Mar 2025): Maine Coon #1, Ragdoll #2, Persian #3. | https://cfa.org/cat-talk/most-popular-breeds-for-2024/ | "The Maine Coon has topped CFA registrations most recently, with the Ragdoll close behind." Stale annual stat — see §7. |
| S6 | `content/dogs/food/can-dogs-eat-chocolate.md` | "Baker's chocolate/cocoa powder ≈10× milk chocolate" | Merck's table: baker's ≈6.7×, cocoa powder ≈12.4× (they average to ~10×; baker's alone is closer to 7×). | https://www.merckvetmanual.com/toxicology/food-hazards/chocolate-toxicosis-in-animals | "Baker's chocolate and cocoa powder run roughly 7–12× more concentrated than milk chocolate." Low stakes. |
| S7 | `dogs/puppy-care/puppy-vaccination-schedule.md`, `dogs/training/*` | "...until 1–2 weeks after the final parvo shot" attributed to AVSAB | AVSAB's own statement says only that unsanitized/high-traffic areas "should be avoided" — no stated end-date tied to the final dose. | https://avsab.org/wp-content/uploads/2018/03/Puppy_Socialization_Position_Statement_Download_-_10-3-14.pdf | Keep the 1–2-week guidance (reasonable, common practice) but attribute it as general veterinary practice, not as "the AVSAB position" — reserve the AVSAB citation for "start before fully vaccinated" and "avoid unsanitized/high-traffic areas," which are genuinely AVSAB's own words. |
| S8 | `content/cats/kitten-care/kitten-vaccination-schedule.md` | FVRCP booster "12–16 months" | Current 2020 AAHA/AAFP guideline recommends *considering* the booster at 6 months instead of 12, to close a susceptibility gap. | AAHA/AAFP Feline Vaccination Guidelines table (mirrored at hubspotusercontent-na1.net; see M6 source note) | Add: "AAHA/AAFP guidance now recommends considering this booster at 6 months rather than 12, to close a window of vaccine susceptibility — ask your vet." |
| S9 | `cats/kitten-care/kitten-vaccination-schedule.md` | FeLV "a leading infectious killer of young cats" | Cornell frames it as "one of the most common and important infectious diseases" and "the most common cause of cancer in cats" — median post-diagnosis survival 2.5 years, not phrased as an acute killer. | Cornell Feline Health Center FeLV page | "A leading infectious disease of young cats, and the most common cause of cancer in cats." Drop "killer." |
| S10 | `cats/kitten-care/kitten-vaccination-schedule.md` | "Per AAFP guidelines" | The document is the joint **2020 AAHA/AAFP** Feline Vaccination Guidelines, confirmed still current. | https://www.aaha.org/resources/2020-aahaaafp-feline-vaccination-guidelines/ | "Per the 2020 AAHA/AAFP Feline Vaccination Guidelines." |
| S11 | Multiple pages | APOP obesity stats (59% dogs / 61% cats) undated | Figures are APOP's **2022** clinically-assessed survey; newer 2023–2025 releases are lower, differently-methodologied owner self-reports. | https://www.petobesityprevention.org/2022 | Add "(APOP's 2022 clinical survey)" wherever the figure appears. |
| S12 | `content/cats/health/dental-care.md` | Xylitol "toxic to cats" (toothpaste warning) | "Luckily people, **cats** and ferrets are not negatively affected by xylitol." It's a **dog**-specific hazard. | https://www.aspca.org/news/updated-safety-warning-xylitol-how-protect-your-pets | Drop the xylitol-toxic-to-cats claim. Reframe: "human toothpaste isn't formulated for a swallower and often contains fluoride, which doesn't belong in a cat's stomach — skip it regardless." |
| S13 | `content/cats/food/senior-cat-food.md` (FAQ) | "'Geriatric' often used from 15" | The current 2021 AAHA/AAFP guideline deliberately declined to attach an age to "geriatric," treating it as a health-status term. Its life-stage table simply says "Senior: aged over 10 years." | https://pmc.ncbi.nlm.nih.gov/articles/PMC10812130/ | "Around 10 years — current guidelines treat 'senior' as a single life stage rather than splitting out a separate 'geriatric' age." |
| S14 | `content/dogs/health/signs-your-dog-needs-a-vet.md` | Quoted as: "Brachycephalic dogs are at increased risk of developing heat-related illness" | The source's real sentence: "Dogs that are brachycephalic are **likely to have an increased risk** for developing heat-related illness compared to non-brachycephalic dogs" — hedged, comparative. | https://pmc.ncbi.nlm.nih.gov/articles/PMC13011106/ | Fix the quote to match verbatim, or soften the page's claim to match the hedge. |
| S15 | `content/dogs/food/puppy-food.md` (FAQ) | Large-breed formula threshold "~50 lb" | AAFCO's regulatory large-breed growth category (calcium-capped) is defined at **70 lb** projected adult weight. 50 lb is a real, commonly-cited "more conservative" figure, not the regulatory one — direction of the imprecision is protective. | https://sites.tufts.edu/petfoodology/2017/02/24/confused-about-what-to-feed-your-large-breed-puppy-new-rules-may-help/ | "If the adult weight will pass ~50 lb (a conservative threshold; AAFCO's own regulatory large-breed cutoff is 70 lb), feed large-breed growth formula." |
| S16 | `content/pet-guides/pet-costs.md` (FAQ) | First-year cat cost "$800–$1,500" | ASPCA's own 2021 first-year total for a cat is **$1,904** — 27% above the page's ceiling. Costs have not fallen since 2021. | https://www.aspca.org/pet-care/general-pet-care/cutting-pet-care-costs | Raise the ceiling to roughly "$800–$1,900," or re-benchmark against a current APPA/Synchrony figure. |
| S17 | `content/pet-guides/pet-insurance.md` (FAQ) | "No U.S. pet insurer covers pre-existing conditions" | NAIC's own language is "**most**," not "no/none." | https://content.naic.org/insurance-topics/pet-insurance | "Practically no U.S. pet insurer covers pre-existing conditions." |
| S18 | `content/cats/products/best-cat-litter.md` (FAQ) | Kitten non-clumping litter needed "under ~8–10 weeks" | No primary veterinary-society source states this exact cutoff. Several secondary sources describe the ingestion-risk window as lasting to "under four months" (16 weeks) — materially later. AAHA's own kitten checklist recommends clumping litter with no age gate stated. | https://www.aaha.org/resources/2021-aaha-aafp-feline-life-stage-guidelines/life-stage-checklists/ | Widen to "under about 4 months" until a primary source pins the exact age, or flag for a direct AAFP/Cornell check before wave 2. |
| S19 | `content/dogs/health/why-is-my-dog-throwing-up.md`, `content/dogs/training/puppy-training.md`, `content/dogs/breeds/french-bulldog.md` | Bland-diet ratio (2:1–3:1); diabetic-dog rice glucose spike; alpha-wolf debunking; "Frenchies cannot swim" | Facts are each independently plausible/likely correct, but sourced below the site's own stated hierarchy (`content/editorial-process.md`): a single private clinic's blog (bland-diet ratio, rice/glucose), a content aggregator (alpha-wolf), a breed registry rather than a clinical source (Frenchie drowning risk). | see R5 for details | Re-source each: Tufts Petfoodology or Merck for the diet claims, L. David Mech's own 1999 paper for alpha-wolf, a veterinary/clinical source for the drowning-risk claim. None of the underlying facts are believed wrong — this is a citation-tier issue. |

---

## 5. UNVERIFIABLE — needs a human decision

No authoritative source could be retrieved for these. Each row names the
recommended action: **HEDGE** (soften/generalize the copy so it no longer
needs the unverified number), **ATTRIBUTE** (find and cite the real source
before publishing further), or **DROP** (remove the specific figure/attribution).

| # | Page(s) | Claim | Why unverifiable | Action |
|---|---|---|---|---|
| U1 | `content/cats/food/how-much-should-i-feed-my-cat.md`, `wet-cat-food.md` (FAQ, schema) | Daily-calorie chart (8 lb→150–200 kcal, 10 lb→180–250, 12–14 lb→230–320) | WSAVA's own calorie chart is for an *average* adult cat, not scoped to indoor/neutered cats specifically. The page's figures may be correct for that population, but that hasn't been confirmed against a source that actually conditions on neuter/indoor status. | **ATTRIBUTE.** Verify against WSAVA's own obese-prone-adult row, or AAHA/AAFP feline life-stage guidance, before touching the numbers. Do **not** raise them to match the generic WSAVA chart — that would push guidance the wrong way for a population the site's own APOP citation says is majority-overweight. |
| U2 | `content/cats/behavior/why-does-my-cat-bite-me.md` (FAQ, schema) | "A large share of cat bites to hands become infected" | No base-rate source reached (commonly-cited 30–50% figures trace to Dire 1991 / Talan et al. 1999 NEJM, neither retrievable in this pass). | **HEDGE.** Drop the "large share" quantifier; keep "cat bites to the hand carry a real infection risk — seek medical care the same day for any puncture," which needs no percentage to be true and actionable. |
| U3 | `content/dogs/puppy-care/puppy-vaccination-schedule.md` | Which post-vaccine signs are normal vs. emergency (facial swelling, hives, repeated vomiting, difficulty breathing, collapse) | AAHA's full narrative guideline and the JAAHA journal page were both blocked (403/Cloudflare) on every attempt. | **ATTRIBUTE** — top priority. This is the single most consequential unverified claim in the whole fact-check: a reader decides whether to go to the ER off this list. Retry AAHA's narrative guideline with a non-bot browser, or use WSAVA's vaccination guidelines, before treating the current list as settled. |
| U4 | `content/cats/kitten-care/kitten-vaccination-schedule.md` | Injection-site sarcoma incidence/rarity | AVMA's task-force page and Cornell's vaccinations page both returned empty/404. | **HEDGE.** Keep the qualitative framing ("rare but real; modern practice manages the risk via vaccine choice and standardized injection sites") without an incidence figure until AVMA's task-force page is retrieved directly. |
| U5 | `cats/kitten-care/kitten-vaccination-schedule.md` | FVR/calicivirus latency mechanism | Cornell and Merck URLs both 404'd for this sub-topic. | **ATTRIBUTE.** Standard textbook virology, low risk of being wrong — find a working Cornell or Merck feline-herpesvirus page and cite it rather than leaving it unsourced. |
| U6 | `content/dogs/food/best-dog-food.md` | "The AVMA discourages raw diets" | avma.org's raw-diet policy URL silently redirected to the bare homepage on every attempt (5 tries, 2 tools). | **ATTRIBUTE.** Retry with a human-operated browser, or cite the FDA raw-pet-food page instead (independently confirmed elsewhere in this fact-check: "raw pet food was more likely to be contaminated with disease-causing bacteria" — https://www.fda.gov/animal-veterinary/animal-health-literacy/get-facts-raw-pet-food-diets-can-be-dangerous-you-and-your-pet). Drop the specific "AVMA" attribution until its own page is retrieved. |
| U7 | `cats/kitten-care/kitten-vaccination-schedule.md` | Feline rabies "state law typically 12–16 weeks...(1–3 yr)" | AAHA/AAFP's feline table gives no age or interval at all, only "follow vaccine label instructions and local laws" (it does support the 3-year half of the range, not the 1-year half or the 12–16 week start). | **HEDGE.** "Per your state's law and your vet's guidance" in place of specific numbers, matching what the guideline itself says. |
| U8 | `dogs/health/signs-your-dog-needs-a-vet.md`, `symptoms.md` (FAQ, schema) | "Not eating for 24+ hours" as the dog vet-call threshold | No AAHA/AVMA/Merck/Cornell page giving this exact numeric threshold was retrieved. | **HEDGE.** Keep it as the site's own conservative rule of thumb, not implied as a named-authority figure. This direction (call sooner) is safe either way. |
| U9 | `content/cats/breeds/maine-coon.md`, `ragdoll.md`, `persian.md`, `siamese.md`, `bengal.md`, `british-shorthair.md` | Breed lifespan figures (12–15 to 12–20 years depending on breed) | Only pet-content sites (excluded tier) repeat these exact figures; icatcare.org 404'd, CFA breed pages carry no lifespan data. | **ATTRIBUTE.** Find a primary source (Cornell, a breed-club health survey, or a peer-reviewed feline-longevity study) before wave 2 — these are FAQ-schema claims readers use to plan a decade-plus commitment. |
| U10 | `content/cats/breeds/hypoallergenic-cats.md` | Cat allergy prevalence ("roughly one in ten adults," "about twice as common as dog allergies") | Flagged in the page's own Notes as needing an AAAAI/ACAAI citation; not reached in this pass. | **ATTRIBUTE.** Find the AAAAI/ACAAI source the page's Notes already call for, or **HEDGE** to "allergies to cats are common" without the specific ratio. |
| U11 | `content/pet-guides/pet-costs.md`, `content/pet-guides/new-pet-checklist.md` | Lifetime cost "$15,000–$30,000+" | No primary ASPCA/APPA/Synchrony page states this range. It's arithmetically plausible from ASPCA's own (2021) annual figures, but not correctly attributed. | **DROP** the implied third-party sourcing. Relabel as "our own estimate, based on ASPCA's annual-cost data over a typical 10–16 year lifespan," or find and cite the actual APPA/Synchrony lifetime-cost report. |
| U12 | `content/cats/products/best-cat-toys.md` | Catnip non-response "~1/3 of cats, genetic" | Widely attributed to Cornell across pet-content sites, but no working Cornell URL for it was found. | **HEDGE.** Keep "catnip is non-toxic and non-addictive; some cats simply don't respond, it's genetic" and drop the specific "1/3" figure until a working primary source is found. |
| U13 | Most of `best-dog-beds.md`, `best-dog-harnesses.md`, `best-gps-dog-trackers.md`, `best-cat-carriers.md`, `best-cat-trees.md`; `new-pet-checklist.md`'s general advice; `pet-insurance.md`'s deductible/reimbursement-rate/cap/waiting-period numeric bands | Not reached at all this pass | Cluster ran out of scope/time before reaching these. | **ATTRIBUTE** — schedule a follow-up pass; these were never checked, not checked-and-cleared. |

---

## 6. Claims the audit rejected

These are **NOT verified**, whatever the originating cluster report said. Do
not treat any of these as cleared for publication on the strength of the
cluster report alone.

### R1 — Fabricated citation: Ragdoll HCM (MyBPC3 mutation testable)

**Page:** `content/cats/breeds/ragdoll.md`

The claim was cited to a PubMed **search-results** URL
(`pubmed.ncbi.nlm.nih.gov/?term=POMC+deletion+labrador+retriever+obesity`) whose
actual hits are about an unrelated POMC/Labrador-obesity gene. The attributed
quote — *"A substitution mutation in the myosin binding protein C gene in
ragdoll hypertrophic cardiomyopathy"* — does not appear anywhere on that page.

The underlying claim (a Ragdoll-specific MYBPC3 R820W variant is genetically
testable) is very likely true, which makes this worse, not better: it means
the citation was back-filled after the checker already knew the answer.

**Correct citation to use instead:** Meurs KM et al., *"A substitution
mutation in the myosin binding protein C gene in ragdoll hypertrophic
cardiomyopathy,"* Genomics, 2007 — find its actual PubMed record, confirm the
title matches, and quote directly from that record before republishing.

**Open action item:** re-audit every other citation from the same cluster
worker (breeds) — a back-filled citation is rarely a one-off, and this one was
found by chance during a 45-row sample.

### R2 — Five "supported" verdicts with no evidence behind them

Demote every one of these to **UNVERIFIED** — none currently has a working
source_url or a real quote behind it, regardless of the "supported" label in
the cluster report.

| Page | Claim | Risk | Recommended real source |
|---|---|---|---|
| `content/dogs/food/senior-dog-food.md` | "Protein restriction for aging kidneys is an outdated myth for healthy seniors" | **HIGH** | WSAVA Global Nutrition Committee guidance, or Tufts Petfoodology on protein and kidney disease |
| `content/cats/food/kitten-food.md` | "No cow's milk at any stage" | medium | Cornell Feline Health Center (lactose-intolerance figure was cited elsewhere in this fact-check via search-synthesis only — needs its own direct fetch) |
| `content/cats/health/why-is-my-cat-sneezing.md` | "FVRCP covers herpesvirus and calicivirus" | medium | The 2020 AAHA/AAFP Feline Vaccination Guidelines (already confirmed current elsewhere in this document — see M6, S10) |
| `content/dogs/index.md` | "Most U.S. dog owners spend roughly $1,000–$2,000 per year" | low | APPA National Pet Owners Survey — fetch directly, don't rely on secondary citation |
| `content/cats/food/cat-food-for-sensitive-stomachs.md` | Feline allergen/elimination-diet claim | medium | Cited to https://pubmed.ncbi.nlm.nih.gov/26753610/ with an empty quote — that record could not even be retrieved to confirm it's the right paper. Re-fetch or substitute Merck's cutaneous food allergy page. |

### R3 — Cat heart rate "140–220 bpm": the CONTRADICTED verdict doesn't survive scrutiny

**Page:** `content/cats/health/signs-your-cat-needs-a-vet.md`

A cluster marked this contradicted against Merck's resting-heart-rate table
("Cat 120–140"). The quote is verbatim — but that table is explicitly adapted
from a **2004 comparative-physiology textbook** (*Dukes' Physiology of
Domestic Animals*), not clinical feline guidance, and Merck has no dedicated
"normal physiological values for cats" page (that URL 404s). 140–220 bpm is
the standard clinical resting range taught for cats; a stressed cat in a
clinic routinely sits at 180–200.

**This is genuinely mixed evidence — do not lower the page's figure on the
strength of this one 2004 table.** Corroborate against a feline-specific
clinical source (Cornell Feline Health Center, AAFP/ISFM feline-friendly
handling guidelines, or the 2020 ACVIM cardiomyopathy consensus statement)
before touching this number in either direction.

### R4 — dogs/index.md: bare-domain citations

`https://www.aaha.org` and `https://www.avma.org` — homepage roots, not deep
links — were cited as sources for specific multi-part quotes. Neither a bare
homepage nor a search-results page can contain the quoted text.

- **AAHA claim** (vet-visit frequency by life stage): the claim is real and
  independently confirmed elsewhere in this document at its actual deep URL —
  https://www.aaha.org/resources/how-often-should-my-dog-go-to-the-vet-a-dog-life-stage-guide/.
  Fix the citation; the content is fine.
- **AVMA claim** (first-aid emergency-category list): could not be substantiated
  anywhere in this session. **Status: NOT verified.** Retrieve AVMA's actual
  first-aid page directly and re-quote, or drop the attribution.

**Standing rule for future fact-checks:** a bare domain root is never an
acceptable source_url.

### R5 — Wrong-tier sources on claims that are probably fine

Facts likely correct; citations don't meet the site's own stated source
hierarchy (`content/editorial-process.md`). See S4, S19 for the specific
replacement action on each:

- Devocalization AVMA/AAHA position — sourced to Wikipedia, not AVMA's own
  policy page.
- "AVMA discourages raw diets" — see U6.
- "Senior cats need more calories" — sourced to academy.royalcanin.com, a
  manufacturer that sells senior cat food. (The fact itself is independently
  corroborated elsewhere in this document via AAHA — see nutrition-cats
  finding #11 — so treat the fact as fine, re-source the citation.)
- Bland-diet ratio and diabetic-dog rice glucose-spike claims — each sourced
  to a single private veterinary clinic's blog.
- Alpha-wolf debunking — sourced to a-z-animals.com (a content site), not
  Mech's own retraction paper.
- "Frenchies cannot swim" — sourced to AKC (a breed registry; confirmed
  accurate on the live page, but outside the clinical-source tier for a
  drowning-risk safety claim).

### R6 — Brachycephalic heatstroke quote: paraphrase presented as verbatim

**Page:** `content/dogs/health/signs-your-dog-needs-a-vet.md`

Quoted as *"Brachycephalic dogs are at increased risk of developing
heat-related illness"* and attributed to PMC13011106. The source's real
sentence: *"Dogs that are brachycephalic are **likely to have** an increased
risk for developing heat-related illness compared to non-brachycephalic
dogs"* — hedged, and specifically a comparison to non-brachycephalic dogs.

**Status: the quote as presented is not accurate.** See S14 for the fix. The
underlying claim is directionally fine.

---

## 7. Standing maintenance — things that expire

For the annual review named in `content/SEO-STRATEGY.md`. Each row: the
source/figure, its status as of this fact-check, and what should trigger a
recheck.

| Source / figure | Status as of 2026-09-14 | Re-check trigger |
|---|---|---|
| AAHA Canine Vaccination Guidelines | 2022 edition, amended 2024 (leptospirosis moved to core) | Check annually — AAHA amends tables between full editions; don't assume the last full-edition year is current. |
| AAHA/AAFP Feline Vaccination Guidelines | 2020 edition, confirmed still current | Check annually on catvets.com's practice-guidelines index. |
| AAHA/AAFP Feline Life Stage Guidelines | 2021 edition | Recheck when AAHA publishes a refresh. |
| AAHA Canine Life Stage Guidelines | 2019 edition | Recheck when AAHA publishes a refresh. |
| CDC dog-import rabies rule | Aug 1, 2024 rule, confirmed live | Recheck at cdc.gov/importation before every pet-travel content refresh — this rule set has changed before. |
| FDA grain-free/DCM investigation | Last public update Dec 2022; FDA states no further updates pending new science | Recheck fda.gov before republishing DCM claims. Note: independent (non-FDA) research increasingly points at legume/pulse content specifically rather than "grain-free" as a category — may warrant a content update even without a new FDA statement. |
| ASPCA Top 10 Toxins | 2025 report (published ~Mar 2026) | Recheck every year in Q1 — category order and % shares move year to year. |
| ASPCA "Cutting Pet Care Costs" annual/first-year figures | Dated 2021 — already stale, see S16 | Find a fresher APPA/Synchrony "Lifetime of Care" report at the next content pass. |
| APOP pet obesity prevalence | 2022 clinically-assessed survey (59% dogs / 61% cats) — correct figure to cite. APOP's 2023–2025 releases are a different, lower, self-reported metric. | Recheck petobesityprevention.org for a new *clinical* survey (not just a new self-report release) before updating the %. |
| NAPHIA pet-insurance premium averages | 2025 report, 2024 premium-year data (~$62/mo dogs, ~$32/mo cats) | Recheck naphia.org annually — NAPHIA shows ~11%/yr dog premium growth, so the page's stated range will need raising. |
| APPA household pet-ownership figures | Page cites 65.1M dog-owning households (2023–2024 survey); APPA's 2025 release reportedly cites 71M under revised methodology | Reconfirm the current APPA figure directly (not via secondary citation) before the next content pass. |
| CFA cat breed popularity ranking | 2024 registrations (published Mar 2025): Maine Coon #1, Ragdoll #2, Persian #3 | Recheck cfa.org every March when the new year's ranking publishes. |
| AKC dog breed popularity ranking | French Bulldog #1 since 2022 registrations | Recheck akc.org every spring when AKC publishes the prior year's registrations. |

---

## A note on schema trust

Several rows above (M1, M4–M11, S5, S8–S10, S16–S18, U1, U3, U7, U8) are
FAQPage JSON-LD claims — but CLAUDE.md §11 already tracks a separate, open
issue: **25 pages outside wave 1 have FAQ visible-text that differs from
their JSON-LD text**, and this fact-check's own cluster workers noticed the
same pattern recurring across pages they checked (e.g. the dog vitals FAQ on
`signs-your-dog-needs-a-vet.md` drops the capillary-refill-time detail
entirely from its JSON-LD version). That is a separate, already-tracked SEO
decision — not something this document resolves — but it means "in schema"
in the tables above refers to the **visible** FAQ answer specifically, and a
fix applied to visible copy should be checked against its JSON-LD twin before
either is considered done.
