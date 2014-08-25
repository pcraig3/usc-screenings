<?php

/*
Returning an empty value, or a made up value, returns an empty string.

var_dump( get_post_meta( $post->ID, 'official_site_link', true ) );
var_dump( get_post_meta( $post->ID, 'this_doesnt_exist', true ) );  // empty string

awesome.  Here's the sum total of things a movie has.
(indented the stuff we don't care about)

array(12) {
    ["_edit_lock"]=>
    ["_edit_last"]=>
["start_date"]=>                    //string
["end_date"]=>                      //string
["showtimes_repeatable"]=>          //array
["if_weekend_showtimes"]=>          //array
["weekend_showtimes_repeatable"]=>  //array
["genre"]=>                         //string
["trailer_link"]=>                  //string
["official_site_link"]=>            //string
    ["_thumbnail_id"]=>
["duration"]=>                      //string
["rating"]=>                        //string
["content_advisories"]=>            //string
["alert"]=>                         //string
*/

get_header(); ?>

    <div id="main-content">


    <div id="back_to_home" class="et_pb_section et_section_regular">

        <div class="et_pb_row">
            <div class="et_pb_column et_pb_column_1_3">

                <a class="etmodules arrow_back" href="<?php echo trailingslashit(site_url()); ?>">
                        Back to Western Film
                </a>
            </div> <!-- .et_pb_column -->
        </div> <!-- .et_pb_row -->

    </div><!-- #back_to_home -->



    <div class="container">
    <div id="content-area" class="clearfix">
    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>


        <div class="et_pb_section et_section_regular">



        <div class="et_pb_row">
        <div class="et_pb_column et_pb_column_1_3">

            <!-- in here we put the image and the trailer and the official site. -->

            <?php


            $thumb = '';

            $width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );
            $height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
            $classtext = 'et_featured_image';
            $titletext = get_the_title();
            $thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
            $thumb = $thumbnail["thumb"];

            print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height );

            /* ~official_site_link */
            $official_site_link = esc_url( get_post_meta( $post->ID, 'official_site_link', true ) );

            if( !empty( $official_site_link ) ) { ?>
                <a class="usc_screenings__link" href="<?php echo  $official_site_link; ?>" alt="link to official site" target="_blank">Official Site</a>
                <!-- end of .usc_screenings__official_site_link-->

            <?php

            } //end of the $official_site_link if statement

            /* ~trailer_link */
            $video_url = esc_url( get_post_meta( $post->ID, 'trailer_link', true ) );
            if(!empty($video_url)) {

                echo '<div class="usc_screenings__trailer_link"><h2>Trailer</h2>';

                $shortcode = '[embed]'.$video_url.'[/embed]';
                global $wp_embed;
                echo $wp_embed->run_shortcode($shortcode);
                echo '</div><!-- end of .usc_screenings__trailer_link-->';
            }

            ?>

        </div> <!-- .et_pb_column -->
        <div class="et_pb_column et_pb_column_2_3">


            <?php

            if( '' !== ( $alert = get_post_meta( $post->ID, 'alert', true ) ) )  { ?>
            <div class="usc_screenings__alert">
                <p class="usc_screenings__alert__message etmodules icon_error-triangle usc_screenings__alert__icon"><?php echo esc_html( $alert ); ?></p>
            </div><!-- end of .usc_screenings__alert -->

            <?php  }  ?>

            <?php

            /* ~start_date and ~end_date used to decide whether this film is playing this week or not. */
            $start_date = get_post_meta( $post->ID, 'start_date', true );
            $end_date = get_post_meta( $post->ID, 'end_date', true );

            $is_playing_this_week = USC_Screenings::get_instance()->is_playing_this_week( $start_date, $end_date );

            ?>

            <!-- in here we put the title and all the content-->
            <h1 class="usc_screenings__title <?php echo ($is_playing_this_week) ? 'now_playing' : ''; ?>">
                <span title="<?php the_title(); ?> is playing at Western Film this week!" class="etmodules icon_box-checked usc_screenings__title__icon"></span>
                <?php the_title(); ?>
            </h1><!-- end of .usc_screenings__title -->
            <? /* Pretty sure we don't need this: et_divi_post_meta(); */ ?>

            <div class="entry-content">

                <div class="usc_screenings__showtimes clearfix">
                    <h2>
                        <?php

                        /* ~start_date and ~end_date fed into a date string here.  If neither are set, skip the showtimes. */
                        $date_string = USC_Screenings::get_instance()->return_date_range_string( $start_date, $end_date );

                        if( empty( $date_string )) {

                            echo "No idea when this will be playing.</h2>";
                        }
                        else {

                        echo $date_string;
                        ?>
                    </h2>

                    <?php

                    //returns empty string if dates array is empty
                    $alternative_showtimes_dates = USC_Screenings::get_instance()->return_alternate_showtimes_date_string( get_post_meta( $post->ID, 'if_weekend_showtimes', true ) );


                    /* If there are no alternative showtimes, then we don't need this section to break into two sections. */
                    ?>
                    <div class="showtimes__dates <?php echo ( !empty( $alternative_showtimes_dates ) ) ? 'showtimes__dates--left' : ''; ?>">
                        <h5>Regular Showtimes</h5>

                        <?php

                        //array_filter removes "" empty elements http://stackoverflow.com/questions/3654295/remove-empty-array-elements
                        $showtimes_repeatable = array_filter(get_post_meta( $post->ID, 'showtimes_repeatable', true ));

                        $duration = get_post_meta( $post->ID, 'duration', true );

                        echo USC_Screenings::get_instance()->generate_usc_screenings_single_showtimes_HTML(
                            $showtimes_repeatable, $duration);

                        ?>

                    </div>

                    <?php

                    //if this is an empty string, we don't have any alternative showtimes.
                    if( !empty( $alternative_showtimes_dates ) ) {

                        ?>

                        <div class="showtimes__dates showtimes__dates--right">
                            <h5><?php echo $alternative_showtimes_dates?></h5>

                            <?php

                            //array_filter removes "" empty elements http://stackoverflow.com/questions/3654295/remove-empty-array-elements
                            $weekend_showtimes_repeatable = array_filter(get_post_meta( $post->ID, 'weekend_showtimes_repeatable', true ));

                            echo USC_Screenings::get_instance()->generate_usc_screenings_single_showtimes_HTML(
                                $weekend_showtimes_repeatable, $duration);

                            ?>

                        </div>

                    <?php
                    }
                    ?>

                    <?php } //this is the end of the dates section ?>
                </div><!-- end of .usc_screenings__showtimes -->

                <?php
                if( '' !== ( $genre = esc_html( get_post_meta( $post->ID, 'genre', true ) ) ) ) { ?>
                    <div class="usc_screenings__genre">
                        <span class="subhead">Genre:</span>
                        <p>
                            <?php echo esc_html( ucwords( $genre ) ); ?>
                        </p>
                    </div><!-- end of .usc_screenings__genre -->

                <?php  }  ?>

                <div class="usc_screenings__synopsis">
                    <span class="subhead">Synopsis:</span>
                    <?php
                    //ob_start();
                    the_content();
                    //echo ob_get_clean();
                    ?>
                </div><!-- end of .usc_screenings__synopsis -->

                <?php
                if( '' !== ( $rating = esc_html( get_post_meta( $post->ID, 'rating', true ) ) ) )  { ?>
                    <div class="usc_screenings__rating">
                        <span class="subhead">Rating:</span>
                        <p>
                            <?php echo strtoupper( $rating ); ?>
                        </p>
                    </div><!-- end of .usc_screenings__rating -->

                <?php  }  ?>

                <?php
                if( '' !== ( $content_advisories = esc_html( get_post_meta( $post->ID, 'content_advisories', true ) ) ) )  {

                    $content_advisories = array_map('trim', array_filter( explode('- ', $content_advisories) ) );

                    $html_string = '';

                    foreach($content_advisories as $advisory){

                        $html_string .= '<span style="display:block">' . trim(trim($advisory), "-") . '</span>';
                    }

                    //$html_string = implode(", ", $content_advisories);

                    ?>
                    <div class="usc_screenings__content_advisories">
                        <span class="subhead">Content Advisories:</span>
                        <p>
                            <?php echo $html_string; ?>
                        </p>
                    </div><!-- end of .usc_screenings__content_advisories -->

                <?php  }


                get_post_meta( $post->ID, 'official_site_link', true );

                ?>
            </div> <!-- .entry-content -->


        </div> <!-- .et_pb_column -->
        </div> <!-- .et_pb_row -->

        </div>



        </article> <!-- .et_pb_post -->

        <?php if (et_get_option('divi_integration_single_bottom') <> '' && et_get_option('divi_integrate_singlebottom_enable') == 'on') echo(et_get_option('divi_integration_single_bottom')); ?>
    <?php endwhile; ?>

    </div> <!-- #content-area -->
    </div> <!-- .container -->
    </div> <!-- #main-content -->

<?php get_footer(); ?>