function slugify(value) {
    return String(value || "")
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
}

function initCategorySlugAutofill() {
    const nameInput = document.getElementById("name");
    const slugInput = document.getElementById("slug");

    if (!nameInput || !slugInput) {
        return;
    }

    let slugTouched = slugInput.value.trim() !== "";

    slugInput.addEventListener("input", () => {
        slugTouched = true;
    });

    nameInput.addEventListener("input", (event) => {
        if (slugTouched && slugInput.value.trim() !== "") {
            return;
        }

        slugInput.value = slugify(event.target.value);
    });
}

function createCategoryUploader(config) {
    return {
        iconPreview: config.iconPreview || null,
        imagePreview: config.imagePreview || null,
        iconFileName: "",
        imageFileName: "",
        iconClientError: "",
        imageClientError: "",

        onIconSelected(event) {
            this.iconClientError = "";
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const extension = (file.name.split(".").pop() || "").toLowerCase();
            const isSvg = extension === "svg" || file.type === "image/svg+xml";

            if (!isSvg) {
                this.iconClientError = "Icon must be an SVG file.";
                this.clearIcon();
                return;
            }

            this.iconFileName = file.name;
            this.iconPreview = URL.createObjectURL(file);
        },

        onImageSelected(event) {
            this.imageClientError = "";
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const extension = (file.name.split(".").pop() || "").toLowerCase();
            const allowedExtensions = ["jpg", "jpeg", "png", "webp", "avif", "gif"];
            const isSvg = extension === "svg" || file.type === "image/svg+xml";

            if (isSvg || !allowedExtensions.includes(extension)) {
                this.imageClientError = "Image must be JPG, PNG, WEBP, AVIF, or GIF.";
                this.clearImage();
                return;
            }

            this.imageFileName = file.name;
            this.imagePreview = URL.createObjectURL(file);
        },

        clearIcon() {
            this.iconClientError = "";
            this.iconFileName = "";
            this.iconPreview = config.iconPreview || null;
            if (this.$refs.iconInput) {
                this.$refs.iconInput.value = "";
            }
        },

        clearImage() {
            this.imageClientError = "";
            this.imageFileName = "";
            this.imagePreview = config.imagePreview || null;
            if (this.$refs.imageInput) {
                this.$refs.imageInput.value = "";
            }
        },
    };
}

window.categoryUploader = createCategoryUploader;

document.addEventListener("DOMContentLoaded", initCategorySlugAutofill);
