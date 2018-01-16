
<?php if ($datatable['config']['data_source_type'] == 'field'){
  $unique_id = '_datatables_' . piklist::unique_id($_object); ?>
  <script type='text/javascript' charset='utf-8'>
    var <?php echo $unique_id ?> = [
<?php 
  $attributes['data-data_source_type'] = 'json_var';
  $attributes['data-data_source_param'] = $unique_id;
  
  foreach ($datatable['table_data'] as $row) {
    echo '["' . implode('","', $row) . '"],' . PHP_EOL;
  }
 ?>
    ];
</script>
<?php } ?>

<?php if ($datatable['config']['data_source_type'] != 'dom') { ?>
  <table <?php echo piklist_form::attributes_to_string($attributes); ?>>
    <thead>
      <tr>
        <?php 
          if (isset($datatable['columns'])) {
            foreach ($datatable['columns'] as $col) {
              echo '<th>' . $col['title'] . '</th>' . PHP_EOL;
            }
          } ?>
      </tr>    
    </thead>
  </table>
<?php } else { ?>
  <span <?php echo piklist_form::attributes_to_string($attributes); ?>></span>
<?php } ?>