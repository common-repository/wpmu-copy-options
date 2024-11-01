<div class="wrap" id="center-panel">

 <form action="?page=dmf-tools-WPMUCopyOptions&action=save" method="POST">

	<h2 class="r"><?php _e('Selected Options', 'copy-option-in-wpmu'); ?></h2>

	<table cellpadding="5" cellspacing="5"><thead>
	<tr>
	<td></td>
	<td>#</td>
	<td><?php _e('Option Name', 'copy-option-in-wpmu'); ?></td>
	<td><?php _e('Option Value', 'copy-option-in-wpmu'); ?></td>
	</tr></thead>

	<?php
		$i=0;
		$maxcols = 200;
		$odd=true;

		foreach ($_POST as $key=>$value) {
			if ($key == "DmfSendButton")
				continue;
			if ($key == "dmf_source_id")
				continue;

			$i++;
			if ($odd)
				echo '<tr class="odd">';
			else
				echo '<tr>';
			$odd = !$odd;
	?>
			<td><input type=checkbox checked name="ch-<?php echo $key; ?>"></input></td>
			<td><?php echo $i; ?></td>
			<td><?php echo $key; ?></td>
			<td>
			<?php
				$c = strlen($value);
				if ($c < $maxcols)
					echo '<INPUT ID="option-'.$i.'" TYPE=text readonly VALUE="' . htmlspecialchars($value) . '" SIZE=' . $c . ' name="'.$key.'">';
				else
				{
					$cols = $maxcols;
					$rows = round($c / $maxcols) * 2;
					echo '<TEXTAREA ID="option-'.$i.'" readonly COLS='.$cols.' ROWS='.$rows.' name="'.$key.'">'.htmlspecialchars($value).'</TEXTAREA>';
				}
			?>
			</td>
			</tr>
	<?php } ?>

	</table>

	<h2 class="r"><?php _e('List of blogs to apply selected items', 'copy-option-in-wpmu'); ?></h2>
	<table cellpadding="5" cellspacing="5"><thead>
	<tr>
	<td></td>
	<td>#</td>
	<td><?php _e('Blog domain', 'copy-option-in-wpmu'); ?></td>
	<td><?php _e('Blog name', 'copy-option-in-wpmu'); ?></td>
	</tr></thead>

	<?php
		$i=0;
		$odd=true;

        $blogs = $wpdb->get_results("SELECT blog_id, domain FROM $wpdb->blogs ORDER BY blog_id", ARRAY_A);

		foreach ($blogs as $blog) {
			$i++;

			$blog_tb = $this->get_blog_OptionsTbName($blog['blog_id']);
			$blogname = $wpdb->get_var("SELECT option_value FROM $blog_tb WHERE option_id = 2");

			if ($odd)
				echo '<tr class="odd">';
			else
				echo '<tr>';
			$odd = !$odd;

			$checked = 'CHECKED';

			if ($blog['blog_id'] == 1)
				$checked='';
	?>
			<td><input type=checkbox <?php echo $checked; ?> name="blog-<?php echo $blog['blog_id']; ?>"></input></td>
			<td><?php echo $i; ?></td>
			<td><?php echo $blog['domain']; ?></td>
			<td><?php echo $blogname; ?></td>
			</tr>
	<?php } ?>
	</table>

  <div style="text-align:left">
    <input type=hidden name="dmf_source_id" value = <?php echo $_POST['dmf_source_id']; ?> />
	<input type="submit" class="button" name="DmfSendButton" value="<?php _e('Clone checked items', 'copy-option-in-wpmu'); ?>" />
  </div>

 </form>
</div> <!-- /#center-panel -->
