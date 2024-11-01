=== Plugin Name ===
Contributors: Mike Conrad
Donate link: https://virtualinc.com
Tags: vforce, salesforce, formassembly, virtual
Requires at least: 3.0.1
Tested up to: 5.3
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Vforce helper tool.  To be used by any organization on the Vforce platform.

== Description ==

Easily manage Vforce integrations through Wordpress.  Currently supports voting forms and web to case forms through Form Assembly.  As well as adding products to a cart through Formidable.



## Changelog

### 1.0.3
* Cleaned up some styling.  Removed debug statements.
* Fixed issue with WPEngine quick links in admin bar not showing up.  
* Added VForce Settings quick link to admin bar.
* Added Association ID to VForce settings quick links.

### 1.0.2 
* Updated Formassembly integration.  Added the ability to customize the message show at the top of the page when a user is reviewing their form submission.  This can currently be accessed by checking the "Show Formassembly Settings" checkbox in the right sidebar while editing a post or page.  Note* currently you will need to reload the page to see the new Formassembly Form Settings section.  It will appear below your page/post.
* Fixed bug with Formidable forms not submitting.

### 1.0.1
* Added Formassembly integration.  Now you can simply insert the <code>[vforce_form formid='id']</code> shortcode into a page or post.  If you have issues with the form displaying, make sure that the id is in single qoutes.  The server url defaults to https://virtual.tfaforms.net.  You can change this under VForce Settings->Formassembly Settings.  This plugin will not override or interfere with the official Formassembly plugin.

### 1.0
* Removed Plugin update checker
* Submitted to Wordpress Plugins Repo
* Added ACF dependency to allow for adding an Association ID override metabox to all pages/posts.
* All pages/posts now have an Association ID override box.  By default this box is empty.  There is a hook that runs just before each page is loaded.  This function is in the main plugin file.  It checks to see if a value has been set in the override box.  If so, it will use the value in the override meta box as the global association id, if not it will use the default site wide Association ID/

```php
// This function runs just before the requested page is loaded
add_action('template_redirect', 'vforce_hook_before_page_load');
function vforce_hook_before_page_load(){
  $vforce_helper = get_option('vforce_helper');

    wp_enqueue_script( 'init-global', plugin_dir_url( __FILE__ ) . 'inc/js/init-global.js', array('jquery'), '1.0', true );
    /* 
     Setting the association ID variable
     Check to see if a value has been entered into the Association Id override metabox of the page.
     If so, inject that into our init-global.js as the associationId variable.  If it has not been set,
     we then use the value entered into the plugin settings.
     The code for the metabox is in functions.php
    */
     wp_localize_script( 'init-global', 'associationId', (get_post_meta( get_the_ID(), 'vforce-metabox-settings-association_id', true) != "") ? get_post_meta( get_the_ID(), 'vforce-metabox-settings-association_id', true) : $vforce_helper['variable']['association-id']); 
}
```

* vforce_helper is now a multidimensional array.
```javascript
vforce_helper.variable
{association-id: "a00f400000VQ8E6AAL"}
vforce_helper.script
{formidable_add-to-cart: "true", fa_web-to-case: "true"}
```
### 0.6
* Added ability to update plugin from Github releases

### 0.5
* Initial Release
* Includes basic functionality for web to case, handling voting ballots and adding items to cart through formidable.
* Basic readme included.


## Included Functions


### js/init-global.js

This file doesn't actually do anything.  It only exists to be passed into the 
```php 
wp_localize_script()
```
function along with an array of variables that we would like to be available to every page.

---


### js/formidable/add-to-cart.js 

This script allows you to create a formidable form with woocommerce products in a dropdown or radio buttons.  Once the user chooses a product and clicks next on the form, the product id is saved in their browser.  When they complete the form, an Ajax request is made to add the time to their cart.

---

### js/form-assembly/hide-ballot.js

 This script will show/hide a form based on the value of an input with title of Ballot Cast.  If that input is found on the page and its value is equal to Cast or cast, then the user will see a custom message instead of the voting form.  The message can be customized by going to the plugin settings (Vforce Extensions) and changing the Form Assembly Ballot Message textarea.  A default message is included.

In order for this to work properly, please make sure to wrap the form in a div with the class of ballot-container.

E.g.

```html
<div class="ballot-container d-none">[formassembly formid=50 server=https://virtual.tfaforms.net]</div>
```

This way the form will be hidden by default and will either appear or show the message when page loads.

---
### js/form-assembly/web-to-case.js ###
 Automatically adds the association id to a Form Assembly contact form embedded on any page.  The association id can be set in the plugin settings.


## Global Variables ##

Every page should have access to an object called vforce_helper.  This object contains all custom variables from the settings pane.

e.g.

```javascript
vforce_helper
{assocId: "", faProductSelector: "item_meta[6]", ballotCastMessage: ""}
```

This is accomplished by passing key/value pairs to the following function in the plugins functions.php file

```php
wp_localize_script( 'init-global', 'vforce_helper', array(
    'assocId' => get_option('association_id'),
    'faProductSelector' => get_option('form_assembly_product_selector'),
    'ballotCastMessage' => get_option('form_assembly_ballot_message')
)
```