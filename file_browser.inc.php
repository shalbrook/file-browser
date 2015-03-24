<!--
3-22-15: requested mods for ko:
1. collapsible directory lists
2. editable dir. names (could provide editable filenames via the same method)
3. files and dirs. sorted by name
-->
  <div align="center">
    <p class="filebrowserheader">File browser</p>
    <table id="filebrowsericonkey">
      <tr><th colspan="5" class="filebrowserheader">Key:</th></tr>
      <tr><td><img src="folder.png"> Folder</td>
      <td><img src="delete.png"> Delete file or folder</td>
      <td><img src="arrow_up.png"> Upload file</td>
      <td><img src="arrow_down.png"> Download file</td>
      <td><img src="folder_new.png"> Create new subfolder</td></tr>
    </table>
  </div>

<?php

if ( $_GET['base'] == 'r' || $_POST['base'] == 'r' ) {
  $base = $rolefolder;
} elseif ( $_GET['base'] == 'p' || $_POST['base'] == 'p' ) {
  $base = $pubfolder;
}
if ( $_GET['action'] == 'delete' ) {
  $todelete = $base.$_GET['file'];
  if ( is_dir( $todelete ) ) {
    if ( rmdir( $todelete ) ) {
      $result = array( 'Folder '.$_GET['file'].' deleted successfully.' );
    } else {
      $result = array( 'Could not delete folder.' );
    }
  } else {
    if ( unlink( $todelete ) ) {
      $result = array( 'File '.$_GET['file'].' deleted successfully.' );
    } else {
      $result = array( 'Could not delete file.' );
    }
  }
} elseif ( isset( $_POST['subf'] ) ) {
  $createsubf = $base;
  if ( isset( $_POST['basef'] ) ) $createsubf .= $_POST['basef'].'/';
  $createsubf .= $_POST['newf'];
  if ( !preg_match( '/^[0-9A-Za-z\-_ ]+$/', $_POST['newf'] ) ) {
    $result = array( 'New folder name must contain only letters and numbers.' );
  } elseif ( mkdir( $createsubf, 0777 ) ) {
    $result = array( 'New folder ' . $_POST['newf'] . ' created successfully.' );
  } else {
    $result = array( 'Could not create folder.' );
  }
} elseif ( isset( $_POST['upload'] ) ) {
  $destination = $base;
  if ( isset( $_POST['basef'] ) ) $destination .= $_POST['basef'].'/';
  require_once $upload_class;
  try {
    $upload = new Ps2_Upload( $destination );
    if ( isset( $logfile ) ) $upload->set_logfile( $logfile );
    $upload->move();
    $result = $upload->getMessages();
  } catch ( Exception $e ) {
    echo $e->getMessage();
  }
} elseif ( isset( $_GET['dl'] ) ) {
  $result = array( 'File ' . $_GET['file'] . ' downloaded successfully.' );
}

if ( isset( $result ) ) {
  //echo '<br><hr width="600px">';
  foreach ( $result as $message ) {
    echo "<p style=\"border:2px solid lightgray; padding:5px\"><strong>Result:</strong> $message</p>";
  }
}

echo '<br>';
//echo '<hr width="600px">';

function list_files( $friendlyname, $startingfolder, $abbrev ) { ?>
<ul class="folderlist">
<li style="font-size: large"><strong><?php echo $friendlyname; ?></strong>
<a class="arrow_up_icon"><img src="arrow_up.png" border="0"></a>
      <div class="upload_div">
        <label for="image">Upload file:</label>
      <form action="index.php" method="post" enctype="multipart/form-data" id="uploadImage">
      <input type="hidden" name="base" value="<?php echo $abbrev; ?>">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>">
      <input type="file" name="image" id="image">
      <input type="submit" name="upload" id="upload" value="Upload"></form></div>

<a class="folder_new_icon"><img src="folder_new.png" border="0"></a>
<div class="newfolder_div">
      New subfolder: <form action="index.php" method="post">
      <input type="hidden" name="base" value="<?php echo $abbrev; ?>">
      <input type="text" name="newf"><input type="submit" name="subf" value="Create"></form></div></li>
<?php
  // http://php.net/manual/en/class.directoryiterator.php
  $last_dir_level = 0;

  echo '<ul class="folderlist">';
  $objects = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $startingfolder ), RecursiveIteratorIterator::SELF_FIRST );
  foreach ( $objects as $name => $object ) {
    if ( $objects->isDot() ) continue;
    if ( $objects->getDepth() > $last_dir_level ) {
      echo "<ul class=\"folderlist\">\n";
      $last_dir_level = $objects->getDepth();
    } elseif ( $objects->getDepth() < $last_dir_level ) {
      echo "</ul>\n";
      $last_dir_level = $objects->getDepth();
    }
    echo "<li";
    if ( $objects->isDir() ) echo ' class="folder"';
    echo ">".$objects->getFilename();
    echo ' <a href="index.php?action=delete&base='.$abbrev.'&file='.rawurlencode( $objects->getSubPathname() ).'"><img src="delete.png" border="0"></a>';
    if ( $objects->isDir() ) {
?>
  <a class="arrow_up_icon"><img src="arrow_up.png"></a>
      <div class="upload_div">
      <label for="image">Upload file:</label>
      <form action="index.php" method="post" enctype="multipart/form-data" id="uploadImage">
      <input type="hidden" name="base" value="<?php echo $abbrev; ?>">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max; ?>">
      <input type="hidden" name="basef" value="<?php echo $objects->getSubPathname(); ?>">
      <input type="file" name="image" id="image">
      <input type="submit" name="upload" id="upload" value="Upload"></form></div>

<a class="folder_new_icon"><img src="folder_new.png" border="0"></a>
<div class="newfolder_div">
      New subfolder: <form action="index.php" method="post">
      <input type="hidden" name="base" value="<?php echo $abbrev; ?>"><input type="hidden" name="basef" value="<?php echo $objects->getSubPathname(); ?>">
      <input type="text" name="newf"><input type="submit" name="subf" value="Create"></form></div>
    <?php } else { ?>
      <a href="index.php?dl=1&base=<?php echo $abbrev; ?>&file=<?php echo rawurlencode( $objects->getSubPathname() ); ?>">
      <img src="arrow_down.png" border="0"></a>
    <?php }
    echo "</li>\n";
  }
?>
  </ul></ul>
<?php
}
?>

<div style="width: 600px; margin: auto">
<?php
if ( !isset( $disable_private_folders ) ) {
  list_files( 'Your private folder', $rolefolder, 'r' );
  echo '<br><hr width="600px" color="lightgray">';
}
list_files( 'Shared folder', $pubfolder, 'p' );
?>
<br>
</div>
