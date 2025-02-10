jQuery(document).ready(function ($) {
    function showPopup() {
        $('#builder-popup').fadeIn();
        $('body').addClass('popup-active');
    }

    function closePopup() {
        $('#builder-popup').fadeOut();
        $('body').removeClass('popup-active');
    }

    function startProcessing(siteId) {
        $('#builder-popup').hide();
        $('#builder-progress').show();
        processStep(1, siteId);
    }

    function processStep(step, siteId) {
        var steps = [
            'prepare',
            'generate_sitemap',
            'generate_pages',
            'basic_setup',
            'final_setup',
            'site_ready'
        ];

        if (step >= steps.length) {
            $('#step-5 .status').html('✔');
            $('#visit-site-btn').show();
            return;
        }

        var stepKey = steps[step];

        $.post(builder_ajax.ajax_url, { 
            action: stepKey, 
            site_id: siteId 
        }, function (response) {
            if (response.success) {
                $('#step-' + (step + 1) + ' .status').html('✔');
                processStep(step + 1, siteId);
            }
        });
    }

    // Open popup on page load if needed
    if ($('#builder-popup').length) {
        showPopup();
    }

    // Handle form submission via AJAX
    $('#builder-form').on('submit', function (e) {
        e.preventDefault();
        var data = {
            action: 'prefix_site_submission',
            nonce: builder_ajax.nonce,
            site_name: $('input[name="site_name"]').val(),
            category: $('select[name="category"]').val(),
            description: $('textarea[name="description"]').val(),
        };

        $.post(builder_ajax.ajax_url, data, function (response) {
            if (response.success) {
                startProcessing(response.data.site_id);
            }
        });
    });
});