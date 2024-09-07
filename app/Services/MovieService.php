<?php

namespace App\Services;

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
        $director = $data['director'] ?? null;  // Get the director data from request if exsist

        // Prepare sorting
        $query = Movie::query();
        $sortBy = 'release_year';
        $query->orderBy($sortBy, $sortOrder);

        // Prepare filtering
        if ($director) {
            $query->where('director', $director);
        }

        // prepare pagination
        $movies = $query->paginate($perPage);
        if ($movies->isEmpty()) {
            return ['status'    =>  false, 'msg'    =>  "Not Found Any Movie!, GoTo Create New Movie", 'code'   => 404];
        }

        $moviesArray = [];
        foreach ($movies as $movie) {
            $ratingsArray = [];
            if ($movie->ratings()->count() > 0) {
                foreach ($movie->ratings as $rating) {
                    $ratingsArray[] = [
                        "username"      =>      $rating->user->name,
                        "rating"        =>      $rating->rating,
                        "review"        =>      $rating->review
                    ];
                }
            }

            $averageRating = $movie->ratings()->avg('rating') ?? 0;

            $moviesArray[] = [
                "title"          =>   $movie->title,
                "director"       =>   $movie->director,
                "genre"          =>   $movie->genre,
                "release_year"   =>   $movie->release_year,
                "description"    =>   $movie->description,
                "ratings"        =>   $ratingsArray,
                "average_rating" =>   round($averageRating, 1)
            ];
        }
        $responseData = [
            "current-page"      =>   $movies->currentPage(),
            "movies"            =>   $moviesArray,
            "next-page"         =>   $movies->nextPageUrl(),
            "previous-page"     =>   $movies->previousPageUrl(),
            "total-movies"      =>   $movies->total(),
            "movies-per-page"   =>   $movies->perPage()
        ];
        return ['status'    =>  true, 'movies'  =>  $responseData];
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
        // Update the movie's attributes with validated data
        if (isset($data['title'])) {
            $movie->title = $data['title'];
        }
        if (isset($data['director'])) {
            $movie->director = $data['director'];
        }
        if (isset($data['genre'])) {
            $movie->genre = $data['genre'];
        }
        if (isset($data['release_year'])) {
            $movie->release_year = $data['release_year'];
        }
        if (isset($data['description'])) {
            $movie->description = $data['description'];
        }

        // Save the updated movie details
        $movie->save();

        // Return a success response
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
