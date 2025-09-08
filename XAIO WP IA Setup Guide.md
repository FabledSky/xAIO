# XAIO WP Information Architecture — Setup Guide

## 0) Prerequisites

1. **WordPress** (latest).

2. **Plugins (free)**

   * **Advanced Custom Fields** (ACF).
   * **Custom Post Type UI** (CPT UI) — easiest way to register CPTs/taxonomies.
   * *(Optional)* **WPGraphQL** + **WPGraphQL for ACF** — for clean APIs.
   * *(Optional)* **Redirection** — keep canonical URLs if you ever rename slugs.

3. **Permalinks**: Settings → Permalinks → **Post name**.

> Note on ACF Free: You have **Post Object** and **Relationship** field types, but **Repeater** is **Pro-only**. In this guide we replace repeaters with either (a) separate CPTs or (b) plain text fields with clear, simple formatting.

---

## 1) Register Custom Post Types (CPT UI)

Create these CPTs (menu: CPT UI → Add/Edit Post Types). For each, set:

* **Post Type Slug**: as listed below (lowercase).
* **Plural Label** / **Singular Label**: shown below.
* **Supports**: `title`, `editor`, `revisions`. (Disable comments.)
* **Has Archive**: **true** for Fact, Event, Report, Snippet, Evidence, Source; **false** for Info (your choice).
* **Show in REST API**: **true** (helps future API/LLM use).
* **Rewrite**: **Slug equals the CPT slug** below.

CPTs to create:

1. **Fact**

* Slug: `fact`
* Purpose: atomic, validated claim.
* Archive: true

2. **Event**

* Slug: `event`
* Purpose: time/place-bounded collection of facts.
* Archive: true

3. **Report**

* Slug: `report`
* Purpose: bundle of validated facts + optional context (clearly labeled).
* Archive: true

4. **Snippet**

* Slug: `snippet`
* Purpose: ultra-compact factual update (links to Fact/Event).
* Archive: true

5. **Evidence**

* Slug: `evidence`
* Purpose: a single cited item reusable across Facts/Events/Reports.
* Archive: true

6. **Source**

* Slug: `source`
* Purpose: canonical entity for publishers/accounts (BBC, a specific journalist).
* Archive: true

7. **Organization** *(optional but recommended)*

* Slug: `organization`
* Purpose: governments, agencies, media orgs, NGOs.
* Archive: true

8. **Contributor** *(optional; add later if you want)*

* Slug: `contributor`
* Purpose: citizen journalists, influencers, researchers.
* Archive: true

9. **Info**

* Slug: `info`
* Purpose: site-level housekeeping (about, standards, policies, terms).
* Archive: false (typically)

> Tip: In “Menu Icon,” pick simple icons to keep the admin clean.

---

## 2) Register Custom Taxonomies (CPT UI)

Create these taxonomies (CPT UI → Add/Edit Taxonomies). For each, **show in REST**, enable “Hierarchical” only where noted, and attach to the listed CPTs.

1. **Domain**

* Taxonomy slug: `domain`
* Hierarchical: **false**
* Attach to: `fact`, `event`, `report`
* Examples: `geopolitics`, `public_health`, `science`, `economics`

2. **Geography**

* Slug: `geo`
* Hierarchical: **true** (continent → country → region → city)
* Attach to: `fact`, `event`

3. **Source Type**

* Slug: `source_type`
* Hierarchical: **false**
* Attach to: `source`, `evidence`
* Examples: `official_doc`, `dataset`, `peer_reviewed`, `news_org`, `research_institute`, `citizen_journalist`, `social_post`, `video`, `satellite`, `other`

4. **Language** *(optional)*

* Slug: `lang`
* Hierarchical: **false**
* Attach to: `evidence`, `source`

5. **Actor** *(optional)*

* Slug: `actor`
* Hierarchical: **false**
* Attach to: `event`, `fact`, `report`
* Use if you don’t want separate `Organization` CPT; otherwise keep this minimal.

---

## 3) ACF Field Groups (ACF Free-compatible)

ACF → **Field Groups** → Add New. For each group, set **Location Rules** to the matching CPT. Use only free field types (Text, Textarea, URL, Number, True/False, Select, Date/Time Picker, Relationship, Post Object).

### 3.1 Fact — Field Group: “Fact Fields”

* Location: **Post Type is equal to Fact**

Fields:

1. **Claim Text** (Text, *required*)
   Instruction: one-sentence, atomic, time/place-bounded.
2. **Status** (Select, *required*)
   Choices: `validated` (default). *(Publicly XAIO shows validated only.)*
3. **Corroboration Score (%)** (Number, min 0, max 100)
   Instruction: target ≥ 90%.
4. **Evidence Links** (Relationship → **Evidence**, allow multiple, *required*, min 1, ideally ≥10)
   Filters: search; Elements: show title.
5. **Contradiction Links** (Relationship → **Evidence**, allow multiple)
   Instruction: credible dissent or qualifying sources.
6. **Provenance Notes** (Textarea)
   Instruction: archives, hashes, geo/chrono notes.
7. **Validator Signature** (Text)
   Example: `objAI:sha256:...` (+ reviewer ID if used).
8. **Last Validated At** (Date Time Picker)
9. **Correction Issued** (True/False)
   If true, display banner.
10. **Correction Summary** (Textarea)
    Instruction: concise explanation shown in banner.

*(No repeater for change log; see §5.4 for a free-friendly approach.)*

---

### 3.2 Event — Field Group: “Event Fields”

* Location: **Post Type is equal to Event**

Fields:

1. **Event Type** (Select) — e.g., protest, policy\_announcement, attack, election.
2. **Start Time** (Date Time Picker)
3. **End Time** (Date Time Picker)
4. **Where (Latitude)** (Text)
5. **Where (Longitude)** (Text)
6. **Participants** (Relationship → **Organization** or **Contributor**, multiple)
7. **Related Facts** (Relationship → **Fact**, multiple, *recommended*)
8. **Timeline Notes (Plain)** (Textarea)
   *(Free-friendly alternative to repeater. One step per line, with ISO time prefix.)*
9. **Validator Signature** (Text)
10. **Last Validated At** (Date Time Picker)
11. **Correction Issued** (True/False)
12. **Correction Summary** (Textarea)

---

### 3.3 Report — Field Group: “Report Fields”

* Location: **Post Type is equal to Report**

Fields:

1. **Executive Summary** (Textarea, *short factual bullets only*)
2. **Facts Included** (Relationship → **Fact**, multiple, *recommended*)
3. **Analysis (Context)** (Textarea)
   Instruction: interpretation only; no new claims.
4. **Validator Signature** (Text)
5. **Last Validated At** (Date Time Picker)
6. **Correction Issued** (True/False)
7. **Correction Summary** (Textarea)

---

### 3.4 Snippet — Field Group: “Snippet Fields”

* Location: **Post Type is equal to Snippet**

Fields:

1. **Body (≤ 120 tokens)** (Textarea — set **Character Limit** e.g., 800 chars)
2. **Parent Item** (Post Object → restrict to **Fact** or **Event**, single select)
3. **Key Sources** (Relationship → **Evidence**, multiple)

---

### 3.5 Evidence — Field Group: “Evidence Fields”

* Location: **Post Type is equal to Evidence**

Fields:

1. **Source Entity** (Post Object → **Source**, single, *required*)
2. **Original URL** (URL, *required*)
3. **Title (Verbatim or Neutralized)** (Text)
4. **Published At** (Date Time Picker)
5. **Collected At** (Date Time Picker)
6. **Archive URL** (URL)
7. **Media Hash** (Text)
   Instruction: sha256 of file or key frame set.
8. **Independence Key** (Text)
   Instruction: group/owner/affiliate cluster tag.
9. **Excerpts (Plain)** (Textarea)
   Instruction: one excerpt per line; include locator in parentheses, e.g., `“…quote…” (para 3)`
10. **Notes** (Textarea)
11. **Is Correction** (True/False)

*(Free-friendly: “Excerpts” uses plain text instead of a repeater.)*

---

### 3.6 Source — Field Group: “Source Fields”

* Location: **Post Type is equal to Source**

Fields:

1. **Display Name** (Text, *required*)
2. **Source Type** → *use taxonomy `source_type` on the post* (no field needed)
3. **Owner Organization** (Post Object → **Organization**, single)
4. **Official Handles (Plain)** (Textarea)
   Instruction: one URL per line (site, X/Twitter, YouTube, Telegram, etc.).
5. **Disclosures** (Textarea)

---

### 3.7 Organization — Field Group: “Organization Fields” *(optional)*

* Location: **Post Type is equal to Organization**

Fields:

1. **Official Name** (Text, *required*)
2. **Aliases (Plain)** (Textarea) — one alias per line
3. **Country** (Text)
4. **Ownership Cluster ID** (Text)

---

### 3.8 Contributor — Field Group: “Contributor Fields” *(optional)*

* Location: **Post Type is equal to Contributor**

Fields:

1. **Profile Links (Plain)** (Textarea) — one URL per line (socials, website)
2. **Disclosures** (Textarea)
3. **Status** (Select: active, suspended, retired)

---

### 3.9 Info — Field Group: *(optional)*

* Usually just use the Block Editor. If desired, add a **Short Description** (Text) to standardize intros.

---

## 4) Permalinks & Date-Prefixed Slugs

Use short, semantic, stable slugs. For Facts/Events/Reports/Snippets, prefix with date `YYYY-MM-DD-`.

Add this snippet to your theme’s `functions.php` (or a small site plugin) to **auto-prefix** on save:

```php
add_action('save_post', function($post_id) {
  $post = get_post($post_id);
  if (!$post || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) return;

  $types = ['fact','event','report','snippet']; // CPTs to prefix
  if (!in_array($post->post_type, $types, true)) return;

  // Only on first publish or when slug empty
  if ('auto-draft' === $post->post_status) return;

  $slug = $post->post_name;
  $date = get_the_date('Y-m-d', $post_id);
  if (!$date) $date = current_time('Y-m-d');

  // Avoid double-prefixing
  if (strpos($slug, $date . '-') !== 0) {
    $base = sanitize_title(substr($post->post_title, 0, 120));
    $new_slug = sanitize_title($date . '-' . $base);
    // Ensure uniqueness
    $unique = wp_unique_post_slug($new_slug, $post_id, $post->post_status, $post->post_type, $post->post_parent);
    remove_action('save_post', __FUNCTION__);
    wp_update_post(['ID' => $post_id, 'post_name' => $unique]);
    add_action('save_post', __FUNCTION__);
  }
});
```

---

## 5) Templates & Display (Theme)

Create minimal templates per CPT in your (child) theme:

* `single-fact.php`, `single-event.php`, `single-report.php`, `single-snippet.php`, `single-evidence.php`, `single-source.php`, `single-info.php`
* Archives (optional): `archive-fact.php`, etc.

### 5.1 Fact — minimal display logic

**Fields to render (in order):**

* Title (match Claim Text or keep Title concise)
* **Status** (badge “validated”)
* **Corroboration Score**
* **Last Validated At**
* **Correction Banner** (if `correction_issued` true → show `correction_summary`)
* **Claim Text**
* **Scope** (print `domain` terms and `geo` terms if set; show absolute dates from post date if relevant)
* **Evidence list** (loop over Relationship)
* **Contradictions** (if any)
* **Provenance Notes**
* **Validator Signature**
* **JSON-LD** (see §6)

**Evidence list** sample (inside `single-fact.php`):

```php
$evidence = get_field('evidence_links');
if ($evidence) {
  echo '<h3>Key Evidence</h3><ol>';
  foreach ($evidence as $ev) {
    $url = get_field('original_url', $ev->ID);
    $title = get_field('title_verb_neutral', $ev->ID) ?: get_the_title($ev->ID);
    $pub = get_field('published_at', $ev->ID);
    echo '<li><a href="'.esc_url($url).'" rel="noopener nofollow">'.$title.'</a>';
    if ($pub) echo ' — '.esc_html($pub);
    echo '</li>';
  }
  echo '</ol>';
}
```

*(If you used a different field name for Title, adjust accordingly.)*

### 5.2 Event — display logic

* Header: type, start–end time, lat/long (if provided)
* **Related Facts** (loop Relationship → list)
* **Timeline Notes (Plain)**: split by newline and print in order
* **Participants** (Organizations/Contributors linked)
* **JSON-LD** (Event schema, §6)

### 5.3 Report — display logic

* **Executive Summary** (bullets)
* **Facts Included** (linked list)
* **Analysis (Context)** (clearly labeled)
* **JSON-LD** (Collection/Breadcrumb, §6)

### 5.4 Change Log (ACF Free workaround)

You can skip custom UI and rely on:

* **Correction Banner + Summary** on the post (fields already added)
* **Native WP Revisions** (show a “Revisions” link)
* *(Optional)* Create a simple CPT **Change** with fields `{related_post (Post Object), at (DateTime), summary (Textarea), editor (Text)}` to show a table on the page. This is optional and can be added later.

---

## 6) JSON-LD (SEO/LLM)

Add compact JSON-LD in your templates. Example for **Fact** (ClaimReview-style):

```php
<?php
$claim = get_field('claim_text');
$validated = get_field('status') === 'validated';
$date = get_field('last_validated_at') ?: get_the_modified_date('c');
$permalink = get_permalink();
$org_name = 'XAIO';

$data = [
  '@context' => 'https://schema.org',
  '@type'    => 'ClaimReview',
  'url'      => $permalink,
  'claimReviewed' => $claim,
  'datePublished' => $date,
  'author' => ['@type' => 'Organization', 'name' => $org_name],
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

For **Event**, use `@type: "Event"` with `startDate`, `endDate`, `location` (Place with geo coordinates). For **Report**, a simple `@type: "CreativeWork"` or a collection pattern is fine.

---

## 7) Cross-Linking Patterns (Queries)

* **Fact → Evidence**: you already have a direct Relationship field.
* **Evidence → Source**: Post Object field (single).
* **Report → Facts**: Relationship field.
* **Event → Facts**: Relationship field.
* **Snippet → Parent**: Post Object restricted to Fact/Event.

**Reverse lookup** (e.g., show “Used in these Facts” on an Evidence page): query posts where `evidence_links` meta contains this Evidence ID.

```php
$used_in = new WP_Query([
  'post_type' => 'fact',
  'posts_per_page' => 10,
  'meta_query' => [[
    'key'     => 'evidence_links',   // ACF stores relationship IDs in serialized meta
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

*(This “LIKE” approach is standard for ACF Relationship reverse queries. For scale, consider a custom linker table later.)*

---

## 8) Taxonomy Hygiene

* **Domain** on Facts/Events/Reports (multi-select).
* **Geo** on Facts/Events (hierarchical).
* **Source Type** on Sources and Evidence.
* **Language** on Evidence (if helpful).

Keep terms lowercase, hyphenated slugs, no spaces. Curate a small canonical set to avoid drift.

---

## 9) Editorial Workflow (How to Add Content)

### Add a **Source**

* Add → Source
* Set **Display Name**, select **Source Type**, link **Owner Organization** (if any), add **Official Handles** (one per line), add **Disclosures**.

### Add **Evidence**

* Add → Evidence
* Set **Source Entity**, **Original URL**, **Title**, **Published At**, **Archive URL**, **Independence Key**, **Excerpts (Plain)** (one per line), **Notes**.
* Set **Language** taxonomy if used.

### Add a **Fact**

* Add → Fact
* Title: short (can mirror Claim).
* Fill **Claim Text**, **Corroboration Score**, **Evidence Links** (≥10 ideally), **Contradiction Links** if any, **Provenance Notes**, **Validator Signature**, **Last Validated At**.
* Set **Domain** (+ **Geo** if relevant).
* Publish → the slug auto-prefixes with date.

### Add an **Event**

* Add → Event
* Fill **Event Type**, **Start/End Time**, **Lat/Long**, **Participants**, **Related Facts**, **Timeline Notes (Plain)**.
* Set **Geo** and **Domain**.

### Add a **Report**

* Add → Report
* Fill **Executive Summary**, **Facts Included**, **Analysis** (clearly labeled), **Validator Signature**, **Last Validated At**.
* Set **Domain**.

### Add a **Snippet**

* Add → Snippet
* Fill **Body**, **Parent Item** (Fact/Event), **Key Sources** (if any).
* Publish.

### Add **Info** pages

* Add → Info
* Use Block Editor for About, Standards, Policies, Terms.

---

## 10) Optional Tweaks

* **Noindex for Evidence** (if you want to keep them crawlable but not indexed):
  In `functions.php`:

```php
add_action('wp_head', function() {
  if (is_singular('evidence')) {
    echo '<meta name="robots" content="noindex,follow">';
  }
});
```

* **Sitemaps**: ensure your SEO or core sitemap includes CPT archives you want indexed (Facts, Events, Reports, Snippets, Sources).
* **Admin columns**: add ACF columns (status, corroboration score) for quick triage.

---

## 11) Consistency Rules (House Style)

* Slugs: lowercase, hyphenated, no stop words unless essential.
* Date-prefix for Fact/Event/Report/Snippet: enforced by the save hook.
* Titles: concise, factual, time-bounded where appropriate.
* “Correction Issued”: must be true + summary if any factual amendment is made.
* **Wikipedia**: do not cite the article; cite the underlying sources.

---

## 12) Future-Proofing

* If relationships become heavy, migrate reverse lookups to a custom linker table or use WPGraphQL with resolvers.
* For timelines and change logs, you can later switch to ACF Pro repeaters or purpose-built CPTs (`timeline_step`, `change_entry`).
* Add a small `/wp-json/xaio/v1/...` endpoint that returns compact JSON (AIO-friendly) for each CPT when you’re ready.
