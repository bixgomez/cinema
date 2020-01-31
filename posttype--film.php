<?php

add_action( 'init', 'film_cpt', 0 );

function film_cpt() {

  $labels = array(
    'name'                  => _x( 'Films', 'Post Type General Name', 'text_domain' ),
    'singular_name'         => _x( 'Film', 'Post Type Singular Name', 'text_domain' ),
    'all_items'             => __( 'All Films', 'text_domain' ),
    'add_new'               => __( 'Add New Film', 'text_domain' ),
    'add_new_item'          => __( 'Add New Film', 'text_domain' ),
    'edit_item'             => __( 'Edit Film', 'text_domain' ),
    'menu_name'             => __( 'Films', 'text_domain' ),
    'name_admin_bar'        => __( 'Film', 'text_domain' ),
    'archives'              => __( 'Film Archives', 'text_domain' ),
    'attributes'            => __( 'Film Attributes', 'text_domain' ),
    'parent_item_colon'     => __( 'Parent Film:', 'text_domain' ),
    'new_item'              => __( 'New Film', 'text_domain' ),
    'update_item'           => __( 'Update Film', 'text_domain' ),
    'view_item'             => __( 'View Film', 'text_domain' ),
    'search_items'          => __( 'Search Film', 'text_domain' ),
    'not_found'             => __( 'Film not found', 'text_domain' ),
    'not_found_in_trash'    => __( 'Film not found in Trash', 'text_domain' ),
    'featured_image'        => __( 'Featured Image', 'text_domain' ),
    'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
    'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
    'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
    'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this film', 'text_domain' ),
    'items_list'            => __( 'Films list', 'text_domain' ),
    'items_list_navigation' => __( 'Films list navigation', 'text_domain' ),
    'filter_items_list'     => __( 'Filter films list', 'text_domain' ),
  );

  $rewrite = array(
    'slug'                  => 'film',
    'with_front'            => true,
    'pages'                 => false,
    'feeds'                 => true,
  );

  $args = array(
    'label'                 => __( 'Film', 'text_domain' ),
    'description'           => __( 'Any film that you will screen at your cinema.', 'text_domain' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'custom-fields' ),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 20,
    'menu_icon'             => 'dashicons-editor-video',
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'rewrite'               => $rewrite,
    'capability_type'       => 'post',
  );

  register_post_type( 'film', $args );
}

/**
 * Save post metadata when a post is saved.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */

function save_film_stuff( $post_id ) {

  // Debugging.
  // echo '<div>';

  if ($_POST['post_type'] != 'film') {
    return;
  }

  $screenings = get_field('screenings', $post_id );

  if ($screenings) {
    $screenings_unix = array();

    foreach ($screenings as $key=>$thisScreening) {
      $thisScreeningDate = $thisScreening['screening_date'];
      $thisScreeningTime = $thisScreening['screening_time'];
      $thisScreeningDateTime = $thisScreeningDate . ' ' . $thisScreeningTime;
      $unixtime =  strtotime($thisScreeningDateTime);

      // Debugging.
      // echo '$thisScreeningDate: ' . $thisScreeningDate . '<br>';
      // echo '$thisScreeningTime: ' . $thisScreeningTime . '<br>';
      // echo '$thisScreeningDateTime: ' . $thisScreeningDateTime . '<br>';
      // echo '$unixtime: ' . $unixtime . '<br>';
      // echo date("m/j/Y g:i a", $unixtime) . '<br>';
      // echo $key . ' ' . $post_id . '<br>';

      // delete_sub_row( array('screenings', $key+1, 'screening_date'), 1 );
      // delete_sub_row( array('screenings', $key+1, 'screening_time'), 1 );

      array_push($screenings_unix, $unixtime);
    }

    // Sort the screening timestamps in default (ascending) order.
    sort($screenings_unix);

    // Convert list to csv
    $screenings_unix_csv = implode(",", $screenings_unix);

    $last_element = ( count( $screenings_unix ) - 1 );
    $this_first_screening = $screenings_unix[0];
    $this_last_screening = $screenings_unix[$last_element];

    // Debugging.
    // echo '$this_first_screening: ' . $this_first_screening . '<br>';
    // echo '$this_last_screening: ' . $this_last_screening . '<br>';
    // echo '$screenings_unix_csv: ' . $screenings_unix_csv . '<br>';

    // Save first screening, last screening, and all screenings as custom field values.
    update_post_meta( $post_id, 'first_screening', $this_first_screening );
    update_post_meta( $post_id, 'last_screening', $this_last_screening );
    update_post_meta( $post_id, 'all_screenings', $screenings_unix_csv );

    foreach ($screenings as $key=>$thisScreening) {
      $this_row = $key+1;
      $this_screening = $screenings_unix[$key];
      $this_screening_date = date("Ymd", $this_screening);
      $this_screening_time = date("H:i:s", $this_screening);

      delete_sub_row( array('screenings', $this_row, 'screening_date'), 1 );
      delete_sub_row( array('screenings', $this_row, 'screening_time'), 1 );

      // Debugging.
      // echo $this_row . ' ' . $this_screening_date . ' ' . $this_screening_time . '<br>';

      update_sub_field( array('screenings', $this_row, 'screening_date'), $this_screening_date );
      update_sub_field( array('screenings', $this_row, 'screening_time'), $this_screening_time );

    }

  }

  // Debugging.
  // echo '</div>';

}

add_action( 'acf/save_post', 'save_film_stuff', 20 );
