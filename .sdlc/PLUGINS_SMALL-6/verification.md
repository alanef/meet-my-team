# Combined Verification Report: PLUGINS_SMALL-6

**Issue:** free_plugin_lib: setup notice can't be dismissed and never goes away (all free plugins)
**Date:** 2026-10-23
**Commit:** cd2dff6 (MMT); library free_plugin_lib v1.3.3 (4f34052)

## Executive Summary

| Review | Status |
|--------|--------|
| Commit Integrity | passed: 340f358 on master and origin/master; library tags v1.3.1, v1.3.2, v1.3.3 resolve |
| Code Review | passed (library: independent review; its critical 1.3.1 redirect bug fixed in 1.3.2) |
| Acceptance Review | passed: the five fix items, checked in MMT's own wp-env over HTTP |
| Browser-Test Gate | n-a: no layout change; notice and redirect checked over real HTTP with rendered HTML inspected |
| **Overall** | **APPROVED** |

## Where the code is
The fix is in alanef/free_plugin_lib (v1.3.0 to v1.3.3). MMT's change for this issue is 340f358: constraint
`^1.3.3`, and `uninstall.php` calling `Main::plugin_uninstall( 'MMT-Free' )`. Since library 1.3.0 the host
plugin must do this; MMT's uninstall.php had been a TODO stub.

## Library evidence (summary; full detail in SUE's .sdlc/PLUGINS_SMALL-6)
- 35 PHPUnit tests in the library. Run against the v1.3.0 code, 7 of the opt-in tests fail; each fix was
  mutation-checked one at a time and caught by its own test.
- Item 4 (shared static shortname) was proved in wp-env with SUE and SWEGTS on the old library, and fixed.
- An independent review of 1.3.1 found a critical regression: a plugin with an empty settings page (RSHFD) had
  every admin and AJAX request redirected. It was reproduced over HTTP and fixed in 1.3.2.

## MMT acceptance, in MMT's wp-env (port 8710, WP 7.1.3, library 1.3.3 from MMT's vendor)
MMT passes a hook suffix (`team_members_page_meet-my-team-settings`) as its page. With `MMT-Free_form_rendered=rendering`:

| Check | Result |
|---|---|
| Dashboard / admin-ajax heartbeat | 200 / 200 (no 1.3.1-style hijack) |
| Notice | `data-shortname="MMT-Free"`, links to `options-general.php?page=ffpl-opt-in-MMT-Free` |
| Dismiss AJAX | `{"success":true}`, `MMT-Free_notice_dismissed`=1, 0 notices on reload |
| Settings page (edit.php?post_type=team_members&page=meet-my-team-settings) | 302 to the opt-in screen (it never redirected with the old library) |
| Skip | 302, status `optout`, settings page then 200 |
| Uninstall (uninstall.php with WP_UNINSTALL_PLUGIN) | `MMT-Free_form_rendered` removed |

Run twice: before and after the wp-env restart. Options were reset to `optout` afterwards.

## Commit Range Covered
| SHA | Subject |
|-----|---------|
| 340f358 | Take free_plugin_lib 1.3.3 and clean up its option on uninstall (PLUGINS_SMALL-6, PLUGINS_SMALL-7) |

No out-of-range commits for this issue.

---
Verified by: AI Assistant
Date: 2026-10-23
