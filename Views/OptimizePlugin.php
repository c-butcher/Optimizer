<?php
/**
 * Optimize Plugin View
 *
 * @var \My\Optimized\Models\PluginInfo $plugin
 * @var \My\Optimized\Helpers\PluginHelper $plugins
 * @var \My\Optimized\Helpers\PluginOptimizer $optimizer
 * @var \My\Optimized\Configurators\ConfiguratorInterface $configurator
 * @var string[] $suggestions
 * @var \My\Optimized\Models\ConfigRevision $lastRevision
 */
?>

<div class="wrap a2-optimized">
	<?php
        self::render( 'Header', array(
            'plugins'   => $plugins,
            'optimizer' => $optimizer,
        ) );
	?>
    <section>
        <h2>
		   <?php echo htmlentities( $plugin->name ); ?>
            <span style="font-size: 0.8em;">
                [ <?php echo __( 'v', 'a2-optimized' ) . htmlentities( $plugin->version ); ?> ]
             </span>
        </h2>
        <p><?php echo htmlentities( $plugin->description ); ?></p>

	    <?php if ( isset( $suggestions ) && count( $suggestions ) > 0 ): ?>
        <h3><?php echo __( "Suggestions for Optimization", 'a2-optimized' ); ?></h3>
        <form method="post">
		   <?php foreach ( $suggestions as $key => $description ): ?>
                 <p>
                     <label>
                         <input type="checkbox" name="optimizations[<?php echo $key; ?>]" checked/>
					 <?php echo htmlentities( $description ); ?>
                     </label>
                 </p>
		   <?php endforeach; ?>

            <p>
                <strong><?php echo __( 'Notice', 'a2-optimized' ); ?></strong> <br/>
			  <?php echo __( 'We will make a backup of the current plugins configuration before making any changes. This will ensure that you can revert your changes back should something break.', 'a2-optimized' ); ?>
            </p>

	        <?php if ( isset( $lastRevision ) ): ?>
                 <a class="button-secondary" href="?page=my_optimized_backups&my_plugin=<?php echo $plugin->name; ?>"><?php echo __( "Restore", 'a2-optimized' ); ?></a>
	        <?php endif; ?>

            <input type="submit" class="button-primary" name="optimize_plugin" value="Optimize"/>
        </form>
        <?php else: ?>
            <h3><?php echo __( "This plugin is running at its optimal settings.", 'a2-optimized' ); ?></h3>
	        <?php if ( isset( $lastRevision ) ): ?>
                <a class="button-secondary" href="?page=my_optimized_backups&my_plugin=<?php echo $plugin->name; ?>"><?php echo __( "Restore", 'a2-optimized' ); ?></a>
	        <?php endif; ?>
        <?php endif; ?>
    </section>
</div>