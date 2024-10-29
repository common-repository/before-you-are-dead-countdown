<?php
// Creating the widget 
class Byad_Countdown_Widget extends WP_Widget {

  function __construct() {
    parent::__construct(
      // Base ID of your widget
      'byadcd_widget', 
      // Widget name will appear in UI
      __('Before You Are Dead Countdown Widget', 'byad-countdown'), 
      // Widget description
      array( 
        'description' => __( 'Widget for displaying a countdown (Years (opt.), Days, Hours, Minutes, Seconds)', 'byad-countdown' ), 
        'classname' => 'byad-countdown',
      ) 
    );
    
  }
  
  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    $instance['title'] = isset($instance['title']) ? $instance['title'] : __('The Final Countdown', 'byad-countdown');
    $title_str = '<span class="byad-title">' . $instance['title'] . '</span><div class="arrow-container"><div class="arrow-up gray"></div><div class="arrow-up white"></div></div>';
    $title = apply_filters( 'byad_title', $title_str );
  
    // before and after widget arguments are defined by themes
    echo $args['before_widget'];
    
    // This is where you run the code and display the output
    //get timestamp from plugin settings
    $byadcd_dateformat =  get_option('byad_countdown_dateformat', 'eur');
    $byadcd_date = get_option('byad_countdown_date_picker', date('d/m/Y', strtotime('tomorrow')));
    $byadcd_deadend = get_option('byad_countdown_deadend', __('You are dead now!', 'byad-countdown'));
    $byadcd_years = get_option('byad_countdown_years', 0);
    
    //optionals Hours and Minutes
    $byadcd_time = get_option('byad_countdown_time', '');
    $byadcd_time = !strpos($byadcd_time, ':') ? false : $byadcd_time;

    
    $time_tab = !$byadcd_time ? array(23,59,59) : explode(':', $byadcd_time);

    $byadcd_hours = (int) $time_tab[0];
    $byadcd_min = (int) $time_tab[1];
    $byadcd_sec = isset($time_tab[2]) ? (int) $time_tab[2] : 00;
    
    $byadcd_date = explode('/', $byadcd_date);

    //check date format chosen and set end date into timestamp
    switch($byadcd_dateformat) {
      case 'eur':  list($day, $month, $year) = $byadcd_date; break;
      case 'usa':  list($month, $day, $year) = $byadcd_date; break;
      case 'chi':  list($year, $month, $day) = $byadcd_date; break;
    }
    
    date_default_timezone_set( apply_filters('byad_timezone', 'Europe/Paris') ); //Add filter to alter the default timezone
    
    //mktime(hour,minute,second,month,day,year)
    $end_date = mktime($byadcd_hours, $byadcd_min, $byadcd_sec, $month, $day, $year);
    $today = time();

//var_dump(date('d/m/Y - h:i:s', $today));    
//var_dump(date('d/m/Y - h:i:s', $end_date));    
    
    $remaining_time = $end_date - $today;

    if ( is_active_widget( false, false, $this->id_base, true ) ) {
      wp_enqueue_script( 'byad-countdown', plugins_url( 'js/byad-countdown.js' , __FILE__ ), array('jquery'), '1.0', true );
      //Add filter to alter the JS data
      $params = apply_filters('byad_jsdata', array(
        'byadClass' => 'countdown-display',
        'byadImg' => '/before-you-are-dead-countdown/images/skull.png',
        'byadAlt' => '.',
        'endDate' => $end_date,
        'today' => $today,
        'timeLeft' => $remaining_time,
        'deadend' => $byadcd_deadend,
        'display_years' => $byadcd_years,
        'year' => __('Years', 'byad-countdown'),
        'month' => __('Months', 'byad-countdown'),
        'day' => __('Days', 'byad-countdown'),
        'hour' => __('Hours', 'byad-countdown'),
        'min' => __('Minutes', 'byad-countdown'),
        'sec' => __('Seconds', 'byad-countdown'),
      ) );
      wp_localize_script( 'byad-countdown', 'jbydCD_Data', $params );
    }
    
    echo '<div class="countdown-display"  data-root="' . plugins_url() . '"></div>';
  
    if ( ! empty( $title ) ) {
      echo $args['before_title'] . $title . $args['after_title'];
    }
  
    echo $args['after_widget'];
  }
  		
  // Widget Backend 
  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    }
    else {
      $title = __('The Final Countdown', 'byad-countdown');
    }
    // Widget admin form
    ?>
    <p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'byad-countdown' ); ?></label> 
      <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php 
  }
  	
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
  }
} // Class Byad_Countdown_Widget ends here


// Register and load the widget
function byad_load_widget() {
	register_widget( 'Byad_Countdown_Widget' );
}
add_action( 'widgets_init', 'byad_load_widget' );
