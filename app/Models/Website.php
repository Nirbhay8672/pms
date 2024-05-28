<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Website extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class,'website_id', 'id');
    }

    public function packageType() : BelongsTo
    {
        return $this->belongsTo(PackageType::class,'package_type_id', 'id');
    }

    public function websiteDetails() : HasOne
    {
        return $this->hasOne(WebsiteDetails::class, 'website_id', 'id');
    }
}