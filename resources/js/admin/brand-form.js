function createBrandUploader(config) {
    return {
        profilePreview: config.profilePreview || null,
        coverPreview: config.coverPreview || null,
        profileFileName: "",
        coverFileName: "",
        profileClientError: "",
        coverClientError: "",

        onProfileSelected(event) {
            this.profileClientError = "";
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            const extension = (file.name.split(".").pop() || "").toLowerCase();
            const allowed = ["jpg", "jpeg", "png", "webp", "avif", "gif"];

            if (!allowed.includes(extension)) {
                this.profileClientError = "Profile image must be JPG, PNG, WEBP, AVIF, or GIF.";
                this.clearProfile();
                return;
            }

            this.profileFileName = file.name;
            this.profilePreview = URL.createObjectURL(file);
        },

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

        clearProfile() {
            this.profileClientError = "";
            this.profileFileName = "";
            this.profilePreview = config.profilePreview || null;
            if (this.$refs.profileInput) {
                this.$refs.profileInput.value = "";
            }
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

window.brandUploader = createBrandUploader;
