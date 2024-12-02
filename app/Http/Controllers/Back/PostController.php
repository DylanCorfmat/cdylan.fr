<?php

namespace App\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\{
    Controllers\Controller,
    Requests\Back\PostRequest
};
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\DB;
use App\Models\{ Post, Category };
use App\DataTables\PostsDataTable;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PostsDataTable $dataTable)
    {
        return $dataTable->render('back.shared.index');
    }


    public function newindex(){

        $posts = Post::all(); // Or apply filters as needed
        $scheduledPosts = Post::whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now())
            ->get();

        return view('back.posts.index', compact('posts', 'scheduledPosts'));
    }

    public function drafts(){

        $posts = DB::table('posts')->orderBy('created_at')->where('scheduled_at','=',null)->get();

        return view('back.posts.index', compact('posts'));
    }

    public function scheduled(){

        $posts = DB::table('posts')
            ->where('scheduled_at','>',now())
            ->orderBy('scheduled_at')
            ->get();

        return view('back.posts.index', compact('posts'));
    }

    public function published(){

        $posts = DB::table('posts')
            ->where('scheduled_at','<=',now())
            ->orderBy('scheduled_at')
            ->get();

        return view('back.posts.index', compact('posts'));
    }

    public function archived(){

        $posts = DB::table('posts')
            ->where('status','=','archived')
            ->orderBy('created_at')
            ->get();

        return view('back.posts.index', compact('posts'));
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function prep(PostsDataTable $dataTable)
    {
        $posts = DB::table('posts')->where('updated_at', '<', now())->get();
//        dd($posts);
        return View('back.posts.index')->with('posts',$posts);
//        return $dataTable->render('back.shared.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $post = null;
        if($id) {
            $post = Post::findOrFail($id);
            if($post->user_id === auth()->id()) {
                $post->title .= ' (2)';
                $post->slug .='-2';
                $post->active = false;
            } else {
                $post = null;
            }
        }

        $categories = Category::all()->pluck('title', 'id');
        return view('back.posts.form', compact('post', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request, PostRepository $repository)
    {
        $repository->store($request);
        return back()->with('ok', __('The post has been successfully created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all()->pluck('title', 'id');
        return view('back.posts.form', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, PostRepository $repository, Post $post)
    {
        $repository->update($post, $request);
        return back()->with('ok', __('The post has been successfully updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json();
    }
}
