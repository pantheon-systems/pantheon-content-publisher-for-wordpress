=== Pantheon Content Publisher ===
Contributors: getpantheon
Tags: pantheon
Requires at least: 5.7
Tested up to: 6.6.1
Stable tag: 1.2.1
Requires PHP: 8.0.0

Publish WordPress content from Google Docs with Pantheon Content Cloud.

== Frequently Asked Questions ==

= How do I connect Pantheon Content Publisher to Google Drive? =
You can generate an access token at https://pcc.pantheon.io/auth.

= Why can’t I edit posts/pages directly? =
Trying to directly edit a page built with Pantheon Content Publisher will redirect you to its respective Google Document. This is done to maintain consistency between the two platforms.

= What happens if I disconnect Pantheon Content Publisher from my Google Drive? =
All posts/pages created with Pantheon Content Publisher will remain on your WordPress site. However, you will no longer be able to edit them from Google Docs.

== Changelog ==
= 1.2.2 =
* Compatibility: Save <style> tag at the end of post content
* Stability: Improve edge case handling for PCC articles
= 1.2.1 =
* Fix: Ensure clean excerpts for PCC articles
* Compatibility: Improve image upload compatibility
= 1.2.0 =
* Feature: Add support for the title, description, tags, categories and featured image custom metadata fields
* Revert: Re-Enable the WordPress editor for PCC articles
= 1.1.2 =
* Feature: Add disconnect button on intermediary screens of auth/config flow
= 1.1.1 =
* Fix: Verify collection URL logic
= 1.1.0 =
* Feature: Check if plugin is correctly configured before hooking logic
* Feature: Disconnect collection when site URL changes
* Fix: enable style tags globally
= 1.0.1 =
* Fix: Update PCC PHP SDK dependency
= 1.0.0 =
Initial Release
