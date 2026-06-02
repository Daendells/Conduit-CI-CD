<?php

namespace App\Http\Controllers\Htmx;

use App\Models\Tag;
use App\Models\Article;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class HTMXHomeController extends Controller
{
    private function applySort($articles)
    {
        $sort = request()->query('sort', 'newest');

        if ($sort === 'oldest') {
            return $articles->orderBy('created_at', 'asc');
        }

        return $articles->latest();
    }

    private function appendSortToNavbarItems(array $feedNavbarItems): array
    {
        $sort = request()->query('sort');

        if (!$sort) {
            return $feedNavbarItems;
        }

        foreach ($feedNavbarItems as &$item) {
            $item['hx_get_url'] .= '?sort=' . $sort;
            $item['hx_push_url'] .= (str_contains($item['hx_push_url'], '?') ? '&' : '?') . 'sort=' . $sort;
        }

        return $feedNavbarItems;
    }

    public function index()
    {
        return view('home.partials.index')
            .view('components.navbar', [
                'navbar_active' => 'home'
            ]);
    }

    public function favorite(Article $article)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        
        $isArticleFavoritedByUser = $article->toggleUserFavorite(auth()->user());

        return view('home.partials.article-favorite-button', [
            'article' => $article,
            'favorite_count' => $article->favoritedUsers->count(),
            'is_favorited' => $isArticleFavoritedByUser,
        ]);
    }

    public function yourFeed()
    {
        $articles = Article::with(['user', 'tags', 'favoritedUsers']);

        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['personal']['is_active'] = true;
        $feedNavbarItems = $this->appendSortToNavbarItems($feedNavbarItems);

        $articles = $articles->ofAuthorsFollowedByUser(auth()->user());
        $articles = $this->applySort($articles)->paginate(5);

        return view('home.partials.post-preview', ['articles' => $articles])
            .view('home.partials.pagination', [
                'paginator' => $articles,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => 'Your feed —'
            ]);
    }

    public function globalFeed()
    {
        $articles = Article::with(['user', 'tags', 'favoritedUsers']);

        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['global']['is_active'] = true;
        $feedNavbarItems = $this->appendSortToNavbarItems($feedNavbarItems);

        $articles = $this->applySort($articles)->paginate(5);

        return view('home.partials.post-preview', ['articles' => $articles])
            .view('home.partials.pagination', [
                'paginator' => $articles,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => ''
            ]);
    }

    public function tagFeed(Tag $tag)
    {
        $articles = Article::with(['tags', 'favoritedUsers'])
            ->whereHas('tags', function($q) use ($tag) {
                $q->where('id', $tag->id);
            });

        $feedNavbarItems = Helpers::feedNavbarItems();
        $feedNavbarItems['tag'] = [
            'title' => $tag->name,
            'is_active' => true,
            'hx_get_url' => '/htmx/home/tag-feed/' . $tag->name,
            'hx_push_url' => '/tag-feed/' . $tag->name
        ];
        $feedNavbarItems = $this->appendSortToNavbarItems($feedNavbarItems);

        $articles = $this->applySort($articles)->paginate(5);

        return view('home.partials.post-preview', ['articles' => $articles])
            .view('home.partials.pagination', [
                'paginator' => $articles,
                'page_number' => request()->page ?? 1
            ])
            .view('home.partials.feed-navigation', ['feedNavbarItems' => $feedNavbarItems])
            .view('components.htmx.head', [
                'page_title' => Str::words($tag->name, 40, '') . ' —'
            ]);
    }

    public function tagList()
    {
        $popularTags = Tag::favoriteTags();

        return view('home.partials.tag-item-list', [
            'popularTags' => $popularTags
        ]);
    }
}
