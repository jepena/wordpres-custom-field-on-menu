
/**
* Add custom fields to menu item
*
*/
<?php
function je_custom_field_menu($item_id, $item) {
    wp_nonce_field( 'unice_text_field_nonce', '_unice_text_field_nonce_name' );
    $unice_text_field = get_post_meta($item_id, '_unice_text_field', true);
?>
    <input type="hidden" name="unice_text_field_nonce" value="<?php echo wp_create_nonce( 'custom-text-field-meta-name' ); ?>" />
	<p class="unice-text_field description description-wide">
		<label for="unice_text_field-<?php echo $item_id; ?>" >
	    <span class="description"><?php _e( "Custom text under the menu item", 'unice_text_field' ); ?></span>
	        <input type="hidden" class="nav-menu-id" value="<?php echo $item_id ;?>" />
			<input type="text" 
				id="unice_text_field-<?php echo $item_id; ?>"
				class="widefat"
				name="unice_text_field[<?php echo $item_id; ?>]" 
				value="<?php echo esc_attr($unice_text_field); ?>" 
			/>
		</label>
	</p>
<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'je_custom_field_menu', 10, 2 );

/**
* Save the menu item meta
* 
*/
function je_custom_nav_update( $menu_id, $menu_item_db_id ) {
	// Verify this came from our screen and with proper authorization.
	if ( ! isset( $_POST['_unice_text_field_nonce_name'] ) || ! wp_verify_nonce( $_POST['_unice_text_field_nonce_name'], 'unice_text_field_nonce' ) ) {
		return $menu_id;
	}

	if ( isset( $_POST['unice_text_field'][$menu_item_db_id]  ) ) {
		$sanitized_data = sanitize_text_field( $_POST['unice_text_field'][$menu_item_db_id] );
		update_post_meta( $menu_item_db_id, '_unice_text_field', $sanitized_data );
	} else {
		delete_post_meta( $menu_item_db_id, '_unice_text_field' );
	}
	
}
add_action( 'wp_update_nav_menu_item', 'je_custom_nav_update', 10, 2 );

/**
* Displays text on the front-end.
*
*/
function je_custom_menu_text_field( $text_field, $item ) {

	if( is_object( $item ) && isset( $item->ID ) ) {

		$custom_menu_meta = get_post_meta( $item->ID, '_unice_text_field', true );

		if ( ! empty( $custom_menu_meta ) ) {
			$text_field .= '<span>' . $custom_menu_meta . '</span>';
		}
	}
	return $text_field;
}
add_filter( 'walker_nav_menu_start_el', 'je_custom_menu_text_field', 10, 2 );

/**
* Displays text on the front-end after the menu title .
*
*/
function je_custom_text($item) {
    //Use the following to conduct logic;
    $object_id = (int) $item->object_id; //object ID.
    $object_type = $item->type; //E.g. 'post_type'
    $object_type_label = $item->type_label; //E.g. 'post' or 'page';

    //You could, optionally add classes to the menu item.
    $item_class = $item->classes;

    //Make sure $item_class is an array.
    //Alter the class:
    $item->classes = $item_class;

    //Alter the title:
    $custom_menu_meta = get_post_meta( $item->ID, '_unice_text_field', true );
    if( is_object( $item ) && isset( $item->ID ) ) {
        
        if(! empty( $custom_menu_meta) ){
            $item->title = $item->title.'<br><span>' . $custom_menu_meta . '</span>'; 
        }
    }

    return $item;
}
add_filter( 'wp_setup_nav_menu_item','je_custom_text', 10, 2 );
