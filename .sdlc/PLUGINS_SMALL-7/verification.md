# Combined Verification Report: PLUGINS_SMALL-7

**Issue:** free_plugin_lib: "Allow & Continue" subscribes nobody in SSFGM, MMT, LHF and RSHFD (plugin_map gap)
**Date:** 2026-10-23
**Commit:** cd2dff6 (MMT); library free_plugin_lib v1.3.3 (fix 0885daf)

| Review | Status |
|--------|--------|
| Commit Integrity | passed: 0885daf and v1.3.3 in the library; 340f358 in MMT |
| Code Review | passed: a one-line fallback lookup, plus a manage_options check on Skip |
| Acceptance Review | passed |
| Browser-Test Gate | not-applicable |
| **Overall** | **APPROVED** |

**Decision (Alan):** no re-prompt. Users stored as `optout` keep that value. The fix adds no reset logic.

## Evidence
- **Library:** `-Free` shortnames fall back to the plugin ID of the name without the suffix. On 1.3.2 the four
  -Free cases and the skip-capability test fail; on 1.3.3 they pass. Controls behave the same before and
  after: an exact key (CSCF), and unknown names with and without `-Free`.
- **MMT, in wp-env:** `Email('MMT-Free')->handle_optin_submission()` with a `pre_http_request` capture
  (fake 200, never the real webhook) returns `true` and sends `plugin_id=mmt`. With library ≤1.3.2 the
  lookup returned null: nothing was sent and the user was recorded as `optout`.

## Commit Range Covered
340f358 (shared with PLUGINS_SMALL-6). No out-of-range commits.

---
Verified by: AI Assistant
Date: 2026-10-23
