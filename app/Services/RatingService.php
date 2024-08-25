<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Rating;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RatingService
{
    use ResponseTrait;

    /**
     * Get listing of ratings.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        $ratings = Rating::all();
        if ($ratings->isEmpty()) {
            return $this->getResponse("msg", "Not Found Any Rating!, GoTo Create New Rating", 404);
        }
        $data = [];
        foreach ($ratings as $rating) {
            $data[] = [
                "id"            =>      $rating->id,
                "user-name"     =>      $rating->user->name,
                "movie-name"    =>      $rating->movie->title,
                "rating"        =>      $rating->rating,
                "review"        =>      $rating->review
            ];
        }
        return $this->getResponse("data", $data, 200);
    }

    /**
     * Get the specified rating by id.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        $rating = Rating::find($id);
        if (!$rating)
            return $this->getResponse("error", "This rating isn't found!", 404);
        $data = [
            "user-name"     =>      $rating->user->name,
            "movie-name"    =>      $rating->movie->title,
            "rating"        =>      $rating->rating,
            "review"        =>      $rating->review
        ];
        return $this->getResponse("rating", $data, 200);
    }

    /**
     * Create new rating in database.
     * @param array $rating
     * @return \Illuminate\Http\Response
     */
    public function createRating(array $data): Response
    {
        $validator = Validator::make($data, [
            "movie_id"      =>      "required|integer",
            "rating"        =>      "required|numeric|digits:1|min:1|max:5",
            "review"        =>      "nullable|string",
        ]);
        if ($validator->fails())
            throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 422));
        // Get validated data
        $dataValidator = $validator->validate();
        // Search on movie
        $movie = Movie::find($dataValidator['movie_id']);
        if (!$movie)
            return $this->getResponse("error", "This Movie isn't Found!", 404);
        // Merge userId that auth with validated data and writing on database
        $newRating = Rating::create(array_merge(['user_id' => Auth::id()], $dataValidator));
        if ($newRating)
            return $this->getResponse("msg", "Rating is created successfully", 201);
        else
            return $this->getResponse("msg", "Rating create is failed", 400);
    }

    /**
     * Update the specified rating in storage.
     * @param array $data
     * @param \App\Models\Rating $rating
     * @return \Illuminate\Http\Response
     */
    public function updateRating(array $data, Rating $rating): Response
    {
        $validator = Validator::make($data, [
            "movie_id"      =>      "required|integer",
            "rating"        =>      "required|numeric|digits:1|min:1|max:5",
            "review"        =>      "nullable|string",
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 402));
        }
        $dataValidator = $validator->validated();
        $movie = Movie::find($dataValidator['movie_id']);
        if (!$movie)
            return $this->getResponse("error", "This Movie isn't Found!", 404);
        $rating->movie_id = $dataValidator['movie_id'];
        $rating->rating = $dataValidator['rating'];
        $rating->review = $dataValidator['review'];
        $rating->save();
        return $this->getResponse("msg", "Rating is updated successfully", 200);
    }

    /**
     * Remove the specified rating from storage.
     * @param \App\Models\Rating $rating
     * @return \Illuminate\Http\Response
     */
    public function deleteRating(Rating $rating): Response
    {
        $rating->delete();
        return $this->getResponse("msg", "Rating is deleted successfully", 200);
    }
}
