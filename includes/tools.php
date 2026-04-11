<?php

function get_tag_array(string $tags): array
{
    return array_values(array_filter(array_map('trim', explode(',', $tags))));
}

function get_main_number(int $count, $main_values = [10, 25, 50, 666, 75]): int
{
    $count_floored_10 = pow(10, floor(log10($count)));
    foreach (array_reverse($main_values) as $_ => $m) {
        if ($m > $count)
            continue;
        $floating_main = floatval($m) / pow(10, floor(log10($m)));
        $cap = $floating_main * $count_floored_10;
        if ($count >= $cap)
            return $cap;
    }
    return 0;
}
?>