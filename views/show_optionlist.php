<div class="wrap" id="center-panel">
<h2 class="r"><?php _e('Show Options', 'copy-option-in-wpmu'); ?></h2>

 <form action="?page=dmf-tools-WPMUCopyOptions&action=confirm" method="POST">

	<table cellpadding="5" cellspacing="5"><thead>
	<tr>
	<td>#</td>
	<td><?php _e('Option Name', 'copy-option-in-wpmu'); ?></td>
	<td><?php _e('Option Value', 'copy-option-in-wpmu'); ?></td>
	</tr></thead>

	<?php
		$i=0;
		$maxcols = 200;
		$odd=true;

		foreach ($this->data['vals'] as $row) {
			$i++;
			if ($odd)
				echo '<tr class="odd">';
			else
				echo '<tr>';
			$odd = !$odd;
	?>
			<td><?php echo $i; ?></td>
			<td><?php echo $row['option_id']; ?></td>
			<td><?php echo $row['option_name']; ?></td>
			<td>
			<input class="top" type=button value="<?php _e('Edit', 'copy-option-in-wpmu'); ?>" onclick="jQuery('#option-<?php echo $i; ?>').attr('readonly', false).attr('name', '<?php echo $row['option_name']; ?>');"></input>
			<a class="top" href="#save"><?php _e('Save', 'copy-option-in-wpmu'); ?></a>
			<?php
				$c = strlen($row['option_value']);
				if ($c < $maxcols)
					echo '<INPUT ID="option-'.$i.'" TYPE=text readonly VALUE="' . htmlspecialchars($row['option_value']) . '" SIZE=' . $c . '>';
				else
				{
					$cols = $maxcols;
					$rows = round($c / $maxcols) * 2;
					echo '<TEXTAREA ID="option-'.$i.'" readonly COLS='.$cols.' ROWS='.$rows.'>'.htmlspecialchars($row['option_value']).'</TEXTAREA>';
				}
			?>
			</td>
			</tr>
	<?php } ?>

	</table>

  <div style="text-align:left">
    <A NAME="save">
    <input type=hidden name="dmf_source_id" value = <?php echo $_POST['dmf_source_id']; ?> />
	<input type="submit" class="button" name="DmfSendButton" value="<?php _e('Confirm clone of edited items', 'copy-option-in-wpmu'); ?>" />
  </div>

 </form>
</div> <!-- /#center-panel -->
