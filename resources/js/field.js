import DetailField from "./components/DetailField";
import FormField from "./components/FormField";

Nova.booting((app, store) => {
    app.component("detail-enhanced-markdown", DetailField);
    app.component("form-enhanced-markdown", FormField);
});
