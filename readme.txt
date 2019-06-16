=== Sendy Subscription Plus ===
Contributors: finalwebsites
Donate link: http://www.finalwebsites.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: sendy, subscription, subscribe form, ajax forms, email marketing, mailing list, forms, api, ajax, email form, shortcode, clicky, Google Analytics, tracking
Requires at least: 4.0
Tested up to: 4.8.1
Stable tag: 1.0.2.3

Increase the count of new subscribers for your blog or website by using Sendy and a professional subscription form.

== Description ==

Email marketing is still one of the best ways to drive traffic to your website. You can use this WordPress plugin to add a newsletter subscription form below your blog, right in your articles or on other places using the widget. The Ajax technology takes care about that visitor doesn't have to leave your website while the form data gets submitted. The idea for this plugin came up because I created a new mailing list for my website.

= Check the features: =

* Add the subscription form to any page or post by using a shortcode or just include for all blog posts
* Add the form into your blog's sidebar using the widget
* Double opt-in is supported
* Using nonces for simple form value validation
* The visitor stays on your website while submitting the form data
* You can change/translate all plugin text by using a localization tool
* The form HTML is compatible with the Bootstrap CSS framework (v3)
* Optional: use the CSS style-sheet included with the plugin
* Track succesfully submitted forms in Google Analytics and Clicky
* The plugin includes JS and CSS files only if the form is present (there is also an option to include these files sitewide)





== Installation ==

The quickest method for installing the Sendy subscription form is:

1. Automatically install using the built-in WordPress Plugin installer or...
1. Upload the entire `mailchimp-subscription-plus` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Enter your Sendy API key, the mailing list ID and the other options on the plugin settings page.
1. Add the shortcode [FWSSendySubForm] into the page of your choice or enable the form for all blog posts.

== Frequently Asked Questions ==

= The subscription form doesn't work, the first name isn't passed to the MailChimp list =

Compare the merge fields you're using for the plugin settings and for your MailChimp mailing list. They need to be identical, use only the characters between the pipe symbols (|), check the [MC manual](http://kb.mailchimp.com/lists/managing-subscribers/manage-list-and-signup-form-fields) for information how to add additional merge fields

= How to add a manual goal in Clicky? =

If you use a Clicky premium plan it's possible to track Goals.

1. In Clicky, visit: Goals > Setup > Create a new goal.
1. Enter a name for the goal
1. Check the "Manual Goal" checkbox and click Submit
1. Copy/paste the ID into the corresponding field on the plugin options page

= I get an fatal error during plugin activation =

The Mailchimp wrapper class is written for PHP5 and doesn't work for old and unsafe PHP versions. The error is related to the namespace declaration at the top of the class script. To solve that error, you need to move to a better webhost which supports PHP5.3x or higher.

== Screenshots ==
1. Settings for the *MailChimp Subscription Plus* plugin.
2. An example how the subscription form looks like.
3. Subscription form widget (Made together with the theme called "The Bootstrap")

== Changelog ==

= 1.0 =
* Initial release
