<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

class DeleteUnusedProfileImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allImages = Storage::allFiles('profile-images');
        $userImages = User::whereNotNull('profile_image')->get('profile_image')->pluck('profile_image');

        $imagesToDelete = array_diff($allImages, $userImages->toArray());

        Storage::delete($imagesToDelete);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
