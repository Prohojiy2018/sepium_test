<?php

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/src/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

function property($property)
{
    $placeProp = htmlspecialchars((string)$property['place_prop'], ENT_QUOTES, 'UTF-8');
    $nameProp = htmlspecialchars((string)$property['name_prop'], ENT_QUOTES, 'UTF-8');
    $idProp = htmlspecialchars((string)$property['id'], ENT_QUOTES, 'UTF-8');

    $place = '';
    if ($placeProp != '') {
        $place = '<div class="field-help">' . $placeProp . '</div>';
    }

    $allOption = '';

    if ($property['type_prop'] == '1') {
        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $nameProp . '</div>
            ' . $place . '
            <input type="text" class="text-input add-inp ag_pole_good" placeholder="' . $nameProp . '">
        </div>';
    } elseif ($property['type_prop'] == '2') {
        $answers = db()->query(
            "SELECT * FROM property_answer_s WHERE id_prop = '" . (int)$property['id'] . "' ORDER BY sort_answer"
        );

        while ($answer = $answers->fetch()) {
            $ansId = htmlspecialchars((string)$answer['id'], ENT_QUOTES, 'UTF-8');
            $ansProp = htmlspecialchars((string)$answer['answer_prop'], ENT_QUOTES, 'UTF-8');
            $allOption .= '<option value="' . $ansId . '">' . $ansProp . '</option>';
        }

        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $nameProp . '</div>
            ' . $place . '
            <select class="text-input ag_pole_good">
                <option value="">Не выбрано</option>' . $allOption . '
            </select>
        </div>';
    } elseif ($property['type_prop'] == '3') {
        $answers = db()->query(
            "SELECT * FROM property_answer_s WHERE id_prop = '" . (int)$property['id'] . "' ORDER BY sort_answer"
        );
        $checkboxes = '';

        while ($answer = $answers->fetch()) {
            $ansId = htmlspecialchars((string)$answer['id'], ENT_QUOTES, 'UTF-8');
            $ansProp = htmlspecialchars((string)$answer['answer_prop'], ENT_QUOTES, 'UTF-8');
            $checkboxes .= '<label class="choice line_chek">
                <input type="checkbox">
                <span class="ckeck_param" data-val="' . $ansId . '">' . $ansProp . '</span>
            </label>';
        }

        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $nameProp . '</div>
            ' . $place . '
            <div class="choice-grid checkbox_property ag_pole_good">' . $checkboxes . '</div>
        </div>';
    } elseif ($property['type_prop'] == '4') {
        $result = '<div class="property-field name_select_rielt" data-property="' . $idProp . '" data-property-id="' . $idProp . '">
            <div class="field-label name">' . $nameProp . '</div>
            ' . $place . '
            <input type="text" inputmode="decimal" class="text-input add-inp ag_pole_good" placeholder="Числовое значение">
        </div>';
    } else {
        $result = '';
    }

    return $result;
}

// Защита от фатальных ошибок: если category пустой/не массив, используем пустой массив (вернутся только общие)
$category = isset($_POST['category']) && is_array($_POST['category']) ? $_POST['category'] : array();
$result = '';

$properties = db()->query("SELECT * FROM property_s ORDER BY sort_prop");

while ($property = $properties->fetch()) {
    $catProp = trim((string)$property['cat_prop']);
    $show = false;

    if ($catProp === '') {
        $show = true; // Общие характеристики выводятся всегда
    } else {
        // Проверяем вхождение ID выбранных категорий в список характеристик
        $propCats = explode(',', $catProp);
        foreach ($category as $catId) {
            if (in_array(trim((string)$catId), $propCats)) {
                $show = true;
                break;
            }
        }
    }

    if ($show) {
        $result .= property($property);
    }
}

echo $result;
