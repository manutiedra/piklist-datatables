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
    		initComplete: function(settings, json) {
				if (this.data('show-export-buttons')) {
			    	new $.fn.dataTable.Buttons(this, { buttons: ['copy', 'csv', 'excel', 'print'] });

			    	var element_id = '#' + this.attr('id') + '_length';
			    	var button_container = this.DataTable().buttons().container();
			    	button_container.insertBefore(element_id);
			    }
	    	},
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

        if (curr_element.data('the-columns')) {
            config.columns = curr_element.data('the-columns');

            for (var i = 0; i < config.columns.length; i++) {
                if (config.columns[i].render) {
                    config.columns[i].render = window[config.columns[i].render];
                }
            }
        }

    	switch (curr_element.data('data-source-type')) {
    		case 'dom':
    			var target = $(curr_element.data('data-source-param'));
    			target.data(curr_element.data());
    			target.DataTable(config);
    			break;

    		case 'field':
    			curr_element.DataTable(config);
    			break;

    		case 'json_var':
    			config.data = window[curr_element.data('data-source-param')];
    			curr_element.DataTable(config);
    			break;

    		case 'ajax_client': 
    			config.ajax = {
    				url: curr_element.data('data-source-param'),
    				dataSrc: '',
    			};

    			curr_element.DataTable(config);
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
				
				curr_element.DataTable(config);
    			break;

    		default:
    			alert('Invalid data-source-type specified ' + curr_element.data('data-source-type') + ' for element ' + curr_element.attr('id'));
    	}
	});
  });

})(jQuery, window, document);
