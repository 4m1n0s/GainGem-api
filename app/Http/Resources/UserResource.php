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
        $response = [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => optional($this->email_verified_at)->format('M d Y'),
            'profile_image' => $this->profile_image_url,
            'points' => $this->available_points,
            'total_points' => $this->total_points,
            'referred_by' => $this->referred_by,
            'referral_token' => $this->referral_token,
            'banned_at' => optional($this->banned_at)->format('M d Y'),
            'ban_reason' => $this->ban_reason,
            'created_at' => $this->created_at->format('M d Y'),
            'updated_at' => $this->updated_at->format('M d Y'),
        ];

        $user = auth()->user();

        if ($this->resource->isAdminRole() && ($user && $user->isAdminRole())) {
            $response = array_merge($response, [
                'ip' => $this->ip,
                'role' => $this->role,
            ]);
        }

        return $response;
    }
}
