# XAIO.org Playbook v0.1 — Content, Validation & Publishing Guide

> **Purpose of this document**: An end‑to‑end, working playbook for everyone creating and validating content for XAIO.org. It doubles as a product/content spec, contributor handbook, validation SOP, and writing guide aligned to Objectivity AI and the AIO framework.

---

## 1) Scope & Positioning (Internal)

**One‑sentence purpose**: *XAIO.org exists to publish verified factual information in a format that is maximally retrievable and reusable by people, LLMs, and search engines—optimized for clarity, neutrality, efficiency, and energy savings via cached, validated facts.*

* **Focus**: Any domain with high demand for verifiable facts. Current intake leans toward geopolitics and public‑interest science/health because of the prevalence of misinformation and the availability of citizen documentation, but domain is not restricted.
* **Primary audiences**: (1) LLMs and search engines (machine retrieval), (2) public readers, (3) researchers/OSINT analysts and journalists.
* **Outputs**: Facts and factual reports validated by Objectivity AI and formatted according to AIO (semantic clarity, compactness, low‑token density).
* **Non‑goals (implicit)**: XAIO is not commentary or marketing. Analysis may be included only when clearly separated from factual claims and written under Objectivity AI’s logic/neutrality rules.

---

## 2) Core Principles (Internal)

* **Factuality First**: Every published claim must pass corroboration thresholds and objective validation.
* **Neutrality & Transparency**: Maximize neutrality of wording; disclose sources, conflicts, and reasoning trace. Show dissenting evidence when it materially changes interpretation.
* **Reproducibility**: Preserve provenance and verification steps so others can re‑validate.
* **Efficiency (AIO)**: Structure content for compact embeddings, low‑token parsing, and fast machine retrieval.
* **Auditability**: Corrections are highly visible. Version history is accessible. Contradictory evidence is surfaced, not hidden.
* **Source Directness**: Prefer primary sources. Avoid quoting Wikipedia articles; cite the underlying sources instead.

---

## 3) Content Types & Canonical Schemas

XAIO content is modular and machine‑oriented. Each item has a stable ID, explicit fields, and JSON‑LD for SEO/LLM ingestion.

### 3.1 Fact Page (atomic claim)

**Use when** a single claim can be stated precisely and backed by evidence.

**Required fields**

* `xaio_id` (UUID)
* `claim`: One‑sentence, testable statement.
* `domain`: e.g., geopolitics, public health, economics, science, technology.
* `scope`: entity/place/time bounds.
* `status`: `validated` (XAIO only publishes validated facts; see §6).
* `evidence_set`: array of Evidence objects (see 3.5) with at least **10 independent factual sources** where applicable; higher where events are widely reported.
* `corroboration_score`: percentage of independent sources supporting the claim; **target ≥ 90%**.
* `contradictions`: list of credible sources that oppose/qualify the claim, with notes.
* `provenance`: how evidence was collected and verified.
* `last_validated_at` (ISO 8601, UTC) and `validator_signature` (Objectivity AI hash + human reviewer ID if applicable).
* `change_log`: append‑only entries (timestamp, change, reason, editor/validator).
* `analysis` (optional): clearly separated context/explanation.

**JSON‑LD (suggested)**

```json
{
  "@context": "https://schema.org",
  "@type": "ClaimReview",
  "url": "https://xaio.org/fact/{xaio_id}",
  "claimReviewed": "<claim>",
  "reviewRating": {
    "@type": "Rating",
    "ratingValue": 1,
    "bestRating": 1,
    "worstRating": 0,
    "alternateName": "validated"
  },
  "datePublished": "<last_validated_at>",
  "itemReviewed": {"@type": "Thing", "name": "<scope>"},
  "author": {"@type": "Organization", "name": "XAIO"}
}
```

> Note: We intentionally use a binary *validated* status in the public artifact. Internally, corroboration metrics and dissent notes are retained and surfaced in UI.

### 3.2 Event Snapshot (time‑bounded)

**Use when** reporting on a specific event with time/place.

Fields extend Fact Page with:

* `event_type` (e.g., protest, airstrike, policy announcement, publication).
* `when` (start/end timestamps), `where` (geo lat/long, ISO country/region).
* `participants` (organizations/actors; link to entity nodes).
* `media_evidence` (curated, hashed frames, source URLs, archive links).
* `timeline` (ordered facts with timestamps).

### 3.3 Factual Report (multi‑claim bundle)

**Use when** a topic requires multiple atomic facts with structured context.

Sections:

1. **Executive Summary (factual)** — bullets linking to atomic Fact Pages.
2. **Facts** — numbered list of validated claims (link to Fact Pages).
3. **Context & Analysis (clearly labeled)** — background, causal framing; no new claims.
4. **Evidence Table** — matrix of sources by claim.
5. **Contradictory Evidence** — what credible dissent exists.
6. **Provenance & Methods** — how evidence was collected, verified.
7. **Validation Record** — Objectivity AI report and human‑in‑the‑loop notes.

### 3.4 News Snippet (ultra‑compact)

**Use when** delivering minimal, cache‑friendly updates that roll up into Event Snapshot or Fact Pages.

* Hard cap: \~80–120 tokens body, 1–2 links to strongest sources, structured meta.
* Always references a parent Event/Fact when available.

### 3.5 Evidence Object

```json
{
  "source_id": "<stable hash>",
  "type": "official_doc|news_report|social_post|video|satellite|dataset|peer_reviewed|other",
  "url": "<direct link>",
  "title": "<verbatim or neutralized>",
  "publisher": "<outlet or account>",
  "published_at": "<ISO8601>",
  "collected_at": "<ISO8601>",
  "independence_key": "<domain/owner/affiliate hash>",
  "excerpts": [{"text": "<quote>", "loc": "<page:line or timestamp>"}],
  "media_hash": "<sha256 of original or key frames>",
  "archive": {"url": "<permalink>", "timestamp": "<ISO8601>"},
  "notes": "<verification notes, geolocation, chrono checks>",
  "is_correction": false
}
```

---

## 4) AIO Writing & Structuring Guidelines (for Humans & Models)

**Objective**: minimize tokens, maximize retrieval, preserve neutrality.

### 4.1 Claim Style

* Make claims **atomic**, **testable**, and **time‑bounded**.
* Use **absolute dates** (e.g., “2025‑07‑12”) and **specific locations** (country/region/city, GPS when suitable).
* Avoid adjectives/adverbs unless **quantified** (e.g., “large” → “\~25,000 participants per police estimates”).
* Avoid scare quotes, sarcasm, rhetorical framing, and speculative verbs.
* Prefer present simple or past simple. Avoid future speculation.

**Good**: “On 2025‑06‑30, the Ministry of Health reported 3,214 confirmed cases (daily bulletin #472).”

**Weak**: “Officials recently claimed thousands of cases.”

### 4.2 Evidence & Citations

* Prefer **primary sources** (official documents, direct statements, datasets, raw media). If the only reference is a secondary outlet, cite that outlet directly—not its Wikipedia page.
* Maintain **independence**: diversify across owners/regions/perspectives. Track independence via `independence_key`.
* For **social posts**, capture author handle, platform, post URL, timestamp, and archive URL. Extract and hash key frames for video.
* Don’t over‑quote. Summarize neutrally; retain precise locators for audit.

### 4.3 Structure for Retrieval

* Start pages with a **TL;DR facts block** (bullets of validated claims, each linking to Fact Pages).
* Use **stable headings** and **consistent field order** so LLMs learn the schema.
* Keep **analysis** in a separate, clearly labeled section. No new factual claims may appear there; only interpretation of already‑validated claims.

### 4.4 Language Hygiene

* Use **plain English**. Define acronyms on first use.
* Numbers: provide units; use SI where applicable; add uncertainty bounds when relevant.
* Quotations: use only when essential to meaning; otherwise paraphrase neutrally.
* People/org names: use official spellings; include aliases in metadata.

### 4.5 Energy & Token Efficiency (AIO)

* Prefer **short sentences** and **compact bullets**.
* Deduplicate facts across pages; link to existing Fact Pages.
* Use **canonical IDs** and **cross‑references** rather than repeating content.
* Keep News Snippets tiny; they should be cheap to embed and index.

---

## 5) Validation & Corroboration (Objectivity AI + Human‑in‑the‑Loop)

XAIO publishes **validated** facts only. Internal systems may hold drafts in any state; public artifacts are binary: **validated** or **retracted/corrected**.

### 5.1 Corroboration Rule

* **Target ≥ 90% corroboration** across independent sources for the given claim.
* **Default minimum sources**: **10** (or best‑effort if topic space is sparse), scaling higher for widely reported events.
* **Independence**: no single owner/affiliate cluster should dominate the evidence set.

### 5.2 Source Classes & Heuristics

* **Tier A (strongest)**: official docs, peer‑reviewed articles, raw datasets, direct statements on official channels, original media with verifiable provenance (and successful media forensics).
* **Tier B**: reputable news agencies, recognized research institutes.
* **Tier C**: vetted citizen/influencer media with corroborated geo/chrono and cross‑source confirmation.
* **Out‑of‑scope**: Wikipedia articles as a citation target (use their cited sources instead); anonymous claims without corroboration; unverifiable private leaks.

### 5.3 Validation Pipeline

1. **Ingestion**: Author submits claim(s) + evidence set using XAIO schema.
2. **Automated Screening (Objectivity AI)**:

   * Deduplication & canonicalization (match to existing facts).
   * Source independence & balance check.
   * Propaganda/loaded‑language heuristics (surface, don’t block; nudge neutral phrasing).
   * Media forensics pre‑check (hash match, ELA/metadata sanity, frame sampling).
   * Corroboration score computation.
3. **Human Review** (for edge cases/high‑impact items):

   * Sanity check on wording neutrality and scope boundaries.
   * Verify that dissenting but credible evidence is represented in `contradictions`.
   * Approve/reject; request more evidence if needed.
4. **Publication**: Assign `xaio_id`, freeze JSON‑LD, record `validator_signature`.
5. **Post‑Publication Monitoring**: Watch for credible contradictions; trigger re‑validation when thresholds are met or at scheduled TTL.

### 5.4 Validator Report (stored, partially public)

* Corroboration score; list of sources by tier; independence matrix; media forensics summary; reasoning notes; known limitations.
* Public UI exposes a concise version; full report is kept for audit.

---

## 6) Classification, Corrections, and Retractions

* **Public status**: `validated` only. Drafts/unverified items never appear publicly.
* **Corrections**: If any validated fact is amended, display a **prominent banner** at the top: **“Correction issued”** with a summary and link to the change log.
* **Retractions**: If a fact is withdrawn, show a tombstone page with the retraction reason, affected versions, and links to superseding facts.
* **Versioning**: Append‑only `change_log` with timestamps, author/validator IDs, reason, and diff summary. Prior versions remain accessible.

---

## 7) Contributor Program (Citizen Journalists, Influencers, Researchers)

### 7.1 Identity & Discoverability

* Contributors may be **pseudonymous**; reliability is built via **verifiable public footprints**: link to social accounts, prior posts, and on‑platform track record.
* Each profile lists **disclosures** (funding, affiliations, partnerships, geography, prior political work) and a **history of validations/corrections**.

### 7.2 Onboarding

* **Baseline training**: short modules on neutrality phrasing, evidence capture, provenance, and XAIO schema.
* **Starter assignment**: submit a small Event Snapshot with 10+ sources across tiers; pass validation.
* **Checklist** (see Appendix B) must be completed before publish rights.

### 7.3 Disclosures & Conflicts

* Required: funding sources (direct/indirect), sponsor relationships, memberships, and any material ties to the subject matter.
* Display disclosures on profile and on any related report pages.

### 7.4 Conduct & Enforcement

* **Suspension/removal** triggers: intentional submission of false information; coordinated manipulation; refusal to disclose conflicts; repeated egregious neutrality violations; gross unlawful activity.
* **Rehabilitation**: Possible via time‑bound probation, accuracy streaks, and peer review.

---

## 8) Source Capture, Provenance & Archiving

### 8.1 Mandatory Metadata

* Original URL, title, publisher, author/handle, published timestamp, collected timestamp, archive permalink, file hashes; for media: EXIF when available + frame hashes.

### 8.2 Independence Tracking

* Assign `independence_key` based on ownership/affiliate clustering to avoid over‑reliance on a single ecosystem.

### 8.3 Social Media Intake

* Capture canonical URLs, author handles, timestamps, and **archive snapshots** at submission time.
* For videos: store a small **fingerprint set** (e.g., 5–10 key frames, hashed) and note any edits.

### 8.4 Geolocation/Chronolocation (OSINT)

* Record geolocation reasoning (landmarks, signage, sun/shadow, weather, traffic, terrain, satellite map match).
* Note all tools and datasets used for verification.

---

## 9) Writing the Page (Step‑by‑Step SOP)

1. **Define the claim**: One sentence, atomic, bounded by time/place.
2. **Collect evidence**: Aim for ≥10 independent sources (or best‑effort where sparse). Prefer Tier A/B; include vetted Tier C when independently corroborated.
3. **Draft the Fact Page**: Fill all required fields; write `claim` neutrally; add `contradictions` if present.
4. **Run Objectivity AI pre‑check**: Address phrasing and evidence gaps.
5. **Human review (if required)**: Adjust scope, wording, evidence.
6. **Publish**: Freeze JSON‑LD; store validator report; ship.
7. **Monitor**: Track new evidence; update if contradictions emerge; apply correction banner when needed.

**Micro‑checklist**

* [ ] Absolute dates & precise scope
* [ ] Neutral language; no speculative verbs
* [ ] ≥10 sources; independence confirmed
* [ ] Contradictory credible evidence captured
* [ ] Provenance & archive links present
* [ ] JSON‑LD valid; cross‑links to related facts/events

---

## 10) Style Guide (Neutrality & Clarity)

* **Headlines**: Declarative, exact, time‑bounded. No clickbait. Example: “2025‑08‑02: Agency X publishes dataset Y showing Z.”
* **Deck**: One sentence reinforcing the exact claim.
* **Body**: Start with “Key Facts” bullets (link to Fact Pages), followed by details, then analysis (if any).
* **Numbers & Units**: Provide ranges/CI when relevant; avoid “thousands” if exact figures exist.
* **Attribution**: Attribute facts to sources with inline footnotes or reference IDs; keep quotes minimal.
* **Language**: Avoid value‑laden terms (e.g., “brutal”, “heroic”). Replace with precise descriptions.

---

## 11) Domain Notes (Guidance for Common Categories)

### 11.1 Geopolitics & Conflict

* Expect abundant Tier C citizen media; rely on cross‑platform corroboration and geo/chrono verification.
* Elevate Tier A/B confirmation when available. Use satellite, official statements, and recognized agencies cautiously but directly.
* Consider potential state propaganda; include credible contradictions when they materially affect interpretation.

### 11.2 Science, Health, and Wellness

* Prioritize **peer‑reviewed** literature and official advisories/datasets; contextualize preprints clearly as such.
* Avoid over‑claiming; reflect consensus positions when stable.
* Translate complex methodology into plain language in **Analysis** (clearly marked), with the facts section kept tight.

### 11.3 Policy & Economics

* Use official gazettes, regulatory filings, statistical bureaus, and central bank releases as Tier A.
* For forecasts, separate them as **Analysis**; publish facts only when a forecast becomes a realized data point.

---

## 12) UX & Information Design (for Readability and Machine Retrieval)

* **Page header**: claim/event title, status badge `validated`, last validated timestamp, corroboration score.
* **Key Facts block**: compact bullets with links.
* **Evidence Table**: sortable by source type, date, independence key.
* **Contradictions panel**: collapsed by default but prominent.
* **Provenance card**: how we validated (tools, checks, hashes).
* **Change log**: pinned link; banner if correction present.
* **Embeds**: when using social media, prefer static snapshots + links to originals to avoid link rot.

---

## 13) Metrics & Review Cadence (Content‑Focused)

* **Accuracy**: post‑publication contradiction rate; correction latency.
* **Coverage**: unique Fact Pages added per week; sources per claim; independence distribution.
* **Efficiency**: average token count per page; embedding cost per fact; cache hit rate.
* **Retrievability**: search success\@k, time‑to‑fact for common queries.
* **Community**: contributor retention; validation throughput; percent of pages with citizen evidence.

**Cadence**: weekly ops review (content & validation metrics); monthly audit of random sample; quarterly public transparency summary.

---

## 14) Minimal Safety & Legal Posture (for Content Teams)

* **Do not publish**: doxxing; PII of private individuals; instructions for harm; child sexual abuse material; explicit gore thumbnails; incitement to violence.
* **Sensitive contexts**: elections, hostage situations, active combat—require human review.
* **Takedowns**: log requests; preserve evidence; escalate for legal review; add tombstone notes when content is removed.

*(Full policies can expand later; content teams follow these minimums.)*

---

## 15) Example Templates

### 15.1 Fact Page — Markdown Layout

```markdown
# <Claim Title>

**Status**: Validated • **Corroboration**: <>=90%> • **Last validated**: <ISO8601>

**Claim**
<One‑sentence atomic statement.>

**Scope**
- **Domain**: <domain>
- **Time**: <date/time bounds>
- **Place**: <country/region/city, GPS if applicable>

**Key Evidence**
1. <Source 1 — type, publisher, date> [link]
2. <Source 2> [link]
...

**Contradictory Evidence**
- <Source A> [link] — <why it contradicts or qualifies>

**Provenance & Methods**
<How we collected, archived, and verified.>

**Analysis (Context)**
<Optional explanatory section. No new factual claims.>

**Change Log**
- 2025‑09‑08: Initial validation by Objectivity AI (hash …); Reviewer: <ID>.
```

### 15.2 Event Snapshot — Markdown Layout

```markdown
# <Event Title>

**Type**: <event_type> • **Status**: Validated • **When**: <start–end> • **Where**: <place/GPS>

**Summary**
- <2–4 factual bullets with links to Fact Pages>

**Timeline**
- 12:04Z — <fact>
- 12:38Z — <fact>

**Participants**
- <Actor/Org> — role

**Evidence Table**
| Source | Type | Time | Note |
|---|---|---|---|

**Provenance & Methods**
<geo/chrono, media forensics, tools>

**Change Log**
- <entries>
```

### 15.3 News Snippet — Layout

```markdown
**[YYYY‑MM‑DD] <Short title>** — <80–120 tokens of factual update>. Key sources: [S1], [S2]. Parent: <Fact/Event link>.
```

### 15.4 Evidence Object — JSON

```json
{
  "source_id": "sha256:…",
  "type": "news_report",
  "url": "https://…",
  "title": "…",
  "publisher": "…",
  "published_at": "2025-08-31T14:22:00Z",
  "collected_at": "2025-09-01T03:11:00Z",
  "independence_key": "cluster:owner_group_X",
  "excerpts": [{"text": "…", "loc": "para 3"}],
  "media_hash": null,
  "archive": {"url": "https://archive.…", "timestamp": "2025-09-01T03:12:00Z"},
  "notes": "Corroborates casualty figure; matches official brief.",
  "is_correction": false
}
```

---

## 16) Checklists (Print‑Ready)

### 16.1 Writer Checklist (Every Page)

* [ ] Claim is atomic, precise, time/place bounded.
* [ ] Absolute dates, exact names, defined acronyms.
* [ ] ≥10 independent sources (or best‑effort); Tier A/B prioritized.
* [ ] Contradictions captured with explanations.
* [ ] Provenance documented (archives, hashes, geo/chrono notes).
* [ ] Analysis separated and labeled.
* [ ] JSON‑LD valid; cross‑links added; token budget minimized.

### 16.2 Validator Checklist

* [ ] Corroboration ≥ 90%; independence matrix acceptable.
* [ ] Language neutral; no loaded framing.
* [ ] Media forensics passed (if applicable).
* [ ] Dissent handled fairly; no cherry‑picking.
* [ ] Banner and change log prepared for any corrections.

### 16.3 Evidence Intake Checklist

* [ ] Archive snapshot taken for each URL.
* [ ] Author handle captured; timestamp validated; URL canonicalized.
* [ ] Hashes computed for media; key frames stored.
* [ ] Ownership/affiliate cluster identified.

---

## 17) Example: From Social Post to Validated Fact (Walkthrough)

1. **Observation**: A verified local reporter posts video of an explosion (platform link). Timestamp and skyline visible.
2. **Corroboration sweep**:

   * Cross‑platform matches (platforms A/B/C) within ±1 hour.
   * Official account acknowledges incident; emergency services log.
   * Satellite thermal anomaly detected (if applicable) within the window.
3. **Geo/chrono**: Match building profile to satellite/base map; shadow length to solar position.
4. **Draft claim**: “On 2025‑08‑27 at \~16:42 local time, an explosion occurred at \[location], confirmed by \[official source].”
5. **Evidence set**: 4 Tier A/B, 8+ Tier B/C; independence confirmed.
6. **Validation**: Objectivity AI passes language/forensics checks; corroboration 92%.
7. **Publish**: Fact Page goes live; Event Snapshot links multiple related claims.

---

## 18) Data Model (Conceptual)

**Entities**: `Claim`, `Evidence`, `Event`, `Actor`, `Organization`, `Location`, `Contributor`, `Validation`, `ChangeLog`.

**Relationships**:

* `Claim` -has-many→ `Evidence`
* `Event` -has-many→ `Claim`
* `Claim` -has-many→ `ChangeLog`
* `Validation` -belongs→ `Claim` (latest + history)
* `Contributor` -has-many→ `Evidence` (submitted\_by) and `Report`

**IDs**: Stable UUIDs; human‑readable slugs for URLs; alias table for external IDs.

**TTL/Re‑validation**: Domain‑specific schedules; e.g., geopolitics (7–14 days), public data series (per release), science facts (per consensus updates).

---

## 19) Submission Interfaces (Author Experience)

* **Web form** with schema‑first authoring (guided fields, linting for neutrality and AIO style).
* **Bulk upload** (CSV/JSON) for evidence sets.
* **API** for trusted partners (rate‑limited; requires disclosures on file).
* Real‑time **Objectivity AI assistant** flags neutrality issues, missing provenance, weak independence.

---

## 20) Content Examples (Skeletons)

### 20.1 Geopolitics Event — Skeleton

* TL;DR bullets (validated facts)
* Timeline with timestamps
* Evidence table (mix of official + journalist + citizen)
* Provenance with geo/chrono notes
* Analysis (context only)

### 20.2 Health Claim — Skeleton

* Claim bound to population/time.
* Evidence: guidelines, peer‑reviewed meta‑analyses, datasets.
* Contradictions (methodological caveats).
* Analysis: translate methodology to plain language; no new claims.

---

## 21) Corrections & Public Communication

* **Banner** at top of page on any correction: short summary (“Figure revised from X to Y after updated dataset on YYYY‑MM‑DD”).
* **Tombstones**: Keep retracted page with reason and links to newer facts.
* **Transparency note** (periodic): publish a digest of significant corrections and their causes.

---

## 22) Minimal “About” Guidance

* Keep the About page succinct. Note that XAIO operates under the broader umbrella that originally developed the AIO and Objectivity AI frameworks. Avoid promotional framing. Focus on what XAIO *does*: hosts validated facts in AIO format.

---

## 23) Glossary (Selective)

* **AIO (Artificial Intelligence Optimization)**: Methods to structure content so machines retrieve it accurately with minimal tokens/energy.
* **Objectivity AI**: Validation system embedding neutrality/factuality checks and evidence corroboration.
* **Corroboration Score**: Percent of independent sources supporting a claim.
* **Geo/Chrono Verification**: Methods to verify place and time of media.
* **Provenance**: Documentation of origin and handling of evidence.

---

## Appendix A — Source Reliability Ladder (Guide)

1. **Official / Primary Data**: legal texts, official bulletins, datasets, direct statements.
2. **Peer‑Reviewed / Established Research**.
3. **Reputable News / Institutions**.
4. **Citizen / Influencer Media** with strong provenance and cross‑checks.
5. **Anonymous/Unverifiable** — not admissible without exceptional external corroboration.

---

## Appendix B — Author & Validator Training Outline

* Neutral phrasing and scope discipline.
* Evidence capture: archiving, hashing, independence analysis.
* Media forensics basics.
* AIO formatting: JSON‑LD, token minimization, cross‑linking.
* Correction/retraction protocol.

---

## Appendix C — Example Corroboration Matrix

| Claim | Source A | Source B | Source C | Source D | Source E | … | Score |
| ----- | -------- | -------- | -------- | -------- | -------- | - | ----- |
| C‑001 | ✓        | ✓        | ✓        | ✕        | ✓        | … | 92%   |

---

## Appendix D — Neutral Language Substitutions

* “claimed” → “stated” (when neutrally attributing)
* “admits” → “said” (unless legal admission is material)
* “controversial” → remove; specify the nature of the dispute factually.
* “massive” → quantify (e.g., “approximately 25,000 per \[source]”).

---

## Appendix E — Page Header Elements (Spec)

* Title (claim/event)
* Status badge `validated`
* Corroboration score (percentage)
* Last validated timestamp
* Quick links: Evidence, Contradictions, Provenance, Change Log

---

## Appendix F — Example Submission (Filled)

```json
{
  "xaio_id": "xaio:fact:ab12cd34",
  "claim": "On 2025-07-12, Country X's health ministry reported 3,214 confirmed cases of Condition Y.",
  "domain": "public_health",
  "scope": {"time": "2025-07-12", "place": {"country": "Country X"}},
  "status": "validated",
  "evidence_set": [ /* ≥10 sources across tiers with archives */ ],
  "corroboration_score": 0.93,
  "contradictions": [{"url": "https://…", "note": "Unofficial blog disputes methodology; no data provided."}],
  "provenance": "Official bulletin + dataset; cross‑checked with WHO mirror; archive stored.",
  "last_validated_at": "2025-07-13T09:11:00Z",
  "validator_signature": "objAI:sha256:…",
  "change_log": [{"at": "2025-08-01T10:00:00Z", "change": "Updated figure to 3,221 per revised dataset.", "by": "rev-42"}],
  "analysis": "The week‑over‑week increase aligns with seasonal patterns; see context."
}
```

---

### Final Note

This playbook is intentionally content‑centric. For now, every page we ship must be **precise, neutral, efficiently structured, and fully auditable**. If in doubt, make the claim smaller, add evidence, and separate analysis from facts.
