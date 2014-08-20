<?php
/**
 * USC Screenings.
 *
 * @package   USC_Screenings
 * @author    Paul Craig <pcraig3@uwo.ca>
 * @license   GPL-2.0+
 * @copyright 2014
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-usc-screenings-admin.php`
 *
 * @package USC_Screenings
 * @author    Paul Craig <pcraig3@uwo.ca>
 */
class USC_Screenings {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.7.0
     *
     * @var     string
     */
    const VERSION = '0.7.0';

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


    protected $usc_screenings_dir = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since    0.7.0
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

        add_action( 'init', array( $this, 'explictly_add_usc_screenings_thumbnails' ) );
        add_action( 'init',  array( $this, 'register_shortcodes' ) );

        add_filter( 'template_include', array( $this, 'usc_screenings_set_template' ) ) ;

        add_filter( 'body_class', array( $this, 'usc_screenings_body_classes'), 100 );

        /* #main-content .container:before */
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
     * there are no Posts returned for the given status, this function returns a single paragprah to the
     * screen with an error message/explanation.
     *
     * If there is a valid status, however, query the screenings whose end dates have not passed, ordered by their
     * starting date.
     * Then, once you have them, build the HTML for each one and return it to the screen.  Easy.
     *
     * @since    0.7.0
     *
     * @param $atts     parameters entered in along with the shortcode
     * @return string   the html for valid screenings, or an error message if none or found or the shortcode is used incorrectly
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
            . trim( implode(', ',$screenings_statuses), ', ') . ".  Try again with one of those.</p>";
        }

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

        $query = new WP_Query( $args );

        //if _have_posts(), this value is reset.
        $html_string = '<p>Whoops! Didn\'t find anything under "' . $screenings_statuses[$status] . '" right now, sorry about that.</p>';

        if( $query->have_posts() ) {

            $html_string = '';

            while( $query->have_posts() ) {

                $query->the_post();
                global $post;

                $start_date = get_post_meta( $post->ID, 'start_date', true );

                //don't show the movie if there's no start date
                if( !empty( $start_date ) ) {

                    $html_string .= require('views/usc_screenings-shortcode.php');
                }
            }

        }

        wp_reset_postdata();

        return $html_string;
    }

    /**
     * Feed in a start_date and end_date and return the string we would like for the HTML.
     * If the months are the same, just use the second date (ie, "August 3 - 10")
     *
     * The logic before this method ensures that there will always be a start and end date.
     *
     * @since    0.7.0
     *
     * @param $start_date   the start date of our screening
     * @param $end_date     the final date of our screening
     * @return string       a formatted date-range string
     */
    private function return_date_range_string( $start_date, $end_date ) {

        $date_string = '';

        $start_date_timestamp = strtotime($start_date);
        $end_date_timestamp   = strtotime($end_date);

        //get the months
        $start_month    = date('F', $start_date_timestamp);
        $end_month      = date('F', $end_date_timestamp);

        //get the days
        $start_day      = date('j', $start_date_timestamp);
        $end_day        = date('j', $end_date_timestamp);

        $date_string =         $start_month . ' ' . $start_day . ' - ';
        //if the months are the same, then just return the end date.
        $date_string .=         ( $start_month === $end_month ) ? $end_day : $end_month . ' ' . $end_day;

        return $date_string;

    }

    /**
     * Similar to the last one.  Get an array of showtimes and append them to a string.
     * If there are no showtimes given, we still get an array with an empty string.
     * In this case, a blank string is returned.
     *
     * @since    0.7.0
     *
     * @param $showtimes_array  an array of showtimes for this screening
     * @return string           a formatted string of all of the showtimes
     */
    private function return_showtimes_string( $showtimes_array ) {

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
     * Very similar to the last one, except here we also get an array of days.
     * If the array of days is empty, no showtimes.
     * Else, return the days as the subheading for our showtimes.
     *
     * @since    0.7.0
     *
     * @param $days_array       an array of days for this screening
     * @param $showtimes_array  an array of showtimes for a screening
     * @return string           a formatted string of all of the showtimes prepended by the number of days.
     */
    private function return_alternate_showtimes_date_string( $days_array, $showtimes_array ) {

        $showtimes_string = '';

        //basically, create an array of days that have 1s
        foreach( $days_array as $key => $day )
            if( 0 === intval( $day ) )
                unset( $days_array[$key] );

        if( empty( $days_array ) )
            return '';

        else {

            $days_array = array_keys( $days_array );

            $last_day = array_pop( $days_array );

            $showtimes_string .= implode(", ", $days_array) . " & " . $last_day;
        }

        $showtimes_string = '<span class="subhead">' . ucwords( $showtimes_string ) . ':</span>'
            . $this->return_showtimes_string( $showtimes_array );

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
