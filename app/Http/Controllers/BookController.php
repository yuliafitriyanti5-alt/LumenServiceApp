<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // 1. READ ALL (Hanya buku milik user yang login)
    public function index()
    {
        $books = Book::where('user_id', Auth::user()->id)->get();
        
        return response()->json($books, 200);
    }

    // 2. CREATE
    public function store(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'title' => 'required|string|max:150',
            'author' => 'required|string|max:100',
            'published_year' => 'required|integer',
        ]);

        $book = new Book();
        $book->user_id = Auth::user()->id; // Otomatis mengambil ID dari token JWT
        $book->title = $request->input('title');
        $book->author = $request->input('author');
        $book->published_year = $request->input('published_year');
        $book->save();

        return response()->json($book, 201);
    }

    // 3. READ SINGLE
    public function show($id)
    {
        // Cari buku berdasarkan ID dan pastikan itu milik user yang login
        $book = Book::where('id', $id)->where('user_id', Auth::user()->id)->first();
        
        if (!$book) {
            return response()->json(['message' => 'Book not found or unauthorized'], 404);
        }
        
        return response()->json($book, 200);
    }

    // 4. UPDATE
    public function update(Request $request, $id)
    {
        $book = Book::where('id', $id)->where('user_id', Auth::user()->id)->first();
        
        if (!$book) {
            return response()->json(['message' => 'Book not found or unauthorized'], 404);
        }

        $this->validate($request, [
            'title' => 'string|max:150',
            'author' => 'string|max:100',
            'published_year' => 'integer',
        ]);

        // Update data (jika field tidak dikirim, gunakan data lama)
        $book->title = $request->input('title', $book->title);
        $book->author = $request->input('author', $book->author);
        $book->published_year = $request->input('published_year', $book->published_year);
        $book->save();

        return response()->json($book, 200);
    }

    // 5. DELETE
    public function destroy($id)
    {
        $book = Book::where('id', $id)->where('user_id', Auth::user()->id)->first();
        
        if (!$book) {
            return response()->json(['message' => 'Book not found or unauthorized'], 404);
        }

        $book->delete();
        
        return response()->json(['message' => 'Book deleted successfully'], 200);
    }
}