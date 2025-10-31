<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap aps-admin-wrapper">
    <div class="aps-header">
        <h1><?php esc_html_e('All Polls', 'advance-polling-system'); ?></h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=aps-polls-add')); ?>" class="button aps-btn-primary">
            <span class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></span>
            <?php esc_html_e('Add New', 'advance-polling-system'); ?>
        </a>
    </div>
    <?php if (empty($polls)): ?>
        <p class="no-result-found"><?php esc_html_e('No polls found. Create your first poll!', 'advance-polling-system'); ?></p>
    <?php else: ?>
        <table class="aps-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('ID', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Poll Title', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Question', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Shortcode', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Verification', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Display Total Result', 'advance-polling-system'); ?></th>
                    <!-- <th><?php //esc_html_e('Total Votes', 'advance-polling-system'); 
                                ?></th> -->
                    <th><?php esc_html_e('Actions', 'advance-polling-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($polls as $poll):
                    $total_votes = $this->db->get_total_votes($poll->id);
                ?>
                    <tr>
                        <td><span class="aps-badge">#<?php echo esc_html($poll->id); ?></span></td>
                        <td><strong><?php echo esc_html($poll->poll_title); ?></strong></td>
                        <td><?php echo esc_html(wp_trim_words($poll->poll_question, 10)); ?></td>
                        <td><code class="aps-shortcode">[aps_poll id="<?php echo esc_attr($poll->id); ?>"]</code></td>
                        <td>
                            <span class="aps-badge" style="background: <?php echo $poll->verification_method === 'ip' ? '#fbbf24' : '#60a5fa'; ?>;">
                                <?php echo esc_html($poll->verification_method === 'ip' ? esc_html__('IP Address', 'advance-polling-system') : esc_html__('Cookie', 'advance-polling-system')); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $display_icons = array(
                                'top3' => '3',
                                'top5' => '5',
                                'top10' => '10',
                                'all' => 'All'
                            );
                            $display_icon = isset($display_icons[$poll->results_display]) ? $display_icons[$poll->results_display] : '3';
                            ?>
                            <span class="aps-badge" style="background: #8b5cf6; color: white;">
                                <?php echo esc_html($display_icon); ?>
                            </span>
                        </td>

                        <!-- <td><strong><?php //echo esc_html($total_votes); 
                                            ?></strong> <?php //_e('votes', 'advance-polling-system'); 
                                                        ?></td> -->
                        <td class="aps-actions">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=aps-polls-edit&poll_id=' . $poll->id)); ?>" class="aps-edit">
                                <span class="dashicons dashicons-edit" style="vertical-align: middle;"></span> <?php echo esc_html('Edit', 'advance-polling-system'); ?>
                            </a>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=aps-polls-results&poll_id=' . $poll->id)); ?>" class="aps-results">
                                <span class="dashicons dashicons-chart-bar" style="vertical-align: middle;"></span> <?php echo esc_html('Results', 'advance-polling-system'); ?>
                            </a>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=aps-polls&action=delete&poll_id=' . $poll->id), 'aps_delete_poll')); ?>"
                                class="aps-delete"
                                onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this poll?', 'advance-polling-system'); ?>')">
                                <span class="dashicons dashicons-trash" style="vertical-align: middle;"></span> <?php echo esc_html('Delete', 'advance-polling-system'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>