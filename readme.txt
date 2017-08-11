=== qTranslate Plus ===
Contributors: Mirko_Primapagina, chineseleper
Tags: qTranslate, plus, for, 3.9, 4.0, multilingual, multi, language, admin, tinymce, polyglot, bilingual, widget, switcher, primapagina, professional, human, translation, service
Requires at least: 3.9
Tested up to: 4.0
Stable tag: 2.6.2

Adds userfriendly multilingual content management and translation support into Wordpress.

== Description ==

**IMPORTANT**: This is not an Extension of the official qTranslate.

qTranslate Plus is an unOfficial modified version of qTranslate (created by Qian Qin) created to be **compatible with Wordpress 3.9 or highter**.

At this moment the original plugin is dead (Last update of the original plugin 2014-1-26) and it will never be compatibile with the new versions of Wordpress.

- Now compatible with Wordpress 3.9 - 3.9.1 - 3.9.2 - 4.0
- Tested also with Wordpress 4.0 and working without updating the code
- No more deactivation if WordPress has been updated to a new version
- Fixed posts date bug
- Added function for the new TinyMCE (thanks to warenhaus - https://gist.github.com/warenhaus/10990386)

For more informations: mirko@primapagina.it - http://www.primapagina.it/blog/qtranslate-plus/

-- **Original description** --

Writing multilingual content is already hard enough, why make using a plugin even more complicated? I created qTranslate to let Wordpress have an easy to use interface for managing a fully multilingual web site.

qTranslate makes creation of multilingual content as easy as working with a single language. Here are some features:

- qTranslate Services - Professional human translation with two clicks
- One-Click-Switching between the languages - Change the language as easy as switching between Visual and HTML
- Language customizations without changing the .mo files - Use Quick-Tags instead for easy localization
- Multilingual dates out of the box - Translates dates and time for you
- Comes with a lot of languages already builtin! - English, German, Simplified Chinese and a lot of others
- No more juggling with .mo-files! - qTranslate will download them automatically for you
- Choose one of 3 Modes to make your URLs pretty and SEO-friendly. - The everywhere compatible `?lang=en`, simple and beautiful `/en/foo/` or nice and neat `en.yoursite.com`
- One language for each URL - Users and SEO will thank you for not mixing multilingual content

qTranslate supports infinite languages, which can be easily added/modified/deleted via the comfortable Configuration Page.
All you need to do is activate the plugin and start writing the content!

== Installation ==

Installation of this plugin is fairly easy:

1. Download the plugin from [here](http://downloads.wordpress.org/plugin/qtranslate-xp.2.6.2.zip "qTranslate Plus").
1. Extract all the files. 
1. Upload everything (keeping the directory structure) to the `/wp-content/plugins/` directory.
1. There should be a `/wp-content/plugins/qtranslate-xp` directory now with `ppqtranslate.php` in it.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add the qTranslate Widget to let your visitors switch the language.

== Frequently Asked Questions ==

**KNOWN ISSUES**:
- All functions that starts with `qtranslate_` or `qtrans_` 
have been renamed to `ppqtranslate_` and `ppqtrans_` because of Wordpress plugins policy...
if you have "themes" or "plugins" that works with the original qTranslate you should rename all functions.

-- **Original FAQ** --
The FAQ is available at the [Plugin Homepage](http://www.qianqin.de/qtranslate/)
For Problems visits the [WordPress Support Forum](http://wordpress.org/support/forum/plugins-and-hacks)

== Screenshots ==

1. Wordpress 4.0 Editor with working qTranslate Plus 1
2. Wordpress 4.0 Editor with working qTranslate Plus 2
3. qTranslate Services (Translation)
