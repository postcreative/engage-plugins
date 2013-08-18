<?php

/*
|--------------------------------------------------------------------------
| donation_typess Taxonomy Custom Fields
|--------------------------------------------------------------------------
*/

/**
 * Edit Taxonomies Custom Fields page
 *
 * Add new fields to custom taxonomies edit page.
 *
 * @access      private
 * @since       1.0
 * @return      void
*/
function ewd_edit_taxonomies_donation_types_fields($tag) {

    $t_id      = ( isset($tag) ? $tag->term_id : '' );
    $term_meta = ( $t_id != '' ? get_option( "ewd_taxonomy_$t_id") : ''); 
    ?>
    <tr class="form-field">
    <th scope="row" valign="top"><label for="donation_types_target"><?php _e( 'Project Target', 'ewd' ); ?></label></th>
        <td>
            <input type="text" name="term_meta[project_target]" id="term_meta[project_target]" value="<?php echo esc_attr( $term_meta['project_target'] ) ? esc_attr( $term_meta['project_target'] ) : ''; ?>">
            <p class="description"><?php _e( 'Define the project target in your currency (ex: 5000).', 'ewd' ); ?></p>
        </td>
    </tr>
<?php
}
add_action( 'donation-types_edit_form_fields', 'ewd_edit_taxonomies_donation_types_fields', 10, 2 );

/**
 * Add Taxonomies Custom Fields
 *
 * Add new fields to custom taxonomies add page.
 *
 * @access      private
 * @since       1.0
 * @return      void
*/
function ewd_add_taxonomies_donation_types_fields() {

    ?>
    <div class="form-field">
        <label for="project_target"><?php _e( 'Project Target', 'ewd' ); ?></label>
        <input type="text" name="term_meta[project_target]" id="term_meta[project_target]" value="">
        <p class="description"><?php _e( 'Define the project target in your currency (ex: 5000).', 'ewd' ); ?></p>
    </div>
<?php
}
add_action( 'donation-types_add_form_fields', 'ewd_add_taxonomies_donation_types_fields', 10, 2 );

/**
 * Save Taxonomies Custom Fields
 *
 * Save custom taxonomies fields
 *
 * @access      private
 * @since       1.0
 * @return      void
*/
function ewd_save_taxonomies_donation_types_fields( $term_id ) {

    if ( isset( $_POST['term_meta'] ) ) {
    
        $t_id      = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
        $cat_keys  = array_keys( $_POST['term_meta'] );
        
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "ewd_taxonomy_$t_id", $term_meta );
    } // end if
}   
add_action( 'create_donation-types', 'ewd_save_taxonomies_donation_types_fields', 10, 2 );
add_action( 'edited_donation-types', 'ewd_save_taxonomies_donation_types_fields', 10, 2 );