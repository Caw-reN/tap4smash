<?php

if (!function_exists('format_jam_main')) {
    function format_jam_main($jamMainStr)
    {
        if (empty($jamMainStr)) return '—';
        
        $slots = explode(',', $jamMainStr);
        $slots = array_map('intval', array_map('trim', $slots));
        sort($slots);
        
        $ranges = [];
        $start = $slots[0];
        $prev = $start;
        
        for ($i = 1; $i < count($slots); $i++) {
            if ($slots[$i] === $prev + 1) {
                $prev = $slots[$i];
            } else {
                $ranges[] = sprintf('%02d:00 - %02d:00', $start, $prev + 1);
                $start = $slots[$i];
                $prev = $start;
            }
        }
        $ranges[] = sprintf('%02d:00 - %02d:00', $start, $prev + 1);
        
        return implode(', ', $ranges);
    }
}
