<{if isset($xoops_page) && $xoops_page == "index"}>
    <!-- remove "vert" class for standard horizontal scroll -->
    <div id="sliderCarousel" class="vert carousel slide mb-4" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#sliderCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#sliderCarousel" data-slide-to="1"></li>
            <li data-target="#sliderCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="<{$xoops_imageurl}>images/slides/1-DSCN1071.jpeg" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="slidetext"><{$xoops_sitename}></h2>
                    <p class="slidetext"><{$xoops_slogan}></p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="<{$xoops_imageurl}>images/slides/2-DSCN0919.jpeg" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="slidetext"><{$xoops_sitename}></h2>
                    <p class="slidetext"><{$xoops_slogan}></p>
                    <a class="btn btn-primary" href="#"><{$smarty.const.THEME_LEARNMORE}></a>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="<{$xoops_imageurl}>images/slides/3-DSCN0875.jpeg" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h2 class="slidetext-dark"><{$xoops_sitename}></h2>
                    <p class="slidetext-dark"><b><{$xoops_slogan}></b></p>
                </div>
            </div>
        </div>
        <{* horizontal controls
        <a class="carousel-control-prev" href="#sliderCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only"<{$smarty.const.THEME_CONTROL_PREVIOUS}>/span>
        </a>
        <a class="carousel-control-next" href="#sliderCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only"<{$smarty.const.THEME_CONTROL_NEXT}>/span>
        </a>
        *}>
    </div>
<{/if}>