=== Mirae ===
Contributors: maartenkumpen
Tags: linktree, links, profile, social, link-in-bio
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A Linktree-style profile builder for WordPress: add platform buttons, custom labels, and reorder them — all from the admin.

== Description ==

Mirae lets you build a customizable Linktree-style profile page from inside WordPress. Pick a platform from the predefined list, paste a URL, optionally override the button label, and drag-and-drop to reorder.

Mirae is designed to be paired with the companion **Miro** theme, which renders the front page using the data Mirae stores. The plugin will warn in the admin if Miro is not the active theme.

= Features =

* Predefined catalogue of platforms with brand-correct icons and colors (based on LittleLink).
* Add, edit, reorder and delete links from a single admin screen.
* Custom button text per link.
* Front-page settings: display name, intro text, profile picture, background image, overlay pattern, container color, transparency and text color.
* `[mirae]` shortcode to render the link list anywhere.
* Self-hosted updates via GitHub releases.

== Installation ==

1. Upload the `mirae` folder to `/wp-content/plugins/`, or install the ZIP from the GitHub release page.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Install and activate the companion [Miro theme](https://github.com/mrtn/Miro).
4. Go to **Mirae** in the admin menu to add your links and configure the front page.
5. Use the `[mirae]` shortcode on any page or post if you want the list elsewhere than the front page.

== Frequently Asked Questions ==

= Do I need the Miro theme? =

The plugin runs on any theme, but the front-page layout (profile picture, intro text, background) is rendered by the Miro theme. Without Miro you only get the link list via the `[mirae]` shortcode.

= How do updates work? =

Mirae checks the configured GitHub repository (`mrtn/Mirae`, `release` branch) every 12 hours and surfaces new versions through the standard WordPress plugin updater.

= Where do the icons come from? =

The icons and button styles are based on the [LittleLink.io](https://littlelink.io/) project. All trademarks belong to their respective owners.

= How do I uninstall cleanly? =

Deleting the plugin from the **Plugins** screen runs `uninstall.php`, which removes every option Mirae has stored. Deactivation alone keeps your data so you can re-enable later.

== External services ==

This plugin connects to the GitHub API to check whether a newer release is available and to retrieve the release notes shown on the WordPress updates screen. This only applies to copies installed from GitHub; copies installed from the WordPress.org plugin directory do not contact GitHub.

* Service: GitHub REST API (`api.github.com`).
* What is sent: an anonymous `GET` request that includes a User-Agent header containing your site's WordPress version and home URL (the standard WordPress remote-request headers).
* When: at most once every 12 hours, triggered by WordPress's normal update-check cycle.
* Provider terms: [GitHub Terms of Service](https://docs.github.com/en/site-policy/github-terms/github-terms-of-service), [GitHub Privacy Statement](https://docs.github.com/en/site-policy/privacy-policies/github-privacy-statement).

To disable these calls entirely, define `MIRAE_DISABLE_GITHUB_UPDATER` as `true` in your `wp-config.php`.

== Credits ==

* Button styles and platform icons are based on [LittleLink](https://littlelink.io/) by Julian Prieber, MIT-licensed.
* Drag-and-drop reordering uses [TableDnD](https://www.isocra.com/2008/02/table-drag-and-drop-jquery-plugin/) by Denis Howlett, dual MIT/GPL.

== Screenshots ==

1. Admin link manager with drag-and-drop reordering.
2. Front-page settings (intro text, images, colors, transparency).
3. Front-end render of the link list.

== Changelog ==

The full per-release changelog is generated automatically and published on the
[GitHub releases page](https://github.com/mrtn/Mirae/releases).

== Upgrade Notice ==

= 1.2.0 =
Production-ready release: complete uninstall cleanup, conditional asset loading, cached GitHub update lookups, hardened admin sanitization, and full GPL-2.0 LICENSE in the package. No data migration required — your existing links carry over.
