@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <nav class="mb-4 block w-full">
        <ol class="flex flex-wrap items-center text-gray-700 text-sm mb-2">
            @foreach($breadcrumbs as $item)
                <li class="flex items-center">
                    @unless($loop->first)
                        {{--padding eje x--}}
                        <span class="px-2 text-gray-400">/</span>
                    @endunless

                    @isset($item['href'])
                        {{--si existe href muestralo--}}
                        <a href="{{$item['href']}}" class="text-blue-600 hover:text-blue-800 hover:underline transition">{{$item['name']}}</a>
                    @else
                        <span class="text-gray-900 font-medium">{{$item['name']}}</span>
                    @endisset
                </li>
            @endforeach
        </ol>
        @if(count($breadcrumbs) > 1)
            <h1 class="font-bold text-2xl text-gray-900">
                {{end($breadcrumbs)['name']}}
            </h1>
        @endif
    </nav>
@endif
