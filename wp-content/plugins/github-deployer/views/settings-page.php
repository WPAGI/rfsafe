<div class="wrap">
<h1>GitHub Deployer</h1>

<h2>GitHub Token</h2>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'gd_save_token' ); ?>
<input type="hidden" name="action" value="gd_save_token" />
<p><input type="text" name="github_token" value="<?php echo esc_attr( $token ); ?>" class="regular-text" /></p>
<p><input type="submit" class="button button-primary" value="Save Token" /></p>
</form>

<?php if ( $error ) : ?>
<p style="color:red;"><?php echo esc_html( $error ); ?></p>
<?php endif; ?>

<?php if ( $token && $repos ) : ?>
<h2>Repositories</h2>
<table class="widefat">
<thead>
<tr>
<th>Repository</th>
<th>Folder</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach ( $repos as $repo ) : $full = $repo['full_name']; ?>
<tr>
<td><?php echo esc_html( $full ); ?></td>
<td>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;">
<?php wp_nonce_field( 'gd_save_mapping' ); ?>
<input type="hidden" name="action" value="gd_save_mapping" />
<input type="hidden" name="repo" value="<?php echo esc_attr( $full ); ?>" />
<input type="text" name="folder" value="<?php echo esc_attr( isset( $mappings[ $full ] ) ? $mappings[ $full ] : '' ); ?>" class="regular-text" />
<button type="button" class="button gd-find-folder" style="margin-left:4px;">Find Folder...</button>
<input type="submit" class="button" value="Save Mapping" />
</form>
</td>
<td>
<?php if ( isset( $mappings[ $full ] ) && $mappings[ $full ] ) : ?>
<a class="button" href="<?php echo esc_url( admin_url( 'admin-post.php?action=gd_deploy_repo&repo=' . rawurlencode( $full ) ) ); ?>">Pull to Server</a>
<?php else : ?>
Set folder first
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<h2>Recent Logs</h2>
<table class="widefat">
<thead>
<tr>
<th>Time</th>
<th>Repository</th>
<th>Folder</th>
<th>Success</th>
<th>Message</th>
</tr>
</thead>
<tbody>
<?php if ( $logs ) : foreach ( $logs as $log ) : ?>
<tr>
<td><?php echo esc_html( $log->time ); ?></td>
<td><?php echo esc_html( $log->repo ); ?></td>
<td><?php echo esc_html( $log->folder ); ?></td>
<td><?php echo $log->success ? 'Yes' : 'No'; ?></td>
<td><?php echo esc_html( $log->message ); ?></td>
</tr>
<?php endforeach; else : ?>
<tr><td colspan="5">No logs found.</td></tr>
<?php endif; ?>
</tbody>
</table>
<div id="gd-folder-modal" style="display:none;"></div>
</div>
