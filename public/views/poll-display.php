<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="aps-poll-wrapper">
    <div class="aps-poll-header">
        <h3 class="aps-poll-question"><?php echo esc_html($poll->poll_question); ?></h3>
        <p class="aps-total-votes">
            <?php
            /* translators: %s: total number of votes */
            printf(esc_html__('Total Votes: %s', 'advance-polling-system'), esc_html($total_votes));
            ?>
        </p>
    </div>
    
    <?php if (!$user_voted): ?>
        <form method="post" action="" class="aps-poll-form">
            <?php wp_nonce_field('aps_vote_action', 'aps_nonce'); ?>
            <input type="hidden" name="poll_id" value="<?php echo esc_attr($poll_id); ?>">
            <input type="hidden" name="aps_vote_poll" value="1">
            
            <?php foreach ($options_for_form as $option): ?>
                <label class="aps-poll-option-label">
                    <input type="radio" name="poll_option" value="<?php echo esc_attr($option->id); ?>" required>
                    <span class="aps-option-text"><?php echo esc_html($option->option_text); ?></span>
                </label>
            <?php endforeach; ?>
            
            <button type="submit" class="aps-vote-button">
                <?php esc_html_e('Vote', 'advance-polling-system'); ?>
            </button>
        </form>
    <?php else: ?>
        <?php if ($just_voted): ?>
            <div class="aps-vote-success-message" id="apsVoteSuccessMessage">
                <span class="aps-success-icon">✓</span> 
                <?php esc_html_e('Thank you for voting!', 'advance-polling-system'); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($already_voted): ?>
            <div class="aps-vote-error-message">
                <span class="aps-error-icon">⚠</span> 
                <?php esc_html_e('You have already voted in this poll!', 'advance-polling-system'); ?>
            </div>
        <?php endif; ?>
        
        <div class="aps-poll-results">
            <?php foreach ($options_for_results as $option): 
                $percentage = $total_votes > 0 ? round(($option->votes / $total_votes) * 100) : 0;
            ?>
                <div class="aps-result-row">
                    <div class="aps-option-label">
                        <?php echo esc_html(strtoupper($option->option_text)); ?>
                    </div>
                    <div class="aps-progress-container">
                        <div class="aps-progress-bar-bg">
                            <div class="aps-progress-bar-fill" style="width: <?php echo esc_attr($percentage); ?>%;"></div>
                        </div>
                        <div class="aps-percentage-box">
                            <?php echo esc_html($percentage); ?>%
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($results_limit && count($options_for_form) > $results_limit): ?>
                <p class="aps-showing-text">
                    <?php
                    // translators: %d: number of top results shown (e.g., 3, 5, 10)
                    printf(esc_html__('Showing top %d results', 'advance-polling-system'), (int) $results_limit);
                    ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($just_voted): ?>
<script>
(function() {
    var successMessage = document.getElementById('apsVoteSuccessMessage');
    if (successMessage) {
        setTimeout(function() {
            successMessage.classList.add('aps-fade-out');
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
})();
</script>
<?php endif; ?>
