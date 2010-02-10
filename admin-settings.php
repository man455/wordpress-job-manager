<?php
function jobman_conf() {
	global $jobman_formats;
	if( array_key_exists( 'jobmanconfsubmit', $_REQUEST ) ) {
		// Configuration form as been submitted. Updated the database.
		check_admin_referer( 'jobman-conf-updatedb' );
		jobman_conf_updatedb();
	}
	else if( array_key_exists( 'jobmancatsubmit', $_REQUEST ) ) {
		check_admin_referer( 'jobman-categories-updatedb' );
		jobman_categories_updatedb();
	}
	else if( array_key_exists( 'jobmaniconsubmit', $_REQUEST ) ) {
		check_admin_referer( 'jobman-icons-updatedb' );
		jobman_icons_updatedb();
	}
	else if( array_key_exists( 'jobmanusersubmit', $_REQUEST ) ) {
		check_admin_referer( 'jobman-users-updatedb' );
		jobman_users_updatedb();
	}
	else if( array_key_exists( 'jobmanappemailsubmit', $_REQUEST ) ) {
		check_admin_referer( 'jobman-application-email-updatedb' );
		jobman_application_email_updatedb();
	}
	else if( array_key_exists( 'jobmanotherpluginssubmit', $_REQUEST ) ) {
		check_admin_referer( 'jobman-other-plugins-updatedb' );
		jobman_other_plugins_updatedb();
	}
?>
	<div class="wrap">
		<h2><?php _e( 'Job Manager: Admin Settings', 'jobman' ) ?></h2>
<?php
	$writeable = jobman_check_upload_dirs();
	if( ! $writeable ) {
		echo '<div class="error">';
		echo '<p>' . __( 'It seems the Job Manager data directories are not writeable. In order to allow applicants to upload resumes, and for you to upload icons, please ensure that the following directories exist and are writeable.', 'jobman' ) . '</p>';
		echo '<pre>' . JOBMAN_UPLOAD_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . "\n";
		echo JOBMAN_UPLOAD_DIR . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . '</pre>';
		echo '<p>' . sprintf( __( 'For help with changing directory permissions, please see <a href="%1s">this page</a> in the WordPress documentation.', 'jobman' ), 'http://codex.wordpress.org/Changing_File_Permissions' ) . '</p>';
		echo '</div>';
	}

	if( ! get_option( 'pento_consulting' ) ) {
		$widths = array( '78%', '20%' );
		$functions = array(
						array( 'jobman_print_settings_box', 'jobman_print_categories_box', 'jobman_print_icons_box', 'jobman_print_user_box', 'jobman_print_application_email_box', 'jobman_print_other_plugins_box' ),
						array( 'jobman_print_donate_box', 'jobman_print_about_box' )
					);
		$titles = array(
					array( __( 'Settings', 'jobman' ), __( 'Categories', 'jobman' ), __( 'Icons', 'jobman' ), __( 'User Settings', 'jobman' ), __( 'Application Email Settings', 'jobman' ), __('Other Plugins', 'jobman' ) ),
					array( __( 'Donate', 'jobman' ), __( 'About This Plugin', 'jobman' ))
				);
	}
	else {
		$widths = array( '49%', '49%' );
		$functions = array(
						array( 'jobman_print_settings_box', 'jobman_print_categories_box', 'jobman_print_other_plugins_box' ),
						array( 'jobman_print_icons_box', 'jobman_print_user_box', 'jobman_print_application_email_box' )
					);
		$titles = array(
					array( __( 'Settings', 'jobman' ), __( 'Categories', 'jobman' ), __( 'Other Plugins', 'jobman' ) ),
					array( __( 'Icons', 'jobman' ), __( 'User Settings', 'jobman' ), __( 'Application Email Settings', 'jobman' ) )
				);
	}
	jobman_create_dashboard( $widths, $functions, $titles );
}

function jobman_print_settings_box() {
	$options = get_option( 'jobman_options' );
	$structure = get_option( 'permalink_structure' );
	?>
		<form action="" method="post">
		<input type="hidden" name="jobmanconfsubmit" value="1" />
<?php 
	wp_nonce_field( 'jobman-conf-updatedb' ); 
?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'URL path', 'jobman' ) ?></th>
				<td colspan="2">
					<a href="<?php echo get_page_link( $options['main_page'] ) ?>"><?php echo get_page_link( $options['main_page'] ) ?></a> 
					(<a href="<?php echo admin_url("page.php?action=edit&post={$options['main_page']}" ) ?>"><?php _e( 'edit', 'jobman' ) ?></a>)
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Job Manager Page Template', 'jobman' ) ?></th>
				<td colspan="2"><?php printf( __( 'You can edit the page template used by Job Manager, by editing the Template Attribute of <a href="%s">this page</a>.', 'jobman' ), admin_url( 'page.php?action=edit&post=' . $options['main_page'] ) ) ?></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Default email', 'jobman' ) ?></th>
				<td colspan="2"><input class="regular-text code" type="text" name="default-email" value="<?php echo $options['default_email'] ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Show summary or full jobs list?', 'jobman' ) ?></th>
				<td><select name="list-type">
					<option value="summary"<?php echo ( 'summary' == $options['list_type'] )?( ' selected="selected"' ):( '' ) ?>><?php _e( 'Summary', 'jobman' ) ?></option>
					<option value="full"<?php echo ( 'full' == $options['list_type'] )?( ' selected="selected"' ):( '' ) ?>><?php _e( 'Full', 'jobman' ) ?></option>
				</select></td>
				<td><span class="description">
					<?php _e( 'Summary: displays many jobs concisely.', 'jobman' ) ?><br/>
					<?php _e( 'Full: allows quicker access to the application form.', 'jobman' ) ?>
				</span></td>
			</tr>
<?php
	if( ! get_option( 'pento_consulting' ) ) {
?>
			<tr>
				<th scope="row"><?php _e( 'Hide "Powered By" link?', 'jobman' ) ?></th>
				<td><input type="checkbox" value="1" name="promo-link" <?php echo ( $options['promo_link'] )?( 'checked="checked" ' ):( '' ) ?>/></td>
				<td><span class="description"><?php _e( "If you're unable to donate, I would appreciate it if you left this unchecked.", 'jobman' ) ?></span></td>
			</tr>
<?php
	}
?>
		</table>
		
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e( 'Update Settings', 'jobman' ) ?>" /></p>
		</form>
<?php
}

function jobman_print_categories_box() {
	$options = get_option( 'jobman_options' );
?>
		<p><?php _e( 'Similar to the normal WordPress Categories, Job Manager categories can be used to split jobs into different groups. They can also be used to customise how the Application Form appears for jobs in different categories.', 'jobman' ) ?></p>
		<p>
			<strong><?php _e( 'Title', 'jobman' ) ?></strong> - <?php _e( 'The display name of the category', 'jobman' ) ?><br/>
			<strong><?php _e( 'Slug', 'jobman' ) ?></strong> - <?php _e( 'The URL of the category', 'jobman' ) ?><br/>
			<strong><?php _e( 'Email', 'jobman' ) ?></strong> - <?php _e( 'The address to notify when new applications are submitted in this category', 'jobman' ) ?><br/>
			<strong><?php _e( 'Link', 'jobman' ) ?></strong> - <?php _e( 'The URL of the list of jobs in this category', 'jobman' ) ?>
		</p>
		<form action="" method="post">
		<input type="hidden" name="jobmancatsubmit" value="1" />
<?php 
	wp_nonce_field( 'jobman-categories-updatedb' ); 
?>
		<table class="widefat page fixed" cellspacing="0">
			<thead>
			<tr>
				<th scope="col"><?php _e( 'Title', 'jobman' ) ?></th>
				<th scope="col"><?php _e( 'Slug', 'jobman' ) ?></th>
				<th scope="col"><?php _e( 'Email', 'jobman' ) ?></th>
				<th scope="col"><?php _e( 'Link', 'jobman' ) ?></th>
				<th scope="col" class="jobman-fielddelete"><?php _e( 'Delete', 'jobman' ) ?></th>
			</tr>
			</thead>
<?php
	$categories = get_terms( 'jobman_category', 'hide_empty=0' );
	
	if( count( $categories ) > 0 ) {
		foreach( $categories as $cat ) {
			$url = get_term_link( $cat->slug, 'jobman_category' );
?>
			<tr>
				<td>
					<input type="hidden" name="id[]" value="<?php echo $cat->term_id ?>" />
					<input class="regular-text code" type="text" name="title[]" value="<?php echo $cat->name ?>" />
				</td>
				<td><input class="regular-text code" type="text" name="slug[]" value="<?php echo $cat->slug ?>" /></td>
				<td><input class="regular-text code" type="text" name="email[]" value="<?php echo $cat->description ?>" /></td>
				<td><a href="<?php echo $url ?>"><?php _e( 'Link', 'jobman' ) ?></a></td>
				<td><a href="#" onclick="jobman_delete( this, 'id', 'jobman-delete-category-list' ); return false;"><?php _e( 'Delete', 'jobman' ) ?></a></td>
			</tr>
<?php
		}
	}
	
	$template = '<tr><td><input type="hidden" name="id[]" value="-1" />';
	$template .= '<input class="regular-text code" type="text" name="title[]" /></td>';
	$template .= '<td><input class="regular-text code" type="text" name="slug[]" /></td>';
	$template .= '<td><input class="regular-text code" type="text" name="email[]" /></td>';
	$template .= '<td>&nbsp;</td>';
	$template .= '<td><a href="#" onclick="jobman_delete( this, \\\'id\\\', \\\'jobman-delete-category-list\\\' ); return false;">' . __( 'Delete', 'jobman' ) . '</a></td>';
	
	echo $template;
?>
			<tr id="jobman-catnew">
					<td colspan="5" style="text-align: right;">
						<input type="hidden" name="jobman-delete-list" id="jobman-delete-category-list" value="" />
						<a href="#" onclick="jobman_new( 'jobman-catnew', 'category' ); return false;"><?php _e( 'Add New Category', 'jobman' ) ?></a>
					</td>
			</tr>
		</table>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Show related categories?', 'jobman' ) ?></th>
				<td><input type="checkbox" name="related-categories" <?php echo ( $options['related_categories'] )?( 'checked="checked" ' ):( '' ) ?>/></td>
				<td><span class="description"><?php _e( 'This will show a list of categories that any jobs in a given job list belong to.', 'jobman' ) ?></span></td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e( 'Update Categories', 'jobman' ) ?>" /></p>
<script type="text/javascript"> 
//<![CDATA[
	jobman_templates['category'] = '<?php echo $template ?>';
//]]>
</script> 
		</form>
<?php
}

function jobman_print_icons_box() {
	$options = get_option( 'jobman_options' );
?>
		<p><?php _e( 'Icons can be assigned to jobs that you want to draw attention to. These icons will only be displayed when using the "Summary" jobs list type.', 'jobman' ) ?></p>
		<p>
			<strong><?php _e( 'Icon', 'jobman' ) ?></strong> - <?php _e( 'The current icon', 'jobman' ) ?><br/>
			<strong><?php _e( 'Title', 'jobman' ) ?></strong> - <?php _e( 'The display name of the icon', 'jobman' ) ?><br/>
			<strong><?php _e( 'File', 'jobman' ) ?></strong> - <?php _e( 'The icon file', 'jobman' ) ?><br/>
		</p>
		<form action="" enctype="multipart/form-data" method="post">
		<input type="hidden" name="jobmaniconsubmit" value="1" />
<?php 
	wp_nonce_field( 'jobman-icons-updatedb' ); 
?>
		<table class="widefat page fixed" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" class="jobman-icon"><?php _e( 'Icon', 'jobman' ) ?></th>
				<th scope="col"><?php _e( 'Title', 'jobman' ) ?></th>
				<th scope="col"><?php _e( 'File', 'jobman' ) ?></th>
				<th scope="col" class="jobman-fielddelete"><?php _e( 'Delete', 'jobman' ) ?></th>
			</tr>
			</thead>
<?php
	$icons = $options['icons'];
	
	if( count( $icons ) > 0 ) {
		foreach( $icons as $id => $icon ) {
?>
			<tr>
				<td>
					<input type="hidden" name="id[]" value="<?php echo $id ?>" />
					<img src="<?php echo JOBMAN_UPLOAD_URL . "/icons/$id.{$icon['extension']}" ?>" />
				</td>
				<td><input class="regular-text code" type="text" name="title[]" value="<?php echo $icon['title'] ?>" /></td>
				<td><input class="regular-text code" type="file" name="icon[]" /></td>
				<td><a href="#" onclick="jobman_delete( this, 'id', 'jobman-delete-icon-list' ); return false;"><?php _e( 'Delete', 'jobman' ) ?></a></td>
			</tr>
<?php
		}
	}
	
	$template = '<tr><td><input type="hidden" name="id[]" value="-1" /></td>';
	$template .= '<td><input class="regular-text code" type="text" name="title[]" /></td>';
	$template .= '<td><input class="regular-text code" type="file" name="icon[]" /></td>';
	$template .= '<td><a href="#" onclick="jobman_delete( this, \\\'id\\\', \\\'jobman-delete-icon-list\\\' ); return false;">' . __( 'Delete', 'jobman' ) . '</a></td>';
	
	echo $template;
?>
		<tr id="jobman-iconnew">
				<td colspan="4" style="text-align: right;">
					<input type="hidden" name="jobman-delete-list" id="jobman-delete-icon-list" value="" />
					<a href="#" onclick="jobman_new( 'jobman-iconnew', 'icon' ); return false;"><?php _e( 'Add New Icon', 'jobman' ) ?></a>
				</td>
		</table>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e( 'Update Icons', 'jobman' ) ?>" /></p>
<script type="text/javascript"> 
//<![CDATA[
	jobman_templates['icon'] = '<?php echo $template ?>';
//]]>
</script> 
		</form>
<?php
}

function jobman_print_user_box() {
	$options = get_option( 'jobman_options' );
?>
		<p><?php _e( 'Allowing users to register means that they and you can more easily keep track of jobs they\'ve applied for.', 'jobman' ) ?></p>
		<form action="" method="post">
		<input type="hidden" name="jobmanusersubmit" value="1" />
<?php 
	wp_nonce_field( 'jobman-users-updatedb' ); 
?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Enable User Registration', 'jobman' ) ?></th>
				<td><input type="checkbox" value="1" name="user-registration" <?php echo ( $options['user_registration'] )?( 'checked="checked" ' ):( '' ) ?>/></td>
				<td><span class="description"><?php _e( 'This will allow users to register for the Jobs system, even if user registration is disabled for your blog.', 'jobman' ) ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Require User Registration', 'jobman' ) ?></th>
				<td><input type="checkbox" value="1" name="user-registration-required" <?php echo ( $options['user_registration_required'] )?( 'checked="checked" ' ):( '' ) ?>/></td>
				<td><span class="description"><?php _e( 'If the previous option is checked, this option will require users to login before they can complete the application form.', 'jobman' ) ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Which pages should the login form be displayed on?', 'jobman' ) ?></th>
				<td colspan="2">
					<input type="checkbox" value="1" name="loginform-main" <?php echo ( $options['loginform_main'] )?( 'checked="checked" ' ):( '' ) ?>/> <?php _e( 'The main jobs list', 'jobman' ) ?><br />
					<input type="checkbox" value="1" name="loginform-category" <?php echo ( $options['loginform_category'] )?( 'checked="checked" ' ):( '' ) ?>/> <?php _e( 'Category jobs lists', 'jobman' ) ?><br />
					<input type="checkbox" value="1" name="loginform-job" <?php echo ( $options['loginform_job'] )?( 'checked="checked" ' ):( '' ) ?>/> <?php _e( 'Individual jobs', 'jobman' ) ?><br />
					<input type="checkbox" value="1" name="loginform-apply" <?php echo ( $options['loginform_apply'] )?( 'checked="checked" ' ):( '' ) ?>/> <?php _e( 'The application form', 'jobman' ) ?><br />
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e( 'Update User Settings', 'jobman' ) ?>" /></p>
		</form>
<?php
}

function jobman_print_application_email_box() {
	$options = get_option( 'jobman_options' );
	
	$fields = $options['fields'];
?>
		<p><?php _e( 'When an application successfully submits an application, an email will be sent to the appropriate user. These options allow you to customise that email.', 'jobman' ) ?></p>
		<form action="" method="post">
		<input type="hidden" name="jobmanappemailsubmit" value="1" />
<?php 
	wp_nonce_field( 'jobman-application-email-updatedb' ); 
?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Email Address', 'jobman' ) ?></th>
				<td><select name="jobman-from">
					<option value=""><?php _e( 'None', 'jobman' ) ?></option>
<?php
	$fid = $options['application_email_from'];
	if( count( $fields ) > 0 ) {
		foreach( $fields as $id => $field ) {
			if( 'text' == $field['type'] || 'textarea' == $field['type'] ) {
				$selected = '';
				if( $id == $fid ) {
					$selected = ' selected="selected"';
				}
?>
					<option value="<?php echo $id ?>"<?php echo $selected ?>><?php echo $field['label'] ?></option>
<?php
			}
		}
	}
?>
				</select></td>
				<td><span class="description"><?php _e( 'The application field to use as the email address. This will be the "From" address in the initial application, and the field used for emailing applicants.', 'jobman' ) ?></span></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Subject', 'jobman' ) ?></th>
				<td>
					<input class="regular-text code" type="text" name="jobman-subject-text" value="<?php echo $options['application_email_subject_text'] ?>" /><br/>
					<select name="jobman-subject-fields[]" multiple="multiple" size="5" class="multiselect">
					<option value="" style="font-weight: bold; border-bottom: 1px solid black;"><?php _e( 'None', 'jobman' ) ?></option>
<?php
	$fids = $options['application_email_subject_fields'];
	if( count( $fields ) > 0 ) {
		foreach( $fields as $id => $field ) {
			if( 'text' == $field['type'] || 'textarea' == $field['type'] ) {
				$selected = '';
				if( in_array( $id, $fids ) ) {
					$selected = ' selected="selected"';
				}
?>
					<option value="<?php echo $id ?>"<?php echo $selected ?>><?php echo $field['label'] ?></option>
<?php
			}
		}
	}
?>
					</select>
				</td>
				<td><span class="description"><?php _e( 'The email subject, and any fields to include in the subject.', 'jobman' ) ?></span></td>
			</tr>
		</table>
		
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e( 'Update Email Settings', 'jobman' ) ?>" /></p>
		</form>
<?php
}

function jobman_print_other_plugins_box() {
?>
	<p><?php _e( 'Job Manager provides extra functionality through the use of other plugins available for WordPress. These plugins are not required for Job Manager to function, but do provide enhancements.', 'jobman' ) ?></p>
	<form action="" method="post">
	<input type="hidden" name="jobmanotherpluginssubmit" value="1" />
<?php
	wp_nonce_field( 'jobman-other-plugins-updatedb' );

	if( class_exists( 'GoogleSitemapGeneratorLoader' ) ) {
		$gxs = true;
		$gxs_status = __( 'Installed', 'jobman' );
		$gxs_version = GoogleSitemapGeneratorLoader::GetVersion();
	}
	else {
		$gxs = false;
		$gxs_status = __( 'Not Installed', 'jobman' );
	}
?>
		<h4><?php _e( 'Google XML Sitemaps', 'jobman' ) ?></h4>
		<p><?php _e( 'Allows you to automatically add all your job listing and job detail pages to your sitemap. By default, only the main job list is added.', 'jobman' ) ?></p>
		<p>
			<a href="http://wordpress.org/extend/plugins/google-sitemap-generator/"><?php _e( 'Download', 'jobman' ) ?></a><br/>
			<?php _e( 'Status', 'jobman' ) ?>: <span class="<?php echo ( $gxs )?( 'pluginokay' ):( 'pluginwarning' ) ?>"><?php echo $gxs_status ?></span><br/>
			<?php echo ( $gxs )?( __( 'Version', 'jobman' ) . ": $gxs_version" ):( '' ) ?>
			<?php echo ( ! $gxs || version_compare( $gxs_version, '3.2', '<' ) )?( ' <span class="pluginwarning">' . __( 'Job Manager requires Google XML Sitemaps version 3.2 or later.', 'jobman' ) . '</span>' ):( '' ) ?>
		</p>
<?php
	if( $gxs && version_compare( $gxs_version, '3.2', '>=' ) ) {
?>
		<strong><?php _e( 'Options', 'jobman' ) ?></strong>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Add Job pages to your Sitemap?', 'jobman' ) ?></th>
				<td><input type="checkbox" value="1" name="plugin-gxs"<?php echo ( $options['plugins']['gxs'] )?( ' checked="checked"' ):( '' ) ?> /></td>
			</tr>
		</table>
<?php
	}
?>
		<p class="submit"><input type="submit" name="submit"  class="button-primary" value="<?php _e( 'Update Plugin Settings', 'jobman' ) ?>" /></p>
	</form>
<?php
}

function jobman_conf_updatedb() {
	$options = get_option( 'jobman_options' );
	
	$options['default_email'] = $_REQUEST['default-email'];
	$options['list_type'] = $_REQUEST['list-type'];

	if( array_key_exists( 'promo-link', $_REQUEST ) && $_REQUEST['promo-link'] )
		$options['promo_link'] = 1;
	else
		$options['promo_link'] = 0;

	update_option( 'jobman_options', $options );
	
	if( $options['plugins']['gxs'] )
		do_action( 'sm_rebuild' );
}

function jobman_updatedb() {
	global $wpdb;
	$options = get_option( 'jobman_options' );

	$page = array(
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_status' => 'publish',
				'post_content' => stripslashes( $_REQUEST['jobman-abstract'] ),
				'post_name' => strtolower( str_replace( ' ', '-', $_REQUEST['jobman-title'] ) ),
				'post_title' => stripslashes( $_REQUEST['jobman-title'] ),
				'post_type' => 'jobman_job',
				'post_date' => stripslashes( $_REQUEST['jobman-displaystartdate'] ),
				'post_parent' => $options['main_page']);
	
	if( 'new' == $_REQUEST['jobman-jobid'] ) {
		$id = wp_insert_post( $page );
		
		add_post_meta( $id, 'salary', stripslashes( $_REQUEST['jobman-salary'] ), true );
		add_post_meta( $id, 'startdate', stripslashes( $_REQUEST['jobman-startdate'] ), true );
		add_post_meta( $id, 'enddate', stripslashes( $_REQUEST['jobman-enddate'] ), true );
		add_post_meta( $id, 'location', stripslashes( $_REQUEST['jobman-location'] ), true );
		add_post_meta( $id, 'displayenddate', stripslashes( $_REQUEST['jobman-displayenddate'] ), true );
		add_post_meta( $id, 'iconid', $_REQUEST['jobman-icon'], true );
		add_post_meta( $id, 'email', $_REQUEST['jobman-email'], true );
	}
	else {
		$page['ID'] = $_REQUEST['jobman-jobid'];
		$id = wp_update_post( $page );
		
		update_post_meta( $id, 'salary', stripslashes( $_REQUEST['jobman-salary'] ) );
		update_post_meta( $id, 'startdate', stripslashes( $_REQUEST['jobman-startdate'] ) );
		update_post_meta( $id, 'enddate', stripslashes( $_REQUEST['jobman-enddate'] ) );
		update_post_meta( $id, 'location', stripslashes( $_REQUEST['jobman-location'] ) );
		update_post_meta( $id, 'displayenddate', stripslashes( $_REQUEST['jobman-displayenddate'] ) );
		update_post_meta( $id, 'iconid', $_REQUEST['jobman-icon'] );
		update_post_meta( $id, 'email', $_REQUEST['jobman-email'] );
	}

	if( array_key_exists( 'jobman-categories', $_REQUEST ) )
		wp_set_object_terms( $id, $_REQUEST['jobman-categories'], 'jobman_category', false );

	if( $options['plugins']['gxs'] )
		do_action( 'sm_rebuild' );
}

function jobman_categories_updatedb() {
	$options = get_option( 'jobman_options' );
	
	$ii = 0;
	$newcount = -1;
	foreach( $_REQUEST['id'] as $id ) {
		if( -1 == $id ) {
			$newcount++;
			// INSERT new field
			if( '' != $_REQUEST['title'][$ii] ) {
				$cat = wp_insert_term( $_REQUEST['title'][$ii], 'jobman_category', array( 'slug' => $_REQUEST['slug'][$ii], 'description' => $_REQUEST['email'][$ii] ) );
			}
			else {
				// No input. Don't insert into the DB.
				$ii++;
				continue;
			}
		}
		else {
			// UPDATE existing field
			if( '' != $_REQUEST['slug'][$ii] )
				wp_update_term( $id, 'jobman_category', array( 'slug' => $_REQUEST['slug'][$ii], 'description' => $_REQUEST['email'][$ii] ) );
			else
				wp_update_term( $id, 'jobman_category', array( 'description' => $_REQUEST['email'][$ii] ) );
		}
		$ii++;
	}

	$deletes = explode( ',', $_REQUEST['jobman-delete-list'] );
	foreach( $deletes as $delete ) {
		wp_delete_term( $delete, 'jobman_category' );
		
		// Delete the category from any fields
		foreach( $options['fields'] as $fid => $field ) {
			$loc = array_search( $delete, $field['categories'] );
			if( false !== $loc ) {
				unset( $options['fields'][$fid]['categories'][$loc] );
				$options['fields'][$fid]['categories'] = array_values( $options['fields'][$fid]['categories'] );
			}
		}
	}

	if( array_key_exists( 'related-categories', $_REQUEST ) && $_REQUEST['related-categories'] )
		$options['related_categories'] = 1;
	else
		$options['related_categories'] = 0;
	
	if( $options['plugins']['gxs'] )
		do_action( 'sm_rebuild' );
		
	update_option( 'jobman_options', $options );
}

function jobman_icons_updatedb() {
	$options = get_option( 'jobman_options' );
	
	$ii = 0;
	$newcount = -1;
	
	foreach( $_REQUEST['id'] as $id ) {
		if( -1 == $id ) {
			$newcount++;
			// INSERT new field
			if( '' != $_REQUEST['title'][$ii] || '' != $_FILES['icon']['name'][$ii] ) {
				preg_match( '/.*\.(.+)$/', $_FILES['icon']['name'][$ii], $matches );
				$ext = $matches[1];

				$options['icons'][] = array(
											'title' => $_REQUEST['title'][$ii],
											'extension' => $ext
									);
			}
			else {
				// No input. Don't insert into the DB.
				$ii++;
				continue;
			}
		}
		else {
			// UPDATE existing field
			$options['icons'][$id]['title'] = $_REQUEST['title'][$ii];

			if('' != $_FILES['icon']['name'][$ii] ) {
				preg_match( '/.*\.(.+)$/', $_FILES['icon']['name'][$ii], $matches );
				$ext = $matches[1];
			
				$options['icons'][$id]['extension'] = $ext;
			}
		}
		
		if( '' != $_FILES['icon']['name'][$ii] ) {
			if( is_uploaded_file( $_FILES['icon']['tmp_name'][$ii] ) ) {
				if( -1 == $id ) {
					$keys = array_keys( $options['icons'] );
					$id = end( $keys );
				}
				move_uploaded_file( $_FILES['icon']['tmp_name'][$ii], JOBMAN_UPLOAD_DIR . "/icons/$id.$ext");
			}
		}

		$ii++;
	}

	$deletes = explode( ',', $_REQUEST['jobman-delete-list'] );
	foreach( $deletes as $delete ) {
		unset( $options['icons'][$delete] );
		
		// Remove the icon from any jobs that have it
		$jobs = get_posts( "post_type=jobman_job&meta_key=iconid&meta_value=$delete&numberposts=-1" );
		foreach( $jobs as $job ) {
			update_post_meta( $job->ID, 'iconid', '' );
		}
	}
	
	update_option( 'jobman_options', $options );
}

function jobman_users_updatedb() {
	$options = get_option( 'jobman_options' );

	$postnames = array( 'user-registration', 'user-registration-required', 'loginform-main', 'loginform-category', 'loginform-job', 'loginform-apply' );
	$optionnames = array( 'user_registration', 'user_registration_required', 'loginform_main', 'loginform_category', 'loginform_job', 'loginform_apply' );
	
	foreach( $postnames as $key => $var ) {
		if( array_key_exists( $var, $_REQUEST ) && $_REQUEST[$var] )
			$options[$optionnames[$key]] = 1;
		else
			$options[$optionnames[$key]] = 0;
	}
	
	update_option( 'jobman_options', $options );
}

function jobman_application_email_updatedb() {
	$options = get_option( 'jobman_options' );
	
	$options['application_email_from'] = $_REQUEST['jobman-from'];
	$options['application_email_subject_text'] = $_REQUEST['jobman-subject-text'];
	if( is_array( $_REQUEST['jobman-subject-fields'] ) )
		$options['application_email_subject_fields'] = $_REQUEST['jobman-subject-fields'];
	else
		$options['application_email_subject_fields'] = array();
	
	update_option( 'jobman_options', $options );
}

function jobman_other_plugins_updatedb() {
	$options = get_option( 'jobman_options' );

	if( array_key_exists( 'plugin-gxs', $_REQUEST ) && $_REQUEST['plugin-gxs'] )
		$options['plugins']['gxs'] = 1;
	else
		$options['plugins']['gxs'] = 0;
	
	update_option( 'jobman_options', $options );
}

?>