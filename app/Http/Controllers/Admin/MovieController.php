<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Movie;

class MovieController extends Controller
{
    
    public function index()
    {
        //ambil data movie dari DB
        $movies = Movie::all();

        return view('admin.movies', ['movies' => $movies]);
    }

    public function create()
    {
        return view('admin.movie-create');
    }

    public function store(Request $request)
    {
        //untuk ambil semua data, kecuali token
        $data = $request->except('_token');

        //validation
        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'required|image|mimes:png,jpg,jpeg',
            'large_thumbnail' => 'required|image|mimes:png,jpg,jpeg',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required'
        ]);

        //untuk cari nama file gambar yang diupload
        $smallThumbnail = $request->small_thumbnail;
        $largeThumbnail = $request->large_thumbnail;

        //untuk mengubah nama file gambar, agar tidak ada nama file yang kembar
        $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
        $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();

        //upload gambar ke folder thumbnail
        $smallThumbnail->storeAs('public/thumbnail', $originalSmallThumbnailName);
        $largeThumbnail->storeAs('public/thumbnail', $originalLargeThumbnailName);

        $data['small_thumbnail'] = $originalSmallThumbnailName;
        $data['large_thumbnail'] = $originalLargeThumbnailName;

        //save to database
        Movie::create($data);

        return redirect()->route('admin.movie')->with('success', 'Movie Created');
        
        //menampilkan ke array
        //dd(Str::random(10).$largeThumbnail->getClientOriginalName());
        //dd($data);
    }
}
