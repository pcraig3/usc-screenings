<?php get_header(); ?>

    <div id="main-content">


    <div class="et_pb_section et_section_regular" style="background:yellow">



        <div class="et_pb_row">
            <div class="et_pb_column et_pb_column_4_4">
                <a href="<?php echo trailingslashit(site_url()); ?>">
                    <div class="et_pb_text et_pb_bg_layout_light et_pb_text_align_left" style="background:lightblue">
                        Back to Western Film.
                    </div> <!-- .et_pb_text --></a>
            </div> <!-- .et_pb_column -->
        </div> <!-- .et_pb_row -->

    </div>



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

                                <?php

                                } //end of the $official_site_link if statement

                                /* ~trailer_link */
                                $video_url = esc_url( get_post_meta( $post->ID, 'trailer_link', true ) );
                                if(!empty($video_url)) {

                                    echo '<h2>Trailer</h2>';

                                    $shortcode = '[embed]'.$video_url.'[/embed]';
                                    $shortcode = '[embed]http://i.imgur.com/6bphR3T.gif[/embed]';
                                    global $wp_embed;
                                    echo $wp_embed->run_shortcode($shortcode);
                                }

                                ?>

                            </div> <!-- .et_pb_column -->
                            <div class="et_pb_column et_pb_column_2_3">


                                <!-- in here we put the title and all the content-->
                                <h1 class="usc_screenings__title now_playing">
                                    <span title="The Past is playing at Western Film this week!" class="etmodules arrow_triangle-right_alt usc_screenings__title__icon"></span>
                                    <?php the_title(); ?>
                                </h1>
                                <? /* Pretty sure we don't need this: et_divi_post_meta(); */ ?>

                                <div class="entry-content">

                                    <div class="usc_screenings__showtimes clearfix">
                                        <h2>Playing: August 23 - 27</h2>

                                        <div class="showtimes__dates showtimes__dates--left">
                                            <h5>Regular Showtimes</h5>

                                            <p class="showtimes__hours">
                                                <span class="showtime">7:00 pm</span>

                                                <span class="showtime__end">
                                                    (ends at 8:37 pm)
                                                </span>
                                            </p>
                                            <p class="showtimes__hours">
                                                <span class="showtime">10:00 pm</span>

                                                <span class="showtime__end">
                                                    (ends at 11:37 pm)
                                                </span>
                                            </p>

                                        </div>

                                        <div class="showtimes__dates showtimes__dates--right">
                                            <h5>Saturday - Sunday</h5>

                                            <p class="showtimes__hours">
                                                <span class="showtime">2:00 pm</span>

                                                <span class="showtime__end">
                                                    (ends at 3:37 pm)
                                                </span>
                                            </p>

                                        </div>

                                    </div>

                                    <div class="usc_screenings__genre">
                                        <span class="subhead">Genre:</span>
                                        <p>Scary</p>
                                    </div>

                                    <div class="usc_screenings__synopsis">
                                        <span class="subhead">Synopsis:</span>
                                        <?php
                                        //ob_start();
                                        the_content();
                                        //echo ob_get_clean();
                                        ?>
                                    </div>

                                    <div class="usc_screenings__rating">
                                        <span class="subhead">Rating:</span>
                                        <p>14A</p>
                                    </div>

                                    <div class="usc_screenings__content_advisories">
                                        <span class="subhead">Content Advisories:</span>
                                        <p>Smoking, Drinking, Eating, Breathing.</p>
                                    </div>

                                    <?php

                                    /*
                                    $html_string = '';

                                    /*
                                    So, returning one that exists returns either a string or an array (depending repeatable)
                                    Returning one that's empty returns an empty string.
                                    Returning a value that was never there returns an empty string
                                    */
                                    //wp_die(var_dump( get_post_meta( $post->ID, 'weekend_showtimes_repeatable', true ) ));
                                    /*
                                    var_dump( get_post_meta( $post->ID, 'official_site_link', true ) );

                                    var_dump( get_post_meta( $post->ID, 'this_doesnt_exist', true ) );

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
                                    //print all the meta values here that you can think of.
                                    //@TODO: test what happens if there's a day picked with no other time.
                                    /*$screening_meta_keys = array(
                                        'start_date', //0
                                        'end_date',
                                        'showtimes_repeatable', //2
                                        'if_weekend_showtimes',
                                        'weekend_showtimes_repeatable', //4
                                        'genre',
                                        'rating',
                                        'content_advisories',
                                        'alert'
                                    );

                                    //grab them all and just print them.  if statements for two of them.

                                    foreach($screening_meta_keys as &$meta_key) {

                                        $html_string = get_post_meta( $post->ID, $meta_key, true ) ;

                                        if(! empty($html_string) ) {

                                            if( $meta_key === 'if_weekend_showtimes' ) {

                                                $if_weekend_showtimes = $html_string;
                                                $html_string = '';

                                                //basically, create an array of days that have 1s
                                                foreach( $if_weekend_showtimes as $key => $if_weekend_showtime )
                                                    if( 0 === intval( $if_weekend_showtime ))
                                                        unset( $if_weekend_showtimes[$key] );

                                                if( empty($if_weekend_showtimes) )
                                                    unset($screening_meta_keys[4]);

                                                else {

                                                    $if_weekend_showtimes = array_keys( $if_weekend_showtimes );

                                                    $last_day = array_pop( $if_weekend_showtimes );

                                                    $html_string .= implode(", ", $if_weekend_showtimes) . " & " . $last_day;
                                                }

                                            }

                                            if( $meta_key === 'showtimes_repeatable' || $meta_key === 'weekend_showtimes_repeatable' ) {

                                                $showtimes = array_filter( $html_string );
                                                $html_string = '';

                                                foreach( $showtimes as $showtime ) {

                                                    $html_string .= "Start Time: " . $showtime;

                                                    $duration = get_post_meta( $post->ID, 'duration', true ) ;

                                                    if( ! empty( $duration ) ) {

                                                        $minutes = intval( $duration );

                                                        $time = new DateTime( $showtime );
                                                        $time->add(new DateInterval('PT' . $minutes . 'M'));

                                                        $html_string .= " // End Time: " . $time->format('h:i a') . "</p><p>";
                                                    }
                                                }

                                            }
                                            elseif ( $meta_key === 'content_advisories' ) {
                                                //remove empty strings
                                                $content_advisories = array_diff( explode('- ', $html_string), array( '' ));
                                                $html_string = '';

                                                foreach($content_advisories as $advisory){

                                                    $html_string .= '<span style="display:block">' . trim(trim($advisory), "-") . '</span>';
                                                }
                                            }
                                            else {
                                                ;
                                            }

                                            echo '<h4>';
                                            echo ucwords( str_replace( '_', ' ', $meta_key ) );
                                            echo '</h4>';
                                            echo '<p>';
                                            echo $html_string;
                                            echo '</p>';
                                        }
                                    }
                                    unset($meta_key);
                                    */

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