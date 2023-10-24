<?php

namespace Modules\KizunerApi\Services;

use Illuminate\Support\Facades\Log;
use Modules\Config\Config;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Contracts\ReactRepositoryInterface;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\KizunerApi\Http\Requests\React\HangoutReactRequest;
use Modules\KizunerApi\Http\Requests\React\HelpReactRequest;
use Modules\KizunerApi\Http\Requests\React\StatusReactRequest;

class ReactManager
{

    private $reactRepository;

    public function __construct(
        ReactRepositoryInterface $reactRepository
    ) {
        $this->reactRepository = $reactRepository;
    }

    public function reactHangout(HangoutReactRequest $request)
    {
        $reacted = $this->reactRepository->hangoutReact(
            app('request')->user()->id,
            $request->get('hangout_id'),
            $request->react_type
        );

        if($request->react_type=='share') {
            $config_data = new Config();
            $kz = $config_data->getConfigValWithDefault('kizuner_share');
            addKizunaOnShare(auth()->user(), $kz);
        }

        $hangoutReactCount = React::where([
            'reactable_id'      => $request->get('hangout_id'),
            'reactable_type'   => Hangout::class,
            'react_type'        => 'like'
        ])->count();
        $response['data'] = [
            'message' => $reacted ? 'liked' : 'unliked',
            'status'  => true,
            'count'     => $hangoutReactCount
        ];
        return $response;
    }

    public function reactStatus(StatusReactRequest $request)
    {
        $reacted = $this->reactRepository->statusReact(
            app('request')->user()->id,
            $request->get('status_id'),
            $request->react_type
        );
        $statusReactCount = React::where([
            'reactable_id'      => $request->get('status_id'),
            'reactable_type'   => Status::class,
            'react_type'        => 'like'
        ])->count();
        $response['data'] = [
            'message' => $reacted ? 'liked' : 'unliked',
            'status'  => true,
            'count'     => $statusReactCount
        ];
        return $response;
    }

    public function reactHelp(HelpReactRequest $request)
    {
        $reacted = $this->reactRepository->helpReact(
            app('request')->user()->id,
            $request->get('help_id'),
            $request->react_type
        );

        $helpReactCount = React::where([
            'reactable_id'      => $request->get('help_id'),
            'reactable_type'   => Help::class,
            'react_type'        => 'like'
        ])->count();

        $response['data'] = [
            'message' => $reacted ? 'liked' : 'unliked',
            'status'  => true,
            //'status'  => $reacted,
            'count'   => $helpReactCount
        ];

        if($request->react_type=='share') {
            $config_data = new Config();
            $kz = $config_data->getConfigValWithDefault('kizuner_share');
            addKizunaOnShare(auth()->user(), $kz);
        }
        return $response;
    }
}
