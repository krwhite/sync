<?php
/*
UpdraftPlus Addon: migrator:Migrate a WordPress site to a different location.
Description: Import a backup into a different site, incluing database search-and-replace. Ideal for development and testing and cloning of sites.
Version: 1.3
Shop: /shop/migrator/
Latest Change: 1.6.55
*/

// TODO: single-into-multisite migrations:
// TODO: http://iandunn.name/comprehensive-wordpress-multisite-migrations/
// So far: database prefix is switched; a new site is created. Next: selectively add plugins + themes (don't replace). And network-activate them.
// TODO: Then test path-based multisites too
// TODO: The siteurl at the top of the db dump should actually be what's in the DB, and not any over-rides ... ?
// TODO: Search for other TODOs in the code and in updraft-restorer.php
// TODO: Set all post/comment ownership to importing admin. Or import the users.
// TODO: Don't import extraneous tables (e.g. users)
// TODO: Rewriting of URLs like wp-content/uploads/2011/10/bant-logo.png
// TODO: Document it

// See http://lists.automattic.com/pipermail/wp-hackers/2013-May/046089.html

$updraftplus_addons_migrator = new UpdraftPlus_Addons_Migrator;

class UpdraftPlus_Addons_Migrator {

	function __construct() {
		add_action('updraftplus_restore_form_db', array($this, 'updraftplus_restore_form_db'));
		add_action('updraftplus_restored_db', array($this, 'updraftplus_restored_db'), 10, 2);
		add_action('updraftplus_restored_db_table', array($this, 'updraftplus_restored_db_table'), 10, 2);
		add_action('updraftplus_restore_db_pre', array($this, 'updraftplus_restore_db_pre'));
		add_action('updraftplus_restore_db_record_old_siteurl', array($this, 'updraftplus_restore_db_record_old_siteurl'));
		add_action('updraftplus_restored_plugins_one', array($this, 'restored_plugins_one'));
		add_action('updraftplus_restored_themes_one', array($this, 'restored_themes_one'));
		add_filter('updraftplus_restore_set_table_prefix', array($this, 'restore_set_table_prefix'), 10, 2);
		add_filter('updraftplus_dbscan_urlchange', array($this, 'dbscan_urlchange'), 10, 3);
	}

	function dbscan_urlchange($output, $old_siteurl, $res) {
		if (isset($res['updraft_restorer_replacesiteurl']) && $res['updraft_restorer_replacesiteurl']) return '';
		return '<strong>'.__('Warning:', 'updraftplus').'</strong>'.' '.__('This looks like a migration (the backup is from a site with a different address/URL), but you did not check the option to search-and-replace the database. That is usually a mistake.', 'updraftplus');
	}
	
	function restored_plugins_one($plugin) {
		echo '<strong>'.__('Processed plugin:', 'updraftplus').'</strong> '.$plugin."<br>";
	}

	function restored_themes_one($theme) {
		// Network-activate
		$allowed_themes = get_site_option('allowedthemes');
		$allowed_themes[$theme] = true;
		update_site_option('allowedthemes', $allowed_themes);
		echo '<strong>'.__('Network activating theme:', 'updraftplus').'</strong> '.$theme."<br>";
	}

	function restore_set_table_prefix($import_table_prefix, $backup_is_multisite) {
		if (!is_multisite() || $backup_is_multisite !== 0) return $import_table_prefix;
		
		$new_blogid = $this->generate_new_blogid();

		if (!is_integer($new_blogid)) return $new_blogid;

		$this->new_blogid = $new_blogid;

		return (string)$import_table_prefix.$new_blogid.'_';
	}

	function dump_form() {
		$form = '';
		foreach ($_POST as $key =>$val) {
			if (is_array($val)) {
				foreach ($val as $ktwo => $valtwo) {
					$form .= '<input type="hidden" name="'.$key.'['.$ktwo.']" value="'.htmlspecialchars($valtwo).'">';
				}
			} else {
				$form .= '<input type="hidden" name="'.$key.'" value="'.htmlspecialchars($val).'">';
			}
		}
		print $form;
	}

	function getinfo_form($msg = '', $blogname = '') {

		global $current_site;

		echo '<h3>'.__('Information needed to continue:','updraftplus').'</h3>';
		echo '<p><em>'.__('Please supply the following information:', 'updraftplus').'</em></p>';


		echo '<p>'.__('Enter details for where this new site is to live within your multisite install:', 'updraftplus').'</p>';

		if ($msg) {
			echo '<p>'.$msg.'</p>';
		}

		echo '<form method="POST">';
		// These strings are part of WordPress
		if ( !is_subdomain_install() ) {
			echo '<label for="blogname">' . __('Site Name:') . '</label>';
		} else {
			echo '<label for="blogname">' . __('Site Domain:') . '</label>';
		}
		$this->dump_form();

		if ( !is_subdomain_install() )
			echo '<span class="prefix_address">' . $current_site->domain . $current_site->path . '</span><input name="updraftplus_migrate_blogname" type="text" id="blogname" value="'. esc_attr($blogname) .'" maxlength="60" /><br />';
		else
			echo '<input name="updraftplus_migrate_blogname" type="text" id="blogname" value="'.esc_attr($blogname).'" maxlength="60" /><span class="suffix_address">.' . ( $site_domain = preg_replace( '|^www\.|', '', $current_site->domain ) ) . '</span><br />';


		?><p><input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Continue') ?>" /></p><?php

		echo '</form>';
	}

	function generate_new_blogid() {

		$blog_title = __('Migrated site (from UpdraftPlus)', 'updraftplus');

		if (empty($_POST['updraftplus_migrate_blogname'])) {
			$this->getinfo_form();
			return false;
		}

		// Verify value given
		$result = wpmu_validate_blog_signup($_POST['updraftplus_migrate_blogname'], $blog_title);

		if (count($result['errors']) >0 && $result['errors']->get_error_code()) {
			if (is_wp_error($result['errors'])) {
				$err_msg = '<ul style="list-style: disc inside;">';
				foreach ($result['errors']->get_error_messages() as $key => $msg) {
					$err_msg .= '<li><strong>'.__('Error:', 'updraftplus').'</strong> '.htmlspecialchars($msg).'</li>';
				}
				$err_msg .= '</ul>';
			}
			if (isset($err_msg)) {
				$this->getinfo_form($err_msg, $_POST['updraftplus_migrate_blogname']);
				return false;
			}
		}

		$blogname = $_POST['updraftplus_migrate_blogname'];

		global $wpdb;
		if ( domain_exists($result['domain'], $result['path'], $wpdb->siteid) ) {
			// A WordPress-native string
			$this->getinfo_form(__( '<strong>ERROR</strong>: Site URL already taken.'), $_POST['updraftplus_migrate_blogname']);
			return false;
		}

		$create = create_empty_blog($result['domain'], $result['path'], $blog_title, $wpdb->siteid);
		if (is_integer($create)) {
			$url = untrailingslashit($result['domain'].$result['path']);
			echo '<strong>'.__('New site:','updraftplus').'</strong> '.$url.'<br>';

			// Update record of what we want to rewrite the URLs to in the search/replace operation
			// TODO: How to detect whether http or https???
			$this->siteurl = 'http://'.$url;

			return $create;
		} else {
			$this->getinfo_form(print_r($create,true), $_POST['updraftplus_migrate_blogname']);
			return false;
		}
	}

	function updraftplus_restore_form_db() {

		echo '<input name="updraft_restorer_replacesiteurl" id="updraft_restorer_replacesiteurl" type="checkbox" value="1"><label for="updraft_restorer_replacesiteurl" title="'.sprintf(__('All references to the site location in the database will be replaced with your current site URL, which is: %s', 'updraftplus'), htmlspecialchars(site_url())).'"> '.__('Search and replace site location in the database (migrate)','updraftplus').'</label> <a href="http://updraftplus.com/faqs/tell-me-more-about-the-search-and-replace-site-location-in-the-database-option/">'.__('(learn more)','updraftplus').'</a>';

	}

	function updraftplus_restore_db_record_old_siteurl($old_siteurl) {
		// Only record once
		if (!empty($this->old_siteurl)) return;
		$this->old_siteurl = $old_siteurl;
	}

	function updraftplus_restore_db_pre() {

		global $wpdb;

		$this->siteurl = site_url();

		$mysql_dbh = false;

		$use_wpdb = (!function_exists('mysql_query') || !$wpdb->is_mysql || !$wpdb->ready) ? true : false;

		if (false == $use_wpdb) {
			// We have our own extension which drops lots of the overhead on the query
			// This class is defined in updraft-restorer.php, which has been included if we get here
			$wpdb_obj = new UpdraftPlus_WPDB( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
			// Was that successful?
			if (!$wpdb_obj->is_mysql || !$wpdb_obj->ready) {
				$use_wpdb = true;
			} else {
				$mysql_dbh = $wpdb_obj->updraftplus_getdbh();
			}
		}

		$this->use_wpdb = $use_wpdb;
		$this->mysql_dbh = $mysql_dbh;

		if (true == $use_wpdb) {
			echo __('Database access: Direct MySQL access is not available, so we are falling back to wpdb (this will be considerably slower)','updraftplus')."<br>";
		}

		$this->report = array(
			'tables' => 0,
			'rows' => 0,
			'change' => 0,
			'updates' => 0,
			'timetaken' => 0,
			'errors' => array(),
		);

	}

	function updraftplus_restored_db_table($table) {

		// Anything to do?
		if (!isset($_POST['updraft_restorer_replacesiteurl']) || $_POST['updraft_restorer_replacesiteurl'] != 1) return;


		// Can only do something if the old siteurl is known
		$old_siteurl = isset($this->old_siteurl) ? $this->old_siteurl : '';
		if (!$old_siteurl) return;

		if (empty($this->tables_replaced)) $this->tables_replaced = array();

		// Already done?
		if (isset($this->tables_replaced[$table]) && $this->tables_replaced[$table]) return;

		# If not done already, then search & replace this table, + record that it is done
		@set_time_limit(1800);

		$report = $this->_migrator_icit_srdb_replacer(
			$this->use_wpdb,
			$old_siteurl,
			$this->siteurl,
			array($table)
		);

		// Output any errors encountered during the db work.
		if ( ! empty( $report[ 'errors' ] ) && is_array( $report[ 'errors' ] ) ) {
			echo '<p><h3>'.__('Error:','updraftplus').'</h3> <ul style="list-style: disc inside;">';
			foreach( $report[ 'errors' ] as $error ) echo "<li>".htmlspecialchars($error)."</li>";
			echo '</ul></p>';
		}

		if ($report == false) {
			echo sprintf(__('Failed: the %s operation was not able to start.', 'updraftplus'),'search and replace');
		} elseif (!is_array($report)) {
			echo sprintf(__('Failed: we did not understand the result returned by the %s operation.', 'updraftplus'),'search and replace');
		} else {

			$this->tables_replaced[$table] = true;

			// Calc the time taken.

			foreach (array('tables', 'rows', 'change', 'updates') as $key) {
				$this->report[$key] += $report[$key];
			}
			$this->report['timetaken'] += $report['end'] - $report['start'];
// 			foreach ($report['errors'] as $error) {
// 				$final_report['errors'][] = $error;
// 			}

		}

	}

	function updraftplus_restored_db($info, $import_table_prefix) {

		echo "<h3>".__('Database: search and replace site URL','updraftplus')."</h3>";

		if (!isset($_POST['updraft_restorer_replacesiteurl']) || $_POST['updraft_restorer_replacesiteurl'] != 1) {
			echo '<p>'.__('This option was not selected.','updraftplus').'</p>';
			return;
		}

		global $wpdb;
		$replace_this_siteurl = isset($this->old_siteurl) ? $this->old_siteurl : '';
		if (!$replace_this_siteurl) {
			// Don't call site_url() - the result may/will have been cached
			if (isset($this->new_blogid)) switch_to_blog($this->new_blogid);
			$replace_this_siteurl = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name='siteurl'")->option_value;
			if (isset($this->new_blogid)) restore_current_blog();
		}

		// Sanity checks
		if (empty($replace_this_siteurl)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'backup_siteurl', $this->siteurl).'</p>';
			return;
		}

		if (empty($this->siteurl)) {
			echo '<p>'.sprintf(__('Error: unexpected empty parameter (%s, %s)', 'updraftplus'), 'new_siteurl', $replace_this_url).'</p>';
			return;
		}

		if ($replace_this_siteurl == $this->siteurl) {
			echo '<p>'.sprintf(__('Nothing to do: the site URL is already: %s','updraftplus'), $this->siteurl).'</p>';
			return;
		}

		if (isset($info['expected_oldsiteurl']) && $info['expected_oldsiteurl'] != $replace_this_siteurl) {
			echo '<p>'.sprintf(__('Warning: the database\'s site URL (%s) is different to what we expected (%s)', 'updraftplus'), $replace_this_siteurl, $info['expected_oldsiteurl']).'</p>';
		}

		@set_time_limit(1800);

		echo '<p>';

		echo sprintf(__('Database search and replace: replace %s in backup dump with %s', 'updraftplus'), $replace_this_siteurl, $this->siteurl);

		echo '</p><p>';

		// Now, get an array of tables and then send it off to _migrator_icit_srdb_replacer()
		// Code from searchreplacedb2.php version 2.1.0 from http://www.davidcoveney.com

		// Get database access - code copied from updraft-restorer.php
		$use_wpdb = $this->use_wpdb;

		// Do we have any tables and if so build the all tables array
		$tables = array();

		// We use $wpdb for non-performance-sensitive operations (e.g. one-time calls)
		$tables_mysql = $wpdb->get_results('SHOW TABLES', ARRAY_N);

		if ( ! $tables_mysql ) {
			echo __('Error:','updraftplus').' '.__('Could not get list of tables','updraftplus');
			$this->_migrator_print_error('SHOW TABLES', $use_wpdb);
			return false;
		} else {
			// Run through the array - each element a numerically-indexed array
			foreach ( $tables_mysql as $table) {
				// Type equality is necessary, as we don't want to match false
				// "Warning: strpos(): Empty delimiter" means that the second parameter is a zero-length string
				if (strpos($table[0], $import_table_prefix) === 0) {
					$tablename = $table[0];
					if (isset($this->tables_replaced[$tablename])) {
						echo sprintf(__('<strong>Search and replacing table:</strong> %s: already done', 'updraftplus'),htmlspecialchars($tablename)).'<br>';
					} else {
						$tables[] = $tablename;
					}
				}
			}
		}

		$final_report = $this->report;

		if (!empty($tables)) {

			$report = $this->_migrator_icit_srdb_replacer($use_wpdb, $replace_this_siteurl, $this->siteurl, $tables);

			// Output any errors encountered during the db work.
			if ( ! empty( $report[ 'errors' ] ) && is_array( $report[ 'errors' ] ) ) {
				echo '<h3>'.__('Error:','updraftplus').'</h3> <ul style="list-style: disc inside;">';
				foreach( $report[ 'errors' ] as $error ) echo "<li>".htmlspecialchars($error)."</li>";
				echo '</ul>';
			}

			if ($report == false) {
				echo sprintf(__('Failed: the %s operation was not able to start.', 'updraftplus'),'search and replace');
			} elseif (!is_array($report)) {
				echo sprintf(__('Failed: we did not understand the result returned by the %s operation.', 'updraftplus'),'search and replace');
			}

			// Calc the time taken.
			foreach (array('tables', 'rows', 'change', 'updates') as $key) {
				$final_report[$key] += $report[$key];
			}
			$final_report['timetaken'] += $report['end'] - $report['start'];
			foreach ($report['errors'] as $error) {
				$final_report['errors'][] = $error;
			}

		}

		echo '</p><p>';

		echo '<strong>'.__('Tables examined:', 'updraftplus').'</strong> '.$final_report['tables'].'<br>';
		echo '<strong>'.__('Rows examined:', 'updraftplus').'</strong> '.$final_report['rows'].'<br>';
		echo '<strong>'.__('Changes made:', 'updraftplus').'</strong> '.$final_report['change'].'<br>';
		echo '<strong>'.__('SQL update commands run:', 'updraftplus').'</strong> '.$final_report['updates'].'<br>';
		echo '<strong>'.__('Errors:', 'updraftplus').'</strong> '. count($final_report['errors']).'<br>';
		echo '<strong>'.__('Time taken (seconds):', 'updraftplus').'</strong> '.round($final_report['timetaken'], 3).'<br>';

		echo '</p>';

	}

	// Returns either an array of results, or false - we abstract away what the wpdb class does compared to plain mysql_query
	function query($sql_line, $use_wpdb) {
		if ($use_wpdb) {
			global $wpdb;
			$res = $wpdb->get_results($sql_line, ARRAY_A);
			if ($wpdb->last_error) return false;
			return $res;
		} else {
			$res = mysql_query($sql_line, $this->mysql_dbh);
			if (is_bool($res)) return $res;
			$nres = array();
			while ($row = mysql_fetch_array($res)) {
				$nres[] = $row;
			}
			return $nres;
		}
	}

	function _migrator_print_error($sql_line, $use_wpdb) {
		if ($use_wpdb) {
			global $wpdb;
			$last_error = $wpdb->last_error;
		} else {
			$last_error = mysql_error($this->mysql_dbh);
		}
		echo __('Error:', 'updraftplus')." ".htmlspecialchars($last_error)." - ".__('the database query being run was:','updraftplus').' '.htmlspecialchars($sql_line).'<br>';
		
		return $last_error;
	}

	// The raw engine
	function _migrator_icit_srdb_replacer($use_wpdb, $search, $replace, $tables) {

		global $wpdb, $updraftplus;

		$report = array(
			'tables' => 0,
			'rows' => 0,
			'change' => 0,
			'updates' => 0,
			'start' => microtime(true),
			'end' => microtime(true),
			'errors' => array( ),
		);

		if (!is_array( $tables )) return false;

		foreach( $tables as $table ) {
			$report[ 'tables' ]++;

			$this->columns = array( );

			echo sprintf(__('<strong>Search and replacing table:</strong> %s', 'updraftplus'), htmlspecialchars($table));

			// Get a list of columns in this table
			$fields = $wpdb->get_results('DESCRIBE '.$updraftplus->backquote($table), ARRAY_A);

			$indexkey_field = "";

			foreach ($fields as $column) {
				$primary_key = ($column[ 'Key' ] == 'PRI') ? true : false;
				$this->columns[ $column[ 'Field' ] ] = $primary_key;
				if ($primary_key) $prikey_field = $column[ 'Field' ];
			}

			// Count the number of rows we have in the table if large we'll split into blocks, This is a mod from Simon Wheatley

			# InnoDB does not do count(*) quickly. You can use an index for more speed - see: http://www.cloudspace.com/blog/2009/08/06/fast-mysql-innodb-count-really-fast/

			$count_rows_sql = 'SELECT COUNT(*) FROM '.$table;
			if ($prikey_field) $count_rows_sql .= " USE INDEX (PRIMARY)";

			$row_countr = $wpdb->get_results($count_rows_sql, ARRAY_N);

			// If that failed, try this
			if ($prikey_field && $wpdb->last_error) {
				$row_countr = $wpdb->get_results("SELECT COUNT(*) FROM $table USE INDEX ($prikey_field)", ARRAY_N) ;
				if ($wpdb->last_error) $row_countr = $wpdb->get_results("SELECT COUNT(*) FROM $table", ARRAY_N) ;
			}

			$row_count = $row_countr[0][0];
			echo ': '.sprintf(__('rows: %d', 'updraftplus'),$row_count).'<br>';
			if ( $row_count == 0 ) continue;

			$page_size = 5000;
			$pages = ceil( $row_count / $page_size );

			for ($page = 0; $page < $pages; $page++) {

				$this->current_row = 0;
				$start = $page * $page_size;
				$end = $start + $page_size;
				// Grab the content of the table

				// This delivers back a mysql_query object - will need changing in future WordPress versions
				$sql_line = sprintf('SELECT * FROM %s LIMIT %d, %d', $table, $start, $end );

				# Our strategy here is to minimise memory usage if possible; to process one row at a time if we can, rather than reading everything into memory
				if ($use_wpdb) {
					global $wpdb;
					$data = $wpdb->get_results($sql_line, ARRAY_A);
					if ($wpdb->last_error) {
						$report['errors'][] = $this->_migrator_print_error($sql_line, $use_wpdb);
					} else {
						foreach ($data as $row) {
							$rowrep = $this->process_row($table, $row, $search, $replace);
							$report['rows']++;
							$report['updates'] += $rowrep['updates'];
							$report['change'] += $rowrep['change'];
							foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
						}
					}
				} else {
					$res = mysql_query($sql_line, $this->mysql_dbh);
					if (false === $res) {
						$report['errors'][] = $this->_migrator_print_error($sql_line, $use_wpdb);
					} elseif ($res !== true) {
						while ($row = mysql_fetch_array($res)) {
							$rowrep = $this->process_row($table, $row, $search, $replace);
							$report['rows']++;
							$report['updates'] += $rowrep['updates'];
							$report['change'] += $rowrep['change'];
							foreach ($rowrep['errors'] as $err) $report['errors'][] = $err;
						}
					}
				}

			}

		}

		$report['end'] = microtime(true);

		return $report;
	}

	function process_row($table, $row, $search, $replace) {

		global $updraftplus, $wpdb;

		$report = array('change' => 0, 'errors' => array(), 'updates' => 0);

		$this->current_row++;
		
		$update_sql = array( );
		$where_sql = array( );
		$upd = false;

		foreach( $this->columns as $column => $primary_key ) {

			$edited_data = $data_to_fix = $row[ $column ];

			// Run a search replace on the data that'll respect the serialisation.
			$edited_data = $this->_migrator_recursive_unserialize_replace( $search, $replace, $data_to_fix );

			// Something was changed
			if ( $edited_data != $data_to_fix ) {
				$report[ 'change' ]++;
				$ed = $edited_data;
				$wpdb->escape_by_ref($ed);
				$update_sql[] = $updraftplus->backquote($column) . ' = "' . $ed . '"';
				$upd = true;
			}

			if ( $primary_key ) {
				$df = $data_to_fix;
				$wpdb->escape_by_ref($df);
				$where_sql[] = $updraftplus->backquote($column) . ' = "' . $df . '"';
			}
		}

		if ( $upd && ! empty( $where_sql ) ) {
			$sql = 'UPDATE ' . $updraftplus->backquote($table) . ' SET ' . implode( ', ', $update_sql ) . ' WHERE ' . implode( ' AND ', array_filter( $where_sql ) );
			
			$result = $this->query($sql, $use_wpdb);
			if ( false === $result ) {
				$last_error = $this->_migrator_print_error($sql, $use_wpdb);
				$report[ 'errors' ][] = $last_error;
			} else { 
				$report[ 'updates' ]++;
			}

		} elseif ( $upd ) {
			$report[ 'errors' ][] = sprintf( '"%s" has no primary key, manual change needed on row %s.', $table, $this->current_row );
			echo __('Error:','updraftplus').' '.sprintf( __('"%s" has no primary key, manual change needed on row %s.', 'updraftplus'),$table, $this->current_row );
		}

		return $report;

	}

	/**
	* Take a serialised array and unserialise it replacing elements as needed and
	* unserialising any subordinate arrays and performing the replace on those too.
	*
	* @param string $from       String we're looking to replace.
	* @param string $to         What we want it to be replaced with
	* @param array  $data       Used to pass any subordinate arrays back to in.
	* @param bool   $serialised Does the array passed via $data need serialising.
	*
	* @return array	The original array with all elements replaced as needed.
	*/
	function _migrator_recursive_unserialize_replace( $from = '', $to = '', $data = '', $serialised = false ) {

		// some unserialised data cannot be re-serialised eg. SimpleXMLElements
		try {

			if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {
				$data = $this->_migrator_recursive_unserialize_replace( $from, $to, $unserialized, true );
			}

			elseif ( is_array( $data ) ) {
				$_tmp = array( );
				foreach ( $data as $key => $value ) {
					$_tmp[ $key ] = $this->_migrator_recursive_unserialize_replace( $from, $to, $value, false );
				}

				$data = $_tmp;
				unset( $_tmp );
			}

			else {
				if ( is_string( $data ) ) $data = str_replace( $from, $to, $data );
			}

			if ( $serialised )
				return serialize( $data );

		} catch( Exception $error ) {

		}

		return $data;
	}


}