<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="<{$xoops_imageurl}>images/slider1.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5><{$smarty.const.THEME_SLIDE_LABEL1}></h5>
                <p><{$smarty.const.THEME_SLIDE_SUBLABEL1}></p>

                <p><a href="<{$xoops_url}>/modules/pm" class="btn btn-large btn-primary"><{$smarty.const.THEME_READMORE}></a></p>

            </div>
        </div>
        <div class="carousel-item">
            <img src="<{$xoops_imageurl}>images/slider2.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5><{$smarty.const.THEME_SLIDE_LABEL2}></h5>
                <p><{$smarty.const.THEME_SLIDE_SUBLABEL2}></p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<{$xoops_imageurl}>images/slider3.jpg" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5><{$smarty.const.THEME_SLIDE_LABEL3}></h5>
                <p><{$smarty.const.THEME_SLIDE_SUBLABEL3}></p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
