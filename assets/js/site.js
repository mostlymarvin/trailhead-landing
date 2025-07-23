document.addEventListener("DOMContentLoaded", function () {
  const stickyHeader = document.querySelector(".is-position-sticky");
  const coverBlock = document.querySelector(".wp-block-cover:not(.f-off)");

  //make header transparent til scroll past cover block
  if (stickyHeader && coverBlock) {
    const coverBlockHeight = coverBlock.offsetHeight;
    const stickyHeight = stickyHeader.offsetHeight;

    coverBlock.style.marginTop = `-${stickyHeight}px`;

    const nextElement = coverBlock.nextElementSibling;
    if (nextElement) {
      nextElement.style.marginTop = `${stickyHeight}px`;
    }

    window.addEventListener("scroll", function () {
      if (window.scrollY > coverBlockHeight) {
        stickyHeader.classList.add("scrolled");
      } else {
        stickyHeader.classList.remove("scrolled");
      }
    });
  }

  //scale the big F on scroll
  const bigF = document.querySelector("figure.big-f");

  if (bigF && window.innerWidth > 600) { // Only apply animation on screens larger than 600px
    bigF.style.transition = "transform 0.2s ease-out";
  
    const initialTopPercentage = parseFloat(
      getComputedStyle(bigF).getPropertyValue('--big-f-top')
    );
  
    let lastScrollY = 0;
    let ticking = false;
  
    const handleScroll = () => {
      lastScrollY = window.scrollY;
  
      if (!ticking) {
        window.requestAnimationFrame(() => {
          const scaleFactor = 10; // Scaling factor for larger screens
          const scale = 1 + lastScrollY / scaleFactor;
          const translateY = lastScrollY > 0 ? 30 : 0;
  
          bigF.style.transform = `translate(-50%, calc(-${initialTopPercentage}% + ${translateY}px)) scale(${scale})`;
          ticking = false;
        });
  
        ticking = true;
      }
    };
  
    window.addEventListener("scroll", handleScroll);
  }


// Adds animation to cover blocks, in use on the homepage
if (window.matchMedia("(min-width: 601px)").matches) {
  var featurePromo = document.querySelectorAll('.feature-promo');

  featurePromo.forEach(function(promo) {
      promo.addEventListener('mouseenter', function() {
          var h3 = this.querySelector('.wp-block-cover__inner-container h3');
          var p = this.querySelector('.wp-block-cover__inner-container p');
          [p, h3].forEach(function(el) {
              el.style.opacity = '1';
              el.style.transform = 'translateY(0)';
          });
      });

      promo.addEventListener('mouseleave', function() {
          var h3 = this.querySelector('.wp-block-cover__inner-container h3');
          var p = this.querySelector('.wp-block-cover__inner-container p');
          [p, h3].forEach(function(el) {
              el.style.opacity = '0';
              el.style.transform = 'translateY(-20px)';
          });
      });
  });
}

});