# Dog Breed Selector dataset: sources and notes

Companion to `dog-breeds.json` (45 breeds). Compiled 2026-10-22. Internal research notes, not page copy.

## 1. How the 45 breeds were chosen

- **AKC 2025 popularity ranking** (published 2026), ranks 1 to 50: https://www.akc.org/expert-advice/news/most-popular-dog-breeds-2025/
  Top 10: French Bulldog, Labrador, Golden, German Shepherd, Dachshund, Poodle, Beagle, Rottweiler, German Shorthaired Pointer, Bulldog. The dataset has every AKC breed ranked 1 to 32 except #28 Miniature American Shepherd and #31 Brittany, plus AKC #35 to #38, #45, #46, #48 and #49. The European additions rank much lower on the AKC list (Russell Terrier #60, Staffordshire Bull Terrier #61, Coton #79, Border Terrier #91, Danish-Swedish Farmdog #170).
- **UK (Royal Kennel Club 2025 registrations, as reported by The Scotsman, since the RKC's own table could not be retrieved):** Labrador #1 (29,919), Cocker Spaniel #2 (21,618), Dachshunds about 14,000 across all varieties, Cavalier #10, plus German Shepherd, Miniature Schnauzer, Staffordshire Bull Terrier, Border Terrier, Whippet, Jack Russell. Example source: https://www.scotsman.com/lifestyle/family/top-dogs-2026-here-are-the-12-most-popular-breeds-of-adorable-dog-according-to-latest-figures-including-the-loving-labrador-6034774
- **Denmark 2025 top 25** (netnatur.dk; the article draws on Dansk Hunderegister / Agria figures, **not** an official Dansk Kennel Klub table): mixed breed, Labrador, Havanese, Golden, Coton de Tulear, Cavalier, German Shepherd, Cocker Spaniel, Shih Tzu, Danish-Swedish Farmdog, French Bulldog, Border Collie, Staffordshire Bull Terrier, Chihuahua, Jack Russell, Westie, Cavapoo, Cairn Terrier, Welsh Corgi, Sheltie, Maltese, Labradoodle, Bernese, Cockapoo, Dachshund ("Gravhund"). https://netnatur.dk/?p=162441
- European additions from those lists: English Cocker Spaniel, Staffordshire Bull Terrier, Jack Russell Terrier, Coton de Tulear, Danish-Swedish Farmdog, Border Terrier, Whippet.
- **Excluded on purpose:** doodles and other designer crosses (Cavapoo, Labradoodle, Cockapoo all rank in Denmark's top 25); the American Cocker Spaniel (AKC #33), left out so that "Cocker Spaniel" means the English breed, as it does in Europe. Gaps you might want to fill next: Miniature Dachshund (very big in the UK), Cairn Terrier (DK #18), Miniature American Shepherd, Brittany, Basset Hound, Shiba Inu, Australian Cattle Dog, Dalmatian.
- Poodle: `poodle` = Standard Poodle (holds the required path). Miniature and Toy are separate entries. Dachshund = Standard only.

## 2. Facts: weights, lifespans, size

- **Primary fact source:** each breed's AKC page. I parsed the "Height / Weight / Life Expectancy" attributes straight from the page HTML, not from memory. Exceptions: Cane Corso weight (FCI standard), Poodle lifespans (PDSA/VCA). See section 4 and the per-breed list.
- **weightKg** is converted from lb (0.5 kg steps below 20 kg, whole kg above), except Cane Corso, where the FCI kg values are the source and lb is the conversion.
- **Where AKC gives only a ceiling or single values**, the other end of the range is my own editorial call, based on the breed standard and KC/FCI ranges: French Bulldog (AKC "under 28 lb", lower bound set to 16; FCI 8–14 kg), Yorkshire Terrier ("7 lb" max, lower 4), Maltese ("under 7", lower 4; FCI 3–4 kg), Chihuahua ("not exceeding 6", lower 2), Pembroke Corgi ("up to 30/28", lower 24; FCI 10–12 kg), Beagle (two size classes "under 20" and "20–30", combined as 18–30), Bulldog (AKC 50 male / 40 female), English Springer (50 / 40), Boxer (female "about 15 lb less" than the 65–80 lb male, so 50–80).
- **Size** follows your rule, applied to the midpoint of the combined male+female range: small <=20 lb, medium 21–50, large 51–90, giant >90. Applying it literally overrides some common labels, flagged in section 4.

## 3. Trait scores: method

These are our own editorial ratings. For each breed and trait I compared three independent sources: **AKC breed traits** (1–5 sliders), **Royal Kennel Club UK bands** (exercise time, grooming frequency, sheds yes/no, size of home), and **Purina UK** (vet-rated 1–5 scores plus statements such as "suitable for non-experienced owners", "Quiet dog" / "Chatty and vocal", "Can be left alone with training"). For the two breeds missing from KC or Purina, I used PetMD and the FCI standard instead. No source's score set was copied; where the sources split, I used judgement and listed the breed in section 4.

Scale anchors, used to keep the scores spread out (5 = more of the trait):
- **energy / exercise:** 1 = Bulldog-level couch dog; 3 = about an hour of walking; 5 = Border Collie / Malinois / GSP, needing 2+ hours plus work. Exercise maps to KC bands: "up to 30 min" = 1–2, "up to 1 hour" = 2–3, "more than 2 hours" = 4–5.
- **shedding:** 1 = poodle/bichon-type coats; 5 = GSD, Golden, Husky, Bernese, Corgi, Pug.
- **grooming:** 1 = short coat, wipe-down; 5 = daily brushing plus professional clipping (poodles, bichon, maltese, shih tzu).
- **barking:** 1 = Whippet / Newfoundland; 5 = Beagle (baying), Sheltie, Chihuahua, Pomeranian, Miniature Schnauzer.
- **kids:** 1 = Chihuahua (fragility and snappiness); 2 = toy breeds and high-drive guardians; 5 = Labrador, Golden, Beagle, Cavalier, Newfoundland.
- **apartment:** a mix of size, noise and exercise need. 5 = Cavalier, Frenchie, Bichon; 1 = large, high-drive or giant breeds.
- **trainability:** 2 = independent/stubborn (Beagle, Dachshund, Bulldog, Husky); 5 = Border Collie, Poodle, GSD, Malinois, retrievers. No breed in the set scores 1.
- **firstTimeOwner:** 1 = Malinois, Cane Corso, Rottweiler; 5 = Cavalier, Golden, Havanese, Coton, Bichon, Shih Tzu, Boston.
- **aloneTolerance:** deliberately tops out at 4 (Bulldog, Whippet). No common breed copes well being alone for long, and every source says so. 1 = velcro and working breeds prone to separation distress.
- **otherDogs:** 2 = breeds with known dog-dog intolerance (Staffie, Cane Corso, Rottweiler, Malinois, Doberman, Chihuahua, Jack Russell); 5 = pack/social breeds.

Spread across the 45 breeds (count of 1/2/3/4/5): energy 1/4/16/12/12; exercise 5/10/11/8/11; shedding 9/10/15/4/7; grooming 13/9/9/8/6; barking 2/13/16/9/5; kids 1/6/7/18/13; apartment 10/9/6/7/13; trainability 0/4/16/13/12; firstTimeOwner 3/7/14/14/7; aloneTolerance 11/22/10/2/0; otherDogs 0/7/11/14/13.

## 4. Flags: source disagreements and judgement calls

**Conflicts with our own live breed pages (reconcile before shipping):**
1. **French Bulldog size.** The rule gives "medium" (16–28 lb, midpoint 22). The RKC calls it "Small", and `content/dogs/breeds/french-bulldog.md` calls it "small". **Shedding:** that page says "Low", but AKC (3), Purina (3) and the RKC ("Sheds: Yes") all say moderate. I scored it 2 (low-moderate). Decide whether to relabel the page or soften the size rule for this breed.
2. **Poodle lifespans.** AKC gives one range, 10–18, for all three varieties. PDSA gives the Standard 12–14 (used). VCA gives Standard 10–13, Miniature 13–15 (used) and Toy 12–14 (used). `content/dogs/breeds/poodle.md` states 12–15 / 13–16 / 14–18, and I could find no vet or kennel-club source for those figures, only breed blogs (for example Dogster for Toy 14–18). The Toy figure is the shakiest: VCA's 12–14 looks low against AKC's 18 ceiling.

**Size-rule outcomes that differ from common labels:**
3. **Rottweiler** comes out "giant" (AKC 80–135 lb, midpoint 107). The FCI standard puts it at about 42 kg (female) to 50 kg (male), roughly 93–110 lb, so it is giant by the rule either way. It is usually called "large".
4. **Bernese Mountain Dog**: "giant" at a 92.5 lb midpoint (borderline). The RKC says "Large".
5. **Australian Shepherd**: the rule gives "large" (40–65 lb, midpoint 52.5). **Overridden to medium** to match the RKC "Medium" band and typical adults. This is the only manual override.
6. **Standard Poodle** comes out "large" (40–70, midpoint 55). The RKC says "Medium".
7. **Vizsla**: "large", borderline (midpoint 52). The RKC agrees.
8. **Beagle**: "medium" (18–30 lb). The RKC says "Small".
9. **Dachshund**: Standard only, so "medium". The RKC agrees for the Standard; Miniatures (11 lb and under) would be small.
10. Other borderline cases, kept as the rule gives them: Boston Terrier (midpoint 18.5, small), Shetland Sheepdog (20, small), Siberian Husky (47.5, medium), Westie and Danish-Swedish Farmdog (17.5, small).

**Fact disagreements between sources:**
11. **Belgian Malinois:** AKC says 40–80 lb and 14–16 years. The FCI standard is much lighter (about 20–30 kg), and several vet sources give 12–14 years. I used AKC's figures. Treat the lifespan as optimistic.
12. **Doberman:** AKC 60–100 lb, while the FCI is about 32–45 kg. I used AKC.
13. **Bulldog:** AKC 40–50 lb, while the RKC standard is 23–25 kg (about 50–55 lb). I used AKC.
14. **German Shepherd lifespan:** AKC 12–14 (used, and it matches our live page). Many vet sources quote a shorter range.
15. **Coton de Tulear:** AKC 15–19 years (used), against the commonly quoted 14–16.
16. **Cavalier (12–15) and Pug (13–15):** AKC figures (used). Both breeds have a heavy inherited disease burden (heart valve disease in the Cavalier, brachycephalic problems in the Pug), and UK veterinary studies are widely reported to find shorter medians. I did not verify those here: worth checking before these ranges go on a page.
17. **Jack Russell Terrier:** the AKC registers this breed as the "Russell Terrier" (page used: 9–15 lb, 12–14 years). The RKC "Jack Russell Terrier" is the same FCI breed. The Parson Russell is a separate breed.
18. **Cane Corso:** AKC gives no weight ("proportionate to height"), so I used the FCI standard (45–50 kg male, 40–45 kg female). PetMD says some dogs exceed 100 lb.

**Trait disagreements (score chosen):**
- Beagle trainability: AKC 3, Purina 1 → 2. Beagle alone: Purina 1 → 2.
- Doberman otherDogs: AKC 3, Purina 1 ("issues with unknown dogs") → 2. Doberman shedding: AKC 4, Purina 4, smooth coat → 3.
- Staffordshire Bull Terrier otherDogs: AKC 3, Purina 1 ("might not like other dogs") → 2. Trainability: AKC 5, Purina 2 → 3.
- Pomeranian shedding: AKC 2, Purina 4 (double coat) → 4.
- Miniature Schnauzer shedding: AKC 3, RKC "No", Purina 2 → 2. Alone: Purina 5, an outlier against the breed's reputation → 3.
- Newfoundland grooming: AKC 2, RKC "every day", Purina 5 → 4.
- Great Dane firstTimeOwner: Purina "experienced owners", but the breed is gentle → 3 (size is the main issue). Great Dane exercise: RKC "more than 2 hours", Purina 5, AKC energy 4; adult Danes are calm → 3.
- Bernese exercise: RKC "up to 1 hour", Purina 3, AKC energy 4 → 3. Barking: AKC 3, Purina "vocal" → 3.
- Golden barking: AKC 1, Purina "Quiet" → 2.
- German Shepherd kids: AKC 5, Purina 5, but the dog is protective and needs an experienced owner → 4.
- Siberian Husky otherDogs: AKC 5, Purina "issues with unknown dogs" → 4.
- Bichon alone: AKC has no alone score, Purina 1 → 1.
- West Highland White Terrier shedding: AKC 3, Purina 4 → 3.
- Cavalier: Purina family-friendly 3 against AKC kids 5 → 5. Purina looks like the outlier, since its own text calls the breed a "Great family dog".
- Danish-Swedish Farmdog: only AKC and PetMD are available (no RKC or Purina page), so the scores rest on less evidence.

## 5. Per-breed sources

Every line below was pulled from the live page on 2026-10-22: AKC = facts and trait sliders; KC = Royal Kennel Club bands; Purina = Purina UK vet ratings and statements.

### French Bulldog (`french-bulldog`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/french-bulldog/ : Height: 11-13 inches; Weight: under 28 pounds; Life Expectancy: 10-12 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/french-bulldog/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Flat/ Apartment, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/french-bulldog : vet rating Family-friendly=4, Exercise needs=2, Easy to train=4, Tolerates being alone=1, Likes other pets=4, Energy level=3, Grooming needs=3, Shedding=3; Dog suitable for non-experienced owners; Quiet dog; Generally friendly with other dogs; Can be left alone with training

### Labrador Retriever (`labrador-retriever`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/labrador-retriever/ : Height: 22.5-24.5 inches (male), 21.5-23.5 inches (female); Weight: 65-80 pounds (male), 55-70 pounds (female); Life Expectancy: 11-13 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/gundog/retriever-labrador/ : size Large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/labrador-retriever : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=2, Likes other pets=5, Energy level=5, Grooming needs=4, Shedding=5; Dog suitable for non-experienced owners; Quiet dog; Generally friendly with other dogs; Can be left alone with training

### Golden Retriever (`golden-retriever`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/golden-retriever/ : Height: 23-24 inches (male), 21.5-22.5 inches (female); Weight: 65-75 pounds (male), 55-65 pounds (female); Life Expectancy: 10-12 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/gundog/retriever-golden/ : size Large, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/golden-retriever : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, alone=1, Likes other pets=5, Energy level=5, Grooming needs=4, Shedding=5; Dog suitable for non-experienced owners; Quiet dog; Generally friendly with other dogs

### German Shepherd (`german-shepherd`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/german-shepherd-dog/ : Height: 24-26 inches (male), 22-24 inches (female); Weight: 65-90 pounds (male), 50-70 pounds (female); Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/pastoral/german-shepherd-dog/ : size Large, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/german-shepherd : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=2, Likes other pets=2, Energy level=5, Grooming needs=4, Shedding=5; Dogs suitable for experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Dachshund (Standard) (`dachshund`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/dachshund/ : Height: 8-9 inches (standard), 5-6 inches (miniature); Weight: 16-32 pounds (standard), 11 pounds & under (miniature); Life Expectancy: 12-16 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/hound/dachshund-smooth-haired/ : size Medium, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/dachshund-smooth-haired : vet rating Family-friendly=4, Exercise needs=3, Easy to train=2, Tolerates being alone=3, Likes other pets=3, Energy level=3, Grooming needs=1, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Poodle (Standard) (`poodle`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/poodle-standard/ : Height: over 15 inches (standard); Weight: 60-70 pounds (male standard), 40-50 pounds (female standard); Life Expectancy: 10-18 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/poodle-standard/ : size Medium, exercise Up to 1 hour per day, grooming Every day, sheds No, home Large house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/poodle-standard : vet rating Family-friendly=5, Exercise needs=4, Easy to train=5, Tolerates being alone=1, Likes other pets=4, Energy level=4, Grooming needs=2, Shedding=1; Dogs suitable for experienced owners; Quiet dog; Generally friendly with other dogs
- PDSA (lifespan 12–14 used): https://www.pdsa.org.uk/pet-help-and-advice/looking-after-your-pet/puppies-dogs/large-dogs/poodle
- VCA (standard 10–13 yr, 45–65 lb): https://vcahospitals.com/know-your-pet/dog-breeds/standard-poodle

### Poodle (Miniature) (`miniature-poodle`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/poodle-miniature/ : Height: 10-15 inches; Weight: 10-15 pounds; Life Expectancy: 10-18 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/poodle-miniature/ : size Small-Medium, exercise Up to 1 hour per day, grooming Every day, sheds No, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/poodle-miniature : vet rating Family-friendly=4, Exercise needs=4, Easy to train=5, Tolerates being alone=1, Likes other pets=4, Energy level=4, Grooming needs=2, Shedding=1; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs
- VCA (lifespan 13–15 used; 12–18 lb): https://vcahospitals.com/know-your-pet/dog-breeds/miniature-poodle

### Poodle (Toy) (`toy-poodle`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/poodle-toy/ : Height: No more than 10 inches; Weight: 4-6 pounds; Life Expectancy: 10-18 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/poodle-toy/ : size Small, exercise Up to 1 hour per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/poodle-toy : vet rating Family-friendly=5, Exercise needs=4, Easy to train=5, Tolerates being alone=1, Likes other pets=4, Energy level=4, Grooming needs=2, Shedding=1; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs
- VCA (lifespan 12–14 used; 4–8 lb): https://vcahospitals.com/know-your-pet/dog-breeds/toy-poodle

### Beagle (`beagle`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/beagle/ : Height: 13 inches & under, 13-15 inches; Weight: under 20 pounds (13 inches & under), 20-30 pounds (13-15 inches); Life Expectancy: 10-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/hound/beagle/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/beagle : vet rating Family-friendly=5, Exercise needs=4, Easy to train=1, alone=1, Likes other pets=3, Energy level=4, Grooming needs=1, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Rottweiler (`rottweiler`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/rottweiler/ : Height: 24-27 inches (male), 22-25 inches (female); Weight: 95-135 pounds (male), 80-100 pounds (female); Life Expectancy: 9-10 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/rottweiler/ : size Large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Under 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/rottweiler : vet rating Family-friendly=5, Exercise needs=4, Easy to train=4, Tolerates being alone=1, Likes other pets=2, Energy level=4, Grooming needs=4, Shedding=4; Dogs suitable for experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### German Shorthaired Pointer (`german-shorthaired-pointer`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/german-shorthaired-pointer/ : Height: 23-25 inches (male), 21-23 inches (female); Weight: 55-70 pounds (male), 45-60 pounds (female); Life Expectancy: 10-12 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/gundog/german-shorthaired-pointer/ : size Large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/german-short-haired-pointer : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=1, Likes other pets=4, Energy level=5, Grooming needs=3, Shedding=2; Dog suitable for owners with some experience; Quiet dog; Generally friendly with other dogs

### Bulldog (`bulldog`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/bulldog/ : Height: 14-15 inches; Weight: 50 pounds (male), 40 pounds (female); Life Expectancy: 8-10 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/bulldog/ : size Medium, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Small house, lifespan Under 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/bulldog : vet rating Family-friendly=5, Exercise needs=3, Easy to train=3, Tolerates being alone=3, Likes other pets=2, Energy level=2, Grooming needs=3, Shedding=5; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs; These dogs are brachycephalic; problems associated with this condition include:

### Cane Corso (`cane-corso`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/cane-corso/ : Height: 25-27.5 inches (male), 23.5-26 inches (female); Weight: Proportionate to height; Life Expectancy: 9-12 years. weightKg converted from lb unless noted.
- FCI standard No. 343 (weight males 45–50 kg, females 40–45 kg; used for weightKg/weightLb because AKC gives only "proportionate to height"): https://www.fci.be/Nomenclature/Standards/343g02-en.pdf
- PetMD (temperament cross-check, apartment/first-time owner guidance): https://www.petmd.com/dog/breeds/cane-corso

### Cavalier King Charles Spaniel (`cavalier-king-charles-spaniel`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/cavalier-king-charles-spaniel/ : Height: 12-13 inches; Weight: 13-18 pounds; Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/cavalier-king-charles-spaniel/ : size Small, exercise Up to 1 hour per day, grooming More than once a week, sheds Yes, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/cavalier-king-charles-spaniel : vet rating Family-friendly=3, Exercise needs=3, Easy to train=3, Tolerates being alone=1, Likes other pets=5, Energy level=3, Grooming needs=4, Shedding=3; Dog suitable for non-experienced owners; Chatty and vocal dog; Generally friendly with other dogs

### Yorkshire Terrier (`yorkshire-terrier`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/yorkshire-terrier/ : Height: 7-8 inches; Weight: 7 pounds; Life Expectancy: 11-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/yorkshire-terrier/ : size Small, exercise Up to 30 minutes per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/yorkshire-terrier : vet rating Family-friendly=4, Exercise needs=4, Easy to train=3, Tolerates being alone=2, Likes other pets=3, Energy level=5, Grooming needs=2, Shedding=2; Dog suitable for non-experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Australian Shepherd (`australian-shepherd`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/australian-shepherd/ : Height: 20-23 inches (male), 18-21 inches (female); Weight: 50-65 pounds (male), 40-55 pounds (female); Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/pastoral/australian-shepherd/ : size Medium, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/australian-shepherd : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=2, Likes other pets=5, Energy level=5, Grooming needs=2, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs; Cannot be left alone

### Doberman Pinscher (`doberman-pinscher`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/doberman-pinscher/ : Height: 26-28 inches (male), 24-26 inches (female); Weight: 75-100 pounds (male), 60-90 pounds (female); Life Expectancy: 10-12 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/dobermann/ : size Large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/dobermann : vet rating Family-friendly=4, Exercise needs=3, Easy to train=5, Tolerates being alone=2, Likes other pets=1, Energy level=3, Grooming needs=4, Shedding=4; Dogs suitable for experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Pembroke Welsh Corgi (`pembroke-welsh-corgi`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/pembroke-welsh-corgi/ : Height: 10-12 inches; Weight: up to 30 pounds (male), up to 28 pounds (female); Life Expectancy: 12-13 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/pastoral/welsh-corgi-pembroke/ : size Small, exercise Up to 1 hour per day, grooming More than once a week, sheds Yes, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/welsh-corgi-pembroke : vet rating Family-friendly=4, Exercise needs=4, Easy to train=5, Tolerates being alone=3, Likes other pets=3, Energy level=4, Grooming needs=3, Shedding=5; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Miniature Schnauzer (`miniature-schnauzer`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/miniature-schnauzer/ : Height: 12-14 inches; Weight: 11-20 pounds; Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/miniature-schnauzer/ : size Small, exercise Up to 1 hour per day, grooming More than once a week, sheds No, home Flat/ Apartment, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/miniature-schnauzer : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=5, Likes other pets=3, Energy level=5, Grooming needs=2, Shedding=2; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Boxer (`boxer`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/boxer/ : Height: 23-25 inches (male), 21.5-23.5 inches (female); Weight: 65-80 pounds (male), females are about 15 pounds less than male; Life Expectancy: 10-12 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/boxer/ : size Large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/boxer : vet rating Family-friendly=4, Exercise needs=5, Easy to train=4, alone=1, Likes other pets=3, Energy level=5, Grooming needs=3, Shedding=4; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs; Can be left alone with training

### Pomeranian (`pomeranian`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/pomeranian/ : Height: 6-7 inches; Weight: 3-7 pounds; Life Expectancy: 12-16 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/pomeranian/ : size Small, exercise Up to 30 minutes per day, grooming More than once a week, sheds Yes, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/pomeranian : vet rating Family-friendly=2, Exercise needs=2, Easy to train=4, Tolerates being alone=1, Likes other pets=3, Energy level=3, Grooming needs=3, Shedding=4; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Bernese Mountain Dog (`bernese-mountain-dog`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/bernese-mountain-dog/ : Height: 25-27.5 inches (male), 23-26 inches (female); Weight: 80-115 pounds (male), 70-95 pounds (female); Life Expectancy: 7-10 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/bernese-mountain-dog/ : size Large, exercise Up to 1 hour per day, grooming More than once a week, sheds Yes, home Large house, lifespan Under 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/bernese-mountain : vet rating Family-friendly=5, Exercise needs=3, Easy to train=4, alone=1, Likes other pets=3, Energy level=4, Grooming needs=4, Shedding=5; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs; Can be left alone with training

### Shih Tzu (`shih-tzu`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/shih-tzu/ : Height: 9-10.5 inches; Weight: 9-16 pounds; Life Expectancy: 10-18 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/shih-tzu/ : size Small, exercise Up to 1 hour per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/shih-tzu : vet rating Family-friendly=4, Exercise needs=2, Easy to train=3, Tolerates being alone=3, Likes other pets=4, Energy level=2, Grooming needs=2, Shedding=3; Dog suitable for non-experienced owners; Quiet dog; Generally friendly with other dogs

### Great Dane (`great-dane`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/great-dane/ : Height: 30-32 inches (male), 28-30 inches (female); Weight: 140-175 pounds (male), 110-140 pounds (female); Life Expectancy: 7-10 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/great-dane/ : size Extra large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Under 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/great-dane : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=1, Likes other pets=5, Energy level=5, Grooming needs=4, Shedding=5; Dogs suitable for experienced owners; Quiet dog; Could have issues with unknown dogs but gets along with known dogs

### Boston Terrier (`boston-terrier`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/boston-terrier/ : Height: 10-12 inches; Weight: 12-25 pounds; Life Expectancy: 11-13 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/utility/boston-terrier/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Flat/ Apartment, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/boston-terrier : vet rating Family-friendly=4, Exercise needs=4, Easy to train=4, alone=3, Likes other pets=5, Energy level=4, Grooming needs=3, Shedding=2; Dog suitable for non-experienced owners; Quiet dog; Generally friendly with other dogs

### Chihuahua (`chihuahua`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/chihuahua/ : Height: 5-8 inches; Weight: not exceeding 6 pounds; Life Expectancy: 14-16 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/chihuahua-smooth-coat/ : size Small, exercise Up to 30 minutes per day, grooming Once a week, sheds Yes, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/chihuahua-smooth-coat : vet rating Family-friendly=5, Exercise needs=1, Easy to train=4, Tolerates being alone=1, Likes other pets=2, Energy level=3, Grooming needs=2, Shedding=2; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Havanese (`havanese`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/havanese/ : Height: 8.5-11.5 inches; Weight: 7-13 pounds; Life Expectancy: 14-16 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/havanese/ : size Small, exercise Up to 30 minutes per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/havanese : vet rating Family-friendly=5, Exercise needs=3, Easy to train=5, Tolerates being alone=1, Likes other pets=5, Energy level=3, Grooming needs=2, Shedding=2; Dog suitable for non-experienced owners; Chatty and vocal dog; Generally friendly with other dogs

### Border Collie (`border-collie`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/border-collie/ : Height: 19-22 inches (male), 18-21 inches (female); Weight: 30-55 pounds; Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/pastoral/border-collie/ : size Medium, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/border-collie : vet rating Family-friendly=4, Exercise needs=5, Easy to train=5, alone=1, Likes other pets=3, Energy level=5, Grooming needs=3, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs; Can be left alone with training

### English Springer Spaniel (`english-springer-spaniel`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/english-springer-spaniel/ : Height: 20 inches (male), 19 inches (female); Weight: 50 pounds (male), 40 pounds (female); Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/gundog/spaniel-english-springer/ : size Medium, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Small house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/english-springer-spaniel : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=1, Likes other pets=5, Energy level=5, Grooming needs=3, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Shetland Sheepdog (`shetland-sheepdog`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/shetland-sheepdog/ : Height: 13-16 inches; Weight: 15-25 pounds; Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/pastoral/shetland-sheepdog/ : size Small, exercise Up to 1 hour per day, grooming Every day, sheds Yes, home Small house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/shetland-sheepdog : vet rating Family-friendly=5, Exercise needs=4, Easy to train=5, Tolerates being alone=2, Likes other pets=4, Energy level=4, Grooming needs=3, Shedding=5; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Siberian Husky (`siberian-husky`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/siberian-husky/ : Height: 21-23.5 inches (male), 20-22 inches (female); Weight: 45-60 pounds (male), 35-50 pounds (female); Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/siberian-husky/ : size Medium-Large, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/siberian-husky : vet rating Family-friendly=5, Exercise needs=5, Easy to train=2, Tolerates being alone=1, Likes other pets=5, Energy level=5, Grooming needs=3, Shedding=4; Dogs suitable for experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs; Cannot be left alone

### English Cocker Spaniel (`english-cocker-spaniel`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/english-cocker-spaniel/ : Height: 16-17 inches (male), 15-16 inches (female); Weight: 28-34 pounds (male), 26-32 pounds (female); Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/gundog/spaniel-cocker/ : size Small, exercise Up to 1 hour per day, grooming Every day, sheds Yes, home Small house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/english-cocker-spaniel : vet rating Family-friendly=5, Exercise needs=4, Easy to train=4, Tolerates being alone=1, Likes other pets=5, Energy level=5, Grooming needs=3, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Belgian Malinois (`belgian-malinois`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/belgian-malinois/ : Height: 24-26 inches (male), 22-24 inches (female); Weight: 60-80 pounds (male), 40-60 pounds (female); Life Expectancy: 14-16 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/pastoral/belgian-shepherd-dog-malinois/ : size Medium-Large, exercise More than 2 hours per day, grooming More than once a week, sheds Yes, home Large house, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/belgian-shepherd-malinois : vet rating Family-friendly=4, Exercise needs=5, Easy to train=5, Tolerates being alone=3, Likes other pets=2, Energy level=5, Grooming needs=3, Shedding=2; Dogs suitable for experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Vizsla (`vizsla`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/vizsla/ : Height: 22-24 inches (male), 21-23 inches (female); Weight: 55-60 pounds (male), 44-55 pounds (female); Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/gundog/hungarian-vizsla/ : size Large, exercise More than 2 hours per day, grooming Once a week, sheds Yes, home Large house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/hungarian-vizsla : vet rating Family-friendly=5, Exercise needs=5, Easy to train=5, Tolerates being alone=1, Likes other pets=5, Energy level=5, Grooming needs=3, Shedding=2; Dog suitable for owners with some experience; Quiet dog; Generally friendly with other dogs

### Maltese (`maltese`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/maltese/ : Height: 7-9 inches; Weight: under 7 pounds; Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/maltese/ : size Small, exercise Up to 30 minutes per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/maltese : vet rating Family-friendly=5, Exercise needs=2, Easy to train=4, Tolerates being alone=1, Likes other pets=4, Energy level=3, Grooming needs=2, Shedding=2; Dog suitable for non-experienced owners; Quiet dog; Generally friendly with other dogs

### Pug (`pug`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/pug/ : Height: 10-13 inches; Weight: 14-18 pounds; Life Expectancy: 13-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/pug/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Flat/ Apartment, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/pug : vet rating Family-friendly=5, Exercise needs=3, Easy to train=3, Tolerates being alone=1, Likes other pets=4, Energy level=3, Grooming needs=3, Shedding=5; Dog suitable for non-experienced owners; Quiet dog; Could have issues with unknown dogs but gets along with known dogs

### Staffordshire Bull Terrier (`staffordshire-bull-terrier`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/staffordshire-bull-terrier/ : Height: 14-16 inches; Weight: 28-38 pounds (male), 24-34 pounds (female); Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/terrier/staffordshire-bull-terrier/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/staffordshire-bull-terrier : vet rating Family-friendly=5, Exercise needs=4, Easy to train=2, Tolerates being alone=2, Likes other pets=1, Energy level=3, Grooming needs=3, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Might not like other dogs

### Jack Russell Terrier (`jack-russell-terrier`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/russell-terrier/ : Height: 10-12 inches; Weight: 9-15 pounds; Life Expectancy: 12-14 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/terrier/jack-russell-terrier/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Flat/ Apartment, lifespan Over 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/jack-russell-terrier : vet rating Family-friendly=5, Exercise needs=5, Easy to train=4, Tolerates being alone=3, Likes other pets=4, Energy level=5, Grooming needs=3, Shedding=3; Dog suitable for owners with some experience; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### West Highland White Terrier (`west-highland-white-terrier`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/west-highland-white-terrier/ : Height: 11 inches (male), 10 inches (female); Weight: 15-20 pounds; Life Expectancy: 13-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/terrier/west-highland-white-terrier/ : size Small, exercise Up to 1 hour per day, grooming More than once a week, sheds Yes, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/west-highland-white-terrier : vet rating Family-friendly=4, Exercise needs=4, Easy to train=4, Tolerates being alone=2, Likes other pets=5, Energy level=4, Grooming needs=3, Shedding=4; Dog suitable for non-experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs

### Coton de Tulear (`coton-de-tulear`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/coton-de-tulear/ : Height: 10-11 inches (male), 9-10 inches (female); Weight: 9-15 pounds (male), 8-13 pounds (female); Life Expectancy: 15-19 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/coton-de-tulear/ : size Small, exercise Up to 30 minutes per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/coton-de-tulear : vet rating Family-friendly=5, Exercise needs=3, Easy to train=3, Tolerates being alone=1, Likes other pets=5, Energy level=3, Grooming needs=2, Shedding=1; Dog suitable for non-experienced owners; Chatty and vocal dog; Generally friendly with other dogs

### Danish-Swedish Farmdog (`danish-swedish-farmdog`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/danish-swedish-farmdog/ : Height: 12.5-14.5 inches; Weight: 15-20 pounds; Life Expectancy: 11-13 years. weightKg converted from lb unless noted.
- PetMD (weights/lifespan match AKC; temperament cross-check): https://www.petmd.com/dog/breeds/danish-swedish-farmdog
- Not recognised by the UK Kennel Club and not on Purina UK, so only two trait sources (AKC + PetMD).

### Border Terrier (`border-terrier`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/border-terrier/ : Height: 12-15 inches; Weight: 13-15.5 pounds (male), 11.5-14 pounds (female); Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/terrier/border-terrier/ : size Small, exercise Up to 1 hour per day, grooming More than once a week, sheds Yes, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/border-terrier : vet rating Family-friendly=4, Exercise needs=5, Easy to train=3, alone=1, Likes other pets=2, Energy level=4, Grooming needs=3, Shedding=3; Dog suitable for non-experienced owners; Chatty and vocal dog; Generally friendly with other dogs

### Whippet (`whippet`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/whippet/ : Height: 19-22 inches (male), 18-21 inches (female); Weight: 25-40 pounds; Life Expectancy: 12-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/hound/whippet/ : size Small, exercise Up to 1 hour per day, grooming Once a week, sheds Yes, home Small house, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/whippet : vet rating Family-friendly=5, Exercise needs=5, Easy to train=4, Tolerates being alone=2, Likes other pets=3, Energy level=5, Grooming needs=3, Shedding=2; Dog suitable for owners with some experience; Chatty and vocal dog; Generally friendly with other dogs

### Bichon Frise (`bichon-frise`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/bichon-frise/ : Height: 9.5-11.5 inches; Weight: 12-18 pounds; Life Expectancy: 14-15 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/toy/bichon-frise/ : size Small, exercise Up to 30 minutes per day, grooming Every day, sheds No, home Flat/ Apartment, lifespan Over 12 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/bichon-frise : vet rating Family-friendly=5, Exercise needs=3, Easy to train=5, Tolerates being alone=1, Likes other pets=4, Energy level=5, Grooming needs=4, Shedding=2; Dog suitable for non-experienced owners; Chatty and vocal dog; Could have issues with unknown dogs but gets along with known dogs; Bichon Frise dogs can suffer from:

### Newfoundland (`newfoundland`)
- Facts (weightLb, lifespan): AKC https://www.akc.org/dog-breeds/newfoundland/ : Height: 28 inches (average male), 26 inches (average female); Weight: 130-150 pounds (male), 100-120 pounds (female); Life Expectancy: 9-10 years. weightKg converted from lb unless noted.
- Kennel Club UK https://www.thekennelclub.org.uk/search/breeds-a-to-z/breeds/working/newfoundland/ : size Large, exercise Up to 1 hour per day, grooming Every day, sheds Yes, home Large house, lifespan Under 10 years
- Purina UK https://www.purina.co.uk/find-a-pet/dog-breeds/newfoundland : vet rating Family-friendly=4, Exercise needs=2, Easy to train=2, Tolerates being alone=2, Likes other pets=3, Energy level=2, Grooming needs=5, Shedding=5; Dogs suitable for experienced owners; Quiet dog; Could have issues with unknown dogs but gets along with known dogs

## 6. How this was applied (2026-10-22)

- The dataset now lives inside `wp-content/plugins/pawmanual-calculators/calculators/dog-breed/dog-breed.js` (between `BREEDS-DATA-START` and `BREEDS-DATA-END`). Edit it there; this file is the source record.
- French Bulldog is set to `small` (the size rule gives medium) to match `/dogs/breeds/french-bulldog/` and common usage. Australian Shepherd stays `medium` as noted above.
- Still open, for the content pass on the breed pages (wave 5, not live): `poodle.md` lifespans (12–15 / 13–16 / 14–18) have no vet or kennel-club source; the dataset uses PDSA/VCA's 12–14 / 13–15 / 12–14. `french-bulldog.md` calls shedding "Low" where AKC, Purina and the Kennel Club say moderate.
