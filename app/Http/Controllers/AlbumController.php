<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $albums = Album::where('user_id', auth()->user()->id)->get();
        if ($albums->count() > 0) {
            return view('pages.albums.albums', ['albums' => $albums]);
        } else {

            return view('pages.albums.empty', compact('albums'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'albumName' => 'required|string|max:50',
        ]);

        $data['user_id'] = Auth::user()->id;
        Album::create($data);
        session()->flash('Add', 'album Created successfully');
        return redirect('/albums');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $album = Album::findOrFail($id);
        $album_all = Album::all();
        $images = Image::where('album_id', $album->id)->get();
        return view('pages.albums.showAlbum', compact('album_all', 'album', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Album $album)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);
        $data = $request->validate([
            'albumName' => 'required|string|max:50|unique:albums,albumName,' . $id,
        ]);
        $album->update($data);
        return redirect('/albums')->with('success', 'menuItems updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $id = $request->id;
        $album = Album::findOrFail($id);
        $album->delete();
        session()->flash('Delete', 'Album Deleted Successfully');
        return redirect('/albums');
    }
}
