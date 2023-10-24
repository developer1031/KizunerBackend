<?php

namespace Modules\Kizuner\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Upload\Models\Upload;

class Offer extends Model
{
    protected $table = 'hangout_offers';

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
        'cast_cancelled'  => 11,
        'paid'  => 12,
        'guest_started'  => 13, // for noti
        'guest_declined' => 14,
    ];

    public static $label = [
        'pending'   => 'waiting',
        'queuing'   => 'waiting',
        'accept'    => 'approved',
        'reject'    => 'rejected',
        'cancel'    => 'canceled',
    ];

    protected $fillable = [
        'hangout_id',
        'hangout_title',
        'sender_id',
        'receiver_id',
        'kizuna',
        'position',
        'start',
        'end',
        'status',
        'address',
        'hangout_update',
        'amount',
        'payment_method',
        'stripe_intent_id',
        'payment_status',
        'stripe_refund_id',
        'now_payments_refund_id',
        'invoice_url',
        'is_within_time',
        'is_able_contact',
        'subject_cancel',
        'message_cancel',
        'is_refund',
        'subject_reject',
        'message_reject',
        'media_evidence',
        'refund_crypto_wallet_id',
        'stripe_transfer_id',
        'now_payments_transfer_id',
        'now_payments_id'
    ];

    public function hangout()
    {
        return $this->belongsTo(Hangout::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function media()
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

}
