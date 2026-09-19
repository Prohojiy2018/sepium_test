(function ($) {
    'use strict';

    function collectCategories() {
        var cats = [];

        $('.category_checked').each(function () {
            cats[cats.length] = $(this).attr('data-category-chpu');
        });

        return cats;
    }

    // Сбор характеристик со всеми правильными типами и проверками
    function collectPropertyValues() {
        var propertyMas = {};

        $('.name_select_rielt').each(function () {
            var propertyId = $(this).attr('data-property-id');
            var $checkboxes = $(this).find('input[type="checkbox"]:checked');

            // Если это множественный выбор
            if ($checkboxes.length > 0) {
                var checkedVals = [];
                $checkboxes.each(function () {
                    checkedVals.push($(this).siblings('.ckeck_param').attr('data-val'));
                });
                propertyMas[propertyId] = checkedVals.join(':::');
            } else {
                // Текст, число, одиночный селект
                var value = $(this).find('input[type="text"], input[inputmode="decimal"], select').first().val();

                // Пропускаем пустые поля, но оставляем "0"
                if (value !== undefined && value !== null && value !== '') {
                    propertyMas[propertyId] = value;
                }
            }
        });

        return propertyMas;
    }

    $('body').on('click', '.addgood_click', function () {
        var $button = $(this);

        $button.prop('disabled', true).text('Проверяем…');

        $.ajax({
            type: 'POST',
            url: './admin/ajax/Preview_Good_Payload.php',
            dataType: 'json',
            data: {
                cats: collectCategories(),
                property_mas: collectPropertyValues()
            },
            success: function (data) {
                $('.js-payload-preview').text(JSON.stringify(data, null, 2));
            },
            error: function () {
                $('.js-payload-preview').text('Не удалось проверить отправку.');
            },
            complete: function () {
                $button.prop('disabled', false).text('Проверить отправку');
            }
        });
    });
}(jQuery));
