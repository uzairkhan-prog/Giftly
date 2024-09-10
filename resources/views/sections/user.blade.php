@if($topUsers && count($topUsers) > 0)
<section class="client_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Top Users
            </h2>
        </div>
    </div>
    <div class="container px-0">
        <div id="customCarousel2" class="carousel  carousel-fade" data-ride="carousel">
            <div class="carousel-inner">
                @foreach($topUsers as $key => $user)
                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                    <div class="box">
                        <div class="client_info">
                            <div class="client_name">
                                <h5>
                                    {{ $user->name }}
                                </h5>
                                <h6>
                                    Hi, I'm {{ $user->name }}
                                </h6>
                                @if(!empty($user->getRoleNames()))
                                @foreach($user->getRoleNames() as $v)
                                @if(in_array($v, $roles))
                                <p class="text-xs font-weight-bold mb-0">{{ $v }}</p>
                                @endif
                                @endforeach
                                @endif
                            </div>
                            <i class="fa fa-quote-left" aria-hidden="true"></i>
                        </div>
                        @if(!empty($user->getRoleNames()))
                        @foreach($user->getRoleNames() as $v)
                        @if(in_array($v, $roles))
                        <p>
                            As a {{ $v }}, your contributions are invaluable. You lead the way with expertise and vision, setting standards that others follow. Your role is essential in shaping the future and driving success in our organization.
                        </p>
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="carousel_btn-box">
                <a class="carousel-control-prev" href="#customCarousel2" role="button" data-slide="prev">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#customCarousel2" role="button" data-slide="next">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endif