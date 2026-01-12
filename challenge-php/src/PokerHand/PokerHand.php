<?php

namespace PokerHand;

use Exception;

class Card
{
    public $rank;
    public $suit;
    public $value;

    public function __construct($card)
    {
        // Card format: "As" = Ace of spades, "10h" = 10 of hearts
        // Extract the rank (A, K, Q, etc) and suit (s, h, d, c)
        preg_match('/^(\d+|[JQKA])([shdc])$/i', $card, $matches);
        
        if (empty($matches)) {
            throw new Exception("Invalid card: {$card}");
        }

        $this->rank = strtoupper($matches[1]);
        $this->suit = strtolower($matches[2]);
        
        // Convert rank to a number for easy comparison
        $rankValues = [
            '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, 
            '7' => 7, '8' => 8, '9' => 9, '10' => 10, 
            'J' => 11, 'Q' => 12, 'K' => 13, 'A' => 14
        ];
        
        $this->value = $rankValues[$this->rank];
    }
}

class PokerHand
{
    public $cards = [];

    public function __construct($handString)
    {
        // split "As Ks Qs Js 10s" into individual strings
        $cardStrings = explode(' ', trim($handString));
        // now cardStrings = 
        // Array(
        //     [0] => As
        //     [1] => Ks
        //     [2] => Qs
        //     [3] => Js
        //     [4] => 10s
        // )
        if (count($cardStrings) !== 5) {
            throw new Exception('Hand must have exactly 5 cards');
        }

        foreach ($cardStrings as $cardString) {
            $this->cards[] = new Card($cardString);
        }
        
        // sort cards highest to lowest
        usort($this->cards, function($a, $b) {
            return $b->value - $a->value;
        });
    }

    public function getHandRanked()
    {
        $ranker = new HandRanker();
        return $ranker->rank($this);
    }

    public function getRanks()
    {
        $ranks = [];
        foreach ($this->cards as $card) {
            $ranks[] = $card->value;
        }
        return $ranks;
    }

    public function getSuits()
    {
        $suits = [];
        foreach ($this->cards as $card) {
            $suits[] = $card->suit;
        }
        return $suits;
    }

    // count how many of each rank (A, Q, K, etc.)
    public function getRankCounts()
    {
        $counts = [];

        foreach ($this->getRanks() as $rank) {
            if (!isset($counts[$rank])) {
                $counts[$rank] = 0;
            }
            $counts[$rank]++;
        }

        // sort counts descending
        arsort($counts);
        return $counts;
    }
}

class HandRanker
{
    public function rank($hand)
    {
        // Check from best hand to worst
        if ($this->isRoyalFlush($hand)) return 'Royal Flush';
        if ($this->isStraightFlush($hand)) return 'Straight Flush';
        if ($this->isFourOfAKind($hand)) return 'Four of a Kind';
        if ($this->isFullHouse($hand)) return 'Full House';
        if ($this->isFlush($hand)) return 'Flush';
        if ($this->isStraight($hand)) return 'Straight';
        if ($this->isThreeOfAKind($hand)) return 'Three of a Kind';
        if ($this->isTwoPair($hand)) return 'Two Pair';
        if ($this->isOnePair($hand)) return 'One Pair';
        
        return 'High Card';
    }

    // all 5 cards have same suit
    private function isFlush($hand)
    {
        $suits = $hand->getSuits();
        $uniqueSuits = array_unique($suits);
        return count($uniqueSuits) === 1;
    }

    // 5 cards in a row by rank
    private function isStraight($hand)
    {
        $ranks = $hand->getRanks();

        // Check if each card is 1 less than the previous
        for ($i = 0; $i < 4; $i++) {
            if ($ranks[$i] - $ranks[$i + 1] !== 1) {
                // Special case: A-2-3-4-5 (ace can be low)
                if ($ranks[0] === 14 && $ranks[1] === 5 &&
                    $ranks[2] === 4 && $ranks[3] === 3 && $ranks[4] === 2) {
                    return true;
                }
                return false;
            }
        }

        return true;
    }

    // A-K-Q-J-10 all highest/best cards in order with same suit
    private function isRoyalFlush($hand)
    {
        if (!$this->isFlush($hand)) {
            return false;
        }

        $ranks = $hand->getRanks();
        // check for highest cards
        return $ranks[0] === 14 && $ranks[1] === 13 &&
               $ranks[2] === 12 && $ranks[3] === 11 && $ranks[4] === 10;
    }

    // Straight + flush
    private function isStraightFlush($hand)
    {
        return $this->isFlush($hand) && $this->isStraight($hand);
    }
    
    // 3 of one rank + 2 of another
    private function isFullHouse($hand)
    {
        $counts = array_values($hand->getRankCounts());
        return count($counts) === 2 && $counts[0] === 3 && $counts[1] === 2;
    }

    private function isFourOfAKind($hand)
    {
        $counts = $hand->getRankCounts();
        $firstCount = reset($counts);
        return $firstCount === 4;
    }

    private function isThreeOfAKind($hand)
    {
        $counts = $hand->getRankCounts();
        $firstCount = reset($counts);
        return $firstCount === 3;
    }

    private function isTwoPair($hand)
    {
        $counts = array_values($hand->getRankCounts());
        return count($counts) === 3 && $counts[0] === 2 && $counts[1] === 2;
    }

    private function isOnePair($hand)
    {
        $counts = $hand->getRankCounts();
        $firstCount = reset($counts);
        return $firstCount === 2;
    }
}
