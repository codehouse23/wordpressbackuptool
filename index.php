<?php
/**
 * Wordpress Backup Tool
 *
 * @author Christian Hoenick (Number42.io)
 * @copyright reserved by Number42.io
 * @license GPL V3
 * @version 0.1
 *
 */

// configuration file
require('inc/config.php');
require('inc/function.php');

// initial variable with default value
$content = "";
$flashmsg = false;

if ( (isset($_GET["id"]) && (!empty($_GET["id"]) || $_GET["id"] == 0)) && (isset($_GET["action"]) && !empty($_GET["action"])) ) {
	// set id
	$id = htmlspecialchars($_GET["id"]);
	// set action
	$action = htmlspecialchars($_GET["action"]);
	
	switch($action) {
	case "backupnow":
		// increase script timeout value
		ini_set('max_execution_time', 5000);

		// define variables
		$zipFileName = BACKUP_STORAGE . '/' . $arySource[$id]["arcprefix"] .'_'. date("Ymd") .'_'. date("His") . '.zip';
		$backupDirectory = $arySource[$id]["path"]."/";

		// create backup as zip file
		$aryZippedContent = createZipFile($zipFileName, $backupDirectory);

		// redirect after backup
		$location = 'Location: ./index.php?id='. $id .'&action=backupdone';
		header($location);
		exit;
		break;
	case "restorenow":
		if ( isset($_GET["file"]) && !empty($_GET["file"])) {
			$zipFile2Extract = BACKUP_STORAGE . '/' . $_GET["file"];
			$targetPath = $arySource[$id]["path"]."/";
			removeFilesFromFolder($targetPath);
			extractZipFile($zipFile2Extract, $targetPath);
		} else {
			// no file name
		}
		break;
	case "backupdone":
		$flashmsg = FLASH_MSG_BACKUP_DONE . "&nbsp;" . $arySource[$id]["name"];
		break;
	}
} 
 

?>
<html>
<head>
	<title>Wordpress Backup Tool</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

	<style>
		main {
			padding-top:70px;
		}
	</style>

	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Wordpress Backup Tool</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">Home</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	<main>
	<?php if ($flashmsg != false) { ?>
		<div class="container">
		<div class="alert alert-success alert-dismissible" role="alert">
  		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
 		<strong><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> </strong><?php echo $flashmsg; ?>
		</div>
		</div>
	<?php } ?>

	<div class="container">
		<h1>Your Backups</h1>

		<!-- collapse panel for all backups -->
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			<?php   foreach ($arySource as $key => $aSource) { 
				//pattern of backup files
        	       		$pattern = getBackupFilePattern($arySource, $key);
               			$aryFileList = getFileList($pattern);
			?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading-<?php echo $key; ?>">
        			<h4 class="panel-title">
        		        	<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $key; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $key; ?>"><?php echo $aSource['name']; ?>&nbsp;<span class="badge"><?php echo count($aryFileList); ?></span></a>
               			 </h4>
				</div>

                		<div id="collapse-<?php echo $key; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo $key; ?>">
                		<div class="panel-body">
                			<p>&nbsp;
					<a href="./index.php?id=<?php echo $key; ?>&action=backupnow"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Backup Files</a>
					&nbsp;</p>
					<?php
        			        // loop over all saved backups
        			        foreach ($aryFileList as $fid => $filename) {
					?>
                			        <strong> <?php echo getDateTimeFromFileName($filename); ?></strong>
                        			&nbsp;<?php echo  basename($filename); ?>&nbsp;
	   					<a href="./index.php?id=<?php echo $key; ?>&action=restorenow&file=<?php echo basename($filename); ?>"><span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span> restore</a>
						<br />
					<?php } ?>

				</div>
				</div>
			</div>
      			<?php } ?>
		</div>
		<!-- end collapse panel -->

	</div>
	</main>

	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>

