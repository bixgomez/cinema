<?php

function showtimes_function($atts = [], $tag = '') {

  $output = '';
  $output .= '<h4 class="screenings--title">Showtimes</h4>';

  $post_id = get_the_ID();

  $screenings = get_field('screenings', $post_id);
  $screenings_unix = array();

  if ($screenings) {
    foreach ($screenings as $key=>$thisScreening) {
      $thisScreeningDate = $thisScreening['screening_date'];
      $thisScreeningTime = $thisScreening['screening_time'];
      $thisScreeningDateTime = $thisScreeningDate . ' ' . $thisScreeningTime;
      $unixtime =  strtotime($thisScreeningDateTime);
      array_push($screenings_unix, $unixtime);
    }

    sort($screenings_unix);

    $output .= '<table class="screenings"><tr><td>';

    $lastScreening = 0;
    foreach ($screenings_unix as &$thisScreening) {

      $lastScreening_date = date('m/d', $lastScreening);
      $thisScreening_date = date('m/d', $thisScreening);

      // $output .= '<h6>' . $lastScreening_date . ' ' . $thisScreening_date . '</h6>';

      if ( $lastScreening_date != $thisScreening_date ) :
        if ($lastScreening != 0) :
          $output .= '</td></tr><tr><td>';
        endif;
        $output .= date('l, F j: ', $thisScreening) . '</td><td>' . date('g:i a', $thisScreening);
      else :
        $output .= ', ' . date('g:i a', $thisScreening);
      endif;

      $lastScreening = $thisScreening;
    }
    $output .= '</td></tr></table>';
  }

  else {
    $output .= '<p>TBA</p>';
  }

  return $output;
}

add_shortcode('film_showtimes', 'showtimes_function');
