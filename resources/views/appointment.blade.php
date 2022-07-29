<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        <script src="{{ asset('js/app.js') }}"></script>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
                @auth
                    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                        <form action="{{ route('logout') }}" method="POST">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-block">
                                {{__("Logout")}}
                            </button>
                        </form>
                    </div>
                @endauth
                <div id="app-calendar" class="vanilla-calendar"></div>

{{--                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">--}}
{{--                        v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
    <script>
        const calendar = new VanillaCalendar('#app-calendar', {
            date: {
                min: '{{ $minDate }}',
                max: '{{ $maxDate }}',
                // today: new Date('2022-01-07'),
            },
            settings: {
                lang: 'zh',
                iso8601: false,
                selection: {
                    day: '{{ $minDate == $maxDate ? "false" : "single" }}'
                }
            },
            actions: {
                clickDay(e) {
                    alert(e.target.dataset.calendarDay);
                },
            }
        });
        calendar.init();
    </script>
    </body>
</html>
