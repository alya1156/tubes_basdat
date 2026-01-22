<?php
// Pastikan folder upload ada
@mkdir('uploads/gallery', 0755, true);
@mkdir('uploads/tipe_kamar', 0755, true);

// Function untuk membuat gambar cave pool
function createCavePoolImage($filename) {
    $img = imagecreatetruecolor(600, 600);
    
    // Colors
    $gold = imagecolorallocate($img, 212, 175, 55);
    $water = imagecolorallocate($img, 64, 191, 178);
    $lightWater = imagecolorallocate($img, 120, 220, 210);
    $cave = imagecolorallocate($img, 139, 119, 101);
    
    // Gradient background
    for ($y = 0; $y < 600; $y++) {
        $r = 26 + (($y / 600) * 40);
        $g = 26 + (($y / 600) * 30);
        $b = 46 + (($y / 600) * 30);
        $color = imagecolorallocate($img, (int)$r, (int)$g, (int)$b);
        imageline($img, 0, $y, 600, $y, $color);
    }
    
    // Water pool
    imagefilledellipse($img, 300, 450, 400, 180, $water);
    imagefilledellipse($img, 250, 420, 100, 60, $lightWater);
    imagefilledellipse($img, 350, 400, 120, 70, $lightWater);
    
    // Cave rocks
    imagefilledellipse($img, 100, 100, 80, 100, $cave);
    imagefilledellipse($img, 500, 120, 90, 110, $cave);
    
    // Gold accent
    imagefilledarc($img, 300, 120, 60, 40, 0, 360, $gold, IMG_ARC_PIE);
    
    imagepng($img, $filename);
    imagedestroy($img);
    return true;
}

// Create images
createCavePoolImage('uploads/gallery/cave_pool_1.png');
createCavePoolImage('uploads/gallery/cave_pool_2.png');
createCavePoolImage('uploads/tipe_kamar/cave_luxury.png');

echo "Gambar berhasil dibuat!";

/**
 * Function untuk generate SVG image
 */
function generateSVGImage($width, $height, $title, $subtitle, $icon = 'image', $filename = null) {
    // SVG dengan luxury hotel theme
    $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="$width" height="$height" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
        <linearGradient id="bgGradient" x1="0%" y1="0%" x2="135%" y2="135%">
            <stop offset="0%" style="stop-color:#2a2a3e;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#1a1a2e;stop-opacity:1" />
        </linearGradient>
        <filter id="glow">
            <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
            <feMerge>
                <feMergeNode in="coloredBlur"/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
    </defs>
    
    <!-- Background gradient -->
    <rect width="$width" height="$height" fill="url(#bgGradient)"/>
    
    <!-- Border -->
    <rect x="0" y="0" width="$width" height="$height" fill="none" stroke="#d4af37" stroke-width="2"/>
    
    <!-- Decorative corner elements -->
    <g stroke="#d4af37" stroke-width="1" opacity="0.3">
        <line x1="10" y1="10" x2="40" y2="10"/>
        <line x1="10" y1="10" x2="10" y2="40"/>
        
        <line x1="{$width-40}" y1="10" x2="{$width-10}" y2="10"/>
        <line x1="{$width-10}" y1="10" x2="{$width-10}" y2="40"/>
        
        <line x1="10" y1="{$height-10}" x2="40" y2="{$height-10}"/>
        <line x1="10" y1="{$height-40}" x2="10" y2="{$height-10}"/>
        
        <line x1="{$width-40}" y1="{$height-10}" x2="{$width-10}" y2="{$height-10}"/>
        <line x1="{$width-10}" y1="{$height-40}" x2="{$width-10}" y2="{$height-10}"/>
    </g>
    
    <!-- Main content -->
    <g text-anchor="middle">
        <!-- Icon -->
        <circle cx="{$width/2}" cy="{$height/2 - 40}" r="30" fill="none" stroke="#d4af37" stroke-width="1.5" opacity="0.6"/>
        <text x="{$width/2}" y="{$height/2 - 25}" font-family="Arial, sans-serif" font-size="36" fill="#d4af37" filter="url(#glow)">🏨</text>
        
        <!-- Title -->
        <text x="{$width/2}" y="{$height/2 + 30}" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="#d4af37">$title</text>
        
        <!-- Subtitle -->
        <text x="{$width/2}" y="{$height/2 + 55}" font-family="Arial, sans-serif" font-size="12" fill="#b0b0b0">$subtitle</text>
    </g>
    
    <!-- Decorative line -->
    <line x1="{$width/4}" y1="{$height/2 + 70}" x2="{3*$width/4}" y2="{$height/2 + 70}" stroke="#d4af37" stroke-width="1" opacity="0.3"/>
</svg>
SVG;

    if ($filename) {
        file_put_contents($filename, $svg);
        return file_exists($filename);
    }
    return $svg;
}

/**
 * Generate placeholder images untuk gallery
 */
$galleryData = [
    ['hotel-hero.jpg', 'Hotel Exterior', 'Pemandangan Eksterior'],
    ['kolam-renang.jpg', 'Kolam Renang', 'Fasilitas Kolam Renang'],
    ['lobby.jpg', 'Lobby Utama', 'Ruang Penerimaan'],
    ['restaurant.jpg', 'Restaurant', 'Tempat Bersantap'],
    ['gym.jpg', 'Gym & Spa', 'Pusat Kesehatan'],
    ['event-room.jpg', 'Ruang Acara', 'Ruang Pertemuan']
];

echo "═══════════════════════════════════════\n";
echo "GENERATE GAMBAR LOKAL - HOTEL GALASA\n";
echo "═══════════════════════════════════════\n\n";

echo "📸 Membuat gambar Gallery...\n";
foreach ($galleryData as $item) {
    $filepath = __DIR__ . "/uploads/gallery/{$item[0]}";
    if (!file_exists($filepath)) {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        if ($ext === 'jpg') {
            $svgPath = str_replace('.jpg', '.svg', $filepath);
            if (generateSVGImage(800, 600, $item[1], $item[2], 'image', $svgPath)) {
                echo "   ✓ {$item[1]} ({$item[2]})\n";
            }
        }
    } else {
        echo "   - {$item[1]} (sudah ada)\n";
    }
}

/**
 * Generate placeholder images untuk tipe kamar
 */
$roomData = [
    ['standard-room.jpg', 'Standard Room', 'Kamar Standar'],
    ['deluxe-room.jpg', 'Deluxe Room', 'Kamar Mewah'],
    ['suite-room.jpg', 'Suite Room', 'Kamar Suite'],
    ['premium-room.jpg', 'Premium Room', 'Kamar Premium']
];

echo "\n🛏️  Membuat gambar Tipe Kamar...\n";
foreach ($roomData as $item) {
    $filepath = __DIR__ . "/uploads/tipe_kamar/{$item[0]}";
    if (!file_exists($filepath)) {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        if ($ext === 'jpg') {
            $svgPath = str_replace('.jpg', '.svg', $filepath);
            if (generateSVGImage(400, 300, $item[1], $item[2], 'bed', $svgPath)) {
                echo "   ✓ {$item[1]} ({$item[2]})\n";
            }
        }
    } else {
        echo "   - {$item[1]} (sudah ada)\n";
    }
}

echo "\n═══════════════════════════════════════\n";
echo "✅ Proses selesai!\n";
echo "═══════════════════════════════════════\n";
echo "\nGambar telah disimpan di:\n";
echo "  • uploads/gallery/\n";
echo "  • uploads/tipe_kamar/\n";
echo "\nTips: Ganti file .svg dengan gambar asli (.jpg/.png) sesuai kebutuhan\n";
?>
