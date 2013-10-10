<?php

/*  Copyright 2012 aneeskA. aneeskA.com



    This program is free software; you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation; either version 2 of the License, or

    (at your option) any later version.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program; if not, write to the Free Software

    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/



add_action( 'admin_notices', 'downml_check_create_log_folder' );



// create log folder

// return true if folder has been created or exists else false

function downml_createLogFolder() {

	$error=false;

	if (!@is_dir (DML_LOGPATH)) {

		if (!@mkdir (DML_LOGPATH, 0777)) {

			$dml_on_off=0;

			update_option('dml_on_off', 0);

			$error=true;

		}

	}



	# check if log folder is writeable

	if (!@is_writable(DML_LOGPATH) ) {



		# trying to set permissions

		if (!@chmod(DML_LOGPATH, 0777)) {

			$dml_on_off=0;

			update_option('dml_on_off', 0);

			$error=true;

		}

	} else {

		# create empty index.html file to hide logs from browsing

		$emptyFile=DML_LOGPATH.'index.html';

		$fileWrite = fopen($emptyFile, 'a');

		fclose($fileWrite);

	}

	if ($error) return false; else return true;

}



# delete log folder

function downml_deleteLogFolder() {

	# delete log folder and logs

	if (@is_dir(DML_LOGPATH)) {

		downml_deltree(DML_LOGPATH);

	}

}



// try to create log folder and print success or error on admin panel

function downml_check_create_log_folder() {

	# check for folder

	if (downml_check_folder_error()) {

		if (!downml_createLogFolder()) {

			print '<div id="message" class="error">'.__("Down Media Library (DML) Error: Can't write to log folder ", EMU2_I18N_DOMAIN).DML_LOGPATH.__(" Permissions 777 needed.", EMU2_I18N_DOMAIN).'</div>';

		} else {

			print '<div id="message" class="updated">'.__("Down Media Library (DML): Log folder created: ", EMU2_I18N_DOMAIN).DML_LOGPATH.'</div>';

		}

	}

}



function downml_check_folder_error() {

	$error=false;

	# check if log folder is writeable

	if (!@is_writable(DML_LOGPATH) ) {

		# trying to set permissions

		if (!@chmod(DML_LOGPATH, 0777)) {

			// can't write to or create log folder

			// maybe you should switch you lugin to off now

			// $dml_on_off=false;

			$error=true;

		}

	}

	return $error;

}



# deletes all files and folders and subfolders in given folder

function downml_deltree($f) {

	if (@is_dir($f)) {

		foreach(glob($f.'/*') as $sf) {

			if (@is_dir($sf) && !is_link($sf)) {

				@downml_deltree($sf);

			} else {

				@unlink($sf);

			}

		}

	}

	if (@is_dir($f)) rmdir($f);

	return true;

}





# write a message to the logfile

function downml_writelog() {

	$numargs = func_num_args();

	$arg_list = func_get_args();

	if ($numargs >2) $linenumber=func_get_arg(2); else $linenumber="";

	if ($numargs >1) $functionname=func_get_arg(1); else $functionname="";

	if ($numargs >=1) $string=func_get_arg(0);

	if (!isset($string) or $string=="") return;



	$logFile=DML_LOGPATH.'/ops-'.date("Y-m").".log";

	$timeStamp = date("d/M/Y:H:i:s O");



	$fileWrite = fopen($logFile, 'a');



	//flock($fileWrite, LOCK_SH);

	if (downml_debug()) {

		$logline="[$timeStamp] ".html_entity_decode($string)." $functionname $linenumber\r\n";	# for debug purposes

	} else {

		$logline="[$timeStamp] ".html_entity_decode($string)."\r\n";

	}

	fwrite($fileWrite, utf8_encode($logline));

	//flock($fileWrite, LOCK_UN);

	fclose($fileWrite);

}



?>