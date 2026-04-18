const ROOT_SELECTOR = "#static-page-form-root";
const MAX_EDITOR_INIT_ATTEMPTS = 40;

const slugify = (value) => {
    return String(value || "")
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
};

const bindSlugAutofill = (root) => {
    if (root.dataset.slugAutofillBound === "1") {
        return;
    }

    const titleInput = root.querySelector("#title");
    const slugInput = root.querySelector("#slug");

    if (!titleInput || !slugInput) {
        return;
    }

    let slugTouched = slugInput.value.trim() !== "";

    slugInput.addEventListener("input", () => {
        slugTouched = slugInput.value.trim() !== "";
    });

    titleInput.addEventListener("input", () => {
        if (!slugTouched) {
            slugInput.value = slugify(titleInput.value);
        }
    });

    root.dataset.slugAutofillBound = "1";
};

const initEditor = (root) => {
    if (root.dataset.editorBound === "1") {
        return true;
    }

    const contentTextarea = root.querySelector("#content");
    const editorContainer = root.querySelector("#editor-container");

    if (!contentTextarea || !editorContainer || !window.Quill) {
        return false;
    }

    const quill = new window.Quill(editorContainer, {
        theme: "snow",
        placeholder: "Enter page content...",
        modules: {
            toolbar: [
                ["bold", "italic", "underline", "strike"],
                ["blockquote", "code-block"],
                [{ header: 1 }, { header: 2 }],
                [{ list: "ordered" }, { list: "bullet" }],
                [{ script: "sub" }, { script: "super" }],
                [{ indent: "-1" }, { indent: "+1" }],
                [{ size: ["small", false, "large", "huge"] }],
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                [{ color: [] }, { background: [] }],
                [{ font: [] }],
                [{ align: [] }],
                ["clean"],
                ["link", "image", "video"],
            ],
        },
    });

    if (contentTextarea.value) {
        quill.root.innerHTML = contentTextarea.value;
    }

    const form = contentTextarea.closest("form");
    if (form) {
        form.addEventListener("submit", () => {
            contentTextarea.value = quill.root.innerHTML;
        });
    }

    root.dataset.editorBound = "1";
    return true;
};

const initStaticPageForm = (attempt = 0) => {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    bindSlugAutofill(root);

    const editorReady = initEditor(root);
    if (editorReady) {
        return;
    }

    if (attempt >= MAX_EDITOR_INIT_ATTEMPTS) {
        return;
    }

    window.setTimeout(() => {
        initStaticPageForm(attempt + 1);
    }, 100);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initStaticPageForm);
} else {
    initStaticPageForm();
}
