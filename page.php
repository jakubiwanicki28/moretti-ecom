<?php
/**
 * The template for displaying all pages
 *
 * @package Moretti
 */

get_header(); ?>

<div id="primary" class="content-area bg-white min-h-screen">
    <main id="main" class="site-main">

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="page-header py-16 md:py-24 border-b border-gray-100">
                    <div class="container mx-auto px-4 text-center">
                        <h1 class="text-4xl md:text-6xl font-bold text-charcoal uppercase tracking-tighter leading-none">
                            <?php the_title(); ?>
                        </h1>
                    </div>
                </header>

                <div class="page-content container mx-auto px-4 py-12 md:py-20">
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
