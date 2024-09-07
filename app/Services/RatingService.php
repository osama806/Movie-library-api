<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Rating;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class RatingService
{
    use ResponseTrait;

    /**
     * Create new rating in database.
     * @param array $data
     * @return array
     */
    public function createRating(array $data)
    {
        // Search on movie
        $movie = Movie::find($data['movie_id']);
        if (!$movie)
            return ['status'    =>  false, "msg" => "This Movie isn't Found!", 'code'   => 404];
        // Merge userId that auth with validated data and writing on database
        $newRating = Rating::create(array_merge(['user_id' => Auth::id()], $data));
        if ($newRating)
            return ['status'    =>  false];
        else
            return ['status'    =>  false, "msg" => "Rating create is failed", 'code' =>   400];
    }

    /**
     * Get the specified rating by id.
     * @param \App\Models\Rating $rating
     * @return array
     */
    public function show(Rating $rating)
    {
        $data = [
            "user-name"     =>      $rating->user->name,
            "movie-name"    =>      $rating->movie->title,
            "rating"        =>      $rating->rating,
            "review"        =>      $rating->review
        ];
        return ['status'    =>  true,  "rating" =>  $data];
    }

    /**
     * Update the specified rating in storage.
     * @param array $data
     * @param \App\Models\Rating $rating
     * @return array
     */
    public function updateRating(array $data, Rating $rating)
    {
        $movie = Movie::find($data['movie_id']);
        if (!$movie)
            return ['status'    => false,  "msg" => "This Movie isn't Found!", 'code'   =>   404];
        if (isset($data['movie_id'])) {
            $rating->movie_id = $data['movie_id'];
        }
        if (isset($data['rating'])) {
            $rating->rating = $data['rating'];
        }
        if (isset($data['review'])) {
            $rating->review = $data['review'];
        }
        $rating->save();
        return ['status'    =>  true];
    }

    /**
     * Remove the specified rating from storage.
     * @param \App\Models\Rating $rating
     * @return bool[]
     */
    public function deleteRating(Rating $rating)
    {
        $rating->delete();
        return ['status'    =>  true];
    }
}
