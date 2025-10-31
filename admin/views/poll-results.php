<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap aps-admin-wrapper">
    <div class="aps-header">
        <h1>
            <?php
            /* translators: %s: poll title */
            printf(esc_html__('Poll Results: %s', 'advance-polling-system'), esc_html($poll->poll_title));
            ?>
        </h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=aps-polls')); ?>" class="button">
            <span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
            <?php esc_html_e('Back to Polls', 'advance-polling-system'); ?>
        </a>
    </div>
    
    <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="margin: 0 0 10px 0; color: #334155;"><?php esc_html_e('Question:', 'advance-polling-system'); ?></h3>
        <p style="font-size: 16px; color: #475569; margin: 0;"><?php echo esc_html($poll->poll_question); ?></p>
    </div>
    
    <div class="aps-stats-grid">
        <div class="aps-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="aps-stat-label"><?php esc_html_e('Total Votes', 'advance-polling-system'); ?></div>
            <div class="aps-stat-value"><?php echo esc_html($total_votes); ?></div>
        </div>
        <div class="aps-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="aps-stat-label"><?php esc_html_e('Total Options', 'advance-polling-system'); ?></div>
            <div class="aps-stat-value"><?php echo esc_html($option_count); ?></div>
        </div>
        <div class="aps-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="aps-stat-label"><?php esc_html_e('Leading Option', 'advance-polling-system'); ?></div>
            <div class="aps-stat-value"><?php echo $total_votes > 0 ? esc_html($options[0]->votes) : 0; ?></div>
        </div>
    </div>
    
    <div class="aps-results-container">
        <div class="aps-chart-box">
            <h3 style="margin-top: 0; color: #1e293b;"><?php esc_html_e('Votes Distribution (Bar Chart)', 'advance-polling-system'); ?></h3>
            <canvas id="apsBarChart" width="400" height="300"></canvas>
        </div>
        <div class="aps-chart-box">
            <h3 style="margin-top: 0; color: #1e293b;"><?php esc_html_e('Votes Distribution (Pie Chart)', 'advance-polling-system'); ?></h3>
            <canvas id="apsPieChart" width="400" height="300"></canvas>
        </div>
    </div>
    
    <div class="aps-chart-box" style="margin-top: 30px;">
        <h3 style="margin-top: 0; color: #1e293b;"><?php esc_html_e('Detailed Results', 'advance-polling-system'); ?></h3>
        <table class="aps-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Rank', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Option', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Votes', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Percentage', 'advance-polling-system'); ?></th>
                    <th><?php esc_html_e('Progress', 'advance-polling-system'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($options as $option): 
                    $percentage = $total_votes > 0 ? round(($option->votes / $total_votes) * 100, 1) : 0;
                ?>
                <tr>
                    <td><span class="aps-badge">#<?php echo esc_html($rank++); ?></span></td>
                    <td><strong><?php echo esc_html($option->option_text); ?></strong></td>
                    <td><?php echo esc_html($option->votes); ?></td>
                    <td><?php echo esc_html($percentage); ?>%</td>
                    <td>
                        <div style="width: 100%; background: #e2e8f0; border-radius: 10px; overflow: hidden; height: 20px;">
                            <div style="width: <?php echo esc_attr($percentage); ?>%; background: linear-gradient(90deg, #667eea, #764ba2); height: 100%;"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
    function initCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(initCharts, 100);
            return;
        }
        
        var barCanvas = document.getElementById('apsBarChart');
        if (barCanvas) {
            new Chart(barCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?php echo wp_json_encode($labels); ?>,
                    datasets: [{
                        label: <?php echo wp_json_encode(esc_html__('Votes', 'advance-polling-system')); ?>,
                        data: <?php echo wp_json_encode($votes); ?>,
                        backgroundColor: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)', 'rgba(240, 147, 251, 0.8)', 'rgba(79, 172, 254, 0.8)', 'rgba(245, 87, 108, 0.8)', 'rgba(67, 233, 123, 0.8)', 'rgba(252, 176, 69, 0.8)'],
                        borderColor: ['rgba(102, 126, 234, 1)', 'rgba(118, 75, 162, 1)', 'rgba(240, 147, 251, 1)', 'rgba(79, 172, 254, 1)', 'rgba(245, 87, 108, 1)', 'rgba(67, 233, 123, 1)', 'rgba(252, 176, 69, 1)'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
        
        var pieCanvas = document.getElementById('apsPieChart');
        if (pieCanvas) {
            new Chart(pieCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: <?php echo wp_json_encode($labels); ?>,
                    datasets: [{
                        data: <?php echo wp_json_encode($votes); ?>,
                        backgroundColor: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)', 'rgba(240, 147, 251, 0.8)', 'rgba(79, 172, 254, 0.8)', 'rgba(245, 87, 108, 0.8)', 'rgba(67, 233, 123, 0.8)', 'rgba(252, 176, 69, 0.8)'],
                        borderColor: '#fff',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }
})();
</script>
