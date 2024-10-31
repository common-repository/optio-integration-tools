=== Optio Integration Tools ===
Contributors: rvencu
Donate link: 
Tags: optio, dentistry, orthodontics, lightbox, kids, cosmetics
Requires at least: 3.1
Tested up to: 4.7
Stable tag: 0.5
License: GPLv2

This plugin integrates Option Publishing videos in your dentistry site by means of shortcodes.

== Description ==

This plugin integrates Option Publishing videos in your dentistry site by means of shortcodes. The following shortcodes are available to use:

1. complete library [optio type="library" scope="all"]
1. partial library (useful for partial subscriptions) [optio type="library" scope="dentistry"], [optio type="library" scope="orthodontics"], [optio type="library" scope="cosmetics"], [optio type="library" scope="kids"]
1. lightbox single video [optio type="single" scope="dentistry/missing_tooth/implant_fixed"]
1. option to use widget instead or in combination with shortcodes. Useful when multiple videos are related to the current post or page
1. instructions and assistance for creation of Optio videos tab on your Facebook page (the tab will run as iframe with canvas from your website)

== Installation ==

1. Upload `optio-integration-tools.zip` to the `/wp-content/plugins/` directory
1. Unzip the archive
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Fill up the required option settings

== Frequently Asked Questions ==

= How do I use this plugin? =

First you need to have an active subscription for Optio Publishing services. You can get one here: http://www.optiopublishing.com/dental-videos

This plugin automatically loads all javascript and css files needed to perform the integration of Optio media library in your dentistry website. 
It generates automatically a "video of the day" that is rotated every day inside the Optio Publishing Movies widget in case the current post has no related videos
defined. The definition of the related videos is made via a custom field attached to the respective post, page or custom post type.

This plugin offers multiple ways of posts - videos association management. In the Edit Post screen it introduces a specific metabox that allows for videos browsing
and addition/removal tools for the current post. Also a checkbox offers the control to cancel the usage of the association of the videos for that particular post without deleting the
association itself, this being useful for temporary suspension of the function.

Another way to manage the association of videos with posts, pages or custom post types is done via Quick Edit screens where both Quick Edit and Bulk Edit modes can be used. Functionality
is similar to one presented above.

The Optio Publishing Movies widget can be inserted into a sidebar/widgetized area. At the admin interface the plugin can be activated in various contexts, besides the single post/pages/custom post types. 
Therefore the Optio Publishing Movies widget can function in homepage, archive pages, search pages, tag pages, category pages, author pages, etc. In case some context is disabled the widget can be automatically taken out
of view. This is done by using the Widget Logic plugin (http://wordpress.org/extend/plugins/widget-logic/) and by manual insertion of this logic statement for Optio Publishing Movies widget:
`global $optiodisplay; return $optiodisplay;`

In single pages the Optio Publishing Movies widget will render the video of the day in case there is no association defined for the current post. If there is an association defined, the associated video thumbnails 
will be rendered in the original order of the video catalog/library. This will keep proper order of videos such as in the concept of Introduction -> Problem -> Solution.

The usage of shortcodes is quite trivial, they will be rendered in the place of usage as described in the "Plugin Description" section.

== Screenshots ==

1. Administration interface
1. Edit Post metabox interface
1. Library view modal dialog
1. Quick Edit mode interface
1. Bulk Edit mode interface
1. Optio Publishing Movies Widget administration interface

== Known Issues ==

1. tba

== Changelog ==

= 0.1 =
Incipient version

= 0.2 =
Fixed bug with SSL pages when one script from Optio website was loaded without SSL

= 0.3 =
Fixed some bugs with widgets.
Added width and margin control to the widget. This way if there are more than one video to be shown they will be displayed in pairs with smaller
icons. Please adjust margin low enough so the pair videos will be displayed side by side.

= 0.4 =
Updated niroModal library to fix compatibility with latest jQuery version

= 0.5 =
Code brushed, tested to 4.7

== Upgrade Notice ==

Nothing here yet

== To Do List ==

1. Add "Do not use Optio on this post" checkbox in Quick Edit and Bulk Edit modes.
1. Add filtering and pagination to the videos library (admin mode)
1. Add proper comments to the whole plugin code
1. Add possibility to change the default description for individual videos
1. Add gallery shortcode to display the associated movies when they are too many to display in sidebar.