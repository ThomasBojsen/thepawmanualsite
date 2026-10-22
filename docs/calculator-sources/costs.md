# Pet cost calculator: source research (USD sanity check + Western European EUR set)

> Research record behind `calculators/pet-cost/pet-cost.js`, 2026-10-22. Applied as recommended: every USD "ADJUST" line, the full EUR set, dog lifespans small 12–16 / medium 11–14 / large 10–13 / giant 7–10 and cats 12–18. Pet rent kept only as an optional field (help text says it is rare in Europe); dog tax and liability insurance go in the optional "other yearly costs" field. Dog wet/mixed food uses multipliers 5× / 2× derived from Tufts 2022 (wet costs about 5× dry per calorie), an estimate.

Researched 2026-10-22. Every figure is tagged with its source and the data year.
**[EST]** = my own estimate derived from the cited sources (method shown).
**[WEAK]** = commercial/SEO or undated source; usable for a range, not as a headline number.
Rover's own pages (rover.com/blog/cost-of-owning-a-dog/, cost-of-owning-a-cat) returned HTTP 403 to every fetch, so Rover figures below come via secondary write-ups that quote them. Treat them as second-hand.

---

## Key sources (short list)

| ID | Source | Year | URL |
|---|---|---|---|
| ASPCA | ASPCA "Cutting Pet Care Costs" table ("2021 UPDATE") | 2021 | https://www.aspca.org/pet-care/general-pet-care/cutting-pet-care-costs |
| SYF25 | Synchrony/CareCredit 2025 Pet Lifetime of Care Study (press release, 2 Jun 2025) | 2025 | https://www.carecredit.com/pressrelease/synchrony-2025-pet-lifetime-of-care-study/ |
| SYF22 | Synchrony 2022 study page (first-year ranges) | 2022 | https://www.synchrony.com/synchrony-study-reveals-pet-owners.html |
| ROV25 | Rover True Cost of Pet Parenthood 2025, via GlobalPETS | 2025 | https://globalpetindustry.com/news/data-insight-2025-pet-parenthood-costs-in-the-us/ |
| ROV25b | Rover 2025 annual ranges, via Dallas Express | 2025 | https://dallasexpress.com/?p=310844 |
| NAPHIA | NAPHIA State of the Industry 2025 (YE-2024 data) | 2024 data | https://naphia.org/news/naphia-news/soi-report-2025/ |
| NAPHIA-b | 2025-data premiums ($836 dog / $435 cat), via GlobalPETS (not verified on naphia.org) | 2025 data | https://globalpetindustry.com/news/us-pet-insurance-hits-record-levels-in-2025/ |
| TUFTS-D | Tufts Petfoodology, "Comparing the costs of different types of dog food" (21 Mar 2022) | 2022 | https://sites.tufts.edu/petfoodology/2022/03/21/pet-food-costs-by-type/ |
| TUFTS-C | Tufts Petfoodology, "Comparing kitty's calorie costs" (17 May 2022) | 2022 | https://sites.tufts.edu/petfoodology/2022/05/17/comparing-cat-food-costs/ |
| AKC | AKC "Cost to raise a dog" (last updated Jan 2018) | 2018 | https://www.akc.org/expert-advice/lifestyle/cost-to-raise-dog/ |
| LIFE-D | McMillan et al., Sci Rep 2024, "Longevity of companion dog breeds" (UK, n=584,734) | 2024 | https://pmc.ncbi.nlm.nih.gov/articles/PMC10834484/ |
| LIFE-C | Teng et al. 2024, JFMS, UK cat life tables (VetCompass, RVC) | 2024 | https://www.rvc.ac.uk/vetcompass/news/new-research-from-the-rvc-predicts-the-future-life-expectancy-for-companion-cats?page=235 |
| RENT | RentCafe pet-friendly renting statistics (2025 data) | 2025 | https://www.rentcafe.com/blog/apartmentsearch-2/pet-friendly-housing-statistics/ |
| GOOSE | Goose 2025 US boarding facility price survey | 2025 | https://www.goose.pet/research/pet-boarding-prices |

European sources are listed inline in Part B.

---

# PART A: USD sanity check

Verdict key: **OK** = supported; **ADJUST** = suggest a change; **FLAG** = keep, but note the caveat.

### A1. First-year (one-time) costs

| Line | Our article | Evidence | Verdict / suggested range |
|---|---|---|---|
| Adoption fee, dog | $50–$500 | City/county shelters ~$25–$300; private humane societies $129–$767 [WEAK, hepper.com via search]; Animal League 2025: $375 puppy, $125 adult dog (https://www.animalleague.org/faq-items/much-adoption-fees/, 2025). | **OK** ($50–$500). |
| Adoption fee, cat | $50–$200 | Animal Humane Society $39–$317 (2025, cited by catster [WEAK]); Animal League 2025: $125 kitten, $75 adult cat. | **OK.** Optionally widen the top to $250. |
| Breeder, dog | $1,000–$3,000+ | Purebred $800–$2,000 typical, show quality more (Northwestern Mutual, https://northwesternmutual.com/life-and-money/the-true-cost-of-owning-a-dog-or-cat, undated). Popular breeds (Frenchie, doodles) often go above $3,000 (general knowledge, no source fetched). | **OK.** |
| Breeder, cat | $500–$1,500+ | Purebred cats $300–$1,200 (same Northwestern Mutual page). | **OK.** |
| Spay/neuter, dog | $150–$600 | ASPCA $300 (2021). Low-cost clinics ~$50–$150; private vets ~$250–$600+ for a large dog (webvet.com / thepricer.org via search, 2025–26 [WEAK]). | **OK.** Mention that low-cost clinics can be $50–$150. |
| Spay/neuter, cat | $100–$400 | ASPCA $150 (2021). | **OK**, though $400 is the high end (private vet, female). Could be $75–$400. |
| Vaccine series + initial vet, dog | $200–$500 | ASPCA "initial medical" $300 (2021). | **OK.** |
| Vaccine series + initial vet, cat | $150–$400 | ASPCA $175 (2021). | **OK.** |
| Setup gear, dog | $200–$500 | ASPCA: collar/leash $60, carrier $50, crate $60, grooming tools $40 (sum $210), plus "training assets" $200 (2021). | **OK.** |
| Setup gear, cat | $150–$400 | ASPCA: collar $15, litter box $20, scratching post $15, carrier $40, grooming tools $20 (sum $110) (2021). | **ADJUST** to **$100–$300**. $400 is high for a cat setup. |
| Microchip, license, misc | $50–$150 / $50–$100 | ASPCA microchip $20, license $15 (2021). The registration fee and the vet's implant fee often add up to more. | **OK.** |

First-year totals for context: ASPCA $3,221 dog / $1,904 cat first year (2021, includes insurance $516/$348 and dental $500/$300). Synchrony 2022 study: $1,300–$2,800 dog, ~$960–$2,500 cat in year one (SYF22; the page doesn't print its year, but the methodology, 1,200 owners plus 100 vets, matches the 2022 edition). Rover 2025: upfront $1,150–$4,420 dog, $750–$2,715 cat (ROV25).

### A2. Annual costs

| Line | Our article | Evidence | Verdict / suggested range |
|---|---|---|---|
| Food, dog (overall) | $300–$900+ | ASPCA $300/yr (2021); AKC $435/yr (2018). Tufts 2022, 55 lb dog at 1,100 kcal/day: cheapest dry diet $201/yr; wet-only about 5x dry; fresh food 4–6x the most expensive dry, which puts the most expensive dry at about $900/yr. | **OK** as an overall range. |
| Food, dog by size | small 300–450, medium 450–650, large 650–900, giant 900–1,200 | See the derivation below. Your proposed bands sit at the premium end, especially for small and medium dogs. | **ADJUST:** small **$200–$450**, medium **$350–$650**, large **$550–$950**, giant **$800–$1,400** [EST]. |
| Food, cat | $200–$500 | Tufts 2022, 10 lb cat at 260 kcal/day: dry-only $0.23–$0.97/day (≈ $85–$355/yr); wet-only $1.22–$5.77/day (≈ $445–$2,105/yr). The per-day figures are relayed by a search summary of the Tufts article; the text I fetched confirms the overall span of "$0.23 to nearly $6/day" and "$86 to over $2,100/yr". ASPCA $225 (2021). | **ADJUST:** split it. **Dry $150–$350; mixed $300–$700; wet $600–$1,300** [EST: Tufts 2022 mid-market band, +~10–15% for 2022→2025 food inflation, premium extremes excluded]. $200–$500 only holds for dry or mixed feeding. |
| Routine vet + prevention, dog | $300–$700 | ASPCA routine $225 + preventives $185 = $410 (2021). Synchrony 2025: wellness plans $422–$701/yr. AKC: vet $650 + preventives $389 (2018, includes one illness). | **OK.** |
| Routine vet + prevention, cat | $200–$500 | ASPCA $160 + $140 = $300 (2021). | **OK.** |
| Insurance, dog | ~$360–$840 | NAPHIA average accident & illness (A&I) premium (2024 data): **$749.29/yr** ($62.44/mo). Secondary report of 2025 data: **$836** A&I, $190 accident-only (NAPHIA-b). | **ADJUST:** your range tops out at the national average. Suggest **$450–$1,000** for A&I (average about $750–$840), with a note that accident-only plans run about $150–$250. |
| Insurance, cat | ~$180–$480 | NAPHIA 2024 data: **$386.47/yr** ($32.21/mo); 2025 data: **$435** A&I, $112 accident-only (NAPHIA-b). | **ADJUST:** **$250–$550** A&I (average about $390–$435). |
| Litter, cat | $150–$300 | ASPCA $150 (2021). Clumping litter at $0.75–$1.30/lb (PetSafe, https://www.petsafe.net/learn/cost-comparison-of-litter-litterboxes) × 28–40 lb/month (Hepper [WEAK]) ≈ $250–$440/yr [EST]. | **ADJUST** (minor): **$150–$350**. |
| Toys/chews/gear, dog | $100–$300 | ASPCA toys $37 + treats $60 + grooming supplies $28 (2021); AKC "supplies" $432 (2018). | **OK.** |
| Toys/chews/gear, cat | $50–$150 | ASPCA toys $22 + treats $36 + grooming supplies $28 (2021). | **OK.** |

Annual totals for context: Rover 2025, dog $1,390–$5,295/yr, cat $760–$3,495/yr (ROV25b). ASPCA (2021): $1,391 dog, $1,149 cat.

**Dog food by size: derivation [EST].**
Calorie needs scale with metabolic body weight (kg^0.75), anchored to Tufts' 25 kg (55 lb) dog at 1,100 kcal/day:
- small (~7 kg): ~425 kcal/day ≈ 155 Mcal/yr
- medium (~16 kg): ~790 kcal/day ≈ 288 Mcal/yr
- large (~32 kg): ~1,320 kcal/day ≈ 482 Mcal/yr
- giant (~55 kg): ~1,990 kcal/day ≈ 726 Mcal/yr

Tufts 2022 prices dry food from $0.50/Mcal (cheapest) to about $2.24/Mcal (most expensive). A mainstream-to-premium kibble band of about $0.90–$2.00/Mcal, plus ~10% for treats, gives:
- small $150–$340 (raised to $200–$450, because small-breed bags cost more per kcal)
- medium $285–$635
- large $475–$1,060
- giant $715–$1,600 (trimmed, because giant dogs are usually fed from large, cheaper bags)

The suggested bands round these figures. A weak 2026 cost guide (selector.kurlon.com, via search) gives averages of $350 small, $520 medium, $900 large and $1,350 giant, which lands inside these bands.

### A3. Extras

| Line | Our article | Evidence | Verdict |
|---|---|---|---|
| Dental cleaning | $300–$800+ | $300–$700 for a routine cleaning under anaesthesia (Banfield, via thepricer.org, 2025; webvet 2026); PetMD gives $350 to $1,500+, $500–$1,500 with X-rays and extractions (https://petmd.com/dog/care/how-much-does-dog-teeth-cleaning-cost). ASPCA dental $500 dog / $300 cat (2021). | **OK.** |
| Boarding/sitter per night | $25–$85+ | Goose 2025 facility survey: national average $44.99, median $42, state averages $35.80 (OK) to $62.47 (DC), full range $10–$180. Rover city sitter averages $38.85–$61.70/night (rover.com city pages, via search). ASPCA kennel $25/day (2021). | **OK.** Could say "typically $35–$65". |
| Grooming per visit | $60–$120+ | Sources disagree by roughly 2x: MoeGo full groom small $75–$125 up to XL $200+; PetsCare 2025 guide $25–$150+; chains $40–$80+ [all WEAK]. | **FLAG/OK.** For a high-maintenance coat, $60–$150 is more defensible. |
| Pet rent | $25–$100/mo | RentCafe 2025: average **$35.65/mo** (range $34.88–$37.14 by city size), one-time pet fee averages $315, deposit ~$300. PIHI 2025: $34/mo, deposit $325 (via CounterPunch). | **OK.** Typical is $25–$50; $100 is the high end. |
| Emergency | $1,500–$5,000+ | Pumpkin claims data (2022–25): average emergency bill **$653 dog / $919 cat**; Spot: emergency visits typically $800–$1,500, complex cases above $5,000 (spotpet.com, 2024–25). Exam fee alone $65–$215 (Rover via search). | **FLAG.** $1,500–$5,000+ describes a serious emergency (surgery or hospitalisation), not an average visit. Suggest **"$800–$5,000+ (surgery or hospitalisation is at the upper end)"**. |
| Emergency fund | $2,000–$5,000 | No primary source sets a target; consistent with the above. | **OK** (editorial). |

### A4. Lifespan

| Group | Evidence | Suggested calculator default range |
|---|---|---|
| Small dogs | Median **12.7 yrs** (UK purebreds, LIFE-D 2024) | 12–16 yrs |
| Medium dogs | Median **12.5 yrs** (LIFE-D) | 11–14 yrs |
| Large dogs | Median **11.9 yrs** (LIFE-D) | 10–13 yrs |
| Giant dogs | No giant category in LIFE-D. Breed medians: Mastiff **9.0**, St Bernard **9.3**, Cane Corso **8.1** (LIFE-D). | 7–10 yrs [EST] |
| All dogs | Median **12.5 yrs** (LIFE-D). PDSA quotes O'Neill 2013 size ranges: small 7.1–14.2, medium 8.4–13.5, large 5.5–13.1 (pdsa.org.uk, 2024). | |
| Cats | Life expectancy at birth **11.74 yrs** (UK, Teng et al. 2024, LIFE-C); females 12.51, males 11.18. Burmese and Birman ~14.4, Sphynx 6.68. Cost studies assume 15–16 yrs (Synchrony 15, Rover 16, Allianz 16). | 12–18 yrs (12–15 as a typical default) |

Our "10–16 years" works for dogs overall, but giant breeds fall below it. The 11.74 figure is life expectancy at birth, so it includes early deaths; a cat that reaches adulthood typically lives longer.

---

# PART B: "Western European typical ranges" (EUR)

Built from Germany (DE), the Netherlands (NL), France (FR), Ireland (IE), Denmark (DK), Sweden (SE) and the UK (GBP converted, flagged). No line here is a converted USD figure.
Approximate conversion rates used for the flagged lines: £1 ≈ €1.16; €1 ≈ 7.46 DKK (pegged); €1 ≈ 11 SEK. These are my approximations; check them before publishing.

**Structural notes for the copy:**
- The German vet fee schedule (GOT, revised 2022) sets the 1x rate. Practices bill 1x–3x the rate, and 2x–4x out of hours plus a €50 net emergency fee, with VAT on top (BTK, https://www.bundestieraerztekammer.de/d.php?id=9122; HanseMerkur, https://www.hansemerkur.de/haustiere/tierarzt-notdienstgebuehr).
- UK vet prices rose 63% from 2016 to 2023 (CMA provisional decision, Oct 2025, via BSAVA https://www.bsava.com/?p=73762). Swedish vet costs rose 50% from 2019 to 2025 (SCB, via TV4).
- German pet food costs about 36% more than in 2020 (dpa via t-online, https://www.t-online.de/finanzen/aktuelles/id_101469810/teures-futter-und-hohe-hundesteuer.html, updated Oct 2026).

### B1. First-year (one-time) costs, EUR

| Line | Dog | Cat | Evidence |
|---|---|---|---|
| Adoption (shelter) | **€150–€450** | **€80–€200** | IE DSPCA: €295 puppy, €245 adult dog, €150 kitten, €135 adult cat (fee covers neutering, chip, first 2 vaccines, parasite treatment), https://www.dspca.ie/adopt (current, 2025–26). DE: dog €270–€400 (web.de, https://web.de/magazine/wissen/tiere/tierheim-tier-adoptieren-teurer-komplizierter-40512162); cat about €100 (Berliner Sparkasse, https://www.berliner-sparkasse.de/de/home/privatkunden/kopfgeld-magazin/we-are-family/KostenHaustier.html). AT: €100–€300 (Sparkasse.at). FR: SPA kitten €170–€190 (Santévet, https://www.santevet.com/articles/un-chat-a-quel-prix, Jan 2026). NL: shelter dog about €140 (2022 prices, LICG via Hart van Nederland). DK: Dyrenes Beskyttelse dog about 3,000–4,300 DKK (≈ €400–€575), cat 700–1,450 DKK (≈ €95–€195) [WEAK, skagenonline.dk FAQ, undated; the official site shows fees per animal]. |
| Breeder | **€1,000–€2,500+** | **€600–€1,500+** | NL: pedigree puppy average about €1,250, non-pedigree €450 (2022 prices, LICG via Hart van Nederland, https://www.hartvannederland.nl/nieuws/dieren/huisdieren-ongekend-populair-door-corona-maar-wat-kosten-ze). DE: breed-club recommended price €1,600 (Kromfohrländer club, Dec 2024, https://kromfohrlaender.de/wp-content/uploads/2024/12/ZueNa_2024-12-03.pdf); retriever breeder €2,000 (Schwäbische, 2022). FR cats: pedigree kitten up to €1,500, adult €600–€800 (Santévet 2026). |
| Spay/neuter | **€200–€650** (large females up to ~€850) | **€70–€250** | NL dog: castration €250–€400, spay €350–€650 incl. medication, table up to €850 for large dogs (Overstappen 2026, https://www.overstappen.nl/dierenverzekering/hondenverzekering/dierenartskosten); Univé worked example, 29 kg Labrador castration €305 total. FR dog: male €150–€300, female €200–€400 (lecomparateurassurance.com, 2025, https://www.lecomparateurassurance.com/assurance-animaux/guides-chien/frais-veterinaires). DE GOT 1x: male €70.60, female €192.00 (3x: €211.80 / €576.00), before anaesthesia, medication and VAT (Allianz, https://www.allianz.de/gesundheit/hundekrankenversicherung/kastration/). NL cat: about €90 (tom) / €160–€185 (queen) (Overstappen 2026). FR cat: €90–€150 male, €100–€200 female (Santévet 2026). |
| Vaccines + initial vet | **€120–€300** | **€100–€250** | FR dog: first-year vaccination €120–€160 (ymanci, 2026); €120–€200 (comdewouf, 2026). FR cat: €100–€160 (comdewouf); vaccines €60–€100/yr (Santévet). NL consult averages €51, range €35–€70 (Overstappen 2026). The added initial exam and deworming are [EST]. |
| Setup gear | **€150–€450** | **€120–€350** | SE: collar/leash, brush, bowls, bed, toys and clippers total ~1,690 SEK (≈ €155), plus a car crate at 1,000–9,000 SEK (≈ €90–€820) (Icabanken via TV4, 31 May 2026, https://www.tv4.se/artikel/tt-260531-valpekonomi1-f2c3095f/valpchocken-sa-dyrt-ar-det-att-skaffa-hund). FR cat accessories itemised at about €150–€400 total (Santévet 2026). Ranges are [EST]. |
| Microchip, registration, misc | **€50–€120** | **€40–€100** | FR: ID chip €50–€80, mandatory (lecomparateurassurance 2025; comdewouf). NL: dog chip + registration €55–€65; cat about €40 plus registration (Overstappen 2026). DK: Dansk Hunderegister 145 DKK (≈ €19) [WEAK]. EU pet passport about €10–€25. Excludes dog tax (see B4). |

### B2. Annual costs, EUR

| Line | Dog | Cat | Evidence |
|---|---|---|---|
| Food, dog small (<10 kg) | **€150–€350** | | [EST] Same method as A2, using EU prices: Royal Canin Medium Adult 15 kg at €58.64–€68.99 (Fressnapf via discounto.de, Sep 2026, https://www.discounto.de/Angebot/ROYAL-CANIN-Medium-Adult-15-kg-9952921/) ≈ €3.9–€4.6/kg ≈ €1.0–€1.2 per 1,000 kcal (mid-premium). Band used: ~€0.75–€1.75/Mcal plus 10% treats. |
| Food, dog medium (10–22 kg) | **€250–€550** | | Cross-check: FR 25 kg dog €180–€890/yr depending on quality (French insurer/comparator write-up via search [WEAK]); FR premium kibble €30–€80/month (ymanci 2026). |
| Food, dog large (23–40 kg) | **€400–€850** | | SE medium dog about 8,000 SEK/yr (≈ €730) (Icabanken via TV4 2026). The Nordic level is at the top of this band. |
| Food, dog giant (40+ kg) | **€600–€1,200** | | FR all dogs €330–€1,600/yr (comdewouf 2026 [WEAK]). |
| Food, cat (dry / mixed / wet) | | **Dry €150–€400; Mixed €350–€750; Wet €450–€900** | FR (Santévet, Jan 2026): economy dry €180–€300, mid-range €300–€420, premium dry €480–€720, wet-only €480–€840, mixed €540–€1,080. DE: good cat food €20–€40/month (familie.de, via search). FR all food €250–€750 (comdewouf 2026). I trimmed Santévet's mixed range to sit between dry and wet [EST]. |
| Routine vet + prevention | **€200–€450** | **€120–€300** | FR dog €200–€350/yr; cat €120–€220/yr (comdewouf 2026). NL dog deworming + flea + dental check €360–€610/yr (Overstappen 2026, high end). FR annual vaccination visit €50–€90 (lecomparateurassurance 2025; ymanci). BE: routine consults under €100/yr (Test-Aankoop, https://www.test-aankoop.be/woning-energie/huisdieren/dossier/kosten-hond-of-kat/dierenartskosten). |
| Insurance (accident & illness) | **€250–€750** (surgery-only cover from ~€150) | **€120–€420** | DE (Stiftung Warentest, Finanzen 5/2025, 16 Apr 2025): dog surgery-only cover rated "very good" costs €216–€403/yr; entry prices €236 (Jack Russell) and €305 (Labrador); full health cover is "usually several hundred euros a year more" (https://www.test.de/Hundekrankenversicherung-Voll-und-OP-Versicherung-Vergleich-5777769-0/). DE cat surgery cover: ~€103–€210/yr for a 6-month-old cat, €368 for a 7-year-old (test.de cat comparison, reference date 1 Dec 2025, partly paywalled, https://www.test.de/Tierkrankenversicherungen-fuer-Katzen-im-Vergleich-5934432-0/). FR (Jan 2026, 7,066 quotes): dog €12–€44/mo (≈ €144–€528/yr), cat €9–€35/mo (≈ €108–€420/yr) (MoneyVox citing lecomparateurassurance, https://www.moneyvox.fr/assurance/actualites/107615/combien-ca-coute-assurer-votre-chien-ou-votre-chat-en-2026). NL dog €15–€70/mo (keuze.nl 2026, https://www.keuze.nl/huisdierenverzekering/kosten-hondenverzekering). SE golden retriever 3,000–5,000 SEK/yr (≈ €270–€455) (Icabanken via TV4 2026). UK average premium £389 (2023, ABI) ≈ €450 [GBP converted]. |
| Litter | | **€80–€200** | FR €70–€190/yr (comdewouf 2026); DE about €15/month ≈ €180/yr (familie.de via search). |
| Toys/chews/gear | **€80–€250** | **€40–€120** | [EST] SE: toys 200 SEK plus poop bags 400 SEK/yr (TV4 2026). No good EU survey found. |

**Optional EUR line: mandatory dog liability insurance (recommended).**
Third-party liability cover is compulsory for dogs in **Denmark** (by law, from 4 months old; FDM, https://fdm.dk/forsikringer/hundeforsikring) and in several **German states**: Berlin, Hamburg, Niedersachsen, Sachsen-Anhalt, Schleswig-Holstein and Thüringen per Stiftung Warentest, plus Bremen since 2025 per Hundeo; sources conflict on Bremen and Schleswig-Holstein.
Price in DE: €36–€156/yr. The cheapest tariffs start at €36 (Jack Russell) and €55 (Labrador) (test.de press release, https://www.test.de/presse/pressemitteilungen/Hundehalterhaftpflicht-So-guenstig-sind-Policen-fuer-Labrador-und-Jack-Russel-6246233-0/; I did not verify the date).
Suggested line: **€40–€150/yr**. A US calculator has no equivalent; in the US this is usually part of homeowner or renter cover.

### B3. Extras, EUR

| Line | Range | Evidence |
|---|---|---|
| Dental cleaning (under anaesthesia) | **Dog €150–€500** (more with extractions or X-rays); **cat €150–€400** | FR scale & polish: €100–€300 (lecomparateurassurance 2025); €150–€350 (comdewouf); €150–€300 (ymanci). NL dog dental treatment €200–€400; cat "from €150" plus anaesthesia and extras (Overstappen 2026). DE GOT 1x scaling €62–€109, plus anaesthesia (~€62 inhalation at 1x), monitoring and VAT; billed at up to 3x (kuechenfibel.de via search [WEAK]). The upper end is [EST]. |
| Boarding per night | **Dog €18–€45; cat €10–€25** (Nordic and UK city centres higher) | FR dog €15–€30/day (ymanci 2026); €15–€25 (comdewouf); FR cat €12–€20/day (comdewouf cat 2026). NL dog €15–€35/day, typically €20–€25 [WEAK, webwoordenboek.nl]; NL cat €11.50–€15.26/day (old pension tariff sheets, 2017 and undated [WEAK]). DE long-stay dog €20–€50/day [WEAK, finom business-planning blog]. UK kennels £21–£62/night, median £31 (≈ €24–€72, median ≈ €36), RSPCA Solent 2025 £22 [GBP converted]. |
| Grooming per visit (high-maintenance coat) | **€45–€110** (Nordic up to ~€140) | FR session €40–€90, every 2–3 months (ymanci 2026). NL full groom by weight €50 (≤2 kg) to €105 (50+ kg) [WEAK, undated price list]. DK 500–750 DKK for a 20 kg Goldendoodle (≈ €67–€100) [WEAK, handyhand.dk marketplace offers, 2025]. SE 8,000 SEK/yr for 5 visits ≈ 1,600 SEK/visit (≈ €145) (Icabanken via TV4 2026). UK full groom £54–£104 at one Hertfordshire kennel (2025 price list) [GBP converted, single business]. Stiftung-Warentest-style German guides explicitly say no reliable German average exists (testundtipps.de, Oct 2026). Treat this line as the weakest in the EUR set. |
| Emergency | **€500–€3,000+** | NL emergency consult €75–€120, up to ~€240 at night or at specialist clinics; operations €500–€3,500 (Overstappen 2026). FR accident/emergency surgery €1,000–€3,000 per episode (comdewouf 2026); vet surgery €300–€3,000 (lecomparateurassurance 2025). DE out of hours: GOT 2x–4x plus €50 net fee. A worked example puts a ~€120 standard treatment at ~€300 (2x) or ~€540 (4x) out of hours (HanseMerkur). UK average insurance claim £685 (2024, ABI, https://www.abi.org.uk/news/news-articles/2025/5/insurance-payouts-for-pawly-pets-top-1-billion-for-third-year-in-a-row/) ≈ €795 [GBP converted]. |
| Emergency fund | **€1,500–€3,000** | [EST] Editorial, scaled to the emergency range above. |

### B4. Optional EUR-only line: dog tax / licence

| Country | Annual, first dog | Source |
|---|---|---|
| Germany (Hundesteuer) | **€0–€186**; the large-city norm is ~€90–€160 | 395 cities over 30k population, 2025: top Mainz €186, Hagen/Wiesbaden €180; Ahlen €0; 16 towns under €50 (kommunal.de, https://kommunal.de/hundesteuer2025-teuer-guenstig-staedte). City examples: Berlin €120, Hamburg €90, Köln €156, München €100 [WEAK, deutschland-rechner.de]. A Munich draft resolution (Jul 2026) would raise the rate to €144 from 2027, and to €1,200 for listed "dangerous" breeds (muenchen-transparent.de). Listed breeds pay far more almost everywhere. |
| Netherlands (hondenbelasting) | **€0–€142**; only 100 of 342 municipalities levy it | 2026: average €81.69 where levied (€25.25 averaged across all municipalities); top Katwijk €142.18, Tilburg €132.28 (Overstappen/COELO, https://www.overstappen.nl/nieuws/nog-maar-100-gemeenten-heffen-hondenbelasting-tarieven-lopen-uiteen-van-0-euro-tot-142-euro/). |
| Austria (Hundeabgabe) | e.g. Vienna €72 | Sparkasse.at, https://www.sparkasse.at/sgruppe/finanziell-gesund/haushaltsbudget/haustier-kostet (undated). |
| Ireland (dog licence) | **€20/yr** (or €140 lifetime) | Fingal / Dublin City Council datasets on data.gov.ie, https://data.gov.ie/dataset/dog-licences-2025-2028-fcc. |
| France, Denmark, Sweden, UK (GB) | None | One French site claimed a "€100/yr dog tax from 2025". I could not verify it and believe it is false, so do not use it. Northern Ireland charges a £12.50 licence. |

**Suggested calculator line:** "Dog tax / licence (where applicable): €0–€190/yr; typically €20–€160". Cats are not taxed in these countries.

### B5. Pet rent in Europe: recommendation

**Omit pet rent from the EUR set**, or replace it with a one-line note.
- UK (England), Renters' Rights Act: tenants can request a pet and landlords cannot unreasonably refuse. A last-minute amendment dropped the power to require pet insurance. Landlords cannot raise rent or charge a pet fee or pet deposit for a pet (moneysupermarket guide, via search). The Tenant Fees Act caps deposits at 5 weeks' rent (NRLA, https://www.nrla.org.uk/news/pet-friendly-rentals-9-ways-to-stay-compliant-with-the-new-laws). Scotland allows an extra pet deposit within a two-month total (NRLA).
- Germany: the total deposit is capped at 3 months' cold rent (BGB §551). A separate dog deposit is legally contested; one Amtsgericht Berlin-Köpenick ruling allowed it (buhl.de / Berliner Mieterverein, via search). Monthly pet rent is not a recognised practice.
- NL/FR: deposits are capped by law, and no monthly pet-rent practice was found (general knowledge; I fetched no source).

If you keep a line, make it a one-off "higher deposit / pet clause: €0–€500 one-off" [EST] and label it optional. A monthly pet-rent line would mislead EU users.

### B6. Lifespan (EU set)
Use the same science as A4; the lifespan data (LIFE-D, LIFE-C) is European (UK VetCompass) to begin with. PDSA (UK, 2024 cost model) uses O'Neill 2013 size ranges. German consumer sources assume dogs live 10–15 years (Sparkasse) and cats 15–16 years (Herz für Tiere / Allianz).

---

## Lines that rest on weak evidence (read before publishing)
1. **EUR grooming per visit.** There is no survey; it rests on a single French insurer guide, undated price lists and marketplace offers.
2. **EUR boarding.** French figures are reasonable; NL and DE rest on blogs or old tariff sheets.
3. **Dog food by size, USD and EUR.** These are my own calorie-based derivations anchored to Tufts 2022 and one 2026 EU retail price, not a survey.
4. **US insurance 2025 figures ($836 / $435).** These come via GlobalPETS and I could not confirm them on naphia.org. The NAPHIA-verified 2024-data figures are $749 / $386.
5. **Rover numbers.** Rover blocked direct fetches, so I only saw them quoted second-hand.
6. **ASPCA table.** It dates from 2021. Later vet-price inflation (UK +63% from 2016 to 2023 per the CMA; SE +50% from 2019 to 2025 per SCB) means 2021 figures understate today's costs, so treat them as floors.
7. **Danish adoption fees.** They come from an aggregator FAQ that is internally inconsistent.
