<{if $xoops_page == "index"}>
    <div id="sliderCarousel" class="carousel slide mb-4"  data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#sliderCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#sliderCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#sliderCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
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
        <button class="carousel-control-prev" type="button" data-bs-target="#sliderCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><{$smarty.const.THEME_CONTROL_PREVIOUS}></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#sliderCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><{$smarty.const.THEME_CONTROL_NEXT}></span>
        </button>
    </div>
<{/if}>