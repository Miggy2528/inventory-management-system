<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAuthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'action',
        'ip_address',
        'user_agent',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the log entry
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get action display name
     */
    public function getActionDisplayAttribute(): string
    {
        return match($this->action) {
            'account_created' => 'Account Created',
            'login_success' => 'Login Successful',
            'login_failed' => 'Login Failed',
            'logout' => 'Logout',
            'password_changed' => 'Password Changed',
            'account_suspended' => 'Account Suspended',
            'account_activated' => 'Account Activated',
            default => ucwords(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Scope for successful logins
     */
    public function scopeLoginSuccess($query)
    {
        return $query->where('action', 'login_success');
    }

    /**
     * Scope for failed logins
     */
    public function scopeLoginFailed($query)
    {
        return $query->where('action', 'login_failed');
    }

    /**
     * Scope for account creation
     */
    public function scopeAccountCreated($query)
    {
        return $query->where('action', 'account_created');
    }

    /**
     * Scope for recent logs (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Get formatted details
     */
    public function getFormattedDetailsAttribute(): string
    {
        if (!$this->details) {
            return 'No additional details';
        }

        $details = [];
        
        foreach ($this->details as $key => $value) {
            $details[] = ucwords(str_replace('_', ' ', $key)) . ': ' . $value;
        }

        return implode(', ', $details);
    }
} 