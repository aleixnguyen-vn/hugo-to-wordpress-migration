<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
<style>
                            @media (max-width: 768px) {
                            .single-post {
                                flex-direction: column;
                                align-items: center;
                            }

                                .swiper-lazy,
    .swiper-lazy-loaded {
        transform: none !important;
        transition: none !important;
    }

                            .slider-container,
                            .card {
                                width: 100%;
                            }
                            }
                            .slider-container {
                                position: relative;
                                max-width: 880px;
                            }
                            
                            .main-slider img {
                                width: 100%;
                                display: block;
                                border: 1px solid hsla(0, 0%, 100%, .2);
                            }
                            
                            .thumb-slider {
                                position: absolute;
                                bottom: 15px;
                                left: 15px;
                                width: 140px;
                                z-index: 10;
                                overflow: hidden;
                                box-shadow: 0 0 8px rgba(0,0,0,0.1);
                                background: rgba(0,0,0,0.3);
                                border: 1px solid hsla(0, 0%, 100%, .2);
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
                        border-left: 2px solid rgba(255,255,255,0.75) !important;
                        border-right: 2px solid rgba(255,255,255,0.75) !important;
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
                                font-family: 'Steppe', sans-serif;
                                max-width: 240px;
                                height: 495.88px;
                                width: 100%;
                                border: 1px solid hsla(0, 0%, 100%, .2);
                                padding: 9px;
                                box-shadow: 0 10px 15px rgba(0,0,0,0.5);
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
                                border: 1px solid hsla(0, 0%, 100%, .2);
                            
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
$tracklogo = get_field('track_logo');
$tracklogo_slug = urlize($tracklogo);
$logo_imgur   = get_field('track_imgur');
$logoimgur_slug   = urlize($logo_imgur);

if ($logo_imgur) {
    $img_src = 'https://i.imgur.com/' . $logo_imgur . '.png';
} else {
    $img_src = content_url('/uploads/assets/logo/' . $tracklogo_slug . '.png');
}

?>
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
                            <div class="card flex flex-col justify-around">
                                <div class="logo-container thumbnail">
                                    <img src="<?php echo esc_url($img_src); ?>" style="height: 70px" title="<?php the_field('brand'); ?>" alt="<?php the_field('mod_name'); ?>">
                                </div>

                                <div class="info-group thumbnail">
                                    <div class="info-row">
                                        <div>
                                            <p class="info-label">Country</p>
                                            <p class="info-value">
                                                <?php the_field('location'); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="info-label">Year</p>
                                            <p class="info-value">
                                                <?php the_field('year'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div>
                                            <p class="info-label">FIA Grade</p>
                                            <p class="info-value">
                                                <?php the_field('fia_rank'); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="info-label">Type</p>
                                            <p class="info-value">
                                                <?php the_field('track_type'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-group thumbnail">
                                    <div class="info-row">
                                        <div>
                                            <p class="info-label">Length</p>
                                            <p class="info-value">
                                                <?php the_field('length'); ?> km
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="info-label">Width</p>
                                            <p class="info-value">
                                                <?php the_field('width'); ?> m
                                            </p>
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div>
                                            <p class="info-label">Pitboxes</p>
                                            <p class="info-value">
                                                <?php the_field('pitbox'); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="info-label">Surface</p>
                                            <p class="info-value">
                                                <?php the_field('surface'); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="info-row" style="padding-bottom: 0px;">
                                        <div>
                                                </p>
                                        </div>
                                    </div>
                                </div>
                            </div>