# XAIO WordPress Information Architecture — ACF-Only Setup Guide (Free, v6.1+)

> Goal: Model XAIO’s **Fact / Event / Report / Snippet / Evidence / Source / Info** types with **cross-linking** and **machine-friendly** permalinks using **ACF’s built-in Post Types & Taxonomies** plus **ACF Field Groups**.
> Plugins required: **Advanced Custom Fields (Free)** only.

---

## 0) Prerequisites

* WordPress (latest)
* ACF Free **v6.1+** installed & active
* Settings → Permalinks → **Post name**
* (Optional) Version control: enable ACF Local JSON (Theme folder `acf-json/`) for auto-sync of fields/post types/taxonomies

---

## 1) Create Custom Post Types (ACF → Post Types)

You’ll add the following CPTs. For each:
**Location:** `ACF → Post Types → Add New`
**Common settings:**

* **Singular/Plural Label:** as below
* **Post Type Key (slug):** as below (lowercase)
* **Visibility:** Public
* **Has archive:** On (except `Info`, usually Off)
* **Supports:** Title, Editor, Revisions (disable Comments)
* **Show in REST API:** On
* **Rewrite Slug:** match the CPT slug (e.g., `fact`, `event`)

### 1.1 Fact

* Key: `fact`
* Purpose: atomic, validated claim

### 1.2 Event

* Key: `event`
* Purpose: time/place-bounded aggregation of facts

### 1.3 Report

* Key: `report`
* Purpose: bundle of validated facts with optional (clearly labeled) analysis

### 1.4 Snippet

* Key: `snippet`
* Purpose: ultra-compact factual update linking back to a Fact or Event

### 1.5 Evidence

* Key: `evidence`
* Purpose: single cited item (URL/document/media) reusable across many Facts/Events/Reports

### 1.6 Source

* Key: `source`
* Purpose: canonical publisher/account (e.g., BBC, a specific journalist)

### 1.7 Organization *(optional)*

* Key: `organization`
* Purpose: governments, agencies, media orgs, NGOs

### 1.8 Contributor *(optional)*

* Key: `contributor`
* Purpose: citizen journalists, influencers, researchers

### 1.9 Info

* Key: `info`
* Purpose: site-level housekeeping (about, standards, policies, terms)
* **Has archive:** Off (typical)

> Tip: After adding CPTs, visit **Settings → Permalinks → Save** to flush rewrite rules.

---

## 2) Create Custom Taxonomies (ACF → Taxonomies)

**Location:** `ACF → Taxonomies → Add New`
**Common settings:**

* **Public:** On
* **Show in REST API:** On
* **Rewrite Slug:** match taxonomy key
* Attach to CPTs as listed.

### 2.1 Domain

* Key: `domain` (non-hierarchical)
* Attach to: `fact`, `event`, `report`
* Examples: `geopolitics`, `public_health`, `science`, `economics`

### 2.2 Geography

* Key: `geo` (**hierarchical**)
* Attach to: `fact`, `event`
* Structure: Continent → Country → Region → City

### 2.3 Source Type

* Key: `source_type` (non-hierarchical)
* Attach to: `source`, `evidence`
* Examples: `official_doc`, `dataset`, `peer_reviewed`, `news_org`, `research_institute`, `citizen_journalist`, `social_post`, `video`, `satellite`, `other`

### 2.4 Language *(optional)*

* Key: `lang` (non-hierarchical)
* Attach to: `evidence`, `source`

### 2.5 Actor *(optional)*

* Key: `actor` (non-hierarchical)
* Attach to: `event`, `fact`, `report`

---

## 3) Add ACF Field Groups (ACF → Field Groups)

> ACF Free does **not** include “Repeater” fields. Where repeatability helps, we’ll use **plain text** with “one per line” instructions or **relationships** between CPTs.

### 3.1 Fact — Field Group: “Fact Fields”

**Location rule:** Post Type = Fact

| Field Label             | Type                                         | Notes                                    |
| ----------------------- | -------------------------------------------- | ---------------------------------------- |
| Claim Text              | Text (required)                              | One-sentence, atomic, time/place bounded |
| Status                  | Select (required)                            | Choices: `validated` (default)           |
| Corroboration Score (%) | Number (0–100)                               | Target ≥ 90%                             |
| Evidence Links          | Relationship → Evidence (multiple, required) | Aim ≥ 10; search enabled                 |
| Contradiction Links     | Relationship → Evidence (multiple)           | Credible dissent/qualifiers              |
| Provenance Notes        | Textarea                                     | Archives, hashes, geo/chrono notes       |
| Validator Signature     | Text                                         | e.g., `objAI:sha256:...` (+ reviewer ID) |
| Last Validated At       | Date Time Picker                             |                                          |
| Correction Issued       | True/False                                   | If true, show banner                     |
| Correction Summary      | Textarea                                     | One concise line for banner              |

**Taxonomies to use on this CPT:** `domain` (+ `geo` where relevant)

---

### 3.2 Event — Field Group: “Event Fields”

**Location rule:** Post Type = Event

| Field Label            | Type                                               | Notes                                                 |
| ---------------------- | -------------------------------------------------- | ----------------------------------------------------- |
| Event Type             | Select                                             | e.g., protest, policy\_announcement, attack, election |
| Start Time             | Date Time Picker                                   |                                                       |
| End Time               | Date Time Picker                                   |                                                       |
| Where (Latitude)       | Text                                               |                                                       |
| Where (Longitude)      | Text                                               |                                                       |
| Participants           | Relationship → Organization/Contributor (multiple) |                                                       |
| Related Facts          | Relationship → Fact (multiple)                     |                                                       |
| Timeline Notes (Plain) | Textarea                                           | One step per line, `YYYY-MM-DDTHH:MMZ — detail`       |
| Validator Signature    | Text                                               |                                                       |
| Last Validated At      | Date Time Picker                                   |                                                       |
| Correction Issued      | True/False                                         |                                                       |
| Correction Summary     | Textarea                                           |                                                       |

**Taxonomies:** `geo`, `domain` (+ optional `actor`)

---

### 3.3 Report — Field Group: “Report Fields”

**Location rule:** Post Type = Report

| Field Label         | Type                           | Notes                              |
| ------------------- | ------------------------------ | ---------------------------------- |
| Executive Summary   | Textarea                       | Short factual bullets only         |
| Facts Included      | Relationship → Fact (multiple) |                                    |
| Analysis (Context)  | Textarea                       | Interpretation only; no new claims |
| Validator Signature | Text                           |                                    |
| Last Validated At   | Date Time Picker               |                                    |
| Correction Issued   | True/False                     |                                    |
| Correction Summary  | Textarea                       |                                    |

**Taxonomies:** `domain` (+ optional `actor`)

---

### 3.4 Snippet — Field Group: “Snippet Fields”

**Location rule:** Post Type = Snippet

| Field Label         | Type                                          | Notes         |
| ------------------- | --------------------------------------------- | ------------- |
| Body (≤ 120 tokens) | Textarea (set character limit \~800)          | Ultra-compact |
| Parent Item         | Post Object → limit to Fact or Event (single) |               |
| Key Sources         | Relationship → Evidence (multiple)            | Optional      |

---

### 3.5 Evidence — Field Group: “Evidence Fields”

**Location rule:** Post Type = Evidence

| Field Label                     | Type                                    | Notes                                                         |
| ------------------------------- | --------------------------------------- | ------------------------------------------------------------- |
| Source Entity                   | Post Object → Source (single, required) |                                                               |
| Original URL                    | URL (required)                          |                                                               |
| Title (Verbatim or Neutralized) | Text                                    |                                                               |
| Published At                    | Date Time Picker                        |                                                               |
| Collected At                    | Date Time Picker                        |                                                               |
| Archive URL                     | URL                                     |                                                               |
| Media Hash                      | Text                                    | sha256 of file or of key frames                               |
| Independence Key                | Text                                    | Owner/affiliate cluster tag                                   |
| Excerpts (Plain)                | Textarea                                | One excerpt per line; include locator `(para 3)` or `(00:12)` |
| Notes                           | Textarea                                |                                                               |
| Is Correction                   | True/False                              |                                                               |

**Taxonomies:** `source_type`, `lang` (optional)

---

### 3.6 Source — Field Group: “Source Fields”

**Location rule:** Post Type = Source

| Field Label              | Type                                | Notes                                         |
| ------------------------ | ----------------------------------- | --------------------------------------------- |
| Display Name             | Text (required)                     |                                               |
| Owner Organization       | Post Object → Organization (single) | Optional                                      |
| Official Handles (Plain) | Textarea                            | One URL per line (site, X, YouTube, Telegram) |
| Disclosures              | Textarea                            |                                               |

**Taxonomies:** `source_type`, `lang` (optional)

---

### 3.7 Organization *(optional)* — Field Group: “Organization Fields”

**Location rule:** Post Type = Organization

| Field Label          | Type            | Notes              |
| -------------------- | --------------- | ------------------ |
| Official Name        | Text (required) |                    |
| Aliases (Plain)      | Textarea        | One alias per line |
| Country              | Text            |                    |
| Ownership Cluster ID | Text            |                    |

---

### 3.8 Contributor *(optional)* — Field Group: “Contributor Fields”

**Location rule:** Post Type = Contributor

| Field Label           | Type     | Notes                            |
| --------------------- | -------- | -------------------------------- |
| Profile Links (Plain) | Textarea | One URL per line                 |
| Disclosures           | Textarea |                                  |
| Status                | Select   | `active`, `suspended`, `retired` |

---

## 4) Permalink Strategy (date-prefixed slugs)

**Recommended:** For `fact`, `event`, `report`, `snippet`, use `YYYY-MM-DD-short-slug`.
ACF’s CPT UI doesn’t enforce slug prefixes automatically. Add this small snippet to your theme’s `functions.php` (or a tiny site plugin) to auto-prefix on save:

```php
add_action('wp_insert_post', function($post_id, $post, $update){
  if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) return;

  $types = ['fact','event','report','snippet'];
  if (!in_array($post->post_type, $types, true)) return;

  $date = get_the_date('Y-m-d', $post_id);
  if (!$date || $post->post_status === 'draft') $date = current_time('Y-m-d');

  $slug = $post->post_name ?: sanitize_title($post->post_title);
  if (strpos($slug, $date . '-') === 0) return;

  $new_slug = sanitize_title($date . '-' . substr($slug, 0, 120));
  $unique   = wp_unique_post_slug($new_slug, $post_id, $post->post_status, $post->post_type, $post->post_parent);

  // prevent loops
  remove_action('wp_insert_post', __FUNCTION__, 10);
  wp_update_post(['ID' => $post_id, 'post_name' => $unique]);
  add_action('wp_insert_post', __FUNCTION__, 10, 3);
}, 10, 3);
```

Then visit **Settings → Permalinks → Save**.

---

## 5) Template Display (minimal, machine-first)

Create (in your theme) `single-{cpt}.php` files to render **neutral, structured** pages. Suggested order:

### 5.1 Fact (`single-fact.php`)

* Title (concise; mirror Claim)
* **Status** (badge: “validated”)
* **Corroboration Score**; **Last Validated At**
* **Correction Banner** (if `Correction Issued` → show `Correction Summary`)
* **Claim Text**
* **Scope**: print `domain` + `geo` term(s)
* **Evidence List** (Relationship → Evidence)
* **Contradictions** (if any)
* **Provenance Notes**
* **Validator Signature**
* **JSON-LD** (see §7)

Evidence loop example:

```php
$evidence = get_field('evidence_links');
if ($evidence) {
  echo '<h3>Key Evidence</h3><ol>';
  foreach ($evidence as $ev) {
    $url   = get_field('original_url', $ev->ID);
    $title = get_field('title_verb_or_neutral', $ev->ID) ?: get_the_title($ev->ID);
    $pub   = get_field('published_at', $ev->ID);
    echo '<li><a href="'.esc_url($url).'" rel="noopener nofollow">'.$title.'</a>';
    if ($pub) echo ' — '.esc_html($pub);
    echo '</li>';
  }
  echo '</ol>';
}
```

### 5.2 Event (`single-event.php`)

* Header: type, start–end time, lat/long
* **Related Facts** (list)
* **Timeline Notes (Plain)**: split lines, print in order
* **Participants** (Organizations/Contributors)
* **JSON-LD** (`Event` schema)

### 5.3 Report (`single-report.php`)

* **Executive Summary**
* **Facts Included** (linked list)
* **Analysis (Context)** (clearly labeled)
* **JSON-LD** (Collection/CreativeWork)

*(Create similar minimal templates for Snippet/Evidence/Source/Info.)*

---

## 6) Cross-Linking Patterns

* **Fact ↔ Evidence**: Relationship on Fact to multiple Evidence
* **Evidence → Source**: Post Object (single)
* **Event ↔ Fact**: Relationship on Event to multiple Facts
* **Report ↔ Fact**: Relationship on Report to multiple Facts
* **Snippet → Parent**: Post Object to Fact or Event

**Reverse lookup** example (show “Used in Facts” on Evidence page):

```php
$used_in = new WP_Query([
  'post_type'      => 'fact',
  'posts_per_page' => 10,
  'meta_query'     => [[
    'key'     => 'evidence_links',   // ACF stores relationship IDs serialized
    'value'   => '"' . get_the_ID() . '"',
    'compare' => 'LIKE'
  ]]
]);
if ($used_in->have_posts()) {
  echo '<h3>Used in Facts</h3><ul>';
  while ($used_in->have_posts()) { $used_in->the_post();
    echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
  }
  echo '</ul>';
  wp_reset_postdata();
}
```

---

## 7) JSON-LD (compact, retrieval-oriented)

**Fact (ClaimReview-style):**

```php
<?php
$claim = get_field('claim_text');
$validated = get_field('status') === 'validated';
$date = get_field('last_validated_at') ?: get_the_modified_date('c');
$permalink = get_permalink();
$data = [
  '@context' => 'https://schema.org',
  '@type'    => 'ClaimReview',
  'url'      => $permalink,
  'claimReviewed' => $claim,
  'datePublished' => $date,
  'author' => ['@type' => 'Organization', 'name' => 'XAIO'],
  'reviewRating' => [
    '@type' => 'Rating',
    'ratingValue' => $validated ? 1 : 0,
    'bestRating' => 1,
    'worstRating' => 0,
    'alternateName' => $validated ? 'validated' : 'unpublished'
  ]
];
?>
<script type="application/ld+json">
<?php echo wp_json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); ?>
</script>
```

**Event:** use `@type: "Event"` with `startDate`, `endDate`, `location` (Place → GeoCoordinates).
**Report:** `@type: "CreativeWork"` (or collection pattern) with links to Facts.

---

## 8) Editorial Workflow (what your team clicks)

**Add a Source** → set Display Name, Source Type (taxonomy), Owner Org (optional), Official Handles (one per line), Disclosures.
**Add Evidence** → link Source Entity, enter Original URL, Title, Published/Collected times, Archive URL, Independence Key, Excerpts (one per line), Notes, `source_type`/`lang`.
**Add Fact** → fill Claim Text, Corroboration Score, Evidence Links (≥10 ideally), Contradictions (if any), Provenance Notes, Validator Signature, Last Validated At; set `domain` (+ `geo` if relevant).
**Add Event** → fill Event Type, Start/End, Lat/Long, Participants, Related Facts, Timeline Notes; set `geo` & `domain`.
**Add Report** → fill Executive Summary, Facts Included, Analysis; set `domain`.
**Add Snippet** → Body, Parent Item (Fact/Event), Key Sources (optional).
**Add Info** → freeform (About/Standards/Policies/Terms).

---

## 9) Consistency Rules (house style)

* Slugs: **lowercase**, **hyphenated**, avoid stop words unless essential
* **Date prefix** enforced for `fact|event|report|snippet` (code in §4)
* Titles: concise, factual, time-bounded where relevant
* “Correction Issued”: set True + provide **Correction Summary**
* **Wikipedia**: do **not** cite; cite the underlying sources instead

---

## 10) Portability (ACF Local JSON / Export)

* Create `/wp-content/themes/your-theme/acf-json/`
* ACF will auto-save JSON for **Field Groups, Post Types, and Taxonomies** when you click **Save**
* Commit these JSON files to Git for reproducible environments
* You can also **Export to PHP** from ACF for hard-coding the model later

