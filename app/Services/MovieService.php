<?php

namespace App\Services;

use App\Models\Movie;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MovieService
{
    use ResponseTrait;

    /**
     * Get a listing of movies with pagination, sorting and filtering
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    public function index(array $data): Response
    {
        $validator = Validator::make($data, [
            "per_page"          =>      "nullable|integer|min:1",
            "sort_order"        =>      "nullable|string|in:asc,desc",
            "director"          =>      "nullable|string"
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator, $this->getResponse("error", "Invalid data entered", 400));
        }

        $data = $validator->validated();
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
            return $this->getResponse("msg", "Not Found Any Movie!, GoTo Create New Movie", 404);
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
            $moviesArray[] = [
                "id"            =>   $movie->id,
                "title"         =>   $movie->title,
                "director"      =>   $movie->director,
                "genre"         =>   $movie->genre,
                "release_year"  =>   $movie->release_year,
                "description"   =>   $movie->description,
                "ratings"       =>   $ratingsArray
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
        return $this->getResponse("data", $responseData, 200);
    }

    /**
     * Create new movie in database.
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    public function createMovie(array $data): Response
    {
        $validator = Validator::make($data, [
            "title"         => "required|string|max:255",
            "director"      => "required|string|max:255",
            "genre"         => "required|string|max:255",
            "release_year"  => "required|digits:4|integer|min:1800|max:" . date('Y'),
            "description"   => "required|string",
        ]);
        if ($validator->fails())
            throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 422));
        // Get the validated data
        $validatedData = $validator->validated();
        //Writing data in database
        $movie = Movie::create($validatedData);
        return $movie ?
            $this->getResponse("msg", "Movie is created successfully", 201)
            : $this->getResponse("msg", "Create movie failed!", 400);
    }

    /**
     * Get the specified movie.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        $movie = Movie::find($id);
        if (!$movie) {
            return $this->getResponse("msg", "Not Found This Movie!", 404);
        }
        $ratingsData = [];
        if ($movie->ratings()->count() > 0) {
            foreach ($movie->ratings as $rating) {
                $ratingsData[] = [
                    "user-name"         =>     $rating->user->name,
                    "rating"            =>     $rating->rating,
                    "review"            =>     $rating->review
                ];
            }
        }
        $data = [
            "id"            =>   $movie->id,
            "title"         =>   $movie->title,
            "director"      =>   $movie->director,
            "genre"         =>   $movie->genre,
            "release_year"  =>   $movie->release_year,
            "description"   =>   $movie->description,
            "ratings"       =>   $ratingsData
        ];
        return $this->getResponse("movie", $data, 200);
    }

    /**
     * Update details for specified movie.
     * @param array $data
     * @param \App\Models\Movie $movie
     * @return \Illuminate\Http\Response
     */
    public function updateMovie(array $data, Movie $movie): Response
    {
        // Validate data that recieved
        $validator = Validator::make($data, [
            "title"         => "required|string|max:255",
            "director"      => "required|string|max:255",
            "genre"         => "required|string|max:255",
            "release_year"  => "required|digits:4|integer|min:1800|max:" . date('Y'),
            "description"   => "required|string",
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            throw new ValidationException($validator, $this->getResponse("errors", $validator->errors(), 422));
        }

        // Get the validated data
        $validatedData = $validator->validated();

        // Update the movie's attributes with validated data
        $movie->title = $validatedData['title'];
        $movie->director = $validatedData['director'];
        $movie->genre = $validatedData['genre'];
        $movie->release_year = $validatedData['release_year'];
        $movie->description = $validatedData['description'];

        // Save the updated movie details
        $movie->save();

        // Return a success response
        return $this->getResponse("msg", "Movie details updated successfully", 200);
    }

    /**
     * Remove the specified movie from the database.
     * @param \App\Models\Movie $movie
     * @return \Illuminate\Http\Response
     */
    public function deleteMovie(Movie $movie): Response
    {
        $movie->delete();
        return $this->getResponse("msg", "Movie is deleted successfully", 200);
    }
}
