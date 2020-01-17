<{if $xoops_page == "index"}>
    <div id="sliderCarousel" class="carousel slide mb-4" data-ride="carousel">
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
        <a class="carousel-control-prev" href="#sliderCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#sliderCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
<{/if}>