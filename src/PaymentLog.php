<?php
namespace Xehub\Xepay;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $table = 'payment_log';

    protected $fillable = ['pg', 'oid', 'tid', 'type', 'method', 'currency', 'amount', 'success', 'response'];

    protected $casts = ['success' => 'boolean', 'response' => 'array'];

    protected $dates = ['created_at'];

    public $timestamps = false;

    const TYPE_PAY = 'pay';

    const TYPE_ROLLBACK = 'rollback';

    const TYPE_CANCEL = 'cancel';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasOne(static::class, 'parent_id');
    }

    public function scopeByPg($query, $name)
    {
        return $query->where('pg', $name);
    }

    public function scopeByOid($query, $id)
    {
        return $query->where('oid', $id);
    }

    public function scopeByTid($query, $id)
    {
        return $query->where('tid', $id);
    }

    public function scopePaid($query)
    {
        return $query->where('type', static::TYPE_PAY);
    }

    public function scopeRollbacked($query)
    {
        return $query->where('type', static::TYPE_ROLLBACK);
    }

    public function scopeCancelled($query)
    {
        return $query->where('type', static::TYPE_CANCEL);
    }

    public function scopeSucceeded($query)
    {
        return $query->where('success', true);
    }
}
