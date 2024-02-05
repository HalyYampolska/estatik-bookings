<?php
/*
Plugin Name: Estatik Bookings
Plugin URI:
Description: Plugin for test task on position developer.
Author: Halyna Yampolska    
Author URI: halyna.yampolska@gmail.com
Version: 1.0
*/

/**
 * Create Booking Post Type 
 */

add_action( 'init', 'create_booking_post_type' );

function create_booking_post_type() {

  $labels = array(
    'name' => __( 'Bookings', 'estatik-bookings' ),
    'singular_name' => __( 'Booking', 'estatik-bookings' ),
    'menu_name' => __( 'Bookings', 'estatik-bookings' ),
    'parent_item_colon' => __( 'Parent Booking:', 'estatik-bookings' ),
    'all_items' => __( 'All Bookings', 'estatik-bookings' ),
    'add_new_item' => __( 'Add New Booking', 'estatik-bookings' ),
    'edit_item' => __( 'Edit Booking', 'estatik-bookings' ),
    'new_item' => __( 'New Booking', 'estatik-bookings' ),
    'view_item' => __( 'View Booking', 'estatik-bookings' ),
    'search_items' => __( 'Search Bookings', 'estatik-bookings' ),
    'not_found' => __( 'No bookings found.', 'estatik-bookings' ),
    'not_found_in_trash' => __( 'No bookings found in Trash.', 'estatik-bookings' ),
  );

  $args = array(
    'labels' => $labels,
    'public' => true,
    'has_archive' => true,
    'menu_icon' => 'dashicons-calendar',
    'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions' ),
  );

  register_post_type( 'booking', $args );

}

/**
 * Add file with meta-box functions 
 */
require_once 'bookings-meta-box.php';

/**
 * Display booking ditails on frontend
 */

function display_booking_details_after_content( $content ) {
  if (is_singular('booking') && is_main_query()) {
      $start_date = get_post_meta(get_the_ID(), 'my_booking_start_date', true);
      $end_date = get_post_meta(get_the_ID(), 'my_booking_end_date', true);
      $address = get_post_meta(get_the_ID(), 'my_booking_address', true);

      if ($start_date && $end_date && $address) {
          $booking_details = '<div class="booking-details">';
          $booking_details .= '<p><strong>Start Date:</strong> ' . date('j M Y H:i', $start_date) . '</p>';
          $booking_details .= '<p><strong>End Date:</strong> ' . date('j M Y H:i', $end_date) . '</p>';
          $booking_details .= '<p><strong>Address:</strong> ' . esc_attr(esc_html($address)) . '</p>';
          $booking_details .= '</div>';

          // Display Date Info
          $content .= $booking_details;

          // Display Map
          $content .= '<div id="my_booking_map" style="height: 300px;"></div>';
          $content .= '<script>
              document.addEventListener("DOMContentLoaded", function() {
                  if (typeof google !== "undefined") {
                      var address = "' . esc_js($address) . '";
                      var mapElement = document.getElementById("my_booking_map");


                      var geocoder = new google.maps.Geocoder();


                      geocoder.geocode({ "address": address }, function(results, status) {
                          if (status == "OK") {
                              var map = new google.maps.Map(mapElement, {
                                  center: results[0].geometry.location,
                                  zoom: 12
                              });


                              var marker = new google.maps.Marker({
                                  map: map,
                                  position: results[0].geometry.location
                              });
                          } else {
                              console.error("Geocode was not successful for the following reason: " + status);
                          }
                      });
                  }
              });
          </script>';
      }
  }


  return $content;
}
add_filter( 'the_content', 'display_booking_details_after_content' );
