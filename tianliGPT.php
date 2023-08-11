<?php
/**
 * Plugin Name: TianliGPT
 * Description: 为你的博客添加AI摘要
 * Version: 1.0
 * Author: Tianli
 * Author URI: https://tianli-blog.club/
 */

// 添加插件设置页面
function custom_plugin_settings_page() {
    add_options_page(
        'TianliGPT设置',
        'TianliGPT',
        'manage_options',
        'custom-plugin-settings',
        'custom_plugin_settings_page_content'
    );
}
add_action('admin_menu', 'custom_plugin_settings_page');

// 注册插件设置
function custom_plugin_register_settings() {
    register_setting('custom-plugin-settings-group', 'custom_plugin_version', array(
        'default' => 'heo',
        'sanitize_callback' => 'custom_plugin_sanitize_version'
    ));
    register_setting('custom-plugin-settings-group', 'custom_plugin_key');
    register_setting('custom-plugin-settings-group', 'custom_plugin_post_selector');
    register_setting('custom-plugin-settings-group', 'custom_plugin_heo_version');
    register_setting('custom-plugin-settings-group', 'custom_plugin_chuckle_version');
    register_setting('custom-plugin-settings-group', 'custom_plugin_advanced_config');
}
add_action('admin_init', 'custom_plugin_register_settings');

// 插件设置页面内容
function custom_plugin_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>TianliGPT设置</h1>
        <form method="post" action="options.php">
            <?php settings_fields('custom-plugin-settings-group'); ?>
            <?php do_settings_sections('custom-plugin-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">版本</th>
                    <td>
                        <label>
                            <input type="radio" name="custom_plugin_version" value="heo" <?php checked(get_option('custom_plugin_version'), 'heo'); ?>>
                            Heo
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="custom_plugin_version" value="chuckle" <?php checked(get_option('custom_plugin_version'), 'chuckle'); ?>>
                            轻笑
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Key</th>
                    <td><input type="text" name="custom_plugin_key" value="<?php echo esc_attr(get_option('custom_plugin_key')); ?>"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">文章选择器</th>
                    <td><input type="text" name="custom_plugin_post_selector" value="<?php echo esc_attr(get_option('custom_plugin_post_selector')); ?>"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Heo版本号</th>
                    <td><input type="text" name="custom_plugin_heo_version" value="<?php echo esc_attr(get_option('custom_plugin_heo_version')); ?>"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">轻笑版本号</th>
                    <td><input type="text" name="custom_plugin_chuckle_version" value="<?php echo esc_attr(get_option('custom_plugin_chuckle_version')); ?>"></td>
                </tr>
                <tr valign="top">
                    <th scope="row">高级配置</th>
                    <td><textarea name="custom_plugin_advanced_config" rows="5"><?php echo esc_textarea(get_option('custom_plugin_advanced_config')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <h2 style="color: red;">注意：不要随意切换使用版本！！！！</h2>
        <ul style="display: flex;">
            <li style="margin-right: 10px;"><a class="button" href="https://summary.zhheo.com/admin" target="_blank">TianliGPT管理</a></li>
            <li style="margin-right: 10px;"><a class="button" href="https://summary.zhheo.com/static/matrix.html" target="_blank">网站矩阵</a></li>
            <li style="margin-right: 10px;"><a class="button" href="https://tianli-blog.club/tianligpt-p/" target="_blank">文章矩阵</a></li>
            <li style="margin-right: 10px;"><a class="button" href="https://github.com/zhheo/Post-Abstract-AI" target="_blank">Heo版本FAQ</a></li>
            <li><a class="button" href="https://github.com/qxchuckle/Post-Summary-AI" target="_blank">轻笑版本FAQ</a></li>
        </ul>
    </div>
    <?php
}

// 验证版本号
function custom_plugin_sanitize_version($input) {
    if ($input != 'heo' && $input != 'chuckle') {
        $input = 'heo';
    }
    return $input;
}

// 加载脚本
function custom_plugin_enqueue_scripts() {
    if (is_admin()) {
        return;
    }

    $version = get_option('custom_plugin_version');
    $key = get_option('custom_plugin_key');
    $post_selector = get_option('custom_plugin_post_selector');
    $advanced_config = get_option('custom_plugin_advanced_config');

    if ($version == 'heo') {
        $heo_version = get_option('custom_plugin_heo_version');
        $link = "https://cdn1.tianli0.top/gh/zhheo/Post-Abstract-AI@{$heo_version}/tianli_gpt.css";
        $script = "https://cdn1.tianli0.top/gh/zhheo/Post-Abstract-AI@{$heo_version}/tianli_gpt.js";
        wp_enqueue_style('custom-plugin-heo-css', $link);
        wp_enqueue_script('custom-plugin-heo-js', $script);
    } elseif ($version == 'chuckle') {
        $chuckle_version = get_option('custom_plugin_chuckle_version');
        $script = "https://jsd.onmicrosoft.cn/gh/qxchuckle/Post-Summary-AI@{$chuckle_version}/chuckle-post-ai.js";
        wp_enqueue_script('custom-plugin-chuckle-js', $script);
        wp_add_inline_script('custom-plugin-chuckle-js', $advanced_config, 'after');
    }
}
add_action('wp_enqueue_scripts', 'custom_plugin_enqueue_scripts');

// 添加Heo版本的内容
function custom_plugin_add_heo_content() {
    $post_selector = get_option('custom_plugin_post_selector');
    $key = get_option('custom_plugin_key');
    $advanced_config = get_option('custom_plugin_advanced_config');
    ?>
    <script>
        let tianliGPT_postSelector = '<?php echo esc_js($post_selector); ?>';
        let tianliGPT_key = '<?php echo esc_js($key); ?>';
        <?php echo $advanced_config; ?>
    </script>
    <?php
}
add_action('wp_footer', 'custom_plugin_add_heo_content');

// 添加轻笑版本的内容
function custom_plugin_add_chuckle_content() {
    $post_selector = get_option('custom_plugin_post_selector');
    $key = get_option('custom_plugin_key');
    $rec_method = get_option('custom_plugin_rec_method');
    $advanced_config = get_option('custom_plugin_advanced_config');
    ?>
    <script data-pjax defer>
        new ChucklePostAI({
            el: '<?php echo esc_js($post_selector); ?>',
            key: '<?php echo esc_js($key); ?>',
            rec_method: '<?php echo esc_js($rec_method); ?>',
            <?php echo $advanced_config; ?>
        });
    </script>
    <?php
}
add_action('wp_footer', 'custom_plugin_add_chuckle_content');
