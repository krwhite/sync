<div class="wrap">
<h2><?php print DML_PUGIN_NAME ." ". DML_CURRENT_VERSION; ?></h2>
<?php
/*  Copyright 2012  aneeskA  (email : contact@aneeska.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
				// echo " Success!<br/>"
			}
			else {
				// echo " Oh bother!<br>";
				;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return "zip open failed. Exiting<br/>";
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,str_replace('/', '', strrchr($file, '/')));
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return (string)file_exists($destination);
	}
	else
	{
		return "No valid files found. Exiting<br/>";
	}
}

/* creates a compressed tar file */
function create_zip_Zip($files = array(),$destination = '') {
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
				// echo " Success!<br/>"
			}
			else {
				// echo " Oh bother!<br>";
				;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		unlink($destination);
		$zip = new Zip();
		$zip->setZipFile($destination); 
		
		foreach ($valid_files as $file) {
			$zip->addFile(file_get_contents($file), str_replace('/', '', strrchr($file, '/')));
		}
		$zip->finalize();
		$zip->setZipFile($destination); 
		
		//check to make sure the file exists
		return (string)file_exists($destination);
	}
	else
	{
		return "No valid files found. Exiting<br/>";
	}
}

function _format_bytes($a_bytes)
{
    if ($a_bytes < 1024) {
        return $a_bytes .' B';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024, 2) .' KiB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576, 2) . ' MiB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824, 2) . ' GiB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776, 2) .' TiB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624, 2) .' PiB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976, 2) .' EiB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
    } else {
        return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
    }
}

$media_query = new WP_Query(
    array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
    )
);
$list = array();
foreach ($media_query->posts as $post) {
	$metadata = wp_get_attachment_metadata($post->ID);
	$filename = dirname(dirname(dirname(__FILE__)));
	$filename = $filename."/uploads/";
	$filename = $filename.$metadata["file"];
	$list[] = $filename;
}
?>
<br/>
Media Library contains <b><?php echo count($list); ?></b> files. Creating compressed file ...
<?php
ob_flush();
require_once "Zip.php";
$targetfilepath = DML_LOGPATH;
if (class_exists(ZipArchive)) {
	$filename = DML_ZIP_FILE;
	$targetfile = $targetfilepath.$filename;
	$ret = create_zip ($list, $targetfile, true);
	$filetype = "Note&nbsp;:&nbsp;This file is of type zip and can be extracted using any unzip application";
}
else if (class_exists(Zip)) {
	$filename = DML_ZIP_FILE;
	$targetfile = $targetfilepath.$filename;
	$ret = create_zip_Zip ($list, $targetfile, true);
	$filetype = "Note&nbsp;:&nbsp;This file is of type zip and can be extracted using any unzip application";
}
else {
	$ret = "Zip support not present in your server. Please contact your server administrator to <a target=\"_blank\"  href=\"http://www.php.net/manual/en/zip.installation.php\">install zip libraries</a>";
}
if ($ret == "1") {
?>
done. <br/><br/>
<b>Download File : <a href='<?php echo get_bloginfo('wpurl')."/wp-content".DML_LOG_FILE.$filename; ?>'><?php echo str_replace('/', '', $filename);?></a></b><br/><br/>
<b>File Size : <?php $size = filesize ($targetfile); echo _format_bytes($size); ?></b><br/><br/>
<?php echo $filetype; ?><br/>

<?php
}
else {
?>
failed. <br/><br/>
Sorry Sonny! Something went wrong. <b>Reason : <?php echo $ret; ?></b><br/>
<?php
} ?>
<br/><br/><table><tr bgcolor="#cec9c9"><td><b>&nbsp;&nbsp;&nbsp;Feel free to contact the author - <a href="http://aneeskA.com" target="_blank">aneeskA</a> - for any clarification at <i>contact(at)aneeskA(dot)com</i>&nbsp;&nbsp;</b></td></tr></table>
</div>