/* --------------------------------------------------------------------------------
  Init piklist-datatable fields
--------------------------------------------------------------------------------- */

;(function($, window, document, undefined) {
  'use strict';

  $(document).ready(function() {
    $('.piklist-datatable').each(function() {
    	var curr_element = $(this);
    	var table = null;

    	var config = {
    		autoWidth: false,
    	};

    	if (curr_element.data('group-by-column')) {
        	config.rowGroup = {
        		dataSrc: curr_element.data('group-by-column')
    		};
    	}
    	/*
    	if (curr_element.data('language-file')) {
        	config.language = {
        		url: curr_element.data('language-file')
    		};
    	}*/

    	switch (curr_element.data('data-source-type')) {
    		case 'dom':
    			var target = $(curr_element.data('data-source-param'));
    			target.data(curr_element.data());
    			table = target.DataTable(config);
    			curr_element = target;
    			break;

    		case 'field':
    			table = curr_element.DataTable(config);
    			break;

    		case 'json_var':
    			config.data = window[curr_element.data('data-source-param')];
    			table = curr_element.DataTable(config);
    			break;

    		case 'ajax_client': 
    			config.ajax = {
    				url: curr_element.data('data-source-param'),
    				dataSrc: '',
    			};

    			table = curr_element.DataTable(config);
    			break;

    		case 'ajax_server': 
    			var ajax_url = curr_element.data('data-source-param');
    			config.processing = true;
    			config.serverSide = true;

				config.ajax = function(params, callback, settings) {
			        var request_params = {
			        	offset: params.start,
			        	per_page: params.length,
			        	orderby: params.columns[params.order[0].column].data,
			        	order: params.order[0].dir,
			        };

			        if (params.search.value != "") {
			        	request_params.search = params.search.value;
			        }

			        $.get(ajax_url, request_params, function(data, status, jqXHR) {
			        	var total_records = parseInt(jqXHR.getResponseHeader('X-WP-Total'));

				        var result = {
				        	recordsTotal: total_records,
							recordsFiltered: total_records,
				          	data: data,
				        };
			            
			            callback(result);
			        });
			    };
				
				table = curr_element.DataTable(config);
    			break;

    		default:
    			alert('Invalid data-source-type specified ({0}) for element ({1})'.format(curr_element.data('data-source-type'), curr_element.attr('id')));
    	}

    	// with ajax_server the buttons were not created if we put them in the configuration, so we create them here if needed
    	if (curr_element.data('show-export-buttons')) {
	    	new $.fn.dataTable.Buttons(table, { buttons: ['copy', 'csv', 'excel', 'print'] });

	    	var element_id = '#' + curr_element.attr('id') + '_length';
	    	var button_container = table.buttons().container();
	    	button_container.insertBefore(element_id);
	    }
	});
  });

})(jQuery, window, document);
