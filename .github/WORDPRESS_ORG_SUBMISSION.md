# WordPress.org submission checklist

Steps to take **before** submitting Mirae to the WordPress.org plugin directory.
This file is excluded from the build ZIP (it lives under `.github/`).

## 1. Slug availability

- Check that `mirae` is available at `https://wordpress.org/plugins/mirae/`.
  If taken, pick a new slug and rename: plugin folder, main file, text domain,
  `Plugin Name`, all references in code/docs, and the GitHub repo's release
  ZIP filename in [release.yaml](workflows/release.yaml).

## 2. Trademark / naming review

- Confirm the name does not appear on the WP trademark exclusion list:
  https://wordpress.org/about/trademark-policy/
- "Mirae" itself is fine; brand-name platform buttons (Spotify, GitHub, etc.)
  are surfaced as user content, not in the plugin name — that's allowed.

## 3. Build for .org distribution

The .org build must NOT contact GitHub. Two ways:

a) Add `define( 'MIRAE_DISABLE_GITHUB_UPDATER', true );` to a tiny `compat-wporg.php`
   that's only included in the .org build, OR
b) Strip `class-mirae-github-updater.php` and the constructor call in
   `class-mirae.php` for the .org build branch.

Option (a) is simpler and what readme.txt currently documents.

## 4. Assets folder (icon, banner, screenshots)

These live in the SVN `/assets/` folder, NOT in the plugin code:

- `icon-256x256.png` (or `.svg`)
- `banner-1544x500.png` (regular)
- `banner-1544x500-rtl.png` (optional)
- `screenshot-1.png` … `screenshot-N.png` matching the order in `readme.txt`

Reference: https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/

## 5. Run Plugin Check (PCP) locally

The official self-review tool: https://wordpress.org/plugins/plugin-check/.
Install on a test site, run against the Mirae .org-build ZIP, fix everything
flagged as `error`. Warnings are usually optional but worth reading.

## 6. readme.txt validation

Paste the file into https://wordpress.org/plugins/developers/readme-validator/
to confirm it parses cleanly.

## 7. Submit

Submit at https://wordpress.org/plugins/developers/add/. After approval you
get an SVN repository — the trunk gets the code, `/tags/<version>/` gets the
release snapshot, and `/assets/` gets the artwork from step 4.

## 8. Post-launch

- Set the GitHub release workflow to skip `.org` builds, OR maintain the
  GitHub releases as the canonical source and sync to SVN on every tag.
- Replace the in-plugin GitHub updater with a no-op for .org users (already
  handled by `MIRAE_DISABLE_GITHUB_UPDATER`).
