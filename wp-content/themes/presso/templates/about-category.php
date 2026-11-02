<?php $curcategory = get_the_category(); ?>
<h1 class="post-title title title-large"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php single_cat_title(); ?></a></h1>
<h2 class="post-subtitle subtitle"><?php echo $curcategory[0]->category_description ?></h2>