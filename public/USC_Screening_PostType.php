<?php
/**
 * USCScreening_PostType extends the AdminPageFramework_PostType class and creates a 'usc_screenings' as a WordPress Custom Post
 * Type
 * Also associates the 'Status' taxonomy with usc_screenings.
 */
class USCScreening_PostType extends AdminPageFramework_PostType {

    /**
     * This method is called at the end of the constructor.
     *
     * Alternatively, you may use the start_{extended class name} method, which also is called at the end of the constructor.
     */
    public function start() {

        $this->setAutoSave( false );
        $this->setAuthorTableFilter( true );

        $this->setPostTypeArgs(
            array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels' => array(
                    'name'			=>	'Screenings',
                    'all_items' 	=>	__( 'All Screenings', 'usc-screenings' ),
                    'singular_name' =>	'Screening',
                    'add_new'		=>	__( 'Add New', 'usc-screenings' ),
                    'add_new_item'	=>	__( 'Add New Screening', 'usc-screenings' ),
                    'edit'			=>	__( 'Edit', 'usc-screenings' ),
                    'edit_item'		=>	__( 'Edit Screening', 'usc-screenings' ),
                    'new_item'		=>	__( 'New Screening', 'usc-screenings' ),
                    'view'			=>	__( 'View', 'usc-screenings' ),
                    'view_item'		=>	__( 'View Screening', 'usc-screenings' ),
                    'search_items'	=>	__( 'Search Screenings', 'usc-screenings' ),
                    'not_found'		=>	__( 'No Screenings found', 'usc-screenings' ),
                    'not_found_in_trash' => __( 'No Screenings found in Trash', 'usc-screenings' ),
                    //'parent'		=>	__( 'Parent APF Post', 'usc-screenings' ),
                    'plugin_listing_table_title_cell_link'	=>	__( 'Screenings', 'usc-screenings' ),		// framework specific key. [3.0.6+]
                ),
                'public'			=>	true,
                'description'       =>  'Screenings at Western Film.  Break out the popcorn!',
                'menu_position' 	=>	6,  //below Posts
                'supports'			=>	array( 'title', 'editor', 'thumbnail', 'excerpt' ), // 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),	// 'custom-fields'
                'taxonomies'		=>	array( '' ),
                'has_archive'		=>	false,
                'show_admin_column' =>	true,	// this is for custom taxonomies to automatically add the column in the listing table.
                'menu_icon'			=>	'dashicons-video-alt2',
                'rewrite'           => array( 'slug' => 'screenings','with_front' => false ),
                // ( framework specific key ) this sets the screen icon for the post type for WordPress v3.7.1 or below.
                //'screen_icon'		=>	'http://testwestern.com/wp-content/plugins/admin-page-framework/asset/image/wp_logo_bw_32x32.png'
            )
        );

        // the setUp() method is too late to add taxonomies. So we use start_{class name} action hook.
        //if we have to http://wordpress.stackexchange.com/questions/140351/how-to-completely-disable-a-taxonomy-archive-on-the-frontend
        $this->addTaxonomy(
            'screenings_status', // taxonomy slug
            array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
                'labels' => array(
                    'name' => 'Statuses',
                    'singular_name' => 'Status',
                    'add_new_item' => 'Add New Status',
                    'new_item_name' => 'New Status'
                ),
                'show_ui' => true,
                'show_tagcloud' => false,
                'hierarchical' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => false,
                'rewrite' => array( 'with_front' => false ),
                'show_table_filter' => true,	// framework specific key
                'show_in_sidebar_menus' => true,	// framework specific key
            )
        );

        add_filter( 'the_content', array( $this, 'replyToPrintOptionValues' ) );

        //add_filter( 'request', array( $this, 'replyToSortCustomColumn' ) );
    }

    /*
    * Method I've largely tried to circumvent. Not really using it.
    */
    public function columns_usc_screenings( $aHeaderColumns ) {	// columns_{post type slug}

        return array_merge(
            $aHeaderColumns,
            array(
                'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions.
                'title'			=> __( 'Title', 'usc-screenings' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
                'author'		=> __( 'Author', 'usc-screenings' ),		// Post author.
                // 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to.
                // 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post.
                'date'			=> __( 'Date', 'usc-screenings' ), 	// The date and publish status of the post.
                //'samplecolumn'			=> __( 'Sample Column' ),
            )
        );

    }

    /**
     * Custom callback methods
     */

    /**
     * Modifies the output of the post content.
     *
     * If we wanted, we could define the output of single 'usc_screenings' here instead of having a template file
     * (it makes use of the 'the_content' filter to insert whatever markup you'd like), but I figured that's probably
     * less intuitive than working with a template file, so I've circumvented this method as well
     *
     * I've come up with a template file called single-usc_screenings, and so that's where to make the changes
     * to the layout of a USC Screening.
     */
    public function replyToPrintOptionValues( $sContent ) {

        return $sContent;

        if ( ! isset( $GLOBALS['post']->ID ) || get_post_type() !== 'usc_screenings' ) return $sContent;

        // 1. To retrieve the meta box data	- get_post_meta( $post->ID ) will return an array of all the meta field values.
        // or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );
        /*
         *
         Possible Meta Values (useless ones are indented)

        @TODO: this is wrong, but we haven't set anything yet
                [_edit_last] => 1
            [apply_by_date] => 2014-08-13 12:00
            [remuneration] => volunteer
            [application_link] => http://33.media.tumblr.com/2d95777547966a733ccdfb3e34afaacc/tumblr_n55qheEABg1qlka8ko1_400.gif
            [job_posting_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
            [job_description_file] => http://testwestern.com/wp-content/uploads/2014/08/Governance.pdf
            [contact_information] => email@westernusc.ca
                [_edit_lock] => 1407311587:1
         */

        $iPostID = $GLOBALS['post']->ID;

        $aPostData = get_post_custom($iPostID);

        $html_string = "";

        /** @TODO: get the departments */

        $subhead = "";

        foreach( $aPostData as $key => $value ) {

            //anything starting with an underscore we don't want.
            if( ! (substr($key, 0, 1) === '_') ) {

                //get the first item of the array
                //var_dump($key . ' => '. $value);
                $value = (string) array_shift($value);

                //get a better title
                $subhead = ucwords(str_replace("_", " ", $key));

                if( filter_var( $value, FILTER_VALIDATE_URL )  ) { //test for a url

                    $html_string .= '<a href="' . esc_url($value) . '" title="click me!"><h3>' . __( $subhead , 'usc-screenings') . '</h3></a>';
                }
                else {

                    $html_string .= '<h3>' . __( $subhead , 'usc-screenings') . '</h3>'
                        . '<p>' . $value . '</p>';
                }
            }
        }

        return $html_string;
    }
}