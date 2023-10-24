<?php

namespace Modules\Feed\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Feed\Contracts\Data\TimelineInterface;

class Timeline extends Model implements TimelineInterface
{

    use SoftDeletes;

    const TYPE_STATUS = 'status';
    const TYPE_HANGOUT = 'hangout';
    const TYPE_HELP = 'help';

    const SCOPE_PERSONAL = 'personal';
    const SCOPE_TIMELINE = 'timeline';

    protected $table = 'feed_timelines';
    protected $fillable = [
        'user_id',
        'reference_id',
        'type',
        'status'
    ];

    public function setUserId(string $userId): TimelineInterface
    {
        $this->user_id = $userId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setStatus(string $status): TimelineInterface
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setType(string $type): TimelineInterface
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isStatus(): bool
    {
        return self::TYPE_STATUS === $this->getType() ? true : false;
    }

    public function isHangout(): bool
    {
        return self::TYPE_HANGOUT === $this->getType() ? true : false;
    }

    public function isHelp(): bool
    {
        return self::TYPE_HELP === $this->getType() ? true : false;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setReferenceId(string $referenceId): TimelineInterface
    {
        $this->reference_id = $referenceId;
        return $this;
    }

    public function getReferenceId(): string
    {
        return $this->reference_id;
    }

    public function setReferenceUserId(string $referenceUserId): TimelineInterface
    {
        $this->reference_user_id = $referenceUserId;
        return $this;
    }

    public function getReferenceUserId(): string
    {
        return $this->reference_user_id;
    }
}
