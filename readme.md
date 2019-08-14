# Subscription tools for Sendy

Increase the count of new subscribers for your blog or website by using Sendy and a professional subscription form.

## Description

Email marketing is still one of the best ways to drive traffic to your website. You can use this WordPress plugin to add a newsletter subscription form below your blog, right in your articles or on other places using the widget or shortcode. The Ajax technology takes care about that visitor doesn't have to leave your website while the form data gets submitted.

*To use this plugin, you need access to a Sendy application. You can host the application by your self or maybe you can het an account from someone else.*

### These are the features:

* Add the subscription form to any page or post by using a shortcode or just include the form after every blog post
* Add the form into your blog's sidebar using the widget
* Use the MailMunch connector for a better integration between MailMunch and Sendy
* Using nonces for simple form value validation
* The visitor stays on your website while submitting the form data
* You can change/translate all plugin text by using a localization tool (Loco Translate is our favorite)
* The form HTML is compatible with the Bootstrap CSS framework (v3)
* Optional: use the CSS style-sheet included with the plugin
* Track succesfully submitted forms in Google Analytics and Clicky
* The plugin includes JS and CSS files only if the form is present (there are different options to include them only on those page where the code is necessary)
* Unsubscribe page on your own website
* Show the number of subscribers using a shortcode

## Installation

The quickest method for installing the plugin is:

1. Automatically install using the built-in WordPress Plugin installer or...
1. Upload the entire `sendy-subscriptions` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Enter your Sendy API key, the mailing list ID and the other options on the plugin settings page.

## Frequently Asked Questions

### The subscription form doesn't work, the secondary name isn't passed to the mailing list

Compare the merge field names you're using for the plugin settings and for your Sendy mailing list. They need to be identical, use only the characters from the  "Personalization tag"

### How to add a manual goal in Clicky?

If you use a Clicky premium plan it's possible to track Goals.

1. In Clicky, visit: Goals > Setup > Create a new goal.
1. Enter a name for the goal
1. Check the "Manual Goal" checkbox and click Submit
1. Copy/paste the ID into the corresponding field on the plugin options page
