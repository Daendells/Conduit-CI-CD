<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{{ $page_title ?? '' }} Conduit</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tagify.css') }}">
    
    <style>
      .tagify--outside{
        border: 0;
      }

      .tagify--outside .tagify__input{
        order: -1;
        flex: 100%;
        border: 1px solid var(--tags-border-color);
        margin-bottom: 1em;
        transition: .1s;
      }

      .tagify--outside .tagify__input:hover{ border-color:var(--tags-hover-border-color); }
      .tagify--outside.tagify--focus .tagify__input{
        transition:0s;
        border-color: var(--tags-focus-border-color);
      }

      .tagify__input { border-radius: 4px; margin: 0; padding: 10px 12px; }

      /* Article editor — single unified card border */
      .article-form-card {
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 4px;
        margin-bottom: 1rem;
      }
      .article-form-card .card-block {
        padding: 0;
      }
      .article-form-card .form-group {
        margin-bottom: 0;
        border-bottom: 1px solid rgba(0,0,0,.1);
      }
      .article-form-card .form-group.article-form-last {
        border-bottom: none;
      }
      .article-form-card .form-control {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
      }

      .banner { background: #1a73e8 !important; }

      /* Article card — whole card is clickable */
      .post-preview {
        position: relative;
        border-radius: 4px;
        padding: 1.5rem !important;
        transition: background 0.15s, box-shadow 0.15s;
        cursor: pointer;
      }

      .post-preview:hover {
        background: #f9f9f9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      }

      .preview-link {
        user-select: none;
      }

      /* Stretch the link to cover the whole card */
      .preview-link::after {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 0;
      }

      /* Keep avatar, author, favorite button above the stretched link */
      .post-preview .post-meta {
        position: relative;
        z-index: 1;
      }
    </style>

    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Titillium+Web:700|Source+Serif+Pro:400,700|Merriweather+Sans:400,700|Source+Sans+Pro:400,300,600,700,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css">
    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}">
  </head>
  <body hx-ext="head-support">
    <nav class="navbar navbar-light">
      <div class="container">
        <a class="navbar-brand" 
          href="/"
          hx-push-url="/"
          hx-get="/htmx/home" 
          hx-target="#app-body">conduit</a>
          
        @include('components.navbar')
      </div>
    </nav>

    <div id="app-body">
      @yield('content')
    </div>
    
    <footer>
      <div class="container">
        <a href="/" class="logo-font">conduit</a>
        <span class="attribution">
          An interactive learning project from <a href="https://thinkster.io">Thinkster</a>. Code & design licensed under MIT.
        </span>
      </div>
    </footer>

    <div id="htmx-redirect"></div>

    <script src="{{ asset('js/htmx.js') }}"></script>
    <script src="{{ asset('js/htmx-head-support.js') }}"></script>
    <script src="{{ asset('js/tagify.js') }}"></script>

    <script>
      var isTagify = null;

      document.body.addEventListener('htmx:configRequest', function(evt) {
        evt.detail.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
      })

      window.addEventListener('DOMContentLoaded', function() {
        renderTagify();
      });

      document.body.addEventListener("htmx:afterSwap", function(evt) {
        renderTagify();
      });

      function renderTagify() {
        const input = document.querySelector('input[name=tags]');
        const tagify = document.querySelector('tags[class="tagify  form-control tagify--outside"]');

        if (input && !tagify) {
          new Tagify(input, {
            whitelist: [],
            dropdown: {
              position: "input",
              enabled : 0 // always opens dropdown when input gets focus
            }
          })
        }
      }
    </script>
  </body>
</html>