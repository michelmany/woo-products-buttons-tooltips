(function($) {

    $(document).ready(function() {
        let cache = {};
        let debounceTimer;
        let sku;
        let anchorSelector;

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

        if(isPageWithSkyInTheButton) {
            anchorSelector = '.apply-labkings-tooltips .wpb_text_column table td a';
        } else {
            anchorSelector = '.apply-labkings-tooltips .wpb_single_image + .wpb_text_column table td + td a';
        }

        $(anchorSelector).each(function() {
            $(this).prepend('<div class="tooltip">Loading price...</div>');
        });

        $(anchorSelector).on('mouseenter', function() {
            let $tdWrapper = $(this).parent();
            let $tdBefore = $tdWrapper.prev();

            if (isPageWithSkyInTheButton) {
                sku = $(this).find('button').text().toString().trim();
            } else {
                sku = $tdBefore.text().toString().trim();
            }

            if (cache[sku]) {
                $tdWrapper.find('div.tooltip').html(cache[sku]);
            } else {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    console.time('AJAX Request'); // Start timing here
                    axios({
                        method: 'post',
                        url: ajax_var.url,
                        data: {
                            action: ajax_var.action,
                            nonce: ajax_var.nonce,
                            sku: sku,
                        },
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                    }).then(function(response) {
                        console.timeEnd('AJAX Request'); // End timing here
                        cache[sku] = response.data;
                        $tdWrapper.find('div.tooltip').html(response.data);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }, 300);
            }
        });
    });

})(jQuery);