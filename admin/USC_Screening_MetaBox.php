<?php
class USC_Screening_MetaBox extends AdminPageFramework_MetaBox {

    /**
     * Framework method that registers all of the DateTime fields we need
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.3.1
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

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
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
            array(
                'field_id'		=> 'duration',
                'type'			=> 'text',
                'title'			=> __( 'Duration', 'usc-screenings' ),
                'description'	=> __( 'How long is this screening?', 'usc-screenings' ),
                'help'	=> __( 'How long is this screening?', 'usc-screenings' ),
            ),
            /*  //rating?
            array (
                'field_id'		=> 'position',
                'type'			=> 'radio',
                'title'			=> __( 'Position*', 'usc-screenings' ),
                'description'	=> __( '(required if position is paid)', 'usc-screenings' ),
                'help'	        => __( 'What kind of position this job is for.', 'usc-screenings' ),
                'label' => array(
                    'ft_permanent'  => __( 'Full-Time Permanent', 'usc-screenings' ),
                    'ft_contract'   => __( 'Full-Time Contract', 'usc-screenings' ),
                    'pt_permanent'  => __( 'Part-Time Permanent', 'usc-screenings' ),
                    'pt_contract'   => __( 'Part-Time Contract', 'usc-screenings' ),
                    'honourarium'   => __( 'Honourarium', 'usc-screenings' ),
                ),
                'default' => 'volunteer',
                'attributes'	=>	array(
                    'class'	=>	'hidden',
                ),
            ),
            */
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
            )
        /* ,
        array (
            'field_id'		=> 'taxonomy_checklist',
            'type'			=> 'taxonomy',
            'title'			=> __( 'Departments', 'usc-screenings' ),
            'taxonomy_slugs'	=>	array( 'departments' )
        )
        */
        );

        /*
        $this->enqueueScript(
            plugins_url('assets/js/reveal_job_pane.js', __FILE__ ),   // source url or path
            array( 'usc_jobs' ),
            array(
                'handle_id' => 'reveal_job_pane',     // this handle ID also is used as the object name for the translation array below.
                'dependencies ' => array('jquery'),
                'in_footer' => true
            )
        );*/
    }

    /** Draft if errors found in validation: http://stackoverflow.com/questions/5007748/modifying-wordpress-post-status-on-publish */
    public function validation_USC_Screening_MetaBox( $aInput, $aOldInput ) {	// validation_{instantiated class name}

        $_fIsValid = true;
        $_aErrors = array();

        // You can check the passed values and correct the data by modifying them.
        //echo $this->oDebug->logArray( $aInput );

        $non_empty_fields = array(

            'job_description'   => 'Sorry, but Job Description cannot be empty.',
            'apply_by_date'     => 'Yikes!  You forgot to put in an apply-by date.',
            'job_description_file'  => 'Oh no! Please upload and select a job description file.'
        );

        // Validate the submitted data.
        /*foreach( $non_empty_fields as $key => $value ) {

            if ( empty( $aInput[$key] ) ) {

                $_aErrors[$key] = __( $value, 'usc-screenings' );
                $_fIsValid = false;
            }
        }

        if( ! isset( $_aErrors['job_description_file'] ) ) {

            //get only the file extension
            $job_description_file_extension = pathinfo($aInput['job_description_file'], PATHINFO_EXTENSION);

            $allowed_extensions = array(
                'pdf',
                'doc',
                'docx'
            );

            if ( ! in_array($job_description_file_extension, $allowed_extensions) ) {

                $_aErrors['job_description_file'] = __( 'Not an acceptable file type.  Please upload a PDF or a Word Document.', 'usc-screenings' );
                $_fIsValid = false;
            }
            ///http://stackoverflow.com/questions/7952977/php-check-if-url-and-a-file-exists
            elseif ( ! $this->web_item_exists( $aInput['job_description_file'] ) ){

                $_aErrors['job_description_file'] = __( 'Sorry, but your URL doesn\'t appear to exist. Try uploading and selecting your file again.', 'usc-screenings' );
                $_fIsValid = false;

            }
        }

        if( ! filter_var( $aInput['application_link'], FILTER_VALIDATE_URL )  ) {

            $_aErrors['application_link'] = __( 'Sorry, can you try a properly formatted URL?', 'usc-screenings' );
            $_fIsValid = false;

        }
        */

        if ( ! $_fIsValid ) {

            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( '<pre>' . print_r($aInput, true) . '</pre><p>' . 'nothing' . '</p>', 'usc-screenings' ) );

            //hacky, but fun!
            add_filter( 'wp_insert_post_data', function( $data ) { //use ( $status ) {

                $data['post_status'] = 'pending';

                return $data;
            });

            return $aInput;

        }

        return $aInput;

    }

    /**
     * Check if an item exists out there in the "ether".
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


