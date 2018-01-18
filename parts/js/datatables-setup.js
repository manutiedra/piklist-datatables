/* --------------------------------------------------------------------------------
  Init piklist-datatable fields
--------------------------------------------------------------------------------- */

;(function($, window, document, undefined) {
  'use strict';

  $(document).ready(function() {
    $('.piklist-datatable').each(function() {
    	var curr_element = $(this);

    	var config = {
    		autoWidth: false,
    	};

    	if (curr_element.data('group-by-column')) {
        	config.rowGroup = {
        		dataSrc: curr_element.data('group-by-column')
    		};
    	}

    	if (curr_element.data('language-file')) {
        	config.language = {
        		url: curr_element.data('language-file')
    		};
    	}

    	switch (curr_element.data('data_source_type')) {
    		case 'dom':
    			var target = $(curr_element.data('data_source_param'));
    			target.data(curr_element.data());
    			target.DataTable(config);
    			break;

    		case 'field':
    			curr_element.DataTable(config);
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
    			var ajax_url = curr_element.data('data_source_param');
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
				
				curr_element.DataTable(config);
    			break;

    		default:

    	}
	});
  });

})(jQuery, window, document);
