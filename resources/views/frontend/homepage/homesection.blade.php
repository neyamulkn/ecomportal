@foreach($sections as $section)
    @include('frontend.homepage.'.$section->type)
@endforeach 