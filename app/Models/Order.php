<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    /**
     * Temporary storage for status audit during update.
     */
    protected ?array $pendingStatusAudit = null;

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_service_id',
        'tailor_id',
        'product_id',
        'status',
        'total_price',
        'currency',
        'notes',
        'delivery_date',
        'length',
        'shoulder',
        'chest',
        'waist',
        'hip',
        'sleeve',
        'measurement_notes',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'delivery_date' => 'date',
            'length' => 'decimal:2',
            'shoulder' => 'decimal:2',
            'chest' => 'decimal:2',
            'waist' => 'decimal:2',
            'hip' => 'decimal:2',
            'sleeve' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
            if (empty($order->currency)) {
                $order->currency = \App\Helpers\CurrencyHelper::getDefaultCurrency();
            }
        });

        static::created(function ($order) {
            // Log initial status when order is created
            if ($order->status && auth()->check()) {
                OrderStatusAudit::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'status' => $order->status,
                    'from_status' => null,
                ]);
            }
        });

        static::updating(function ($order) {
            // Track status changes
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                // Only create audit if status actually changed and user is authenticated
                if ($oldStatus !== $newStatus && auth()->check()) {
                    // Store the change in the model instance to persist between events
                    $order->pendingStatusAudit = [
                        'order_id' => $order->id,
                        'user_id' => auth()->id(),
                        'status' => $newStatus,
                        'from_status' => $oldStatus,
                    ];
                } else {
                    $order->pendingStatusAudit = null;
                }
            } else {
                $order->pendingStatusAudit = null;
            }
        });

        static::updated(function ($order) {
            // Create audit record after the order is updated
            if ($order->pendingStatusAudit !== null && auth()->check()) {
                OrderStatusAudit::create($order->pendingStatusAudit);
                $order->pendingStatusAudit = null;
            }
        });
    }

    /**
     * Generate unique order number.
     */
    protected static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) Str::afterLast($lastOrder->order_number, '-');
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'ORD-' . $date . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the customer service user that created the order.
     */
    public function customerService(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_service_id');
    }

    /**
     * Get the tailor assigned to the order.
     */
    public function tailor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tailor_id');
    }

    /**
     * Get the product for the order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the status audits for the order.
     */
    public function statusAudits(): HasMany
    {
        return $this->hasMany(OrderStatusAudit::class);
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }
}
