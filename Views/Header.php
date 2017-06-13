<?php
/**
 * Header Partial View
 *
 * @var \My\Optimized\Helpers\PluginHelper $plugins
 * @var \My\Optimized\Helpers\PluginOptimizer $optimizer
 */

use My\Optimized\Helpers\Logger;
?>

<header>
    <h1><?php echo __( 'My Optimizer', 'a2-optimizer' ); ?></h1>

    <?php
        $logger = Logger::getInstance();
        self::render( 'Notices', array(
            'successes' => $logger->get( 'success' ),
            'errors'    => $logger->get( 'error' ),
            'warnings'  => $logger->get( 'warning' ),
	        'notices'   => $logger->get( 'notice' ),
        ) );
    ?>

    <menu id="configurators" class="configurators">
	    <?php foreach ( $plugins->getAll() as $plugin ): ?>
		    <?php if ( $optimizer->hasConfigurator( $plugin->name ) ): ?>
                 <a class="<?php echo str_replace(' ', '_', strtolower($plugin->name)); ?>" href="<?php echo admin_url( '?page=my_optimize_plugin&my_plugin=' . $plugin->name ); ?>"><i></i><?php echo $plugin->name; ?></a>
		    <?php endif; ?>
	    <?php endforeach; ?>
    </menu>
</header>
