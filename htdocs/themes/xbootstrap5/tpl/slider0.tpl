<{if $xoops_page == "index"}>
<div id="myCarousel" class="carousel slide slideshow" data-bs-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li class="active" data-bs-slide-to="0" data-bs-target="#myCarousel"></li>
        <li data-bs-slide-to="1" data-bs-target="#myCarousel" class=""></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active"><img class="d-block w-100" alt="XOOPS" src="<{$xoops_imageurl}>images/slider1.jpg">

            <div class="carousel-caption hidden-xs">
                <h1>Lorem ipsum dolor sit amet</h1>

                <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id
                    nibh ultricies vehicula ut id elit.</p>

                <p><a href="javascript:;" class="btn btn-large btn-primary"><{$smarty.const.THEME_READMORE}></a></p>
            </div>
        </div>
        <div class="carousel-item"><img class="d-block w-100" alt="XOOPS" src="<{$xoops_imageurl}>images/slider2.jpg">

            <div class="carousel-caption hidden-xs">
                <h1>Lorem ipsum dolor sit amet</h1>

                <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id
                    nibh ultricies vehicula ut id elit.</p>

                <p><a href="javascript:;" class="btn btn-large btn-primary"><{$smarty.const.THEME_READMORE}></a></p>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev carousel-control" href="#myCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next carousel-control" href="#myCarousel" role="button" data-bs-slide="next" >
        <span class="carousel-control-next-icon"></span>
        <span class="sr-only">Next</span>
    </a>
</div><!-- .carousel -->
<{/if}>
