function createCaseStudyUploader(config) {
    return {
        coverPreview: config.coverPreview || null,
        coverFileName: "",
        coverClientError: "",

        onCoverSelected(event) {
            this.coverClientError = "";
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const extension = (file.name.split(".").pop() || "").toLowerCase();
            const allowed = ["jpg", "jpeg", "png", "webp", "avif", "gif"];

            if (!allowed.includes(extension)) {
                this.coverClientError = "Cover image must be JPG, PNG, WEBP, AVIF, or GIF.";
                this.clearCover();
                return;
            }

            this.coverFileName = file.name;
            this.coverPreview = URL.createObjectURL(file);
        },

        clearCover() {
            this.coverClientError = "";
            this.coverFileName = "";
            this.coverPreview = config.coverPreview || null;
            if (this.$refs.coverInput) {
                this.$refs.coverInput.value = "";
            }
        },
    };
}

window.caseStudyUploader = createCaseStudyUploader;
