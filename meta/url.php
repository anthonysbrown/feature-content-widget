<?php

function fcw_url_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function fcw_url_add_meta_box() {
	add_meta_box(
		'url-url',
		__( 'Treatment Page', 'fcw' ),
		'fcw_url_html',
		'treatment',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'fcw_url_add_meta_box' );

function fcw_url_html( $post) {
	wp_nonce_field( '_fcw_url_nonce', 'fcw_url_nonce' ); ?>

	<p>
		<label for="fcw_url_treatment_url">Choose a treatment page to link</label><br>
        
        <?php
		
		 $args = array(
    'depth'                 => 0,
   
    'selected'              => fcw_url_get_meta( 'fcw_url_treatment_url' ),
    'echo'                  => 1,
    'name'                  => 'fcw_url_treatment_url',
    'id'                    => 'fcw_url_treatment_url', // string
  
);

 wp_dropdown_pages( $args );
 
 ?>
	
	</p><?php
}

function fcw_url_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['fcw_url_nonce'] ) || ! wp_verify_nonce( $_POST['fcw_url_nonce'], '_fcw_url_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['fcw_url_treatment_url'] ) )
		update_post_meta( $post_id, 'fcw_url_treatment_url', esc_attr( $_POST['fcw_url_treatment_url'] ) );
}
add_action( 'save_post', 'fcw_url_save' );

/*
	Usage: fcw_url_get_meta( 'fcw_url_treatment_url' )
*/
