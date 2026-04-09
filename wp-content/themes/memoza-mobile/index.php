<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php wp_head(); ?>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; background: #000; color: #fff; overscroll-behavior: none; }
        #root { height: 100%; }
    </style>
</head>
<body <?php body_class(); ?>>
    <div id="root"></div>
    <?php wp_footer(); ?>
</body>
</html>