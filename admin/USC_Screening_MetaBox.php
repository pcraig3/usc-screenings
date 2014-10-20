<?php
/**
 * Class USC_Screening_MetaBox
 *
 * This class is later associated with the USCScreening_PostType. (since we're just creating a metabox, we can put it
 * wherever we like.)
 *
 * Anyway, this class defines a bunch of custom fields to associate with the 'usc_screenings' custom post type.
 */
class USC_Screening_MetaBox extends AdminPageFramework_MetaBox {

    /**
     * Framework method that registers all of the DateTime fields we need
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.8.0
     */
    public function start_USC_Screening_MetaBox() { // start_{extended class name} - this method gets automatically triggered at the end of the class constructor.

        /*
         * Register custom field types.
         */


        //exactly one up from this directory is the home directory of the plugin
        $usc_screenings_dir = trailingslashit( dirname( __DIR__ ) );

        /* 1. Include the file that defines the custom field type. */
        $aFiles = array(

            dirname( $usc_screenings_dir) . '/admin-page-framework/third-party/date-time-custom-field-types/DateCustomFieldType.php',
            dirname( $usc_screenings_dir) . '/admin-page-framework/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
            dirname( $usc_screenings_dir) . '/admin-page-framework/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
        );

        foreach( $aFiles as $sFilePath )
            if ( file_exists( $sFilePath ) ) include_once( $sFilePath );

        /* 2. Instantiate the classes  */
        $sClassName = get_class( $this );
        new DateCustomFieldType( $sClassName );
        new TimeCustomFieldType( $sClassName );
        new DateTimeCustomFieldType( $sClassName );

    }

    /**
     * ( optional ) Use the setUp() method to define settings of this meta box.
     *
     * @since    0.8.0
     */
    public function setUp() {

        /**
         * Framework method sets up all of the custom fields for a USC Screening and then enqueues some JS
         *
         * Fields added are:
         * 01. start_date: the date that a screening will start showing
         * 02. end_date: the final date for a screening
         * 03. showtimes_repeatable: the time(s) of day that the screening will be showed
         * 04. if_weekend_showtimes: a checkbox to indicate whether there will be alternative showtimes on weekends,
         * 05. weekend_showtimes_repeatable: the alternative time(s) for the weekend screenings.
         * 06. duration: length of a screening (in minutes)
         * 07. rating: the rating for a screening (usually found on the ontario film board review website)
         * 08. content_advisories: content warnings and advisories (also usually found on the ontario film board review website)
         * 09. genre: the genre of a screening as a string
         * 10. trailer_link: a link to the official trailer.  If this trailer comes from a Whitelisted Video Hosting website, the
         *              trailer video will be embedded in the single page template
         * 11. official_site_link: a link to this screening's official site
         * 12. alert: (optional) a very important alert about this film to appear above the listing and the single page template
         *
         * @remark this is a pre-defined framework method
         *
         * @since 0.4.0
         */
        $this->addSettingFields(
            array(
                'field_id'		=> 'start_date',
                'title'			=> __( 'Start Date', 'usc-screenings' ),
                'description'	=> __( 'The first day of the runtime.', 'usc-screenings' ),
                'help'			=> __( 'The first day of the runtime.', 'usc-screenings' ),
                'type'			=> 'date',
                'date_format'	=>	'yy-mm-dd',
                'size'          =>  '40',
                //'attributes'	=>	array()
            ),
            array(	// date picker
                'field_id'	    =>	'end_date',
                'title'			=>  __( 'End Date', 'usc-screenings' ),
                'description'	=> __( 'The final day of the runtime.', 'usc-screenings' ),
                'help'			=> __( 'The final day of the runtime.', 'usc-screenings' ),
                'type'			=> 'date',
                'date_format'	=>	'yy-mm-dd',
                'size'          =>  '40',
            ),
            array(	// Repeatable time picker fields
                'field_id'	=>	'showtimes_repeatable',
                'type'	=>	'time',
                'title'	=>	__( 'Showtime(s)', 'usc-screenings' ),
                'repeatable'	=> true,
                'options'	=>	array(
                    'hourGrid'		=>	12,
                    'minuteGrid'	=>	30,
                    'timeFormat'	=>	'hh:mm tt',
                ),
                'description'	=>	__( 'The time(s) of day for the screening.', 'usc-screenings' ),
                'help'	        =>	__( 'The time(s) of day for the screening.', 'usc-screenings' ),
            ),
            array(	// Multiple checkbox items - for multiple checkbox items, set an array to the 'label' element.
                'field_id'	=>	'if_weekend_showtimes',
                'title'	=>	__( 'Alternate Weekend Showtimes?', 'admin-page-framework-demo' ),
                'type'	=>	'checkbox',
                'label'	=>	array(
                    'friday'	=>	__( 'Friday', 'usc-screenings' ),
                    'saturday'	=>	__( 'Saturday', 'usc-screenings' ),
                    'sunday'	=>	__( 'Sunday', 'usc-screenings' ),
                ),
                'default'	=>	array(
                    'fri'	=>	false,
                    'sat'	=>	false,
                    'sun'	=>	false,
                ),
                'description'	=> __( 'Which days have an alternate schedule?', 'usc-screenings' ),
                'help'	        => __( 'Which days have an alternate schedule?', 'usc-screenings' ),
                /* 'after_label'	=>	'<br />',*/
            ),
            array(	// Repeatable time picker fields
                'field_id'	=>	'weekend_showtimes_repeatable',
                'type'	=>	'time',
                'title'	=>	__( 'Alternate Showtime(s)', 'usc-screenings' ),
                'repeatable'	=> true,
                'options'	=>	array(
                    'hourGrid'		=>	12,
                    'minuteGrid'	=>	30,
                    'timeFormat'	=>	'hh:mm tt',
                ),
                'description'	=>	__( 'Alternate weekend showtimes.', 'usc-screenings' ),
                'help'	        =>	__( 'Alternate weekend showtimes.', 'usc-screenings' ),
                /*'attributes'	=>	array(
                    'class'	=>	'hidden',
                ),*/
            ),
            array(
                'field_id'		=> 'duration',
                'type'			=> 'text',
                'title'			=> __( 'Duration (in minutes)', 'usc-screenings' ),
                'description'	=> __( 'How many minutes is this screening?', 'usc-screenings' ),
                'help'	        => __( 'How many minutes is this screening?', 'usc-screenings' ),
            ),

            array (
                'field_id'		=> 'rating',
                'type'			=> 'radio',
                'title'			=> __( 'Rating', 'usc-screenings' ),
                'description'	=> __( 'Rating for this film.', 'usc-screenings' ),
                'help'	        => __( 'Rating for this film.', 'usc-screenings' ),
                'label' => array(
                    'g'     => __( 'G', 'usc-screenings' ),
                    'pg'    => __( 'PG', 'usc-screenings' ),
                    '14a'   => __( '14A', 'usc-screenings' ),
                    '18a'   => __( '18A', 'usc-screenings' ),
                    'r'     => __( 'R', 'usc-screenings' ),
                ),
            ),
            array(
                'field_id'		=> 'content_advisories',
                'type'			=> 'textarea',
                'title'			=> __( 'Content Advisories', 'usc-screenings' ),
                'description'	=> __( 'A hyphen followed by a space (ie, " - WARNING") is rendered as a newline on the '
                    .' public page for this Screening.', 'usc-screenings' ),
                'help'			=> __( 'More detail on content advisories.', 'usc-screenings' ),
            ),
            array(
                'field_id'		=> 'genre',
                'type'			=> 'text',
                'title'			=> __( 'Genre', 'usc-screenings' ),
                'description'	=> __( '(optional)', 'usc-screenings' ),
                'help'			=> __( 'What genre is this screening?', 'usc-screenings' ),
            ),
            array( // Media File (which we are constraining to PDFs.)
                'field_id'		=>	'trailer_link',
                'title'			=>	__( 'Link to Trailer', 'usc-screenings' ),
                'type'			=>	'text',
                'description'	=>	__( 'Link to the trailer. (optional)', 'usc-screenings' ),
                'description'	=>	__( 'Link to the trailer. (optional)', 'usc-screenings' ),
                'allow_external_source'	=>	true,
            ),
            array(
                'field_id'		=>	'official_site_link',
                'title'			=>	__( 'Link to Official Site', 'usc-screenings' ),
                'type'			=>	'text',
                'description'	=>	__( 'Link to the official site. (optional)', 'usc-screenings' ),
                'description'	=>	__( 'Link to the official site. (optional)', 'usc-screenings' ),
                'allow_external_source'	=>	true,
            ),
            array(
                'field_id'		=> 'alert',
                'type'			=> 'text',
                'title'			=> __( 'Alerts / Updates', 'usc-screenings' ),
                'description'	=> __( 'Information entered here appears on the movie listing as well as the single page.', 'usc-screenings' ),
                'help'			=> __( 'Information entered here appears on the movie listing as well as the single page.', 'usc-screenings' ),
            )
        );

        /* enqueue a javascript file with a very specialized function directly relating to this metabox */
        $this->enqueueScript(
            plugins_url('assets/js/reveal-alternate-showtimes-pane.js', __FILE__ ),   // source url or path
            array( 'usc_screenings' ),
            array(
                'handle_id' => 'reveal_alternate_showtimes_pane',     // this handle ID also is used as the object name for the translation array below.
                'dependencies ' => array('jquery'),
                'in_footer' => true
            )
        );
    }

    /**
     * Function that validates values for usc_screenings.
     *
     * Decided it wasn't worth error-checking.  We can just trust James to be attentive
     *
     * @see: http://stackoverflow.com/questions/5007748/modifying-wordpress-post-status-on-publish
     *
     * @param array $aInput values in each of the input fields at the time of submitting the form
     * @param array $aOldInput old values saved from before inputting the form.
     * @return      array $aInput values as they were received
     *
     */    public function validation_USC_Screening_MetaBox( $aInput, $aOldInput ) {	// validation_{instantiated class name}

        $_fIsValid = true;
        $_aErrors = array();

        return $aInput;

    }

    /**
     * Check if an item exists out there in the "ether".
     * Could potentially be used to check for trailer/official site links if this was every extended to do that.
     *
     * @since    0.8.0
     *
     * @see: http://stackoverflow.com/questions/7952977/php-check-if-url-and-a-file-exists
     *
     * @param string $url - preferably a fully qualified URL
     * @return boolean - true if it is out there somewhere
     */
    private function web_item_exists( $url ) {
        if ( !isset( $url ) || empty( $url ) )
            return false;

        $response = wp_remote_head( $url, array( 'timeout' => 5 ) );

        $accepted_status_codes = array( 200, 301, 302 );

        if ( ! is_wp_error( $response ) && in_array( wp_remote_retrieve_response_code( $response ), $accepted_status_codes ) ) {
            return true;
        }
        return false;
    }
}


