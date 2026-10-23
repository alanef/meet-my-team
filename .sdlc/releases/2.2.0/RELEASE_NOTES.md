# Release 2.2.0

**Date:** 2026-10-23
**Previous:** v2.1.1

## Highlights

A security release. Team member details and shortcode attributes are now escaped, which closes stored
cross-site scripting on the front end and on the team member edit screen. It also brings the opt-in library
fixes: the notice can be dismissed, and "Allow & Continue" actually subscribes. The plugin's red CI is green again.

## What's New

### Security
- Team member names, designations, emails, links and pictures are escaped on the front end.
- `[meet-my-team]` attributes are escaped and validated:
  - container tags are limited to div, section, article, aside, ul, ol, li and span;
  - align, enable_modal, display_picture, disable_modal_centering and debug accept only their documented values.
- The Bio Picture is saved as a clean URL, and the team member edit screen escapes every field.
- Request data in the bundled meta box library is unslashed and sanitized. Its oEmbed lookup no longer answers logged-out visitors.

### Fixes
- "Allow & Continue" subscribed nobody and recorded the user as opted out (PLUGINS_SMALL-7).
- The opt-in notice could not be dismissed, linked to the wrong place, stayed up if the opt-in screen was left
  without a choice, and could belong to the wrong plugin on multi-plugin sites (PLUGINS_SMALL-6).
- Deleting the plugin now removes its stored opt-in choice.
- Name and designation headings close with the right tags.

### Improvements
- Added: an FAQ entry on reporting security bugs through the Patchstack VDP.
- The bundled opt-in library is updated to 1.3.3. Tested up to WordPress 7.1.
- Removed: an unused demo file and a stray archive from the package.

## Breaking Changes

None intended. Visible changes for unusual data:
- HTML in a team member's title, or in a designation, now shows as text instead of being rendered.
- Quotes in names show straight rather than curly.
- A shortcode with an invalid container tag falls back to `div`.

## Migration Guide

None. Stored data and option keys are unchanged. Users already recorded as opted out are not asked again
(Alan's decision on PLUGINS_SMALL-7).

## Technical Details

### Issues Included
| Issue | Title | Type |
|-------|-------|------|
| PLUGINS_SMALL-6 | free_plugin_lib: setup notice can't be dismissed and never goes away | Fix |
| PLUGINS_SMALL-7 | free_plugin_lib: "Allow & Continue" subscribes nobody (plugin_map gap) | Fix |
| PLUGINS_SMALL-5 | Boilerplate release standard (tooling only) | Tooling |
| (no ticket) | Escaping, input sanitising and Plugin Check fixes; independently reviewed twice | Security |

### Commits
- 302ec9c, 0cea2b4, 2493fd5, 25f5fdb, 8457139, 4f86c53, 52b04b3, 0d3dca3, d09d83e: tooling syncs (PLUGINS_SMALL-5)
- 06a66b3: Add FAQ on reporting security bugs via Patchstack VDP
- d5b9b92: Reformat shortcodes.php and declare its properties (an uncommitted 2024 edit, no behaviour change)
- 6c24d0c: Escape output and sanitize input in the bundled meta box library
- 340f358: Take free_plugin_lib 1.3.3 and clean up its option on uninstall (PLUGINS_SMALL-6, -7)
- ff735ba: Fix the Plugin Check errors in the plugin package
- dd126ed: Tested up to WordPress 7.1
- cd2dff6: Escape the team on the front end and validate shortcode attributes
- Release 2.2.0 (version bump, upgrade notice, regenerated .pot)

## Security and quality work without a ticket

### Errors fixed
**PHPCS security, 221 errors and 7 warnings**, all in the bundled Custom-Metaboxes-and-Fields 1.0.1 (CMB).
MMT's own files had none.

| File | Errors | What |
|---|---|---|
| `helpers/cmb_Meta_Box_types.php` | 174 | unescaped output |
| `init.php` | 36 + 4 warnings | escaping, nonce and sanitising |
| `helpers/cmb_Meta_Box_ajax.php` | 11 | sanitising |
| `helpers/cmb_Meta_Box_Show_Filters.php` | 3 warnings | nonce |

By type: 185 unescaped output, 14 missing nonce verification, about 22 unslashed or unsanitized input.

**Plugin Check, 159 errors.** CI never reached it before, because the job stopped at PHPCS.

| Count | What |
|---|---|
| 108 | wrong or missing text domain |
| 27 | translation strings built from variables |
| 6 | missing direct-access guards |
| 5 | `date()` |
| 3 | deprecated `get_terms` |
| 1 | stray zip |
| 1 each | `parse_url`, LIKE wildcards, an unprepared query, a translators comment, a `load_plugin_textdomain` argument |

Plugin Check on the built zip now has 0 errors. Warnings remain: scripts not in the footer, missing versions,
one direct query, and `load_plugin_textdomain`.

**Front-end XSS, not visible to PHPCS** (shortcodes.php builds a string rather than echoing it). Found by the
first independent review, fixed in cd2dff6:
- shortcode attributes went into tag names and attributes;
- member fields went into `src`, `href`, `alt` and headings;
- the CMB file field skipped sanitising on save.

### phpcs:ignore and phpcs:disable, each with its reason in the code
1. `init.php` save_fields, field value: kept slashed because `update_metadata()` unslashes post and user meta.
   It's sanitized by `sanitization_cb()`, or by `esc_url_raw()` for file fields. The caller checks the nonce.
2. `init.php` file ID: NonceVerification only, because the caller of save_fields() checked the nonce.
3. `init.php` get_object_id, `user_id` and `post`: NonceVerification.Recommended, read-only, values absint'd.
4. `cmb_Meta_Box_Show_Filters.php`, `$_GET['page']`: NonceVerification.Recommended, read-only routing, sanitized.
5. `init.php` cmb_metabox_form `echo $form`: each field is escaped by cmb_print_metabox(), and the format's values are escaped.
6. `cmb_Meta_Box_types.php` oEmbed echo: WordPress oEmbed provider HTML; kses would strip the iframe.
7. `public/class-meet-my-team.php` get_blog_ids: a fixed query on a core table with no user input, run on network activation.

### Independent reviews
- **Review 1, d5b9b92..dd126ed:** no regressions in the diff, but it confirmed live, pre-existing stored XSS
  on the front end (two critical findings, two warnings, one suggestion). All were fixed in cd2dff6.
- **Review 2, dd126ed..cd2dff6:** no remaining issues (no critical, no warning). Every payload is neutralised,
  saving through the real form works, defaults are unchanged, nothing fatals, and debug.log stays empty.
  Non-blocking follow-ups are under "Known limitations" in CHANGE_IMPACT.md.

### Before and after captures (wp-env, WP 7.1.3)
- **Admin edit screens, new screen, settings page, and a save round-trip** with quotes, a backslash and HTML:
  byte-identical to the pre-change baseline after each commit (6c24d0c, ff735ba, cd2dff6).
- **Front end, 7 shortcode variants.** Every difference is intended:
  1. heading close tags fixed for every member;
  2. member 7's special characters are encoded (`<b>` and `<Lead>` show as text; quotes straight instead of curly);
  3. the `alt` text is no longer cut off at a quote;
  4. a `"` in a Personal URL is dropped instead of breaking the href;
  5. `align="right"` now gives `data-align='left'`, which behaves the same.
- **Attack shortcode** `parent_container_class="x' onmouseover='alert(1)" item_container="img src=x onerror=alert(2) "`:
  live before, neutralised after (class encoded, tag falls back to `div`).
- **Bio Picture `x" onerror="alert(1)` through the edit form:** before, stored raw and live on the front end;
  after, stored as `http://x%20onerror=alert(1)`, escaped everywhere. A legitimate URL is stored unchanged.
- **Control:** the admin attribute-injection payload was live with the old CMB code and escaped with the new
  code, which proves the capture can detect a difference.

### What ff735ba deleted, and why that's safe
- `admin/includes/Custom-Metaboxes-and-Fields/example-functions.php` is CMB's demo meta-box code. Nothing loads
  it; MMT requires only CMB's `init.php`. The only mentions are in CMB's readme. It accounted for 90 Plugin Check errors.
- `admin/includes/Custom-Metaboxes-and-Fields-for-WordPress-master.zip` is a 30-file download of the CMB
  source, added in 2024 (9f77001). WordPress never runs code from a zip, and Plugin Check rejects archives in a plugin.

## Evidence

All issues passed their gates:
- Verification: 3/3 (`.sdlc/PLUGINS_SMALL-5`, `-6`, `-7`)
- Security: 2/2 passed (+1 skipped via the `.trivial` marker)
- Tests: `.sdlc/releases/2.2.0/TESTS_PASSED.md`
- Dependencies: `.sdlc/releases/2.2.0/DEPENDENCY_AUDIT.md`. The shipped Composer tree is clean; the dev-only npm
  tree has 12 advisories (3 critical, 4 high, 5 moderate), warn only.

## Contributors

- Alan Fuller (alanef)
- AI Assistant

---
Generated by: AI Assistant
Date: 2026-10-23
