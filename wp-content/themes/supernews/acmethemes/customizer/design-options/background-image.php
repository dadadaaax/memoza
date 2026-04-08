<?php
/*Adding default background image section in new panel */
$wp_customize->get_section( 'background_image' )->panel = 'supernews-design-panel';
$wp_customize->get_section( 'background_image' )->priority = 50;