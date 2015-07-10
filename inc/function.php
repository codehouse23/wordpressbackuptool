<?php
/**
 * Functions
 * 
 * This file contains all functions for the Wordpress Backup Tool
 *
 * @author Christian Hoenick (Number42.io)
 * @copyright reserved by Number42.io
 * @license GPL V3
 * @version 0.2
 *
 */

function createZipFile($targetZipFileName, $sourceDir4Zip) {
        // initial variables
        $result = "";
        $i = 0;

        // Zip archive object
        $objZipArc = new ZipArchive();

        // open archive or create, if not exist
        if ($objZipArc->open($targetZipFileName, ZIPARCHIVE::CREATE) !== TRUE) {
                die ("Could not open archive");
        }

        // array of files, which should be zipped
        $source = getDirFiles($sourceDir4Zip);

        // loop over all file, which should be zipped
        foreach ($source as $name => $file) {
                // check if it is a file, cause directory will be added automatically
                if (!$file->isDir()) {

                        // Get real and relative path for current file
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($sourceDir4Zip));
                        
			// Add file to archive
                        $objZipArc->addFile($filePath, $relativePath);
                        $result[$i]['filepath'] = $filePath;
                        $result[$i]['relativepath'] = $relativePath;
                        $i++;
                }
        }

        // close and save archive
        $objZipArc->close();

        // return array of zipped file/directories
        return $result;
}

function extractZipFile($zipFile, $targetPath) {
	// initial result variable
	$result = false;

	// inital a new zip object
	$objZip = new ZipArchive;

	// open zip file and extract content to the target path
        if ($objZip->open($zipFile) === TRUE) {
		$objZip->extractTo($targetPath);
                $objZip->close();
		$result = true;
	} else {
		$result = false;
	}
}

/**
 * function: getDirFiles 
 *
 * get all directories and files recursive
 *
 * @return array
 */
function getDirFiles($directory) {
        $result = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory),
                RecursiveIteratorIterator::LEAVES_ONLY);
        return $result;
}

function getFileList($pattern) {
	$aryDirList = glob($pattern);
	return $aryDirList;
}

function getBackupFilePattern($arySource, $id) {
	$result = BACKUP_STORAGE . '/' . $arySource[$id]["arcprefix"] .'_*';
	return $result;
}

function getDateTimeFromFileName($file) {
	$filename = basename($file);
	$strPosPostfix = strripos($filename, '.');
	$fileNameWithoutExt = substr($filename, 0, $strPosPostfix);
	$aryFileSplit = split('_', $fileNameWithoutExt);
	$dateTime = substr($aryFileSplit[1],6,2).".".substr($aryFileSplit[1],4,2).".".substr($aryFileSplit[1],0,4);
	return $dateTime;
}

function removeFilesFromFolder($dir) {
	$result = false;
	$files = array_diff(scandir($dir), array('.','..')); 

	if( is_array($files) && count($files) > 0 ) {
		foreach($files as $file){ // iterate files
  			if(is_file("$dir/$file")) {
				unlink("$dir/$file"); 
			} elseif (is_dir("$dir/$file")) {
				$isDirEmpty = count(glob("$dir/$file/*")) == 0 ? true : false;
				if($isDirEmpty) {
					rmdir("$dir/$file");
				} else {
					removeFilesFromFolder("$dir/$file");
					rmdir("$dir/$file");
				}
        		} else {
				// do nothing
			}
		}	
		
	}
	
	return $result;
}
?>
