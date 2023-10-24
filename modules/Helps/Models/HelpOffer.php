<?php

namespace Modules\Helps\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Upload\Models\Upload;

class HelpOffer extends Model
{

    protected $table = 'help_offers';

    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_TRANSFERRING = 'transferring';
    const PAYMENT_STATUS_TRANSFERRED = 'transferred';
    const PAYMENT_STATUS_REFUNDING = 'refunding';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    public static $status = [
        'pending'   => 1,
        'queuing'   => 2,
        'accept'    => 3,
        'reject'    => 4,
        'completed' => 5,
        'cancel'    => 6,
        'started'    => 7,
        'bought'    => 8, // unused
        'approved'  => 9,

        'declined'  => 10,
        'helper_cancelled'  => 11,
        'paid'  => 12,
        'helper_started'  => 13, // for noti
        'helper_declined'  => 14,
    ];


    public static $label = [
        'pending'   => 'waiting',
        'queuing'   => 'waiting',
        'accept'    => 'approved',
        'reject'    => 'rejected',
        'cancel'    => 'canceled',
        'started'    => 'approved',
        'bought'    => 'approved',
    ];

    protected $fillable = [
        'help_id',
        'help_title',
        'help_update',
        'sender_id',
        'receiver_id',
        'kizuna',
        'position',
        'start',
        'end',
        'status',
        'address',
        'crypto_wallet_id',
        'subject_cancel',
        'message_cancel',
        'is_able_contact',
        'media_evidence',
        'payment_status',
        'invoice_url',
        'stripe_intent_id',
        'now_payments_id',
        'stripe_transfer_id',
        'now_payments_transfer_id',
        'stripe_refund_id',
        'now_payments_refund_id',
    ];

    public function help()
    {
        return $this->belongsTo(Help::class);
    }

    public function media()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
