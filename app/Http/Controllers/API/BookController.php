<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/api/books',
        summary: "Lister les livres",
        tags: ["Livres"],
        parameters: [new OA\Parameter(name: 'Accept', in: 'header', example: 'application/json')],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    public function index()
    {
        // return BookResource::collection(Book::all());
        return BookResource::collection(Book::paginate(2));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|min:3|max:255',
            'author'  => 'required|string|min:3|max:100',
            'summary' => 'required|string|min:10|max:500',
            'isbn'    => 'required|string|size:13|unique:books',
        ]);

        $book = Book::create($validated);
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $cachedBook = Cache::remember('book_' . $book->id, 3600, function () use ($book) {
            return $book; 
        });

        return new BookResource($cachedBook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'   => 'sometimes|string|min:3|max:255',
            'author'  => 'sometimes|string|min:3|max:100',
            'summary' => 'sometimes|string|min:10|max:500',
            'isbn'    => 'sometimes|string|size:13|unique:books,isbn,' . $book->id,
        ]);

        $book->update($validated);

        Cache::forget('book_' . $book->id);

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        Cache::forget('book_' . $book->id);
        $book->delete();
        return response()->noContent();
    }
}
