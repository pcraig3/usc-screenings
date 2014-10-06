<?php
/**
 * Defines the HTML to be generated for a listing  of screenings
 */

ob_start();

?>

<div class="usc_screenings shortcode-usc_screenings clearfix">

    <?php

    if( '' !== ( $alert = esc_html( get_post_meta( $post->ID, 'alert', true ) ) ) ) { ?>
        <div class="usc_screenings__alert">
            <p class="usc_screenings__alert__message etmodules icon_error-triangle usc_screenings__alert__icon"><?php echo $alert; ?></p>
        </div><!-- end of .usc_screenings__alert -->

    <?php  }  ?>

    <div class="media usc_screenings__content">
        <a href="<?php echo get_permalink(); ?>" class="img usc_screenings__img">
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
        </a><!-- end of .usc_screenings__img -->

        <div class="bd">
            <a href="<?php echo get_permalink(); ?>">
                <div class="usc_screenings__title">
                    <h2 class="movie_title"><?php echo get_the_title(); //there is ALWAYS a title. ?></h2>
                </div><!-- end .usc_screenings__title -->
            </a>

            <div class="usc_screenings__excerpt">
                <p><?php echo get_the_excerpt(); //we are assuming that there will either be an excerpt or description. ?>
                </p>
            </div><!-- end .usc_screenings__excerpt -->

            <div class="usc_screenings__context">

                <?php

                if( '' !== ( $rating = esc_html( get_post_meta( $post->ID, 'rating', true ) ) ) ) { ?>
                    <p class="usc_screenings__rating"><span class="subhead">Rating:</span><?php echo strtoupper( $rating ); ?></p>
                    <!-- end of .usc_screenings__rating-->

                <?php  }  ?>

                <?php

                if( '' !== ( $genre = esc_html( get_post_meta( $post->ID, 'genre', true ) ) ) ) { ?>
                    <p class="usc_screenings__genre"><span class="subhead">Genre:</span><?php echo ucwords( $genre ); ?></p>
                    <!-- end of .usc_screenings__genre -->

                <?php  }  ?>

                <?php /*

                if( '' !== ( $duration = esc_html( get_post_meta( $post->ID, 'duration', true ) ) ) ) { ?>
                    <p class="genre"><span class="subhead">Time:</span><?php echo $duration; ?></p>

                <?php  }*/  ?>

            </div><!-- end .usc_screenings__context -->
        </div><!-- end .bd -->

    </div><!-- end .media -->


    <div class="usc_screenings__showtimes">
        <?php

        $date_string = esc_html( USC_Screenings::get_instance()
            ->return_date_range_string( $start_date, get_post_meta( $post->ID, 'end_date', true ) ) );

        if( empty( $date_string )) {

            echo '<p class="dates">Not sure when this will be playing.</p>';
        }
        else {

            echo '<p class="dates">' . $date_string . '</p>';

            ?>


            <p class="showtimes">
                <?php

                if( '' !== get_post_meta( $post->ID, 'showtimes_repeatable', true ) ) { ?>
                    <?php echo '<span class="subhead">Reg. Showtimes:</span>' . USC_Screenings::get_instance()
                            ->generate_usc_screenings_shortcode_showtimes_HTML( get_post_meta( $post->ID, 'showtimes_repeatable', true ) ); ?>

                <?php  }  ?>
            </p>

            <?php

            //returns empty string if dates array is empty
            $alternative_showtimes_dates = USC_Screenings::get_instance()->return_alternate_showtimes_date_string( get_post_meta( $post->ID, 'if_weekend_showtimes', true ), 3 );
            $weekend_showtimes_repeatable = array_filter(get_post_meta( $post->ID, 'weekend_showtimes_repeatable', true ));

            if( !empty( $alternative_showtimes_dates ) && !empty( $weekend_showtimes_repeatable ) ) {
                ?>
                <p class="alternate_showtimes">
                    <span class="subhead"><?php echo $alternative_showtimes_dates; ?>:</span><?php echo USC_Screenings::get_instance()
                        ->generate_usc_screenings_shortcode_showtimes_HTML( $weekend_showtimes_repeatable ); ?>
                </p>

            <?php
            }  ?>

        <?php  } //end of $date_string else statement ?>

    </div><!-- end .usc_screenings__showtimes -->

    <div class="usc_screenings__links">

        <?php

        $official_site_link = esc_url( get_post_meta( $post->ID, 'official_site_link', true ) );

        if( !empty( $official_site_link ) ) { ?>
            <a class="usc_screenings__link" href="<?php echo  $official_site_link; ?>" alt="link to official site" target="_blank">Official Site</a>

        <?php

        } //end of the $official_site_link if statement

        $trailer_link = esc_url( get_post_meta( $post->ID, 'trailer_link', true ) );

        if( !empty( $trailer_link ) ) { ?>
            <a class="usc_screenings__link" href="<?php echo $trailer_link; ?>" alt="link to trailer" target="_blank">View Trailer</a>

        <?php

        } //end of the $trailer_link if statement

        ?>

    </div><!-- end .usc_screening__links -->



</div><!-- end of .usc_screening-shortcode -->



<?php

return ob_get_clean();

?>
