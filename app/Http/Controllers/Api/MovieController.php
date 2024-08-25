<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Services\MovieService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use ResponseTrait;
    protected $movieService;

    /**
     * Call MovieService when this controller is running
     * @param \App\Services\MovieService $movieService
     */
    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Get a listing of movies with pagination, sorting and filtering
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->movieService->index($request->all());
    }

    /**
     * Store a newly created movie in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // send all data that recieved from request to createMovie method in movieService service
        return $this->movieService->createMovie($request->all());
    }

    /**
     * Get the specified movie by id.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->movieService->show($id);
    }

    /**
     * Update the specified movie in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // check if movie is exsist or no
        $movie = Movie::find($id);
        if (!$movie)
            return $this->getResponse("msg", "Not Found This Movie!", 404);

        // send data that recieved from request and movie id to updateMovie method in movieService service
        return $this->movieService->updateMovie($request->all(), $movie);
    }

    /**
     * Remove the specified movie from the storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movie = Movie::find($id);
        if (!$movie)
            return $this->getResponse("msg", "Not Found This Movie", 404);

        return $this->movieService->deleteMovie($movie);
    }
}
