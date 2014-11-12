<?php

    if ($instance['images']) { 
    ?>
   <!-- <script type="text/javascript" src="<?php echo plugins_url("jquery.minicolors.js",__FILE__);?>"></script>
    <script type="text/javascript" src="<?php echo plugins_url("counter.js",__FILE__);?>"></script>
    <link rel='stylesheet' href='<?php echo plugins_url("jquery.minicolors.css",__FILE__);?>' type='text/css' media='all' />
    <link rel='stylesheet' href='<?php echo plugins_url("counter_style.css",__FILE__);?>' type='text/css' media='all' /> -->
    <table class="image_counter_table">
        <tr>
            <td colspan="2">Your custom counter:</td>
            <td> 
                <div class="img-block">
                    <div class="img-counter" type-counter="color" id="<?php echo $this->get_field_id( 'counter_color' ); ?>" onclick="saveImage(this,'<?php echo $this->get_field_id( 'counter_color' ); ?>','<?php echo $this->get_field_id( 'counter_type' ); ?>');">
                        <div class="image-counter-cokorpiker" >
                            <input type="text" id="<?php echo $this->get_field_id('colors_counter_image'); ?>" class="color_image" onclick="change_size()"> 
                            <script type="text/javascript">   
                                <?php echo "var id_base_widget='{$this->id_base}';";?>
                                jQuery( document ).ajaxComplete(function( event, xhr, settings ) {
                                    url_search = settings.url.substr(settings.url.indexOf("/wp-admin/admin-ajax.php"),settings.url.length);
                                    if (url_search == "/wp-admin/admin-ajax.php") {
                                        params = decodeURI(settings.data);  
                                        array_params = params.split('&');
                                        i = in_array("id_base", array_params);    // action = save-widget  widget-id  delete_widget=1
                                        i_action = in_array("action", array_params);
                                        i_widget_id = in_array("widget-id", array_params);
                                        i_delete = in_array("delete_widget", array_params); 
                                        if (i && i_action && i_widget_id && !i_delete) {
                                            temp_i = array_params[i].split('=');
                                            temp_i_action = array_params[i_action].split('=');
                                            temp_i_widget_id = array_params[i_widget_id].split('=');
                                            if (temp_i[1] == id_base_widget && temp_i_action[1] == "save-widget") {  //widget-counter_free_widget-15
                                                setColorPicker('widget-' + temp_i_widget_id[1] + '-colors_counter_image',
                                                '<?php echo $instance['color_image']?>', 
                                                'widget-' + temp_i_widget_id[1] + 'counter_image_color',
                                                'widget-' + temp_i_widget_id[1] +  'counter_type',
                                                'widget-' + temp_i_widget_id[1] +  'counter_color', 
                                                'widget-' + temp_i_widget_id[1] +  'counter_hidden' 
                                                );
                                            }
                                        }
                                    }
                                });
                                jQuery(document).ready( function () {
                                    setColorPicker('<?php echo $this->get_field_id('colors_counter_image'); ?>',
                                    '<?php echo $instance['color_image']?>', 
                                    '<?php echo $this->get_field_id( 'counter_image_color' ); ?>',
                                    '<?php echo $this->get_field_id( 'counter_type' ); ?>',
                                    '<?php echo $this->get_field_id( 'counter_color' ); ?>',
                                    '<?php echo $this->get_field_id( 'counter_hidden' ); ?>' );
                                });
                            </script>
                        </div>
                    </div> 
                </div>
            </td>

        </tr>
        <tr>
            <?php $temp = 1;
                foreach($instance['images'] as $key => $value) {
                    if ($value['type'] == $instance['default_image'] && $instance['default_hidden'] == 0) {
                        echo '<script type="text/javascript"> 
                        jQuery(document).ready( function () {'; 
                        echo 'set_default_image("' . $this->get_field_id( 'counter_' . $value['type'] ) . '", "img-counter-active");';
                        echo '}); </script>';
                    }

                ?>
                <?php if($temp < 3) ?>
                <td>
                    <div class="img-block">
                        <div class="img-counter" type-counter="<?php echo $value['type']?>" 
                            id="<?php echo  $this->get_field_id( 'counter_' . $value['type'] );?>" 
                            onclick="saveImage(this,'<?php echo $this->get_field_id( 'counter_hidden' ); ?>','<?php echo $this->get_field_id( 'counter_type' ); ?>')">
                            <img src="<?php echo  'http://' . SERVER_URL . '/'.$value['link']; ?>" />
                        </div> 
                    </div>
                </td>
                <?php if($temp%3==0) print '</tr><tr>'; ?>
                <?php $temp++;
                }     
                if ($instance['default_hidden'] == 2) {
                    echo '<script type="text/javascript">';
                    echo '
                    jQuery(document).ready( function () { 
                    set_default_image("' . $this->get_field_id( 'counter_hidden_show' ) . '", "img-counter-active");
                    });';
                    echo ' </script>';
                } elseif ($instance['default_image'] == 0) {
                    echo '<script type="text/javascript">';
                    echo 'jQuery(document).ready( function () {
                    set_default_image("' . $this->get_field_id( 'counter_color' ) . '", "img-counter-active" );
                    });';
                    echo ' </script>';
                }   

            ?>
            <td> 
                <div class="img-block">
                    <div class="img-counter" type-counter="hidden" 
                        onclick="saveImage(this, '<?php echo $this->get_field_id( 'counter_hidden' ); ?>','<?php echo $this->get_field_id( 'counter_type' ); ?>')" 
                        id="<?php echo $this->get_field_id( 'counter_hidden_show' ); ?>">
                        <span class="hidden-class">
                            Hidden counter
                        </span>
                    </div> 
                </div>
            </td>
            <td>
            </td>
        </tr>
    </table>
    <br />
    <a href="<?php echo admin_url( 'admin.php?page=counter_free_plagin' ) ;?>">Create an account</a>
    <br />
    <br />
    <input type="hidden" name="<?php echo $this->get_field_name('counter_type'); ?>" id="<?php echo $this->get_field_id( 'counter_type' ); ?>" value="<?php echo $instance['default_image'];?>">
    <input type="hidden" name="<?php echo $this->get_field_name('counter_hidden'); ?>" id="<?php echo $this->get_field_id( 'counter_hidden' ); ?>" value="<?php echo $instance['default_hidden'] ;?>">
    <input type="hidden" name="<?php echo $this->get_field_name('counter_image_color'); ?>" id="<?php echo $this->get_field_id( 'counter_image_color' ); ?>" value="<?php echo $instance['color_image'] ;?>">
    <?php  } ?>