# Tests Passed: 2.2.0

**Date:** 2026-11-08T11:31:00Z
**Commit:** e822928139f2dcaad26cc03ca98a8b19b016ef52
**Tracked changes at run time:** 1

| Gate | Result |
|---|---|
| composer install | pass |
| composer validate --strict | pass |
| composer run check | pass |
| version consistency | pass |
| build (composer run build) | pass |
| Plugin Check | not run locally (no plugin-check script); CI runs it on the built zip |
| PHPUnit (npm test) | SKIPPED: wp-env not running (--no-wp-env); CI tests job is the gate |

PHPUnit summaries:


Full output: tests.log (not committed).
