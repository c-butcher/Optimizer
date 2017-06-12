<?php
/**
 * Optimize Plugin View
 *
 * @var \My\Optimized\Models\PluginInfo $plugin
 * @var \My\Optimized\Helpers\PluginHelper $plugins
 * @var \My\Optimized\Helpers\PluginOptimizer $optimizer
 * @var \My\Optimized\Models\ConfigRevision[] $revisions
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
		   <?php echo __( 'Backups for', 'a2-optimized' ); ?>
		   <?php echo htmlentities( $plugin->name ); ?>
        </h2>
        <p><?php echo htmlentities( $plugin->description ); ?></p>


	    <?php if ( isset( $revisions ) && count( $revisions ) > 0 ): ?>
             <form method="post">
                 <table class="table table-options widefat">
                     <thead>
                     <tr>
                         <th></th>
                         <th>Plugin Name</th>
                         <th>Plugin Version</th>
                         <th>Backup Date</th>
                         <th>Actions</th>
                     </tr>
                     </thead>
				  <?php foreach ( $revisions as $revision ): ?>
                          <tr>
                              <td><input type="radio" name="restore_revision" value="<?php echo $revision->id; ?>"/>
                              </td>
                              <td><?php echo $revision->plugin; ?></td>
                              <td><?php echo htmlentities( $plugin->version ); ?></td>
                              <td>
                                   <?php echo date_i18n( "F jS, Y H:ia", $revision->datetime->getTimestamp() ); ?>
                              </td>
                              <td>
                                  <a class="button-link-delete delete-revision"
                                     data-title="Are you sure that you want to delete the backup of the <?php echo $plugin->name; ?> configuration from <?php echo $revision->datetime->format('F jS, Y'); ?> at <?php echo $revision->datetime->format('h:ia'); ?>?"
                                     href="?page=my_optimized_backups&my_plugin=<?php echo $plugin->name; ?>&delete_revision=<?php echo $revision->id; ?>"><?php echo __( 'Delete', 'a2-optimized' ); ?></a>
                              </td>
                          </tr>
				  <?php endforeach; ?>
                 </table>

                 <p>
                     <a class="button-secondary" href="?page=my_optimize_plugin&my_plugin=<?php echo $plugin->name; ?>"><?php echo __( 'Back', 'a2-optimized' ); ?></a>
                     <input type="submit" class="button-primary" name="restore_backup" value="<?php echo __( 'Restore', 'a2-optimized' ); ?>"/>
                 </p>
             </form>
	    <?php else: ?>
             <p>There are no revisions for this plugin</p>
             <a class="button-secondary" href="?page=my_optimize_plugin&my_plugin=<?php echo $plugin->name; ?>"><?php echo __( 'Back', 'a2-optimized' ); ?></a>
	    <?php endif; ?>
    </section>
</div>