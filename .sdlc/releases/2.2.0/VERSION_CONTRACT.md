# Version Contract: 2.2.0

**Date:** 2026-10-23

## Canonical Source

The plugin header `Version:` in meet-my-team/meet-my-team.php and `Stable tag:` in meet-my-team/readme.txt.
`scripts/release/bump-version.sh` writes and reads back both, plus package.json, package-lock.json and the
.pot. CI fails when they disagree.

## Expected State After Deploy

- master contains every version location set to `2.2.0`, and `= 2.2.0 =` at the top of changelog.txt
- Git tag `v2.2.0` exists on master's HEAD. release.yml builds the zip and deploys to wp.org SVN (`Type: free`)
- wordpress.org/plugins/meet-my-team reports 2.2.0 after the wp.org release confirmation
- The zip bundles alanef/free_plugin_lib 1.3.3, and contains no example-functions.php and no .zip

## Required Deploy Command

```
/tk-deploy live
```

Do not push tags outside `/tk-deploy live`. Its preconditions refuse on a dirty tree, a version mismatch,
missing or stale evidence, checks.yml not green on HEAD, or an existing tag.

## Post-Deploy Verification

See `.claude/commands/deploy-live.md`: the SVN tag check, the wp.org release confirmation, and the YouTrack update.
