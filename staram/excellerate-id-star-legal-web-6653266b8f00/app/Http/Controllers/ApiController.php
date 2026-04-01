<?php

namespace App\Http\Controllers;

use App\Model\DocReview;
class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    function apiRoute(Request $request) {
        return $request->user();
    }

    public function getReviewDocs()
    {
        return response()->json(DocReview::all());
    }
}
