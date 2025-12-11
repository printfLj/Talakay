<?php

function load_json(string $path, $default = []) {
    if (!file_exists($path)) {
        return $default;
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : $default;
}

function save_json(string $path, $data): bool {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return (bool) file_put_contents($path, $json);
}

function generate_id(string $prefix): string {
    return $prefix . '_' . bin2hex(random_bytes(4));
}

