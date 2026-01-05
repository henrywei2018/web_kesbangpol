<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class BlogPostController extends Controller
{
    /**
     * Get list of blog posts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['category', 'tags'])
            ->when($request->search, function($query) use ($request) {
                $query->search($request->search);
            })
            ->when($request->category_id, function($query) use ($request) {
                $query->filterByCategory($request->category_id);
            })
            ->when($request->has('is_featured'), function($query) use ($request) {
                $query->where('is_featured', $request->boolean('is_featured'));
            })
            ->when($request->published, function($query) {
                $query->published();
            });

        $posts = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    /**
     * Get a specific blog post
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        $post->load(['author', 'category', 'tags']);
        
        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    /**
     * Create a new blog post
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'content_overview' => 'required|string',
            'published_at' => 'nullable|date',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'image' => 'nullable|image|max:2048' // 2MB Max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $post = new Post($request->all());
        $post->blog_author_id = auth()->id();
        $post->slug = Str::slug($request->title);
        $post->save();

        // Handle tags
        if ($request->has('tags')) {
            $post->syncTags($request->tags);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')
                ->toMediaCollection('featured_image');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Post created successfully',
            'data' => $post->load(['author', 'category', 'tags'])
        ], 201);
    }

    /**
     * Update a blog post
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'blog_category_id' => 'exists:blog_categories,id',
            'title' => 'string|max:255',
            'content' => 'string',
            'content_overview' => 'string',
            'published_at' => 'nullable|date',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('title')) {
            $request->merge(['slug' => Str::slug($request->title)]);
        }

        $post->update($request->all());

        // Handle tags
        if ($request->has('tags')) {
            $post->syncTags($request->tags);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $post->clearMediaCollection('featured_image');
            $post->addMediaFromRequest('image')
                ->toMediaCollection('featured_image');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Post updated successfully',
            'data' => $post->load(['author', 'category', 'tags'])
        ]);
    }
}