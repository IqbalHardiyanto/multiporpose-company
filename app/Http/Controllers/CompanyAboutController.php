<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAboutRequest;
use App\Http\Requests\UpdateAboutRequest;
use App\Models\CompanyAbout;
use Illuminate\Support\Facades\DB;

class CompanyAboutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $abouts = CompanyAbout::orderByDesc('id')->paginate(10);

        return view('admin.abouts.index', compact('abouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.abouts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAboutRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $imagePath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $imagePath;
            }

            $newAboutRecord = CompanyAbout::create($validated);

            if (! empty($validated['keypoints'])) {
                foreach ($validated['keypoints'] as $keypoint) {
                    $newAboutRecord->keypoints()->create([
                        'keypoint' => $keypoint,
                    ]);
                }
            }
        });

        return redirect()->route('admin.abouts.index')->with('success', 'About created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanyAbout $companyAbout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanyAbout $about)
    {
        //
        return view('admin.abouts.edit', compact('about'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAboutRequest $request, CompanyAbout $about)
    {
        //
        DB::transaction(function () use ($request, $about) {
            $validated = $request->validated();

            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if exists
                if ($about->thumbnail) {
                    Storage::disk('public')->delete($about->thumbnail);
                }

                $imagePath = $request->file('thumbnail')->store('thumbnails', 'public');
                $validated['thumbnail'] = $imagePath;
            }

            $about->update($validated);

            // Update keypoints
            if (isset($validated['keypoints'])) {
                // Delete existing keypoints
                $about->keypoints()->delete();

                // Create new keypoints
                foreach ($validated['keypoints'] as $keypoint) {
                    $about->keypoints()->create([
                        'keypoint' => $keypoint,
                    ]);
                }
            }
        });

        return redirect()->route('admin.abouts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyAbout $about)
    {
        //
        DB::transaction(function () use ($about) {
            $about->delete();
        });

        return redirect()->route('admin.abouts.index');
    }
}
