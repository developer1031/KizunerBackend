<?php

namespace Modules\Rating\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Rating\Domains\Entities\RatingEntity;

class Rating
{

    /** @var RatingEntity  */
    private $rating;

    /**
     * Rating constructor.
     * @param RatingEntity $rating
     */
    public function __construct(RatingEntity $rating)
    {
        $this->rating = $rating;
    }

    /**
     * @param $userId
     * @param $rating
     * @param $comment
     * @param $ratedUser
     * @param $offerId
     * @return mixed
     */
    public static function create($userId, $rating, $comment, $ratedUser, $offerId)
    {

        $checkRated = self::findByUserAndOffer($userId, $offerId);

        if ($checkRated) {
            return $checkRated;
        }
        $rate = EntityManager::create(RatingEntity::class);
        $rate->user_id          = $userId;
        $rate->rate             = $rating;
        $rate->comment          = $comment;
        $rate->ratted_user_id   = $ratedUser;
        $rate->offer_id         = $offerId;
        $rate->save();
        return $rate;
    }

    /**
     * @param $id
     * @param $rating
     * @param $comment
     * @return mixed
     */
    public static function update($id, $rating, $comment)
    {
        $rateManager    = EntityManager::getManager(RatingEntity::class);
        $rate           = $rateManager->find($id);
        $rate->rate     = $rating;
        $rate->comment  = $comment;
        $rate->save();
        return $rate;
    }

    public static function findByOfferId(string $offerId)
    {
        $rateManager = EntityManager::getManager(RatingEntity::class);
        return $rateManager->where('offer_id', $offerId)->first();
    }


    /**
     * @param $userId
     * @param $offerId
     * @return mixed
     */
    public static function findByUserAndOffer($userId, $offerId)
    {
        $rateManager    = EntityManager::getManager(RatingEntity::class);
        return $rateManager->where([
            'user_id'   => $userId,
            'offer_id'  => $offerId
        ])->first();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function delete(string $id)
    {
        $rateManager    = EntityManager::getManager(RatingEntity::class);
        $rate = $rateManager->find($id);
        return $rate->delete();
    }
}
