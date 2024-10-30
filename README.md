# Inboxify Sign Up Form #
- Contributors: budgetmailer
- Tags: inboxify, newsletter, mailing list, sign up, newsletter sign up, sign up form, email marketing, subscribe widget
- Requires at least: 3.0.0
- Tested up to: 4.9.8
- Stable tag: 1.0.4
- License: MIT
- License URI: https://gitlab.com/inboxify/inboxify-sign-up-form/blob/master/license.txt

Easily add Inboxify newsletter sign up forms to your WordPress website with the offical Inboxify Sign Up Form plugin.

## Description ##
Inboxify is an online application for sending email newsletters. Many companies, mostly from the Netherlands, make use of Inboxify for sending newsletters to their customers and relations. At the moment, Inboxify is only available in Dutch.

With the Inboxify Sign Up Form plugin you can add sign up forms and checkboxes to your WordPress website to enable your visitors to sign up directly to your Inboxify list.

### Features ###

- Easy to use Sign Up Form Widget
- Add a sign up form to your posts and pages using the shortcode [inboxify_subscribe]
- Add a subscribe checkbox to your comment form, registration form or user profile form
- Prevent spam submissions using one of the supported CAPTCHA plugins
- Compatible with the "Really Simple CAPTCHA", "SI CAPTCHA Anti-Spam" and "WordPress ReCaptcha Integration" plugins
- AJAX form processing

## Installation ##
Before you can use the plugin you need to log in to your Inboxify account and go to "Mijn gegevens>Account". 
You can find your API key and save your secret key here. You need these keys to set up the plugin.

The rest of the installation is like any other WordPress plug-in:

1. Download plugin archive
2. Extract plugin archive to "wp-content/plugins/" directory (or upload it in WordPress admin)
3. Activate the plugin through the "Plugins" menu in WordPress administration
4. Go to WordPress administration "Settings>Inboxify Sign Up"
5. Fill in your API and secret key and click "Save Changes" to load your Inboxify lists
6. Select the list you wish to use with the Inboxify Sign Up Form
7. Configure "Advanced Settings" for the features you would like to use

## Frequently Asked Questions ##
### How do I use the shortcode? ###

The simplest use is to paste the shortcode "[inboxify_subscribe]" to any page or post.

The shortcode has a lot of optional arguments. Here is the full example:

`[inboxify_subscribe t="My form title" e="Email label" cl="Company Name Label" cd=1 cr=1 fnl="First Name label" fnd=1 fnr=1 mnl="Middle Name label" mnd=1 mnr=0 lnl="Last name label" lnd=1 lnr=1 sel="Sex Label" sed=1 ser=1 tel="Telephone Label" ted=1 ter=1 mol="Mobile Label" mod=1 mor=1 adl="Address Label" add=1 adr=1 zil="ZIP Label" zid=1 zir=1 cil="City Label" cid=1 cir=1 col="Country Label" cod=1 cor=1 sl="Submit Button Label" hb="Text before form" ha="Text after form" mi="Invalid field warning" mif="Invalid form warning" ms="Success message" me="Error message"]`

Meaning of all the attributes:

- t: Title
- e: Email field label
- cl: Company label
- cd: Company displayed
- cr: Company required
- fnl: First name field label
- fnd: First name displayed
- fnr: First name required
- mnl: Middle name field label
- mnd: Middle name displayed
- mnr: Middle name required
- lnl: Last name label
- lnd: Last name displayed
- lnr: Last name required
- sel: Sex field label
- sed: Sex displayed
- ser: Sex required
- tel: Telephone field label
- ted: Telephone displayed
- ter: Telephone required
- mol: Mobile field label
- mod: Mobile displayed
- mor: Mobile required
- adl: Address field label
- add: Address displayed
- adr: Address required
- cil: City field label
- cid: City displayed
- cir: City required
- col: Country field label
- cod: Country displayed
- cor: Country required
- sl: Submit button label
- ta: Automatic tags
- taa: Allowed tags
- tal: Tags label
- tad: Tags displayed
- tar: Tags required
- ta_in: Tags input type
- cdl: Custom date label
- cdd: Custom date displayed
- cdr: Custom date required
- hb: Text before form (HTML not supported)
- ha: Text after form (HTML not supported)
- mi: Invalid field error message
- mif: Invalid form error message
- ms: Sign-up successful message
- me: Sign-up failed Message

To configure the shortcode more easily, you can also use the widget (see "How do I create a shortcode with a widget?").

### How do I display the Widget? ###

- Go to WordPress administration "Appearance>Widgets" and find "Inboxify Sign Up Form Widget"
- Drag it to any widget container you want
- Configure the options in the Widget Settings Form

### How do I create a shortcode with a widget? ###

Because of all the attributes, using the shortcode can be quite complicated. We therefore made it possible to create a shortcode using the widget configuration.

If you create and configure any Inboxify widget, at the end of the form you will find a unique shortcode. With this shortcode, you can display the sign up form at every page or post.

If you don't want to display the widget itself, configure it in "Inactive Widgets" area.

## Screenshots ##

1. Shortcode and Widget in action
2. Admin settings
3. Widget settings

## Changelog ##

### 1.0.4 ###

- added option to add custom date and tags with various types of input
- fixed ajax form validation (not displaying invalid field message when not valid in backend)
- improved usability of back-end configuration (api checking, lists reloading)
- updated inboxify api php client to 1.0.3

### 1.0.3 ###
- added shortcode generator icon and form to the WYSIWYG editor
- added support for insible recaptcha plug-in
- fixed si captcha and wp recaptcha integration
- improved captcha handling
- updated translations

### 1.0.2 ###
- Synced `README.md`, `readme.txt`, and `inboxify-sign-up-form.php` (no code changes).

### 1.0.1 ###
- Changing API end-point, and other URLs from .eu to .nl

### 1.0.0 ###
- Initial plugin version
