<?php

add_shortcode('custom_audio_button', function ($atts) {
    $atts = shortcode_atts([
        'meta_key' => '',
        'heading' => '',
    ], $atts);

    if (empty($atts['meta_key'])) return '';

    $home = get_option('home-page-slug');
    if (!$home) return '';

    $audio_url = $home[$atts['meta_key']];
    if (!$audio_url) return '';

    // Generate a unique ID for each player
    $unique_id = uniqid('custom-audio-');

    ob_start();
?>
    <style>
        .audio-button-wrapper {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .audio-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            padding: 0.75rem;
            border-radius: 50%;
            transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
            position: relative;
            background-color: var(--e-global-color-primary);
            color: var(--e-global-color-f613620) !important;
            transform-origin: center;
        }

        .audio-button:hover,
        .audio-button.playing {
            transform: scale(1.1);
            color: var(--e-global-color-primary) !important;
            background-color: var(--e-global-color-f613620);
        }

        .audio-button-wrapper p {
            font-size: 0.6rem;
            margin-top: 0.5rem;
            margin-bottom: 0;
            text-align: center;
            font-weight: 600;
            color: var(--e-global-color-f613620) !important;
        }

        .audio-button-audio {
            display: flex;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            padding: 30%;
            align-items: center;
            justify-content: center;
        }

        .audio-button.playing .audio-button-audio {
            display: none;
        }

        .audio-button-pause {
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            padding: 30%;
            align-items: center;
            justify-content: center;
        }

        .audio-button.playing .audio-button-pause {
            display: flex;
        }

        .audio-button-progress {
            display: flex;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            align-items: center;
            justify-content: center;
            transform: rotateZ(-90deg) rotatex(180deg);
            pointer-events: none;
        }
    </style>

    <div class="audio-button-wrapper">
        <a class="audio-button" data-audio-id="<?php echo esc_attr($unique_id); ?>" aria-label="Play Audio" aria-hidden="">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="54" viewBox="0 0 34 54" fill="none" class="audio-button-audio">
                <path d="M17.2608 0.148987C14.6114 0.151105 12.0712 1.20451 10.1978 3.07792C8.32439 4.95132 7.27096 7.49159 7.26884 10.141V28.211C7.26514 29.5242 7.52112 30.8252 8.02195 32.0391C8.52279 33.2531 9.25859 34.3561 10.1872 35.2846C11.1158 36.2132 12.2188 36.9491 13.4327 37.4499C14.6467 37.9508 15.9476 38.2067 17.2608 38.203C19.9185 38.1916 22.463 37.1264 24.3363 35.2412C26.2095 33.356 27.2584 30.8046 27.2528 28.147V10.147C27.2565 8.83378 27.0006 7.5328 26.4998 6.31884C25.999 5.10489 25.2631 4.00191 24.3345 3.07333C23.4059 2.14476 22.3029 1.40889 21.089 0.908055C19.875 0.407219 18.574 0.151291 17.2608 0.154986V0.148987Z" fill="currentColor" />
                <path d="M1.332 19.713C1.00976 19.713 0.700721 19.841 0.472865 20.0689C0.245008 20.2967 0.116969 20.6058 0.116969 20.928V28.376C0.109602 32.6278 1.71588 36.7241 4.61135 39.8376C7.50683 42.9512 11.4758 44.8501 15.717 45.151V50.851H11.317C10.9947 50.851 10.6857 50.979 10.4578 51.2069C10.23 51.4347 10.102 51.7438 10.102 52.066C10.102 52.3882 10.23 52.6973 10.4578 52.9252C10.6857 53.153 10.9947 53.281 11.317 53.281H23.19C23.5123 53.281 23.8213 53.153 24.0492 52.9252C24.277 52.6973 24.405 52.3882 24.405 52.066C24.405 51.7438 24.277 51.4347 24.0492 51.2069C23.8213 50.979 23.5123 50.851 23.19 50.851H18.148V45.151C22.3891 44.8501 26.3582 42.9512 29.2537 39.8376C32.1492 36.7241 33.7554 32.6278 33.748 28.376V20.928C33.748 20.7685 33.7166 20.6105 33.6555 20.4631C33.5945 20.3156 33.5049 20.1817 33.3921 20.0689C33.2793 19.9561 33.1454 19.8666 32.998 19.8055C32.8505 19.7444 32.6925 19.713 32.533 19.713C32.3734 19.713 32.2154 19.7444 32.068 19.8055C31.9206 19.8666 31.7867 19.9561 31.6739 20.0689C31.561 20.1817 31.4715 20.3156 31.4105 20.4631C31.3494 20.6105 31.318 20.7685 31.318 20.928V28.376C31.318 32.1921 29.8021 35.8518 27.1037 38.5502C24.4054 41.2486 20.7455 42.7645 16.9295 42.7645C13.1134 42.7645 9.45364 41.2486 6.75527 38.5502C4.05691 35.8518 2.54098 32.1921 2.54098 28.376V20.928C2.54098 20.6068 2.41384 20.2987 2.18728 20.071C1.96072 19.8433 1.65319 19.7146 1.332 19.713Z" fill="currentColor" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="audio-button-pause">
                <rect x="6" y="4" width="4" height="16"></rect>
                <rect x="14" y="4" width="4" height="16"></rect>
            </svg>
            <svg width="100%" height="100%" viewBox="0 0 60 60" class="audio-button-progress">
                <circle class="audio-button-progress-circle"
                    stroke="currentColor"
                    stroke-width="2"
                    fill="transparent"
                    r="28" cx="30" cy="30" />
            </svg>
        </a>
        <p><?php echo esc_html($atts['heading']); ?></p>
        <audio id="<?php echo esc_attr($unique_id); ?>" src="<?php echo esc_url($audio_url); ?>" preload="auto"></audio>
    </div>
<?php
    return ob_get_clean();
});
