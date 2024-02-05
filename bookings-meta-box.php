<?php

// Create Meta Box
function my_booking_meta_box() {
  add_meta_box(
    'my_booking_meta_box',
    __( 'Booking', 'estatik-bookings' ),
    'my_booking_meta_box_callback',
    'booking',
    'normal',
    'high'
  );
}
add_action( 'add_meta_boxes', 'my_booking_meta_box' );

// Display Meta Box
function my_booking_meta_box_callback( $post ) {
    $start_date = get_post_meta( $post->ID, 'my_booking_start_date', true );
    $end_date = get_post_meta( $post->ID, 'my_booking_end_date', true );
    $address = get_post_meta( $post->ID, 'my_booking_address', true );
 
    ?>
    <p>
      <label for="my_booking_start_date">Start Date:</label>
      <input type="text" name="my_booking_start_date" id="my_booking_start_date" value="<?php echo esc_attr( $start_date ? date('d M Y H:i', $start_date) : '' ); ?>" class="my-booking-date-picker">
    </p>
    <p>
      <label for="my_booking_end_date">End Date:</label>
      <input type="text" name="my_booking_end_date" id="my_booking_end_date" value="<?php echo esc_attr( $end_date ? date('d M Y H:i', $end_date) : '' ); ?>" class="my-booking-date-picker">
    </p>
    <p>
      <label for="my_booking_address">Address:</label>
      <input type="text" name="my_booking_address" id="my_booking_address" value="<?php echo esc_attr( $address ); ?>" placeholder="Add Address">
    </p>
    <p>
      <label for="my_booking_map">Location:</label>
      <div id="my_booking_map" style="height: 300px;"></div>
    </p>
    <?php
  }
 
// Save Date Info
function save_my_booking_meta_box( $post_id ) {
    if ( ! isset( $_POST['my_booking_start_date'] ) || ! isset( $_POST['my_booking_end_date'] ) || ! isset( $_POST['my_booking_address'] ) ) {
      return;
    }
 
    $start_date = strtotime( sanitize_text_field( $_POST['my_booking_start_date'] ) );
    $end_date = strtotime( sanitize_text_field( $_POST['my_booking_end_date'] ) );
    $address = sanitize_text_field( $_POST['my_booking_address'] );
 
    update_post_meta( $post_id, 'my_booking_start_date', $start_date );
    update_post_meta( $post_id, 'my_booking_end_date', $end_date );
    update_post_meta( $post_id, 'my_booking_address', $address );
}
add_action( 'save_post', 'save_my_booking_meta_box' );

// Add jQuery
function my_booking_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.js', array('jquery'), '4.6.9', true);
    wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css');

// Add Google Maps JavaScript API
 wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCUfsYOIyOnRUcOjsTOwWocDbAfisZXVlw&callback=initMap', array(), null, true);
?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr('.my-booking-date-picker', {
            dateFormat: 'd M Y H:i',
            enableTime: true,
        });
    });

    function initMap() {
        var geocoder = new google.maps.Geocoder();
        var address = document.getElementById('my_booking_address').value;
        var mapElement = document.getElementById('my_booking_map');

        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status == 'OK' && results[0].geometry.location) {
                var map = new google.maps.Map(mapElement, {
                    center: results[0].geometry.location,
                    zoom: 12
                });

                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            } else {
                console.error('Geocode was not successful for the following reason: ' + status);
            }
        });
    }
</script>
<?php
  }
 
add_action( 'wp_footer', 'my_booking_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'my_booking_enqueue_scripts' );