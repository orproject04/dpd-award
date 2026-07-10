<x-volt-auth>
    <h3 class="ui header horizontal divider section">@lang('laravolt::auth.login')</h3>

    {!! form()->open(route('auth::login.store'))->attribute('up-target', 'body') !!}
    {!! form()->email('email')->label(__('laravolt::auth.identifier')) !!}
    {!! form()->password('password')->label(__('laravolt::auth.password')) !!}

    @if(config('laravolt.platform.features.captcha'))
        <div class="field">
            {!! app('captcha')->display() !!}
            {!! app('captcha')->renderJs() !!}

        </div>
    @endif

    <div class="field action">
        <x-volt-button class="fluid">@lang('laravolt::auth.login')</x-volt-button>
    </div>
    {!! form()->close() !!}

</x-volt-auth>
