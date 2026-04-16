<?php
function image($path)
{
    return str_replace(' ', '%20', Storage::disk('external')->url($path));
}

function insert_between_keys($array, $after_key, $new_key, $new_value) {
    $keys = array_keys($array);
    // Find the position (index) of the key we want to insert after
    $position = array_search($after_key, $keys);

    // If the key exists, increment position to insert *after* it
    if ($position !== false) {
        $position += 1;
    } else {
        // If not found, add to the end
        $position = count($array);
    }

    // Split the array into two parts
    $array_part1 = array_slice($array, 0, $position, true); // Retain keys
    $array_part2 = array_slice($array, $position, null, true); // Retain keys

    // Create the new key-value pair as an array
    $new_pair = [$new_key => $new_value];

    // Combine the parts with the new pair in the middle using the union operator (+)
    $new_array = $array_part1 + $new_pair + $array_part2;

    return $new_array;
}

function array_export(array $array, int $indent = 0): string
{
    $pad = str_repeat('    ', $indent);
    $inner = str_repeat('    ', $indent + 1);
    
    if (empty($array)) {
        return '[]';
    }

    $items = [];
    $isList = array_is_list($array);

    foreach ($array as $key => $value) {
        $exportedValue = is_array($value)
            ? array_export($value, $indent + 1)
            : var_export($value, true);

        $items[] = $isList
            ? "{$inner}{$exportedValue}"
            : "{$inner}" . var_export($key, true) . " => {$exportedValue}";
    }

    return "[\n" . implode(",\n", $items) . ",\n{$pad}]";
}