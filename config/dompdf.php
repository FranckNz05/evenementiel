<?php

return [
    'mode' => 'utf-8',
    'format' => [210, 100], // Format personnalisé pour nos billets
    'default_font_size' => '12',
    'default_font' => 'sans-serif',
    'margin_left' => 0,
    'margin_right' => 0,
    'margin_top' => 0,
    'margin_bottom' => 0,
    'margin_header' => 0,
    'margin_footer' => 0,
    'orientation' => 'portrait',
    'title' => 'Billet',
    'author' => 'Yabetoo',
    'watermark' => '',
    'show_watermark' => false,
    'watermark_font' => 'sans-serif',
    'display_mode' => 'fullpage',
    'watermark_text_alpha' => 0.1,
    'custom_font_dir' => base_path('resources/fonts/'),
    'custom_font_data' => [
        'montserrat' => [
            'R' => 'Montserrat-Regular.ttf',
            'B' => 'Montserrat-Bold.ttf',
        ]
    ],
    'default_media_type' => 'screen',
    'dpi' => 96,
    'enable_php' => false,
    'enable_javascript' => true,
    'enable_remote' => true,
    'enable_svg' => true,
    'enable_font_subsetting' => false,
    'pdf_backend' => 'CPDF',
    'chroot' => realpath(base_path()),
    'temp_dir' => sys_get_temp_dir(),
    'font_dir' => storage_path('fonts/'),
    'font_cache' => storage_path('fonts/'),
    'log_output_file' => storage_path('logs/dompdf.html'),
];