<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\IndexFormRequest;
use App\Http\Requests\Movie\StoreFormRequest;
use App\Http\Requests\Movie\UpdateFormRequest;
use App\Models\Movie;
use App\Services\MovieService;
use App\Traits\ResponseTrait;

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
     * @param \App\Http\Requests\Movie\IndexFormRequest $indexFormRequest
     * @return \Illuminate\Http\Response
     */
    public function index(IndexFormRequest $indexFormRequest)
    {
        $validated = $indexFormRequest->validated();
        $response = $this->movieService->index($validated);
        return $response['status']
            ? $this->getResponse('movies', $response['movies'], 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Store a newly created movie in storage.
     * @param \App\Http\Requests\Movie\StoreFormRequest $storeFormRequest
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFormRequest $storeFormRequest)
    {
        $validated = $storeFormRequest->validated();
        $response = $this->movieService->createMovie($validated);
        return $response['status']
            ? $this->getResponse('msg', 'Movie is created successfully', 201)
            : $this->getResponse('msg', $response['msg'], $response['code']);
    }

    /**
     * Get the specified movie by id.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->movieService->show($id);
        return $response['status']
            ? $this->getResponse('msg', $response['movie'], 200)
            : $this->getResponse('msg', $response['msg'], $response['code']);
    }

    /**
     * Update the specified movie in storage.
     * @param \App\Http\Requests\Movie\UpdateFormRequest $updateFormRequest
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFormRequest $updateFormRequest, $id)
    {
        $validated = $updateFormRequest->validated();
        // check if movie is exsist or no
        $movie = Movie::find($id);
        if (!$movie)
            return $this->getResponse("msg", "Not Found This Movie!", 404);

        // send data that recieved from request and movie id to updateMovie method in movieService service
        $response = $this->movieService->updateMovie($validated, $movie);
        return $response['status']
            ? $this->getResponse('msg', 'Movie details updated successfully', 200)
            : $this->getResponse('error', 'There is error in server', 500);
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

        $response = $this->movieService->deleteMovie($movie);
        return $response['status']
            ? $this->getResponse('msg', "Movie is deleted successfully", 200)
            : $this->getResponse('error', "There is error in server", 500);
    }
}
