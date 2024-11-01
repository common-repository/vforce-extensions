<?php 
/*
*   vforce_create_input_element('Label to be added before input', 'Placeholder text', 'Text or Checkbox', 'variable, script, cache')
*
*/

function vforce_create_input_element($label, $placeholder, $inputType, $id, $optionType) {
    global $vforce_options;
    $vforce_options = get_option('vforce_helper');
    $value = '';
    if ($vforce_options) 
    {
        if (array_key_exists($optionType, $vforce_options) && isset($vforce_options[$optionType][$id])) 
        {
            $value = $vforce_options[$optionType][$id];
        }
    }
?>
<div class="form-group-row">
    <h2 class="form-input-label"><?php echo $label; ?></h2>
    <?php if($inputType == 'checkbox') {
                echo '<label class="switch">';
                } ?>
    <input type="<?php echo $inputType; ?>" class="form-control" placeholder="<?php echo esc_html($placeholder); ?>" onchange="updateVforceSetting('<?php echo esc_attr($id); ?>', <?php if($inputType == 'checkbox'){
                     echo 'this.checked';
                 } else {
                     echo 'this.value';
                 } ?>, '<?php echo $optionType;?>')" value="<?php echo esc_html($value);?>" <?php checked(esc_attr($value), 'true'); ?>>
    <?php if($inputType == 'checkbox') {
                    echo '<span class="slider round"></span>';
                    } ?>
    <?php
            if ($inputType == 'text') {
                echo '<a href="#" class="btn btn-primary">Save</a>';
            }
            ?>

</div>
<?php }

    
    function vforce_custom_meta_box_markup($object)
{
    
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
   
    ?>
        <div>
            <label for="association_id_override">Assoc ID Override</label>
            <input name="association_id_override" type="text" value="<?php echo esc_html(get_post_meta($object->ID, "association_id_override", true)); ?>">
            <br>
            <small>Enter an association id here to override the site wide association id for this page only</small>

            <br>
            <br>
                <input id="show_formassembly_checkbox" type="checkbox" name="show_form_assembly_settings" value="true" <?php checked(get_post_meta($object->ID, "show_form_assembly_settings", true), 'true') ?>>
<label for="show_formassembly_checkbox">Show Formassembly Settings</label>
<!--
            <label for="form_assembly_form_id">Formassembly Form Id</label>
            <input name="form_assembly_form_id" type="text" value="<?php echo esc_html(get_post_meta($object->ID, "form_assembly_form_id", true)); ?>">
-->

        </div>
    <?php  }

// Add a wysiwg editor to allow for overriding the review header in a form assembly form.
function vforce_formassembly_meta_box_markup($object)
{
    ?>
                <label for="form_assembly_hidden_input_id">Assoc ID Hidden Input ID</label>
            <input name="form_assembly_hidden_input_id" type="text" value="<?php echo esc_html(get_post_meta($object->ID, "form_assembly_hidden_input_id", true)); ?>">
            <br>
            <small>Enter the selector that identifies the hidden input that you want to hold the association id.  ie. <i>#tfa_34</i></small>    
            <hr class="my-2">

            <?php
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    $text= get_post_meta($object->ID, "vforce_formassembly_review_header", true);
    $editor_id = 'vforce_formassembly_review_header';
    $settings = array( 
        "editor_height" => 100
    );
    echo "<h1>Customize form review header</h1><p>This is the text that will show up at the top of the page when a user is reviewing their submission.  Leave blank to use the default text provided by Formassembly, or enter code below to over write.  Whatever is entered below will replace the contents of the <pre><code>&ltdiv class='reviewHeader'></code></pre> element";
    wp_editor( $text, $editor_id );

    ?>

    <?php  }

    function vforce_add_custom_meta_box()
    {
        add_meta_box("demo-meta-box", "VForce Settings", "vforce_custom_meta_box_markup", null, "side", "high", null);
        if (get_post_meta(get_the_ID(), "show_form_assembly_settings", true) === "true") 
        {
            add_meta_box("formassembly-form-settings-meta-box", "Formassembly Form settings", "vforce_formassembly_meta_box_markup", null, "normal", "high", null);

        }
    }
    
    add_action("add_meta_boxes", "vforce_add_custom_meta_box");


    function vforce_save_custom_meta_box($post_id, $post, $update)
{
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    if(!current_user_can("edit_post", $post_id))
        return $post_id;

    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;


    $meta_box_text_value = "";
    $vforce_form_assembly_hidden_id = "";
    $meta_box_checkbox_value = "";
    $vforce_show_formassembly_settings = "false";

    if(isset($_POST["association_id_override"]))
    {
        $meta_box_text_value = sanitize_text_field( $_POST["association_id_override"] );
    }   
    update_post_meta($post_id, "association_id_override", $meta_box_text_value);

    if(isset($_POST["form_assembly_hidden_input_id"]))
    {
        $vforce_form_assembly_hidden_id = sanitize_text_field($_POST["form_assembly_hidden_input_id"]);
    }   
    update_post_meta($post_id, "form_assembly_hidden_input_id", $vforce_form_assembly_hidden_id);

    if(isset($_POST["form_assembly_form_id"]))
    {
        $vforce_form_assembly_hidden_id = sanitize_text_field($_POST["form_assembly_form_id"]);
    }   
    if(isset($_POST["show_form_assembly_settings"]))
    {
        $vforce_show_formassembly_settings = $_POST["show_form_assembly_settings"];
    }
  
    update_post_meta($post_id, "show_form_assembly_settings", $vforce_show_formassembly_settings);
    wp_redirect('/');

    // if(isset($_POST["vforce_formassembly_review_header"]))
    // {
    //     $vforce_form_assembly_hidden_id = sanitize_textarea_field($_POST["vforce_formassembly_review_header"]);
    // }   
    // update_post_meta($post_id, "vforce_formassembly_review_header", $vforce_form_assembly_hidden_id);
}

add_action("save_post", "vforce_save_custom_meta_box", 10, 3);

function save_wp_editor_fields(){
    global $post;
    $data = $_POST['vforce_formassembly_review_header'];
    // $data = wp_kses_data($data);
    update_post_meta($post->ID, 'vforce_formassembly_review_header', $data);
}
add_action( 'save_post', 'save_wp_editor_fields' );