<style>
    @media (max-width: 768px) {
      .single-post {
        flex-direction: column;
        align-items: center;
      }
    
      .slider-container,
      .card {
        width: 100%;
      }
    }
    .swiper-lazy,
    .swiper-lazy-loaded {
        transform: none !important;
        transition: none !important;
    }

    .slider-container {
      position: relative;
      max-width: 100%;
    }
    
    .main-slider img {
      width: 100%;
      display: block;
      border: 1px solid hsla(0, 0%, 100%, 0.2);
    }
    
    .thumb-slider {
      position: absolute;
      bottom: 15px;
      left: 15px;
      width: 140px;
      z-index: 10;
      overflow: hidden;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid hsla(0, 0%, 100%, 0.2);
    }
    
    .thumb-slider .swiper-slide {
      opacity: 0.6;
      cursor: pointer;
      transition: opacity 0.3s;
    }
    .swiper-button-prev,
    .swiper-button-next {
      color: #fff;
      opacity: 0;
      transition: opacity 0.3s;
    }
    
    .slider-container:hover .swiper-button-prev,
    .slider-container:hover .swiper-button-next {
      opacity: 1;
    }
    .swiper-pagination {
      bottom: 10px !important;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      position: absolute;
      width: 100%;
      z-index: 10;
    }
    
    .swiper-pagination-bullet {
      width: 14px !important;
      height: 14px !important;
      border-radius: 50% !important;
      background-color: transparent !important;
      border: 2px solid #fff !important;
      opacity: 1;
    }
    
    .swiper-pagination-bullet-active {
      width: 14px !important;
      height: 14px !important;
      border-radius: 50% !important;
      background-color: #d1d1d1 !important;
      border-color: #fff !important;
      transform: 0.5 !important;
    }
    
    .swiper-slider-stop {
      border-left: 2px solid rgba(255, 255, 255, 0.75) !important;
      border-right: 2px solid rgba(255, 255, 255, 0.75) !important;
      width: 4px;
      height: 12px;
      margin-left: 5px;
    }
    
    .slider-toggle-play {
      position: absolute;
      top: 5px;
      right: 5px;
      z-index: 20;
      color: #fff;
      border: none;
      border-radius: 4px;
      padding: 4px 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.2s;
    }
    
    .slider-toggle-play svg {
      filter: drop-shadow(0 0 2px rgba(0, 0, 0, 0.3));
    }
    
    .card {
      font-family: "Steppe", sans-serif;
      max-width: 240px;
      height: 495.88px;
      width: 100%;
      border: 1px solid hsla(0, 0%, 100%, 0.2);
      padding: 9px;
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.5);
      box-sizing: border-box;
    }
    
    .logo-container {
      padding: 1.5rem 1rem;
      display: flex;
      justify-content: center;
      margin-bottom: 0.5rem;
    }
    
    .logo-container img {
      height: 24px;
      width: auto;
      display: block;
    }
    
    .info-group {
      padding: 1rem;
      color: #d1d5db;
      font-size: 0.85rem;
      margin-bottom: 0.5rem;
      border: 1px solid hsla(0, 0%, 100%, 0.2);
    }
    
    .info-group:last-child {
      margin-bottom: 0;
    }
    
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    
    .info-row:last-child {
      margin-bottom: 0;
    }
    
    .info-label {
      color: #9ca3af;
      margin: 0 0 0.25rem 0;
    }
    
    .info-value {
      font-weight: 800;
      color: #fff;
      margin: 0;
    }
    
    .text-right {
      text-align: right;
    }
    
    @media (max-width: 400px) {
      .card {
        max-width: 100%;
      }
    }
</style>
                        
  <?php
    $imgur_ids = get_field('imgur_ids');
    if ($imgur_ids):
    $ids = array_map('trim', explode(',', $imgur_ids));
    $is_single_image = count($ids) === 1;
    ?>
                        
<div class="slider-container">
    <div class="swiper main-slider">
        <div class="swiper-wrapper">
            <?php foreach ($ids as $id): ?>
                <div class="swiper-slide">
                    <img src="https://i.imgur.com/<?php echo esc_attr($id); ?>.jpg" alt="Download <?php echo the_title(); ?> for free" />
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$is_single_image): ?>
        <button class="slider-toggle-play" title="Pause">⏸</button>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const mainSwiper = new Swiper('.main-slider', {
    loop: false,
    spaceBetween: 10,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
    },
    <?php if (!$is_single_image): ?>
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    <?php endif; ?>
  });

  const playToggleBtn = document.querySelector('.slider-toggle-play');
  if (playToggleBtn) {
    let isPlaying = true;

    const playSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`;
const pauseSVG = `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>`;

playToggleBtn.innerHTML = pauseSVG;

playToggleBtn.addEventListener('click', () => {
  if (isPlaying) {
    mainSwiper.autoplay.stop();
    playToggleBtn.innerHTML = playSVG;
  } else {
    mainSwiper.autoplay.start();
    playToggleBtn.innerHTML = pauseSVG;
  }
  isPlaying = !isPlaying;
});
  }
});
</script>
<?php endif; ?>

