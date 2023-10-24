<?php

namespace Modules\Feed\Listeners;

use Modules\Feed\Contracts\Data\FeedFollowerInterfaceFactory;
use Modules\Feed\Contracts\Data\TimelineInterfaceFactory;
use Modules\Feed\Contracts\Repositories\FeedFollowerRepositoryInterface;
use Modules\Feed\Contracts\Repositories\TimelineRepositoryInterface;
use Modules\Feed\Models\FeedFollowerFactory;
use Modules\User\Contracts\UserRepositoryInterface;

/**
 * Class AbstractEventSubscriber
 * @package Modules\Feed\Listeners
 */
abstract class AbstractEventSubscriber
{

    /** @var FeedFollowerRepositoryInterface  */
    protected $feedFollowRepository;

    /** @var TimelineRepositoryInterface  */
    protected $feedTimelineRepository;

    /** @var UserRepositoryInterface  */
    protected $userRepository;

    /** @var FeedFollowerFactory  */
    protected $feedFollowFactory;

    /** @var TimelineInterfaceFactory */
    protected $feedTimelineFactory;

    /**
     * AbstractEventSubscriber constructor.
     * @param FeedFollowerRepositoryInterface $feedFollowRepository
     * @param TimelineRepositoryInterface $feedTimelineRepository
     * @param UserRepositoryInterface $userRepository
     * @param FeedFollowerInterfaceFactory $feedFollowerInterfaceFactory
     * @param TimelineInterfaceFactory $feedTimelineFactory
     */
    public function __construct(
        FeedFollowerRepositoryInterface $feedFollowRepository,
        TimelineRepositoryInterface $feedTimelineRepository,
        UserRepositoryInterface $userRepository,
        FeedFollowerInterfaceFactory $feedFollowerInterfaceFactory,
        TimelineInterfaceFactory $feedTimelineFactory
    ) {
        $this->feedTimelineFactory = $feedTimelineFactory;
        $this->feedFollowFactory = $feedFollowerInterfaceFactory;
        $this->userRepository = $userRepository;
        $this->feedFollowRepository = $feedFollowRepository;
        $this->feedTimelineRepository = $feedTimelineRepository;
    }
}
