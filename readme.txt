=== Pigeon Pack ===
Contributors: layotte
Tags: email, campaign, newsletter, email marketing, widget, email newsletter, email widget, newsletter widget, widget
Requires at least: 3.4
Tested up to: 4.1 
Stable tag: 1.0.10

Free and easy email marketing, newsletters, and campaigns; built into your WordPress dashboard!

== Description ==

The Pigeon Pack plugin is completely free! This plugin aims to solve one problem, putting the power of email marketing, newsletters, and campaigns in the hands of YOU. Now you do not need to pay for a third party like MailChimp or Constant Contact to reach out to your customers. You have full control and power over your own lists and you can bring them anywhere at any time. The Pigeon Pack plugin is GPL and free to use on any WordPress website. 

Features of this plugin include:

* Single Email Campaigns
* WordPress Post Campaigns (single or digest)
* Email a specific role or create a list
* Easy to use shortcodes and widgets for users to sign up for your emails from your website
* Create custom fields for your lists
* Double Opt-in available for lists
* Easily extendable with filters and action hooks

You can follow the development of this plugin at [GitHub](https://github.com/PigeonPack/PigeonPack)!

Premium Pigeon Pack Add-ons (COMING SOON):

* Analytics - Learn more about what your subscribers are doing!
* Autoresponders (Events) - Send a series of messages to new subscribers.
* RSS Campaigns - Import the RSS feed from another site to send to your subscribers.
* Import Scripts - Import subscribers from other lists using CSV, MailChimp, Salesforce, Highrise, Constant Contact and more!
* Bounce Detector - Use the power of the Pigeon Pack servers to detect when a subscriber's email is bounced to help reduce the chances of your server being flagged as SPAM.
* Responsive Email Template - No more plain-jane emails!
* Notifications - Receive notifications whenever someone subscribers or unsubscribers from your list.
* Amazon SES - Built-in functionality to work directly with the Amazon Simple Email Server service.

Please visit the Pigeon Pack website for access to [support and premium membership add-ons](http://pigeonpack.com/)!

== Installation ==

1. Upload the entire `pigeonpack` folder to your `/wp-content/plugins/` folder.
1. Go to the 'Plugins' page in the menu and activate the plugin.

== Frequently Asked Questions ==

= SPAM Laws =

Pigeon Pack enables you to own and operate your own email campaign manager. You have full control and ownership over your email lists, campaigns, autoresponders, and more. Due to this, you are also required to follow the SPAM laws, guidelines and recommendations for your country. The plugin is setup to meet compliance with current laws, however, you have the responsibility to know the laws and make sure you are using the plugin appropriately. For more information about the SPAM laws in your country, see the list below or google "SPAM LAWS" for your country.

= Email Sending Limits =

Every web host and SMTP provider has limits on the numbers of messages that can be sent from their systems. Please check with your web host or SMTP provider to verify their email limit policy. This is important to ensure you setup the plugin properly to prevent your customers from missing emails. If you anticipate sending large amount of email campaigns, there are services out there that you can use with Pigeon Pack, such as Amazon's Simple Email Service (SES). Amazon SES lets you send bulk and transactional email to customers in a quick and cost-effective manner.

= What are the minimum requirements for Pigeon pack? =

You must have:

* WordPress 3.4 or later
* PHP 5

= How is Pigeon pack Licensed? =

* Pigeon Pack is GPL

== Changelog ==
= 1.0.10 =
* Fixing typo in SMTP Authentication setting

= 1.0.9 =
* Adding ability to use the Post's Featured Image in a campaign

= 1.0.8 =
* Adding español language files, thanks to Andrew from [Web Hosting Hub](http://www.webhostinghub.com/)

= 1.0.7 =
* Adding i18n support

= 1.0.6 =
* Moving actions
* Fixed bug with multiple publishing

= 1.0.5 =
* Updated Pigeon mascot

= 1.0.4 =
* Setup better multi-part MIME support
* Fixed typo in campaign save
* Adding proper HTML error codes to wp_die statements
* Fixed error in calling static method in shortcodes

= 1.0.3 =
* General typo and code fixes
* Fixed update return variable
* Fixed bug in adding new subscribers AJAX
* Updating Pigeon Pack icon and Special Thanks
* Redux of the hash functions
* Change priority of script enqueues and default to in_footer when possible
* Fixing tag lines
* Undefined variable error in widgets
* Updating Help Page and CSS, fixing misnamed functions
* Updated {{MERGE}} tag helpers
* Removing unused code
* More robust post status transition checking
* Do not add trash => publish posts to digest, but added an action hook to override this
* Check against false in array_search calls
* Use pigeonpack default settigns for footer info if none set in list
* Renamed all functions in class.php to remove pigeonpack_ (not needed)

= 1.0.2 =
* Remove premium nag

= 1.0.1 =
* Added comment for future note on dealing with previewing campaigns
* Some code format updates
* Adding rewrite rule flush check

= 1.0.0 =
* Initial Release

= 0.0.1 =
* Beta Release

== License ==

Pigeon Pack
Copyright (C) 2013 leenk.me, LLC.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.
