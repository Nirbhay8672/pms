<template>
    <Modal ref="plugin_upload_form" :id="'plugin_upload_form'">
        <template #modal_title>
            <span>Update Plugin For : {{ site_name }}</span>
        </template>

        <form>
            <div class="row mb-3 gy-3">
                <div class="col-12">
                    <label for="zip_file" class="required mb-2">Plugin Zip File</label>
                    <input
                        type="file"
                        id="zip_file"
                        ref="zip_file"
                        @change="setFile"
                        class="form-control"
                        accept=".zip"
                        :class="{
                            'is-invalid':
                                formValidation.hasError('zip_file'),
                        }"
                    />
                    <span
                        :class="{
                            'is-invalid':
                                formValidation.hasError('zip_file'),
                        }"
                    ></span>
                    <div
                        class="invalid-feedback"
                        v-if="formValidation.hasError('zip_file')"
                    >
                        <span>{{
                            formValidation.getError("zip_file")[0]
                        }}</span>
                    </div>
                </div>
            </div>
        </form>

        <template #modal_footer>
            <button class="btn btn-success btn-sm" type="button" @click="handleSubmit">
                Submit
            </button>
        </template>
    </Modal>
</template>

<script setup>
import axios from "axios";
import { ref, reactive } from "vue";
import Modal from "../../../components/Modal.vue";
import { FormValidation } from "../../../helpers/Validation";
import { pluginRoutes } from "../../../routes/PluginRoutes";
import { toastAlert } from "../../../helpers/alert";

let plugin_upload_form = ref(null);

const emits = defineEmits(["reload"]);

let site_name = ref('');

let fields = reactive({
    id: "",
    zip_file: "",
});

function openModal(member) {
    clearFormData();
    plugin_upload_form.value.open();

    fields.id = member.id;
    site_name.value = member.website_name;
}

function clearFormData() {
    formValidation.reset();
    fields.zip_file = "";
    fields.id = "";

    let fileInput = document.getElementById('zip_file');
    fileInput.value = '';
}

function setFile(event) {
    fields.zip_file = event.target.files[0].name;
}

function handleSubmit() {
    formValidation.validate();

    let form_data = new FormData();
    let zip_file = document.getElementById("zip_file");

    if (zip_file && zip_file.files.length > 0) {
        let file = zip_file.files[0];
        form_data.set("zip_file", file, file.name);
    }

    form_data.set("id", fields.id);

    let settings = { headers: { "content-type": "multipart/form-data" } };

    if (formValidation.isValid()) {
        axios
            .post(pluginRoutes.updatePlugin, form_data, settings)
            .then((response) => {
                plugin_upload_form.value.close();
                toastAlert({ title: response.data.message });
                clearFormData();
            })
            .catch(function (error) {
                if (error.response.status === 422) {
                    formValidation.setServerSideErrors(
                        error.response.data.errors
                    );
                }
            });
    }
}

defineExpose({
    openModal,
});

let formValidation = reactive(
    new FormValidation(fields, {
        zip_file: {
            required: "The plugin file is required.",
        },
    })
);
</script>
