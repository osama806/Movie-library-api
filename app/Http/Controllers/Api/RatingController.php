<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Services\RatingService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RatingController extends Controller
{
    use ResponseTrait;
    protected $ratingService;

    /**
     * Call RatingService when this controller is running
     * @param \App\Services\RatingService $ratingService
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Get a listing of ratings.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        return $this->ratingService->index();
    }

    /**
     * Store a newly created rating in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): Response
    {
        return $this->ratingService->createRating($request->all());
    }

    /**
     * Get the specified rating by id.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        return $this->ratingService->show($id);
    }

    /**
     * Update the specified rating in storage.
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): Response
    {
        $rating = Rating::find($id);
        if (!$rating)
            return $this->getResponse("msg", "Not Found This Rating", 404);
        return $this->ratingService->updateRating($request->all(), $rating);
    }

    /**
     * Remove the specified rating from storage.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rating = Rating::find($id);
        if (!$rating)
            return $this->getResponse("msg", "Not Found This Rating", 404);
        return $this->ratingService->deleteRating($rating);
    }
}
