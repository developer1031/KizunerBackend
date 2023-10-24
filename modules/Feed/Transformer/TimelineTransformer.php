<?php

namespace Modules\Feed\Transformer;

use League\Fractal\TransformerAbstract;
use Modules\Feed\Contracts\Data\TimelineInterface;
use Modules\Helps\Contracts\HelpRepositoryInterface;
use Modules\Helps\Transformers\HelpTransform;
use Modules\Kizuner\Contracts\HangoutRepositoryInterface;
use Modules\Kizuner\Contracts\StatusRepositoryInterface;
use Modules\KizunerApi\Transformers\HangoutTransform;
use Modules\KizunerApi\Transformers\StatusTransform;

class TimelineTransformer extends TransformerAbstract
{
    public function transform(TimelineInterface $timeline)
    {
        /** @var StatusRepositoryInterface $statusRepository */
        $statusRepository = resolve(StatusRepositoryInterface::class);
        /** @var HangoutRepositoryInterface $hangoutRepository */
        $hangoutRepository = resolve(HangoutRepositoryInterface::class);
        /** @var HelpRepositoryInterface $helpRepository */
        $helpRepository = resolve(HelpRepositoryInterface::class);

        if ($timeline->isStatus()) {
            $status = $statusRepository->get($timeline->getReferenceId());
            return [
                'id'        => $timeline->getId(),
                'type'      => 'status',
                'relation'  => fractal($status, new StatusTransform())
            ];
        }
        else if ($timeline->isHangout()) {
           $hangout = $hangoutRepository->get($timeline->getReferenceId());

           return [
               'id'         => $timeline->getId(),
               'type'       => 'hangout',
               'relation'   => fractal($hangout, new HangoutTransform())
           ];
        }

        else if($timeline->isHelp()) {
            $help = $helpRepository->get($timeline->getReferenceId());

            return [
                'id'         => $timeline->getId(),
                'type'       => 'help',
                'relation'   => fractal($help, new HelpTransform())
            ];
        }

    }
}
