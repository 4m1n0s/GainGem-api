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
            'email_verified_at' => $this->email_verified_at,
            'profile_image' => $this->profile_image,
            'points' => $this->available_points,
            'total_points' => $this->total_points,
            'referred_by' => $this->referred_by,
            'referral_token' => $this->referral_token,
            'banned_at' => $this->banned_at,
            'ban_reason' => $this->ban_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        $user = auth()->user();

        if ($this->resource->isAdminRole() && ($user && $user->isAdminRole())) {
            $response = array_merge($response, [
                'email_verified_at' => $this->email_verified_at,
                'ip' => $this->ip,
                'role' => $this->role,
            ]);
        }

        return $response;
    }
}
