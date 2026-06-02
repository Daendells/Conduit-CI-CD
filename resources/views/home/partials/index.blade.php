<div class="home-page">
  <div class="banner">
    <div class="container">
      <h1 class="logo-font">conduit</h1>
      <p>A place to share your knowledge.</p>
    </div>
  </div>

  <div class="container page">
    <div class="row">

      <div class="col-md-9">
        @php
          $sort = request()->query('sort', 'newest');
          $activeFeed = isset($tag) ? 'tag' : (isset($personal) ? 'personal' : 'global');
          $feedUrls = [
            'global' => ['url' => '/', 'htmx' => '/htmx/home/global-feed'],
            'personal' => ['url' => '/your-feed', 'htmx' => '/htmx/home/your-feed'],
            'tag' => ['url' => '/tag-feed/' . (isset($tag) ? $tag->name : ''), 'htmx' => '/htmx/home/tag-feed/' . (isset($tag) ? $tag->name : '')],
          ];
        @endphp

        @php
          $feedQuery = [];
          if (request()->query('page')) {
              $feedQuery['page'] = request()->query('page');
          }
          if (request()->query('sort')) {
              $feedQuery['sort'] = request()->query('sort');
          }
          $feedQueryString = count($feedQuery) ? '?' . http_build_query($feedQuery) : '';
        @endphp

        <div class="feed-toggle">
          <ul id="feed-navigation" class="nav nav-pills outline-active"></ul>
        </div>

        <div class="feed-sort mb-3">
          <span class="text-muted">Sort:</span>
          <div class="btn-group btn-group-sm" role="group">
            @foreach (['newest' => 'Newest', 'oldest' => 'Oldest'] as $value => $label)
              <a class="btn btn-outline-secondary {{ $sort === $value ? 'active' : '' }}"
                href="{{ $feedUrls[$activeFeed]['url'] }}?sort={{ $value }}"
                hx-get="{{ $feedUrls[$activeFeed]['htmx'] }}?sort={{ $value }}"
                hx-target="#feed-post-preview"
                hx-push-url="{{ $feedUrls[$activeFeed]['url'] }}?sort={{ $value }}"
              >
                {{ $label }}
              </a>
            @endforeach
          </div>
        </div>

        <div id="feed-post-preview"
          hx-trigger="load"
          hx-get="{{ $feedUrls[$activeFeed]['htmx'] }}{{ $feedQueryString }}"
        ></div>

        <nav id="feed-pagination"></nav>
      </div>

      <div class="col-md-3">
        <div class="sidebar">
          <p>Popular Tags</p>

          <div id="popular-tag-list" class="tag-list"
            hx-trigger="load"
            hx-get="/htmx/home/tag-list"
          ></div>
        </div>
      </div>

    </div>
  </div>
</div>