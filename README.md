
# piklist-datatables

## About:

piklist-datatables is a wordpress plugin that uses [datatables](https://datatables.net) to add support for powerful tables.

The supported features include paging, filtering, grouping, sorting, fixed headers, responsive, internationalization, data export (clipboard, CSV or Excel):

![Example of piklist-datatables in action](https://raw.githubusercontent.com/manutiedra/piklist-datatables/master/piklist-datatables.jpg)

It can use local or remote data. To remotely load the data, it uses the wordpress REST API by default, although that can be changed.

## How to use

To use the datatable field, you should set the ```type``` option to ```datatable```.
The field has 5 operating modes controlled by the ```data_source_type``` configuration option:

### 1. Using an existing HTML Table as a data source:
If you have a table defined somewhere in the page and you want to style it and add support for some of the features mentioned above, you can do this: 
```php
piklist('field', array(
  'type' => 'datatable',
  'attributes' => array(
    'data-width' => '100%',
  ),
  'options' => array(
    'config' => array(
      'data_source_type' => 'dom',
      'data_source_param' => '#example',
      'order' => array(array(3, 'desc')),
      'group_by_column' => 3,
    ),
  ),
));
```
and add the ```style``` class to the table with ```id='example'``` and it will transform the table (as long it is has header, because that is required).

### 2. Defining the table data directly in the piklist field:
If you want to show some data you don't need to resort to a html table for that. Just use the ```field``` mode and specify the columns and the data
```php
piklist('field', array(
  'type' => 'datatable',
  'attributes' => array(
    'data-width' => '100%',
  ),
  'options' => array(
    'config' => array(
      'data_source_type' => 'field',
      'generate_footer' => true,
      'enable_responsive' => true,
      'enable_paging' => false,
    ),
    'table_data' => array(
      array('1', 'John Doe', 'CEO', '48'),
      array('2', 'Frederic Tattum', 'Senior Engineer', '38'),
      array('3', 'Betty Sue', 'Senior Engineer', '35'),
      array('4', 'Gill Strahl', 'Marketing', '44'),
      array('5', 'Kelly McDougall', 'Human Resources', '67'),
    ),
    'columns' => array(
      array('title' => 'ID', 'searchable' => false),
      array('title' => 'Name'),
      array('title' => 'Position', 'sortable' => false),
      array('title' => 'Age'),
    ),
  ),
));
```
The columns structure  is defined in 2D array ```columns``` and the data is defined in the ```table_data```, that is an array of rows, where each row is specified by another array (the entries in a row must match the number of columns).

### 3. Defining the table data directly in the piklist field:
If you want to show some data you don't need to resort to a html table for that. Just use the ```field``` mode and specify the columns and the data
```php
piklist('field', array(
  'type' => 'datatable',
  'attributes' => array(
    'data-width' => '100%',
  ),
  'options' => array(
    'config' => array(
      'data_source_type' => 'json_var',
      'data_source_param' => 'dataSet',
      'generate_footer' => true,
      'enable_responsive' => true,
      'page_size' => 15,
      'page_sizes' => array(
        array(5, 15, 25, 50, 100, -1),
        array('5', '15', '25', '50', '100', 'All')
      ),
    ),
    'columns' => array(
      array('title' => 'ID', 'searchable' => false),
      array('title' => 'Name'),
      array('title' => 'Position'),
      array('title' => 'Age', 'sortable' => false),
    ),
  ),
));
```
The javascript data is a 2D array too:
```javascript
var dataSet = [
    [ '1', 'John Doe', 'CEO', '48' ],
  [ '2', 'Frederic Tattum', 'Senior Engineer', '38' ],
  ...
];
```
### 4. AJAX data with client side processing:
The mode ```ajax_client``` generates an AJAX request to get all the data, but the paging, sorting and filtering takes place in the client side. The query options are used to generate the REST API call.

```php
piklist('field', array(
  'type' => 'datatable',
  'label' => 'Last 100 posts',
  'attributes' => array(
    'data-width' => '100%',
  ),
  'options' => array(
    'config' => array(
      'data_source_type' => 'ajax_client',      
      'generate_footer' => true,
      'show_export_buttons' => true,
    ),
    'query' => array(
      'type' => 'posts',
      'orderby' => 'date',
      'order' => 'desc',
      'per_page' => 100,
    ),
    'columns' => array(
      array('title' => 'Post ID', 'field_name' => 'id'),
      array('title' => 'Post Date', 'field_name' => 'date'),
      array('title' => 'Post Title', 'field_name' => 'title.rendered'),
      array('title' => 'Author ID', 'field_name' => 'author'),
    ),
  ),
));
```
### 5. AJAX data with server side processing:
The mode ```ajax_server``` generates an AJAX request where the server should handle paging, sorting and filtering. As the ajax_client mode, the query options are used to refine the REST API call.
```php
piklist('field', array(
  'type' => 'datatable',
  'label' => 'Posts by me'
  'attributes' => array(
    'data-width' => '100%',
  ),
  'options' => array(
    'config' => array(
      'data_source_type' => 'ajax_server',
      'generate_footer' => true,
      'show_export_buttons' => true,
      'paging_type' => 'full_numbers',
      'language' => 'Spanish',
    ),
    'query' => array(
      'type' => 'posts',
      'author' => '1'
    ),
    'columns' => array(
      array('title' => 'Post ID', 'field_name' => 'id'),
      array('title' => 'Post Date', 'field_name' => 'date'),
      array('title' => 'Post Title', 'field_name' => 'title.rendered'),
      array('title' => 'Author ID', 'field_name' => 'author'),
    ),
  ),
));
```
The field adds the parameters offset, per_page, orderby, order, search to match the current table status, so there is no server side code to write as this parameters are supported by the REST API. 

## History:
* 26/01/2018: v0.0.2 released. 
    What's new? 
      - Changed configuration options from field['datatable'] to field['options'].
      - Added render property support in order to generate custom column data.

* 19/01/2018: v0.0.1 released
