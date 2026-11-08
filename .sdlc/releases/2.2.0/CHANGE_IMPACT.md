# Change Impact Assessment: 2.2.0

## Risk Level: Medium

Medium rather than Low, because the change is broad:
- every output in the bundled CMB admin library and in the front-end shortcode renderer was rewritten;
- shortcode attributes are now validated;
- the opt-in library moved from 1.2.x to 1.3.3;
- two files were deleted from the package.

What keeps it at Medium rather than High:
- the admin screens and the save round-trip are byte-identical to the baseline;
- every front-end difference is listed and intended;
- two independent reviews (the second found nothing remaining);
- CI is green, with Plugin Check at 0 errors.

### Factors Considered
- Number of issues: 3 tickets, plus security work, which has no ticket
- Security-sensitive changes: yes (detail in YouTrack).
- API changes: no. Shortcode attributes keep their names. Invalid values now fall back to defaults that behave
  as the old pass-through did.
- Database changes: no (same meta and option keys)
- Breaking changes: none intended; see the visible changes below

## Affected Areas

### Code Areas
- `public/includes/shortcodes.php`: front-end output, validated attributes, and the heading tag fix.
- `admin/includes/Custom-Metaboxes-and-Fields/`:
  - field rendering and request input;
  - `example-functions.php` and the source zip removed.
- `admin/class-meet-my-team-admin.php`, `admin/includes/class-meet-my-team-build-cpt.php`, and
  `public/class-meet-my-team.php`: literal i18n strings and direct-access guards.
- `uninstall.php`: removes the library's `MMT-Free_form_rendered` option.
- The bundled `alanef/free_plugin_lib` is now 1.3.3, which brings the opt-in notice, redirect and subscription fixes.

### Visible changes users may notice
- Quotes in names and designations render straight instead of curly.
- A shortcode with an invalid container tag renders a `div`.
- Admins who left the opt-in screen without choosing see it again from the plugin's settings until they choose.

### Configuration
- None.

### Dependencies
- alanef/free_plugin_lib ^v1.0.0 (2.1.1 shipped 1.2.x) → ^1.3.3. The shipped Composer tree has no advisories.

## Rollback Plan

### Immediate Rollback
```bash
git revert --no-edit <Release 2.2.0>..cd2dff6   # or rebuild from tag v2.1.1 and ship it as 2.2.1
```
On wordpress.org a rollback ships as a new version. Prefer a forward fix.

### Database Rollback
N/A

### Configuration Rollback
N/A

## Monitoring

Post-release, monitor:
- wp.org support topics for Meet My Team mentioning broken layout, missing modal, or names showing HTML tags
- Patchstack and WPScan for any report against Meet My Team
