const FORM_SELECTOR = "#featured-collaboration-form";

function updateAssetSections(assetTypeInput, imageSection, videoSection, thumbnailSection) {
    const value = assetTypeInput?.value || "";
    const isImage = value === "image";
    const isVideo = value === "video";

    imageSection?.classList.toggle("hidden", !isImage);
    videoSection?.classList.toggle("hidden", !isVideo);
    thumbnailSection?.classList.toggle("hidden", !isVideo);

    const thumbnailInput = thumbnailSection?.querySelector("input[type='file']");
    if (thumbnailInput) {
        thumbnailInput.disabled = !isVideo;
    }
}

function bindFileName(inputSelector, labelSelector) {
    const input = document.querySelector(inputSelector);
    const label = document.querySelector(labelSelector);

    if (!input || !label) return;

    input.addEventListener("change", (event) => {
        const file = event.target.files?.[0];
        if (file) {
            label.textContent = file.name;
        }
    });
}

function initFeaturedCollaborationForm() {
    const form = document.querySelector(FORM_SELECTOR);
    if (!form) return;

    const assetTypeInput = form.querySelector("#asset_type");
    const imageSection = form.querySelector("#imageSection");
    const videoSection = form.querySelector("#videoSection");
    const thumbnailSection = form.querySelector("#thumbnailSection");

    updateAssetSections(assetTypeInput, imageSection, videoSection, thumbnailSection);
    assetTypeInput?.addEventListener("change", () => {
        updateAssetSections(assetTypeInput, imageSection, videoSection, thumbnailSection);
    });

    bindFileName("#image_path", "#image-file-label");
    bindFileName("#video_path", "#video-file-label");
    bindFileName("#thumbnail_path", "#thumbnail-file-label");
}

document.addEventListener("DOMContentLoaded", initFeaturedCollaborationForm);
