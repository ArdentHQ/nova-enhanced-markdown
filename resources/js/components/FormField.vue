<script>
// Needed to add markdown highlighting
require("@/../../node_modules/codemirror/mode/markdown/markdown.js");
import MarkdownField from "@/fields/Form/MarkdownField.vue";
import { FormField } from "@/mixins";
import Toasted from "toastedjs";

export default {
    extends: MarkdownField,

    mixins: [FormField],

    data: () => ({
        draftId: uuidv4(),
        index: 0,
        toasted: new Toasted({
            theme: "nova",
            position: "bottom-right",
            duration: 6000,
        }),
    }),
    computed: {
        actions: () => ({
            ...MarkdownField.computed.actions(),

            image() {
                if (!this.isEditable) return;

                this.insertAround("![", "](url)");

                const cursor = this.doc().getCursor();

                // Select the url part
                this.doc().setSelection(
                    {
                        line: cursor.line,
                        ch: cursor.ch - 4,
                    },
                    {
                        line: cursor.line,
                        ch: cursor.ch - 1,
                    }
                );
            },

            link() {
                if (!this.isEditable) return;

                this.insertAround("[", "](url)");

                const cursor = this.doc().getCursor();

                // Select the url part
                this.doc().setSelection(
                    {
                        line: cursor.line,
                        ch: cursor.ch - 4,
                    },
                    {
                        line: cursor.line,
                        ch: cursor.ch - 1,
                    }
                );
            },
        }),
    },
    methods: {
        fill(formData) {
            this.fillIfVisible(
                formData,
                this.field.attribute,
                this.value || ""
            );
            this.fillIfVisible(
                formData,
                `${this.field.attribute}DraftId`,
                this.draftId
            );
        },
        dropHandler(codemirror, event) {
            const { files } = event.dataTransfer;

            const handler = () => {
                codemirror.off("cursorActivity", handler);

                this.handleFiles(codemirror, files);
            };

            // Wait for the cursor to be in the proper position
            // before adding the placeholder.
            codemirror.on("cursorActivity", handler);
        },
        pasteHandler(codemirror, event) {
            const { files } = event.clipboardData;

            this.handleFiles(codemirror, files);
        },
        handleFiles(codemirror, files) {
            Array.from(files).forEach((file) =>
                this.uploadFile(codemirror, file)
            );
        },
        uploadFile(codemirror, file) {
            const data = new FormData();

            const textSelection = codemirror.getSelection() || "";
            const currentCursor = codemirror.getCursor();

            const placeholderPosition = {
                line: currentCursor.line,
                ch: currentCursor.ch - textSelection.length,
            };

            const loadingPlaceholder = `![Uploading ${file.name}}…]()`;
            codemirror.replaceSelection(loadingPlaceholder + "\n");

            data.append("Content-Type", file.type);
            data.append("attachment", file);
            data.append("draftId", this.draftId);

            const replacePlaceholder = (newContent) => {
                codemirror.setSelection(
                    {
                        line: placeholderPosition.line,
                        ch: placeholderPosition.ch - 1,
                    },
                    {
                        line: placeholderPosition.line,
                        ch: placeholderPosition.ch + loadingPlaceholder.length,
                    }
                );

                codemirror.replaceSelection(newContent);
            };

            Nova.request()
                .post(
                    `/ardenthq/nova-enhanced-markdown/${this.resourceName}/store/${this.field.attribute}`,
                    data
                )
                .then(({ data: url }) => {
                    replacePlaceholder(`![${textSelection}](${url})`);
                })
                .catch(({ response }) => {
                    if (response.status === 422) {
                        this.toasted.show(response.data.message, {
                            type: "error",
                        });
                    } else {
                        this.toasted.show(
                            "An error occured while uploading your file.",
                            { type: "error" }
                        );
                    }

                    codemirror.setSelection(
                        {
                            line: placeholderPosition.line,
                            ch: placeholderPosition.ch - 1,
                        },
                        {
                            line: placeholderPosition.line,
                            ch:
                                placeholderPosition.ch +
                                loadingPlaceholder.length,
                        }
                    );

                    replacePlaceholder(textSelection);
                });
        },

        insertAround(start, end) {
            if (this.doc().somethingSelected()) {
                MarkdownField.methods.insertAround.call(this, start, end);
            } else {
                this.doc().replaceSelection(start + end);
            }
        },
    },
    mounted() {
        this.codemirror.on("drop", this.dropHandler);

        this.codemirror.on("paste", this.pasteHandler);
    },
};

function uuidv4() {
    return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
        (
            c ^
            (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))
        ).toString(16)
    );
}
</script>
