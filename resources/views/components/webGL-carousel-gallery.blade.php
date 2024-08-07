<!-- WebGL Carousel Gallery -->
<div class="anita-gl-container-wrap anita-albums-listing anita-gl-carousel-gallery-wrap anita-scrollEW">
    <div class="anita-gl-container anita-gl-carousel-gallery" data-prev-label="Previous Work" data-next-label="Next Work">

        @foreach($data->components as $component)
            @if($component->type === 'paragraph--webgl_carousel_gallery')
                @foreach($component->slides as $index => $slide)
                    @php
                    $index = $index+ 1;
                       if($slide->media->type === 'video'){
                           $dataSize = $slide->resolution;
                       }
                       else {
                           $width = $slide->media->width;
                           $height = $slide->media->height;
                           $dataSize = "$width"."x"."$height";
                       }
                    @endphp

                    <!-- Gallery Item -->
                    <div class="anita-gl-gallery-item {{$slide->media->type === 'video' ? 'is-video' : 'is-image'}}" data-src="{{$slide->media->url}}" data-size={{$dataSize}}>
                        <div class="anita-gl-gallery-item__content" data-aos="fade-up">
                            <span class="anita-meta anita-gl-gallery__meta">{{$slide->album->name}}</span>
                            <h2 class="anita-gl-gallery__caption">
                                <sup>{{ $index < 10 ? "0$index":'' . ($index)}}.</sup>{{$slide->title}}
                            </h2>
                            <span class="anita-gl-gallery__explore">Explore</span>
                            <a href="{{$slide->album->url}}" class="anita-album-link"></a>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach

    </div><!-- .anita-gl-carousel-gallery -->
</div><!-- .anita-gl-carousel-gallery-wrap -->
