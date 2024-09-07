<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\StoreFormRequest;
use App\Http\Requests\Rating\UpdateFormRequest;
use App\Models\Rating;
use App\Services\RatingService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
     * Store a newly created rating in storage.
     * @param \App\Http\Requests\Rating\StoreFormRequest $storeFormRequest
     * @return Response
     */
    public function store(StoreFormRequest $storeFormRequest)
    {
        $validated = $storeFormRequest->validated();
        $response = $this->ratingService->createRating($validated);
        return $response['status']
            ? $this->getResponse('msg', 'Rating is created successfully', 201)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Get the specified rating by id.
     * @param mixed $id
     * @return Response
     */
    public function show($id)
    {
        $rating = Rating::find($id);
        if (!$rating)
            return $this->getResponse("error", "This rating isn't found!", 404);
        $response = $this->ratingService->show($rating);
        return $response['status']
            ? $this->getResponse('msg', $response['rating'],  200)
            :   $this->getResponse('error', "There is error in server",  500);
    }

    /**
     * Update the specified rating in storage.
     * @param \App\Http\Requests\Rating\UpdateFormRequest $updateFormRequest
     * @param mixed $id
     * @return Response
     */
    public function update(UpdateFormRequest $updateFormRequest, $id)
    {
        $validated = $updateFormRequest->validated();
        $rating = Rating::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$rating)
            return $this->getResponse("msg", "Not Found This Rating", 404);
        $response = $this->ratingService->updateRating($validated, $rating);
        return $response['status']
            ? $this->getResponse('msg', 'Updated rating successfully', 200)
            :   $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Remove the specified rating from storage.
     * @param mixed $id
     * @return Response
     */
    public function destroy($id)
    {
        $rating = Rating::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$rating)
            return $this->getResponse("msg", "Not Found This Rating", 404);
        $response = $this->ratingService->deleteRating($rating);
        return $response['status']
            ? $this->getResponse('msg', "Rating is deleted successfully", 200)
            :   $this->getResponse('error', $response['msg'], $response['code']);
    }
}
