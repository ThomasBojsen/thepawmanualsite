# Cat Breed Selector dataset: sources and notes

Companion to `cat-breeds.json` (25 entries, validated with `node -e "JSON.parse(...)"`).
Researched 2026-10-22. Internal notes, not page copy.

## 1. How the list was chosen

- **CFA (US), 2026 list, based on 2025 registrations:** 1 Maine Coon, 2 Ragdoll, 3 Persian, 4 Exotic,
  5 Devon Rex, 6 Abyssinian, 7 British Shorthair, 8 Siberian, 9 American Shorthair, 10 Russian Blue.
  https://cfa.org/cat-talk/most-popular-breeds-for-2026/ . All ten are in the dataset.
- **GCCF (UK):** British Shorthair is clearly #1 (about 6,481 registrations in 2024), followed by Ragdoll
  (about 3,061) and Maine Coon (about 2,642). These figures come from a secondary summary of GCCF tables
  (https://select.keenable.ai/r/cat-breed-popularity-across-two-centuries/49nD6c7X-u2tFiGQRclU4w), because
  I could not reach GCCF's own statistics page (404). Treat the exact counts as unverified. GCCF's breed
  directory (https://www.gccfcats.org/getting-a-cat/choosing/cat-breeds/) supplied the European-popular
  breeds: Birman, Burmese, Norwegian Forest Cat, Siberian, Oriental, Tonkinese, Chartreux, Turkish Van, Somali.
- **TICA:** TICA's site returned 403 every time, so I could not cite its rankings directly. Bengal (TICA's
  flagship breed), Sphynx, Selkirk Rex and RagaMuffin are included because of TICA/CFA prominence and search demand.
- **FIFe:** I found no FIFe registration statistics that I could access. "Popular in Europe" rests on GCCF plus
  the GCCF directory, so it is UK-weighted. Say so if the page claims pan-European popularity.

**Deliberately excluded (an editorial call, please confirm):**
- **Scottish Fold.** Popular with CFA (it has been on CFA top-10 lists), but the fold comes from a cartilage
  defect (osteochondrodysplasia) that affects every folded cat. GCCF and FIFe do not recognise the breed (it is
  absent from the GCCF directory above), and veterinary welfare bodies have called for its breeding to stop. A
  selector that "matches" readers to it would be recommending a welfare problem. If it is added later, pair it
  with a health warning instead of a score.
- **Savannah.** A wild-cat hybrid that is restricted or needs a permit in parts of the US and some European
  countries; it is a poor fit for a general "find your breed" quiz.
- **Himalayan.** CFA treats it as a Persian colour division, so it would duplicate the Persian entry.

## 2. Sources for weight and lifespan, by breed

**Main rule:** I used the GCCF breed page's "Breed characteristics" box, because it is one registry giving
the same kind of figure for every breed. I departed from it only where GCCF's figure was a single number, contradicted itself,
or disagreed sharply with CFA. Each departure is marked **[flag]**. GCCF pages are at
`https://www.gccfcats.org/getting-a-cat/choosing/cat-breeds/<slug>/`. CFA pages (`https://cfa.org/breed/<slug>/`) were
the second source for temperament and, where they give one, for weight. CFA breed pages almost never give a lifespan.
`weightKg` is converted from `weightLb` (×0.4536, rounded to 0.1 kg).

**Population-level lifespan data, for context.** These figures are not comparable with breed-standard ranges:
- O'Neill et al. 2015, J Feline Med Surg 17(2):125–133, median age at death in England (PMC10816413,
  https://pmc.ncbi.nlm.nih.gov/articles/PMC10816413/): Birman 16.1 (n=12), Burmese 14.3, Siamese 14.2,
  Persian 14.1, crossbred 14.0 (n=3,621), British Shorthair 11.8, Maine Coon 11.0, Ragdoll 10.1, Abyssinian 10.0,
  Bengal 7.3 (n=15).
- Teng et al. 2024, J Feline Med Surg, VetCompass life tables (DOI 10.1177/1098612X241234556), life expectancy at
  birth in the UK: Burmese 14.42, Birman 14.39, crossbred 11.9, Siamese 11.7, Persian 10.93, Maine Coon about 9.7,
  Bengal about 8.5, Sphynx 6.68; all cats 11.74. I could not open the full table (403), so the Persian, Maine Coon
  and Bengal values come from press coverage (phys.org, CNN syndication and AOL summaries).
- **Why these differ from the dataset:** breed-standard ranges describe a healthy cat's typical span. VetCompass
  figures include deaths at every age, accidents among them, and the small breed samples have wide confidence
  intervals. Several breed ranges in the JSON are therefore far above what the VetCompass data shows. That is flagged per breed below,
  and the page copy should not present breed lifespans as a guarantee.

| id | weightLb | lifespan | Sources and flags |
|---|---|---|---|
| domestic-shorthair | 8–12 | 12–15 | PDSA (https://www.pdsa.org.uk/pet-help-and-advice/looking-after-your-pet/kittens-cats/domestic-breeds): average weight "about 4 kg", lifespan 12–15. **[flag]** No authoritative source gives a weight *range* for mixed-breed cats: 8–12 lb (3.6–5.4 kg) is the band most vet and pet-care sites quote around PDSA's 4 kg average, but individuals vary widely and body condition score matters more than the scale. VetCompass: crossbred median 14.0 (2015), life expectancy at birth 11.9 (2024). |
| maine-coon | 10–20 | 9–15 | GCCF box (10–20 lb, 9–15 yr). GCCF's health section says 10–15 yr. CFA: mature males "reach or exceed 20 pounds". **[flag]** The site's own `/cats/breeds/maine-coon/` page says 12–15 yr. VetCompass: median 11.0, life expectancy about 9.7. |
| ragdoll | 10–20 | 12–17 | GCCF box. GCCF's health section: males 12–20 lb, females 8–15 lb. CFA: males can exceed 20 lb. VetCompass median 10.1 (n=21). This matches the site's Ragdoll page. |
| persian | 6–12 | 14–16 | GCCF box (6–12 lb; its care text says males weigh "more than 12 lbs"). **[flag]** GCCF contradicts itself on weight, and the site's Persian page says 7–12 lb and 12–17 yr. CFA gives no figures. VetCompass: median 14.1, life expectancy 10.93. |
| exotic-shorthair | 7–14 | 12–15 | GCCF box. CFA: "medium to large", no figures. |
| devon-rex | 6–9 | 12–16 | GCCF box. CFA agrees on weight ("six to nine pounds"). |
| abyssinian | 8–10 | 12–16 | GCCF box: weight 8–10 lb, lifespan "15 years". I used the 12–16 yr given in GCCF's own care section. **[flag]** 8–10 lb is a narrow range, and CFA gives no figures. VetCompass median 10.0 (n=11, small sample). |
| british-shorthair | 7–17 | 14–20 | GCCF box (males 9–17 lb, females 7–12 lb). CFA: males "as large as 17 pounds". **[flag]** 14–20 yr is optimistic: VetCompass median 11.8 (n=69), and the site's British Shorthair page says 12–17 yr. Consider using the page's 12–17. |
| siberian | 8–18 | 12–15 | **[flag]** Weight from CFA (males 12–18 lb, females 8–12). GCCF gives 13–26 lb (males 17–26), well above every other source, so I did not use it. Lifespan from the GCCF box. |
| american-shorthair | 6–15 | 15–20 | Weight from CFA ("Learn more about the American Shorthair": males 11–15 lb, females 6–12). Hill's says females 8–12 and males over 12. **[flag]** GCCF does not register the breed, and neither registry gives a lifespan. 15–20 yr comes from Hill's (https://www.hillspet.com/cat-care/cat-breeds/american-shorthair), and Purina and Royal Canin say the same. It is a pet-food-company figure and on the generous side. |
| russian-blue | 7–12 | 12–15 | **[flag]** The registries disagree on weight: GCCF 8–15 lb, CFA males "rarely exceeding 10", females 5–8. I used Hill's 7–12 (https://www.hillspet.com/cat-care/cat-breeds/russian-blue), which sits between them. Lifespan from the GCCF box; Hill's says 15–20. |
| bengal | 8–15 | 12–16 | GCCF box. CFA gives no figures. **[flag]** VetCompass shows Bengals short-lived in UK vet data (median 7.3, n=15; life expectancy about 8.5). The site's Bengal page says 12–16, the same as GCCF. |
| siamese | 8–15 | 10–15 | GCCF box (males 10–15 lb, females 8–12); GCCF's text adds that Siamese "often" live into their late teens. **[flag]** The site's Siamese page says 8–12 lb and 12–20 yr, a clear mismatch. VetCompass: median 14.2, life expectancy 11.7. Decide which range the site uses everywhere. |
| sphynx | 6–11 | 8–15 | GCCF box: 6–11 lb, "15 years". Hill's (https://www.hillspet.com/cat-care/cat-breeds/sphynx): under 12 lb, 8–14 yr. **[flag]** The 8–15 range takes its ends from those two sources. VetCompass gives the Sphynx the shortest life expectancy of any breed analysed (6.68 yr). |
| birman | 10–15 | 12–16 | GCCF box. VetCompass rates it among the longest-lived (median 16.1, life expectancy 14.39). |
| burmese | 6–14 | 10–17 | GCCF box (males 11–14 lb, females 6–9). GCCF's care text says 16–18 yr, which contradicts its own box. CFA: males "reach or exceed 12 pounds". VetCompass: longest life expectancy at birth (14.42). |
| norwegian-forest-cat | 9–16 | 14–16 | GCCF box. CFA agrees on weight (males 12–16 lb, females 9–12). |
| oriental-shorthair | 8–14 | 12–15 | GCCF box (GCCF "Oriental"). CFA gives no figures. |
| cornish-rex | 5–10 | 9–16 | Weight from the GCCF box (males 8–10 lb, females 5–7). **[flag]** The lifespan sources contradict each other: GCCF says 16 yr, "up to 20" in its text, and Hill's (https://www.hillspet.com/cat-care/cat-breeds/cornish-rex) says 9–13. The 9–16 range takes its ends from those two. |
| tonkinese | 6–12 | 10–16 | Weight from the GCCF box. **[flag]** GCCF gives only "15+ years", so 10–16 is the range most secondary profiles quote (Guild Insurance VetsChoice and others). No vet-school or registry range was available, so confidence is low. |
| ragamuffin | 10–20 | 12–16 | GCCF box. CFA: males up to 20 lb, females nearer 15. |
| selkirk-rex | 9–16 | 12–15 | Weight from the GCCF box; CFA says males 12–15 lb. **[flag]** GCCF gives "15 years" and Hill's ">15". 12–15 comes from the Purina Australia profile (https://www.purina.com.au/selkirk-rex.html), and other sources range from 10–15 to 15–20. |
| turkish-van | 7–20 | 12–14 | GCCF box (males 10–20 lb, females 7–12). CFA: males "may weigh 20 pounds". |
| chartreux | 11–18 | 12–15 | GCCF box. **[flag]** CFA says males "as much as 14 pounds" and females "significantly smaller", and Hill's says over 12 lb and 8–13 yr. The GCCF weight looks high at the bottom end, and its lifespan high compared with Hill's. |
| somali | 8–12 | 12–14 | GCCF box. CFA gives no figures. |

## 3. Trait scores (our own editorial ratings)

The scores are not copied from any site's rating set. For each breed I read the GCCF breed page and the CFA breed page
(and Hill's where it was fetched) and settled each score by cross-checking the two. Neither registry uses numeric scales, so
no score set exists to copy. Calibration anchors:
- **vocal:** 5 = Siamese, Oriental (both registries stress a loud, frequent voice); 1 = Persian, Exotic, Ragdoll,
  British Shorthair, Chartreux (quiet, "seldom used" voice).
- **grooming:** 5 = Persian (daily combing, per GCCF); 4 = Sphynx (regular bathing and ear cleaning) and Siberian
  (GCCF: several times a week); 1 = short single or rex coats that need hardly any brushing.
- **shedding:** hairless and rex coats get 1, long double coats and plush coats get 4–5. Low shedding says nothing
  about allergens (see section 4).
- **aloneTolerance:** 1 = breeds where a source explicitly says they should not be left alone or need a companion cat
  (Siamese, Oriental, Sphynx); 5 = American Shorthair (CFA: "independent enough to be left alone for long periods").
- **firstTimeOwner:** combines grooming load, energy, health complexity and tolerance of handling. The domestic
  shorthair, Ragdoll, Birman, British Shorthair, American Shorthair, RagaMuffin and Chartreux get 5. The Persian gets 2
  (daily grooming, problems from its flat face), and so do the Bengal and Sphynx.
- **domestic-shorthair:** set mid-scale (mostly 3) because individuals vary, except for low grooming, high
  aloneTolerance and firstTimeOwner 5. The quiz should present it as "varies by cat; meet the individual".

**Where the sources disagreed, and the score I chose:**
- **Bengal, vocal:** GCCF says "very vocal and can be quite strident"; CFA says it varies by cat. Scored 4.
- **Siberian, energy:** GCCF says "huge reserves of energy"; CFA says curious "without being hyperactive". Scored 4.
- **Devon Rex, vocal:** GCCF says quiet, chirps mainly; CFA says chatty but soft. Scored 3.
- **Somali, vocal:** GCCF says talkative, "love to chirrup"; CFA says it communicates mostly through body language. Scored 3.
- **Turkish Van, vocal:** GCCF says soft voice; CFA says vocal. Scored 3.
- **Burmese, aloneTolerance:** GCCF says "intensely social" and dislikes being neglected; CFA says it can entertain itself when left alone. Scored 3.
- **Norwegian Forest Cat, aloneTolerance:** GCCF says it can amuse itself during the day; CFA says it seeks company if left too long. Scored 4.
- **Selkirk Rex, grooming:** GCCF says daily combing; CFA says a comb once or twice a month; Hill's says weekly. Scored 3.
- **Siamese, otherPets:** CFA says it often gets on well with other pets; GCCF warns it may not accept rivals for its
  person's affection. Scored 3. Neither registry addresses children; kids scored 3.
- **Maine Coon, grooming:** GCCF says weekly; CFA says two or three times a week. Scored 3.
- **Coat category:** semi-longhairs whose coats resist matting (Ragdoll, Birman, RagaMuffin, Turkish Van, Somali) are
  "medium". Maine Coon, Norwegian Forest Cat, Siberian and Persian are "long". Selkirk Rex comes in short and long
  versions and is marked "medium". The domestic cat is "short", but domestic medium- and longhairs exist too.

## 4. Allergies: "no cat breed is hypoallergenic"

Neither the dataset nor any copy built on it should claim a breed is hypoallergenic. What the sources support:
- **Every cat produces Fel d 1**, the main cat allergen. It is made mostly in saliva and skin glands, not by the hair,
  so coat length and shedding do not decide it. GCCF says outright that the Sphynx "is not a hypoallergenic cat despite
  the claims of some people" and that the Devon Rex is not truly hypoallergenic. CFA says the same of the Devon Rex
  and the Sphynx ("not low-allergen").
- **Breeds often *reported* as lower-allergen:** Siberian (and its colourpoint variant, the Neva Masquerade), Balinese,
  Russian Blue, Devon Rex, Cornish Rex, Sphynx and Oriental. Evidence quality:
  - *Siberian:* weak to moderate. Allergen-testing projects run by breeders report that about half of Siberians tested
    were lower than other breeds and under 20% very low, but every cat produced some, and individuals ranged widely
    (siberianresearch.com, a breeder-affiliated source with an interest in the result). A small 2014 conference
    abstract (Satorina et al., Clin Transl Allergy, poster P11; PMC4072467) found 8 "hypoallergenic" cats, Neva
    Masquerade per the Vienna thesis record, spread less Fel d 1 onto their fur than 6 ordinary cats. The sample is
    tiny, and it is not peer-reviewed as a full paper.
  - *Rex breeds and Sphynx:* the claim rests on less shedding, not less allergen. The registries themselves deny the
    breeds are hypoallergenic. Evidence: anecdotal.
  - *Russian Blue, Balinese, Oriental:* breeder and pet-site claims only; I found no controlled study. Evidence: anecdotal.
- **What varies more than breed:** Bastien et al. 2019 (J Feline Med Surg; 64 neutered domestic shorthairs followed for a
  year) found an 80-fold difference in salivary Fel d 1 between the highest and lowest cats, with large swings within
  the same cat over time, and no link to sex, coat colour or body size. Purina material, which is not peer-reviewed,
  says intact males produce more Fel d 1 than neutered ones.
- **Practical guidance for copy:** advise spending time with the specific cat before committing, and suggest the reader
  speaks to an allergist. Do not promise a breed will work. This matches the site's existing
  `/cats/breeds/hypoallergenic-cats/` page, which is worth cross-checking against this section.

## 5. Mismatches with the site's own breed pages (fix before launch)

These six entries have a `path`, so the quiz will link to these pages, and the numbers should match:
- **British Shorthair:** the page says 12–17 yr; the JSON says 14–20 (GCCF).
- **Persian:** the page says 7–12 lb and 12–17 yr; the JSON says 6–12 lb and 14–16 yr.
- **Maine Coon:** the page says 12–15 yr; the JSON says 9–15 (GCCF). The page's sex-split weight (males 13–18 lb or more,
  females 8–12) fits inside the JSON's 10–20 except for the bottom of the female range.
- **Siamese:** the page says 8–12 lb and 12–20 yr; the JSON says 8–15 lb and 10–15 yr.
- **Ragdoll and Bengal:** they match.
Option: align the JSON to the pages for these four, which are all defensible within the sources above, or update the pages.

## 6. Source access problems

- TICA (tica.org): 403 on every breed page, so no TICA figure is cited.
- Purina US (purina.com): 403. Purina Australia was used once (Selkirk Rex).
- The RVC press release and PeerJ 2025 (Mata, "moggies and mollies live longer"): 403. The figures come from
  secondary coverage and the PMC copy of O'Neill 2015.
- I found no breed pages from International Cat Care or VCA, so PDSA, GCCF, CFA and Hill's are the fallbacks.

## 7. How this was applied (2026-10-22)

- The dataset now lives inside `wp-content/plugins/pawmanual-calculators/calculators/cat-breed/cat-breed.js` (between `BREEDS-DATA-START` and `BREEDS-DATA-END`). Edit it there; this file is the source record.
- Section 5's four mismatches were resolved in favour of the site's own breed pages, since the selector links to them and the page figures are defensible: British Shorthair lifespan 12–17, Maine Coon lifespan 12–15, Persian 7–12 lb (3.2–5.4 kg) and 12–17 years, Siamese 8–12 lb (3.6–5.4 kg) and 12–20 years. Revisit both together if a page is fact-checked to different numbers.
- Scottish Fold, Savannah and Himalayan stay out, as recommended (Scottish Fold matches the site-wide "not recommended" stance in `content/README.md` rule 9).
