<?php

function get_tag_array(string $tags): array
{
    return array_values(array_filter(array_map('trim', explode(',', $tags))));
}
