<?php

$jsonFilePath = __DIR__ . '/../../../../web/flash.json';

$jsonData = file_get_contents($jsonFilePath);

$data = json_decode($jsonData, true);



$newsTitles = $data['news']['titles'] ?? [];

$techTitles = $data['tech']['titles'] ?? [];



$maxCount = max(count($newsTitles), count($techTitles));

$interleavedTitles = [];



for ($i = 0; $i < $maxCount; $i++) {

    if (isset($newsTitles[$i])) {

        $interleavedTitles[] = $newsTitles[$i]; 

    }

    if (isset($techTitles[$i])) {

        $interleavedTitles[] = $techTitles[$i];

    }

}

?>

<style>

    .marquees-wrapper {

        position: relative; /* Keep it relative to allow normal document flow, adjust if necessary */

        height: max-content;

        width: 100%;

        overflow-x: hidden; /* Hide anything outside the width */

        z-index: 9999999999999;

        top: 0;

    }

    .marquee {

        --gap: 2em;

        display: flex;

        gap: var(--gap);

        background-color: rgba(255, 255, 255, 1);

        backdrop-filter: blur(20px);

        -webkit-backdrop-filter: blur(20px);

        overflow: hidden;

        user-select: none;

        color: #00000f;

        padding-top: 10px;

        padding-bottom: 10px;

        width: 100%; /* Ensure it spans the full width */

    }



    .marquee ul {

        margin-bottom: 0;

    }



    .marquee__content {

        flex-shrink: 0;

        display: flex;

        justify-content: space-around;

        min-width: 200%; /* Ensure it's twice the viewport to cover entire animation distance */

        gap: var(--gap);

    }



    .marquee-1 .scroll, .marquee-2 .scroll {

        animation: scroll 200s linear infinite;

    }



    @keyframes scroll {

        from { transform: translateX(0); }

        to { transform: translateX(-100%); }

    }



    .marquee__content li {

        list-style: none;

        line-height: normal;

        text-transform: uppercase;

        font-size: 1.1rem;

        letter-spacing: 1px;

    }



    .marquee__content li i {

        color: #f1008d;

    }



    @media screen and (max-width: 850px) {

        .marquee__content li {

            font-size: 0.8rem;

        }

    }

</style>



<section class="marquees-wrapper">

    <div class="marquee marquee-1">

        <ul class="marquee__content scroll">

            <?php

            foreach ($interleavedTitles as $entry) {

                if (isset($entry['title'])) {

                    $title = htmlentities($entry['title'], ENT_QUOTES, 'UTF-8');

                    echo "<li>{$title}</li><li><i class='fa-solid fa-person-biking'></i></li>";

                }

            }

            if (empty($interleavedTitles)) {

                echo "<li>No news or tech items available.</li>";

            }

            ?>

        </ul>

        <ul class="marquee__content scroll" aria-hidden="true">

            <?php

            foreach ($interleavedTitles as $entry) {

                if (isset($entry['title'])) {

                    $title = htmlentities($entry['title'], ENT_QUOTES, 'UTF-8');

                    echo "<li>{$title}</li><li><i class='fa-solid fa-person-biking'></i></li>";

                }

            }

            if (empty($interleavedTitles)) {

                echo "<li>No news or tech items available.</li>";

            }

            ?>

        </ul>

    </div>

</section>

