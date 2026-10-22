# Dog & cat calorie calculator: factor verification (researched 2026-10-22)

> Research record behind `calculators/dog-food/` and `calculators/cat-food/`, 2026-10-22. What was applied: dog inactive 1.2, no heavy-work option, senior dog 1.4; cat kitten flat 2.5, indoor neutered 1.0, neutered 1.2, senior never below adult. See the two tool pages' Notes for the citations used.

Formula under test: RER = 70 x (kg)^0.75; MER = RER x factor.
Every number below was read directly from the source document unless marked
**UNVERIFIED** or **DERIVED** (my arithmetic on a source's own numbers).

## 0. Sources actually read (primary documents)

| Key | Source | URL | How read |
|---|---|---|---|
| MERCK | Merck Veterinary Manual, "Nutritional Requirements of Small Animals" (Caloric Requirements section) + its table "Daily Maintenance Energy Requirements for Dogs and Cats" | https://www.merckvetmanual.com/management-and-nutrition/nutrition-small-animals/nutritional-requirements-of-small-animals and https://www.merckvetmanual.com/multimedia/table/daily-maintenance-energy-requirements-for-dogs-and-cats | page text |
| AAHA-BOX1 | 2021 AAHA Nutrition and Weight Management Guidelines, Box 1 "Energy Requirement Calculations" | https://www.aaha.org/wp-content/uploads/globalassets/02-guidelines/2021-nutrition-and-weight-management/resourcepdfs/nutritiongl_box1.pdf (the older `https://www.AAHA.org/globalassets/...nutritiongl_box1.pdf` 301-redirects here) | PDF read in full |
| AAHA-2021 | 2021 AAHA Nutrition and Weight Management Guidelines for Dogs and Cats, full text. Cline MG et al. J Am Anim Hosp Assoc 2021;57:153–174. DOI 10.5326/JAAHA-MS-7232 | https://www.aaha.org/wp-content/uploads/2021/06/new-2021-aaha-nutrition-and-weight-management-guidelines-with-ref.pdf | PDF text extracted |
| AAHA-SENIOR | 2023 AAHA Senior Care Guidelines for Dogs and Cats. J Am Anim Hosp Assoc 2023;59:1–21. DOI 10.5326/JAAHA-MS-7343 | https://www.aaha.org/wp-content/uploads/globalassets/02-guidelines/2023-aaha-senior-care-guidelines-for-dogs-and-cats/resources/2023-aaha-senior-care-guidelines-for-dogs-and-cats.pdf | PDF text extracted (the HTML page returns 403 to bots) |
| WSAVA-DOG | WSAVA Global Nutrition Committee, "Calorie Ranges for an Average Healthy Adult Dog in Ideal Body Condition" (updated July 2020) | https://wsava.org/wp-content/uploads/2020/07/Calorie-Needs-for-Healthy-Adult-Dogs-updated-July-2020.pdf | PDF read in full |
| WSAVA-CAT | WSAVA GNC, "Calorie Needs for an Average Healthy Adult Cat in Ideal Body Condition" (updated July 2020) | https://wsava.org/wp-content/uploads/2020/07/Calorie-Needs-for-Healthy-Adult-Cats-updated-July-2020.pdf | PDF read in full |
| PNA | Pet Nutrition Alliance, "Calculating Calories Based on Pet Needs" (MER factor table) | https://petnutritionalliance.org/wp-content/uploads/2023/03/MER.RER_.PNA_.pdf | PDF read in full |
| PNA-CALC | Pet Nutrition Alliance calorie calculator (cats/dogs) | https://petnutritionalliance.org/resources/calorie-calculator?type=cats (and `?type=dogs`) | page text; formula is NOT disclosed (runs in JS) |
| OSU | Ohio State University Veterinary Medical Center, "Basic Calorie Calculator for Dogs and Cats", Table 1 (an image on the page) | https://vmc.vet.osu.edu/companion/our-services/nutrition-support-service/basic-calorie-calculator | page text + Table 1 image downloaded and read |
| TUFTS | Freeman LM, "Feeding Frenzy: How Accurate Are Your Pet Food's Feeding Directions?", Petfoodology (Tufts Clinical Nutrition Service), 22 Apr 2019 | https://sites.tufts.edu/petfoodology/2019/04/22/accuracy-of-feeding-directions/ | page text |
| FEDIAF | FEDIAF Nutritional Guidelines, September 2025, §7.2.4 (Tables VII-6, VII-7, VII-8b, VII-9, VII-10) | https://europeanpetfood.org/wp-content/uploads/2025/09/FEDIAF-Nutritional-Guidelines_2025-ONLINE.pdf | PDF text extracted |
| FEDIAF-SENIOR | FEDIAF Scientific Advisory Board Statement, "Nutrition of senior dogs", Nov 2017 | https://europeanpetfood.org/wp-content/uploads/2022/03/FEDIAF_SAB_Statement_Nutrition_of_Senior_Dogs_fin.pdf | PDF text extracted |
| BERM-2014 | Bermingham EN, Thomas DG, Cave NJ, Morris PJ et al. "Energy Requirements of Adult Dogs: A Meta-Analysis." PLoS ONE 2014. DOI 10.1371/journal.pone.0109681 | https://pmc.ncbi.nlm.nih.gov/articles/PMC4196927/ (read via Europe PMC full text) | full text |
| LINDER-2010 | Linder DE, Freeman LM. "Evaluation of calorie density and feeding directions for commercially available diets designed for weight loss in dogs and cats." JAVMA 2010;236(1):74. DOI 10.2460/javma.236.1.74 | https://pubmed.ncbi.nlm.nih.gov/20043803/ | abstract (via Europe PMC) |

**NRC 2006** itself (the book) was not accessible. NRC values below are only
as quoted by WSAVA, FEDIAF and BERM-2014, which all cite it.

**Not found / not fetchable:** a Cornell page with factors; the Tufts
"cat treat math" article (old tuftscatnip.com URL now 404s); the Tufts
vetnutrition.tufts.edu pages (all redirect to a generic hospital page).

---

## 1. Dogs

| Factor | What the sources say | Recommended value (range) |
|---|---|---|
| **Neutered adult** | MERCK 1.6. PNA 1.6. OSU 1.6. AAHA-BOX1 **1.4–1.6** (its own worked example uses 1.4 for a 10 lb neutered dog = 302 kcal). FEDIAF adults 3–7 y: 110 (95–130) kcal/kg^0.75 = **DERIVED 1.57 (1.36–1.86) x RER**. | **1.6** (1.4–1.6). Strongly supported; four sources give 1.6 exactly. |
| **Intact adult** | MERCK 1.8. PNA 1.8. OSU 1.8. AAHA-BOX1 1.6–1.8. BERM-2014: intact dogs 195.7 vs neutered 146.4 kcal/kg^0.75 (P<0.001), i.e. intact really is higher. | **1.8** (1.6–1.8). |
| **Inactive / obese-prone** | MERCK "obesity prone" 1.4. PNA "overweight-prone/inactive" 1.2–1.4. OSU "inactive/obese prone" 1.2–1.4. AAHA-BOX1 "inactive/obese prone" **1.0–1.2**. FEDIAF "obese prone adults" ≤90 kcal/kg^0.75 = DERIVED ≤1.29; "low activity (<1 h/day, walking on the lead)" 95 = DERIVED 1.36. | **1.2** (1.0–1.4). Sources disagree on the centre: Merck 1.4, AAHA 1.0–1.2. 1.2 is the value inside both PNA/OSU and AAHA ranges. |
| Weight loss (reference only) | AAHA-BOX1 1.0. PNA "weight loss/obese" 1.0. OSU "1.0 x RER for ideal weight". | 1.0 x RER **of ideal weight**. Not offered. |
| **Light work / very active** | AAHA-BOX1 work "Light: 1.6–2.0". OSU "Active, working dogs" 2.0–5.0. FEDIAF moderate activity 1–3 h/day: 110 (low impact) / 125 (high impact) kcal/kg^0.75 = DERIVED 1.57 / 1.79. | **2.0** (1.6–2.0). |
| **Moderate work** | AAHA-BOX1 "Moderate: 2.0–5.0". OSU 2.0–5.0 (one bucket). FEDIAF high activity 3–6 h/day, working dogs e.g. sheep dogs: 150–175 = DERIVED 2.1–2.5. | **3.0** (2.0–5.0). Your 3.0 sits inside AAHA's range. |
| **Heavy work** | AAHA-BOX1 "Heavy: 5.0–11.0". FEDIAF racing sled dogs, extreme cold: 860–1240 kcal/kg^0.75 = DERIVED 12–18 x RER. | AAHA says **5.0–11.0**. Your "4–8" is **not** in any source I could read (it is a common textbook figure, but UNVERIFIED here). If you offer it, use 5.0 (5.0–11.0) and tell users to work with a vet. My recommendation is not to offer a "heavy work" option on a consumer calculator. |
| **Puppy 0–4 months** | MERCK "< 4 months old" 3. AAHA-BOX1 growth "<4 mo: 3.0". OSU "Puppy 0–4 months" 3.0. PNA growth 2–3 (higher when young, "start decreasing around 4 months of age"). FEDIAF equation [254.1 − 135 x (actual/expected adult BW)] x BW^0.75 = DERIVED about 3.0–3.4 x RER at 10–30 % of adult weight. | **3.0** (2.5–3.0+). |
| **Puppy 4 months to adult** | MERCK "> 4 months old" 2. AAHA-BOX1 "≥4 mo: 2.0". OSU "Puppy 4 months to adult" 2.0. FEDIAF equation = DERIVED 2.7 at 50 % of adult weight, 2.1 at 80 %, 1.7 at 100 %. | **2.0** (2.0–2.7, tapering to adult). AAHA-2021: growth diet "until approximately 1 yr" for small/medium dogs; large/giant "closer to 15–16 mo". |
| **Senior** | No multiplier in MERCK, AAHA, PNA or OSU. AAHA-SENIOR: "Maintenance energy requirements (MER) generally decrease over a dog's lifetime" and "senior dogs have decreased MER". FEDIAF Table VII-6: "> 7 (senior dogs) 95 (80 - 120)" kcal/kg^0.75 = **DERIVED 1.36 (1.14–1.71) x RER**; FEDIAF adds that older dogs "which are still playing and running will need more energy". FEDIAF-SENIOR: "At present there are no experimental data dealing with energy or nutrient requirements for older dogs"; "In most cases, increasing age is associated with a reduced energy requirement". BERM-2014 meta-analysis: "The age of the dog had no effect on maintenance energy requirements" (limited data on old dogs). | **1.4** (1.2–1.6). Best numeric anchor is FEDIAF's 95 kcal/kg^0.75 (=1.36). Present it as a soft estimate. |
| Gestation (reference) | AAHA-BOX1 "3.0 (for last 21 days)". PNA 1.6–2.0, with footnote: dogs "remain relatively stable until the third trimester". FEDIAF: 132 x BW^0.75 first 4 weeks (= DERIVED 1.9), plus 26 x kg BW in the last 5 weeks. | Reference only. |
| Lactation (reference) | AAHA-BOX1 "3.0 to ≥6.0". PNA 2–6. FEDIAF litter-size equations. | Reference only. |

**Individual variation:** PNA: "individual animals can vary by as much as 50% from the predicted values". OSU: "can vary by as much as 50% from calculated values". MERCK: "any calculation of caloric intake is only a starting point". AAHA-BOX1 footnote: "Sedentary and/or indoor pets may require less caloric intake than indicated above. Adjustment of caloric intake should be done by monitoring BW and BCS."

---

## 2. Cats

| Factor | What the sources say | Recommended value (range) |
|---|---|---|
| **Neutered adult** | MERCK 1.2. PNA 1.2. AAHA-BOX1 1.2–1.4. FEDIAF (below) puts neutered *and/or indoor* cats lower, at about 0.65–0.95 x RER. | **1.2** (1.0–1.4) as the textbook neutered value. See §4 for the indoor case. |
| **Intact adult** | MERCK 1.4. PNA 1.4. AAHA-BOX1 1.4–1.6. FEDIAF "Active cats" 100 kcal/kg^0.67 = DERIVED about 1.25–1.3 x RER at 4–5 kg. | **1.4** (1.4–1.6). |
| **Inactive / indoor / obese-prone** | MERCK "obesity prone" 1. PNA "overweight-prone/inactive" 1.0. AAHA-BOX1 "inactive/obese prone" 1.0. FEDIAF Table VII-9 "Neutered and/or indoor cats" 52–75 kcal/kg^0.67 (FEDIAF text: "average maintenance energy requirement is estimated to be 75 kcal/kg BW0.67") = DERIVED 0.66–0.95 x RER at 4.5 kg. | **1.0** (0.8–1.2). |
| Weight loss (reference only) | AAHA-BOX1 0.8. PNA "weight loss/obese" 0.8–1.0. | 0.8 x RER of ideal weight. Not offered. |
| **Kitten** | MERCK 2.5 (single value; footnote: "Kittens can alternatively be fed free choice"). AAHA-BOX1 growth 2.5 (single value). PNA growth 2–3, higher when young and decreasing from about 4 months. FEDIAF Table VII-10, as multiples of adult MER: "Up to 4 months 2.0 - 2.5; 4 to 9 months 1.75 - 2.0; 9 to 12 months 1.5". **DERIVED** conversion to RER (assuming FEDIAF's adult MER is its active-cat 100 kcal/kg^0.67 at the kitten's current weight, which is 1.3–1.4 x RER at 1–3 kg): up to 4 mo ≈ 2.7–3.6; 4–9 mo ≈ 2.3–2.7; 9–12 mo ≈ 1.9. | **Simple, best-sourced:** 2.5 flat (Merck + AAHA). **If you want age bands:** <4 mo 3.0, 4–9 mo 2.5, 9–12 mo 2.0. The bands are defensible only through the DERIVED FEDIAF conversion plus PNA's "decreasing from 4 months"; no source states "3.0 x RER for kittens" directly. A 2024 Veterinary Practice table (via search snippet only, page now 404, **UNVERIFIED**) gave a lower set (2.0 to 4 mo). Kittens grow on a diet for growth until about 1 yr (AAHA-2021). |
| **Senior** | No multiplier in MERCK, AAHA-BOX1, PNA. AAHA-SENIOR: "MER decrease in cats up to age 10, after which they start to increase. However, published data on the effects of age on energy requirements are extremely variable in cats"; "Proteins are less digestible in 20% of cats older than 11 yr, and fats are less digestible in up to 33% of cats"; "Healthy senior cats may require higher-caloric-density, higher-protein diets". AAHA-2021: "Energy intake can be higher for senior cats to compensate for this decrease in digestibility." OSU (citing UK Feline Advisory Bureau): mature 7–10, senior 11–14, geriatric 15+. | **1.2** (1.1–1.4) for cats ≥11, and never lower than the adult factor. Say plainly that geriatric cats (about 12–13+) often need *more*, not less, and that unexplained weight loss in an old cat needs a vet. |
| Gestation / lactation (reference) | AAHA-BOX1 gestation 1.6–2.0, lactation 2.0–6.0. PNA gestation 2–3, lactation 2–6. | Reference only. |

---

## 3. Consistency check 1: dog article (neutered adult)

Article: 10 lb 300–400, 30 lb 700–900, 50 lb 1,000–1,300, 70 lb 1,300–1,700, 90 lb 1,600–2,000.

Back-calculated, every band is RER x **1.37–1.84** (10 lb 1.38–1.84; 30 lb 1.41–1.81; 50 lb 1.37–1.79; 70 lb 1.39–1.82; 90 lb 1.42–1.77). The arithmetic is internally consistent.

**Is it defensible? Partly, and it leans high for a *neutered* dog.**
- The lower half (1.4–1.6) is exactly AAHA's neutered range, and 1.6 is Merck/PNA/OSU's neutered value.
- The top (1.8) is the **intact** factor in Merck/PNA/OSU/AAHA. Calling 1.8 a neutered figure is not supported.
- **WSAVA-DOG chart runs lower.** Every row of WSAVA's dog table works out to exactly 1.19–1.50 x RER (about 84–105 kcal x BW^0.75), e.g. 10 kg: 470–590; 30 kg: 1,080–1,346. (Oddity worth knowing: the chart's footer cites NRC "Active Adult: 130" and "Inactive Adult: 95" kcal x BW^0.75, which would give 1.36–1.86 x RER; the printed numbers do not match those formulas. I report the table as printed.) At the article's weights the WSAVA-equivalent is: 10 lb 261–326, 30 lb 595–744, 50 lb 873–1,091, 70 lb 1,124–1,404, 90 lb 1,357–1,696. So the article's bands overlap WSAVA's upper half and extend about 20 % above it.
- TUFTS: label feeding directions "overestimated the calorie needs of many adult dogs and cats"; one food gave "61% more calories than a dog would really need".

**Suggested fix (pick one):**
- Neutered = RER x 1.4–1.6 (AAHA neutered range, tightest sourcing): 10 lb 305–350; 30 lb 695–795; 50 lb 1,020–1,165; 70 lb 1,310–1,500; 90 lb 1,585–1,810.
- Or RER x 1.2–1.6 (WSAVA chart low end to Merck neutered): 10 lb 260–350; 30 lb 595–795; 50 lb 875–1,165; 70 lb 1,125–1,500; 90 lb 1,355–1,810.
- Or keep 1.4–1.8 but label it "adult dog (neutered to intact)", not "neutered adult".

## 4. Consistency check 2: cat article (indoor neutered)

Article: 8 lb 150–200, 10 lb 180–250, 12–14 lb 230–320. Back-calculated: 8 lb 0.82–1.09; 10 lb 0.83–1.15; 12 lb 0.92–1.28; 14 lb 0.82–1.14 x RER. Centre ≈ 1.0, as you said. (The single 12–14 lb band is coarse: it is 0.92–1.28 at 12 lb but 0.82–1.14 at 14 lb. Consider splitting it: 12 lb ≈ 200–300, 14 lb ≈ 225–335 at 0.8–1.2.)

**Is "1.0 x RER is typical for an indoor neutered cat" defensible? Yes, with one honest caveat.**

Support:
- **FEDIAF (strongest):** "For indoor and/or neutered adult cats the average maintenance energy requirement is estimated to be 75 kcal/kg BW0.67", Table VII-9 range 52–75 kcal/kg^0.67. At 4.5 kg that is 143–207 kcal/day = DERIVED 0.66–0.95 x RER. FEDIAF also says that although NRC's 100 kcal/kg^0.67 is meant for lean cats, "many lean cats may need less energy".
- AAHA-BOX1 footnote: "Sedentary and/or indoor pets may require less caloric intake than indicated above"; AAHA, MERCK and PNA all put inactive/obese-prone cats at 1.0.
- BERM-2014, summarising the 2010 cat meta-analysis (Bermingham et al., Br J Nutr 2010;103:1083–93): "Its key finding was that the maintenance energy requirements reported by the NRC [1] over-estimate the true energy requirements of the domestic cat."
- TUFTS: label directions "overestimated the calorie needs of many adult dogs and cats", because they were derived from more active animals.
- AAHA-2021: "Energy requirements are reduced after spaying or neutering, necessitating intake reduction."

Caveat (state it in the article or the calculator):
- The **standard neutered factor is 1.2** (Merck, PNA; AAHA 1.2–1.4). 1.0 is the textbook "inactive/obese-prone" value. So say "many indoor neutered cats need roughly 1.0–1.2 x RER", not "the typical cat is 1.0".
- **WSAVA-CAT chart is higher than the article.** At 4.5 kg (9.9 lb) it says 240–270 kcal/day = DERIVED 1.11–1.25 x RER; its bounds are NRC's obese-prone 130 x BW^0.4 and lean 100 x BW^0.67. The article's 10 lb 180–250 sits mostly below WSAVA and mostly above FEDIAF. Both are defensible "starting points", and they disagree by about 25 %. PNA and OSU both say individuals vary by up to 50 %.
- **I could not verify** the often-quoted Tufts line that an 8–10 lb cat needs 200–250 kcal/day or that a 12 lb cat needs about 250 (search snippets only; the tuftscatnip.com article now 404s). Do not cite it.

**Calculator recommendation:** default neutered adult cat = 1.2, plus an "indoor / low activity / gains weight easily" option = 1.0. That matches Merck, PNA and AAHA exactly and still lets the article say most indoor cats land near 1.0–1.2.

---

## 5. Treats: the 10 % rule

- **AAHA-2021 (best citation):** the complete and balanced diet should be "≥90% of total intake whereas treats and other food items make up ≤10%"; also "Advise owners to maintain all additional item intake to ≤10% total daily caloric intake to avoid disrupting nutrient balance of the primary diet." Also: non-complete food calories above 10 % "dilute essential nutrients and provide excess calories".
- **TUFTS (Petfoodology 2019):** treats, table food, chews etc. should provide "no more than 10% of your pet's total daily calorie needs".
- **PNA-CALC:** "Treats should not exceed 10% of total daily Calories", and the calculator reduces the main food by 10 % when treats are included.

## 6. kcal per cup / per can

The only peer-reviewed numbers I found are for **weight-management** foods (LINDER-2010, 93 diets):
- Dry dog: 217–440 kcal/cup (median 301).
- Canned dog: 189–398 kcal/can (median 310).
- Dry cat: 235–480 kcal/cup (median 342).
- Canned cat: 78–172 kcal/can (median 146).

Mainstream (non-diet) dry foods are commonly quoted at about 300–500+ kcal/cup, and cat cans vary with can size (3 oz vs 5.5 oz), but I found **no authoritative source** giving a general range. Summers et al. (JVIM 2020, DOI 10.1111/jvim.15858) found senior cat foods "highly variable in their caloric density". AAHA-2021: "AAFCO requires calorie content on dog and cat food labels, including snacks and treats".

**Recommendation:** make kcal/cup and kcal/can a required input taken from the label. If you show an example default, label it as an example (e.g. "about 350–400 kcal/cup is common for adult dry dog food; check your bag") and do not attribute it to a source.

## 7. Suggested final factor table for the calculators

| Species | Option | Factor | Range shown/mentioned | Primary citations |
|---|---|---|---|---|
| Dog | Neutered adult | 1.6 | 1.4–1.6 | Merck, PNA, OSU, AAHA |
| Dog | Intact adult | 1.8 | 1.6–1.8 | Merck, PNA, OSU, AAHA |
| Dog | Inactive / gains weight easily | 1.2 | 1.0–1.4 | AAHA 1.0–1.2; PNA/OSU 1.2–1.4; Merck 1.4 |
| Dog | Senior (≥7 y, less active) | 1.4 | 1.2–1.6 | FEDIAF 95 (80–120) kcal/kg^0.75; AAHA-Senior (MER decreases) |
| Dog | Very active / light work | 2.0 | 1.6–2.0 | AAHA light work |
| Dog | Moderate work | 3.0 | 2.0–5.0 | AAHA moderate; OSU 2.0–5.0 |
| Dog | Puppy <4 months | 3.0 | – | Merck, AAHA, OSU |
| Dog | Puppy 4 months to adult | 2.0 | 2.0–2.7 | Merck, AAHA, OSU; FEDIAF growth equation |
| Cat | Neutered adult | 1.2 | 1.0–1.4 | Merck, PNA; AAHA 1.2–1.4 |
| Cat | Intact adult | 1.4 | 1.4–1.6 | Merck, PNA, AAHA |
| Cat | Indoor / inactive / gains weight easily | 1.0 | 0.8–1.2 | Merck, PNA, AAHA; FEDIAF indoor/neutered 52–75 kcal/kg^0.67 |
| Cat | Kitten | 2.5 (or bands <4 mo 3.0 / 4–9 mo 2.5 / 9–12 mo 2.0) | 2–3 | Merck, AAHA (2.5); PNA 2–3; FEDIAF Table VII-10 (DERIVED) |
| Cat | Senior (≥11 y) | 1.2 | 1.1–1.4 | AAHA-Senior/AAHA-2021: needs may rise after ~10 y; no numeric factor exists |

Every source uses the same wording, which the calculator should repeat: this is a **starting point**, and you adjust it using body weight and body condition score.

Copyright note: the WSAVA toolkit is copyrighted, and WSAVA asks users to follow an SOP and request form before using its materials (https://wsava.org/wp-content/uploads/2024/05/SOP-for-use-of-the-WSAVA-GNC-Toolkits-and-Guidelines-v4.pdf). Cite and link the WSAVA charts; do not reproduce the tables on the site.
