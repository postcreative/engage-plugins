<?php
// deny access
if( !defined('ABSPATH') ) die('Security check');
?>
<?php 
$is_part_init=false;
foreach ($modules as $module_name=>$data)
{
    if (isset($data[$element->section]))
    {
        foreach ($data[$element->section] as $el)
        {
            if ($el['id']==$element->id)
            {
                $is_part_init=true;
                break;
            }
        }
    }
    if ($is_part_init) break;
}
?>
<style type="text/css">
body .module-manager-box-head {
padding-left:30px !important; 
background-repeat:no-repeat !important; 
background-image:url('<?php echo MODMAN_ASSETS_URL.'/images/module-manager_16x16.png'?>') !important; 
background-position:5px 50% !important;
}
</style>
<p>
<?php if ($is_part_init) { ?>
<?php printf(__('Belongs to these modules:','module-manager'), $element->title); ?>
<?php } else { ?>
<?php printf(__('Want to reuse this in other websites?','module-manager'), $element->title); ?>
<?php } ?>
</p>
<div class="modman-inline-module-manager">
    <div class="modman-inline-module-manager-inner">
    <?php
    $is_part=false;
    foreach ($modules as $module_name=>$data)
    {
        $is_part=false;
        
        if (isset($data[$element->section]))
        {
            foreach ($data[$element->section] as $el)
            {
                if ($el['id']==$element->id)
                {
                    $is_part=true;
                    break;
                }
            }
        }
        ?>
        <p><label><input type="checkbox" value="<?php echo esc_attr($module_name); ?>" <?php if ($is_part) echo 'checked="checked"'; ?> /><span style="margin-left:10px"><?php echo $module_name; ?></span></label></p>
    <?php } ?>
    </div><br />
    <?php wp_nonce_field('modman-add-to-module-action','modman-add-to-module-field');  ?>
    <span style="margin-right:10px;"><img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="modman-ajax-feedback" title="" alt="" /></span><a class="button button-large modman-addnew-module-inline" href="javascript:;"><?php _e('New Module','module-manager'); ?></a>
    <?php if (!$is_part_init) { ?>
    <p><a href="http://wp-types.com/documentation/user-guides/using-toolset-module-manager/" target="_blank"><?php _e('Learn about modules', 'module-manager'); ?></a></p>
    <?php } ?>
</div>
<script type='text/javascript'>
/* <![CDATA[ */
(function($){
    var mmboxinner=$('.modman-inline-module-manager');
    
    // add icon
    mmboxinner
    .closest('.postbox')
    .find('.hndle')
    /*.css({
        'padding-left':'30px', 
        'background-repeat':'no-repeat', 
        'background-image':'url(<?php echo MODMAN_ASSETS_URL.'/images/module-manager_16x16.png'?>)', 
        'background-position':'5px 50%'
    });*/
    .addClass('module-manager-box-head');
    
    mmboxinner
    .closest('table')
    .find('thead th:eq(0)')
    /*.css({
        'padding-left':'30px', 
        'background-repeat':'no-repeat', 
        'background-image':'url(<?php echo MODMAN_ASSETS_URL.'/images/module-manager_16x16.png'?>)', 
        'background-position':'5px 50%'
    });*/
    .addClass('module-manager-box-head');
    
    function addNewMod($el)
    {
        var ajax_url="<?php echo ModuleManager::route('/Modules/addToModule'); ?>";
        var modname=prompt('<?php echo esc_js(__('Module Name','module-manager')); ?>','New Module');
        if (!modname) return false;
        var $container=$el.closest('.modman-inline-module-manager').find('.modman-inline-module-manager-inner');
        var data={};
        if ($('.modman-inline-module-manager-inner input[type="checkbox"]').filter(function(){
            if (modname==$(this).val())
                return true;
            return false;
        }).length)
        {
            alert('<?php echo esc_js(__('Module already exists!','module-manager')); ?>');
            return false;
        }
        data.mod_name=modname;
        data.elem={id:"<?php echo $element->id; ?>", title:"<?php echo $element->title; ?>", section:"<?php echo $element->section; ?>", details:"<?php echo $element->description; ?>"};
        data = $.param(data)+'&mod_creat=1&modman-add-to-module-field='+$('#modman-add-to-module-field').val();
        $('.modman-ajax-feedback').css('visibility','visible');
		$.post( ajax_url, data, function(req){
            $('.modman-ajax-feedback').css('visibility', 'hidden');
            var html='<label><input type="checkbox" value="'+modname+'" checked="checked" /><span style="margin-left:10px">'+modname+'</span></label><br />';
            $container.append(html);
		});
    }
    function toggleItem($el)
    {
        var ajax_url="<?php echo ModuleManager::route('/Modules/toggleItem'); ?>";
        var data={},set=2;
        data.mod_name=$el.val();
        data.elem={id:"<?php echo $element->id; ?>", title:"<?php echo $element->title; ?>", section:"<?php echo $element->section; ?>"};
        if ($el.is(':checked'))
            set=1;
        data = $.param(data)+'&mod_set='+set+'&modman-add-to-module-field='+$('#modman-add-to-module-field').val();
        $('.modman-ajax-feedback').css('visibility','visible');
		$.post( ajax_url, data, function(req){
            $('.modman-ajax-feedback').css('visibility', 'hidden');
		});
    }
    $(function(){
        $('.modman-ajax-feedback').css('visibility','hidden');
        $('.modman-inline-module-manager').on('click','.modman-addnew-module-inline', function(){
            addNewMod($(this));
        });
        $('.modman-inline-module-manager-inner').on('change','input[type="checkbox"]', function(){
            toggleItem($(this));
        });
    });
})(jQuery);
/* ]]> */
</script>