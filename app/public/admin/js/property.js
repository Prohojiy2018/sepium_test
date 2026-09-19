(function ($) {
    'use strict';

    // Выбор категории в товаре и обновление блока характеристик.
    $('body').on('change', '.js-category', function () {
        var category = [];
        var $properties = $('.property_all');

        $(this).closest('.add_good_name_category')
            .toggleClass('category_checked is-selected', this.checked);

        $('.category_checked').each(function () {
            category[category.length] = $(this).attr('data-category-id');
        });

        // 1. Сохраняем введенные данные перед обновлением HTML
        var savedValues = {};
        $('.name_select_rielt').each(function () {
            var propId = $(this).attr('data-property-id');
            var $checkboxes = $(this).find('input[type="checkbox"]:checked');

            if ($checkboxes.length > 0) {
                savedValues[propId] = [];
                $checkboxes.each(function () {
                    savedValues[propId].push($(this).siblings('.ckeck_param').attr('data-val'));
                });
            } else {
                var val = $(this).find('input[type="text"], input[inputmode="decimal"], select').val();
                if (val !== undefined && val !== '') {
                    savedValues[propId] = val;
                }
            }
        });

        $properties.addClass('is-loading').attr('aria-busy', 'true');

        $.ajax({
            type: 'POST',
            url: './admin/ajax/property/Refresh_Property_Good.php',
            dataType: 'html',
            data: { category: category },
            success: function (data) {
                if (data != 'no') {
                    $properties.html(data);

                    // 2. Восстанавливаем сохраненные значения
                    $('.name_select_rielt').each(function () {
                        var propId = $(this).attr('data-property-id');
                        if (savedValues[propId] !== undefined) {
                            var savedVal = savedValues[propId];

                            if ($.isArray(savedVal)) {
                                $(this).find('input[type="checkbox"]').each(function () {
                                    var cbVal = $(this).siblings('.ckeck_param').attr('data-val');
                                    if ($.inArray(cbVal, savedVal) !== -1) {
                                        this.checked = true;
                                    }
                                });
                            } else {
                                $(this).find('input[type="text"], input[inputmode="decimal"], select').val(savedVal);
                            }
                        }
                    });
                } else {
                    $properties.empty();
                }
            },
            error: function () {
                $properties.html('<div class="error-state">Не удалось обновить характеристики.</div>');
            },
            complete: function () {
                $properties.removeClass('is-loading').attr('aria-busy', 'false');
            }
        });
    });
}(jQuery));
