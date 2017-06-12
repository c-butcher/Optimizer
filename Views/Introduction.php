<?php

/**
 * Introduction View
 *
 * @var \My\Optimized\Helpers\PluginHelper    $plugins
 * @var \My\Optimized\Helpers\PluginOptimizer $optimizer
 */

?>

<div class="wrap a2-optimized">
	<?php
		self::render( 'Header', array(
			'plugins'   => $plugins,
			'optimizer' => $optimizer,
		) );
	?>

    <section id="introduction" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'updates' ); ?>">
        <h2><?php echo __( "Instructions", 'a2-optimized' ); ?></h2>
        <p><?php echo __( "This plugin will help configure your site so that it is optimized to the best abilities.", 'a2-optimized' ); ?></p>

        <h2><?php echo __( 'Suggested Plugins', 'a2-optimized' ); ?></h2>
	    <?php if ( ( $suggestedPlugins = $plugins->getSuggestedPlugins() ) && count( $suggestedPlugins ) > 0 ): ?>
            <p><?php echo __( 'Below are a list of suggested plugins. These plugins have been selected based on the best features and performance.', 'a2-optimized' ); ?></p>

            <?php foreach ( $suggestedPlugins as $plugin ): ?>
             <p id="<?php echo $plugin['slug']; ?>">
                 <input type="submit" name="install_plugin" value="Install Plugin" class="button-primary install-plugin" data-slug="<?php echo $plugin['slug']; ?>" />
                 <?php echo htmlentities( $plugin['name'] ); ?>
                 <?php echo htmlentities( $plugin['description'] ); ?>
             </p>
            <?php endforeach; ?>

        <?php else: ?>
            <p><?php echo __( "You already have all of the suggested plugins installed.", 'a2-optimized' ); ?></p>
        <?php endif; ?>

    </section>
</div>