<?php

namespace App\Http\Controllers\User;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class PostsController extends Controller
{
    public function index()
    {
        if (request()->query('type') == 'drafted') {
            $posts = UserRepository::getDraftedPosts();
        } else {
            $posts = UserRepository::getQueuedPosts();
        }
        return view('user.main.posts.index', compact(['posts']));
    }

    public function create()
    {
        $userMedia = UserRepository::getMedia(['id', 'name']);
        $userAccounts = UserRepository::getAccounts(['id', 'type', 'name']);
        return view('user.main.posts.create', compact(['userMedia', 'userAccounts']));
    }

    public function store(PostRequest $request)
    {
        PostService::store($request);
        return redirect()->route('posts.create')->with('status', 'your post saved successfuly');
    }

    public function show(Post $post)
    {
        $this->authorize('show', $post);
        $post->load(['media:id,name']);
        return view('user.main.posts.show', compact(['post']));
    }
    public function edit(Post $post)
    {
        $this->authorize('edit', $post);
        $post->load(['accounts:id,name,type', 'media:id,name']);
        $userAccounts = UserRepository::getAccounts(['name', 'type', 'id']);
        $userMedia = UserRepository::getMedia();
        return view('user.main.posts.edit', compact(['userAccounts', 'userMedia', 'post']));
    }

    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('edit', $post);
        PostService::update($request, $post);
        return redirect()->route('posts.edit', $post->id)->with('status', 'your post updated successfully');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('posts.index', ($post->draft ? ['type' => 'drafted'] : ''))->with('status', 'your post deleted successfully');
    }
}
