<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return abort(404);
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
            'imageName' => 'required|string|max:50',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'album_id' => 'required|numeric'
        ]);

        if ($request->has('image')) {
            $data['image'] = Storage::putFile("images", $data['image']);
        }
        $data['user_id'] = Auth::user()->id;
        Image::create($data);
        session()->flash('Add', 'image Created successfully');
        return redirect()->back();
    }


    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request)
    {
        $id = $request->id;
        $image = Image::findOrFail($id);
        $data = $request->validate([
            'album_id' => 'required|exists:albums,id',
        ]);



        $image->update($data);
        return redirect()->back()->with('success', 'Image has been moved to another album successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $image = Image::findOrFail($id);
        if (!empty($image->image)) {
            Storage::delete($image->image);
        }
        $image->delete();
        session()->flash('Delete', 'image Deleted Successfully');
        return redirect()->back();
    }

    public function destroyAll($id)
    {
        // Retrieve the album by ID
        $album = Album::findOrFail($id);
        $images = Image::where('album_id', $album->id)->get();
        // Loop through each image in the album and delete it from the file system
        foreach ($images as $image) {
            // Delete image file from the file system
            if (is_file(public_path($image->file_path))) {
                unlink(public_path($image->file_path));
            }
        }

        // Delete images from the database
        $album->images()->delete();
        $image->delete();
        return redirect()->back()->with('success', 'All images in the album have been deleted successfully.');
    }
}
