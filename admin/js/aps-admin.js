jQuery(document).ready(function($) {
    
    // Track option count
    var optionCount = $('#aps-options-container .aps-option-item').length;
    
    // Add option button - Use event delegation
    $(document).on('click', '#aps-add-option', function(e) {
        e.preventDefault(); // Prevent default button behavior
    
        optionCount++;
        
        // Check if we're on edit page by looking for existing option_id fields
        var isEditPage = $('#aps-options-container').find('input[name="option_id[]"]').length > 0;
        
        var newOptionHtml = '';
        
        if (isEditPage) {
            // For EDIT page - use option_id[] and option_text[]
            newOptionHtml = 
                '<div class="aps-option-item">' +
                    '<input type="hidden" name="option_id[]" value="0">' +
                    '<input type="text" name="option_text[]" class="aps-input" placeholder="New Option ' + optionCount + '" required>' +
                    '<button type="button" class="aps-btn-remove aps-remove-option">Remove</button>' +
                '</div>';
        } else {
            // For ADD page - use poll_options[]
            newOptionHtml = 
                '<div class="aps-option-item">' +
                    '<input type="text" name="poll_options[]" class="aps-input" placeholder="Option ' + optionCount + '">' +
                    '<button type="button" class="aps-btn-remove aps-remove-option">Remove</button>' +
                '</div>';
        }
        
        $('#aps-options-container').append(newOptionHtml);
        
        // Focus on the new input
        $('#aps-options-container .aps-option-item:last-child input[type="text"]').focus();
    });
    
    // Remove option button - Use event delegation
    $(document).on('click', '.aps-remove-option', function(e) {
        e.preventDefault(); // Prevent default
        
        console.log('Remove option clicked'); // Debug log
        
        if (confirm('Are you sure you want to remove this option?')) {
            $(this).closest('.aps-option-item').remove();
        }
    });
    
    // Copy shortcode on click
    $(document).on('click', '.aps-shortcode', function() {
        var shortcode = $(this).text();
        
        // Create temporary input
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(shortcode).select();
        
        // Copy to clipboard
        try {
            var successful = document.execCommand('copy');
            if (successful) {
                // Show visual feedback
                var $this = $(this);
                var originalBg = $this.css('background-color');
                $this.css('background-color', '#10b981');
                
                setTimeout(function() {
                    $this.css('background-color', originalBg);
                }, 500);
                
                console.log('Shortcode copied:', shortcode);
            }
        } catch (err) {
            console.error('Failed to copy shortcode:', err);
        }
        
        $temp.remove();
    });
    
});
