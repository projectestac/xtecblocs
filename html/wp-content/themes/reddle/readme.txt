== Changelog ==

= 1.3.8 =
* Update reddle with some more recent theme standards

= 1.3.7 =
* Update default widget population after theme switch

= 1.3.6 =
* Add Requires at least to all style.css files (#156730)

= 1.3.5 - Oct 26 2015 =
* Add responsive menu

= 1.3.4 - Sep 25 2015 =
* Move infinite scroll code from plugin to theme to fix self-hosted errors

= 1.3.3 - Jun 16 2015 =
* Minor bug fixes
* Hook widgets function into widgets_init rather than init
* Escaping fixes

= 1.3 - Jul 22 2013 =
* Updated markup and template logic.
* Moved away from using deprecated functions and improve compliance with .org theme review guidelines.
* Removed auto height from embeds, objects, and iframes; it was causing videos to shrink in height.
* Updated license.

= 1.2 - Apr 2 2013 =
* Moved content width adjustment in a callback and extended use cases.
* Updated screenshot.
* Removed theme support for wp.com print styles.
* Updated package declaration in searchform.php.

= 1.1 - Dec 28 2012 =
* Fixed scaled image bug in Theme Unit Test.
* Extended overflow rules to all widget areas.
* Fixed deprecated function and undefined variable/constant notices.
* Removed duplicate post date. Also fixed color of edit link on image post format.
* Made the post edit link less conspicuous -- treat it like tags and categories and include it in the post instead of as a circle to the left of the post.
* Ensured the permalink is viewable to users whether logged in or logged out. Also ensured there is always a permalink available in the date by linking the date and styling it to look like regular entry meta information.
* Removed loading of $locale.php.
* Made sure attribute escaping occurs after printing.
* Added styling for HTML5 email inputs.

= 1.0 =
* Initial release.
