=== USC Screenings ===
Contributors: pcraig3
Donate link: ---
Tags: usc
Requires at least: 3.6
Tested up to: 4.0
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin creates a new 'Screenings' Custom Post Type -- expects the Admin Page Framework to be included.

== Description ==

Plugin creates a new 'Screenings' Custom Post Type -- expects the Admin Page Framework to be included.
Screenings, if it's ambiguous, are meant to be used for Western Film.

List of features we want:

*   Create a Screening in the backend
*   Screening populates a list of screenings on the home page
*   Define a custom template for a screening

== Frequently Asked Questions ==

= Show me a movie. =

Sure.  http://westernusc.org/westernfilm/

== Changelog ==

= 1.0.3 =
* Fixed where alerts are displayed, again

= 1.0.2 =
* Changed where alerts are displayed
* Improved the way dates are displayed

= 1.0.1 =
* Improved the error message returned by shortcode if an appropriate status is not defined
* Minor improvement to a description in the Edit USC Screening admin page
* Removed CSS rules governing the 'back to Western Film' button as they were already in child theme.

= 1.0.0 =
* Ready enough for launch!
* Commented pretty well everything pretty comprehensively.
* Small CSS change to specify home screen tabs rather than all tabs.
* Small CSS change expands CSS media queries to to match Divi's (i.e., target 480px page widths instead of 479px, etc.).
* Changed the names of the timezone-setting methods to match other USC_{x} plugins
* Set initial timezone in the construct method
* Template file in the theme now (although this isn't really a change made to the plugin)

= 0.9.0 =
* Yes!  Single page in both mobile and desktop versions working.
* Shortcode listing working for both mobile and desktop as well.
* Most if not all of the empty strings/arrays checked for.
* Fields that James asked for working great.

= 0.8.1 =
* Bloody finally got my single movies page worked out

= 0.8.0 =
* Redid all the front-age screenings.
* Made them mobile-responsive as well.
* Added a few extra fields James wanted
* Isolated all of the code for the usc_screening shortcode
* Working on the single-page view for Screenings.

= 0.7.0 =
* Not to be cocky, but I think I've nearly wrapped this one up.
* USC Screenings Post Type exists.
* Statuses exist.
* Perhaps most importantly, the shortcode for only pulling specific Posts is working.
* Lots of HTML + styling

*** Still have to meet James and see what else he wants
*** Still need a single Screening template
*** Still need mobile responsive Screenings

= 0.3.1 =
* Changed (nearly) all instances of 'Job' and 'Department'.  Here goes.

= 0.3.0 =
* USC Screenings being hacked together based heavily on our prior experience with USC Jobs.

== Updates ==

The basic structure of this plugin was cloned from the [WordPress-Plugin-Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) project.
This plugin supports the [GitHub Updater](https://github.com/afragen/github-updater) plugin, so if you install that, this plugin becomes automatically updateable direct from GitHub. Any submission to WP.org repo will make this redundant.
