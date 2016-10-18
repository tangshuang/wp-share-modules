<style>
    .list .item {
        margin: 10px;
        padding-left: 25px;
    }
    .list .item .name {
        font-size: 16px;
        margin-left: -25px;
    }
    .list .item .description,
    .list .item .file {
        color: #aaa;
    }
</style>
<div class="wrap">
    <h2>WP SHARE MODULES</h2>
    <div class="container">
        <form method="post">
        <div class="list">
            <?php 
            global $wp_share_modules;
            if(!empty($wp_share_modules)) foreach($wp_share_modules as $module) {
                ?><div class="item">
                    <div class="name">
                        <label>
                            <input type="checkbox" name="wp_share_modules[]" value="<?php echo $module['id']; ?>" <?php if($module['is_active']) echo 'checked' ?>>
                            <?php echo $module['name']; ?>
                        </label>
                    </div>
                    <?php if($module['description']) {
                        echo '<div class="description">'.$module['description'].'</div>';
                    } ?>
                    <div class="file"><?php echo str_replace(ABSPATH,'',$module['file']); ?></div>
                </div><?php
            }
            ?>
            <div class="clear"></div>
            <div class="submit">
                <button type="submit" class="button button-primary">提交</button>
                <input type="hidden" name="action" value="wp_share_modules">
            </div>
        </div>
        </form>
    </div>
</div>