<?php get_header(); ?>

    <div id="main-content">
        <div class="container">
            <div id="content-area" class="clearfix">
                <div id="left-area">
                    <?php while ( have_posts() ) : the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
                            <h1><?php the_title(); ?> Paul </h1>

                            <?php
                            if ( ! post_password_required() ) :

                                et_divi_post_meta();

                                $thumb = '';

                                $width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );
                                $height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
                                $classtext = 'et_featured_image';
                                $titletext = get_the_title();
                                $thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
                                $thumb = $thumbnail["thumb"];

                                print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height );

                                ?>

                            <?php

                            endif;
                            ?>

                            <div class="entry-content">
                                <?php

                                the_content();

                                $html_string = '';

                                /*
                                So, returning one that exists returns either a string or an array (depending repeatable)
                                Returning one that's empty returns an empty string.
                                Returning a value that was never there returns an empty string

                                var_dump( get_post_meta( $post->ID, 'duration', true ) );

                                var_dump( get_post_meta( $post->ID, 'official_site_link', true ) );

                                var_dump( get_post_meta( $post->ID, 'this_doesnt_exist', true ) );

                                awesome.  Here's the sum total of things a movie has.
                                (indented the stuff we don't care about)

                                array(12) {
                                    ["_edit_lock"]=>
                                    ["_edit_last"]=>
                                ["start_date"]=>            //string
                                ["end_date"]=>              //string
                                ["showtimes_repeatable"]=>  //array
                                ["genre"]=>                 //string
                                ["trailer_link"]=>          //string
                                ["official_site_link"]=>    //string
                                    ["_thumbnail_id"]=>
                                ["duration"]=>              //string
                                ["rating"]=>                //string
                                ["content_advisories"]=>    //string
                                ["alert"]=>                 //string
                                */
                                //print all the meta values here that you can think of.
                                $screening_meta_keys = array(
                                    'start_date',
                                    'end_date',
                                    'showtimes_repeatable',
                                    'genre',
                                    'trailer_link',
                                    'official_site_link',
                                    'duration',
                                    'rating',
                                    'content_advisories',
                                    'alert'
                                );

                                get_post_meta( $post->ID, 'official_site_link', true );


                                ?>
                            </div> <!-- .entry-content -->

                            <?php
                            if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'divi_show_postcomments', 'on' ) )
                                comments_template( '', true );
                            ?>
                        </article> <!-- .et_pb_post -->

                        <?php if (et_get_option('divi_integration_single_bottom') <> '' && et_get_option('divi_integrate_singlebottom_enable') == 'on') echo(et_get_option('divi_integration_single_bottom')); ?>
                    <?php endwhile; ?>
                </div> <!-- #left-area -->

                <?php get_sidebar(); ?>
            </div> <!-- #content-area -->
        </div> <!-- .container -->
    </div> <!-- #main-content -->

<?php get_footer(); ?>