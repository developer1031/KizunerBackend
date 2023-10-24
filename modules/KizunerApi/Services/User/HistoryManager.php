<?php

namespace Modules\KizunerApi\Services\User;

use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Contracts\OfferRepositoryInterface;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Exceptions\InCorrectFormatException;
use Modules\KizunerApi\Transformers\OfferTransform;

class HistoryManager
{
    /** @var OfferRepositoryInterface  */
    private $offerRepository;

    /**
     * HistoryManager constructor.
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        OfferRepositoryInterface $offerRepository
    ) {
        $this->offerRepository = $offerRepository;
    }

    /**
     * @param string $status
     * @return \Spatie\Fractal\Fractal
     * @throws InCorrectFormatException
     */
    public function getHangoutHistory(string $status = null)
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $this->checkStatusFormat($status);
        $status = $status != null ? Offer::$status[$status] : null;
        $offers = $this->offerRepository->getOfferForUser($this->getUserId(), $perPage, $status);
        Log::info($this->getUserId());
        return fractal($offers, new OfferTransform());
    }


    /**
     * @param string $status
     * @return \Spatie\Fractal\Fractal
     * @throws InCorrectFormatException
     */
    public function getOfferHistory(string $status = null)
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $this->checkStatusFormat($status);
        $status = $status != null ? Offer::$status[$status] : null;
        $offers = $this->offerRepository->getOfferByUser($this->getUserId(), $perPage, $status);
        return fractal($offers, new OfferTransform());
    }

    /**
     * @return int
     */
    private function getUserId()
    {
        return app('request')->user()->id;
    }

    private function checkStatusFormat($status)
    {
        if ($status) {
            if (!array_key_exists($status, Offer::$status)) {
                throw new InCorrectFormatException('Status does not exist');
            }
        }
    }
}
