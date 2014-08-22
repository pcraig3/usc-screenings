<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 20/08/14
 * Time: 12:40 AM
 */

ob_start();

?>

<div class="usc_screenings usc_screenings-shortcode clearfix">

    <?php

    if( '' !== ( $alert = get_post_meta( $post->ID, 'alert', true ) ) )  { ?>
        <div class="usc_screenings__alert">
                <p class="usc_screenings__alert__message etmodules icon_error-triangle usc_screenings__alert__icon"><?php echo esc_html( $alert ); ?></p></div>

    <?php  }  ?>


    <div class="media usc_screenings__content">
        <a href="<?php echo get_permalink(); ?>" class="img usc_screenings__img">
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
        </a>


        <div class="bd">
            <div class="bd__head usc_screenings__title">
                <h2 class="movie_title"><?php echo get_the_title(); //there is ALWAYS a title. ?></h2>
            </div><!-- end .bd__head -->

            <div class="bd__gut usc_screenings__excerpt">
                <p><?php echo get_the_excerpt(); //we are assuming that there will either be an excerpt or description. ?>
                </p>
            </div><!-- end .bd__gut -->

            <div class="bd__foot usc_screenings__rating">

                <?php

                if( '' !== ( $rating = get_post_meta( $post->ID, 'rating', true ) ) )  { ?>
                    <p class="rating"><span class="subhead">Rating:</span><?php echo esc_html( strtoupper( $rating ) ); ?></p>

                <?php  }  ?>

                <?php

                if( '' !== ( $genre = get_post_meta( $post->ID, 'genre', true ) ) )  { ?>
                    <p class="genre"><span class="subhead">Genre:</span><?php echo esc_html( ucwords( $genre ) ); ?></p>

                <?php  }  ?>

                <?php /*

                if( '' !== ( $duration = get_post_meta( $post->ID, 'duration', true ) ) )  { ?>
                    <p class="genre"><span class="subhead">Time:</span><?php echo esc_html( $duration ); ?></p>

                <?php  }*/  ?>

            </div><!-- end .bd__foot -->
        </div><!-- end .bd -->

    </div><!-- end .media -->

    <div class="usc_screenings__showtimes">
        <p class="dates">
            <?php echo esc_html( $this->return_date_range_string( $start_date, get_post_meta( $post->ID, 'end_date', true ) ) ); ?>
        </p>

        <p class="showtimes">
        <?php

        if( '' !== get_post_meta( $post->ID, 'showtimes_repeatable', true ) ) { ?>
            <?php echo '<span class="subhead">Nightly:</span>' . $this->return_showtimes_string( get_post_meta( $post->ID, 'showtimes_repeatable', true ) ); ?>

        <?php  }  ?>
        </p>


        <p class="alternate_showtimes">
            <?php

            if( '' !== get_post_meta( $post->ID, 'if_weekend_showtimes', true ) ) { ?>
                <?php echo $this->return_alternate_showtimes_date_string( get_post_meta( $post->ID, 'if_weekend_showtimes', true ), get_post_meta( $post->ID, 'weekend_showtimes_repeatable', true ) ); ?>

            <?php  }  ?>
        </p>

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
