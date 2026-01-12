<?php
namespace PokerHand;

use PHPUnit\Framework\TestCase;

class PokerHandTest extends TestCase
{
    /**
     * @test
     */
    public function itCanRankARoyalFlush()
    {
        $hand = new PokerHand('As Ks Qs Js 10s');
        $this->assertEquals('Royal Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAPair()
    {
        $hand = new PokerHand('Ah As 10c 7d 6s');
        $this->assertEquals('One Pair', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankTwoPair()
    {
        $hand = new PokerHand('Kh Kc 3s 3h 2d');
        $this->assertEquals('Two Pair', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAFlush()
    {
        $hand = new PokerHand('Kh Qh 6h 2h 9h');
        $this->assertEquals('Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAStraightFlush()
    {
        $hand = new PokerHand('9h 8h 7h 6h 5h');
        $this->assertEquals('Straight Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankFourOfAKind()
    {
        $hand = new PokerHand('Ah As Ac Ad 6s');
        $this->assertEquals('Four of a Kind', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAFullHouse()
    {
        $hand = new PokerHand('Kh Kc Ks 3h 3d');
        $this->assertEquals('Full House', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAStraight()
    {
        $hand = new PokerHand('9h 8c 7d 6s 5h');
        $this->assertEquals('Straight', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankThreeOfAKind()
    {
        $hand = new PokerHand('Kh Kc Ks 7d 2s');
        $this->assertEquals('Three of a Kind', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankHighCard()
    {
        $hand = new PokerHand('Kh Qc 6d 2s 9h');
        $this->assertEquals('High Card', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAWheelStraight()
    {
        // A-2-3-4-5 (Ace low straight)
        $hand = new PokerHand('Ah 2s 3d 4h 5c');
        $this->assertEquals('Straight', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankAWheelStraightFlush()
    {
        // A-2-3-4-5 all same suit
        $hand = new PokerHand('Ah 2h 3h 4h 5h');
        $this->assertEquals('Straight Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankARoyalFlushInHearts()
    {
        $hand = new PokerHand('Ah Kh Qh Jh 10h');
        $this->assertEquals('Royal Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankARoyalFlushInDiamonds()
    {
        $hand = new PokerHand('Ad Kd Qd Jd 10d');
        $this->assertEquals('Royal Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itCanRankARoyalFlushInClubs()
    {
        $hand = new PokerHand('Ac Kc Qc Jc 10c');
        $this->assertEquals('Royal Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itDoesNotConfuseFlushWithStraightFlush()
    {
        // All same suit but not in sequence
        $hand = new PokerHand('2s 5s 7s 9s Ks');
        $this->assertEquals('Flush', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itDoesNotConfuseStraightWithFlush()
    {
        // In sequence but different suits
        $hand = new PokerHand('2s 3h 4d 5c 6s');
        $this->assertEquals('Straight', $hand->getHandRanked());
    }

    /**
     * @test
     */
    public function itDoesNotConfuseThreeOfAKindWithFullHouse()
    {
        // Three of a kind but no pair
        $hand = new PokerHand('7h 7s 7c Kd 2s');
        $this->assertEquals('Three of a Kind', $hand->getHandRanked());
    }

}
?>