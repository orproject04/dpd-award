<?php

if (!function_exists('colorNameToHex')) {
    function colorNameToHex($name)
    {
        $colors = [
            'red'     => '#db2828',
            'orange'  => '#f2711c',
            'yellow'  => '#fbbd08',
            'olive'   => '#b5cc18',
            'green'   => '#21ba45',
            'teal'    => '#00b5ad',
            'blue'    => '#2185d0',
            'violet'  => '#6435c9',
            'purple'  => '#a333c8',
            'pink'    => '#e03997',
            'brown'   => '#a5673f',
            'grey'    => '#767676',
            'black'   => '#1b1c1d',
        ];

        return $colors[strtolower($name)] ?? $name;
    }
}

if (!function_exists('hexToRgba')) {
    function hexToRgba($color, $opacity = 1.0)
    {
        $color = colorNameToHex($color);
        $color = str_replace('#', '', $color);

        if (strlen($color) === 3) {
            $r = hexdec(str_repeat(substr($color, 0, 1), 2));
            $g = hexdec(str_repeat(substr($color, 1, 1), 2));
            $b = hexdec(str_repeat(substr($color, 2, 1), 2));
        } else {
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));
        }

        return "rgba($r, $g, $b, $opacity)";
    }
}

if (!function_exists('get_backlink_url')) {
    function get_backlink_url(?string $defaultUrl = null): string
    {
        $defaultUrl = $defaultUrl ?? url()->previous();
        $urlPath = trim(parse_url($defaultUrl, PHP_URL_PATH) ?? '', '/');

        if ($urlPath !== '') {
            $previousUrl = url()->previous();
            $currentUrl = url()->current();
            $previousPath = trim(parse_url($previousUrl, PHP_URL_PATH) ?? '', '/');

            // 1. Direct referer matches target path
            if ($previousUrl && $previousUrl !== $currentUrl && $previousPath === $urlPath) {
                if (session()->isStarted()) {
                    session(['backlink_url_' . $urlPath => $previousUrl]);
                }
                return $previousUrl;
            }

            // 2. Retrieve from session if stored path matches
            if (session()->isStarted() && session()->has('backlink_url_' . $urlPath)) {
                $savedUrl = session('backlink_url_' . $urlPath);
                $savedPath = trim(parse_url($savedUrl, PHP_URL_PATH) ?? '', '/');
                if ($savedPath === $urlPath) {
                    return $savedUrl;
                }
            }
        }

        return $defaultUrl;
    }
}

