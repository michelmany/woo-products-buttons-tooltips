(function($) {

    $(document).ready(function() {
        let sku;
        let currentPage = 1;
        let isLoading = false;

        const pagesSkuInTheButton = [
            'page-id-20403',
            'page-id-20423',
            'page-id-20307',
            'page-id-20378',
            'page-id-20447',
            'page-id-24690',
            'page-id-20403',
            'page-id-20392',
            'page-id-20304',
            'page-id-20293',
            'page-id-19706',
        ];

        const anchorSelector = `
                .apply-labkings-tooltips .wpb_text_column table td a,
                .apply-labkings-tooltips .wpb_text_column table td a button,
                .apply-labkings-tooltips .wpb_single_image + .wpb_text_column table td + td a,
                .apply-labkings-tooltips .wpb_single_image + .wpb_text_column table td a
            `;

        function loadProductDetails(page, sku, elem = null) {
            if (
                $(elem).hasClass('tooltip') && $(elem).hasClass('has-price')
                || $(elem).hasClass('tooltip') && $(elem).hasClass('has-error'))
            {
                // console.log('has price');
                isLoading = false;
                return;
            }

            isLoading = true;

            if (isLoading) {
                $(elem).html('Loading price...');
            }

            // console.log(sku);

            $.ajax({
                method: 'GET',
                url: '/wp-json/labkings/v1/product/' + sku,
                success: function(response) {
                    if (response) {
                        isLoading = false;

                        $(elem).html(response).addClass('has-price');
                    } else {
                        isLoading = false;
                        console.error('Failed to fetch product details.');
                    }
                },
                error: function() {
                    $(elem).html('No price available').addClass('has-error');
                    console.error('AJAX request failed.');
                    isLoading = false;
                },
            });
        }

        let isPageWithSkuInTheButton = pagesSkuInTheButton.some(function(className) {
            return $('body').hasClass(className);
        });

        $(anchorSelector).each(function() {
            $(this).prepend('<div class="tooltip">Loading price...</div>');
        });

        $(anchorSelector).on('mouseenter', function() {
            let $tdWrapper = $(this).parent();
            let $tdBefore = $tdWrapper.prev();
            let $tooltip = $tdWrapper.find('div.tooltip');

            sku = isPageWithSkuInTheButton
                ? $(this).find('button').text().toString().trim()
                : $tdBefore.text().toString().trim();

            loadProductDetails(currentPage, sku, $tooltip);
        });
    });
})(jQuery);
