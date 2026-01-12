<?php

require __DIR__ . '/../vendor/autoload.php';

use PokerHand\PokerHand;

// Card display functions
function drawCard($rank, $suit) {
    $suitSymbol = match($suit) {
        's' => '♠', //unicode characters
        'h' => '♥',
        'd' => '♦',
        'c' => '♣',
        default => '?'
    };

    $color = ($suit === 'h' || $suit === 'd') ? "\033[1;31m" : "\033[1;37m"; //ansi colors
    $reset = "\033[0m";
    $displayRank = str_pad($rank, 2, ' ', STR_PAD_RIGHT);

    return [
        $color . "┌─────────┐" . $reset,
        $color . "│ " . $displayRank . "      │" . $reset,
        $color . "│         │" . $reset,
        $color . "│    " . $suitSymbol . "    │" . $reset,
        $color . "│         │" . $reset,
        $color . "│      " . str_pad($rank, 2, ' ', STR_PAD_LEFT) . " │" . $reset,
        $color . "└─────────┘" . $reset,
    ];
}

function displayHand($handString) {
    $cards = explode(' ', trim($handString));
    $cardDrawings = [];

    foreach ($cards as $cardStr) {
        preg_match('/^(\d+|[JQKA])([shdc])$/i', $cardStr, $matches);
        if (!empty($matches)) {
            $rank = strtoupper($matches[1]);
            $suit = strtolower($matches[2]);
            $cardDrawings[] = drawCard($rank, $suit);
        }
    }

    if (!empty($cardDrawings)) {
        for ($line = 0; $line < 7; $line++) {
            foreach ($cardDrawings as $card) {
                echo $card[$line] . " ";
            }
            echo "\n";
        }
    }
}

// Deck management
function createDeck() {
    $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
    $suits = ['h', 'd', 's', 'c'];
    $deck = [];

    foreach ($ranks as $rank) {
        foreach ($suits as $suit) {
            $deck[] = $rank . $suit;
        }
    }

    shuffle($deck);
    return $deck;
}

function getRandomInterestingHand() {
    // Curated interesting hands for demo purposes
    $interestingHands = [
        // Royal Flushes
        'As Ks Qs Js 10s',
        'Ah Kh Qh Jh 10h',
        'Ad Kd Qd Jd 10d',
        'Ac Kc Qc Jc 10c',

        // Straight Flushes
        '9h 8h 7h 6h 5h',
        'Kd Qd Jd 10d 9d',
        '7c 6c 5c 4c 3c',
        'Ah 2h 3h 4h 5h', // Wheel

        // Four of a Kind
        'Ah As Ac Ad Ks',
        'Kh Ks Kc Kd Qh',
        '8h 8s 8c 8d 2h',

        // Full House
        'Kh Kc Ks 3h 3d',
        'Ah Ac As 10h 10d',
        '7h 7s 7c Jh Jd',

        // Flush
        'Kh Qh 6h 2h 9h',
        'Ad Jd 8d 5d 2d',
        '10s 8s 6s 4s 2s',

        // Straight
        '9h 8c 7d 6s 5h',
        'Ah 2s 3d 4h 5c', // Wheel straight - lowest possible
        'Kd Qh Jc 10s 9h',

        // Three of a Kind
        'Kh Kc Ks 7d 2s',
        'Ah As Ac Kd Qh',
        '9h 9s 9c 4d 2h',

        // Two Pair
        'Kh Kc 3s 3h 2d',
        'Ah Ad 10h 10c 5s',
        'Qh Qs 7c 7d 3h',

        // One Pair 
        'Ah As Kc Qd Js',
        'Kh Ks Qd Jc 10h',

        // High Card 
        'As Kd Qh Jc 9s',
        'Kh Qc Jd 10s 8h',
    ];

    return $interestingHands[array_rand($interestingHands)];
}

function colorizeRank($rank) {
    $colors = [
        'Royal Flush'     => "\033[1;35m",
        'Straight Flush'  => "\033[1;34m",
        'Four of a Kind'  => "\033[1;31m",
        'Full House'      => "\033[1;33m",
        'Flush'           => "\033[1;36m",
        'Straight'        => "\033[1;32m",
        'Three of a Kind' => "\033[1;32m",
        'Two Pair'        => "\033[1;32m",
        'One Pair'        => "\033[1;32m",
        'High Card'       => "\033[90m",
    ];

    return ($colors[$rank] ?? "\033[0m") . $rank . "\033[0m";
}

// Main demo
function runDemo() {
    echo "\033[2J\033[H"; // Clear screen
    echo "\033[1;35m";
    echo <<<BANNER
         ____   ___  _  _______ ____  
        |  _ \ / _ \| |/ / ____|  _ \ 
        | |_) | | | | ' /|  _| | |_) |
        |  __/| |_| | . \| |___|  _ < 
        |_|    \___/|_|\_\_____|_| \_\

       ♠ ♥ ♦ ♣  HAND COMPARISON  ♠ ♥ ♦ ♣

BANNER;
    echo "\033[0m\n";

    $rankValues = [
        'High Card' => 1,
        'One Pair' => 2,
        'Two Pair' => 3,
        'Three of a Kind' => 4,
        'Straight' => 5,
        'Flush' => 6,
        'Full House' => 7,
        'Four of a Kind' => 8,
        'Straight Flush' => 9,
        'Royal Flush' => 10,
    ];


    $hand1_totalWins = 0;
    $hand2_totalWins = 0;
    // Show 5 hand comparisons
    for ($round = 1; $round <= 5; $round++) {
        echo "\033[1;36m" . str_repeat("═", 60) . "\033[0m\n";
        echo "\033[1;33m🎲 Round {$round}/5\033[0m\n";
        echo "\033[1;36m" . str_repeat("═", 60) . "\033[0m\n\n";

        // Deal Hand 1
        $hand1String = getRandomInterestingHand();
        $hand1 = new PokerHand($hand1String);
        $rank1 = $hand1->getHandRanked();

        echo "\033[1;33mHand 1:\033[0m\n";
        displayHand($hand1String);
        echo "\n➜ " . colorizeRank($rank1) . "\n\n";

        sleep(1);

        // Deal Hand 2
        $hand2String = getRandomInterestingHand();
        $hand2 = new PokerHand($hand2String);
        $rank2 = $hand2->getHandRanked();

        echo "\033[1;33mHand 2:\033[0m\n";
        displayHand($hand2String);
        echo "\n➜ " . colorizeRank($rank2) . "\n\n";

        // Compare
        echo "\033[1;36m" . str_repeat("═", 60) . "\033[0m\n";

        $value1 = $rankValues[$rank1];
        $value2 = $rankValues[$rank2];

        if ($value1 > $value2) {
            echo "\033[1;32m🏆 Hand 1 WINS! 🏆\033[0m\n";
            $hand1_totalWins++;
        } elseif ($value2 > $value1) {
            echo "\033[1;32m🏆 Hand 2 WINS! 🏆\033[0m\n";
            $hand2_totalWins++;
        } else {
            echo "\033[1;33m🤝 It's a TIE! 🤝\033[0m\n";
        }

        echo "\033[1;36m" . str_repeat("═", 60) . "\033[0m\n\n";

        // Small delay between rounds
        if ($round < 5) {
            sleep(2);
        }
    }

    // Final results
    echo "\033[1;35m" . str_repeat("═", 60) . "\033[0m\n";
    echo "\033[1;33m🏁 Final Results 🏁\033[0m\n";
    if ($hand1_totalWins > $hand2_totalWins) {
        echo "\033[1;32m🏆 Hand 1 is the Overall Winner with {$hand1_totalWins} wins! 🏆\033[0m\n";
    } elseif ($hand2_totalWins > $hand1_totalWins) {
        echo "\033[1;32m🏆 Hand 2 is the Overall Winner with {$hand2_totalWins} wins! 🏆\033[0m\n";
    } else {
        echo "\033[1;33m🤝 The Match Ends in a Tie! Both Hands have {$hand1_totalWins} wins! 🤝\033[0m\n";
    }
    echo "\033[1;35m" . str_repeat("═", 60) . "\033[0m\n";

    // Final message
    echo "\n\033[1;35m";
    echo "    ♠ ♥ ♦ ♣  Demo Complete!  ♠ ♥ ♦ ♣\n";
    echo "\033[0m\n";
}

// Start the demo
runDemo();
