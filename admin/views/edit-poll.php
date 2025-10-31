<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap aps-admin-wrapper">
    <div class="aps-header">
        <h1><?php esc_html_e('Edit Poll', 'advance-polling-system'); ?></h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=aps-polls')); ?>" class="button">
            <span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
            <?php esc_html_e('Back to Polls', 'advance-polling-system'); ?>
        </a>
    </div>
    
    <form method="post" action="">
        <?php wp_nonce_field('aps_edit_poll', 'aps_nonce'); ?>
        
        <table class="form-table aps-form-table">
            <tr>
                <th><label for="poll_title"><?php esc_html_e('Poll Title', 'advance-polling-system'); ?> *</label></th>
                <td><input type="text" name="poll_title" id="poll_title" class="aps-input" value="<?php echo esc_attr($poll->poll_title); ?>" required></td>
            </tr>
            <tr>
                <th><label for="poll_question"><?php esc_html_e('Poll Question', 'advance-polling-system'); ?> *</label></th>
                <td><textarea name="poll_question" id="poll_question" class="aps-textarea" required><?php echo esc_textarea($poll->poll_question); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="verification_method"><?php esc_html_e('Verification Method', 'advance-polling-system'); ?> *</label></th>
                <td>
                    <select name="verification_method" id="verification_method" class="aps-input" style="width: auto;">
                        <option value="cookie" <?php selected($poll->verification_method, 'cookie'); ?>><?php esc_html_e('Cookie (Browser-based)', 'advance-polling-system'); ?></option>
                        <option value="ip" <?php selected($poll->verification_method, 'ip'); ?>><?php esc_html_e('IP Address (Device-based)', 'advance-polling-system'); ?></option>
                    </select>
                    <p class="description">
                        <strong><?php esc_html_e('Current:', 'advance-polling-system'); ?></strong> 
                        <?php echo $poll->verification_method === 'ip' ? esc_html__('IP Address', 'advance-polling-system') : esc_html__('Cookie', 'advance-polling-system'); ?>
                    </p>
                </td>
            </tr>
            
            <!-- Results Display -->
            <tr>
                <th><label for="results_display"><?php esc_html_e('Results Display', 'advance-polling-system'); ?> *</label></th>
                <td>
                    <select name="results_display" id="results_display" class="aps-input" style="width: auto;">
                        <option value="top3" <?php selected($poll->results_display, 'top3'); ?>><?php esc_html_e('Top 3 Results', 'advance-polling-system'); ?></option>
                        <option value="top5" <?php selected($poll->results_display, 'top5'); ?>><?php esc_html_e('Top 5 Results', 'advance-polling-system'); ?></option>
                        <option value="top10" <?php selected($poll->results_display, 'top10'); ?>><?php esc_html_e('Top 10 Results', 'advance-polling-system'); ?></option>
                        <option value="all" <?php selected($poll->results_display, 'all'); ?>><?php esc_html_e('All Results', 'advance-polling-system'); ?></option>
                    </select>
                    <p class="description">
                        <strong><?php esc_html_e('Current:', 'advance-polling-system'); ?></strong> 
                        <?php
                        $display_labels = array(
                            'top3' => __('Top 3 Results', 'advance-polling-system'),
                            'top5' => __('Top 5 Results', 'advance-polling-system'),
                            'top10' => __('Top 10 Results', 'advance-polling-system'),
                            'all' => __('All Results', 'advance-polling-system')
                        );
                        echo isset($display_labels[$poll->results_display]) ? esc_html($display_labels[$poll->results_display]) : esc_html__('Top 3 Results', 'advance-polling-system');
                        ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th><label><?php esc_html_e('Poll Options', 'advance-polling-system'); ?> *</label></th>
                <td>
                    <div id="aps-options-container">
                        <?php foreach ($options as $option): ?>
                        <div class="aps-option-item">
                            <input type="hidden" name="option_id[]" value="<?php echo esc_attr($option->id); ?>">
                            <input type="text" name="option_text[]" class="aps-input" value="<?php echo esc_attr($option->option_text); ?>" required>
                            <span style="margin: 0 10px; color: #64748b;">
                                <?php echo esc_html($option->votes); ?> 
                                <?php esc_html_e('votes', 'advance-polling-system'); ?>
                            </span>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=aps-polls-edit&action=delete_option&poll_id=' . $poll_id . '&option_id=' . $option->id), 'aps_delete_option')); ?>" 
                               class="aps-btn-remove" 
                               onclick="return confirm('<?php echo esc_js(esc_attr__('Delete this option? All votes will be lost!', 'advance-polling-system')); ?>')"
                               style="text-decoration: none;">
                                <?php esc_html_e('Remove', 'advance-polling-system'); ?>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="aps-add-option" class="button aps-btn-add" style="margin-top: 10px;">
                        <span class="dashicons dashicons-plus" style="vertical-align: middle;"></span> 
                        <?php esc_html_e('Add More Options', 'advance-polling-system'); ?>
                    </button>
                </td>
            </tr>
        </table>
        <?php submit_button(esc_html__('Update Poll', 'advance-polling-system'), 'primary aps-btn-primary', 'update_poll'); ?>
    </form>
</div>
