<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /** @var User */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => optional($this->email_verified_at)->format('M d Y'),
            'profile_image' => $this->profile_image_url,
            'role' => $this->role,
            'points' => $this->formatted_available_points,
            'total_points' => $this->formatted_total_points,
            'referred_by' => $this->referred_by,
            'referral_token' => $this->referral_token,
            'banned_at' => optional($this->banned_at)->format('M d Y'),
            'ban_reason' => $this->ban_reason,
            'registered_giveaway_at' => $this->registered_giveaway_at,
            'two_factor_enabled_at' => $this->two_factor_enabled_at,
            'created_at' => $this->formatted_created_at,
            'updated_at' => $this->updated_at->format('M d Y'),
        ];
    }
}
