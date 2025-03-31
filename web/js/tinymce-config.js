let script = document.createElement('script');
script.src = "https://cdn.tiny.cloud/1/4zya77m9f7cxct4wa90s8vckad17auk31vflx884mx6xu1a3/tinymce/7/tinymce.min.js";
script.referrerPolicy = "origin";

// Zaznamenat selhání načtení API klíče
script.onerror = function() {
    console.error("Nepodařilo se načíst TinyMCE z cloudu - přepínám na lokální CDN...");
    // Pokud selže načtení, použij CDN verzi bez API klíče
    const fallbackScript = document.createElement('script');
    fallbackScript.src = "https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.6.2/tinymce.min.js";
    fallbackScript.onload = initTinyMCE;
    document.head.appendChild(fallbackScript);
};

script.onload = initTinyMCE;

function initTinyMCE() {
    tinymce.init({
        selector: '#editor',
        plugins: 'image link lists code',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link | code',
        height: 500,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: '/admin/upload-image',
        document_base_url: window.location.origin, // Explicitně nastavíme base URL

        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                fetch('/admin/upload-image', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result && result.location) {
                        resolve(result.location);
                    } else {
                        reject('Chybí "location" v odpovědi serveru.');
                    }
                })
                .catch(error => reject(`Chyba při uploadu: ${error.message}`));
            });
        },
    });
}

document.head.appendChild(script);
