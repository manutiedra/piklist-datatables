/* --------------------------------------------------------------------------------
  Init piklist-datatable fields
--------------------------------------------------------------------------------- */

;(function($, window, document, undefined) {
  'use strict';

  function resolve(path, obj) {
  	return path.split('.').reduce(function(prev, curr) {
    	return prev ? prev[curr] : null
    }, obj || self)
  }

  function setValue(obj, path, value) {
    var i;
    path = path.split('.');
    for (i = 0; i < path.length - 1; i++)
        obj = obj[path[i]];

    obj[path[i]] = value;
}

  $(document).ready(function() {
    $('.piklist-datatable').each(function() {
    	var curr_element = $(this);

    	var config = {
    		autoWidth: false,
    	};

    	/*
    	if (curr_element.data('export-buttons')) {
        	config.buttons = ['copy', 'csv', 'excel', 'print'];

        	if (!curr_element.data('dom')) {
        		config.dom = 'Blfrtip';
        	}
    	}*/

    	switch (curr_element.data('data_source_type')) {
    		case 'dom':
    			var target = $(curr_element.data('data_source_param'));
    			target.data(curr_element.data());
    			target.DataTable(config);
    			break;

    		case 'json_var': 
    			config["data"] = window[curr_element.data('data_source_param')];
    			curr_element.DataTable(config);
    			break;

    		case 'ajax_client': 
    			config["ajax"] = curr_element.data('data_source_param');
    			curr_element.DataTable(config);
    			break;

    		case 'ajax_server': 
    			config["ajax"] = curr_element.data('data_source_param');
    			config["processing"] = true;
    			config["serverSide"] = true;

    			/*curr_element.DataTable({
					ajax: {
						url: function () {
			      			return curr_element.data('datatable-url');
			    		},
						dataType: 'json',
						data: function (params) {
							var query = {
								search: params.term,
								page: params.page || 1,
								per_page: curr_element.data('items-per-page')
							};

							return query;
						},
						transport: function (params, success, failure) {
							var read_headers = function(data, textStatus, jqXHR) {
						        var total_pages = parseInt(jqXHR.getResponseHeader('X-WP-TotalPages')) || 1;
						        var display_field_name = curr_element.data('display-field-name');

						        var formatted_data = $.map(data, function (obj) {
								  obj.text = resolve(display_field_name, obj);

								  return obj;
								});

						        return {
						          	results: formatted_data,
						          	pagination: {
						            	more: params.data.page < total_pages
						          	}
						        };
						    };

			    			var $request = $.ajax(params);

			    			$request.then(read_headers).then(success);
						    $request.fail(failure);

						    return $request;
						}
					}
				});*/
    			break;

    		default:

    	}
	});

  });

})(jQuery, window, document);
