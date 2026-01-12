
<?php

require __DIR__ . '/../vendor/autoload.php';

use PokerHand\PokerHand;

$hands = [
    // Royal Flush
    'As Ks Qs Js 10s',

    // Straight Flush
    '9h 8h 7h 6h 5h',

    // Four of a Kind
    'Ah As Ac Ad 6s',

    // Full House
    'Kh Kc Ks 3h 3d',

    // Flush
    'Kh Qh 6h 2h 9h',

    // Straight
    '9h 8c 7d 6s 5h',

    // Three of a Kind
    'Kh Kc Ks 7d 2s',

    // Two Pair
    'Kh Kc 3s 3h 2d',

    // One Pair
    'Ah As 10c 7d 6s',

    // High Card
    'Kh Qc 9d 6s 2h',
];


echo PHP_EOL;
echo "🂡 Poker Hand Examples 🂡 \n";
echo "Showing all 10 standard poker hand rankings\n";
echo str_repeat('─', 40) . PHP_EOL;

foreach ($hands as $handString) {
    $hand = new PokerHand($handString);
    $rank = $hand->getHandRanked();

    printf(
        " %-22s ➜  %s\n",
        renderHand($handString),
        colorizeRank($rank)
    );
}

echo str_repeat('─', 40) . PHP_EOL;
echo PHP_EOL;

function renderHand(string $hand): string
{
    return implode(' ', array_map(
        fn ($c) => "[{$c}]",
        explode(' ', $hand)
    ));
}

function colorizeRank(string $rank): string
{
    $colors = [
        'Royal Flush'     => "\033[35m",
        'Straight Flush'  => "\033[34m",
        'Four of a Kind'  => "\033[31m",
        'Full House'      => "\033[33m",
        'Flush'           => "\033[36m",
        'Straight'        => "\033[32m",
        'Three of a Kind' => "\033[32m",
        'Two Pair'        => "\033[32m",
        'One Pair'        => "\033[32m",
        'High Card'       => "\033[90m",
    ];

    $reset = "\033[0m";

    return ($colors[$rank] ?? '') . $rank . $reset;
}