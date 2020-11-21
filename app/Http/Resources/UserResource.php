<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $cols = [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'points' => $this->points,
            'total_points' => $this->total_points,
            'referred_by' => $this->referred_by,
            'banned_at' => $this->banned_at,
            'ban_reason' => $this->ban_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];


        if (! $this->resource->isAdminRole() || (auth()->check() && ! auth()->user()->isAdminRole())) {
            return $cols;
        }

        $adminCols = [
            'email_verified_at' => $this->email_verified_at,
            'ip' => $this->ip,
            'role' => $this->role,
        ];

        return array_merge($cols, $adminCols);
    }
}
