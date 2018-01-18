
<?php
$tag = ($datatable['config']['data_source_type'] != 'dom') ? 'table' : 'span'; ?>
<<?php echo $tag; ?> id="<?php echo piklist::unique_id($_object); ?>" <?php echo piklist_form::attributes_to_string($attributes); ?>>
<?php if ($tag == 'table') { ?>
  <thead>
    <?php
      if (isset($datatable['columns'])) {
        echo '<tr>' . PHP_EOL;
        foreach ($datatable['columns'] as $col) {
          echo '<th>' . $col['title'] . '</th>' . PHP_EOL;
        }
        echo '</tr>' . PHP_EOL;
      } ?>
  </thead>
  <tfoot>
    <?php
      if (isset($datatable['columns']) && $datatable['config']['generate_footer']) {
        echo '<tr>' . PHP_EOL;
        foreach ($datatable['columns'] as $col) {
          echo '<th>' . $col['title'] . '</th>' . PHP_EOL;
        }
        echo '</tr>' . PHP_EOL;
      } ?>
  </tfoot>
<?php } ?>

</<?php echo $tag; ?>>
