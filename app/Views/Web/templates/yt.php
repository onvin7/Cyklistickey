<script>
    document.addEventListener('DOMContentLoaded', fetchLatestVideo);

    function fetchLatestVideo() {
        const API_KEY = 'AIzaSyAAxWhHYrHR4urKjI7Eb_PgI4ZRI2jr6gU'; // Replace with your actual API Key
        const CHANNEL_ID = 'UCCZaqz8jUcJNX3QCI1lxTNg'; // Replace with your actual Channel ID
        const url = `https://www.googleapis.com/youtube/v3/search?key=${API_KEY}&channelId=${CHANNEL_ID}&part=snippet,id&order=date&maxResults=1&type=video`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.items && data.items.length > 0) {
                    const latestVideoId = data.items[0].id.videoId;
                    displayVideo(latestVideoId);
                } else {
                    console.error('No videos found or check the data structure:', data);
                    document.querySelector('.iframe-container iframe').src = '';
                    document.querySelector('.iframe-container iframe').hidden = true; // Hide the iframe if no video is found
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                document.querySelector('.iframe-container iframe').src = '';
                document.querySelector('.iframe-container iframe').hidden = true;
            });
    }

    function displayVideo(videoId) {
        const iframe = document.querySelector('.iframe-container iframe');
        iframe.src = `https://www.youtube.com/embed/${videoId}?mute=1`;
        iframe.allow = 'accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.referrerpolicy = 'strict-origin-when-cross-origin';
        iframe.allowfullscreen = true;
    }
</script>

<div class="iframe-container">
    <div class="text-mobile">
        <h1>Nejnovější youtube video</h1>
    </div>
    <iframe width="100%" height="450px" src="" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen=""></iframe>
    <div class="text">
        <h1>Pusť si naše nejnovější video na youtube kanálu!</h1>
        <div class="cist-clanek">
            <a href="https://www.youtube.com/@Cyklistickey" target="_blank">ZOBRAZIT YOUTUBE KANÁL <i class="fa-solid fa-angle-right"></i></a>
        </div>
    </div>
</div>