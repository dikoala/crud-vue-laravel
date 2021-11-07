<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Article;
use App\Http\Resources\Article as ArticleResource;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Services\SlugService;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($n=10)
    {
        // Get articles
        $articles = Article::paginate($n);

        // Return collection of articles as a resource
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'cover_photo' => 'required'
        ]);
        //
        try {
            $article = $request->isMethod('put') ? Article::findOrFail($request->article_id) : new Article;

            // id can be excluded since it's auto in the table
            //$article->id = $request->input('article_id');
            $article->title = $request->input('title');
            $article->body = $request->input('body');
            $article->status = $request->input('status');
            $article->tags = $request->input('tags');

            if ($request->input('cover_photo')) {
                $image = $request->input('cover_photo');
                $imageInfo = explode(";base64,", $image);
                $imgExt = str_replace('data:image/', '', $imageInfo[0]);
                $image = str_replace(' ', '+', $imageInfo[1]);
                $imageName = "article-" . time() . "." . $imgExt;
                Storage::disk('uploads')->put($imageName, base64_decode($image));
                $article->cover_photo = $imageName;
            }

            $article->slug = SlugService::createSlug(Article::class, 'slug', $request->input('title'));

            if ($article->save()) {
                return new ArticleResource($article);
            }
        } catch (\Exception $e) {
            return \response()->json([
                'status' => 'error',
                'data'   => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // Get article
        if (is_numeric($slug)) {
            $article = Article::find($slug);
        } else {
            $article = Article::where('slug', $slug)->first();
        }

        // Return single artilce as resource
        return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get article
        $article = Article::findOrFail($id);

        if ($article->delete()) {
            return new ArticleResource($article);
        }
    }
}
