@auth
    <div class="sidebar__profile">
        <img src="{{ Vite::asset('resources/images/avatar.png') }}" class="ui image">
        <div class="meta">
            <h4 class="ui header">{{ auth()->user()->name }}</h4>
            <div class="extra">
                <a href="{{ route('auth::logout') }}" class="item">Logout</a>
            </div>
        </div>
    </div>
@endauth
