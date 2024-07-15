(function($) {

    $(document).ready(function() {
        let sku;
        let anchorSelector;

        let productDetails = {};
        let currentPage = 1;
        let isLoading = false;
        let skus = [];
        let tooltips = [];

        function loadProductDetails(page, skus, elem = null) {
            if (isLoading) return;
            isLoading = true;

            $.ajax({
                method: 'POST',
                url: ajax_var.url,
                data: {
                    action: ajax_var.action,
                    nonce: ajax_var.nonce,
                    page: page,
                    skus: skus,
                },
                success: function(response) {
                    if (response.success) {
                        $.extend(productDetails, response.data);
                        currentPage++;

                        console.log(response.data);

                        // console.log('propd:', productDetails)

                        if (productDetails[sku] && elem) {
                            $(elem).html(productDetails[sku].price);
                        }
                    } else {
                        console.error('Failed to fetch product details.');
                    }
                    isLoading = false;
                },
                error: function() {
                    console.error('AJAX request failed.');
                    isLoading = false;
                }
            });
        }

        const pagesSkuInTheButton = [
            'page-id-20403',
            'page-id-20423',
            'page-id-20307',
            'page-id-20378',
            'page-id-20447',
            'page-id-24690',
            'page-id-20403',
            'page-id-20392',
        ];

        let isPageWithSkyInTheButton = pagesSkuInTheButton.some(function(className) {
            return $('body').hasClass(className);
        });

        let regularPagesSelector = '.apply-labkings-tooltips .wpb_text_column table td a';
        let pagesWithSkuSelector = '.apply-labkings-tooltips .wpb_single_image + .wpb_text_column table td + td a';

        anchorSelector = isPageWithSkyInTheButton ? regularPagesSelector : pagesWithSkuSelector;

        $(anchorSelector).each(function() {
            $(this).prepend('<div class="tooltip">Loading price...</div>');
        });

        function checkViewport() {

            $(anchorSelector).each(function() {
                let rect = this.getBoundingClientRect();
                let isInViewport = rect.top < window.innerHeight && rect.bottom >= 0;

                let $tdWrapper = $(this).parent();
                let $tdBefore = $tdWrapper.prev();

                sku = isPageWithSkyInTheButton
                    ? $(this).find('button').text().toString().trim()
                    : $tdBefore.text().toString().trim();

                if (isInViewport) {
                    if (!skus.includes(sku)) {
                        skus.push(sku);
                    }
                } else {
                    const index = skus.indexOf(sku);
                    if (index > -1) {
                        skus.splice(index, 1);
                    }
                }
            });

            console.log(skus);

            if (skus.length > 0) {
                console.log('loading from viewport');
                loadProductDetails(currentPage, skus);
            }
        }

        // Initial check
        checkViewport();

        // Check on scroll
        let debounceTimeout;
        $(window).on('scroll', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(checkViewport, 300);
        });


        $(anchorSelector).on('mouseenter', function() {
            let $tdWrapper = $(this).parent();
            let $tdBefore = $tdWrapper.prev();
            let $tooltip = $tdWrapper.find('div.tooltip');

            sku = isPageWithSkyInTheButton
                ? $(this).find('button').text().toString().trim()
                : $tdBefore.text().toString().trim();

            if (isLoading) {
                $tooltip.html('Loading price...');
            }

            if (!productDetails[sku]) {
                console.log('loading from mouseeneter')
                loadProductDetails(currentPage, skus, $tooltip);
            } else if (productDetails[sku]) {
                $tooltip.html(productDetails[sku].price);
            }
        });

    });

})(jQuery);