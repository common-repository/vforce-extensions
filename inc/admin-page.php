<?php
function vforce_create_settings_page () {

    $wp_editor_settings =   array(
      'wpautop' => true, //Whether to use wpautop for adding in paragraphs. Note that the paragraphs are added automatically when wpautop is false.
      'media_buttons' => true, //Whether to display media insert/upload buttons
      'textarea_name' => 'editFormAssemblyBallot', // The name assigned to the generated textarea and passed parameter when the form is submitted.
      'textarea_rows' => get_option('default_post_edit_rows', 10), // The number of rows to display for the textarea
      'tabindex' => '', //The tabindex value used for the form field
      'editor_css' => '', // Additional CSS styling applied for both visual and HTML editors buttons, needs to include <style> tags, can use "scoped"
      'editor_class' => '', // Any extra CSS Classes to append to the Editor textarea
      'teeny' => false, // Whether to output the minimal editor configuration used in PressThis
      'dfw' => false, // Whether to replace the default fullscreen editor with DFW (needs specific DOM elements and CSS)
      'tinymce' => true, // Load TinyMCE, can be used to pass settings directly to TinyMCE using an array
      'quicktags' => true, // Load Quicktags, can be used to pass settings directly to Quicktags using an array. Set to false to remove your editor's Visual and Text tabs.
      'drag_drop_upload' => true //Enable Drag & Drop Upload Support (since WordPress 3.9) 
  );
      
  // check that the user has the required capability 
      if (!current_user_can('manage_options'))
      {
        wp_die( __('You do not have sufficient privileges to access this page. Sorry!') );
      }	
      
      ///////////////////////////////////////
      // MAIN AMDIN CONTENT SECTION
      ///////////////////////////////////////
      
    // display heading with icon WP style
    $defaultBallotMessage = '<h2>This ballot has been closed. Thank you for your response.</h2>';
?>

<!-- TAB CONTROLLERS -->
<input id="panel-1-ctrl" class="panel-radios" type="radio" name="tab-radios" checked>
<input id="panel-2-ctrl" class="panel-radios" type="radio" name="tab-radios">
<input id="panel-3-ctrl" class="panel-radios" type="radio" name="tab-radios">
<input id="panel-4-ctrl" class="panel-radios" type="radio" name="tab-radios">
<input id="panel-5-ctrl" class="panel-radios" type="radio" name="tab-radios">
<input id="nav-ctrl" class="panel-radios" type="checkbox" name="nav-checkbox">

<header id="introduction">
    <h1 class="admin-header">VForce Tools and Extensions</h1>
</header>

<!-- TABS LIST -->
<ul id="tabs-list">
    <!-- MENU TOGGLE -->
    <label id="open-nav-label" for="nav-ctrl"></label>
    <li id="li-for-panel-1">
        <label class="panel-label" for="panel-1-ctrl">Global Settings</label>
    </li>
    <!--INLINE-BLOCK FIX
   -->
    <li id="li-for-panel-2">
        <label class="panel-label" for="panel-2-ctrl">Formidable Settings</label>
    </li>
    <!--INLINE-BLOCK FIX
   -->
    <li id="li-for-panel-3">
        <label class="panel-label" for="panel-3-ctrl">Formassembly Settings</label>
    </li>
    <label id="close-nav-label" for="nav-ctrl">Close</label>
    <div class="toast toast-success" style="display:none">
        <div class="toast-status"></div>
        <div class="toast-close">X</div>
    </div>
</ul>

<!-- THE PANELS -->
<article id="panels">
    <div class="container">
        <section id="panel-1">
            <main>
                <form method="post" action="options.php">
                    
                <h1>Association Settings</h1>
                <hr class="mb-3">
                <?php vforce_create_input_element('Association Id', '', 'text', 'association-id', 'variable') ;?>


            </main>
        </section>
        <section id="panel-2">
            <main>
                <h1>General Settings</h1>
                <hr class="mb-3">
                <?php vforce_create_input_element('Enable Add To Cart Script', '', 'checkbox', 'formidable_add-to-cart', 'script') ;?>
                <div class="formidable-product-selector-wrapper">
                    <?php vforce_create_input_element('Product Selector', '', 'text', 'formidable_product-selector', 'variable') ;?>
                </div>
            </main>
        </section>
        <section id="panel-3">
            <main>
            <?php vforce_create_input_element('Server Url', '', 'text', 'formassembly-server-url', 'variable') ;?>
                </div>
            </main>
        </section>

    </div>

</article>

<?php
  }
?>