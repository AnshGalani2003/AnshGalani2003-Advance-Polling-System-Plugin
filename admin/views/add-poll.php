<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap aps-admin-wrapper">
    <div class="aps-header">
        <h1><?php esc_html_e('Add New Poll', 'advance-polling-system'); ?></h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=aps-polls')); ?>" class="button">
            <span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
            <?php esc_html_e('Back to Polls', 'advance-polling-system'); ?>
        </a>
    </div>
    
    <form method="post" action="">
        <?php wp_nonce_field('aps_add_poll', 'aps_nonce'); ?>
        
        <table class="form-table aps-form-table">
            <tr>
                <th><label for="poll_title"><?php esc_html_e('Poll Title', 'advance-polling-system'); ?> *</label></th>
                <td><input type="text" name="poll_title" id="poll_title" class="aps-input" required></td>
            </tr>
            <tr>
                <th><label for="poll_question"><?php esc_html_e('Poll Question', 'advance-polling-system'); ?> *</label></th>
                <td><textarea name="poll_question" id="poll_question" class="aps-textarea" required></textarea></td>
            </tr>
            
            <!-- Verification Method -->
            <tr>
                <th><label for="verification_method"><?php esc_html_e('Verification Method', 'advance-polling-system'); ?> *</label></th>
                <td>
                    <select name="verification_method" id="verification_method" class="aps-input" style="width: auto;">
                        <option value="cookie"><?php esc_html_e('Cookie (Browser-based)', 'advance-polling-system'); ?></option>
                        <option value="ip"><?php esc_html_e('IP Address (Device-based)', 'advance-polling-system'); ?></option>
                    </select>
                    <p class="description">
                        <strong><?php esc_html_e('Cookie:', 'advance-polling-system'); ?></strong> <?php esc_html_e('Tracks votes per browser. Users can vote again from a different browser.', 'advance-polling-system'); ?><br>
                        <strong><?php esc_html_e('IP Address:', 'advance-polling-system'); ?></strong> <?php esc_html_e('Tracks votes per device/network. More secure but may affect multiple users on same network.', 'advance-polling-system'); ?>
                    </p>
                </td>
            </tr>
            
            <!-- Results Display -->
            <tr>
                <th><label for="results_display"><?php esc_html_e('Results Display', 'advance-polling-system'); ?> *</label></th>
                <td>
                    <select name="results_display" id="results_display" class="aps-input" style="width: auto;">
                        <option value="top3" selected><?php esc_html_e('Top 3 Results', 'advance-polling-system'); ?></option>
                        <option value="top5"><?php esc_html_e('Top 5 Results', 'advance-polling-system'); ?></option>
                        <option value="top10"><?php esc_html_e('Top 10 Results', 'advance-polling-system'); ?></option>
                        <option value="all"><?php esc_html_e('All Results', 'advance-polling-system'); ?></option>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Choose how many poll results to display on the frontend after voting.', 'advance-polling-system'); ?>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th><label><?php esc_html_e('Poll Options', 'advance-polling-system'); ?> *</label></th>
                <td>
                    <div id="aps-options-container">
                        <div class="aps-option-item">
                            <input type="text" name="poll_options[]" class="aps-input" placeholder="<?php esc_attr_e('Option 1', 'advance-polling-system'); ?>" required>
                        </div>
                        <div class="aps-option-item">
                            <input type="text" name="poll_options[]" class="aps-input" placeholder="<?php esc_attr_e('Option 2', 'advance-polling-system'); ?>" required>
                        </div>
                        <div class="aps-option-item">
                            <input type="text" name="poll_options[]" class="aps-input" placeholder="<?php esc_attr_e('Option 3', 'advance-polling-system'); ?>">
                        </div>
                    </div>
                    <button type="button" id="aps-add-option" class="button aps-btn-add" style="margin-top: 10px;">
                        <span class="dashicons dashicons-plus" style="vertical-align: middle;"></span> 
                        <?php esc_html_e('Add More Options', 'advance-polling-system'); ?>
                    </button>
                </td>
            </tr>
        </table>
        <?php submit_button(esc_html__('Create Poll', 'advance-polling-system'), 'primary aps-btn-primary', 'submit_poll'); ?>
    </form>
</div>
