<?php
// Initialize function, with attributes.
function film_teasers_function($atts = [], $content = null, $tag = '') {

  $output = '';

  global $wpdb;

  // override default attributes with user attributes
  $filmteaser_atts = shortcode_atts([
    'from_date' => '0',
    'to_date' => '0',
    'list_only' => '0',
  ], $atts, $tag);

  // Set from & to date variables based on attributes passed.
  $from_date = $filmteaser_atts['from_date'];
  $to_date = $filmteaser_atts['to_date'];
  $list_only = $filmteaser_atts['list_only'];

  $this_date = $from_date;
  $querystr = "SELECT ID FROM $wpdb->posts WHERE post_type = 'film' AND post_status = 'publish' AND ID IN (SELECT post_id FROM $wpdb->postmeta WHERE post_id > 0 AND ( ";

  while ($this_date <= $to_date) :
    $this_date_num = date('Ymd', $this_date);
    $i=0;
    while ( $i <= $GLOBALS['max_screenings'] ) :
      $querystr .= "( (meta_value='" . $this_date_num . "') AND (meta_key='screenings_" . $i . "_screening_date') ) OR ";
      $i++;
    endwhile;
    $this_date = strtotime('+1 days', $this_date);
  endwhile;

  $querystr .= "meta_value='29991231' )) ORDER BY ID ASC";

  $rows = $wpdb->get_results($querystr, OBJECT);

  $films_to_display = array();

  $prefix = $films_list = '';
  if ($rows) :
    foreach ($rows as $row) {
      $film_id = $row->ID;
      if ( !in_array($film_id, $films_to_display) ) :
        array_push($films_to_display, $film_id);
        $films_list .= $prefix . $film_id;
        $prefix = ',';
      endif;
    }
  endif;

  if ($list_only) :

    $output = $films_list;

  else:

    $output .= '<div class="film-teasers">';

    if ( sizeof($films_to_display) ) :
    foreach ($films_to_display as $film_to_display) {
      $output .= do_shortcode('[film_teaser film_id="' . $film_to_display .'"]');
    }
    else:
      $output .= '<div class="film-teaser no-screenings"><p>No screenings are scheduled for this week.</p></div>';
    endif;

    $output .= '</div>';

  endif;

  return $output;
}

function film_teasers_init() {
  add_shortcode('film_teasers', 'film_teasers_function');
}

add_action('init', 'film_teasers_init');
