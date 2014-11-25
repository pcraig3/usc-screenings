<?php
/**
 * USC Screenings is more or less a standard Custom Post Type.
 * It creates the 'usc_screenings' Post Type (meant to represent a screening at WesternFilm).
 * Screenings might or might not be movies, but probably most of them will be.
 *
 * Instead of an archive page, this plugin generates a shortcode that creates listings of screenings
 * The intention is that films can be separated into statuses like 'Coming Soon' and 'Now Playing' and disappear
 * once their runtime has ended
 *
 * Uses the AdminPageFramework to create the admin page, and then uses filter.js to create a better archive page.
 *
 * @package   USC_Screenings
 * @author    Paul Craig <pcraig3@uwo.ca>
 * @license   GPL-2.0+
 * @copyright 2014
 */

/**
 * @package USC_Screenings
 * @author    Paul Craig <pcraig3@uwo.ca>
 */
class USC_Screenings {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
<<<<<<< HEAD
     * @since   1.0.2
     *
     * @var     string
     */
    const VERSION = '1.0.2';
=======
     * @since   1.0.3
     *
     * @var     string
     */
    const VERSION = '1.0.3';
>>>>>>> 39d3347652574f09e62efb151f5e9ef8b5ea3a68

    /**
     *
     * Unique identifier for your plugin.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    0.7.0
     *
     * @var      string
     */
    protected $plugin_slug = 'usc-screenings';

    /**
     * Instance of this class.
     *
     * @since    0.1.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * @since 0.8.0
     *
     * @var string variable used so that the template-finding function works
     */
    protected $usc_screenings_dir = null;

    /**
     * @var used for saving the default server timezone so that nothing odd happens with our time calculations
     *
     * @since 1.0.0
     */
    protected $date_default_timezone_get_status = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since    1.0.0
     */
    private function __construct() {

        //exactly one up from this directory is the home directory of the plugin
        $this->usc_screenings_dir = trailingslashit( dirname( __DIR__ ) );


        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        /* Define custom functionality.
         * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        //add_action( '@TODO', array( $this, 'action_method_name' ) );
        //add_filter( '@TODO', array( $this, 'filter_method_name' ) );

        $this->add_screenings_post_type();

        //add thumbnails to usc_screenings on init
        add_action( 'init', array( $this, 'explictly_add_usc_screenings_thumbnails' ) );

        //create shortcodes on init
        add_action( 'init',  array( $this, 'register_shortcodes' ) );

        //include the single-usc_screening template if a usc_screening is requested
        add_filter( 'template_include', array( $this, 'usc_screenings_set_template' ) ) ;

        //add various classes to the body class in order to square our layout with the Divi Theme's
        add_filter( 'body_class', array( $this, 'usc_screenings_body_classes'), 100 );

        /*
        * set an initial value to this variable, in case we call the method to overwrite the global value before we
        * call the method to store it
        */
        global $_wp_using_ext_object_cache;

        $this->wp_using_ext_object_cache_status = $_wp_using_ext_object_cache;

        //set a default timezone so that we don't accidentally call the wrong method first and overwrite anything
        $this->date_default_timezone_get_status = date_default_timezone_get();
    }

    /**
     * function sets the default timezone to America/Toronto because that's where #westernu is.
     *
     * @since 1.0.0
     */
    public function set_server_to_local_time() {

        $this->date_default_timezone_get_status = date_default_timezone_get();

        date_default_timezone_set("America/Toronto");
    }

    /**
     * function resets the server timezone back to whatever it was before calling 'set_server_to_local_time'
     *
     * @since 1.0.0
     */
    public function set_server_back_to_default_time() {

        date_default_timezone_set( $this->date_default_timezone_get_status );
    }

    /**
     * Adds classes to the array of body classes.
     *
     * @uses body_class() filter
     */
    function usc_screenings_body_classes( $classes ) {

        foreach($classes as $key => &$class)
            if($class === 'et_right_sidebar' || $class === 'et_includes_sidebar')
                unset($classes[$key]);

        unset($class);

        return array_values($classes);
    }

    /**
     * Pretty straightforward.  This method registers the shortcodes we want our plugin to use.
     *
     * @since    0.7.0
     */
    public function register_shortcodes(){

        add_shortcode('usc_screenings', array( $this, 'list_usc_screenings' ) );
    }

    /**
     * This is actually a pretty important method, so I'll try to make it make sense.
     * First, we get the shortcode parameter, which is meant to be the 'status' of a screening
     * (ie, coming-soon, special-events, or whatever).
     *
     * If there is no status given, or the given status does not correspond to a status slug, or
     * there are no Posts returned for the given status, this function returns a single paragraph to the
     * screen with an error message/explanation.
     *
     * If there is a valid status, however, query the screenings whose end dates have not passed, ordered by their
     * starting date.
     * Then, once you have them, build the HTML for each one and return it to the screen.  Easy.
     *
     * (Oh, and if there are no screenings with a valid date range for a given status, return a bit of text to the screen
     * that explains this to our handsome, witty users)
     *
     * @since    1.0.2
     *
     * @param array $atts   parameters entered in along with the shortcode
     * @return string       the html for valid screenings, or an error message if none or found or the shortcode is used incorrectly
     */
    public function list_usc_screenings ( $atts ) {

        $status = '';

        //initialize variables, in this case just the status
        extract(
            shortcode_atts(
                array(
                    'status' => '', //don't set a default term slug
                ), $atts ),
            EXTR_OVERWRITE);

        //array of terms for the taxonomies
        $screenings_statuses = get_terms( 'screenings_status', array( 'hide_empty' => 0 ) );

        foreach( $screenings_statuses as $index => $screenings_status ) {

            unset($screenings_statuses[$index]);

            //associative array( slug => name )
            $screenings_statuses[$screenings_status->slug] = $screenings_status->name;
        }
        unset($screenings_status);

        //if the given status isn't valid, return list of valid statuses.
        if( !in_array($status, array_keys( $screenings_statuses ) ) ) {
            return '<p>Sorry mate, you\'ve picked a bad status.  Acceptable statuses are '
            . trim( implode(', ', array_map( array( $this, 'wrap_strings_in_code_tags') , array_keys( $screenings_statuses ) ) ), ', ' )
            . ".  Try again with one of those.</p>";
        }

        //setting the 'end_date' for the query to Ontario's timezone rather than the 4-hours-ahead UTC time
        $this->set_server_to_local_time();

        /*
        //http://wordpress.stackexchange.com/questions/50761/when-to-use-wp-query-query-posts-and-pre-get-posts
        http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters
        */
        $args = array(
            'post_type' => 'usc_screenings',
            'meta_key'   => 'start_date',
            'orderby'    => 'meta_value modified',
            'order'      => 'ASC',
            //'posts_per_page' => '1',  this is effectively the 'limit'
            'meta_query' => array(
                //filter by movies which end today or later than today
                array(
                    'key'     => 'end_date',
                    'value'   => date('Y-m-d'),
                    'compare' => '>=',
                ),
            ),
            //select only those with the correct status term in the taxonomy.
            'tax_query' => array(
                array(
                    'taxonomy' => 'screenings_status',
                    'field'    => 'slug', // or slug
                    'terms'    => $status,
                ),
            ),
        );

        $this->set_server_back_to_default_time();

        $query = new WP_Query( $args );

        //if _have_posts(), this value is reset.
        $html_string = '<p>Nothing is under "' . $screenings_statuses[$status] . '" right now, sorry about that.</p><p>Check back later for updates!</p>';

        if( $query->have_posts() ) {

            $html_string = '';

            while( $query->have_posts() ) {

                $query->the_post();
                global $post;

                $start_date = get_post_meta( $post->ID, 'start_date', true );

                //don't show the movie if there's no start date
                if( !empty( $start_date ) ) {

                    $html_string .= require('views/shortcode-usc_screenings.php');
                }
            }

        }

        wp_reset_postdata();

        return $html_string;
    }

    /**
     * Private helper function that returns strings wrapped in <code> tags.  Used for an array_map in the above function
     *
     * @since 1.0.1
     *
     * @param string $array_element an array element to be wrapper
     * @return string               the array element wrapped in the HTML <code> tag
     */
    private function wrap_strings_in_code_tags($array_element) {

        return '<code>' . $array_element . '</code>';
    }

    /**
     * Check if a movie is playing this week.  Basically, it is if:
     * 1. It is currently the same date as its first screening.
     * 2. It is currently the date of its final screening.
     * 3. It is currently a date which falls between its first and final screening
     *
     * @since   0.8.1
     *
     * @param string $start_date    the start_date of a screening
     * @param string $end_date      the end date of a screening
     * @return bool                 true if the current time falls on or between either day.  Else, false.
     */
    public function is_playing_this_week( $start_date, $end_date ) {

        if( empty($start_date) || empty($end_date) )
            return false;

        $this->set_server_to_local_time();

        $start_date_timestamp = strtotime($start_date);
        $end_date_timestamp   = strtotime($end_date . "+23 hours"); //because if a movie ends today, it ends at the END of the day
        $now = time();

        $this->set_server_back_to_default_time();

        return ($start_date_timestamp < $now && $end_date_timestamp > $now);
    }

    /**
     * Feed in a start_date and end_date and return the string we would like for the HTML.
     * If the months are the same, just use the second date (ie, "August 3 - 10")
     *
     * Makes accommodations in case there is no start or end date (or either) input
     *
     * @since    1.0.2
     *
     * @param string $start_date    the start date of our screening
     * @param string $end_date      the final date of our screening
     * @return string               a formatted date-range string
     */
    public function return_date_range_string( $start_date, $end_date ) {

        $start_month = $end_month = $date_string = '';

        if( empty( $start_date ) && empty( $end_date ) )
            return false;

        $this->set_server_to_local_time();

        $start_date_timestamp = strtotime($start_date);
        $end_date_timestamp   = strtotime($end_date);

        $this->set_server_back_to_default_time();


        if( false !== $start_date_timestamp ) {

            $start_month    = date('F', $start_date_timestamp);
            $start_day      = date('j', $start_date_timestamp);
        }
        if( false !== $end_date_timestamp ) {

            $end_month    = date('F', $end_date_timestamp);
            $end_day      = date('j', $end_date_timestamp);
        }

        if( empty( $start_month ) && empty( $end_month ) )
            return '';

        if( empty( $start_month ) )
            return "Ending on " . $end_month . " " . $end_day;

        if( empty( $end_month ) )
            return "Starting on " . $start_month . " " . $start_day;


        $date_string =  'Playing: ' . $start_month . ' ' . $start_day;
        //Check logic when months and days are the same
        if ($start_month === $end_month) {
            if ($start_day !== $end_day)
                $date_string .= ' - ' . $end_day;
        } else
            $date_string .= ' - ' . $end_month . ' ' . $end_day;

        return $date_string;
    }

    /**
     * Similar to the last one.  Get an array of showtimes and append them to a string.
     * If there are no showtimes given, we still get an array with an empty string.
     * In this case, a blank string is returned.
     *
     * @since    0.7.0
     *
     * @param $showtimes_array  array of showtimes for this screening
     * @return string           a formatted string of all of the showtimes
     */
    public function generate_usc_screenings_shortcode_showtimes_HTML( $showtimes_array ) {

        $showtimes_string = '';

        if( !empty( $showtimes_array ) ) {

            $showtime = '';
            while( !empty( $showtimes_array )) {

                $showtime = array_shift( $showtimes_array );

                $showtimes_string .= ( !empty( $showtime ) ) ? '<span class="showtime">' . $showtime . '</span>' : '' ;
            }
            unset( $showtime );
        }

        return $showtimes_string;
    }

    /**
     * Creates the HTMl for the showtimes section of a single screenings page.
     *
     * @since   0.8.2
     *
     * @param array $showtimes_repeatable   an array of showtimes ("07:30 pm", etc)
     * @param string $duration              length of the movie in minutes ("162 mins")
     * @return string                       the HTML for listing the movie times
     */
    public function generate_usc_screenings_single_showtimes_HTML($showtimes_repeatable, $duration) {

        //this will be overwritten if the array of showtimes is not empty
        $html_string = '<p>No showtimes have been settled upon yet.</p>';

        //if empty, print some message.
        if( !empty( $showtimes_repeatable )) {

            $html_string = '';

            while( !empty( $showtimes_repeatable ) ) {

                $showtime = array_shift( $showtimes_repeatable );

                $html_string .= '<p class="showtimes__hours"><span class="showtime">' . $showtime . '</span>';

                if( !empty( $duration ) )
                    $html_string .= ' <span class="showtime__end"> (ends at '
                        . $this->calculate_time_interval( $showtime, $duration ) . ')</span>';

                $html_string .= '</p>';
            } //end of the while loop
            unset( $showtime );

        }

        return $html_string;
    }


    /**
     * I flatter myself that this method should be pretty easily guessed.
     * Pass in a show time and the movie duration (in minutes) and watch as the end-time is calculated.
     *
     * @since   0.8.1
     *
     * @param string $showtime  the time (07:33 pm or similar) of a film screening
     * @param string $duration  the duration in minutes of the screening
     * @return string           the time when the movie ends.
     */
    public function calculate_time_interval( $showtime, $duration ) {

        $minutes = intval( $duration );

        $time = new DateTime( $showtime );
        $time->add(new DateInterval('PT' . $minutes . 'M'));

        return $time->format('h:i a');
    }

    /**
     * Very similar to the last one, except here we also get an array of days.
     * If the array of days is empty, no showtimes, return an empty string.
     * Else, return a string with the days that the alternative showtimes apply
     * New!  Option to cut the day strings to a certain length (like 3, right?)
     *
     * @since    0.9.0
     *
     * @param array $days_array     an array of days for this screening
     * @param int $substr_length    an integer, if set, we cut down the individual day strings to this length
     * @return string               a formatted string of all of the showtimes prepended by the number of days.
     */
    public function return_alternate_showtimes_date_string( $days_array, $substr_length = 0 ) {

        $showtimes_string = '';

        //basically, create an array of days that have 1s
        foreach( $days_array as $key => $day )
            if( 0 === intval( $day ) )
                unset( $days_array[$key] );

        if( empty( $days_array ) )
            return '';

        else {

            $days_array = array_keys( $days_array );

            //cut down the size of the string only if $substr_length is not zero.
            if( 0 !== $substr_length )
                foreach($days_array as &$day)
                    $day = substr($day, 0, $substr_length);

            $last_day = array_pop( $days_array );

            $showtimes_string .= implode(", ", $days_array);

            $showtimes_string .= ( !empty( $showtimes_string ) ) ? ' & ' . $last_day : $last_day;
        }

        $showtimes_string = ucwords( $showtimes_string );

        return $showtimes_string;
    }

    /**
     * *Some* themes don't support post-thumbnails.  This function makes sure that they do.
     *
     * @since    0.7.0
     */
    public function explictly_add_usc_screenings_thumbnails() {
        add_theme_support( 'post-thumbnails', array( 'usc_screenings' ) );
    }

    /**
     * Creates a new Screening Post Type.  You should come watch.
     *
     * @since    0.7.0
     */
    public function add_screenings_post_type() {

        if ( ! class_exists( 'AdminPageFramework' ) )
            include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin-page-framework/library/admin-page-framework.min.php' );

        include_once('USC_Screening_PostType.php');
        new USCScreening_PostType( 'usc_screenings' );

    }

    /**
     * Checks if provided template path points to a 'usc_screenings' template recognised by our humble little plugin.
     * If no usc_screenings-single template is present the plug-in will pick the most appropriate
     * option, first from the theme/child-theme directory then the plugin.
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L153
     * @author  Stephen Harris
     *
     * @since    0.8.1
     *
     * @param string    $templatePath absolute path to template or filename (with .php extension)
     * @param string    $context What the template is for ('single-usc_screenings', etc).
     * @return bool     return true if template is recognised as a 'usc_screenings' template. False otherwise.
     */
    private function usc_screenings_is_screenings_template($templatePath,$context=''){

        $template = basename($templatePath);

        switch($context):
            case 'usc_screenings';
                return $template === 'single-usc_screenings.php';

        endswitch;

        return false;
    }

    /**
     * Checks to see if appropriate templates are present in active template directory.
     * Otherwises uses templates present in plugin's template directory.
     * Hooked onto template_include'
     *
     * **THIS MEANS THAT IF YOU WANT A CHANGE TO A TEMPLATE TO PROPAGATE, MAKE THE CHANGE TO THE TEMPLATE IN THE
     * THEMES FOLDER, NOT THE TEMPLATE FILE IN THE FOLDER FOR THIS PLUGIN**
     *
     * @see     https://github.com/stephenharris/Event-Organiser/blob/1.7.3/includes/event-organiser-templates.php#L192
     * @author  Stephen Harris
     *
     * @since    0.8.1
     *
     * @param string $template Absolute path to template
     * @return string Absolute path to template
     */
    public function usc_screenings_set_template( $template ) {

        if( is_singular( 'usc_screenings' ) && ! $this->usc_screenings_is_screenings_template( $template,'usc_screenings' ) ){

            //Viewing a single usc_screenings
            $template = $this->usc_screenings_dir . 'templates/single-usc_screenings.php';
        }

        return $template;
    }

    /**
     * Return the plugin slug.
     *
     * @since    0.1.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.1.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide  ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_activate();

                    restore_current_blog();
                }

            } else {
                self::single_activate();
            }

        } else {
            self::single_activate();
        }

    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    0.1.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_deactivate();

                    restore_current_blog();

                }

            } else {
                self::single_deactivate();
            }

        } else {
            self::single_deactivate();
        }

    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    0.1.0
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site( $blog_id ) {

        if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
            return;
        }

        switch_to_blog( $blog_id );
        self::single_activate();
        restore_current_blog();

    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    0.1.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col( $sql );

    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    0.1.0
     */
    private static function single_activate() {
        // @TODO: Define activation functionality here
        flush_rewrite_rules();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.1.0
     */
    private static function single_deactivate() {
        // @TODO: Define deactivation functionality here
        flush_rewrite_rules();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.1.0
     */
    public function load_plugin_textdomain() {

        $domain = $this->plugin_slug;
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
        wp_enqueue_script( 'jquery-ui-datepicker' );

    }

    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    0.1.0
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * NOTE:  Filters are points of execution in which WordPress modifies data
     *        before saving it or sending it to the browser.
     *
     *        Filters: http://codex.wordpress.org/Plugin_API#Filters
     *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    0.1.0
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
    }

}
