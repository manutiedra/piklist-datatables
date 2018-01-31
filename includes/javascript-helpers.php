<?php
/**
 * Helpers for client side tasks
 */
class PD_JS_Helpers {
	/**
	 * Generates a javascript variable from a json object
	 *
	 * @param mixed $object The object to generate in the client side
	 * @param string $var_name The associated client side variable name
	 * @return void
	 * @since 0.0.1
	 */
	static function save_object($object, $var_name) {
?>
<script type="text/javascript">
    var <?php echo $var_name; ?> = <?php echo json_encode($object); ?>
</script>
<?php
	}

	/**
	 * Generates a javascript callback function to map a value using a look up table
	 *
	 * @param string $function_name The client side function name to generate
	 * @param string $var_name The associated client side variable name with the look up table
	 * @param string $field_name The row field entry used as key in the look up table
	 * @return void
	 * @since 0.0.1
	 */
	static function lookup_single_value($function_name, $var_name, $field_name) {
?>
<script type="text/javascript">
    function <?php echo $function_name; ?>(data,type,row,meta)
    {
        var result = '';

        if (row.<?php echo $field_name; ?>) {
            if (row.<?php echo $field_name; ?>) {
                if (row.<?php echo $field_name; ?> in <?php echo $var_name; ?>) {
                    result = <?php echo $var_name; ?>[row.<?php echo $field_name; ?>];
                }
            }
        }

        return result;
    }
</script>
<?php
	}

	/**
	 * Generates a javascript callback function to map a value using a look up table
	 *
	 * @param string $function_name The client side function name to generate
	 * @param string $var_name The associated client side variable name with the look up table
	 * @param string $field_name The row field entry used as key in the look up table
	 * @return void
	 * @since 0.0.1
	 */
	static function lookup_multiple_values($function_name, $var_name, $field_name) {
?>
<script type="text/javascript">
    function <?php echo $function_name; ?>(data,type,row,meta)
    {
        var result = [];

        if (row.<?php echo $field_name; ?>) {
        	if (Array.isArray(row.<?php echo $field_name; ?>)) {
	            for (var i = 0; i < row.<?php echo $field_name; ?>.length; i++) {
	                if (row.<?php echo $field_name; ?>[i]) {
	                    if (row.<?php echo $field_name; ?>[i] in <?php echo $var_name; ?>) {
	                        result.push(<?php echo $var_name; ?>[row.<?php echo $field_name; ?>[i]]);
	                    }
	                }
	            }
	        }
        }

        return result.join(', ');
    }
</script>
<?php
	}

	/**
	 * Generates a string from an array of values
	 *
	 * @param string $function_name The client side function name to generate
	 * @param string $field_name The row field entry with the array of values
	 * @return void
	 * @since 0.0.1
	 */
	static function explode_values($function_name, $field_name) {
?>
<script type="text/javascript">
    function <?php echo $function_name; ?>(data,type,row,meta)
    {
        var result = [];

        if (row.<?php echo $field_name; ?>) {
        	if (Array.isArray(row.<?php echo $field_name; ?>)) {
	            for (var i = 0; i < row.<?php echo $field_name; ?>.length; i++) {
	                if (row.<?php echo $field_name; ?>[i]) {
                        result.push(row.<?php echo $field_name; ?>[i]);
	                }
	            }
	        }
        }

        return result.join(', ');
    }
</script>
<?php
	}

	/**
	 * Gets the backend url to edit a post entry
	 *
	 * @return string  the edit url
	 * @since 0.0.1
	 */
	static function get_edit_url() {
	    return get_admin_url() . 'post.php?action=edit&post=';
	}

	/**
	 * Gets the backend url to create a new post entry
	 *
	 * @param string $post_type The type or custom post type to create
	 * @return string  the url to create a new item
	 * @since 0.0.1
	 */
	static function get_new_url($post_type) {
	    return get_admin_url() . 'post-new.php?post_type=' . $post_type;
	}

	/**
	 * Gets the backend url to show all the items of a post type
	 *
	 * @param string $post_type The type or custom post type to show
	 * @return string  the url to show all items
	 * @since 0.0.1
	 */
	static function get_all_posts_url($post_type) {
	    return get_admin_url() . 'edit.php?post_type=' . $post_type;
	}

	/**
	 * Generates the HTML for a link
	 *
	 * @param string $function_name The client side function name to generate
	 * @param string $link_text The text for the link that will ve created
	 * @param string $link_url The url for the link that will ve created
	 * @param string $class The css class to use
	 * @param string $id_name The row field entry used as key (if it's needed, or null if it doesn't apply)
	 * @return void
	 * @since 0.0.1
	 */
	static function generate_cell_link($function_name, $link_text, $link_url, $class = null, $id_name = null) {
?>
<script type="text/javascript">
    function <?php echo $function_name; ?>(data,type,row,meta)
    {
        var url = '<?php echo esc_attr($link_url) ?>'<?php echo ($id_name ? " + (row.$id_name)" : ""); ?>;
        var css_class  = '<?php  echo esc_attr(is_array($class) ? implode(' ', $class) : ($class ? $class : '')) ?>';
        return '<a href="' + url + '" class="' + css_class + '"><?php echo $link_text; ?></a>';
    }
</script>
<?php
	}
}
