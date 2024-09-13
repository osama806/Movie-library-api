<?php

namespace App\Services;

use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Traits\ResponseTrait;

class MovieService
{
    use ResponseTrait;

    /**
     * Get a listing of movies with pagination, sorting and filtering
     * @param array $data
     * @return array
     */
    public function index(array $data)
    {
        $perPage = $data['per_page'] ?? 5; // Default to 5 if 'per_page' is not provided
        $sortOrder = $data['sort_order'] ?? 'desc'; // Default sort order to 'desc'
        $director = $data['director'] ?? null;  // Get the director data from request if exists
        $genre = $data['genre'] ?? null;  // Get the genre from request if exists

        // Prepare sorting
        $query = Movie::query();
        $sortBy = 'release_year';
        $query->orderBy($sortBy, $sortOrder);

        // Apply filtering
        if ($director) {
            $query->where('director', $director);
        }

        if ($genre) {
            $query->where('genre', $genre);
        }

        // Prepare pagination
        $movies = $query->paginate($perPage);

        // Check if no movies found
        if ($movies->isEmpty()) {
            return [
                'status' => false,
                'msg' => "Not Found Any Movie!, GoTo Create New Movie",
                'code' => 404
            ];
        }

        // Use API Resource to format the movie data
        $responseData = [
            "current-page"      => $movies->currentPage(),
            "movies"            => MovieResource::collection($movies), // MovieResource used instead of manual array
            "next-page"         => $movies->nextPageUrl(),
            "previous-page"     => $movies->previousPageUrl(),
            "total-movies"      => $movies->total(),
            "movies-per-page"   => $movies->perPage()
        ];

        return [
            'status' => true,
            'movies' => $responseData
        ];
    }

    /**
     * Create new movie in database.
     * @param array $data
     * @return array
     */
    public function createMovie(array $data)
    {
        $movie = Movie::create([
            'title'             =>      $data['title'],
            'director'          =>      $data['director'],
            'genre'             =>      $data['genre'],
            'release_year'      =>      $data['release_year'],
            'description'       =>      $data['description']
        ]);
        return $movie
            ? ['status'   =>  true]
            : ['status' =>  false,  "msg" => "Create movie failed!", 'code' => 400];
    }

    /**
     * Get the specified movie.
     * @param mixed $id
     * @return array
     */
    public function show($id)
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return ['status' => false, "msg" => "Movie not found!", "code" => 404];
        }

        // Get all ratings for the movie
        $ratingsData = [];
        if ($movie->ratings()->count() > 0) {
            foreach ($movie->ratings as $rating) {
                $ratingsData[] = [
                    "user-name" => $rating->user->name,
                    "rating"    => $rating->rating,
                    "review"    => $rating->review
                ];
            }
        }

        // Calculate average rating
        $averageRating = $movie->ratings()->avg('rating') ?? 0;

        // Prepare response data
        $data = [
            "title"          => $movie->title,
            "director"       => $movie->director,
            "genre"          => $movie->genre,
            "release_year"   => $movie->release_year,
            "description"    => $movie->description,
            "ratings"        => $ratingsData,
            "average_rating" => round($averageRating, 1)
        ];

        return ['status' => true, "movie" => $data, 'code' => 200];
    }


    /**
     * Update details for specified movie.
     * @param array $data
     * @param \App\Models\Movie $movie
     * @return bool[]
     */
    public function updateMovie(array $data, Movie $movie)
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (count($filteredData) < 1) {
            return ['status'    =>  false, 'msg' => 'Not Found Data To Update!', 'code' =>  404];
        }

        $movie->update($filteredData);
        return ['status'    =>  true];
    }

    /**
     * Remove the specified movie from the database.
     * @param \App\Models\Movie $movie
     * @return bool[]
     */
    public function deleteMovie(Movie $movie)
    {
        $movie->delete();
        return ['status'    =>  true];
    }
}
