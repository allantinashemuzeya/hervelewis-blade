@extends('layouts.Home')
@section('content')

    <!-- Page Container -->
    <div class="anita-container">

        <!-- Page Title -->
        <section class="anita-section">
            <div class="anita-album-title">
                <div class="anita-albums-back-wrap" data-aos="fade-up">
                    <a href="/home" class="anita-albums-back">Back to Listing</a>
                </div>
                <h1 class="anita-page-title" data-aos="fade-up" data-aos-delay="50">{{$data->name}}</h1>
                <div class="anita-post-meta anita-meta" data-aos="fade-up" data-aos-delay="100">
                    <span>{{$data->category}}</span>
                    <span>{{count($data->media)}} Photos</span>
                </div>
            </div><!-- .anita-album-title -->
            <div class="anita-page-intro anita-offset-left--33 anita-offset--tablet-left--25" data-aos="fade-up" data-aos-delay="150">
                <p>Autumn is a perfect time for a romantic love story photo session. When trees are covered with gold and fallen leaves are all around, the weather is cool but such hot feelings can warm all around.</p>
            </div>
        </section>

        <!-- Page Content -->
        <section class="anita-section" data-aos="fade-up">
            <div class="anita-masonry anita-grid-gallery anita-grid-2cols anita-zoom-hover anita-item-zoom-hover anita-item-fade-hover">

                @foreach($data->media as $index => $media)
                    @if(str_contains($media->type, 'image'))
                        <!-- Gallery Item -->
                        <div class="anita-grid-gallery-item">
                            <div class="anita-grid-item__inner">
                                <div class="anita-grid-item__image">
                                    <img src="{{$media->url}}" class="anita-lazy" data-src="{{$media->url}}" alt="{{$media->meta['alt']}}" width="{{$media->meta['width']}}" height="{{$media->meta['height']}}">
                                </div>
                                <a href="{{$media->url}}" class="anita-lightbox-link" data-size="{{$media->meta['width']}}x{{$media->meta['height']}}"></a>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div><!-- .anita-works-grid -->
        </section>

        <!-- Next Album Link -->
        <section class="anita-section">
            <div class="anita-next-album-wrap">
                <div class="anita-next-album-title">
                    <h4 data-aos="fade-up" data-aos-offset="20">
                        Next Album
                    </h4>
                    <a href="{{$data->nextAlbum['id']}}" class="anita-underline anita-caption" data-aos="fade-up" data-aos-delay="50" data-aos-offset="20">
                        {{$data->nextAlbum['attributes']['title']}}
                    </a>
                    <div class="anita-page-background" data-src="{{$data->nextAlbum['attributes']['cover']->url}}"></div>
                </div>
            </div><!-- .anita-next-album-wrap -->
        </section>
    </div><!-- .anita-container -->

@endsection
