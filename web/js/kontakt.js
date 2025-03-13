$(document).ready(function() {
    var parallax = -0.3;
    var $parallaxImages = $(".parallax-image");
    var original_offsets = [];
    $parallaxImages.each(function(i, el) {
        original_offsets.push($(el).offset().top);
    });

    $(window).scroll(function() {
        var dy = $(this).scrollTop();
        $parallaxImages.each(function(i, el) {
            var original_offset = original_offsets[i];
            $(el).css("top", (original_offset + dy * parallax) + "px");
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.galerie');

    if (!gallery) {
        console.error('Gallery element not found.');
        return;
    }

    let isDown = false;
    let startX;
    let scrollLeft;

    gallery.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - gallery.offsetLeft;
        scrollLeft = gallery.scrollLeft;
        gallery.style.cursor = 'grabbing';
    });

    gallery.addEventListener('mouseleave', () => {
        isDown = false;
        gallery.style.cursor = 'grab';
    });

    gallery.addEventListener('mouseup', () => {
        isDown = false;
        gallery.style.cursor = 'grab';
    });

    gallery.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - gallery.offsetLeft;
        const walk = (x - startX); // Removed the multiplication for direct control
        gallery.scrollLeft = scrollLeft - walk;
    });
});