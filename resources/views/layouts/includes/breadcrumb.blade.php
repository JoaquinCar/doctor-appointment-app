@if(count($breadcrumbs))
    <nav class="mb-2 block">
        <ol class="flex flex-wrap text-slate-700 text-sm">
            @foreach($breadcrumbs as $item)


                <li class="flex items-center">
                    @unless($loop->first)
                        {{--padding eje x--}}
                        <span class="px-2 text-gray-400">/</span>
                    @endunless

                    @isset($item['href'])
                        {{--si existe href muestralo--}}
                        <a href="{{$item['href']}}" class="opacity-60 hover:opacity-100 transition">{{$item['name']}}</a>
                    @else
                        {{$item['name']}}
                    @endisset
                </li>
            @endforeach
        </ol>
        @if(count($breadcrumbs)>1)
            <h6 class="font-bold mt-2">
                {{end($breadcrumbs)['name']}}
            </h6>

        @endif
    </nav>
@endif
