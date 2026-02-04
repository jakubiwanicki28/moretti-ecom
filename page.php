<?php
/**
 * The template for displaying all pages
 *
 * @package Moretti
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="page-header py-12 md:py-16 bg-cream/30 border-b border-gray-100 mb-8 md:mb-12">
                    <div class="container mx-auto px-4 text-center">
                        <h1 class="text-3xl md:text-5xl font-light text-charcoal uppercase tracking-widest">
                            <?php the_title(); ?>
                        </h1>
                    </div>
                </header>

                <div class="page-content container mx-auto px-4 pb-20">
                    <div class="max-w-6xl mx-auto">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'moretti'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>
                </div>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>
