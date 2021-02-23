
/**
* Add custom fields to menu item
*
*/
function wp_cf_navMenu($item_id, $item) {
    wp_nonce_field( 'unice_text_field_nonce', '_unice_text_field_nonce_name' );
    $unice_text_field = get_post_meta($item_id, '_unice_text_field', true);
?>
    <input type="hidden" name="unice_text_field_nonce" value="<?php echo wp_create_nonce( 'custom-text-field-meta-name' ); ?>" />
	<p class="unice-text_field description description-wide">
		<label for="unice_text_field-<?php echo $item_id; ?>" >
		    custom text under the menu <br>
	        <input type="hidden" class="nav-menu-id" value="<?php echo $item_id ;?>" />
			<input type="text" 
				id="unice_text_field-<?php echo $item_id; ?>"
				class="widefat"
				name="unice_text_field[<?php echo $item_id; ?>]" 
				value="<?php esc_attr($unice_text_field); ?>" 
			/>
		</label>
	</p>
<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'wp_cf_navMenu', 10, 2 );

/**
* Save the menu item meta
* 
*/
function custom_nav_update( $menu_id, $menu_item_db_id ) {
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
add_action( 'wp_update_nav_menu_item', 'custom_nav_update', 10, 2 );

/**
* Displays text on the front-end.
*
*/
function unice_custom_menu_text_field( $text_field, $item ) {

	if( is_object( $item ) && isset( $item->ID ) ) {

		$custom_menu_meta = get_post_meta( $item->ID, '_unice_text_field', true );

		if ( ! empty( $custom_menu_meta ) ) {
			$text_field .= '<span>' . $custom_menu_meta . '</span>';
		}
	}
	return $text_field;
}
add_filter( 'walker_nav_menu_start_el', 'unice_custom_menu_text_field', 10, 2 );
